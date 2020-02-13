<?php

declare(strict_types=1);

namespace RouteHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Login extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    // login page
    $group->get("", function (Request $req, Response $res, array $args) {
      $msg = $_SESSION["msg"];
      $_SESSION["msg"] = "";

      $this->get("view")->render($res, "login.twig", ["msg" => $msg]);
      return $res;
    });

    // attempt login
    $group->post("", function (Request $req, Response $res, array $args) use ($db) {
      // sanitize email
      $email = filter_var($req->getParsedBody()["email"], FILTER_SANITIZE_EMAIL);
      $pass = $req->getParsedBody()["password"];

      // mode - create
      if (isset($req->getParsedBody()["create"])) {
        // validate email
        if ($email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $pass = password_hash($pass, PASSWORD_DEFAULT);

          $stmt = $db->prepare("insert into account (email, password) values (?, ?)");

          try {
            // insert user into db
            $stmt->execute([$email, $pass]);
          } catch (\PDOException $e) {
            // email already in use
            $_SESSION["msg"] = "Email already in use";
            return $res->withHeader("Location", "/login");
          }
        } else {
          // email invalid
          $_SESSION["msg"] = "Email is invalid";
          return $res->withHeader("Location", "/login");
        }
      } else {
        // mode - sign in

        // try to find user
        $stmt = $db->prepare("select * from account where email = ?");
        $stmt->execute([$email]);
        $stmt = $stmt->fetch();

        // user not found or wrong password
        if (!($stmt && password_verify($pass, $stmt["password"]))) {
          $_SESSION["msg"] = "Email or password incorrect";
          return $res->withHeader("Location", "/login");
        }
      }

      // setup session
      $_SESSION["email"] = $email;

      // get id and privileges
      $stmt = $db->prepare("select id, admin from account where email = ?");
      $stmt->execute([$email]);
      $account = $stmt->fetch();
      $_SESSION["id"] = $account["id"];
      $_SESSION["admin"] = filter_var($account["admin"], FILTER_VALIDATE_BOOLEAN);

      // go to account page
      return $res->withHeader("Location", "/account");
    });
  }
}
