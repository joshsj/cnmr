<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageGenre extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $root = function (Request $req, Response $res) use ($db) {
      // get and clean session message
      $msg = $_SESSION["msg"] ?? "";
      $_SESSION["msg"] = "";

      $genres = $db->query("select * from genre order by id")->fetchAll();

      $this->get("view")->render(
        $res,
        "manage/genre.twig",
        ["genres" => $genres, "msg" =>  $msg]
      );
      return $res;
    };

    $group->get("", $root);
    $group->get("/", $root);

    // new genre
    $group->post("/new", function (Request $req, Response $res) use ($db) {
      // sanitize input
      $genre = filter_input(INPUT_POST, "genre", FILTER_SANITIZE_STRING);

      // validate name to just characters
      if (!ctype_alpha($genre)) {
        // store error message in session
        $_SESSION["msg"] = "Genre contains invalid characters.";
        return $res->withHeader("Location", "/manage/genre");
      }

      // format name to titlecase
      $genre = ucwords($genre);

      // insert
      $q_new_genre = $db->prepare("insert into genre (name) values (?)");
      try {
        $q_new_genre->execute([$genre]);
      } catch (\PDOException $e) {
        $_SESSION["msg"] = $e->getMessage();
        return $res->withHeader("Location", "/manage/genre");
      }

      // it worked
      $_SESSION["msg"] = "'$genre' inserted successfully.";
      return $res->withHeader("Location", "/manage/genre");
    });
  }
}
