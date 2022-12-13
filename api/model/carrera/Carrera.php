<?php

namespace dsa\api\model\carrera;

use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\carrera\Exceptions\UsuarioDirectorYaAsignadoACarreraException;
use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ParametrosNoValidosException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\imparten\ImpartenEn;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioNoHabilitadoComoDirectorException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;

class Carrera
{

    const INGENIERIA = "Ing";
    const LICENCIATURA = "Lic";
    const MAESTRIA_INGENIERIA = "M.I.";

    // todo: modificar a que sólo sea un array
    private ?array $data;

    private bool $is_new;
    private ?array $data_tmp;
    private ?COperacionesSQL $SqlOp;

    /**
     * @throws CarreraNoExistenteException
     */
    private function __construct(int $id=null, String $nombre=null, String $clave=null, Usuario $director=null, bool $is_new=false, array $newData=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->is_new = $is_new;
        $this->data = null;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_carrera_by_id($id)) {
                    throw new CarreraNoExistenteException("La carrera con id: $id no existe.", 3001);
                }
            }

            if (!is_null($nombre)) {
                if (!$this->_get_carrera_by_nombre($nombre)) {
                    throw new CarreraNoExistenteException("La carrera con nombre: $nombre no existe", 3002);
                }
            }

            if (!is_null($clave)) {
                if (!$this->_get_carrera_by_clave($clave)) {
                    throw new CarreraNoExistenteException("La carrera con nombre corto $clave no existe", 3003);
                }
            }

            if (!is_null($director)) {
                $id_usuario_director = $director->get_data("id");
                if (!$director->es_director() || !$this->_get_carrera_by_director($id_usuario_director)) {
                    throw new CarreraNoExistenteException("El usuario $director no ha sido asignado como director de alguna carrera", 3004);
                }
            }
        }
        else {
            $this->data_tmp = $newData;
        }
    }

    //******************************************************************************************************************
    //************************* Pseudo constructores de la clase *******************************************************
    //******************************************************************************************************************

    /**
     * Factoría que obtiene una instancia de la clase Carrera. Se utiliza el id de la carrera como campo de búsqueda.
     * @throws CarreraNoExistenteException
     */
    public static function get_carrera_by_id(int $id_carrera, ?COperacionesSQL &$cop=null) : Carrera {
        return new Carrera($id_carrera, null, null, null, false, null, $cop);
    }

    /**
     * @param String $nombre_carrera
     * @return Carrera
     * @throws CarreraNoExistenteException
     */
    public static function get_carrera_by_name(String $nombre_carrera , ?COperacionesSQL &$cop=null) : Carrera {
        return new Carrera(null, $nombre_carrera, null, null, false, null, $cop);
    }

    /**
     * @param String $clave
     * @return Carrera
     * @throws CarreraNoExistenteException
     */
    public static function get_carrera_by_clave(String $clave, ?COperacionesSQL &$cop=null) : Carrera {
        return new Carrera(null, null, $clave, null, false, null, $cop);
    }

    /**
     * @param Usuario $director
     * @return Carrera
     * @throws CarreraNoExistenteException
     */
    public static function get_carrera_by_director(Usuario $director, ?COperacionesSQL &$cop=null) : Carrera {
        return new Carrera(null, null, null, $director, false, null, $cop);
    }

    /**
     * @param Usuario $director
     * @param String $nombre
     * @param String $clave
     * @param string $nivel
     * @return Carrera|null
     * @throws CarreraException
     * @throws CarreraNoExistenteException
     * @throws UsuarioDirectorYaAsignadoACarreraException
     * @throws UsuarioNoHabilitadoComoDirectorException
     */
    public static function crear_nueva_carrera(Usuario $director, String $nombre, String $clave, String $nivel=Carrera::INGENIERIA, ?COperacionesSQL &$cop=null): ?Carrera
    {

        if (!$director->es_director()) {
            throw new UsuarioNoHabilitadoComoDirectorException("El usuario $director no ha sido habilitado como director, verifique", 3009);
        }

        try {
            $tmp_carrera = Carrera::get_carrera_by_director($director, $cop);

            if (!is_null($tmp_carrera)) {
                throw new UsuarioDirectorYaAsignadoACarreraException("El usuario $director ya ha sido asignado a otra carrera, no puede ser asignado nuevamente", 3011);
            }
        } catch (CarreraNoExistenteException $e) {
            $tmp_data = array("nombre" => $nombre, "clave" => $clave, "nivel" => $nivel, "id_director" => $director->get_data("id"));
            $tmp_carrera = new Carrera(null, null, null, null, true, $tmp_data, $cop);
            if ($tmp_carrera->_save()) {
                return Carrera::get_carrera_by_director($director, $cop);
            }
            else {
                throw new CarreraException("No se ha podido guardar la carrera, verifica los datos");
            }
        }

        return null;
    }

    public static function get_all(?array $filtro=null) : array {
        $tmpCarrera = new Carrera(null, null, null, null,false,null);
        return $tmpCarrera->_get_all_carreras($tmpCarrera->_create_sqlquery($filtro));
    }

    //******************************************************************************************************************
    //************************* invterfaz pública de la clase **********************************************************
    //******************************************************************************************************************

    public function actualiza_datos_de_carrera(array $data_to_update) : bool {
        $tmp_data = array();
        $allowed_keys = ["nombre", "clave", "nivel", "id_director"];

        foreach (array_keys($data_to_update) as $array_key) {
            if (!in_array($array_key, $allowed_keys))
            {
                throw new CarreraException("La llave $array_key no es válida, verifica la documentación", 3224);
            }
        }

        $tmp_data["nombre"] = $data_to_update["nombre"] ?? $this->data["nombre"];
        $tmp_data["clave"] = $data_to_update["clave"] ?? $this->data["clave"];
        $tmp_data["nivel"] = $data_to_update["nivel"] ?? $this->data["nivel"];
        $tmp_data["id_director"] = $data_to_update["id_director"] ?? $this->data["id_director"];

        if (!Usuario::get_usuario_by_id($tmp_data["id_director"])->es_director()) {
            throw new UsuarioNoHabilitadoComoDirectorException("El usuario " . $tmp_data["director"] . " no esta habilitado como director, verifique", 3005);
        }

        $this->data_tmp = $tmp_data;

        return $this->_save();
    }

    /**
     * @param null $filtro
     * @return array|mixed|null
     * @throws CarreraException
     */
    public function get_data($filtro=null) {

        $allowed_keys = ["id", "nombre", "clave", "nivel", "id_director", "director"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new CarreraException("La llave $filtro, no es válida. Verifica la documentación", 3179);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new CarreraException("La llave $key, no es válida. Verifica la documentación", 3261);
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
     * Método que obtiene la instancia de la clase Usuario del director asociado a la carrera.
     * @return Usuario
     * @throws UsuarioNoExistenteException
     */
    public function get_director() : Usuario {
        return Usuario::get_usuario_by_id($this->data["id_director"]);
    }

    /**
     * @return array
     */
    public function get_planes_de_estudio() : array {
        return PlanDeEstudio::get_all(["carrera" => $this]);
    }

    /**
     * Método que obtiene a todos los profesores adscritos a la carrera
     * @return array
     * @throws ProfesorNoExisteException
     */
    public function get_profesores_adscritos() : array {
        return $this->_get_ids_profesores_ascritos();
    }

    /**
     * Método que obtiene un array de CProfesor con los profesores que imparten materias en la carrera
     * @return array
     * @throws ParametrosNoValidosException
     */
    public function get_profesores_que_imparten() : array {
        $imparten = ImpartenEn::impartenEn_by_carrera($this);
        return $imparten->get_profesores();
    }

    /**
     * @param Profesor $profesor
     * @return bool
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorException
     */
    public function agrega_profesor_para_impartir(Profesor $profesor) : bool {
        $imparten = ImpartenEn::impartenEn_by_carrera($this);
        return $imparten->agrega_profesor($profesor);
    }

    /**
     * @param Profesor $profesor
     * @return bool
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws CarreraNoAgregadaException
     */
    public function quita_profesor_de_imparticion(Profesor $profesor) : bool {
        $imparten = ImpartenEn::impartenEn_by_carrera($this);
        return $imparten->quita_profesor($profesor);
    }

    public function __toString() : String {
        return !is_null($this->data) ? $this->data["nombre"] : "";
    }

    //******************************************************************************************************************
    //************************* invterfaz privada de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * Método que obtiene los datos de la carrera desde la base de datos
     * @param int $id
     */
    private function _get_carrera_by_id(int $id) : bool {
        $ban = false;
        $query = "SELECT carrera.nombre as nombre, carrera.clave as clave, carrera.nivel as nivel, carrera.director as id_usuario_director FROM carrera WHERE carrera.id = ?";

        try {
            $res = $this->SqlOp->exec($query, "i", [$id]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($id, $res[0]["nombre"], $res[0]["nivel"], $res[0]["id_usuario_director"], $res[0]["clave"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método que obtiene los datos de la carrera por medio del nombre de la carrera
     * @param string $nombre
     * @return bool
     */
    private function _get_carrera_by_nombre(string $nombre) : bool {
        $ban = false;
        $sqlquery = "SELECT carrera.id as id, carrera.clave as clave, carrera.nivel as nivel, carrera.director as id_usuario_director FROM carrera WHERE carrera.nombre = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "s", [$nombre]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($res[0]["id"], $nombre, $res[0]["nivel"], $res[0]["id_usuario_director"], $res[0]["clave"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método que obtiene los datos de la carrera por medio del nombre corto
     * @param string $clave
     * @return bool
     */
    private function _get_carrera_by_clave(string $clave) : bool {
        $ban = false;
        $sqlquery = "SELECT carrera.id as id, carrera.nombre as nombre, carrera.nivel as nivel, carrera.director as id_usuario_director FROM carrera WHERE carrera.clave = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "s", [$clave]);
            if (count($res) == 1) {
                $this->_asigna_campos_privados($res[0]["id"], $res[0]["nombre"], $res[0]["nivel"], $res[0]["id_usuario_director"], $clave);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método que asigna valores a los campos privados
     * @param int $id identificador único de la carrera
     * @param String $nombre nombre de la carrera
     * @param String $nivel nivel educativo de la carrera: Ingeniería, Licenciatura, Maestría en Ingeniería
     * @param int $id_usuario_director Identificador únido del usuario asignado como director.
     * @param String|null $clave Nombre corto para identificar a la carrera.
     */
    private function _asigna_campos_privados(int $id, String $nombre, String $nivel, int $id_usuario_director, String $clave=null) {
        $this->data["id"] = $id;
        $this->data["nombre"] = $nombre;
        $this->data["nivel"] = $nivel;
        $this->data["id_director"] = $id_usuario_director;
        $this->data["clave"] = $clave;
    }

    /**
     * Método que obtienen los datos de la carrera a través de identificador único del usuario designado como director de la carrera.
     * @param int $id_usuario_director
     * @return bool
     */
    private function _get_carrera_by_director(int $id_usuario_director) : bool {
        $ban = false;
        $sqlquery = "SELECT carrera.id as id, carrera.nombre as nombre, carrera.clave as clave, carrera.nivel as nivel FROM carrera, usuario WHERE carrera.director = usuario.id AND usuario.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id_usuario_director]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($res[0]["id"], $res[0]["nombre"], $res[0]["nivel"], $id_usuario_director, $res[0]["clave"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * Método que guarda los datos en la base de datos, ya sea para crear una nueva carrera o actualizar una existente
     * @return bool
     * @throws CarreraException
     */
    private function _save() : bool {
        $ban = false;

        if ($this->is_new) {
            if (!$this->_crea_nueva_carrera()) {
                throw new CarreraException("No se ha podido crear la nueva carrera, verifique los datos", 3010);
            }
            else {
                $ban = true;
            }
        }
        else {
            if (!$this->_actualiza_datos_de_carrera()) {
                throw new CarreraException("No se ha podido actualizar los datos de la carrera, verifique", 3008);
            }
            else {
                $ban = true;
            }
        }

        return $ban;
    }

    private function _actualiza_datos_de_carrera() : bool {
        $ban = false;
        $sqlquery = "UPDATE carrera SET nombre = ?, clave = ?, nivel = ?, director = ? WHERE carrera.id = ?";

        try {
            $ban = ($this->SqlOp->exec($sqlquery, "sssii", [$this->data_tmp["nombre"], $this->data_tmp["clave"], $this->data_tmp["nivel"],
            $this->data_tmp["id_director"], $this->data["id"]]) == 1);
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _crea_nueva_carrera() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO carrera(nombre, clave, nivel, director) VALUES (?, ?, ?, ?)";

        try {
            $ban = ($this->SqlOp->exec($sqlquery, "sssi", [$this->data_tmp["nombre"], $this->data_tmp["clave"], $this->data_tmp["nivel"],
                    $this->data_tmp["id_director"]]) == 1);
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _get_ids_planesDeEstudio() : array {
        $ids = array();
        $sqlquery = "SELECT plan_de_estudio.id as id FROM plan_de_estudio WHERE carrera = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id"]]);

            if (count($res) > 0) {
                foreach ($res as $r) {
                    $ids[] = $r["id"];
                }
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ids = array();
        }
        return $ids;
    }

    /**
     * Método que obtiene los IDs de los profesores adscritos a la materia.
     * @return array
     */
    private function _get_ids_profesores_ascritos() : array {
        $ids = array();
        $sqlquery = "SELECT profesor.id FROM profesor WHERE carrera_adscripcion = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id"]]);

            foreach ($res as $r) {
                $ids[] = $r["id"];
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ids = array();
        }

        return $ids;
    }

    private function _get_all_carreras(String $sqlquery) : array {
        $ids = array();
        $res = $this->SqlOp->exec($sqlquery);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT carrera.id FROM carrera";
        $allowed_keys = ["nivel"];
        $allowed_vals = ["Ing", "Lic", "M.I."];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) throw new LlaveDeBusquedaIncorrectaException("El filtro debe ser un diccionario con llaves permitidas, verifica la documentación", 3482);

            $sqlquery .= " WHERE ";
            foreach (array_keys($filtro) as $key) {
                if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave $key no es válida en el filtro de Carreras.", 3486);
                if (!is_array($filtro["nivel"])) {
                    if (!in_array($filtro["nivel"], $allowed_vals)) {
                        throw new LlaveDeBusquedaIncorrectaException("El valor " . $filtro["nivel"] . " no es válido, verifica la documentación", 3490);
                    } else {
                        $sqlquery .= "nivel = '" . $filtro["nivel"] . "'";
                    }
                } else {
                    $tmpArray = array();
                    foreach($filtro["nivel"] as $item) {
                        if (!in_array($item, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es válido, verifica la documentación", 3496);

                        $tmpArray[] = "nivel = '$item'";
                    }
                    $sqlquery .= "(" . implode(" OR ", $tmpArray) . ")";
                }
            }
        }
        return $sqlquery;
    }

    private function _data_return($key) {
        if ($key == "id_director" || $key == "director") {
            $dataReturn = $this->data["id_director"];
        } else {
            $dataReturn = $this->data[$key];
        }

        return $dataReturn;
    }

}