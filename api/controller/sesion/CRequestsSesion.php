<?php

namespace dsa\api\controller\sesion;

use dsa\api\controller\sesion\Exceptions\SesionNoInizializadaException;

class CRequestsSesion
{
    private ?Sesion $sesion;

    private function __construct() {
        $this->sesion = null;
    }

    public static function getInstance() {
        return new CRequestsSesion();
    }

    public static function inits(): CRequestsSesion
    {
        $tmpSesion = new CRequestsSesion();
        $tmpSesion->initInstance();

        if (!$tmpSesion->is_logged) {
            throw new SesionNoInizializadaException("No se ha inicializado la sesión del usuario", -2000);
        }
        return $tmpSesion;
    }

    private function initInstance() {
        $this->sesion = Sesion::getInstance();
    }

    public function __get($name) {
        if (!is_null($this->sesion)) {
            return $this->sesion->$name;
        } else {
            throw new SesionNoInizializadaException("La sesión no ha sido inicializada", 21001);
        }
    }
}