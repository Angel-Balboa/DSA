<?php

namespace dsa\api\controller\admin;

use dsa\api\model\planeacion_academica\Exceptions\DatosDePlaneacionAcademicaIncorrectosException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;

class CPlaneacionAcademica extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Profesor $profesor
     * @param int $periodo
     * @param int|null $anio
     * @return PlaneacionAcademica|null
     * @throws DatosDePlaneacionAcademicaIncorrectosException
     * @throws PlaneacionAcademicaException
     * @throws ProfesorException
     */
    public function crea_nueva_planeacion_academica(Profesor $profesor, int $periodo=1, ?int $anio=null) {
        return PlaneacionAcademica::crea_nueva_PlaneacionAcademica($profesor, $periodo, $anio, $this->Msql);
    }
}