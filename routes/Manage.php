<?php

declare(strict_types=1);

namespace RootHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Manage extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $root = function (Request $req, Response $res, array $args) {
      $this->get("view")->render($res, "manage/main.twig");
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);

    // root for each table
    $group->get("/genre", function (Request $req, Response $res, array $args) use ($db) {
      $genres = $db->query("select * from genre order by id")->fetchAll();

      $this->get("view")->render($res, "manage/genre.twig", ["genres" => $genres]);
      return $res;
    });
  }
}
