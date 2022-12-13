<?php

namespace dsa\api\controller\direc;

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\grupo\Grupo;
use dsa\api\model\usuario\Usuario;
use dsa\lib\conexionSQL\COperacionesSQL;

class CGrupo extends Director
{
    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
    }

    public function crea_grupo_en_carga(CargaAcademica $cargaAcademica, String $clave, int $turno, int $cuatrimestre, ?String $fecha_inicio, ?String $fecha_final) {
        return Grupo::crea_nuevo_grupo($cargaAcademica, $clave, $turno,$cuatrimestre,$fecha_inicio, $fecha_final, $this->Msql);
    }

    public function finalizar_grupo(Grupo $grupo) {
        $tmpGrupo = Grupo::get_grupo_by_id($grupo->get_data("id"), $this->Msql);
        $newData = array("finalizado" => true);

        $tmpGrupo->actualiza_datos_grupo($newData);
    }
}