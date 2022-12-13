<?php

namespace dsa\api\controller\direc;

use dsa\api\model\carrera\Carrera;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\Exceptions\AtributoNoExistenteException;
use dsa\api\model\usuario\Usuario;

class Director
{
    private array $data;
    protected COperacionesSQL $Msql;

    public function __construct(Usuario $usuario) {
        $this->Msql = COperacionesSQL::getInstance();
        $this->data = array("carrera" => Carrera::get_carrera_by_director($usuario), "director" => $usuario);
    }

    public function __get(String $name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new AtributoNoExistenteException("El atributo $name no existe", -50);
        }
    }
}