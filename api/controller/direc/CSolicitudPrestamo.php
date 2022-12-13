<?php

namespace dsa\api\controller\direc;

use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\prestamo\Exceptions\SolicitudNoEncontradaException;
use dsa\api\model\prestamo\Exceptions\SolicitudPrestamoException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\prestamo\SolicitudPrestamo;
use dsa\lib\Exceptions\FormatoDeFechaException;

class CSolicitudPrestamo extends Director
{
    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
    }

    /**
     * @param Carrera $carreraObjetivo
     * @param Profesor $profesor
     * @return SolicitudPrestamo
     * @throws CarreraException
     * @throws CarreraNoExistenteException
     * @throws SolicitudNoEncontradaException
     * @throws SolicitudPrestamoException
     * @throws ProfesorException
     * @throws ProfesorNoExisteException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     * @throws FormatoDeFechaException
     */
    public function crea_solicitud_prestamo(Carrera $carreraObjetivo, Profesor $profesor) {
        return SolicitudPrestamo::crea_nueva_solicitudDePrestamo($this->director, $carreraObjetivo->get_director(), $profesor, $this->Msql);
    }
}