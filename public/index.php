<?php

declare(strict_types=1);

// composer autoload
require_once(__DIR__ . "/../vendor/autoload.php");

// slim
use Slim\Factory\AppFactory;

use DI\Container; // dependency injection container for middleware
use Slim\Views\PhpRenderer; // template engine

// Root handlers, autoloaded by composer
use RootHandler\Index;

// configure mysql database connection
try {
  $db_cnmr = new PDO(
    "mysql:host=localhost;dbname=cnmr",
    "admin",
    "FILMl0v3r420",
    [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  throw new PDOException($e->getMessage(), (int) $e->getCode());
}

// create container for middleware
$container = new Container();

// set view engine
$container->set("view", function () {
  $renderer = new PhpRenderer();
  $renderer->setTemplatePath(__DIR__ . "/../templates");
  $renderer->setLayout("layout.php");
  return $renderer;
});

// create app
AppFactory::setContainer($container);
$app = AppFactory::create();

// setup routes
$app->group("/", new Index($db_cnmr, $container));

$app->run();
