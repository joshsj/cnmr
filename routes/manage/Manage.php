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

    // restrict manage access to admins only
    // check admin in session
    if (!(isset($_SESSION["admin"]) && $_SESSION["admin"] === true)) {
      // redirect all paths on /manage to home
      $group->redirect($group->getBasePath(), "/home", 401);
      return;
    }

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
