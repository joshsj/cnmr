<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once(__DIR__ . "/../vendor/autoload.php");

$app = AppFactory::create();

$app->get("/", function (Request $req, Response $res, $args) {
  $res->getBody()->write("Hello world");
  return $res;
});

$app->run();
