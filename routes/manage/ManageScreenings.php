<?php

declare(strict_types=1);

namespace RouteHandler;

use DateTime;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageScreenings extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $group->get("", function (Request $req, Response $res) use ($db) {
      // get all screenings
      $screening = $db->query(
        'select
        screening.id, screening.start,
        screen.no as "screen_no",
        film.title as "film_title",
        cinema.area as "cinema_area", cinema.city as "cinema_city"
        from screening
        inner join screen on screening.fk_screen = screen.ID
        inner join film on screening.fk_film = film.ID
        inner join cinema on screen.fk_cinema = cinema.ID'
      )->fetch();

      $this->get("view")->render(
        $res,
        "/manage/screenings.twig",
        ["screening" => $screening]
      );
      return $res;
    });

    $group->get("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $msg = $_SESSION["msg"];
      $_SESSION["msg"] = "";
      $id = $args["id"];

      // get screening info
      $stmt = $db->prepare(
        'select
        screening.id, screening.start,
        screen.id as "screen_id", screen.no as "screen_no",
        film.id as "film_id",
        cinema.id as "cinema_id"
        from screening
        inner join screen on fk_screen = screen.id
        inner join film on fk_film = film.id
        inner join cinema on screen.fk_cinema = cinema.id
        where screening.id = ?'
      );
      $stmt->execute([$id]);
      $screening = $stmt->fetch();

      // get all screen and cinema info
      $screens = $db->query(
        'select screen.id, screen.no,
        cinema.id as "cinema_id", cinema.area as "cinema_area", cinema.city as "cinema_city"
        from screen
        inner join cinema on screen.fk_cinema = cinema.id'
      );

      // get all film info
      $films = $db->query('select id, title from film');

      $this->get("view")->render(
        $res,
        "/manage/screenings-id.twig",
        [
          "screening" => $screening,
          "screens" => $screens,
          "films" => $films,
          "msg" => $msg
        ]
      );

      return $res;
    });

    $group->post("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $id = $args["id"];

      $updates = 0;

      // update film
      if ($film = filter_input(INPUT_POST, "film", FILTER_VALIDATE_INT)) {
        $stmt = $db->prepare(
          'update screening
          set fk_Film = ?
          where id = ?'
        );
        $stmt->execute([$film, $id]);
        $updates += $stmt->rowCount();
      }

      // update screen
      if ($screen = filter_input(INPUT_POST, "screen", FILTER_VALIDATE_INT)) {
        $stmt = $db->prepare(
          'update screening
          set fk_Screen = ?
          where id = ?'
        );
        $stmt->execute([$screen, $id]);
        $updates += $stmt->rowCount();
      }

      // update start time
      // release date
      try {
        $start = new DateTime(filter_input(INPUT_POST, "start", FILTER_SANITIZE_STRING));

        $stmt = $db->prepare(
          'update screening
          set start = ?
          where id = ?'
        );
        $stmt->execute([$start->format("Y-m-d H:i:s"), $id]);
        $updates += $stmt->rowCount();
      } catch (\Exception $e) {
      }

      $_SESSION["msg"] = "$updates updates";
      return $res->withHeader("Location", "/manage/screenings/$id");
    });
  }
}
