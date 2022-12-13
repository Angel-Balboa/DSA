<?php

namespace dsa\api\model\plan_de_estudio;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\api\model\materia\Materia;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudiosYaExisteException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Exceptions\MetodoNoImplementadoException;
use dsa\lib\Utils\DataChecker;
use dsa\lib\Utils\DateUtils;

class PlanDeEstudio
{
    const INGENIERIA = "Ing";
    const MAESTRIA_EN_INGENIERIA = "M.I.";
    const LICENCIATURA = "Lic";
    const PROFESIONAL_ASOCIADO = "P.A.";
    const ESPECIALIDAD = "Esp";

    private ?array $data;

    private COperacionesSQL $SqlOp;
    private bool $is_new;
    private ?array $data_tmp;

    /**
     * @param int|null $id
     * @param String|null $clave
     * @param bool $is_new
     * @param array|null $data
     * @param COperacionesSQL|null $cop
     * @throws PlanDeEstudioNoExistenteException
     */
    private function __construct(int $id=null, String $clave=null, bool $is_new=false, array $data=null, ?COperacionesSQL &$cop=null) {

        $this->SqlOp = COperacionesSQL::getInstance($cop);

        $this->is_new = $is_new;
        $this->data_tmp = null;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_planDeEstudio_by_id($id)) {
                    throw new PlanDeEstudioNoExistenteException("El plan de estudios con id: $id no existe, verifica.", 4001);
                }
            }

            if (!is_null($clave)) {
                if (!$this->_get_planDeEstudio_by_clave($clave)) {
                    throw new PlanDeEstudioNoExistenteException("El plan de estudio $clave no existe, verifica", 4003);
                }
            }
        }
        else {
            $this->data_tmp = $data;
        }
    }

    //******************************************************************************************************************
    //************************* Pseudo constructores de la clase *******************************************************
    //******************************************************************************************************************

    /**
     * Factoría que obtiene una instancia de la clase PlanDeEstudio con los datos de un Plan de Estudios utilizando el Id como campo de búsqueda
     * @throws PlanDeEstudioNoExistenteException
     */
    public static function get_planDeEstudio_by_id(int $id, ?COperacionesSQL &$cop=null) : PlanDeEstudio {
        return new PlanDeEstudio($id, null, false, null, $cop);
    }

    /**
     * Factoría que obtiene una instancia de la clase PlanDeEstudio con los datos de un Plan de Estudios utilizando el nombre corto como campo de búsqueda
     * @throws PlanDeEstudioNoExistenteException
     */
    public static function get_planDeEstudio_by_clave(String $clave, ?COperacionesSQL &$cop=null) : PlanDeEstudio {
        return new PlanDeEstudio(null, $clave, false, null, $cop);
    }

    /**
     * Factoría de la clase PlanDeEstudio que guarda un nuevo plan de estudio en la base de datos
     * @param Carrera $carrera
     * @param String $nombre
     * @param int $anio
     * @param String $clave
     * @param String $nivel
     * @param COperacionesSQL|null $cop
     * @return PlanDeEstudio
     * @throws PlanDeEstudioException
     * @throws PlanDeEstudioNoExistenteException
     * @throws PlanDeEstudiosYaExisteException
     */
    public static function crea_nuevo_plan_de_estudio(Carrera $carrera, String $nombre, int $anio, String $clave, String $nivel=PlanDeEstudio::INGENIERIA, ?COperacionesSQL &$cop=null): ?PlanDeEstudio
    {
        try {
            $tmp_plan = PlanDeEstudio::get_planDeEstudio_by_clave($clave);

            if (!is_null($tmp_plan)) {
                $tmp_plan = null;
                throw new PlanDeEstudiosYaExisteException("El plan de estudios ya existe con el nombre: $clave, verificalo", 4008);
            }
        } catch (PlanDeEstudioNoExistenteException $e) {

            $new_data = array("nombre" => $nombre, "clave" => $clave, "anio" => $anio, "nivel" => $nivel, "id_carrera" => $carrera->get_data("id"));
            $tmp_plan = new PlanDeEstudio(null, null, true, $new_data, $cop);
            if (!$tmp_plan->_save()) {
                throw new PlanDeEstudioException("No se ha podido guardar el nuevo plan de estudios, intenta mas tarde", 4009);
            }
            else {
                return PlanDeEstudio::get_planDeEstudio_by_clave($clave, $cop);
            }
        }

        return null;
    }

    /**
     * Factoría que obtiene una instancia de la clase PlanDeEstudio con los datos de un Plan de Estudios utilizando el Id como campo de búsqueda
     * @throws PlanDeEstudioNoExistenteException
     */
    public static function get_planDeEstudio_by_id2(int $id, ?COperacionesSQL &$cop=null) : PlanDeEstudio {
        $tmpPlan = new PlanDeEstudio(null, null, false, null);
        return $tmpPlan->_get_inner_query($id);    }

    public static function get_all(?array $filtro=null) : array {
        $tmpPlan = new PlanDeEstudio(null, null, false, null);
        return $tmpPlan->_get_all_planes($tmpPlan->_create_sqlquery($filtro));
    }


    private function _get_all_planes(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);

        foreach($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    /**
     * Método privado que obtiene los datos del plan de estudios desde la base de datos utilizando el Id como campo de búsqueda
     * @param int $id identificador único del Plan de Estudios
     * @return bool
     */
    private function _get_inner_query(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT usuario.nombre, usuario.apellidos, grupo.clave, grupo.turno, grupo.cuatrimestre, materia.nombre as materia, materia_en_grupo.modificador_horas as modif from usuario INNER JOIN plan_de_estudio ON (plan_de_estudio.carrera = $id) INNER JOIN carga_academica ON ( carga_academica.plan_estudios = plan_de_estudio.id ) INNER JOIN grupo ON( grupo.carga_academica = carga_academica.id ) INNER JOIN materia_en_grupo ON( materia_en_grupo.grupo = grupo.id ) INNER JOIN materia ON ( materia.id = materia_en_grupo.materia ) INNER JOIN profesor on(profesor.id=materia_en_grupo.profesor) WHERE usuario.id=profesor.usuario ORDER By grupo.cuatrimestre; ";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);
            print_r($res);
            if (count($res) == 1) {
                $this->_asignar_atributos_privados($id, $res[0]["nombre"], $res[0]["anio"], $res[0]["clave"], $res[0]["nivel"], $res[0]["id_carrera"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT plan_de_estudio.id FROM plan_de_estudio";
        $allowed_keys = ["anio", "nivel", "carrera"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) {
                throw new PlanDeEstudioException("El formato de los filtros no es válido, verifica la documentación", 4133);
            } else {
                $sqlquery .= " WHERE ";
                $tmpArray = array();
                foreach (array_keys($filtro) as $key) {
                    if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 4140);

                    switch ($key) {
                        case "carrera":
                            $tmpArray[] = $this->_block_query_carrera($filtro["carrera"]);
                            break;
                        case "anio":
                            $tmpArray[] = $this->_block_query_anio($filtro["anio"]);
                            break;
                        case "nivel":
                            $tmpArray[] = $this->_block_query_nivel($filtro["nivel"]);
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

    private function _block_query_nivel($block) : String {
        $allowed_vals = ["Ing", "M.I.", "Lic", "P.A.", "Esp"];
        if (!is_array($block)) {
            if (!in_array($block, $allowed_vals)) {
                throw new LlaveDeBusquedaIncorrectaException("El valor $block en la búsqueda por niveles del Plan de estudio no es válido, verifica los valores válidos.", 4165);
            } else {
                $strResult = "nivel = '$block'";
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!in_array($item, $allowed_vals)) {
                    throw new LlaveDeBusquedaIncorrectaException("El valor $item en la búsqueda por niveles del Plan de estudio no es válido, verifica los valores admitidos", 4174);
                } else {
                    $tmpArray[] = "nivel = '$item'";
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strResult;
    }

    private function _block_query_anio($block) : String {
        if (!is_array($block)) {
            if (!is_numeric($block)) {
                throw new LlaveDeBusquedaIncorrectaException("La llave \"" . $block . "\" no es válida, verifica la documentación", 4163);
            } else {
                $strResult = "anio = $block";
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!is_numeric($item)) {
                    throw new LlaveDeBusquedaIncorrectaException("La llave \"" . $block . "\" no es válida, verifica la documentación", 4171);
                } else {
                    $tmpArray[] = "anio = $item";
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) .")";
        }

        return $strResult;
    }

    private function _block_query_carrera($block) : String {
        if (!is_array($block)) {
            if (is_numeric($block)) {
                $strResult = "carrera = $block";
            } elseif (DataChecker::check_instance_of($block, "Carrera")) {
                $strResult = "carrera = " . $block->get_data("id");
            } else {
                throw new LlaveDeBusquedaIncorrectaException("La llave \"" . strval($block) . "\" no es válida, verifica la documentación", 4164);
            }
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (is_numeric($item)) {
                    $tmpArray[] = "carrera = $item";
                } elseif (DataChecker::check_instance_of($item, "Carrera")) {
                    $tmpArray[] = "carrera = " . $item->get_data("id");
                }
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }

        return $strResult;
    }


    /**
     * Método que obtiene las materias del plan de estudio.
     * @param int|null $cuatrimestre Parámetro opcional para filtrar a las materias del plan de estudio de acuerdo a un cuatrimestre en concreto.
     * @return array
     * @throws MateriaNoExistenteException
     */
    public function obten_materias(?int $cuatrimestre=null) : array {
        // todo: se deberá implementar utilizando la clase Materias o bien se elimina este metodo
        $ids_materias = $this->_obten_id_materias_de_bd($cuatrimestre);
        $array_materias = array();
        foreach ($ids_materias as $id_materia) {
            $array_materias[] = Materia::get_materia_by_id($id_materia);
        }

        return $array_materias;
    }

    //******************************************************************************************************************
    //************************* invterfaz pública de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * Método que actualiza los datos de un plan de estudios
     * @param array $data_to_update diccionario con los datos para actualizar el registro del plan de estudios. Llaves válidas: nombre, anio, clave, nivel
     * @return bool
     * @throws PlanDeEstudioException
     * @throws PlanDeEstudiosYaExisteException
     */
    public function actualiza_datos_de_planDeEstudio(array $data_to_update) : bool {
        $allowed_keys = ["nombre", "anio", "clave", "nivel"];
        $allowed_niveles = array("Ing", "M.I.", "Lic", "P.A.", "Esp");
        $tmp_data = array();

        foreach (array_keys($data_to_update) as $key) {
            if (!in_array($key, $allowed_keys)) {
                throw new PlanDeEstudioException("La llave $key no es válida. Verifica la documentación");
            }
        }

        $tmp_data["nombre"] = $data_to_update["nombre"] ?? $this->data["nombre"];
        $tmp_data["anio"] = $data_to_update["anio"] ?? $this->data["anio"];
        if ($tmp_data["anio"] != $this->data["anio"]) {
            if (!is_numeric($tmp_data["anio"]) || ($tmp_data["anio"] < 2010 || $tmp_data["anio"] > DateUtils::current_year())) {
                throw new PlanDeEstudioException("El valor:" . $tmp_data["anio"] . " del año, no es válido. Verifique la documentación", 4180);
            }
        }

        $tmp_data["clave"] = $data_to_update["clave"] ?? $this->data["clave"];
        if ($tmp_data["clave"] != $this->data["clave"]) {
            if ($this->_check_clave_existente($tmp_data["clave"])) throw new PlanDeEstudiosYaExisteException("La Clave: " . $tmp_data["clave"] . " ya esta regisrado, utilice otra clave", 4079);
        }

        $tmp_data["nivel"] = $data_to_update["nivel"] ?? $this->data["nivel"];
        if ($tmp_data["nivel"] != $this->data["nivel"]) {
            if (!in_array($tmp_data["nivel"], $allowed_niveles)) throw new PlanDeEstudioException("El valor del  nivel: " . $tmp_data["nivel"] . " no es un valor válido, verifique la documentación", 4086);
        }

        $this->data_tmp = $tmp_data;

        return $this->_save();
    }

    /**
     * @param null $filtro
     * @return array|mixed|null
     * @throws PlanDeEstudioException
     */
    public function get_data($filtro=null) {

        $allowed_keys = ["id", "nombre", "anio", "clave", "nivel", "id_carrera", "carrera"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new PlanDeEstudioException("La llave $filtro no es válida, verifica la documentación", 4200);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new PlanDeEstudioException("La llave $key no es válida, verifica la documentación.", 4208);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }
        } else {
            $dataReturn = $this->data;
        }

        return $dataReturn;
    }

    /**
     * Método que retorna una instancia de la clase Carrera Asociada al plan de estudios.
     * @return Carrera
     * @throws CarreraNoExistenteException
     */
    public function get_carrera() : Carrera {
        return Carrera::get_carrera_by_id($this->data["id_carrera"]);
    }

    /**
     * Método mágico que retorna el nombre corto como representación de la clase
     * @return String
     */
    public function __toString() : String {
        return $this->data["clave"];
    }

    //******************************************************************************************************************
    //************************* invterfaz privada de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * Método privado que obtiene los datos del plan de estudios desde la base de datos utilizando el Id como campo de búsqueda
     * @param int $id identificador único del Plan de Estudios
     * @return bool
     */
    private function _get_planDeEstudio_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT plan_de_estudio.nombre as nombre, plan_de_estudio.anio as anio, plan_de_estudio.clave as clave, plan_de_estudio.nivel as nivel, plan_de_estudio.carrera as id_carrera FROM plan_de_estudio WHERE id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_asignar_atributos_privados($id, $res[0]["nombre"], $res[0]["anio"], $res[0]["clave"], $res[0]["nivel"], $res[0]["id_carrera"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método privado que obtiene los datos del plan de estudios desde la base de datos utilizando el nombre corto como campo de búsqueda
     * @param string $clave nombre corto (aca. clave) del plan de estudios
     * @return bool
     */
    private function _get_planDeEstudio_by_clave(string $clave) : bool {
        $ban = false;
        $sqlquery = "SELECT plan_de_estudio.id as id, plan_de_estudio.nombre as nombre, plan_de_estudio.anio as anio, plan_de_estudio.nivel as nivel, plan_de_estudio.carrera as id_carrera FROM plan_de_estudio WHERE clave = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "s", [$clave]);

            if (count($res) == 1) {
                $this->_asignar_atributos_privados($res[0]["id"], $res[0]["nombre"], $res[0]["anio"], $clave, $res[0]["nivel"], $res[0]["id_carrera"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método privado que asigna valores a los atributos privados de la clase
     * @param int $id identificador único del plan de estudios
     * @param String $nombre nombre completo del plan de estudios
     * @param int $anio año de registro del plan de estudios
     * @param String $clave nombre corto (aca clave) del plan de estudios
     * @param String $nivel nivel educativo del plan de estudios
     * @param int $id_carrera identificador único de la carrera asociada al plan de estudios.
     */
    private function _asignar_atributos_privados(int $id, String $nombre, int $anio, String $clave, String $nivel, int $id_carrera) {
        $this->data["id"] = $id;
        $this->data["nombre"] = $nombre;
        $this->data["clave"] = $clave;
        $this->data["anio"] = $anio;
        $this->data["nivel"] = $nivel;
        $this->data["id_carrera"] = $id_carrera;
    }

    /**
     * Método privado que asigna valores a los atributos privados de la clase
     * @param int $id identificador único del plan de estudios
     * @param String $nombre nombre completo del plan de estudios
     * @param int $anio año de registro del plan de estudios
     * @param String $clave nombre corto (aca clave) del plan de estudios
     * @param String $nivel nivel educativo del plan de estudios
     * @param int $id_carrera identificador único de la carrera asociada al plan de estudios.
     */
    private function _asignar_inner(String $nombre, String $apellidos, String $clave,int $turno,int $cuatrimestre,String $materia,int $modif) {
        $this->data["id"] = $id;
        $this->data["nombre"] = $nombre;
        $this->data["clave"] = $clave;
        $this->data["anio"] = $anio;
        $this->data["nivel"] = $nivel;
        $this->data["id_carrera"] = $id_carrera;
    }





    /**
     * Método privado que realiza el proceso de guardado en la creación o la actualización de un plan de estudios en la base de datos
     * @return bool
     * @throws PlanDeEstudioException
     */
    private function _save() : bool {
        $ban = false;
        if (!$this->is_new) {
            if (!$this->_actualiza_datos_de_PlanDeEstudio()) {
                throw new PlanDeEstudioException("No ha sido posible actualizar los datos, verificalos", 4004);
            }
        }
        else {
            if (!$this->_crea_nuevo_planDeEstudios_en_bd()) {
                throw new PlanDeEstudioException("No ha sido posible insertar el nuevo plan de estudios en la base de datos", 4010);
            }
        }

        return true;
    }

    /**
     * Método privado que actualiza los datos del plan de estudios en la base de datos
     * @return bool
     */
    private function _actualiza_datos_de_PlanDeEstudio() : bool {
        $sqlquery = "UPDATE plan_de_estudio SET nombre = ?, anio = ?, clave = ?, nivel = ? WHERE plan_de_estudio.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "sissi", [$this->data_tmp["nombre"], $this->data_tmp["anio"], $this->data_tmp["clave"], $this->data_tmp["nivel"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que crea un nuevo registro en la base de datos de un nuevo plan de estudios
     * @return bool
     */
    private function _crea_nuevo_planDeEstudios_en_bd() : bool {
        $sqlquery = "INSERT INTO plan_de_estudio(nombre, anio, clave, nivel, carrera) VALUES (?, ?, ?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "sissi", [$this->data_tmp["nombre"], $this->data_tmp["anio"], $this->data_tmp["clave"], $this->data_tmp["nivel"], $this->data_tmp["id_carrera"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _obten_id_materias_de_bd(?int $cuatrimestre) : array {
        // todo: lo mas seguro es que este método que tenga que eliminar.
        $ids_materias = array();
        $sqlquery = "SELECT materia.id as id FROM materia WHERE plan = ?";
        $bind_params = 'i';

        if (!is_null($cuatrimestre)) {
            $sqlquery .= " AND materia.cuatrimestre = ?";
            $bind_params = 'ii';
        }

        try {
            $res = (!is_null($cuatrimestre)) ? $this->SqlOp->exec($sqlquery, $bind_params, [$this->data["id"], $cuatrimestre]) : $this->SqlOp->exec($sqlquery, $bind_params, [$this->data["id"]]);

            if (count($res) > 0) {
                foreach ($res as $materia) {
                    $ids_materias[] = $materia["id"];
                }
            }

        } catch (CConnexionException | SQLTransactionException $e) {
            $ids_materias = array();
        }

        return $ids_materias;

    }

    /**
     * Método que revisa la existencia de un nombre corto registrado en los planes de estudio
     * @param String $clave nombre corto a buscar en la tabla plan_de_estudio
     * @return bool
     */
    private function _check_clave_existente(String $clave) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) as existe FROM plan_de_estudio WHERE clave = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "s", [$clave])[0]["existe"] > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _data_return($key) {
        if ($key == "id_carrera" || $key == "carrera") {
            $dataReturn = $this->data["id_carrera"];
        } else {
            $dataReturn = $this->data[$key];
        }

        return $dataReturn;
    }
}