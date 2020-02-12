<?php

declare(strict_types=1);

namespace RouteHandler;

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

    $group->group("/genre", new ManageGenre($db));
    $group->group("/cinemas", new ManageCinemas($db));
    $group->group("/films", new ManageFilms($db));
    $group->group("/screens", new ManageScreens($db));
  }
}
