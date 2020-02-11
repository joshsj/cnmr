<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageCinemas extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $root = function (Request $req, Response $res, array $args) use ($db) {
      $cinemas = $db->query("select id, city from cinema;")->fetchAll();
      $this->get("view")->render($res, "manage/cinemas.twig", ["cinemas" => $cinemas]);
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);

    $group->get("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $q_cinema = $db->prepare("select * from cinema where id = ?");
      $q_cinema->execute([$args["id"]]);
      $cinema = $q_cinema->fetch();

      $this->get("view")->render($res, "/manage/cinemas-id.twig", ["cinema" => $cinema]);
      return $res;
    });
  }
}
