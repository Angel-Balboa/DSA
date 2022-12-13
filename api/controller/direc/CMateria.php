<?php

namespace dsa\api\controller\direc;

use dsa\api\model\materia\Materia;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;

class CMateria extends Director
{
    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
    }

    public function crea_nueva_materia(PlanDeEstudio $planDeEstudio, String $clave_materia, String $nombre, int $creditos, int $cuatrimestre, int $posicion_horizontal, int $horas_totales, String $tipo="Especialidad"): ?Materia
    {
        return Materia::crea_nueva_materia($planDeEstudio, $clave_materia, $nombre, $creditos, $cuatrimestre, $posicion_horizontal, $horas_totales, $tipo, $this->Msql);
    }

    public function actualiza_datos(Materia $materia, array $newData) : bool {
        $tmpMateria = Materia::get_materia_by_id($materia->get_data("id"), $this->Msql);
        return $tmpMateria->actualiza_datos_de_materia($newData);
    }
}