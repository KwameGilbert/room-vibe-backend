<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;

class ErrorHandler
{
    // Error handler method
    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): Response {
        // Get status code
        $statusCode = $exception instanceof HttpNotFoundException ? 404 : ($exception instanceof HttpMethodNotAllowedException ? 405 : 500);

        // Prepare the detailed error message
        $errorDetails = [
            'error' => true,
            'message' => $exception->getMessage(),
            'status_code' => $statusCode,
            'type' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Log the error if necessary (you can expand logging here)
        if ($logErrors) {
            error_log(json_encode($errorDetails));
        }

        // Create a response
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode($errorDetails));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
