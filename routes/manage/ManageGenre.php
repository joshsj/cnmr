<?php

declare(strict_types=1);

namespace RouteHandler;

use PDOException;
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
      $msg = $_SESSION["msg"];
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
      } catch (PDOException $e) {
        $_SESSION["msg"] = $e->getMessage();
        return $res->withHeader("Location", "/manage/genre");
      }

      // it worked
      $_SESSION["msg"] = "'$genre' inserted successfully.";
      return $res->withHeader("Location", "/manage/genre");
    });

    $group->post("/update", function (Request $req, Response $res) use ($db) {
      $_SESSION["msg"] = "";

      // update each genre
      foreach ($_POST as $id => $name) {
        // counters for message
        $deletions = 0;
        $updates = 0;

        // sanitize inputs
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        // empty name means deletion
        if (empty($name)) {
          $q_delete_genre = $db->prepare("delete from genre where id = ?");
          try {
            $q_delete_genre->execute([$id]);
          } catch (PDOException $e) {
          }
          ++$deletions;
        } else {
          // validate name
          if (!ctype_alpha($name)) {
            $_SESSION["msg"] = "Genre contains invalid characters.";
            return $res->withHeader("Location", "/manage/genre");
          }

          // insert
          $q_update_genre = $db->prepare("update genre set name = ? where id = ?");
          try {
            $q_update_genre->execute([$name, $id]);
            // update counter with rows affected
            $updates += $q_update_genre->rowCount();
          } catch (PDOException $e) {
          }
        }
      }

      $_SESSION["msg"] = "$updates updates, $deletions deletions";
      return $res->withHeader("Location", "/manage/genre");;
    });
  }
}
