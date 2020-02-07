<?php

declare(strict_types=1);
require_once(__DIR__ . "/../vendor/autoload.php");

use Slim\Factory\AppFactory;
use RootHandler\Index; // autoloaded by composer

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

// configure app
$app = AppFactory::create();

// setup routes
$app->group("/", new Index($db_cnmr));

$app->run();
