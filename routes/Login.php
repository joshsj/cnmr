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

          $q_account = $db->prepare("insert into account (email, password) values (?, ?)");

          try {
            // insert user into db
            $q_account->execute([$email, $pass]);
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
        $q_account = $db->prepare("select * from account where email = ?");
        $q_account->execute([$email]);
        $q_account = $q_account->fetch();

        // user not found or wrong password
        if (!($q_account && password_verify($pass, $q_account["password"]))) {
          $_SESSION["msg"] = "Email or password incorrect";
          return $res->withHeader("Location", "/login");
        }
      }

      // setup session
      $_SESSION["email"] = $email;
      $_SESSION["admin"] = false;

      // get id
      $q_id = $db->prepare("select id from account where email = ?");
      $q_id->execute([$email]);
      $_SESSION["id"] = $q_id->fetch()["id"];

      // go to account page
      return $res->withHeader("Location", "/account");
    });
  }
}
