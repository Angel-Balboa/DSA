<?php

namespace dsa\api\controller\admin;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudiosYaExisteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;

class CPlanDeEstudio extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Carrera $carrera
     * @param String $nombre
     * @param int $anio
     * @param String $clave
     * @param String $nivel
     * @return PlanDeEstudio|null
     * @throws PlanDeEstudioException
     * @throws PlanDeEstudioNoExistenteException
     * @throws PlanDeEstudiosYaExisteException
     */
    public function crea_nuevo_plan_de_estudio(Carrera $carrera, String $nombre, int $anio, String $clave, String $nivel="Ing") {
        return PlanDeEstudio::crea_nuevo_plan_de_estudio($carrera, $nombre, $anio, $clave, $nivel, $this->Msql);
    }

    /**
     * @param PlanDeEstudio $planDeEstudio
     * @param array $newData
     * @return bool
     * @throws PlanDeEstudioException
     * @throws PlanDeEstudioNoExistenteException
     * @throws PlanDeEstudiosYaExisteException
     */
    public function actualiza_datos_de_plan(PlanDeEstudio $planDeEstudio, array $newData) : bool {
        $tmpPlan = PlanDeEstudio::get_planDeEstudio_by_id($planDeEstudio->get_data("id"));

        return $tmpPlan->actualiza_datos_de_planDeEstudio($newData);
    }
}