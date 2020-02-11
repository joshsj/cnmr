<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageFilms extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    // all films list
    $root =  function (Request $req, Response $res, array $args) use ($db) {
      $films = $db->query("select id, title from film;")->fetchAll();
      $this->get("view")->render($res, "manage/films.twig", ["films" => $films]);
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);

    // film
    $group->get("/{id}", function (Request $req, Response $res, array $args) use ($db) {
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

      $this->get("view")->render($res, "manage/films-id.twig", ["film" => $film, "genres" => $genres]);
      return $res;
    });
  }
}
