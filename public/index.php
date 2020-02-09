<?php

declare(strict_types=1);

// composer autoload
require_once(__DIR__ . "/../vendor/autoload.php");

// slim
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

use DI\Container; // dependency injection container for middleware
use Slim\Views\Twig; // template engine

// Root handlers, autoloaded by composer
use RootHandler\Index;
use RootHandler\Films;
use RootHandler\Cinemas;
use RootHandler\Manage;

// configure mysql database connection
try {
  $db_cnmr = new PDO(
    "mysql:host=localhost;dbname=cnmr",
    "admin",
    "FILMl0v3r420",
    [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::ATTR_CASE               => PDO::CASE_LOWER
    ]
  );
} catch (PDOException $e) {
  throw new PDOException($e->getMessage(), (int) $e->getCode());
}

// create app
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

// setup view engine
$container->set("view", Twig::create("../templates"));

// routes
$app->group("/", new Index($db_cnmr));
$app->group("/films", new Films($db_cnmr));
$app->group("/cinemas", new Cinemas($db_cnmr));

// redirects
$app->redirect("/home", "/", 200); // home goes to root


// error handling
$app
  ->addErrorMiddleware(true, true, true)
  // handle 404s
  ->setErrorHandler(HttpNotFoundException::class, function () use ($app) {
    $res = $app->getResponseFactory()->createResponse()->withStatus(404);

    $this->get("view")->render($res, "404.twig");

    return $res;
  });

$app->run();
