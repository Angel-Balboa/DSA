<?php

namespace dsa\api\model\grupo;


use DateTime;
use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\carga_academica\Exceptions\AnioNoValidoException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaYaExistenteException;
use dsa\api\model\carga_academica\Exceptions\PeriodoNoValidoException;
use dsa\api\model\grupo\Exceptions\ClaveDeGrupoExistenteException;
use dsa\api\model\grupo\Exceptions\CuatrimestreFueraDeRango;
use dsa\api\model\grupo\Exceptions\GrupoException;
use dsa\api\model\grupo\Exceptions\GrupoNoExistenteException;
use dsa\api\model\grupo\Exceptions\TurnoFueraDeRangoException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Exceptions\ErrorDesconocidoException;
use dsa\lib\Utils\DataChecker;
use dsa\lib\Utils\DateUtils;
use Exception;

class Grupo
{
    const TURNO_MATUTINO = 1;
    const TURNO_VESPERTINO = 2;
    const TURNO_NOCTURNO = 3;
    const TURNO_SABATINO = 4;

    const CUATRIMESTRE_INMERSION = 0;
    const CUATRIMESTRE_1 = 1;
    const CUATRIMESTRE_2 = 2;
    const CUATRIMESTRE_3 = 3;
    const CUATRIMESTRE_4 = 4;
    const CUATRIMESTRE_5 = 5;
    const CUATRIMESTRE_6 = 6;
    const CUATRIMESTRE_7 = 7;
    const CUATRIMESTRE_8 = 8;
    const CUATRIMESTRE_9 = 9;
    const CUATRIMESTRE_10 = 10;

    private ?array $data;

    private bool $is_new;
    private ?array $tmp_data;
    private COperacionesSQL $SqlOp;

    /**
     * @param int|null $id
     * @param String|null $clave
     * @param int|null $id_carga_academica
     * @param bool $is_new
     * @param array|null $newData
     * @param COperacionesSQL|null $cop
     * @throws ClaveDeGrupoExistenteException
     * @throws CuatrimestreFueraDeRango
     * @throws ErrorDesconocidoException
     * @throws GrupoNoExistenteException
     * @throws TurnoFueraDeRangoException
     */
    private function __construct(?int $id=null, ?String $clave=null, ?int $id_carga_academica=null, bool $is_new=false, ?array $newData=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->is_new = $is_new;

        $this->data = null;
        $this->tmp_data = null;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_grupo_by_id($id)) {
                    throw new GrupoNoExistenteException("No se ha podido encontrar el grupo con id: $id", 10001);
                }
            }

            if (!is_null($clave) && !is_null($id_carga_academica)) {
                if (!$this->_get_grupo_by_clave_cargaAcademica($clave, $id_carga_academica)) {
                    throw new GrupoNoExistenteException("El grupo con la clave: $clave, no existe en la Carga Academica", 10002);
                }
            }
        } else {
            if ($this->_check_clave($newData["clave"], $newData["id_carga_academica"])) {
                throw new ClaveDeGrupoExistenteException("Ya existe un grupo con la clave: . " . $newData["clave"] . " en la carga académica", 10004);
            }

            if ($newData["turno"] < 1 || $newData["turno"] > 4) {
                throw new TurnoFueraDeRangoException("El valor de turno no es válido, por favor verificalo", 10005);
            }

            if ($newData["cuatrimestre"] < 0 || $newData["cuatrimestre"] > 10) {
                throw new CuatrimestreFueraDeRango("El número de cuatrimestre esta fuera de rango, verifícalo", 10006);
            }

            $this->tmp_data = $newData;
        }
    }

    public static function get_grupo_by_id(int $id, ?COperacionesSQL &$cop=null) : Grupo {
        return new Grupo($id, null, null, false, null, $cop);
    }

    public static function get_grupo_by_cargaAcademica_clave(CargaAcademica $cargaAcademica, String $clave, ?COperacionesSQL &$cop=null) : Grupo {
        return new Grupo(null, $clave, $cargaAcademica->get_data("id"), false, null, $cop);
    }

    public static function crea_nuevo_grupo(CargaAcademica $cargaAcademica, String $clave, int $turno=Grupo::TURNO_MATUTINO, int $cuatrimestre=1, String $fecha_inicio=null, String $fecha_final=null, ?COperacionesSQL &$cop=null) : ?Grupo {

        if (!is_null($fecha_inicio)) {
            try {
                $tmp_dt_inicio = new DateTime($fecha_inicio);
                $dt_fecha_inicio = $tmp_dt_inicio->format("Y/m/d");
            }
            catch (Exception $e) {
                throw new CuatrimestreFueraDeRango("El formato de fecha de inicio de cuatrimestre no es correcto", 10085);
            }
        } else {
            $dt_fecha_inicio = null;
        }

        if (!is_null($fecha_final)) {
            try {
                $tmp_dt_final = new DateTime($fecha_final);
                $dt_fecha_final = $tmp_dt_final->format("Y/m/d");
            }
            catch (Exception $e) {
                throw new CuatrimestreFueraDeRango("El formato de fecha del final de cuatrimestre para el grupo no es correcto", 10086);
            }
        } else {
            $dt_fecha_final = null;
        }

        $new_data = array("clave" => $clave, "turno" => $turno, "cuatrimestre" => $cuatrimestre, "id_carga_academica" => $cargaAcademica->get_data("id"), "fecha_inicio" => $dt_fecha_inicio, "fecha_final" => $dt_fecha_final);
        $tmp_grupo = new Grupo(null, null, null,true, $new_data, $cop);

        if ($tmp_grupo->_save()) {
            return Grupo::get_grupo_by_id($tmp_grupo->get_data("id"), $cop);
        }

        return null;
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpGrupo = new Grupo(null, null, null, false, null);
        return $tmpGrupo->_get_all_grupos($tmpGrupo->_create_sqlquery($filtro));
    }

    /**
     * @param null $filtro
     * @return array|false|float|int|mixed|string|null
     * @throws GrupoException
     */
    public function get_data($filtro=null) {

        $allowed_keys = ["id", "clave", "turno", "cuatrimestre", "carga_academica", "id_carga_academica", "fecha_inicio", "fecha_final", "semanas", "finalizado"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new GrupoException("La llave $filtro no es válida, verifica la documentación.", 10163);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new GrupoException("La llave $key no es válida, verifica la documentación.", 10171);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["fecha_final"] = $this->_data_return("fecha_final");
            $dataReturn["fecha_inicio"] = $this->_data_return("fecha_inicio");
            $dataReturn["semanas"] = $this->_data_return("semanas");
        }

        return $dataReturn;
    }

    public function esta_finalizado() : bool {
        return $this->data["finalizado"];
    }

    /**
     * @return String
     */
    public function __toString() : String {
        return $this->data["id_carga_academica"] . "-" . $this->data["clave"];
    }

    /**
     * @param array $new_data
     * @return bool
     * @throws ClaveDeGrupoExistenteException
     * @throws CuatrimestreFueraDeRango
     * @throws ErrorDesconocidoException
     * @throws GrupoException
     * @throws PeriodoNoValidoException
     * @throws TurnoFueraDeRangoException
     */
    public function actualiza_datos_grupo(array $new_data) : bool {
        $tmp_data = array();

        if (isset($new_data["clave"]) && $new_data["clave"] != $this->data["clave"]) {
            if ($this->_check_clave($new_data["clave"])) {
                throw new ClaveDeGrupoExistenteException("Ya existe la clave " . $new_data["clave"] . " en la Carga Academica", 10003);
            }
            $tmp_data["clave"] = $new_data["clave"];
        } else {
            $tmp_data["clave"] = $this->data["clave"];
        }

        $tmp_data["turno"] = $new_data["turno"] ?? $this->data["turno"];
        if ($tmp_data["turno"] != $this->data["turno"]) {
            if (!in_array($tmp_data["turno"], array(1, 2, 3, 4))) {
                throw new TurnoFueraDeRangoException("El turno no es correcto, verifica la documentación", 10172);
            }
        }


        $tmp_data["cuatrimestre"] = $new_data["cuatrimestre"] ?? $this->data["cuatrimestre"];
        if ($tmp_data["cuatrimestre"] != $this->data["cuatrimestre"]) {
            if (!in_array($tmp_data["cuatrimestre"], array(0 ,1 ,2, 3, 4, 5, 6 ,7 ,8 ,9, 10))) {
                throw new CuatrimestreFueraDeRango("El cuatrimestre esta fuera de rango, debe ser entre 0 y 10");
            }
        }

        $tmp_data["finalizado"] = $new_data["finalizado"] ?? $this->data["finalizado"];

        if (isset($new_data["fecha_inicio"])) {
            if (strlen($new_data["fecha_inicio"]) < 3) {
                $tmp_data["fecha_inicio"] = null;
            } else {
                try {
                    $dt_fecha_inicio = new DateTime($new_data["fecha_inicio"]);
                    $tmp_data["fecha_inicio"] = $dt_fecha_inicio->format('Y/m/d');
                } catch (Exception $e) {
                    throw new PeriodoNoValidoException("El formato de la fecha de inicio no es válido", 9014);
                }
            }
        } else {
            $tmp_data["fecha_inicio"] = is_null($this->data["fecha_inicio"]) ? null : $this->data["fecha_inicio"]->format("Y/m/d");
        }


        if (isset($new_data["fecha_final"])) {
            if (strlen($new_data["fecha_final"]) < 3) {
                $tmp_data["fecha_final"] = null;
            }
            else {
                try {
                    $dt_fecha_final = new DateTime($new_data["fecha_final"]);
                    $tmp_data["fecha_final"] = $dt_fecha_final->format('Y/m/d');
                } catch (Exception $e) {
                    throw new PeriodoNoValidoException("El formato de la fecha final del grupo no es válido", 9015);
                }
            }
        } else {
            $tmp_data["fecha_final"] = is_null($this->data["fecha_final"]) ? null : $this->data["fecha_final"]->format('Y/m/d');
        }



        $this->tmp_data = $tmp_data;

        return $this->_save();
    }

    private function _get_grupo_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT grupo.clave as clave, grupo.turno as turno, grupo.cuatrimestre as cuatrimestre, grupo.carga_academica as id_carga_academica, grupo.fecha_inicio as fecha_inicio, grupo.fecha_final as fecha_final, grupo.finalizado as finalizado FROM grupo WHERE grupo.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($id, $res[0]["clave"], $res[0]["turno"], $res[0]["cuatrimestre"], $res[0]["id_carga_academica"], $res[0]["finalizado"],$res[0]["fecha_inicio"], $res[0]["fecha_final"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * @param int $id
     * @param String $clave
     * @param int $turno
     * @param int $cuatrimestre
     * @param int $id_carga_academica
     * @param String|null $fecha_inicio
     * @param String|null $fecha_final
     * @throws Exception
     */
    private function _asigna_campos_privados(int $id, String $clave, int $turno, int $cuatrimestre, int $id_carga_academica, bool $finalizado, ?String $fecha_inicio, ?String $fecha_final) {
        $this->data["id"] = $id;
        $this->data["clave"] = $clave;
        $this->data["turno"] = $turno;
        $this->data["cuatrimestre"] = $cuatrimestre;
        $this->data["id_carga_academica"] = $id_carga_academica;
        $this->data["fecha_inicio"] = (is_null($fecha_inicio)) ? null : new DateTime($fecha_inicio);
        $this->data["fecha_final"] = (is_null($fecha_final)) ? null : new DateTime($fecha_final);
        $this->data["finalizado"] = $finalizado;
    }

    /**
     * @param string $clave
     * @param int $id_carga_academica
     * @return bool
     * @throws Exception
     */
    private function _get_grupo_by_clave_cargaAcademica(string $clave, int $id_carga_academica) : bool {
        $ban = false;
        $sqlquery = "SELECT grupo.id as id, grupo.turno as turno, grupo.cuatrimestre as cuatrimestre, grupo.fecha_inicio as fecha_inicio, grupo.fecha_final as fecha_final, grupo.finalizado as finalizado FROM grupo WHERE grupo.clave = ? AND carga_academica = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "si", [$clave, $id_carga_academica]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($res[0]["id"], $clave, $res[0]["turno"], $res[0]["cuatrimestre"], $id_carga_academica, $res[0]["finalizado"], $res[0]["fecha_inicio"], $res[0]["fecha_final"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _save() : bool {

        if (!$this->is_new) {
            if (!$this->_actualiza_datos_grupo()) {
                throw new GrupoException("No se logró actualizar los datos del grupo, verifica", 10005);
            }
        } else {
            if (!$this->_crea_nuevo_grupo_db()) {
                throw new GrupoException("No fue posible agregar el nuevo grupo, intentalo de nuevo", 10010);
            } else {
                $this->_get_grupo_by_id($this->data["id"]);
            }
        }
        return true;
    }

    /**
     * @param String $clave
     * @param int|null $id_carga_academica
     * @return bool
     * @throws ErrorDesconocidoException
     */
    private function _check_clave(String $clave, ?int $id_carga_academica=null) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) AS existe FROM grupo WHERE clave = ? AND carga_academica = ?";

        $id_carga_academica = is_null($id_carga_academica) ? $this->data["id_carga_academica"] : $id_carga_academica;

        try {
            $ban = intval($this->SqlOp->exec($sqlquery, "si", [$clave, $id_carga_academica])[0]["existe"]) > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            throw new ErrorDesconocidoException($e->getMessage(), $e->getCode());
        }

        return $ban;
    }

    /**
     * @return bool
     */
    private function _actualiza_datos_grupo() : bool {
        $ban = false;
        $sqlquery = "UPDATE grupo SET clave = ?, turno = ?, cuatrimestre = ?, fecha_inicio = ?, fecha_final = ?, finalizado = ? WHERE id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "siissii", [$this->tmp_data["clave"], $this->tmp_data["turno"], $this->tmp_data["cuatrimestre"], $this->tmp_data["fecha_inicio"], $this->tmp_data["fecha_final"], $this->tmp_data["finalizado"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _crea_nuevo_grupo_db() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO grupo (clave, turno, cuatrimestre, fecha_inicio, fecha_final, carga_academica) VALUES (?, ?, ?, ?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "siissi", [$this->tmp_data["clave"], $this->tmp_data["turno"], $this->tmp_data["cuatrimestre"], $this->tmp_data["fecha_inicio"], $this->tmp_data["fecha_final"], $this->tmp_data["id_carga_academica"]]) == 1;

            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _data_return($key) {
        if ($key == "id_carga_academica" || $key == "carga_academica") {
            $dataReturn = $this->data["id_carga_academica"];
        } elseif ($key == "fecha_inicio" || $key == "fecha_final") {
            $dataReturn = is_null($this->data[$key]) ? null : $this->data[$key]->format("Y/m/d");
        } elseif ($key == "semanas") {
            $str_fromDate = is_null($this->data["fecha_inicio"]) ? CargaAcademica::get_cargaAcademica_by_id($this->data["id_carga_academica"])->get_data("fecha_inicio") : $this->data["fecha_inicio"]->format("Y/m/d");
            $str_toDate = is_null($this->data["fecha_final"]) ? CargaAcademica::get_cargaAcademica_by_id($this->data["id_carga_academica"])->get_data("fecha_final") : $this->data["fecha_final"]->format('Y/m/d');
            $dataReturn = DateUtils::datediff("ww", $str_fromDate, $str_toDate, false);
        } else {
            $dataReturn = $this->data[$key];
        }

        return $dataReturn;
    }

    private function _get_all_grupos(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT grupo.id FROM grupo";
        $allowed_keys = ["turno", "cuatrimestre", "finalizado", "carga_academica"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro debe ser un diccionario con llaves válidas, verifica la documentación", 10155);

            $sqlquery .= " WHERE ";
            $tmpArray = array();
            foreach (array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es válida como filtro, verifica la documentación.", 10160);

                switch ($key) {
                    case "turno":
                    case "cuatrimestre":
                        $allowed_vals = ($key == "turno") ? [1, 2, 3] : range(0, 10);
                        $tmpArray[] = $this->_block_query($filtro[$key], $key, $allowed_vals);
                        break;
                    case "finalizado":
                        $tmpArray[] = $this->_block_query_finalizado($filtro["finalizado"]);
                        break;
                    case "carga_academica":
                        $tmpArray[] = $this->_block_query_cargaAcademica($filtro["carga_academica"]);
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

    private function _block_query_cargaAcademica($block) : String {
        if (!is_array($block)) {
            if (is_numeric($block)) {
                $strResult = "carga_academica = $block";
            } elseif (DataChecker::check_instance_of($block, "CargaAcademica")) {
                $strResult = "carga_academica = " . $block->get_data("id");
            } else {
                throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro de carga_academica", 10191);
            }
        } else {
            $tmpArray = array();
            foreach($block as $item) {
                if (is_numeric($item)) {
                    $tmpArray[] = "carga_academica = $item";
                } elseif (DataChecker::check_instance_of($item, "CargaAcademica")) {
                    $tmpArray[] = "carga_academica = " . $item->get_data("id");
                } else {
                    throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro de carga_academica", 10201);
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    private function _block_query_finalizado($block) : String {
        if (!is_bool($block)) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido para el filtro finalizado, verifica la documentación", 10182);
        $tmpStr = $block ? "TRUE" : "FALSE";
        return "finalizado = $tmpStr";
    }

    private function _block_query($block, String $str_attr, array $allowed_vals) : String {
        if (!is_array($block)) {
            if (!is_numeric($block) || !in_array($block, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro $str_attr, verifica la documentación", 10179);
            $strResult = "$str_attr = $block";
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!is_numeric($item) || !in_array($item, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro $str_attr, verifica la documentación", 10184);
                $tmpArray[] = "$str_attr = $item";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }
}