<?php

namespace dsa\api\model\planeacion_academica;

use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\planeacion_academica\Exceptions\DatosDePlaneacionAcademicaIncorrectosException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaNoExistenteException;
use dsa\api\model\planeacion_asesoria\PlaneacionAsesoria;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;
use dsa\lib\Utils\DateUtils;
use dsa\api\model\profesor\Profesor;

class PlaneacionAcademica
{
    private ?array $data;

    private bool $isNew;
    private COperacionesSQL $SqlOp;
    private ?array $tmpData;

    private function __construct(?int $id=null, bool $isNew=false, ?array $newData=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->isNew = $isNew;
        $this->tmpData = null;
        $this->data = null;

        if (!$isNew) {
            if (!is_null($id)) {
                if (!$this->_get_planeacionAcademica_by_id($id)) {
                    throw new PlaneacionAcademicaNoExistenteException("La planeación Académica con id: $id, no existe", 12034);
                }
            }
        } else {
            $this->tmpData = $newData;
        }
    }

    /**
     * @param int $id
     * @param COperacionesSQL|null $cop
     * @return PlaneacionAcademica
     * @throws PlaneacionAcademicaNoExistenteException
     */
    public static function get_PlaneacionAcademica_by_id(int $id, ?COperacionesSQL &$cop=null) : PlaneacionAcademica {
        return new PlaneacionAcademica($id, false, null, $cop);
    }

    /**
     * @param Profesor $profesor
     * @param int $periodo
     * @param int|null $anio
     * @param string $estado
     * @param COperacionesSQL|null $cop
     * @return PlaneacionAcademica|null
     * @throws DatosDePlaneacionAcademicaIncorrectosException
     * @throws PlaneacionAcademicaException
     * @throws ProfesorException
     */
    public static function crea_nueva_PlaneacionAcademica(Profesor $profesor, int $periodo=1, ?int $anio=null, ?COperacionesSQL &$cop=null) : ?PlaneacionAcademica {
        $anio = is_null($anio) ? DateUtils::current_year() : $anio;
        $tmp_data = array("periodo" => $periodo, "anio" => $anio, "estado" => "iniciada", "profesor" => $profesor->get_data("id"));
        $tmp_planeacion = new PlaneacionAcademica(null, true, $tmp_data, $cop);
        if (!$tmp_planeacion->_save()) {
            $tmp_planeacion = null;
            throw new PlaneacionAcademicaException("No se ha podido crear la nueva planeación académica, intenta más tarde.", 12044);
        }
        else {
            return PlaneacionAcademica::get_PlaneacionAcademica_by_id($tmp_planeacion->get_data("id"));
        }
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpPlaneacion = new PlaneacionAcademica(null, false, null);
        return $tmpPlaneacion->_get_all_planeaciones($tmpPlaneacion->_create_sqlquery($filtro));
    }

    public function get_actividades_academicas() : array {
        return Actividad::get_all(["planeacion_academica" => $this->data["id"]]);
    }

    public function get_planeacion_asesorias() : int {
        return PlaneacionAsesoria::get_planeacionAsesoria_by_planeacionAcademica($this)->get_data("id");
    }

    /**
     * @param null $filtro
     * @return array|mixed
     * @throws PlaneacionAcademicaException
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["id", "periodo", "anio", "estado", "profesor", "id_profesor"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) throw new PlaneacionAcademicaException("La llave $filtro no es válida, verifica la documentación", 12073);
                $dataReturn = $this->_data_return($filtro);
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) throw new PlaneacionAcademicaException("La llave $key no es válida, verifica la documentación", 12078);

                    $dataReturn[$key] = $this->_data_return($key);
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["id_profesor"] = $this->_data_return("profesor");
        }

        return $dataReturn;
    }

    public function actualiza_datos(array $data) : bool {
        $allowed_keys = ["periodo", "anio", "estado", "profesor"];
        $tmp_data = array();

        foreach(array_keys($data) as $key) {
            if (!in_array($key, $allowed_keys)) throw new PlaneacionAcademicaException("La llave $key no es válida, verifica la documentación", 12103);
        }

        $tmp_data["periodo"] = $data["periodo"] ?? $this->data["periodo"];
        if (!in_array($tmp_data["periodo"], [1, 2, 3])) throw new PlaneacionAcademicaException("El valor del periodo no es correcto", 12107);

        $tmp_data["anio"] = $data["anio"] ?? $this->data["anio"];
        if (!in_array($tmp_data["anio"], range(2010, DateUtils::current_year()+1))) throw new PlaneacionAcademicaException("El valor para el año no es correcta", 12110);

        $tmp_data["estado"] = $data["estado"] ?? $this->data["estado"];
        if (!in_array($tmp_data["estado"], ["iniciada", "edicion", "finalizada", "aceptada"])) throw new PlaneacionAcademicaException("El estado de la Planeación académica no es correcto", 12113);

        $tmp_data["profesor"] = $data["profesor"] ?? $this->data["profesor"];

        $this->tmpData = $tmp_data;
        return $this->_save();
    }

    private function _data_return($key) {
        if ($key == "profesor" || $key == "id_profesor") {
            $dataReturn = $this->data["profesor"];
        } else {
            $dataReturn = $this->data[$key];
        }
        return $dataReturn;
    }

    /**
     * @return bool
     * @throws DatosDePlaneacionAcademicaIncorrectosException
     * @throws PlaneacionAcademicaException
     */
    private function _save() : bool {
        if ($this->isNew) {
            $this->_valida_datos_para_guardar();
            if (!$this->_crea_nueva_planeacionAcademica_db()) {
                throw new PlaneacionAcademicaException("No se ha podido crear la planeación académica en el base de datos, verifique.", 12060);
            }
        } else {
            if (!$this->_actualiza_datos_de_PlaneacionAcademica()) {
                throw new PlaneacionAcademicaException("No se ha podido actualiza los datos de la planeacion académica", 12143);
            }
        }
        return true;
    }

    private function _actualiza_datos_de_PlaneacionAcademica() : bool {
        $sqlquery = "UPDATE planeacion_academica SET periodo = ?, year = ?, estado = ?, profesor = ? WHERE planeacion_academica.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iisii", [$this->tmpData["periodo"], $this->tmpData["anio"], $this->tmpData["estado"], $this->tmpData["profesor"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _crea_nueva_planeacionAcademica_db() : bool {
        $sqlquery = "INSERT INTO planeacion_academica (periodo, year, estado, profesor) VALUES (?, ?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iisi", [$this->tmpData["periodo"], $this->tmpData["anio"], $this->tmpData["estado"], $this->tmpData["profesor"]]) == 1;
            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * @return bool
     * @throws DatosDePlaneacionAcademicaIncorrectosException
     */
    private function _valida_datos_para_guardar() : bool {
        $allowed_keys = ["periodo", "anio", "estado", "profesor"];
        $allowed_values = array("periodo" => [1, 2, 3], "anio" => range(2010, DateUtils::current_year()+1), "estado" => array("iniciada", "edicion", "finalizada", "aceptada"));
        // verificamos que los datos a guardar no sean nulos.
        if (is_null($this->tmpData)) throw new DatosDePlaneacionAcademicaIncorrectosException("Los datos para guardar estan vacios, verifica.", 12059);
        // verificamos que sea un diccionario
        if (!DataChecker::isAssoc($this->tmpData)) throw new DatosDePlaneacionAcademicaIncorrectosException("Los datos deben ser un diccionario con llaves permitidas, verifica la documentación", 12065);
        // verificamos que las llaves seas permitidas
        foreach (array_keys($this->tmpData) as $key) {
            if (!in_array($key, $allowed_keys)) throw new DatosDePlaneacionAcademicaIncorrectosException("La llave $key no es permitida, verifica la documentación");
        }

        //verificamos cada llave.
        // verificamos el periodo
        if (!in_array($this->tmpData["periodo"], $allowed_values["periodo"])) throw new DatosDePlaneacionAcademicaIncorrectosException("El periodo debe ser un entero positivo en el rango [1, 2, 3]", 12074);
        if (!in_array($this->tmpData["anio"], $allowed_values["anio"])) throw new DatosDePlaneacionAcademicaIncorrectosException("El año no es válido, verificalo", 12075);
        if (!in_array($this->tmpData["estado"], $allowed_values["estado"])) throw new DatosDePlaneacionAcademicaIncorrectosException("El estado de la planeación no es correcto", 12076);
        if (!is_numeric($this->tmpData["profesor"])) throw new DatosDePlaneacionAcademicaIncorrectosException("El valor del ID del profesor debe ser entero positivo". 12077);

        return true;
    }

    /**
     * @param int|null $id
     * @return bool
     */
    private function _get_planeacionAcademica_by_id(?int $id) : bool {
        $ban = false;
        $query = "SELECT planeacion_academica.id as id, planeacion_academica.periodo as periodo, planeacion_academica.year as anio, planeacion_academica.estado as estado, planeacion_academica.profesor as profesor FROM planeacion_academica WHERE planeacion_academica.id = ?";

        try {
            $res = $this->SqlOp->exec($query, "i", [$id]);
            if (count($res) == 1) {
                $this->_asigna_campos_privados($id, $res[0]["periodo"], $res[0]["anio"], $res[0]["estado"], $res[0]["profesor"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * @param int $id
     * @param int $periodo
     * @param int $anio
     * @param String $estado
     * @param int $profesor
     */
    private function _asigna_campos_privados(int $id, int $periodo, int $anio, String $estado, int $profesor) {
        $this->data["id"] = $id;
        $this->data["periodo"] = $periodo;
        $this->data["anio"] = $anio;
        $this->data["estado"] = $estado;
        $this->data["profesor"] = $profesor;
    }

    private function _get_all_planeaciones(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }
        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT planeacion_academica.id FROM planeacion_academica";
        $allowed_keys = ["periodo", "anio", "estado", "profesor"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro deber ser un diccionario con llaves permitidas, verifica la documentación", 12082);

            $sqlquery .= " WHERE ";
            $tmpArray = array();
            foreach(array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 12087);

                switch ($key) {
                    case "periodo":
                        $allowed_vals = [1, 2, 3];
                        $tmpArray[] = $this->_block_query($filtro["periodo"], "periodo", $allowed_vals);
                        break;
                    case "anio":
                        $allowed_vals = range(2010, DateUtils::current_year()+1);
                        $tmpArray[] = $this->_block_query($filtro["anio"], "year", $allowed_vals);
                        break;
                    case "estado":
                        $allowed_vals = ["iniciada", "edicion", "finalizada", "aceptada"];
                        $tmpArray[] = $this->_block_query($filtro["estado"], "estado", $allowed_vals, true);
                        break;
                    case "profesor":
                        $tmpArray[] = $this->_block_query_profesor($filtro["profesor"]);
                        break;
                    default:
                        echo "$key aún no implementada";
                        break;
                }
            }
            $sqlquery .= implode(" AND ", $tmpArray);
        }

        return $sqlquery;
    }

    private function _block_query_profesor($block) : String {
        if (!is_array($block)) {
            if (is_numeric($block)) {
                $strResult = "profesor = $block";
            } elseif (DataChecker::check_instance_of($block, "Profesor")) {
                $strResult = "profesor = " . $block->get_data("id");
            } else {
                throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido para el filtro por profesor, verifica la documentación", 12122);
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (is_numeric($item)) {
                    $tmpArray[] = "profesor = $item";
                } elseif (DataChecker::check_instance_of($item, "CProfesor")) {
                    $tmpArray[] = "profesor = " . $item->get_data("id");
                } else {
                    throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido para el filtro por profesor, verifica la documentación", 12132);
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    private function _block_query($block, String $str_attr, array $allowed_vals, bool $isString=false) : String {
        if (!is_array($block)) {
            if (!in_array($block, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el fitro por $str_attr, verifica la documentación", 12105);
            $strResult = ($isString) ? "$str_attr = '$block'" : "$str_attr = $block";
        } else {
            $tmpArray = array();
            foreach($block as $item) {
                if (!in_array($item, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro por $str_attr, verifica la documentación", 12110);
                $tmpArray[] = ($isString) ? "$str_attr = '$item'" : "$str_attr = $item";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }
}