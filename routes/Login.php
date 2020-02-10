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
      $email = $req->getParsedBody()["email"];
      $pass = $req->getParsedBody()["password"];

      // try to find user
      $q_user = $db->prepare("select * from user where email = ?");
      $q_user->execute([$email]);

      if ($user = $q_user->fetch()) {
        // validate password
        // password_verify($pass, $user["password"]);

        // create session
        $_SESSION["email"] = $user["email"];
        $_SESSION["admin"] = filter_var($user["admin"], FILTER_VALIDATE_BOOLEAN);

        $res = $res->withHeader("Location", "/account");
      } else {
        $res = $res->withHeader("Location", "/");
      }

      return $res;
    };

    $group->post("", $root_post);
    $group->post("/", $root_post);
  }
}
