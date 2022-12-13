<?php

namespace dsa\api\controller\admin;

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\carga_academica\Exceptions\AnioNoValidoException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaYaExistenteException;
use dsa\api\model\carga_academica\Exceptions\PeriodoNoValidoException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;

class CCargaAcademica extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param PlanDeEstudio $planDeEstudio
     * @param String $fecha_inicio
     * @param String $fecha_final
     * @param int $periodo
     * @param int|null $anio
     * @return CargaAcademica|null
     * @throws AnioNoValidoException
     * @throws CargaAcademicaException
     * @throws CargaAcademicaNoExistenteException
     * @throws CargaAcademicaYaExistenteException
     * @throws PeriodoNoValidoException
     * @throws PlanDeEstudioException
     */
    public function crea_carga_academica(PlanDeEstudio $planDeEstudio, String $fecha_inicio, String $fecha_final, int $periodo=3, ?int $anio=null) {
        return CargaAcademica::crea_nueva_cargaAcademica($planDeEstudio, $fecha_inicio, $fecha_final, $periodo, $anio);
    }

    /**
     * @param CargaAcademica $cargaAcademica
     * @param array $newData
     * @return bool
     * @throws AnioNoValidoException
     * @throws CargaAcademicaException
     * @throws CargaAcademicaNoExistenteException
     * @throws CargaAcademicaYaExistenteException
     * @throws PeriodoNoValidoException
     */
    public function actualiza_datos_de_carga_academica(CargaAcademica $cargaAcademica, array $newData) {
        $tmpCarga = CargaAcademica::get_cargaAcademica_by_id($cargaAcademica->get_data("id"), $this->Msql);
        return $tmpCarga->actualiza_datos($newData);
    }
}