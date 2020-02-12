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
      $this->get("view")->render($res, "login.twig");
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

            // setup session
            $_SESSION["email"] = $email;
            $_SESSION["admin"] = false;

            // go to account page
            $res = $res->withHeader("Location", "/account");
          } catch (\PDOException $e) {
            // email already in use
            // render page with alert message
            $this->get("view")->render($res, "login.twig", ["msg" => "Email already in use"]);
          }
        } else {
          // email invalid
          $this->get("view")->render($res, "login.twig", ["msg" => "Email is invalid"]);
        }
      } else {
        // mode - sign in

        // try to find user
        $q_account = $db->prepare("select * from account where email = ?");
        $q_account->execute([$email]);
        $q_account = $q_account->fetch();

        // user found, passwords match
        if ($q_account && password_verify($pass, $q_account["password"])) {
          // create session
          $_SESSION["email"] = $q_account["email"];
          $_SESSION["admin"] = filter_var($q_account["admin"], FILTER_VALIDATE_BOOLEAN);

          $res = $res->withHeader("Location", "/account");
        } else {
          // email or password wrong
          $this->get("view")->render($res, "login.twig", ["msg" => "Email or password incorrect"]);
        }
      }

      return $res;
    });
  }
}
