<?php

namespace dsa\api\controller\rrhhs;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;

class CProfesor extends RecursoHumano
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actualiza_datos(Profesor $profesor, array $newData) : bool {
        $tmpProfesor = Profesor::get_profesor_by_id($profesor->get_data("id"), $this->Msql);
        $oldCarreraAdscripcion = $tmpProfesor->get_data("carrera_adscripcion");
        if ($tmpProfesor->actualiza_datos_de_profesor($newData)) {
            $newCarreraAdscripcion = $tmpProfesor->get_data("carrera_adscripcion");

            if ($oldCarreraAdscripcion == $newCarreraAdscripcion) { // si la carrera es la misma
                return true;
            } else { // si se cambió la carrera de ascripción
                $profImparteEn = $tmpProfesor->get_carreras_de_imparticion(); // carreras donnde actualmente imparte materias

                if (!in_array($newCarreraAdscripcion, $profImparteEn)) { // si no imparte en la nueva carrera
                    $tmpCarrera = Carrera::get_carrera_by_id($newCarreraAdscripcion);
                    return $tmpProfesor->agrega_carrera_para_impartir($tmpCarrera);
                } else {
                    return true;
                }
            }
        }
        return false;
    }
}