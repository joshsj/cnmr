<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManageCinemas extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $group->get("", function (Request $req, Response $res, array $args) use ($db) {
      $cinemas = $db->query("select id, city from cinema;")->fetchAll();
      $this->get("view")->render($res, "manage/cinemas.twig", ["cinemas" => $cinemas]);
      return $res;
    });

    $group->get("/{id}", function (Request $req, Response $res, array $args) use ($db) {
      $msg = $_SESSION["msg"];
      $_SESSION["msg"] = "";

      $q_cinema = $db->prepare("select * from cinema where id = ?");
      $q_cinema->execute([$args["id"]]);
      $cinema = $q_cinema->fetch();

      $this->get("view")->render(
        $res,
        "/manage/cinemas-id.twig",
        [
          "cinema" => $cinema, "msg" => $msg
        ]
      );
      return $res;
    });

    // update
    $group->post("/{id}/update", function (Request $req, Response $res, array $args) use ($db) {
      $id = $args["id"];

      $updates = 0;

      // update fields with counter
      $shop = isset($_POST["shop"]) ? true : false;
      $stmt = $db->prepare("update cinema set shop = ? where id = ?");
      $stmt->execute([$shop, $id]);
      $updates += $stmt->rowCount();

      $validate = function ($key) {
        $s = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
        if (preg_match('/^[a-zA-Z0-9 ]*$/', $s)) {
          return ucwords($s);
        } else {
          return false;
        }
      };

      if ($address = $validate("address")) {
        $stmt = $db->prepare("update cinema set address = ? where id = ?");
        $stmt->execute([$address, $id]);
        $updates += $stmt->rowCount();
      }

      if ($area = $validate("area")) {
        $stmt = $db->prepare("update cinema set area = ? where id = ?");
        $stmt->execute([$area, $id]);
        $updates += $stmt->rowCount();
      }

      if ($city = $validate("city")) {
        $stmt = $db->prepare("update cinema set city = ? where id = ?");
        $stmt->execute([$city, $id]);
        $updates += $stmt->rowCount();
      }

      if ($pc = $validate("postcode")) {
        $stmt = $db->prepare("update cinema set postcode = ? where id = ?");
        $stmt->execute([$pc, $id]);
        $updates += $stmt->rowCount();
      }

      // updates session message
      $_SESSION["msg"] = "$updates updates";

      return $res->withHeader("Location", "/manage/cinemas/$id");
    });
  }
}
