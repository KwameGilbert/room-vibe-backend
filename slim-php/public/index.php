<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\ContentLengthMiddleware;
use Psr\Http\Message\ServerRequestInterface;


// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
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

// Define custom error handler
$container->set('customErrorHandler', function () use ($container) {
    return function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($container) {
        // Retrieve the logger from the container
        $logger = $container->get('logger');
        $logger->error($exception->getMessage(), ['exception' => $exception]);

        // Create a response object (using Slim's Response class)
        $response = new \Slim\Psr7\Response();

        // Prepare error payload
        $payload = json_encode([
            'error' => 'An internal error occurred. Please try again later.'
        ]);

        // Write payload to the response body
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    };
});

// Set the container on AppFactory
AppFactory::setContainer($container);

// Create Slim App instance
$app = AppFactory::create();

// Set Base Path (from environment variable)
$app->setBasePath($_ENV['BASE_PATH']);

// Add middleware for error handling
$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $_ENV['DISPLAY_ERROR_DETAILS'] === 'true', // Display errors in development
    false,
    false
);

// Set custom error handler from the container
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
