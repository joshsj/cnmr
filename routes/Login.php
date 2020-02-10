<?php

declare(strict_types=1);

namespace RootHandler;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Login extends AbstractRouteHandler
{
  public function __invoke(RouteCollectorProxy $group)
  {
    $db = $this->db;

    $root_get = function (Request $req, Response $res, array $args) {
      $this->get("view")->render($res, "login.twig");
      return $res;
    };

    $group->get("", $root_get);
    $group->get("/", $root_get);

    $root_post =  function (Request $req, Response $res, array $args) use ($db) {
      // sanitize email
      $email = filter_var($req->getParsedBody()["email"], FILTER_SANITIZE_EMAIL);
      $pass = $req->getParsedBody()["password"];

      // mode - create
      if (isset($req->getParsedBody()["create"])) {
        if ($email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $pass = password_hash($pass, PASSWORD_DEFAULT);

          // insert user into db
          $i_user = $db->prepare("insert into user (email, password) values (?, ?)");
          $i_user->execute([$email, $pass]);

          // setup session
          $_SESSION["email"] = $email;
          $_SESSION["admin"] = false;

          // go to account page
          return $res->withHeader("Location", "/account");
        }
      }
      // mode - sign in

      // try to find user
      $q_user = $db->prepare("select * from user where email = ?");
      $q_user->execute([$email]);
      $user = $q_user->fetch();

      // user found, passwords match
      if ($user && password_verify($pass, $user["password"])) {
        // create session
        $_SESSION["email"] = $user["email"];
        $_SESSION["admin"] = filter_var($user["admin"], FILTER_VALIDATE_BOOLEAN);

        return $res->withHeader("Location", "/account");
      }

      return $res = $res->withHeader("Location", "/");
    };

    $group->post("", $root_post);
    $group->post("/", $root_post);
  }
}
