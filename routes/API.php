<?php

declare(strict_types=1);

namespace RouteHandler;

use Exception;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class API extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $group->get("", function (Request $req, Response $res, array $args) {
      // root cant do anything
      return $res->withStatus(400);
    });

    $group->get("/reviews", function (Request $req, Response $res, array $args) {
      // get tmdb id
      $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);

      // check has ID
      if ($id == null) {
        return $res->withStatus(400);
      }

      // get data from TMDB as associative array
      // suppress php warning if request fails
      $tmdb_res = @file_get_contents(
        "https://api.themoviedb.org/3/movie/$id?api_key=" . $_ENV["TMDB_API_KEY"]
      );

      // check failure
      if ($tmdb_res === false) {
        return $res->withStatus(400);
      }

      $json = json_decode($tmdb_res, true); // get as assoc
      $data = [
        "score" => $json["vote_average"],
        "count" => $json["vote_count"]
      ];

      $res->getBody()->write(json_encode($data));
      return $res
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json");
    });
  }
}
