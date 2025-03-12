<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use Throwable;

class CustomErrorHandler{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger = $logger;
    }

    /**
     * This method is invoked when an error occurs.
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        // Log the error if logging is enabled.
        if ($logErrors) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        // Create a new response instance.
        $response = new Response();

        // If detailed errors are allowed, include detailed info.
        if ($displayErrorDetails) {
            $payload = json_encode([
                'error'  => $exception->getMessage(),
                'code'   => $exception->getCode(),
                'file'   => $exception->getFile(),
                'line'   => $exception->getLine(),
                'trace'  => $exception->getTrace(),
            ], JSON_PRETTY_PRINT);
        } else {
            // A generic message for production.
            $payload = json_encode([
                'error' => 'An internal error occurred. Please try again later.'
            ]);
        }

        // Write the payload to the response body.
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
}
