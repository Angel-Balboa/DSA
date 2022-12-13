<?php

namespace dsa\api\controller\rrhhs;

use dsa\lib\conexionSQL\COperacionesSQL;

class RecursoHumano
{
    protected COperacionesSQL $Msql;

    public function __construct() {
        $this->Msql = COperacionesSQL::getInstance();
    }
}