<?php

namespace dsa\api\controller\direc;

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;

class CCargaAcademica extends Director
{

    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
    }

    public function crea_carga_academica(PlanDeEstudio $planDeEstudio, String $fecha_inicio, String $fecha_final, int $periodo=3, ?int $anio=null) {
        return CargaAcademica::crea_nueva_cargaAcademica($planDeEstudio, $fecha_inicio, $fecha_final, $periodo, $anio, $this->Msql);
    }

    public function actualiza_datos_de_carga_academica(CargaAcademica $cargaAcademica, array $newData) {

        $tmpCarga = CargaAcademica::get_cargaAcademica_by_id($cargaAcademica->get_data("id"), $this->Msql);
        return $tmpCarga->actualiza_datos($newData);
    }
}