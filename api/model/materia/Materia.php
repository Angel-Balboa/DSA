<?php

namespace dsa\api\model\materia;

use dsa\api\model\materia\Exceptions\MateriaException;
use dsa\api\model\materia\Exceptions\MateriaYaExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\lib\Utils\DataChecker;

class Materia
{
    const TIPO_BASICA = "Básica";
    const TIPO_ESPECIALIDAD = "Especialidad";
    const TIPO_VALORES = "Valores";
    const TIPO_INGLES = "Inglés";

    private ?array $data;

    private COperacionesSQL $SqlOp;
    private bool $is_new;
    private ?array $data_tmp;

    /**
     * Constructor privado de la clase
     * @param int|null $id
     * @param String|null $clave_materia
     * @param int|null $id_plan
     * @param bool $is_new
     * @param array|null $data
     * @param COperacionesSQL|null $cop
     * @throws MateriaNoExistenteException
     */
    private function __construct(?int $id=null, ?String $clave_materia=null, ?int $id_plan=null, bool $is_new=false, ?array $data=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->is_new = $is_new;
        $this->data_tmp = null;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_materia_by_id($id)) {
                    throw new MateriaNoExistenteException("La materia con id: $id, no existe.", 5001);
                }
            }

            if (!is_null($clave_materia) && !is_null($id_plan)) {
                if (!$this->_get_materia_by_clave($id_plan, $clave_materia)) {
                    throw new MateriaNoExistenteException("La materia con clave: $clave_materia, no existe", 5003);
                }
            }
        }
        else {
            $this->data_tmp = $data;
        }
        $this->data["plan"] = $id_plan;
    }

    //******************************************************************************************************************
    //************************* Pseudo constructores de la clase *******************************************************
    //******************************************************************************************************************

    /**
     * Factoria que obtiene una instancia de la clase Materia donde se utiliza el identificador único de la materia como campo de búsqueda
     * @param int $id identificador único de la materia
     * @return Materia
     * @throws MateriaNoExistenteException
     */
    public static function get_materia_by_id(int $id, ?COperacionesSQL &$cop=null) : Materia {
        return new Materia($id, null, null, false, null, $cop);
    }

    /**
     * Factoria que obtiene una instancia de la clase Materia donde se utiliza la clave de la materia como campo de búsqueda
     * @param String $clave_materia clave única de la materia
     * @return Materia
     * @throws MateriaNoExistenteException
     */
    public static function get_materia_by_clave(PlanDeEstudio $plan, String $clave_materia, ?COperacionesSQL &$cop=null) : Materia {
        return new Materia(null, $clave_materia, $plan->get_data("id"), false, null, $cop);
    }

    /**
     * Método que crea una nueva Materia y la guarda en la base de datos
     * @param PlanDeEstudio $planDeEstudio Instancia de la clase PlanDeEstudio al que será osociada la materia
     * @param String $clave clave única de la materia
     * @param String $nombre nombre completo de la materia
     * @param int $creditos creditos totales de la materia
     * @param int $cuatrimestre cuatrimestre de la materia
     * @param int $posicion_H posición horizontal en el mapa curricular del plan de estudio.
     * @param int $horas_totales horas totales al cuatrimestre de la materia
     * @param String $tipo tipo de materia.
     * @param COperacionesSQL|null $cop
     * @return Materia|null
     * @throws MateriaException
     * @throws MateriaNoExistenteException
     * @throws MateriaYaExistenteException
     * @throws PlanDeEstudioException
     */
    public static function crea_nueva_materia(PlanDeEstudio $planDeEstudio, String $clave, String $nombre, int $creditos, int $cuatrimestre, int $posicion_H, int $horas_totales, String $tipo=Materia::TIPO_ESPECIALIDAD, ?COperacionesSQL &$cop=null) : ?Materia {
        try {
            $tmp_materia = Materia::get_materia_by_clave($planDeEstudio, strtoupper($clave));

            if (!is_null($tmp_materia)) {
                $tmp_materia = null;
                throw new MateriaYaExistenteException("La materia con clave: $clave, ya existe. Verifica los datos", 5013);
            }
        }
        catch (MateriaNoExistenteException $e) {
            $tmp_data = array("clave" => strtoupper($clave), "nombre" => $nombre, "creditos" => $creditos, "cuatrimestre" => $cuatrimestre, "posicion_h" => $posicion_H, "horas_totales" => $horas_totales, "tipo" => $tipo, "id_plan" => $planDeEstudio->get_data("id"));

            $tmp_materia = new Materia(null, null, null, true, $tmp_data, $cop);

            if (!$tmp_materia->_save()) {
                $tmp_materia = null;
                throw new MateriaException("No se ha podido crear la nueva materia, intente más tarde", 5009);
            }
            else {
                return Materia::get_materia_by_clave($planDeEstudio, strtoupper($clave), $cop);
            }
        }
        return null;
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpMateria = new Materia(null, null, null,false, null);
        return $tmpMateria->_get_all_materias($tmpMateria->_create_sqlquery($filtro));
    }

    private function _get_all_materias(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT materia.id FROM materia";
        $allowed_keys = ["cuatrimestre", "tipo", "plan"];
        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro debe ser un diccionario con llaves permitidas, verifica la documentación", 5142);

            $sqlquery .= " WHERE ";
            $tmpArray = array();
            foreach(array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 5146);

                switch ($key) {
                    case "cuatrimestre":
                        $tmpArray[] = $this->_block_query_cuatrimestre($filtro["cuatrimestre"]);
                        break;
                    case "plan":
                        $tmpArray[] = $this->_block_query_plan($filtro["plan"]);
                        break;
                    case "tipo":
                        $tmpArray[] = $this->_block_query_tipo($filtro["tipo"]);
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

    private function _block_query_tipo($block) : String {
        $allowed_vals = ["Básica", "Especialidad", "Valores", "Inglés"];
        if (!is_array($block)) {
            if (!in_array($block, $allowed_vals)) {
                throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro por tipo de materia, verifica la documentación", 5173);
            } else {
                $strResult = "tipo = '$block'";
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!in_array($item, $allowed_vals)) {
                    throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro por tipo de materia, verifica la documentación", 5181);
                } else {
                    $tmpArray[] = "tipo = '$item'";
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    private function _block_query_plan($block) : String {
        if (!is_array($block)) {
            if (is_numeric($block)) {
                $strResult = "plan = $block";
            } elseif (DataChecker::check_instance_of($block, "PlanDeEstudio")) {
                $strResult = "plan = " . $block->get_data("id");
            } else {
                throw new LlaveDeBusquedaIncorrectaException("El valor $block no es válido en el filtro de materias, verifica la documentación.", 5172);
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (is_numeric($item)) {
                    $tmpArray[] = "plan = $item";
                } elseif (DataChecker::check_instance_of($item, "PlanDeEstudio")) {
                    $tmpArray[] = "plan = " . $item->get_data("id");
                } else {
                    throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido en el filtro de materias, verifica la documentación.", 5182);
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    private function _block_query_cuatrimestre($block) : String {
        if (!is_array($block)) {
            if (!is_numeric($block)) throw new LlaveDeBusquedaIncorrectaException("El valor de $block no es válido en la búsqueda por cuatrimestres, verifica los datos", 5163);
            if ($block < 0 || $block > 10) throw new LlaveDeBusquedaIncorrectaException("El valor de $block debe ser un entero positivo en el rango [0, 10].", 5164);

            $strResult = "cuatrimestre = $block";
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!is_numeric($item)) throw new LlaveDeBusquedaIncorrectaException("El valor de $item no es válido en la búsqueda por cuatrimestres, verifica los datos", 5163);
                if ($item < 0 || $item > 10) throw new LlaveDeBusquedaIncorrectaException("El valor de $item debe ser un entero positivo en el rango [0, 10].", 5164);

                $tmpArray[] = "cuatrimestre = $item";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    //******************************************************************************************************************
    //************************* invterfaz pública de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * @param $filtro
     * @return array|mixed
     * @throws MateriaException
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["id", "clave", "nombre", "creditos", "cuatrimestre", "posicion_h", "horas_totales", "tipo", "id_plan", "plan_estudios"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new MateriaException("La llave $filtro no es válida, verifica la documentación", 5154);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new MateriaException("la lave $key no es válida, verifica la documentación", 5163);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }

        } else {
            $dataReturn = $this->data;
            $dataReturn["plan"] = $this->data["id_plan"];
        }

        return $dataReturn;
    }

    /**
     * Método mágico que retorna la representación en String de la instancia.
     * @return String
     */
    public function __toString() : String {
        return $this->data["clave"] ?? "";
    }

    /**
     * Métododo que retorna una instancia de la clase PlanDeEstudio, el cual esta asociada la materia
     * @return PlanDeEstudio
     * @throws PlanDeEstudioNoExistenteException
     */
    public function get_plan_de_estudios() : PlanDeEstudio {
            return PlanDeEstudio::get_planDeEstudio_by_id($this->data["id_plan"]);
    }

    /**
     * Método que obiene los datos a actualizar a la materia
     * @param array $data diccionario que contiene lo nuevos datos de la materia. Claves válidas: clave, nombre, creditos, cuatrimestre, posición_h, horas_totales, tipo
     * @return bool
     * @throws MateriaException
     */
    public function actualiza_datos_de_materia(array $data) : bool {
        $allowed_keys = ["clave", "nombre", "creditos", "cuatrimestre", "posicion_h", "horas_totales", "tipo"];
        $tmp_data = array();

        foreach (array_keys($data) as $key) {
            if (!in_array($key, $allowed_keys)) {
                throw new MateriaException("La llave $key no es válida, verifica la documentación", 5198);
            }
        }

        $tmp_data["clave"] = $data["clave"] ?? $this->data["clave"];
        if ($tmp_data["clave"] != $this->data["clave"]) {
            if ($this->_check_claveMateria_existe($tmp_data["clave"])) {
                throw new MateriaException("La clave " . $tmp_data["clave"] . " ya existe", 5205);
            }
        }

        $tmp_data["nombre"] = $data["nombre"] ?? $this->data["nombre"];
        $tmp_data["creditos"] = $data["creditos"] ?? $this->data["creditos"];
        $tmp_data["cuatrimestre"] = $data["cuatrimestre"] ?? $this->data["cuatrimestre"];
        if ($tmp_data["cuatrimestre"] < 0 || $tmp_data["cuatrimestre"] > 10) {
            throw new MateriaException("El valor del cuatrimestre debe ser en el rago [0, 10]", 5195);
        }
        $tmp_data["posicion_h"] = $data["posicion_h"] ?? $this->data["posicion_h"];
        $tmp_data["horas_totales"] = $data["horas_totales"] ?? $this->data["horas_totales"];

        $tipos_permitidos = array("Básica", "Especialidad", "Valores", "Inglés");
        $tmp_data["tipo"] = $data["tipo"] ?? $this->data["tipo"];
        if (!in_array($tmp_data["tipo"], $tipos_permitidos)) {
            throw new MateriaException("El tipo de materia no esta permitido, verifica la documentación.", 5203);
        }

        $this->data_tmp = $tmp_data;

        return $this->_save();
    }

    //******************************************************************************************************************
    //************************* invterfaz privada de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * Método privado que obtiene los datos de la materia desde la base de datos utilizando el identificador único de la materia como campo de búsqueda
     * @param int|null $id
     * @return bool
     */
    private function _get_materia_by_id(?int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT materia.clave as clave, materia.nombre as nombre, materia.creditos as creditos, materia.cuatrimestre as cuatrimestre, materia.posicion_h as posicion_h, materia.horas_totales as horas_totales, materia.tipo as tipo, materia.plan as id_plan from materia WHERE materia.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($id,
                    $res[0]["clave"],
                    $res[0]["nombre"],
                    $res[0]["creditos"],
                    $res[0]["cuatrimestre"],
                    $res[0]["posicion_h"],
                    $res[0]["horas_totales"],
                    $res[0]["tipo"],
                    $res[0]["id_plan"]
                );
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método privado que obtiene los datos de la materia desde la base de datos que utiliza la clave de la materia como campo de búsqueda
     * @param string $clave_materia
     * @return bool
     */
    private function _get_materia_by_clave(int $id_plan, string $clave_materia) : bool {
        $ban = false;
        $sqlquery = "SELECT materia.id as id, materia.nombre as nombre, materia.creditos as creditos, materia.cuatrimestre as cuatrimestre, materia.posicion_h as posicion_h, materia.horas_totales as horas_totales, materia.tipo as tipo from materia WHERE materia.clave = ? and materia.plan = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "si", [$clave_materia, $id_plan]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($res[0]["id"],
                    $clave_materia,
                    $res[0]["nombre"],
                    $res[0]["creditos"],
                    $res[0]["cuatrimestre"],
                    $res[0]["posicion_h"],
                    $res[0]["horas_totales"],
                    $res[0]["tipo"],
                    $id_plan
                );
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método que asigna valores a los campos privados de la clase
     * @param int|null $id
     * @param String $clave_materia
     * @param String $nombre
     * @param int $creditos
     * @param int $cuatrimestre
     * @param int $posicion_h
     * @param int $horas_totales
     * @param String $tipo
     * @param int $id_plan
     */
    private function _asigna_campos_privados(?int $id, String $clave_materia, String $nombre, int $creditos, int $cuatrimestre, int $posicion_h, int $horas_totales, String $tipo, int $id_plan) {
        $this->data["id"] = $id;
        $this->data["clave"] = $clave_materia;
        $this->data["nombre"] = $nombre;
        $this->data["creditos"] = $creditos;
        $this->data["cuatrimestre"] = $cuatrimestre;
        $this->data["posicion_h"] = $posicion_h;
        $this->data["horas_totales"] = $horas_totales;
        $this->data["tipo"] = $tipo;
        $this->data["id_plan"] = $id_plan;
    }

    /**
     * Método que realiza el guardado en la bse de datos en creación y actualización
     * @return bool
     * @throws MateriaException
     */
    private function _save() : bool {
        $ban = false;

        if (!$this->is_new) {
            if (!$this->_actualiza_datos_de_materia_en_db()) {
                throw new MateriaException("No se han podido actualizar los datos de la materia, verifica.", 5003);
            }
            else {
                $ban = true;
            }
        }
        else {
            if (!$this->_crea_nueva_materia_en_db()) {
                throw new MateriaException("No se ha podido crear la nueva materia, intente de nuevo", 5010);
            }
            else {
                $ban = true;
            }
        }

        return $ban;
    }

    /**
     * Método privado que realiza la actualización de los campos en la base de datos de un registro de materia
     * @return bool
     */
    private function _actualiza_datos_de_materia_en_db() : bool {
        $ban = false;
        $sqlquery = "UPDATE materia SET clave = ?, nombre = ?, creditos = ?, cuatrimestre = ?, posicion_h = ?, horas_totales = ?, tipo = ? WHERE materia.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssiiiisi", [$this->data_tmp["clave"], $this->data_tmp["nombre"], $this->data_tmp["creditos"], $this->data_tmp["cuatrimestre"], $this->data_tmp["posicion_h"], $this->data_tmp["horas_totales"], $this->data_tmp["tipo"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que guarda un registro en la base de datos de una materia nueva.
     * @return bool
     */
    private function _crea_nueva_materia_en_db() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO materia (clave, nombre, creditos, cuatrimestre, posicion_h, horas_totales, tipo, plan) VALUES (?, ?, ?, ?, ?, ? ,?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssiiiisi", [$this->data_tmp["clave"], $this->data_tmp["nombre"], $this->data_tmp["creditos"], $this->data_tmp["cuatrimestre"], $this->data_tmp["posicion_h"], $this->data_tmp["horas_totales"], $this->data_tmp["tipo"], $this->data_tmp["id_plan"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _check_claveMateria_existe(String $clave_materia) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) as existe FROM materia WHERE materia.clave = ? and materia.plan = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "si", [$clave_materia, $this->data["id_plan"]])[0]["existe"] > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            // todo: aquí el regresar falso esta mal, hay un bug aquí
            $ban = false;
        }
        return $ban;
    }

    private function _data_return($key) {
        if ($key == "id_plan" || $key == "plan_estudios" || $key == "plan") {
            $dataReturn = $this->data["id_plan"];
        } else {
            $dataReturn = $this->data[$key];
        }
        return $dataReturn;
    }


}