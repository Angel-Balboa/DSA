<?php

namespace dsa\api\model\carga_academica;

use DateTime;
use dsa\api\model\carga_academica\Exceptions\AnioNoValidoException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaYaExistenteException;
use dsa\api\model\carga_academica\Exceptions\PeriodoNoValidoException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;
use dsa\lib\Utils\DateUtils;
use Exception;

class CargaAcademica
{
    const PERIODO_ENE_ABR = 1;
    const PERIODO_MAY_AGO = 2;
    const PERIODO_SEP_DIC = 3;

    private ?array $data;

    private ?COperacionesSQL $SqlOp;
    private bool $is_new;
    private ?array $tmp_data;

    /**
     * @param int|null $id
     * @param int|null $id_planDeEstudio
     * @param int|null $periodo
     * @param int|null $anio
     * @param bool $is_new
     * @param array|null $newData
     * @param COperacionesSQL|null $cop
     * @throws AnioNoValidoException
     * @throws CargaAcademicaNoExistenteException
     * @throws CargaAcademicaYaExistenteException
     * @throws PeriodoNoValidoException
     */
    private function __construct(?int $id=null, ?int $id_planDeEstudio=null, ?int $periodo=null, ?int $anio=null, bool $is_new=false, ?array $newData=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->is_new = $is_new;

        $this->data = null;
        $this->tmp_data = null;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_cargaAcademica_by_id($id)) {
                    throw new CargaAcademicaNoExistenteException("La carga academica con id: $id no existe", 9001);
                }
            }

            if (!is_null($id_planDeEstudio) && !is_null($periodo) && !is_null($anio)) {
                // verificamos el periodo
                if ($periodo < 1 || $periodo > 3) {
                    throw new PeriodoNoValidoException("El periodo no es válido, verifique.", 9002);
                }

                // validamos el año
                if ($anio < 2010 || $anio > intval(date("Y")) + 1) {
                    throw new AnioNoValidoException("El año no es válido, verifique", 9003);
                }

                if (!$this->_get_cargaAcademica_by_PlanPeriodoAnio($id_planDeEstudio, $periodo, $anio)) {
                    throw new CargaAcademicaNoExistenteException("No es posible obtener la carga académica con los datos proporcionados", 9004);
                }
            }
        } else {

            if ($newData["periodo"] < 1 || $newData["periodo"] > 3) {
                throw new PeriodoNoValidoException("El periodo no es válido, verifique.", 9002);
            }

            if ($newData["anio"] < 2010 || $newData["anio"] > intval(date("Y")) + 1) {
                throw new AnioNoValidoException("El año no es válido, verifique", 9003);
            }

            if ($newData["fecha_inicio"] > $newData["fecha_final"]) {
                throw new PeriodoNoValidoException("La fecha de inicio no puede ser después de la fecha final", 9012);
            }

            if ($this->_check_cargaAcademica_existente($newData["id_plan_estudios"], $newData["periodo"], $newData["anio"])) {
                throw new CargaAcademicaYaExistenteException("Ya existe una carga académica existente con los datos proporcionados", 9009);
            }

            $this->tmp_data = $newData;
        }
    }

    /**
     * @param int $id
     * @return CargaAcademica
     * @throws AnioNoValidoException
     * @throws CargaAcademicaNoExistenteException
     * @throws CargaAcademicaYaExistenteException
     * @throws PeriodoNoValidoException
     */
    public static function get_cargaAcademica_by_id(int $id, ?COperacionesSQL &$cop=null) : CargaAcademica {
        return new CargaAcademica($id, null, null, null, false, null, $cop);
    }

    public static function get_cargaAcademica_by_periodo(PlanDeEstudio $planDeEstudio, int $periodo, int $anio, ?COperacionesSQL &$cop=null) : CargaAcademica {
        return new CargaAcademica(null, $planDeEstudio->get_data("id"), $periodo, $anio, false, null, $cop);
    }

    /**
     * Factoría que obtiene una instancia de la clase PlanDeEstudio con los datos de un Plan de Estudios utilizando el Id como campo de búsqueda
     * @throws CargaAcademicaException
     */
    public static function get_planDeEstudio_by_id2(int $id, ?COperacionesSQL &$cop=null) : array {
        $tmpPlan = new CargaAcademica(null, null, null, null);

        //return $tmpPlan->_get_inner_query($id);
        return $tmpPlan->_get_all_inner($tmpPlan->_create_sqlinner($id));
    }

    private function _create_sqlinner(int $id) : String {
        $sqlquery = "SELECT usuario.nombre, usuario.apellidos, grupo.clave, carga_academica.fecha_inicio, carga_academica.fecha_final, materia.nombre as materia, plan_de_estudio.nombre as carrera From profesor INNER JOIN materia_en_grupo ON ( materia_en_grupo.profesor = $id ) INNER JOIN grupo ON ( grupo.id = materia_en_grupo.grupo ) INNER JOIN carga_academica ON ( carga_academica.id = grupo.carga_academica ) INNER JOIN materia ON ( materia.id = materia_en_grupo.materia ) INNER JOIN plan_de_estudio ON(plan_de_estudio.id=carga_academica.plan_estudios) INNER JOIN usuario on(usuario.id=profesor.usuario) where profesor.id=$id;";
        return $sqlquery;
    }
    private function _get_all_inner(String $query): array{
        $consultas = array();
        $res = $this->SqlOp->exec($query);
        foreach ($res as $r){
            $consultas[]=$r;
        }
        return $consultas;
    }

    /**
     * @param PlanDeEstudio $planDeEstudio
     * @param String $fecha_inicio
     * @param String $fecha_final
     * @param int $periodo
     * @param int|null $anio
     * @param COperacionesSQL|null $cop
     * @return CargaAcademica
     * @throws AnioNoValidoException
     * @throws CargaAcademicaException
     * @throws CargaAcademicaNoExistenteException
     * @throws CargaAcademicaYaExistenteException
     * @throws PeriodoNoValidoException
     * @throws PlanDeEstudioException
     */
    public static function crea_nueva_cargaAcademica(PlanDeEstudio $planDeEstudio, String $fecha_inicio, String $fecha_final, int $periodo=CargaAcademica::PERIODO_SEP_DIC,  ?int $anio=null, ?COperacionesSQL &$cop=null) : ?CargaAcademica {

        try {
            $dt_fecha_inicio = new DateTime($fecha_inicio);
            $dt_fecha_final = new DateTime($fecha_final);
        } catch (Exception $e) {
            throw new PeriodoNoValidoException("El formato de fecha no es correcto, por favor verifica las fechas", 9013);
        }

        $new_data = array("id_plan_estudios" => $planDeEstudio->get_data("id"), "fecha_inicio" => $dt_fecha_inicio, "fecha_final" => $dt_fecha_final, "periodo" => $periodo, "anio" => (is_null($anio) ? intval(date("Y")) : $anio));
        $tmp_cargaAcademica = new CargaAcademica(null, null, null, null, true, $new_data, $cop);

        if ($tmp_cargaAcademica->_save()) {
            return CargaAcademica::get_cargaAcademica_by_id($tmp_cargaAcademica->get_data("id"), $cop);
        }

        return null;
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpCarga = new CargaAcademica(null, null, null, null, false, null);
        return $tmpCarga->_get_all_cargasAcademicas($tmpCarga->_create_sqlquery($filtro));
    }

    private function _get_all_cargasAcademicas(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT carga_academica.id FROM carga_academica";
        $allowed_keys = ["plan_estudios", "periodo", "anio"];
        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro debe ser un diccionario con llaves permitidas, verifica la documentación", 9155);

            $sqlquery .= " WHERE ";
            $tmpArray = array();
            foreach(array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 9160);

                switch ($key) {
                    case "anio":
                        $tmpArray[] = $this->_block_query_anio($filtro["anio"]);
                        break;
                    case "periodo":
                        $tmpArray[] = $this->_block_query_periodo($filtro["periodo"]);
                        break;
                    case "plan_estudios":
                        $tmpArray[] = $this->_block_query_plan($filtro["plan_estudios"]);
                        break;
                    default:
                        break;
                }
            }
            $sqlquery .= implode(" AND ", $tmpArray);
        }
        return $sqlquery;
    }

    private function _block_query_plan($block) : String {
        if (!is_array($block)) {
            if (is_numeric($block)) {
                $strResult = "plan_estudios = $block";
            } elseif (DataChecker::check_instance_of($block, "PlanDeEstudio")) {
                $strResult = "plan_estudios = " . $block->get_data("id");
            } else {
                throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro de Plan de Estudios.", 9190);
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (is_numeric($item)) {
                    $tmpArray[] = "plan_estudios = $item";
                } elseif (DataChecker::check_instance_of($item, "PlanDeEstudio")) {
                    $tmpArray[] = "plan_estudios = " . $item->get_data("id");
                } else {
                    throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro de Plan de Estudio", 9200);
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }

        return $strResult;
    }

    private function _block_query_periodo($block) : String {
        if (!is_array($block)) {
            if (!is_numeric($block) || !in_array($block, [1, 2, 3])) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro de periodo, verifícalo.", 9182);
            $strResult = "periodo = $block";
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!is_numeric($item) || !in_array($item, [1, 2, 3])) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro de periodo, verificalo.");
                $tmpArray[] = "periodo = $item";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    private function _block_query_anio($block) : String {
        if (!is_array($block)) {
            if (!is_numeric($block) || !in_array($block, range(2010, DateUtils::current_year()+1))) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro de año, verificalo.", 9177);

            $strResult = "anio = $block";
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!is_numeric($item) || !in_array($item, range(2010, DateUtils::current_year()+1))) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro de año, verificalo.", 9184);

                $tmpArray[] = "anio = $item";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    /**
     * @param array $new_data
     * @return bool
     * @throws AnioNoValidoException
     * @throws CargaAcademicaException
     * @throws PeriodoNoValidoException
     */
    public function actualiza_datos(array $new_data): bool
    {
        $allowed_keys = ["id_plan_estudios", "periodo", "anio", "fecha_inicio", "fecha_final"];
        $tmp_data = array();

        foreach (array_keys($new_data) as $key) {
            if (!in_array($key, $allowed_keys)) {
                throw new CargaAcademicaException("La llave $key no es válida, verifique la documentación", 9158);
            }
        }

        $tmp_data["id_plan_estudios"] = $new_data["id_plan_estudios"] ?? $this->data["id_plan_estudios"];
        $tmp_data["periodo"] = $new_data["periodo"] ?? $this->data["periodo"];
        $tmp_data["anio"] = $new_data["anio"] ?? $this->data["anio"];

        if (isset($new_data["fecha_inicio"])) {
            try {
                $dt_fecha_inicio = new DateTime($new_data["fecha_inicio"]);

                if ($dt_fecha_inicio != $this->data["fecha_inicio"]) {
                    $tmp_data["fecha_inicio"] = $dt_fecha_inicio;
                } else {
                    $tmp_data["fecha_inicio"] = $this->data["fecha_inicio"];
                }
            } catch (Exception $e) {
                throw new PeriodoNoValidoException("El formato de la fecha de inicio no es válido", 9014);
            }
        } else {
            $tmp_data["fecha_inicio"] = $this->data["fecha_inicio"];
        }

        if (isset($new_data["fecha_final"])) {
            try {
                $dt_fecha_final = new DateTime($new_data["fecha_final"]);

                if ($dt_fecha_final != $this->data["fecha_final"]) {
                    $tmp_data["fecha_final"] = $dt_fecha_final;
                } else {
                    $tmp_data["fecha_final"] = $this->data["fecha_final"];
                }
            } catch (Exception $e) {
                throw new PeriodoNoValidoException("El formato de la fecha de fin de cuatrimestre no es válido", 9015);
            }
        } else {
            $tmp_data["fecha_final"] = $this->data["fecha_final"];
        }

        if ($tmp_data["periodo"] < 1 || $tmp_data["periodo"] > 3) {
            throw new PeriodoNoValidoException("El periodo no es válido, verifique.", 9002);
        }

        // validamos el año
        if ($tmp_data["anio"] < 2010 || $tmp_data["anio"] > intval(date("Y")) + 1) {
            throw new AnioNoValidoException("El año no es válido, verifique", 9003);
        }

        $this->tmp_data = $tmp_data;

        return $this->_save();
    }

    /**
     * @param null $filtro
     * @return array|mixed
     * @throws CargaAcademicaException
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["id", "id_plan_estudios", "plan_estudios", "fecha_inicio", "fecha_final", "periodo", "anio"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new CargaAcademicaException("La llave $filtro no es válida, verifica la documentación", 9207);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new CargaAcademicaException("La llave $key no es válida, verifica la documentación", 9215);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["fecha_inicio"] = $this->_data_return("fecha_inicio");
            $dataReturn["fecha_final"] = $this->_data_return("fecha_final");
        }

        return $dataReturn;
    }

    public function __toString() : String {
        return strval(PlanDeEstudio::get_planDeEstudio_by_id($this->data["id_plan_estudios"])) . "/" . $this->_get_periodo_as_string($this->data["periodo"]) . "/" . $this->data["anio"];
    }

    private function _get_cargaAcademica_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT carga_academica.plan_estudios as id_plan_estudios, carga_academica.periodo as periodo, carga_academica.fecha_inicio as fecha_inicio, carga_academica.fecha_final as fecha_final, carga_academica.anio as anio FROM carga_academica WHERE carga_academica.id = ?";

        try {
            $res = ($this->SqlOp->exec($sqlquery, "i", [$id]));

            if (count($res) == 1) {
                $this->_asignar_campos_privados($id, $res[0]["id_plan_estudios"], $res[0]["fecha_inicio"], $res[0]["fecha_final"], $res[0]["periodo"], $res[0]["anio"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _asignar_campos_privados(?int $id, int $id_plan_estudios, String $fecha_inicio, String $fecha_final, int $periodo, int $anio) {
        $this->data["id"] = $id;
        $this->data["id_plan_estudios"] = $id_plan_estudios;
        $this->data["periodo"] = $periodo;
        $this->data["fecha_inicio"] = new DateTime($fecha_inicio);
        $this->data["fecha_final"] = new DateTime($fecha_final);
        $this->data["anio"] = $anio;
    }

    private function _get_cargaAcademica_by_PlanPeriodoAnio(int $id_planDeEstudio, int $periodo, int $anio) : bool {
        $ban = false;
        $sqlquery = "SELECT carga_academica.id as id, carga_academica.fecha_inicio as fecha_inicio, carga_academica.fecha_final as fecha_final FROM carga_academica WHERE plan_estudios = ? AND periodo = ? AND anio = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "iii", [$id_planDeEstudio, $periodo, $anio]);
            if (count($res) == 1) {
                $this->_asignar_campos_privados($res[0]["id"], $id_planDeEstudio, $res[0]["fecha_inicio"], $res[0]["fecha_final"], $periodo, $anio);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _get_periodo_as_string(int $periodo) : String {
        $str_periodo = "";

        switch ($periodo) {
            CASE 1:
                $str_periodo = "Enero - Abril";
                break;
            CASE 2:
                $str_periodo = "Mayo - Agosto";
                break;
            CASE 3:
                $str_periodo = "Septiembre - Diciembre";
                break;
            default:
                throw new PeriodoNoValidoException("El periodo no es válido", 9005);
        }

        return $str_periodo;
    }

    private function _save() : bool {
        if (!$this->is_new) {
            if (!$this->_actualiza_datos_cargaAcademica()) {
                throw new CargaAcademicaException("No fue posible actualizar los datos, verifique", 9008);
            }
        } else {
            if (!$this->_crea_nueva_cargaAcademica()) {
                throw new CargaAcademicaException("No fue posible generar la nueva carga académica", 9010);
            }
        }
        return true;
    }

    private function _actualiza_datos_cargaAcademica() : bool {
        $sqlquery = "UPDATE carga_academica SET carga_academica.plan_estudios = ?, carga_academica.periodo = ?, carga_academica.anio = ?, fecha_inicio = ?, fecha_final = ? WHERE carga_academica.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iiissi", [$this->tmp_data["id_plan_estudios"], $this->tmp_data["periodo"], $this->tmp_data["anio"], $this->tmp_data["fecha_inicio"]->format('Y/m/d'), $this->tmp_data["fecha_final"]->format('Y/m/d'), $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _check_cargaAcademica_existente(int $id_plan_estudios, int $periodo, int $anio) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) as existe FROM carga_academica WHERE plan_estudios = ? AND periodo = ? AND anio = ?";

        try {
            $ban = intval($this->SqlOp->exec($sqlquery, "iii", [$id_plan_estudios, $periodo, $anio])[0]["existe"]) > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _crea_nueva_cargaAcademica() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO carga_academica (plan_estudios, periodo, fecha_inicio, fecha_final, anio) VALUES (?, ?, ?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iissi", [$this->tmp_data["id_plan_estudios"], $this->tmp_data["periodo"], $this->tmp_data["fecha_inicio"]->format('Y/m/d'), $this->tmp_data["fecha_final"]->format('Y/m/d'), $this->tmp_data["anio"]]) == 1;
            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    function _data_return($key) {
        if ($key == "id_plan_estudios" || $key == "plan_estudios") {
            $dataReturn = $this->data["id_plan_estudios"];
        } elseif ($key == "fecha_inicio" || $key == "fecha_final") {
            $dataReturn = $this->data[$key]->format("Y/m/d");
        } else {
            $dataReturn = $this->data[$key];
        }
        return $dataReturn;
    }
}