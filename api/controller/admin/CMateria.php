<?php

namespace dsa\api\controller\admin;

use dsa\api\model\materia\Exceptions\MateriaException;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\api\model\materia\Exceptions\MateriaYaExistenteException;
use dsa\api\model\materia\Materia;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;

class CMateria extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param PlanDeEstudio $planDeEstudio
     * @param String $clave_materia
     * @param String $nombre
     * @param int $creditos
     * @param int $cuatrimestre
     * @param int $posicion_horizontal
     * @param int $horas_totales
     * @param String $tipo
     * @return Materia|null
     * @throws MateriaException
     * @throws MateriaNoExistenteException
     * @throws MateriaYaExistenteException
     * @throws PlanDeEstudioException
     */
    public function crea_nueva_materia(PlanDeEstudio $planDeEstudio, String $clave_materia, String $nombre, int $creditos, int $cuatrimestre, int $posicion_horizontal, int $horas_totales, String $tipo="Especialidad"): ?Materia
    {
        return Materia::crea_nueva_materia($planDeEstudio, $clave_materia, $nombre, $creditos, $cuatrimestre, $posicion_horizontal, $horas_totales, $tipo, $this->Msql);
    }

    /**
     * @param Materia $materia
     * @param array $newData
     * @return bool
     * @throws MateriaException
     * @throws MateriaNoExistenteException
     */
    public function actualiza_datos(Materia $materia, array $newData) : bool {
        $tmpMateria = Materia::get_materia_by_id($materia->get_data("id"), $this->Msql);
        return $tmpMateria->actualiza_datos_de_materia($newData);
    }
}