<?php

declare(strict_types=1);

namespace RouteHandler;

use DateTime;
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

    $group->get("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $msg = $_SESSION["msg"];
      $_SESSION["msg"] = "";

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

      $this->get("view")->render($res, "manage/films-id.twig", [
        "film" => $film, "genres" => $genres, "msg" => $msg
      ]);
      return $res;
    });

    $group->post("/{id}/update", function (Request $req, Response $res, array $args) use ($db) {
      $id = $args["id"];

      $updates = 0;

      $validate = function ($key) {
        $s = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
        if (preg_match('/^[a-zA-Z0-9 ]*$/', $s)) {
          return ucwords($s);
        } else {
          return false;
        }
      };

      // update text fields
      if ($title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING)) {
        $stmt = $db->prepare("update film set title = ? where id = ?");
        $stmt->execute([$title, $id]);
        $updates += $stmt->rowCount();
      }

      if ($director = $validate("director")) {
        $stmt = $db->prepare("update film set director = ? where id = ?");
        $stmt->execute([$director, $id]);
        $updates += $stmt->rowCount();
      }

      if ($desc = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)) {
        $stmt = $db->prepare("update film set description = ? where id = ?");
        $stmt->execute([$desc, $id]);
        $updates += $stmt->rowCount();
      }

      $runtime = filter_input(INPUT_POST, "runtime", FILTER_VALIDATE_INT);
      if ($runtime > 0) {
        $stmt = $db->prepare("update film set runtime = ? where id = ?");
        $stmt->execute([$runtime, $id]);
        $updates += $stmt->rowCount();
      }

      // certificate
      $cert = filter_input(INPUT_POST, "certificate", FILTER_SANITIZE_STRING);
      if (ctype_alnum($cert)) {
        $cert = strtoupper($cert); // format

        $stmt = $db->prepare("update film set certificate = ? where id = ?");
        $stmt->execute([$cert, $id]);
        $updates += $stmt->rowCount();
      }

      // release date
      try {
        $released = new DateTime(filter_input(INPUT_POST, "released", FILTER_SANITIZE_STRING));

        $stmt = $db->prepare("update film set released = ? where id = ?");
        $stmt->execute([$released->format("Y-m-d"), $id]);
        $updates += $stmt->rowCount();
      } catch (\Exception $e) {
      }

      // prices
      $price = filter_input(INPUT_POST, "price_adult", FILTER_VALIDATE_FLOAT);
      if ($price > 0) {
        $stmt = $db->prepare("update film set price_adult = ? where id = ?");
        $stmt->execute([$price, $id]);
        $updates += $stmt->rowCount();
      }

      $price = filter_input(INPUT_POST, "price_child", FILTER_VALIDATE_FLOAT);
      if ($price > 0) {
        $stmt = $db->prepare("update film set price_child = ? where id = ?");
        $stmt->execute([$price, $id]);
        $updates += $stmt->rowCount();
      }

      $price = filter_input(INPUT_POST, "price_student", FILTER_VALIDATE_FLOAT);
      if ($price > 0) {
        $stmt = $db->prepare("update film set price_student = ? where id = ?");
        $stmt->execute([$price, $id]);
        $updates += $stmt->rowCount();
      }

      // checkboxes only return checked elements so we
      // delete all genres for film
      $db->prepare("delete from film_genre where fk_Film = ?")->execute([$id]);

      // add checked genres
      foreach ($_POST["genres"] ?? [] as $genre_id) {
        if ($genre_id = filter_var($genre_id, FILTER_VALIDATE_INT)) {
          $db
            ->prepare("insert into film_genre values (?, ?)")
            ->execute([$id, $genre_id]);
        }
      }

      $_SESSION["msg"] = "$updates updates";

      return $res->withHeader("Location", "/manage/films/$id");
    });
  }
}
