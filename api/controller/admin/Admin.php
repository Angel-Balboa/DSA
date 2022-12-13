<?php

namespace dsa\api\controller\admin;

use dsa\lib\conexionSQL\COperacionesSQL;

class Admin
{
    protected COperacionesSQL $Msql;

    public function __construct() {
        $this->Msql = COperacionesSQL::getInstance();
    }
}