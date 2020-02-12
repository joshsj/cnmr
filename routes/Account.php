<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Account extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $group->get("", function (Request $req, Response $res, array $args) use ($db) {
      // check logged in
      if (!isset($_SESSION["email"])) {
        return $res->withHeader("Location", "/login"); // redirect to login page
      }

      // get account tickets
      $stmt = $db->prepare(
        'select screening.start, film.title as "film",
        cinema.area as "cinema_area", cinema.city as "cinema_city"
        from booking
        inner join screening on fk_screening = screening.id
        -- get film name
        inner join film on screening.fk_film = film.id
        -- get cinema info
        inner join screen on screening.fk_screen = screen.id
        inner join cinema on screen.fk_cinema = cinema.id
        -- get account id
        inner join account on booking.fk_account = account.id
        where account.email = ?'
      );
      $stmt->execute([$_SESSION["email"]]);
      $bookings = $stmt->fetchAll();

      $this->get("view")->render(
        $res,
        "account.twig",
        [
          "account" => [
            "email" => $_SESSION["email"],
            "admin" => $_SESSION["admin"]
          ],
          "bookings" => $bookings
        ]
      );
      return $res;
    });
  }
}
