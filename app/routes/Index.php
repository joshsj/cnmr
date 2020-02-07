<?php

namespace RootHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Index extends AbstractRouteHandler
{

  public function __invoke(RouteCollectorProxy $group)
  {
    $root = function (Request $req, Response $res, $args) {
      $res->getBody()->write("I am root.");
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);
  }
}
