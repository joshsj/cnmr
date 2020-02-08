<?php

declare(strict_types=1);

namespace RootHandler;

use PDO;

abstract class AbstractRouteHandler
{
  protected $db; // cnmr database


  public function __construct(PDO $db_cnmr)
  {
    $this->db = $db_cnmr;
  }
}
