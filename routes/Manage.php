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

    $group->get("/film/{id}", function (Request $req, Response $res, array $args) use ($db) {
      // get film data
      $q_film = $db->prepare("select * from film where id = ?");
      $q_film->execute([$args["id"]]);
      $film = $q_film->fetch();

      // get all genres
      $genres = $db->query("select * from genre")->fetchAll();

      // get film genre IDs
      $q_film_genres = $db->prepare("
      select genre.ID
      from film_genre
      inner join genre on film_genre.fk_Genre = genre.ID where film_genre.fk_Film = ?");
      $q_film_genres->execute([$args["id"]]);

      // indicate if film currently has genre
      while ($id = $q_film_genres->fetch()["id"]) {
        foreach ($genres as $i => $genre) {
          if ($genre["id"] == $id) {
            $genres[$i]["film_has"] = true;
          }
        }
      }

      $this->get("view")->render($res, "manage/film.twig", ["film" => $film, "genres" => $genres]);
      return $res;
    });
  }
}
