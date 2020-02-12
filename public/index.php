<?php

declare(strict_types=1);

// composer autoload
require_once(__DIR__ . "/../vendor/autoload.php");

// slim
use Slim\Factory\AppFactory;

use DI\Container; // dependency injection container for middleware
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig; // template engine

// setup sessions
session_set_cookie_params([
  // TODO make better
  // "lifetime" => 0, // on browser close
  // "path" => "",
  // "domain" => "",
  // "secure" => true,
  "httponly" => true,
  "samesite" => true,
]);
session_start();
// prevent missing key exceptions
$_SESSION["msg"] ?? $_SESSION["msg"] = "";

// get .env variables
Dotenv\Dotenv::createImmutable(__DIR__ . "/../config")->load();

// configure mysql database connection
try {
  $db_cnmr = new PDO(
    "mysql:host=localhost;dbname=cnmr",
    "admin",
    "FILMl0v3r420",
    [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
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

// remove trailing slashes on routes
$app->add(new Middlewares\TrailingSlash(false));

// setup view engine
$container->set("view", Twig::create("../templates"));

// routes
$app->group("/", new RouteHandler\Index($db_cnmr));
$app->group("/films", new RouteHandler\Films($db_cnmr));
$app->group("/cinemas", new RouteHandler\Cinemas($db_cnmr));
$app->group("/login", new RouteHandler\Login($db_cnmr));
$app->group("/logout", new RouteHandler\Logout($db_cnmr));
$app->group("/account", new RouteHandler\Account($db_cnmr));
$app->group("/api", new RouteHandler\API($db_cnmr));

// restrict manage access to admins only
$app->group("/manage", function (RouteCollectorProxy $group) use ($app, $db_cnmr) {
  // check admin in session
  if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    (new RouteHandler\Manage($db_cnmr))($group);
  } else {
    // redirect all paths on /manage to home
    $group->redirect($group->getBasePath(), "/home", 401);
  }
});

// redirects
$app->redirect("/home", "/", 200); // home goes to root

// error handling
$app
  ->addErrorMiddleware(true, true, true)
  // handle 404s
  ->setErrorHandler(\Slim\Exception\HttpNotFoundException::class, function () use ($app) {
    $res = $app->getResponseFactory()->createResponse()->withStatus(404);

    $this->get("view")->render($res, "404.twig");

    return $res;
  });

$app->run();
