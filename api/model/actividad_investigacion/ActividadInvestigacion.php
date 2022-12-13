<?php

namespace dsa\api\model\actividad_investigacion;

use dsa\api\model\actividad_investigacion\Exceptions\ActividadInvestigacionException;
use dsa\api\model\actividad_investigacion\Exceptions\ActividadInvestigacionNoExisteException;
use dsa\api\model\actividad_investigacion\Exceptions\DatosDeActividadInvestigacionException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;

class ActividadInvestigacion
{
    private ?array $data;

    private bool $isNew;
    private ?COperacionesSQL $SqlOp;
    private ?array $tmpData;

    private function __construct(?int $id=null, bool $isNew=false, ?array $newData=null, COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->data = null;
        $this->isNew = $isNew;
        $this->tmpData = null;

        if (!$isNew) {
            if (!is_null($id)) {
                if (!$this->_get_actividadInvestigacion_by_id($id)) {
                    throw new ActividadInvestigacionNoExisteException("La actividad de investigación con Id: $id, no existe.", 14025);
                }
            }
        } else {
            $this->tmpData = $newData;
        }
    }

    /***
     * @param int $id
     * @param COperacionesSQL|null $cop
     * @return ActividadInvestigacion
     * @throws ActividadInvestigacionNoExisteException
     */
    public static function get_actividadInvestigacion_by_id(int $id, ?COperacionesSQL &$cop=null) : ActividadInvestigacion {
        return new ActividadInvestigacion($id, false, null, $cop);
    }

    public static function crea_actividadInvestigacion(PlaneacionAcademica $planeacionAcademica, String $actividad, String $tipo, int $avance_actual, int $avance_esperado, \DateTime $fecha_termino, ?COperacionesSQL &$cop=null) : ?ActividadInvestigacion {
        $newData = array("actividad" => $actividad, "tipo" => $tipo, "avance_actual" => $avance_actual, "avance_esperado" => $avance_esperado, "fecha_termino" => $fecha_termino->format("Y/m/d"), "planeacion_academica" => $planeacionAcademica->get_data("id"));
        $tmpActividadInvestigacion = new ActividadInvestigacion(null, true, $newData, $cop);
        if ($tmpActividadInvestigacion->_save()) {
            return ActividadInvestigacion::get_actividadInvestigacion_by_id($tmpActividadInvestigacion->get_data("id"));
        }
        return null;
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpActividadInv = new ActividadInvestigacion(null, false, null);
        return $tmpActividadInv->_get_all_actividadesInv($tmpActividadInv->_create_sqlquery($filtro));
    }

    private function _get_all_actividadesInv(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);
        foreach ($res as $r) {
            $ids[] = $r["id"];
        }
        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT actividad_investigacion.id FROM actividad_investigacion";
        $allowed_keys = ["planeacion_academica"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro debe ser un diccionario con llaves permitidas, verifica la documentación", 14070);

            $sqlquery .= " WHERE ";
            $tmp_array = array();
            foreach(array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave $key no es permitida, verifica la documentación", 14074);

                switch ($key) {
                    case "planeacion_academica":
                        $tmp_array[] = $this->_block_query($filtro["planeacion_academica"]);
                        break;
                    default:
                        echo "$key aún no implementada";
                        break;
                }
            }
            $sqlquery .= implode(" AND ", $tmp_array);
        }
        return $sqlquery .= " ORDER BY actividad_investigacion.id";
    }

    private function _block_query($block) : String {
        if (is_numeric($block)) {
            $strResult = "planeacion_academica = $block";
        } elseif (DataChecker::check_instance_of($block, "PlaneacionAcademica")) {
            $strResult = "planeacion_academica = " . $block->get_data("id");
        } else {
            throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro por planeacion académica", 14097);
        }
        return $strResult;
    }

    public function get_data($filtro=null) {
        $allowed_keys = ["id", "actividad", "tipo", "avance_actual", "avance_esperado", "fecha_termino", "id_planeacion_academica", "planeacion_academica"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) throw new ActividadInvestigacionException("La llave $filtro no es válida, verifica la documentación", 14051);
                $dataReturn = $this->_data_return($filtro);
            } else {
                $dataReturn = array();
                foreach($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) throw new ActividadInvestigacionException("La llave $key no es válida, verifica la documentación", 14056);
                    $dataReturn[$key] = $this->_data_return($key);
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["fecha_termino"] = $this->_data_return("fecha_termino");
            $dataReturn["planeacion_academica"] = $this->_data_return("planeacion_academica");
        }
        return $dataReturn;
    }

    public function actualiza_datos(array $newData) : bool {
        $allowed_keys = ["actividad", "tipo", "avance_actual", "avance_esperado", "fecha_termino"];
        $tmp_data = array();

        $tmp_data["actividad"] = $newData["actividad"] ?? $this->data["actividad"];
        $tmp_data["tipo"] = $newData["tipo"] ?? $this->data["tipo"];
        $tmp_data["avance_actual"] = $newData["avance_actual"] ?? $this->data["avance_actual"];
        if (!is_numeric($tmp_data["avance_actual"])) throw new ActividadInvestigacionException("El valor del avance actual debe ser entero positivo", 14087);
        $tmp_data["avance_esperado"] = $newData["avance_esperado"] ?? $this->data["avance_esperado"];
        if (!is_numeric($tmp_data["avance_esperado"]) || $tmp_data["avance_esperado"] < $tmp_data["avance_actual"]) throw new ActividadInvestigacionException("El valor del avance esperado debe ser entero positivo y superior al avance actual", 14089);
        $tmp_data["fecha_termino"] = $newData["fecha_termino"] ?? $this->data["fecha_termino"];

        if (!DataChecker::check_instance_of($tmp_data["fecha_termino"], "DateTime")) throw new ActividadInvestigacionException("La fecha de termino debe ser un objeto de la clase DateTime con fecha válida", 14092);
        $this->tmpData = $tmp_data;
        return $this->_save();

    }

    /**
     * @return bool
     * @throws ActividadInvestigacionException
     */
    public function elimina_actividad() : bool {
        $ban = false;
        try {
            if ($this->_elimina_actividad_de_bd()) {
                $this->data = null;
                $this->SqlOp = null;
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            throw new ActividadInvestigacionException("No se ha podido eliminar la actividad, vuelve a intentar o contacta con el administrador del sistema", 14159);
        }
        return $ban;
    }

    /**
     * @return bool
     * @throws CConnexionException
     * @throws SQLTransactionException
     */
    private function _elimina_actividad_de_bd() : bool {
        $sqlquery = "DELETE FROM actividad_investigacion WHERE id = ?";
        return $this->SqlOp->exec($sqlquery, "i", [$this->data["id"]]);
    }

    private function _data_return($key) {
        if ($key == "id_planeacion_academica" || $key == "planeacion_academica") {
            $dataReturn = $this->data["id_planeacion_academica"];
        } elseif ($key == "fecha_termino") {
            $dataReturn = $this->data["fecha_termino"]->format("Y/m/d");
        } else {
            $dataReturn = $this->data[$key];
        }
        return $dataReturn;
    }

    private function _get_actividadInvestigacion_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT actividad_investigacion.actividad as actividad, actividad_investigacion.tipo as tipo, actividad_investigacion.avance_actual as avance_actual, actividad_investigacion.avance_esperado as avance_esperado, actividad_investigacion.fecha_termino as fecha_termino, actividad_investigacion.planeacion_academica as planeacion_academica FROM actividad_investigacion WHERE actividad_investigacion.id = ?";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);
            if (count($res) == 1){
                $this->_asigna_campos_privados($id, $res[0]["actividad"], $res[0]["tipo"], $res[0]["avance_actual"], $res[0]["avance_esperado"], $res[0]["fecha_termino"], $res[0]["planeacion_academica"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _asigna_campos_privados(int $id, String $actividad, String $tipo, int $avance_actual, int $avance_esperado, String $fecha_termino, int $planeacion_academica) {
        $this->data["id"] = $id;
        $this->data["actividad"] = $actividad;
        $this->data["tipo"] = $tipo;
        $this->data["avance_actual"] = $avance_actual;
        $this->data["avance_esperado"] = $avance_esperado;
        $this->data["fecha_termino"] = new \DateTime($fecha_termino);
        $this->data["id_planeacion_academica"] = $planeacion_academica;
    }

    private function _save() : bool {
        if ($this->isNew) {
            $this->_valida_datos_de_actividadInvestigacion();
            if (!$this->_crea_nueva_actividadInvestigacion_db()) {
                throw new ActividadInvestigacionException("No se ha podido crear la actividad de investigación", 14060);
            }
        } else {
            if (!$this->_actualiza_datos_de_actividadInvestigacion()) {
                throw new ActividadInvestigacionException("No se ha podido actualizar la actividad de investigación", 14142);
            }
        }

        return true;
    }

    private function _actualiza_datos_de_actividadInvestigacion() : bool {
        $sqlquery = "UPDATE actividad_investigacion SET actividad = ?, tipo = ?, avance_actual = ?, avance_esperado = ?, fecha_termino = ? WHERE actividad_investigacion.id = ?";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssiisi", [$this->tmpData["actividad"], $this->tmpData["tipo"], $this->tmpData["avance_actual"], $this->tmpData["avance_esperado"], $this->tmpData["fecha_termino"]->format("Y/m/d"), $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _crea_nueva_actividadInvestigacion_db() : bool {
        $sqlquery = "INSERT INTO actividad_investigacion(actividad, tipo, avance_actual, avance_esperado, fecha_termino, planeacion_academica) VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssiisi", [$this->tmpData["actividad"], $this->tmpData["tipo"], $this->tmpData["avance_actual"], $this->tmpData["avance_esperado"], $this->tmpData["fecha_termino"], $this->tmpData["planeacion_academica"]]) == 1;

            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * @return bool
     * @throws DatosDeActividadInvestigacionException
     */
    private function _valida_datos_de_actividadInvestigacion() : bool {
        $allowed_keys = ["actividad", "tipo", "avance_actual", "avance_esperado", "fecha_termino", "planeacion_academica"];

        if (is_null($this->tmpData)) throw new DatosDeActividadInvestigacionException("Los datos para guardar la actividad de investigación no deben estar vacios, verifica", 14071);

        if (!DataChecker::isAssoc($this->tmpData)) throw new DatosDeActividadInvestigacionException("Los datos deben ser un diccionario con llaves permitidas, verifica la documentación", 14074);

        foreach(array_keys($this->tmpData) as $key) {
            if (!in_array($key, $allowed_keys)) throw new DatosDeActividadInvestigacionException("La llave $key no es permitida, verifica la documentación", 14077);
        }

        //verificamos cada llave por separado
        if (!isset($this->tmpData["actividad"]) || strlen($this->tmpData["actividad"]) < 1) throw new DatosDeActividadInvestigacionException("La descripción de la actividad no puede ser vacia o nula", 14081);
        if (!isset($this->tmpData["tipo"]) || strlen($this->tmpData["tipo"]) < 1) throw new DatosDeActividadInvestigacionException("El tipo de actividad no puede estar vacio o nulo", 14082);
        if (!isset($this->tmpData["avance_actual"]) || !is_numeric($this->tmpData["avance_actual"]) || $this->tmpData["avance_actual"] < 0 || $this->tmpData["avance_actual"] > 100) throw new DatosDeActividadInvestigacionException("El valor del avance actual debe ser un entero positivo en el rago [0, 100]", 14083);
        if (!isset($this->tmpData["avance_esperado"]) || !is_numeric($this->tmpData["avance_esperado"]) || $this->tmpData["avance_esperado"] < 0 || $this->tmpData["avance_esperado"] > 100 || $this->tmpData["avance_esperado"] <= $this->tmpData["avance_actual"]) throw new DatosDeActividadInvestigacionException("El valor del avance actual debe ser un entero positivo en el rago [0, 100] y superior al avance actual", 14084);



        return true;
    }
}