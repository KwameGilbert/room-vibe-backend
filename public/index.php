<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Slim\Middleware\ContentLengthMiddleware;
use App\Helpers\LoggerFactory;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create Container using PHP-DI
$container = new Container();

// Set up Logger service using Monolog
$container->set('logger', LoggerFactory::getLogger('App'));

// Set the container on AppFactory
AppFactory::setContainer(container: $container);

// Create Slim App instance
$app = AppFactory::create();

// Set Base Path from environment variable (if not set, default to an empty string)
$app->setBasePath(basePath: $_ENV['BASE_PATH']);

// Add Error Middleware
// In production, consider setting the first parameter (displayErrorDetails) to false.
$app->addErrorMiddleware(
    displayErrorDetails: (bool) $_ENV['DISPLAY_ERROR_DETAILS'],
    logErrors: true,
    logErrorDetails: true, 
    logger: $container->get('logger')
);

// Add middleware for security headers
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('X-Frame-Options', 'DENY')
        ->withHeader('X-Content-Type-Options', 'nosniff')
        ->withHeader('X-XSS-Protection', '1; mode=block');
});

// Optional: Middleware to enforce content length limits
$app->add(middleware: new ContentLengthMiddleware());

// Default route
$app->get('/', function ($request, $response, $args) {
    $data = ['message' => 'Welcome to my Slim App'];
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// Load API routes (assuming routes/api.php returns a callable that accepts the app)
(require __DIR__ . '/../src/routes/api.php')($app);

// Run the application
$app->run();
