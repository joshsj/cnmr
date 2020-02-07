<?php

namespace RootHandler;

use PDO;

abstract class AbstractRouteHandler
{
  private $db;

  public function __construct(PDO $db_cnmr)
  {
    $this->db = $db_cnmr;
  }

  public function  __invoke(\Slim\Routing\RouteCollectorProxy $group)
  {
  }
}
