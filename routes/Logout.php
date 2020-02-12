<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Logout extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $root = function (Request $req, Response $res, array $args) {
      session_destroy(); // remove session data from server
      $_SESSION = [];    // clean session variable
      setcookie("PHPSESSID", "", time() - 1); // remove session ID cookie from browser
      return $res->withHeader("Location", "/home");
    };

    $group->get("", $root);
  }
}
