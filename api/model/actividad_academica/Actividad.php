<?php

namespace dsa\api\model\actividad_academica;

use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaException;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaNoExistenteException;
use dsa\api\model\actividad_academica\Exceptions\DatosDeActividadAcademicaException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DataChecker;

class Actividad
{

    const TIPO_GESTION = "GESTION";
    const TIPO_CAPACITACION = "CAPACITACION";
    const TIPO_VINCULACION = "VINCULACION";
    const TIPO_PROMOCION = "PROMOCION";

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
                if (!$this->_get_actividad_by_id($id)) {
                    throw new ActividadAcademicaNoExistenteException("La actividada académica con Id: $id, no existe.", 13026);
                }
            }
        } else {
            $this->tmpData = $newData;
        }
    }

    public static function get_actividad_academica_by_id(int $id, ?COperacionesSQL &$cop=null) : Actividad {
        return new Actividad($id, false,null, $cop);
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @param String $tipo
     * @param String $descripcion
     * @param String $empresa_receptora
     * @param int $horas
     * @param String $evidencia
     * @param COperacionesSQL|null $cop
     * @return Actividad|null
     * @throws ActividadAcademicaException
     * @throws ActividadAcademicaNoExistenteException
     * @throws PlaneacionAcademicaException
     */
    public static function crea_actividad_academica(PlaneacionAcademica $planeacionAcademica, String $tipo, String $descripcion, String $empresa_receptora, int $horas, String $evidencia, COperacionesSQL &$cop=null) : ?Actividad {
        $newData = array("tipo" => $tipo, "descripcion" => $descripcion, "empresa_receptora" => $empresa_receptora, "horas" => $horas, "evidencia" => $evidencia, "planeacion_academica" => $planeacionAcademica->get_data("id"));
        $tmpActividad = new Actividad(null, true, $newData, $cop);
        if ($tmpActividad->_save()) {
            return Actividad::get_actividad_academica_by_id($tmpActividad->get_data("id"));
        }
        return null;
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpActividad = new Actividad(null, false, null);
        return $tmpActividad->_get_all_actividades($tmpActividad->_create_sqlquery($filtro));
    }

    public function get_data($filtro=null) {
        $allowed_keys = ["id", "tipo", "descripcion", "empresa_receptora", "horas", "evidencia", "id_planeacion_academica", "planeacion_academica"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) throw new ActividadAcademicaException("La llave $filtro no es válida, verifica la documentación", 13046);

                $dataReturn = $this->_data_return($filtro);
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) throw new ActividadAcademicaException("La llave $key no es válida, verifica la documentación", 13052);

                    $dataReturn[$key] = $this->_data_return($key);
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["planeacion_academica"] = $this->_data_return("planeacion_academica");
        }

        return $dataReturn;
    }

    /**
     * @param array $newData
     * @return bool
     * @throws ActividadAcademicaException
     */
    public function actializa_datos(array $newData) : bool {
        $allowed_keys = ["descripcion", "empresa_receptora", "horas", "evidencia"];
        $tmp_data = array();

        if (!DataChecker::isAssoc($newData)) {
            throw new ActividadAcademicaException("Se debe enviar un array asociativo con llaves válidas, verifica la documentación", 13113);
        }

        foreach (array_keys($newData) as $item) {
            if (!in_array($item, $allowed_keys)) throw new ActividadAcademicaException("La llave $item no es válida, verifica la documentación", 13070);
        }

        $tmp_data["descripcion"] = $newData["descripcion"] ?? $this->data["descripcion"];
        $tmp_data["empresa_receptora"] = (!isset($newData["empresa_receptora"])) ? $this->data["empresa_receptora"] : $newData["empresa_receptora"];

        $tmp_data["horas"] = $newData["horas"] ?? $this->data["horas"];
        if ($tmp_data["horas"] < 1) throw new ActividadAcademicaException("El valor de las horas no puede ser menor a 1", 13077);

        $tmp_data["evidencia"] = $newData["evidencia"] ?? $this->data["evidencia"];
        $this->tmpData = $tmp_data;
        return $this->_save();
    }

    /**
     * @return bool
     * @throws ActividadAcademicaException
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
            throw new ActividadAcademicaException("No se logró eliminar la Actividad, vuelve a intentar o contacta con el administrador del sistema", 13138);
        }
        return $ban;
    }

    /**
     * @return bool
     * @throws CConnexionException
     * @throws SQLTransactionException
     */
    private function _elimina_actividad_de_bd() : bool {
        $sqlquery = "DELETE FROM actividad WHERE id = ?";
        return $this->SqlOp->exec($sqlquery, "i", [$this->data["id"]]) == 1;
    }

    /**
     * @return bool
     * @throws ActividadAcademicaException
     */
    private function _save() : bool {
        if (!$this->isNew) {
            if (!$this->_actualiza_datos_de_actividad()) {
                throw new ActividadAcademicaException("No se ha podido actualizar la actividad académica", 13088);
            }
        } else {
            $this->_valida_datos_para_guardar();
            if (!$this->_crea_nueva_activadadAcademica_db()) {
                throw new ActividadAcademicaException("No se ha podido guardar la actividad académica", 13130);
            }
        }

        return true;
    }

    private function _crea_nueva_activadadAcademica_db() : bool {
        $sqlquery = "INSERT INTO actividad(tipo, descripcion, empresa_receptora, horas, evidencia, planeacion_academica) VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "sssisi", [$this->tmpData["tipo"], $this->tmpData["descripcion"], $this->tmpData["empresa_receptora"], $this->tmpData["horas"], $this->tmpData["evidencia"], $this->tmpData["planeacion_academica"]]) == 1;

            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _valida_datos_para_guardar() : bool {
        $allowed_keys = ["tipo", "descripcion", "empresa_receptora", "horas", "evidencia", "planeacion_academica"];

        if (is_null($this->tmpData)) throw new DatosDeActividadAcademicaException("Los datos para guardar esetan vacios, verifica", 13136);

        if (!DataChecker::isAssoc($this->tmpData)) throw new DatosDeActividadAcademicaException("Los datos deben ser un diccionario con llaves permitidas, verifica la documentación", 13139);

        foreach (array_keys($this->tmpData) as $key) {
            if (!in_array($key, $allowed_keys)) throw new DatosDeActividadAcademicaException("La llave $key no es permitida, verifica la documentación", 13142);
        }

        //verificamos cada llave
        if (!in_array($this->tmpData["tipo"], ["GESTION", "CAPACITACION", "VINCULACION", "PROMOCION"])) throw new DatosDeActividadAcademicaException("El valor del \"tipo\" de actividad académica no es válido, verifica", 13146);
        if ((strlen($this->tmpData["descripcion"]) < 1) || is_null($this->tmpData["descripcion"])) throw new DatosDeActividadAcademicaException("La descripción no puede estar vacia o nulla", 13147);
        if ((strlen($this->tmpData["empresa_receptora"]) < 1) || is_null($this->tmpData["empresa_receptora"])) throw new DatosDeActividadAcademicaException("El nombre de la empresa receptora no puede estar vacia o nulla", 13148);
        if ((strlen($this->tmpData["evidencia"]) < 1) || is_null($this->tmpData["evidencia"])) throw new DatosDeActividadAcademicaException("La descripción de la evidencia no puede estar vacia o nulla", 13149);
        if (!is_numeric($this->tmpData["horas"]) || ($this->tmpData["horas"] < 0)) throw new DatosDeActividadAcademicaException("La cantidad de horas no es correcta, verifica", 13150);
        if (!is_numeric($this->tmpData["planeacion_academica"])) throw new DatosDeActividadAcademicaException("El valor del Id de la planeación académica no es correcto", 13151);

        return true;

    }

    /**
     * @return bool
     */
    private function _actualiza_datos_de_actividad() : bool {
        $sqlquery = "UPDATE actividad SET descripcion = ?, empresa_receptora = ?, horas = ?, evidencia = ? WHERE actividad.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssisi", [$this->tmpData["descripcion"], $this->tmpData["empresa_receptora"], $this->tmpData["horas"], $this->tmpData["evidencia"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * @param $key
     * @return mixed
     */
    private function _data_return($key) {
        if ($key == "id_planeacion_academica" || $key == "planeacion_academica") {
            $dataReturn = $this->data["id_planeacion_academica"];
        } else {
            $dataReturn = $this->data[$key];
        }
        return $dataReturn;
    }

    private function _get_actividad_by_id(int $id) : bool {
        $sqlquery = "SELECT actividad.tipo as tipo, actividad.descripcion as descripcion, actividad.empresa_receptora as empresa_receptora, actividad.horas as horas, actividad.evidencia as evidencia, actividad.planeacion_academica as planeacion_academica FROM actividad WHERE actividad.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($id, $res[0]["tipo"], $res[0]["descripcion"], $res[0]["empresa_receptora"], $res[0]["horas"], $res[0]["evidencia"], $res[0]["planeacion_academica"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _asigna_campos_privados(int $id, String $tipo, ?String $descripcion, ?String $empresa_receptora, int $horas, ?String $evidencia, int $planeacion_academica) {
        $this->data["id"] = $id;
        $this->data["tipo"] = $tipo;
        $this->data["descripcion"] = $descripcion;
        $this->data["empresa_receptora"] = $empresa_receptora;
        $this->data["horas"] = $horas;
        $this->data["evidencia"] = $evidencia;
        $this->data["id_planeacion_academica"] = $planeacion_academica;
    }

    private function _get_all_actividades(String $query) : array {
        $ids = array();

        $res = $this->SqlOp->exec($query);

        foreach($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT actividad.id FROM actividad";
        $allowed_keys = ["tipo", "planeacion_academica"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro debe ser un diccionario con llaves permitidas, verifica la documentación", 13083);

            $sqlquery .= " WHERE ";
            $tmpArray = array();
            foreach (array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 13088);

                switch ($key) {
                    case "tipo":
                        $tmpArray[] = $this->_block_query_tipo($filtro["tipo"]);
                        break;
                    case "planeacion_academica":
                        $tmpArray[] = $this->_block_query_planeacion($filtro["planeacion_academica"]);
                        break;
                    default:
                        echo "$key aún no implementada";
                        break;
                }
            }
            $sqlquery .= implode(" AND ", $tmpArray);
        }
        return $sqlquery . " ORDER BY actividad.tipo";
    }

    private function _block_query_planeacion($block) : String {
        if (is_numeric($block)) {
            $strResult = "planeacion_academica = $block";
        } elseif (DataChecker::check_instance_of($block, "PlaneacionAcademica")) {
            $strResult = "planeacion_academica = " . $block->get_data("id");
        } else {
            throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro por planeación académica", 13112);
        }
        return $strResult;
    }

    private function _block_query_tipo($block) : String {
        $allowed_vals = ["GESTION", "CAPACITACION", "VINCULACION", "PROMOCION"];
        if (!is_array($block)) {
            if (!in_array($block, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro por tipo", 13107);
            $strResult = "tipo = '$block'";
        } else {
            $tmpArray = array();
            foreach($block as $item) {
                if (!in_array($item, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro por tipo", 13112);
                $tmpArray[] = "tipo = '$item'";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }
}
