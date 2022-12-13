<?php

namespace dsa\api\model\disponibilidad;

use dsa\api\model\disponibilidad\Exceptions\DisponibilidadException;
use dsa\api\model\disponibilidad\Exceptions\NumeroDeDiaIncorrectoException;
use dsa\api\model\disponibilidad\Exceptions\NumerodeHoraIncorrectoException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\api\model\profesor\Profesor;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;

class Disponibilidad
{
    const LUNES = 0;
    const MARTES = 1;
    const MIERCOLES = 2;
    const JUEVES = 3;
    const VIERNES = 4;
    const SABADO = 5;

    const H700_755 = 0;
    const H755_850 = 1;
    const H850_945 = 2;
    const H945_1040 = 3;
    const H1110_1205 = 4;
    const H1205_1300 = 5;
    const H1300_1355 = 6;
    const H1400_1455 = 7;
    const H1455_1550 = 8;
    const H1550_1645 = 9;
    const H1645_1740 = 10;
    const H1800_1855 = 11;
    const H1855_1950 = 12;
    const H1950_2045 = 13;

    private int $id_profesor;
    private array $dispMatrix;
    private COperacionesSQL $SqlOp;

    private function __construct(int $id_profesor, ?COperacionesSQL &$cop = null)
    {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->id_profesor = $id_profesor;
        $this->_update_dispMatrix();
    }

    public static function get_disponibilidad_by_profesor(Profesor $profesor, ?COperacionesSQL &$cop = null): Disponibilidad
    {
        return new Disponibilidad($profesor->get_data("id", $cop));
    }

    /** Método que obtiene la disponibilidad de un profesor
     * Si el filtro es nulo, se retornará la matriz completa de la disṕonibilidad del profesor
     * @param $filtro
     * @return array
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["dia", "hora"];
        if (!is_null($filtro)) {
            if (is_array($filtro) && DataChecker::isAssoc($filtro)) {
                $array_keys = array_keys($filtro);
                foreach ($array_keys as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new DisponibilidadException("La llave $key no es permitida en el filtro de disponibilidad", 12066);
                    } elseif (!is_int($filtro[$key])) {
                        throw new DisponibilidadException("La llave $key debe ser un entero permitido, verifica la documentación", 12068);
                    }
                }

                if (in_array("dia", $array_keys)) {
                    if ($filtro["dia"] < 0 || $filtro["dia"] > 5) {
                        throw new NumeroDeDiaIncorrectoException("El número de día no es correcto, verifica la documentación", 12074);
                    }

                    if (in_array("hora", $array_keys)) {

                        if ($filtro["hora"] < 0 || $filtro["hora"] > 13) {
                            throw new NumerodeHoraIncorrectoException("El número de hora no es correcto, verifica la documentación", 12080);
                        }

                        return $this->dispMatrix[$filtro["dia"]][$filtro["hora"]];
                    } else {
                        return $this->dispMatrix[$filtro["dia"]];
                    }
                } elseif (in_array("hora", $array_keys)) {

                    if ($filtro["hora"] < 0 || $filtro["hora"] > 13) {
                        throw new NumerodeHoraIncorrectoException("El número de hora no es correcto, verifica la documentación", 12090);
                    }

                    $tmpArray = array();
                    for ($i=0; $i<6; $i++) {
                        $tmpArray[$i] = $this->dispMatrix[$i][$filtro["hora"]];
                    }

                    return $tmpArray;
                }
            } else {
                throw new DisponibilidadException("Se esperaba un array asosiativo con las claves: dia y/o hora para el fitro de disponibilidad", 12070);
            }
        } else {
            return $this->dispMatrix;
        }
    }

    public function quita_disponbilidad(int $dia, int $hora) : bool {
        $ban = false;

        if ($dia < 0 || $dia > 5) {
            throw new NumeroDeDiaIncorrectoException("El día no es correcto, verifica.", 12001);
        }

        if ($hora < 0 || $hora > 13) {
            throw new NumerodeHoraIncorrectoException("La hora asignada no es correcta, verificala", 12060);
        }

        if ($this->_del_disp($dia, $hora)) {
            $this->dispMatrix[$dia][$hora] = 0;
            $ban = true;
        } else {
            throw new DisponibilidadException("No se ha podido quitar la disponibilidad del profesor", 12070);
        }
        return $ban;
    }

    private function _del_disp(int $dia, int $hora) : bool {
        $sqlquery = "DELETE FROM disponibilidad WHERE dia = ? and hora = ? and profesor = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "iii", [$dia, $hora, $this->id_profesor])==1;
        } catch (CConnexionException|SQLTransactionException $e) {
            $res = false;
        }

        return $res;
    }

    public function agrega_disponibilidad(int $dia, int $hora) : bool {
        $ban = false;
        if ($dia < 0 || $dia > 5) {
            throw new NumeroDeDiaIncorrectoException("El día no es correcto, verifica.", 12001);
        }

        if ($hora < 0 || $hora > 13) {
            throw new NumerodeHoraIncorrectoException("La hora asignada no es correcta, verificala", 12060);
        }

        if ($this->_add_disp($dia, $hora)) {
            $this->dispMatrix[$dia][$hora] = 1;
            $ban = true;
        } else {
            throw new DisponibilidadException("No se ha podido agregar la disponibilidad al profesor", 12101);
        }
        return $ban;
    }

    private function _add_disp(int $dia, int $hora) : bool {
        $sqlquery = "INSERT INTO disponibilidad (dia, hora, profesor) VALUES (?, ?, ?)";

        try {
            $res = $this->SqlOp->exec($sqlquery, "iii", [$dia, $hora, $this->id_profesor])==1;
        } catch (CConnexionException|SQLTransactionException $e) {
            $res = false;
        }

        return $res;
    }


    private function _update_dispMatrix() {
        $this->dispMatrix = $this->_get_disponibilidad_from_db();
    }

    private function _get_disponibilidad_from_db() : array {
        $tmpMatrix = $this->_init_disp_matrix();

        $sqlquery = "SELECT * FROM disponibilidad WHERE profesor = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->id_profesor]);

            foreach ($res as $d) {
                $tmpMatrix[$d["dia"]][$d["hora"]] = 1;
            }

        } catch (CConnexionException|SQLTransactionException $e) {
            ;
        }

        return $tmpMatrix;
    }

    private function _init_disp_matrix() : array {
        $tmpArray = array();
        for ($day=0; $day<6; $day++) {
            $hourArray = array();
            for ($hour=0; $hour<14; $hour++) {
                $hourArray[] = 0;
            }
            $tmpArray[] = $hourArray;
        }

        return $tmpArray;
    }
}