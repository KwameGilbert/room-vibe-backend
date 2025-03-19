<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Slim\Middleware\ContentLengthMiddleware;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/../src/helpers/LoggerFactory.php';

// Create Container using PHP-DI
$container = new Container();

// Instantiate LoggerFactory and set up the logger service
$loggerFactory = new LoggerFactory('App');
$container->set('logger', $loggerFactory->getLogger());

// Set the container on AppFactory
AppFactory::setContainer($container);

// Create Slim App instance
$app = AppFactory::create();

// Set Base Path from environment variable (if not set, default to an empty string)
$app->setBasePath($_ENV['BASE_PATH'] ?? '');

// Configure error middleware based on environment
$environment = $_ENV['ENVIRONMENT'] ?? 'production';

switch ($environment) {
    case 'development':
        // Show all errors in development
        $app->addErrorMiddleware(
            displayErrorDetails: true,
            logErrors: true,
            logErrorDetails: true,
            logger: $container->get('logger')
        );
        break;
    
    case 'staging':
        // Log errors but don't display details
        $app->addErrorMiddleware(
            displayErrorDetails: false,
            logErrors: true,
            logErrorDetails: true,
            logger: $container->get('logger')
        );
        break;
    
    case 'production':
    default:
        // Production: Don't display errors, minimal logging
        $app->addErrorMiddleware(
            displayErrorDetails: false,
            logErrors: true,
            logErrorDetails: false,
            logger: $container->get('logger')
        );
        break;
}

// Add middleware for security headers and CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
    ->withHeader('X-Frame-Options', 'DENY')
    ->withHeader('X-Content-Type-Options', 'nosniff')
    ->withHeader('X-XSS-Protection', '1; mode=block');
});

// Optional: Middleware to enforce content length limits
$app->get('/', function ($_, $response) {
    $data = ['message' => 'Welcome to my Slim App'];
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// Load API routes (assuming routes/api.php returns a callable that accepts the app)
(require_once __DIR__ . '/../src/routes/api.php')($app);

// Run the application
$app->run();