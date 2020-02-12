<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Cinemas extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $group->get("", function (Request $req, Response $res, array $args) use ($db) {
      $cinemas = $db->query("select * from cinema")->fetchAll();
      $this->get("view")->render($res, "cinemas.twig", ["cinemas" => $cinemas]);
      return $res;
    });
  }
}
