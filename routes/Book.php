<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Book extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    if (!isset($_SESSION["email"])) {
      // redirect all paths home
      $group->redirect($group->getBasePath(), "/login");
      return;
    }

    $db = $this->db;

    $group->get("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $film_id = $args["id"];

      // get cinemas
      $cinemas = $db->query('select * from cinema')->fetchAll();
      // get screenings for each cinema
      foreach ($cinemas as &$cinema) {

        $stmt = $db->prepare(
          'select screening.id, screening.start
          from screening
          inner join screen on fk_screen = screen.id
          inner join cinema on screen.fk_cinema = cinema.id
          where cinema.id = ?
          and   screening.fk_film = ?'
        );
        $stmt->execute([$cinema["id"], $film_id]);
        $cinema["screenings"] = json_encode($stmt->fetchAll());
      }

      $this->get("view")->render($res, "book.twig", ["cinemas" => $cinemas]);
      return $res;
    });

    $group->post("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $film_id = $args["id"];

      $cinema_id = filter_input(INPUT_POST, "cinema", FILTER_VALIDATE_INT);
      $screening_id = filter_input(INPUT_POST, "screening", FILTER_VALIDATE_INT);

      $tickets = $_POST["tickets"];
      $amt_adult = filter_var($tickets["adult"], FILTER_VALIDATE_INT);
      $amt_child = filter_var($tickets["child"], FILTER_VALIDATE_INT);
      $amt_student = filter_var($tickets["student"], FILTER_VALIDATE_INT);

      $stmt = $db->prepare('
        insert into booking (tickets_adult, tickets_child, tickets_student,
        fk_screening, fk_account) values (?, ?, ?, ?, ?)');

      try {
        $stmt->execute([$amt_adult, $amt_child, $amt_student, $screening_id, $_SESSION["id"]]);
      } catch (\PDOException $e) {
        $_SESSION = "Booking unsuccessful";
        return $res->withHeader("Location", "/home");
      }

      $_SESSION = "Booking successful";
      return $res->withHeader("Location", "/account#tickets");
    });
  }
}
