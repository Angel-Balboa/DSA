<?php

namespace dsa\api\controller\profesor;

use dsa\api\model\profesor\Profesor;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\Exceptions\AtributoNoExistenteException;

class CProfesor
{
    private Profesor $profesor;
    protected COperacionesSQL $Msql;

    public function __construct(Profesor $profesor) {
        $this->Msql = COperacionesSQL::getInstance();
        $this->profesor = Profesor::get_profesor_by_id($profesor->get_data("id"), $this->Msql);
    }

    public function __get(String $name) {

        switch ($name) {
            case 'profesor':
                return $this->profesor;
                break;
            default:
                throw new AtributoNoExistenteException("El atributo $name no existe", -108);
                break;
        }
    }
}