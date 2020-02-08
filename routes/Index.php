<?php

declare(strict_types=1);

namespace RootHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Index extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $root = function (Request $req, Response $res, $args) {
      $this->get("view")->render($res, "index.php", ["title" => "pls"]);
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);
  }
}
