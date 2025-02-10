<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\ContentLengthMiddleware;

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create Container (using PHP-DI)
$container = new Container();

// Set up Logger service using Monolog
$container->set('logger', function () {
    $logger = new Logger('slim_app');
    // Log file path and level from environment variables
    $logFile = __DIR__ . '/../logs/app.log';
    $logLevel = Logger::toMonologLevel($_ENV['LOG_LEVEL'] ?: 'DEBUG');
    $logger->pushHandler(new StreamHandler($logFile, $logLevel));
    return $logger;
});


// Here, we’re registering it under the key "customErrorHandler".
$container->set('customErrorHandler', function () use ($container) {
    // Note: We are using the fully qualified class name from the App namespace.
    return new \App\Middleware\CustomErrorHandler($container->get('logger'));
});

// Set the container on AppFactory
AppFactory::setContainer($container);

// Create Slim App instance
$app = AppFactory::create();

// Set Base Path (from environment variable)
$app->setBasePath($_ENV['BASE_PATH']);

// Add middleware for error handling
// Here, we set displayErrorDetails to true to get detailed error messages.
$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    true,  // Display detailed error details
    false,
    false
);

// Use the custom error handler registered in the container.
$errorMiddleware->setDefaultErrorHandler($container->get('customErrorHandler'));
$app->add($errorMiddleware);

// Add middleware for security headers
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('X-Frame-Options', 'DENY')
        ->withHeader('X-Content-Type-Options', 'nosniff')
        ->withHeader('X-XSS-Protection', '1; mode=block');
});

// Optional: Middleware to enforce content length limits
$app->add(new ContentLengthMiddleware());

// Define application routes

// Health check endpoint (useful for load balancers and monitoring)
$app->get('/health', function ($request, $response, $args) {
    $data = ['status' => 'ok'];
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// Default route
$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Welcome to my Slim App");
    return $response->withHeader('Content-Type', 'application/json');
});

// Additional routes can be defined here...

// Run the application
$app->run();
