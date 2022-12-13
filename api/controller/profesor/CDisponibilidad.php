<?php

namespace dsa\api\controller\profesor;

use dsa\api\model\disponibilidad\Disponibilidad;
use dsa\api\model\profesor\Profesor;

class CDisponibilidad extends CProfesor
{
    private Disponibilidad $disponibilidad;

    public function __construct(Profesor $profesor)
    {
        parent::__construct($profesor);
        $this->disponibilidad = Disponibilidad::get_disponibilidad_by_profesor($this->profesor, $this->Msql);
    }

    public function agrega_disponibilidad(int $dia, int $hora) {

        return $this->disponibilidad->agrega_disponibilidad($dia, $hora);
    }

    public function elimina_disponibilidad(int $dia, $hora) {
        return $this->disponibilidad->quita_disponbilidad($dia, $hora);
    }

    public function cambia_disponbilidad(int $dia, $hora) {
        $DH = $this->disponibilidad->get_data(["dia" => $dia, "hora" => $hora]);

        if ($DH > 0) {
            return $this->disponibilidad->quita_disponbilidad($dia, $hora);
        } else {
            return $this->disponibilidad->agrega_disponibilidad($dia, $hora);
        }
    }
}