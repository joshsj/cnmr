<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Index extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $root = function (Request $req, Response $res, array $args) {
      $this->get("view")->render($res, "index.twig");
      return $res;
    };

    $group->get("", $root);
  }
}
