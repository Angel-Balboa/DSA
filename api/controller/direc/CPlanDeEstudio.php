<?php

namespace dsa\api\controller\direc;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudiosYaExisteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;

class CPlanDeEstudio extends Director
{
    public function __construct(Usuario $usuario) {
        parent::__construct($usuario);
    }

    /**
     * @param String $nombre
     * @param int $anio
     * @param String $clave
     * @param String $nivel
     * @return PlanDeEstudio|null
     * @throws CarreraNoExistenteException
     * @throws PlanDeEstudioException
     * @throws PlanDeEstudioNoExistenteException
     * @throws PlanDeEstudiosYaExisteException
     */
    public function crea_nuevo_plan_de_estudio(String $nombre, int $anio, String $clave, String $nivel="Ing"): ?PlanDeEstudio
    {
        return PlanDeEstudio::crea_nuevo_plan_de_estudio(Carrera::get_carrera_by_id($this->carrera->get_data("id")), $nombre, $anio, $clave, $nivel, $this->Msql);
    }

    /**
     * @param PlanDeEstudio $planDeEstudio
     * @param array $newData
     * @return bool
     * @throws PlanDeEstudioException
     * @throws PlanDeEstudioNoExistenteException
     * @throws PlanDeEstudiosYaExisteException
     */
    public function actualiza_datos_de_plan(PlanDeEstudio $planDeEstudio, array $newData): bool
    {
        if (!in_array($planDeEstudio->get_data("id"), PlanDeEstudio::get_all(["carrera" => $this->carrera->get_data("id")]))) {
            throw new PlanDeEstudioException("No tienes permiso de actualizar el plan de estudio $planDeEstudio", -51);
        }

        $tmpPlan = PlanDeEstudio::get_planDeEstudio_by_id($planDeEstudio->get_data("id"), $this->Msql);
        return $tmpPlan->actualiza_datos_de_planDeEstudio($newData);
    }

}