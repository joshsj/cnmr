<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageGenre extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $root = function (Request $req, Response $res) use ($db) {
      $genres = $db->query("select * from genre order by id")->fetchAll();

      $this->get("view")->render($res, "manage/genre.twig", ["genres" => $genres]);
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);
  }
}
