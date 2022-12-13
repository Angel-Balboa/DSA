<?php

namespace dsa\api\model\materia_en_grupo;

use dsa\api\model\grupo\Grupo;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\api\model\materia\Materia;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoNoExisteException;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoException;
use dsa\api\model\materia_en_grupo\Exceptions\ProfesorEnGrupoException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;

class MateriaEnGrupo {

    private ?array $data;
    private COperacionesSQL $SqlOp;
    private bool $is_new;
    private bool $is_equivalent;
    private ?array $tmp_data;

    /**
     * @param int|null $id
     * @param bool $is_new
     * @param bool $is_equivalent
     * @param array|null $newData
     * @param COperacionesSQL|null $cop
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorEnGrupoException
     * @throws ProfesorNoExisteException
     */
    private function __construct(?int $id=null, bool $is_new=false, bool $is_equivalent=false, ?array $newData=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->data = null;
        $this->tmp_data = null;
        $this->is_new = $is_new;
        $this->is_equivalent = $is_equivalent;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_materiaEnGrupo_by_id($id)) {
                    throw new MateriaEnGrupoNoExisteException("La MEG con id: $id, no existe.", 11001);
                }
            }
        } else {
            if (!$is_equivalent) {
                if (!is_null($newData["id_profesor"])) {
                    if ($this->_check_profesor_en_grupo($newData["id_profesor"], $newData["id_grupo"])) {
                        throw new ProfesorEnGrupoException("No es posible asignar a un profesor dos veces en el mismo grupo", 11002);
                    }
                }

                if ($this->_check_materia_en_grupo($newData["id_materia"], $newData["id_grupo"])) {
                    throw new MateriaEnGrupoException("La materia ya existen en el grupo, no puede haber la misma materia dos veces en el mismo grupo", 11003);
                }
            }

            $this->tmp_data = $newData;
        }
    }

    /**
     * @param int $id
     * @param COperacionesSQL|null $cop
     * @return MateriaEnGrupo
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorEnGrupoException
     * @throws ProfesorNoExisteException
     */
    public static function get_MEG_by_id(int $id, ?COperacionesSQL &$cop=null) : MateriaEnGrupo {
        return new MateriaEnGrupo($id, false, false, null);
    }

    /**
     * @param Grupo $grupo
     * @param Materia $materia
     * @param Profesor|null $profesor
     * @param int|null $modificador_horas
     * @return MateriaEnGrupo|null
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     */
    public static function crear_nueva_asignacion_materiaEnGrupo(Grupo $grupo, Materia $materia, ?Profesor $profesor, ?int $alumnos_estimados=30, ?int $modificador_horas=0, ?COperacionesSQL &$cop=null) : ?MateriaEnGrupo {
        $tmp_data = array("id_grupo" => $grupo->get_data("id"), "id_materia" => $materia->get_data("id"), "id_profesor" => is_null($profesor) ? null : $profesor->get_data("id"), "modificador_horas" => $modificador_horas, "alumnos_estimados" => $alumnos_estimados, "equivalente" => null);
        $tmpMateriaEnGrupo = new MateriaEnGrupo(null, true, false, $tmp_data, $cop);
        if ($tmpMateriaEnGrupo->_save()) return MateriaEnGrupo::get_MEG_by_id($tmpMateriaEnGrupo->get_data("id"), $cop);

        return null;
    }


    /**
     * @param Grupo $grupo
     * @param MateriaEnGrupo $meg
     * @param int $alumnos_estimados
     * @param COperacionesSQL|null $cop
     * @return MateriaEnGrupo|null
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorEnGrupoException
     * @throws ProfesorNoExisteException
     */
    public static function crear_nueva_asignacion_de_equivalencia(Grupo $grupo, MateriaEnGrupo $meg, int $alumnos_estimados=30, ?COperacionesSQL &$cop=null) : ?MateriaEnGrupo {
        $tmp_data = array("equivalente" => $meg->get_data("id"), "id_grupo" => $grupo->get_data("id"), "alumnos_estimados" => $alumnos_estimados);
        $tmpMateriaEnGrupo = new MateriaEnGrupo(null, true, true, $tmp_data, $cop);
        if ($tmpMateriaEnGrupo->_save()) return MateriaEnGrupo::get_MEG_by_id($tmpMateriaEnGrupo->get_data("id"), $cop);

        return null;
    }

    /**
     * @param array|null $filtro
     * @return array
     * @throws LlaveDeBusquedaIncorrectaException
     * @throws MateriaEnGrupoException
     */
    public static function get_all(?array $filtro=null) : array {
        $tmpMEG = new MateriaEnGrupo(null, false, false, null);
        return $tmpMEG->_get_all_MEGs($tmpMEG->_create_sqlquery($filtro));
    }

    private function _get_all_MEGs(String $sqlquery) : array {
        $ids = array();
        $res = $this->SqlOp->exec($sqlquery);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    public static function get_all_by_profesor(int $p,?array $filtro=null) : array {
        $tmpMEG = new MateriaEnGrupo(null, false, false, null);
        return $tmpMEG->_get_all_MEGs_profe($tmpMEG->_create_sqlquery_prof($p));
    }

    private function _get_all_MEGs_profe(String $sqlquery) : array {
        $ids = array();
        $res = $this->SqlOp->exec($sqlquery);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }
    private function _create_sqlquery_prof(int $p) : String
    {
        $sqlquery = "SELECT materia_en_grupo.id FROM materia_en_grupo where materia_en_grupo.profesor=$p";
        return $sqlquery;
    }


    /**
     * @param array|null $filtro
     * @return String
     * @throws LlaveDeBusquedaIncorrectaException
     * @throws MateriaEnGrupoException
     */
    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT materia_en_grupo.id FROM materia_en_grupo";
        $allowed_keys = ["materia", "profesor", "grupo", "equivalente"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) {
                throw new MateriaEnGrupoException("El formato de los filtros no es válido, verifica la documentación", 11130);
            } else {
                $sqlquery .= " WHERE ";
                $tmpArray = array();
                foreach (array_keys($filtro) as $key) {
                    if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 11136);
                    switch ($key) {
                        case "equivalente":
                            $tmpArray[] = $this->_block_query_equivalente($filtro["equivalente"]);
                            break;
                        case "materia":
                        case "profesor":
                        case "grupo":
                            $tmpArray[] = $this->_block_query($filtro[$key], $key, ucfirst($key));
                            break;
                        default:
                            echo "$key aún no implementada";
                            break;
                    }
                }
                $sqlquery .= implode(" AND ", $tmpArray);
            }
        }

        return $sqlquery;
    }

    private function _block_query($block, String $str_attr, String $expected_class) : String {
        $strResult = "";
        if (is_numeric($block)) {
            $strResult = "$str_attr = $block";
        } elseif (DataChecker::check_instance_of($block, $expected_class)) {
            $strResult = "$str_attr = " . $block->get_data("id");
        } else {
            throw new LlaveDeBusquedaIncorrectaException("La llave \"" . strval($block) . "\" no el válida, verifica la documentación", 11166);
        }

        return $strResult;
    }

    private function _block_query_equivalente($block) : String {
        $strResult = "";
        if (!is_bool($block)) throw new LlaveDeBusquedaIncorrectaException("El valor " . strval($block) . " no es correcto, verificalo", 11155);

        $str_val = ($block) ? "IS NOT" : "IS";
        return "equivalente $str_val NULL";
    }


    /**
     * @param array $new_data
     * @return bool
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorEnGrupoException
     * @throws ProfesorNoExisteException
     */
    public function actualizar_datos(array $new_data) : bool {
        $tmpData = array();
        $ban = false;
        if (isset($new_data["equivalente"])) {
            if (DataChecker::check_instance_of($new_data["equivalente"], "MateriaEnGrupo")) {
                $tmpData["id_materiaEnGrupo_Equivalente"] = $new_data["equivalente"]->get_data("id");
            } else {
                $tmpData["id_materiaEnGrupo_Equivalente"] = null;
            }
        } else {
            $tmpData["id_materiaEnGrupo_Equivalente"] = $this->data["id_materiaEnGrupo_Equivalente"];
        }

        if (isset($new_data["materia"]) && DataChecker::check_instance_of($new_data["materia"], "Materia")) {
            $tmpData["id_materia"] = $new_data["materia"]->get_data("id");
        } else {
            $tmpData["id_materia"] = $this->data["id_materia"];
        }

        if (isset($new_data["profesor"]) && DataChecker::check_instance_of($new_data["profesor"], "Profesor")) {
            $tmpData["id_profesor"] = $new_data["profesor"]->get_data("id");
        } else {
            $tmpData["id_profesor"] = $this->data["id_profesor"];
        }

        if (isset($new_data["grupo"]) && DataChecker::check_instance_of($new_data["grupo"], "Grupo")) {
            $tmpData["id_grupo"] = $new_data["grupo"]->get_data("id");
        } else {
            $tmpData["id_grupo"] = $this->data["id_grupo"];
        }

        $tmpData["modificador_horas"] = $new_data["modificador_horas"] ?? $this->data["modificador_horas"];
        $tmpData["alumnos_estimados"] = $new_data["alumnos_estimados"] ?? $this->data["alumnos_estimados"];

        if ($tmpData["id_profesor"] != $this->data["id_profesor"]) {
            if ($this->_check_profesor_en_grupo($tmpData["id_profesor"], $tmpData["id_grupo"])) {
                throw new ProfesorEnGrupoException("No es posible asignar a un profesor dos veces en el mismo grupo", 11002);
            }
        }

        if ($tmpData["id_materia"] != $this->data["id_materia"]) {
            if ($this->_check_materia_en_grupo($tmpData["id_materia"], $tmpData["id_grupo"])) {
                throw new MateriaEnGrupoException("La materia ya existen en el grupo, no puede haber la misma materia dos veces en el mismo grupo", 11003);
            }
        }

        $this->tmp_data = $tmpData;

        if ($this->_save()) {
            $this->_get_materiaEnGrupo_by_id($this->data["id"]);
            $ban = true;
        } else {
            throw new MateriaEnGrupoException("No ha sido posible actualizar los datos de la asignación.", 11008);
        }

        return $ban;
    }

    public function eliminar_MEG(int $new_data) : bool
    {
        $ban = false;

        if ($this->_eliminando_MEG($new_data))
        {
            $ban=true;
        }
        else{
            throw new MateriaEnGrupoException("No se ha podido eliminar la Materia del Grupo", 12090);

        }
        return $ban;
    }


    /**
     * @param array $new_data
     * @return bool
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorNoExisteException
     */
    public function actualizar_datos_P(array $new_data) : bool {
        $tmpData = array();
        $ban = false;
        $tmpData["id_profesor"]=$new_data["id_profesor"];
        $tmpData["id_materia"]=$new_data["id_meg"];
        $this->tmp_data = $tmpData;
        if ($this->_save()) {
            $this->_get_materiaEnGrupo_by_id($new_data["id_meg"]);
            $ban = true;
        } else {
            throw new MateriaEnGrupoException("No ha sido posible actualizar los datos de la asignación.", 11008);
        }

        return $ban;
    }

    public function convertir_en_equivalente_de(MateriaEnGrupo $materiaEnGrupo) : bool {
        $ban = false;

        if ($this->_convertir_en_equivalente_de($materiaEnGrupo->get_data()["id"])) {
            $this->_get_materiaEnGrupo_by_id($this->data["id"]);
            $ban = true;
        }

        return $ban;
    }

    public function __toString() : String {
        return "MEG id: " . $this->data["id"];
    }

    public function get_data($filtro=null) {

        $allowed_keys = ["id", "materia", "id_materia", "modificador_horas", "alumnos_estimados", "profesor", "id_profesor", "grupo", "id_grupo", "es_equivalente", "id_materia_equivalente", "materia_equivalente"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new MateriaEnGrupoException("La llave $filtro no es válida, revisa la documentación", 11225);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new MateriaEnGrupoException("La llave $key no es válida, revisa la documentación", 11233);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }

        } else {
            $dataReturn = $this->data;
            $dataReturn["es_equivalente"] = !is_null($this->data["id_materia_equivalente"]);
        }

        return $dataReturn;
    }

    /**
     * Método que realiza el guardado de los datos en una creación de un registro o una actualización
     * @return bool
     * @throws MateriaEnGrupoException
     */
    private function _save() : bool {
        if ($this->is_new) {
            if (!$this->is_equivalent) {
                if (!$this->_crear_nueva_asignacion_materiaEnGrupo()) {
                    throw new MateriaEnGrupoException("No ha sido posible guardar la asignación, intenta más tarde", 11004);
                } else {
                    $ban = true;
                }
            } else {
                if (!$this->_crear_nueva_asignacion_de_equivalencia()) {
                    throw new MateriaEnGrupoException("No ha sido posible guardar la asignación de equivalencia, intenta más tarde", 11005);
                } else {
                    $ban = true;
                }
            }
        } else {
            if (!$this->_actualiza_materiaEnGrupo_db()) {
                throw new MateriaEnGrupoException("No ha sido posible actualizar el registro de la asignación", 11006);
            }  else {
                $ban = true;
            }
        }
        return $ban;
    }

    /**
     * Método que obtiene la de la base de datos la información de la asignación a travéz del identificador único
     * @param int $id
     * @return bool
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorNoExisteException
     */
    private function _get_materiaEnGrupo_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT materia_en_grupo.materia as id_materia, materia_en_grupo.modificador_horas as modificador_horas, materia_en_grupo.alumnos_estimados as alumnos_estimados, materia_en_grupo.profesor as id_profesor, materia_en_grupo.grupo as id_grupo, materia_en_grupo.equivalente AS id_materiaEnGrupo_equivalente FROM materia_en_grupo WHERE materia_en_grupo.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($id, $res[0]["id_materia"], $res[0]["modificador_horas"], $res[0]["alumnos_estimados"], $res[0]["id_profesor"], $res[0]["id_grupo"], $res[0]["id_materiaEnGrupo_equivalente"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que asigna y crea las instancias de los atributos privados de la clase.
     * @param int $id
     * @param int|null $id_materia
     * @param int|null $modificador_horas
     * @param int|null $id_profesor
     * @param int|null $id_grupo
     * @param int|null $id_materiaEnGrupo_equivalente
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorNoExisteException
     */
    private function _asigna_campos_privados(int $id, ?int $id_materia, ?int $modificador_horas, ?int $alumnos_estimados, ?int $id_profesor, ?int $id_grupo, ?int $id_materiaEnGrupo_equivalente) {
        $this->data["id"] = $id;
        $this->data["alumnos_estimados"] = $alumnos_estimados;
        $this->data["id_materia_equivalente"] = $id_materiaEnGrupo_equivalente;
        $this->data["id_materia"] = $id_materia;
        $this->data["modificador_horas"] = $modificador_horas;
        $this->data["id_profesor"] = $id_profesor;
        $this->data["id_grupo"] = $id_grupo;
    }

    /**
     * Método que verifica la existencia de un profesor asignado al grupo previamente.
     * @param int $id_profesor identificador del profesor
     * @param int $id_grupo identificador del grupo
     * @return bool
     */
    private function _check_profesor_en_grupo(int $id_profesor, int $id_grupo) : bool {
        $sqlquery = "SELECT COUNT(*) as existe FROM materia_en_grupo WHERE profesor = ? AND grupo = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_profesor, $id_grupo])[0]["existe"] == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que verifica la existencia de una materia asignada al grupo previamente
     * @param $id_materia
     * @param $id_grupo
     * @return bool
     */
    private function _check_materia_en_grupo($id_materia, $id_grupo) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) as existe FROM materia_en_grupo WHERE materia = ? AND grupo = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_materia, $id_grupo])[0]["existe"] == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que guardar un nuevo registro en la base de datos de una asignación de materia/profesor/grupo
     * @return bool
     */
    private function _crear_nueva_asignacion_materiaEnGrupo() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO materia_en_grupo (materia, modificador_horas, alumnos_estimados, profesor, grupo) VALUES (?, ?, ?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iiiii", [$this->tmp_data["id_materia"], $this->tmp_data["modificador_horas"], $this->tmp_data["alumnos_estimados"], $this->tmp_data["id_profesor"], $this->tmp_data["id_grupo"]]) == 1;

            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _crear_nueva_asignacion_de_equivalencia() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO materia_en_grupo (alumnos_estimados, grupo, equivalente) VALUES (?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iii", [$this->tmp_data["alumnos_estimados"], $this->tmp_data["id_grupo"], $this->tmp_data["equivalente"]]) == 1;

            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _convertir_en_equivalente_de(int $id_materia_equivalente) : bool {
        $sqlquery = "UPDATE materia_en_grupo SET materia = null, modificador_horas = null, profesor = null, equivalente = ? WHERE materia_en_grupo.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_materia_equivalente, $this->data["id"]]);
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }


    private function _actualiza_materiaEnGrupo_db() : bool {
        $ban = false;
        $sqlquery = "UPDATE materia_en_grupo SET materia = ?, modificador_horas = ?, alumnos_estimados = ?, profesor = ?, grupo = ? WHERE materia_en_grupo.id = ?";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "iiiiii", [$this->tmp_data["id_materia"], $this->tmp_data["modificador_horas"], $this->tmp_data["alumnos_estimados"], $this->tmp_data["id_profesor"], $this->tmp_data["id_grupo"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _eliminando_MEG(int $id) {
        $ban = false;
        $sqlquery = "DELETE FROM materia_en_grupo where id=?";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "i", [$id]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _data_return($key) {

        //var_dump($key);

        if ($key == "id_materia" || $key == "materia") {
            $dataReturn = $this->data["id_materia"];
        } elseif ($key == "id_profesor" || $key == "profesor") {
            $dataReturn = $this->data["id_profesor"];
        } elseif ($key == "grupo" || $key == "id_grupo") {
            $dataReturn = $this->data["id_grupo"];
        } elseif ($key == "id_materia_equivalente" || $key == "materia_equivalente") {
            $dataReturn = $this->data["id_materia_equivalente"];
        } elseif($key == "es_equivalente") {
            $dataReturn = !is_null($this->data["id_materia_equivalente"]);
        } else {
            $dataReturn = $this->data[$key];
        }

        return $dataReturn;
    }


}