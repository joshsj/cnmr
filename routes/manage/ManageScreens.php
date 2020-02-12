<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageScreens extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $group->get("", function (Request $req, Response $res) use ($db) {

      // get all screens info
      $screens = $db->query('
      select screen.id, screen.no, cinema.id as "cinema_id", cinema.area, cinema.city
      from screen
      inner join cinema on screen.fk_cinema = cinema.id')->fetchAll();

      $this->get("view")->render(
        $res,
        "/manage/screens.twig",
        ["screens" => $screens]
      );
      return $res;
    });

    $group->get(
      "/{cinema_id}/{screen_id}",
      function (Request $req, Response $res, array $args) use ($db) {
        $msg = $_SESSION["msg"];
        $_SESSION["msg"] = "";

        $cinema_id = $args["cinema_id"];
        $screen_id = $args["screen_id"];

        // get screen info
        $q_screen = $db->prepare(
          'select screen.id, screen.no, screen.capacity,
                  cinema.area as "cinema_area", cinema.city as "cinema_city"
          from screen
          inner join cinema on screen.fk_cinema = cinema.id
          where screen.id = ?'
        );
        $q_screen->execute([$screen_id]);
        $screen = $q_screen->fetch();
        $screen["cinema_id"] = $cinema_id; // add cinema ID

        $this->get("view")->render($res, "/manage/cinema-screen-id.twig", [
          "screen" => $screen,
          "msg" => $msg
        ]);
        return $res;
      }
    );

    $group->post(
      "/{cinema_id}/{screen_id}/update",
      function (Request $req, Response $res, array $args) use ($db) {
        $cinema_id = $args["cinema_id"];
        $screen_id = $args["screen_id"];

        $updates = 0;

        // update capacity
        $capacity = filter_input(INPUT_POST, "capacity", FILTER_VALIDATE_INT);
        if ($capacity > 0) { // validate
          $stmt = $db->prepare(
            "update screen
            set capacity = ?
            where screen.id = ? and
                  screen.fk_cinema = ?
            "
          );

          $stmt->execute([$capacity, $screen_id, $cinema_id]);
          $updates += $stmt->rowCount();
        }

        // update capacity
        $no = filter_input(INPUT_POST, "no", FILTER_SANITIZE_STRING);
        if (!empty($no)) { // validate
          $stmt = $db->prepare(
            "update screen
            set no = ?
            where screen.id = ? and
                  screen.fk_cinema = ?
            "
          );

          try {
            $stmt->execute([$no, $screen_id, $cinema_id]);
          } catch (\PDOException $e) {
            // duplicate number
            $_SESSION["msg"] = "Duplicate screen number.";
            return $res->withHeader("Location", "/manage/screens/$cinema_id/$screen_id");
          }

          $updates += $stmt->rowCount();
        }

        $_SESSION["msg"] = "$updates updates";
        return $res->withHeader("Location", "/manage/screens/$cinema_id/$screen_id");
      }
    );
  }
}
