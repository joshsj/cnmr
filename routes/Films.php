<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Films extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $films = [];

    // get all films
    $q_film = $this->db->query("select ID, Title, Description, Released, Runtime, Certificate, Director from film");
    $q_genres = $this->db->prepare("
    select genre.Name
    from film_genre
    inner join genre on film_genre.fk_Genre = genre.ID
    where film_genre.fk_Film = ?
    ");

    // get film genres
    while ($film = $q_film->fetch()) {
      $q_genres->execute([$film["id"]]); // get film genres

      // store genres, selecting genre from pair
      $film["genres"] = array_map(function ($pair) {
        return $pair["name"];
      }, $q_genres->fetchAll(\PDO::FETCH_ASSOC));

      array_push($films, $film); // save film
    }

    $root = function (Request $req, Response $res, array $args) use ($films) {
      $this->get("view")->render(
        $res,
        "films.twig",
        [
          "films" => $films
        ]
      );
      return $res;
    };

    // root
    $group->get("", $root);
    $group->get("/", $root);

    // by film id
    $group->get(
      "/{id}",
      function (Request $req, Response $res, array $args) use ($films) {
        $film = current(array_filter($films, function ($film) use ($args) {
          return $film["id"] == $args["id"];
        }));

        $this->get("view")->render($res, "films-id.twig", [
          "film" => $film
        ]);
        return $res;
      }
    );
  }
}
