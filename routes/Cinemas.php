<?php

declare(strict_types=1);

namespace RootHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Cinemas extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $cinemas = $this->db->query("select * from cinema")->fetchAll();

    $root = function (Request $req, Response $res, $args) use ($cinemas) {
      $this->get("view")->render($res, "cinemas.twig", ["cinemas" => $cinemas]);
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);
  }
}
