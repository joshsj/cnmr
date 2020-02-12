<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Account extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $group->get("", function (Request $req, Response $res, array $args) {
      // check logged in
      if (isset($_SESSION["email"])) {
        // show account page
        $this->get("view")->render($res, "account.twig", ["account" => $_SESSION]);
      } else {
        $res = $res->withHeader("Location", "/login"); // redirect to login page
      }
      return $res;
    });
  }
}
