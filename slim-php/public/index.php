<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// require __DIR__ . '/../helpers/Bootstrap.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/ErrorHandler.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true,)->setDefaultErrorHandler(new ErrorHandler());

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Welcome to my Slim App");
    return $response;
});

$app->setBasePath("/room-vibe-backend/slim-php");

$app->run();
