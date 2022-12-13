<?php

namespace dsa\api\model\profesor;

use DateTime;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ParametrosNoValidosException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\imparten\ImpartenEn;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Exceptions\FechaDeContratoException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Exceptions\ProfesorYaExistenteException;
use dsa\api\model\usuario\Exceptions\TipoDeUsuarioNoValidoException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\DataChecker;
use dsa\lib\Utils\DateUtils;
use Exception;

class Profesor
{

    const CONTRATO_INDEFINIDO = "indf";

    const NIVEL_ADSCRIPCION_LIC = 'Lic.';
    const NIVEL_ADSCRIPCION_ING = 'Ing.';
    const NIVEL_ADSCRIPCION_MC = 'M.C.';
    const NIVEL_ADSCRIPCION_MA = 'M.A.';
    const NIVEL_ADSCRIPCION_DR = 'Dr.';

    const TIPO_CONTRATO_PTC = 'P.T.C';
    const TIPO_CONTRATO_PA = 'P.A';

    const CATEGORIA_A = 'A';
    const CATEGORIA_B = 'B';
    const CATEGORIA_C = 'C';
    const CATEGORIA_D = 'D';

    private ?array $data;
    private ?array $data_tmp;

    private bool $is_new;
    private ?COperacionesSQL $SqlOp;

    /**
     * Constructor privado de la clase CProfesor
     * @param int|null $id
     * @param String|null $correo
     * @param Usuario|null $usuario
     * @param bool $is_new
     * @param array|null $new_data
     * @param COperacionesSQL|null $cop
     * @throws ProfesorNoExisteException
     * @throws Exception
     */
    private function __construct(int $id=null, String $correo=null, ?Usuario $usuario=null, bool $is_new=false, array $new_data=null, COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->data = null;
        $this->data_tmp = null;

        $this->is_new = $is_new;

        if ($is_new) {
            $this->data_tmp = $new_data;
        }
        else {
            if (!is_null($id)) {
                if (!$this->_get_profesor_by_id($id)) {
                    throw new ProfesorNoExisteException("El profesor con id: $id, no existe.", 6001);
                }
            }

            if (!is_null($correo)) {
                if (!$this->_get_profesor_by_email($correo)) {
                    throw new ProfesorNoExisteException("El profesor con correo: $correo, no existe", 6002);
                }
            }

            if (!is_null($usuario)) {
                if (!$this->_get_profesor_by_usuario($usuario)) {
                    throw new ProfesorNoExisteException("El perfil no esta asociado a un profesor, verifique", 6009);
                }
            }
        }
    }

    //******************************************************************************************************************
    //************************* Factorias de la clase *******************************************************
    //******************************************************************************************************************

    /**
     * Factoría que obtiene una instancia de la clase CProfesor  que utiliza el identificador único como campo de búsqueda
     * @throws ProfesorNoExisteException
     */
    public static function get_profesor_by_id(int $id, ?COperacionesSQL &$cop=null) : Profesor {
        return new Profesor($id, null, null, false, null, $cop);
    }

    /**
     * Factoría que obtiene una instancia de la clase CProfesor  que utiliza el correo electrónico como campo de búsqueda
     * @throws ProfesorNoExisteException
     */
    public static function get_profesor_by_email(String $correo, ?COperacionesSQL &$cop=null) : Profesor {
        return new Profesor(null, $correo, null, false, null, $cop);
    }

    /**
     * Factoría que obtiene una instancia de la clase profesor que utiliza el perfil asociado como parámetro de búsqueda.
     * @param Usuario $usuario
     * @param COperacionesSQL|null $cop
     * @return Profesor
     * @throws ProfesorNoExisteException
     */
    public static function get_profesor_by_usuario(Usuario $usuario, ?COperacionesSQL &$cop=null) : Profesor {
        if (!$usuario->es_profesor()) {
            new TipoDeUsuarioNoValidoException("No es posible obtener el perfil de profesor, $usuario no tiene rol de profesor", 6013);
        }

        return new Profesor(null, null, $usuario, false, null, $cop);
    }

    /**
     * Factoría que genera un nuevo registro de CProfesor en la base de datos
     * @param Usuario $usuario
     * @param Carrera $carrera_adcripcion Carrera de adscripción asignada al profesor
     * @param string $nivel_adscripcion nivel académico del profesor
     * @param string $tipo_contrato tipo de contrato del profesor: P.A, P.T.C
     * @param string $categoria categoría del contrato del profesor: A, B, C o D
     * @param string $inicio_contrato Fecha de inicio de contrato
     * @param String|null $fin_contrato Fecha de final de contrato. Al ser nula será un tipo de contrato indefinido.
     * @param COperacionesSQL|null $cop
     * @return null
     * @throws CarreraNoAgregadaException
     * @throws FechaDeContratoException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     * @throws ProfesorYaExistenteException
     * @throws TipoDeUsuarioNoValidoException
     * @throws UsuarioException
     */
    public static function crear_nuevo_profesor(Usuario $usuario, Carrera $carrera_adcripcion, String $nivel_adscripcion=Profesor::NIVEL_ADSCRIPCION_ING, String $tipo_contrato=Profesor::TIPO_CONTRATO_PA, String $categoria=Profesor::CATEGORIA_A, String $inicio_contrato="now", ?String $fin_contrato=null, ?COperacionesSQL &$cop=null) {

        try {

            if (!$usuario->es_profesor()) {
                throw new TipoDeUsuarioNoValidoException("No es posible generar el perfil del profesor, su rol no es profesor", 6008);
            }

            $tmp_profesor = Profesor::get_profesor_by_usuario($usuario, $cop);

            if (!is_null($tmp_profesor)) {
                $tmp_profesor = null;
                throw new ProfesorYaExistenteException("El usuario $usuario, ya tiene el perfil de profesor. No es posible generar otro perfil de profesor", 6018);
            }
        } catch (ProfesorNoExisteException $e) {
            try{
                $dt_inicio_contrato = ($inicio_contrato == "now") ? DateUtils::today() : new DateTime($inicio_contrato);
            } catch (Exception $e) {
                throw new FechaDeContratoException("La fecha de inicio de contrato no es válida", 6005);
            }

            if (!is_null($fin_contrato)) {
                try {
                    $dt_fin_contrato = new DateTime($fin_contrato);
                } catch (Exception $e) {
                    throw new FechaDeContratoException("La fecha de fin de contrato no es válida", 6006);
                }
            } else {
                $dt_fin_contrato = null;
            }

            $tmp_data = array("nivel_adscripcion" => $nivel_adscripcion, "tipo_contrato" => $tipo_contrato, "categoria" => $categoria, "inicio_contrato" => $dt_inicio_contrato, "fin_contrato" => $dt_fin_contrato, "id_carrera_adscripcion" => $carrera_adcripcion->get_data()["id"], "id_usuario" => $usuario->get_data("id"));
            $tmp_profesor = new Profesor(null, null, null, true, $tmp_data, $cop);

            if (!$tmp_profesor->_save()) {
                throw new ProfesorException("No fue posible guardar el perfil de profesor en estos momentos, verifique sus datos", 6019);
            }
            else {
                $tmp_profesor = Profesor::get_profesor_by_usuario($usuario, $cop);
                $profesorImpartiraEn = ImpartenEn::impartenEn_by_profesor($tmp_profesor, $cop);
                if (!$profesorImpartiraEn->agrega_carrera($carrera_adcripcion)) {
                    throw new CarreraNoAgregadaException("El profesor se creó sin problema, no se pudo agregar a la tabla carreras compatidas", 6045);
                }
            }
        }

        return $tmp_profesor;
    }

    /**
     * Método que obtiene como un array de String los niveles de adscripción válidos
     * @return string[]
     */
    public static function obten_niveles_de_ascripcion() : array {
        return array('Lic.' => 'Licenciatura', 'Ing.' => 'Ingeniería', 'M.C.' => 'Maestro en Ciencia', 'M.A.' => 'Maestro en Administración', 'Dr.' => 'Doctor');
    }

    /**
     * Método que retorna como un array de String los tipos de cotrantos
     * @return string[]
     */
    public static function obten_tipos_contrato() : array {
        return array('P.T.C' => 'CProfesor de Tiempo Completo', 'P.A' => 'CProfesor de Asignatura');
    }

    /**
     * Método que retorna las categorías de contrato que puede tener un profesor.
     * @return string[]
     */
    public static function obten_categorias() : array {
        return array("A" => "A", "B" => "B", "C" => "C", "D" => "D");
    }

    /**
     * @param array|null $filtro
     * @return array
     * @throws LlaveDeBusquedaIncorrectaException
     */
    public static function get_all(array $filtro=null) {
        $tmpProfesor = new Profesor(null, null, null, false, null);
        return $tmpProfesor->_get_all_ids($tmpProfesor->_create_sqlquery($filtro));
    }

    //******************************************************************************************************************
    //************************* invterfaz pública de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * Método que recibe el array con los nuevos datos
     * @param array $data_to_update
     * @return bool
     * @throws ProfesorException
     */
    public function actualiza_datos_de_profesor(array $data_to_update): bool
    {
        $allowed_keys = ["nivel_adscripcion", "tipo_contrato", "categoria", "inicio_contrato", "fin_contrato", "carrera_adscripcion"];
        $tmp_data = array();

        foreach (array_keys($data_to_update) as $key) {
            if (!in_array($key, $allowed_keys)) {
                throw new ProfesorException("La llave $key, no es válida para actualizar. Verifica la documentación", 6246);
            }
        }

        $tmp_data["nivel_adscripcion"] = $data_to_update["nivel_adscripcion"] ?? $this->data["nivel_adscripcion"];
        $tmp_data["tipo_contrato"] = $data_to_update["tipo_contrato"] ?? $this->data["tipo_contrato"];
        $tmp_data["categoria"] = $data_to_update["categoria"] ?? $this->data["categoria"];
        $str_inicio_contrato = $data_to_update["inicio_contrato"] ?? null;
        $tmp_data["inicio_contrato"] = (is_null($str_inicio_contrato)) ? $this->data["inicio_contrato"] : new DateTime($str_inicio_contrato);
        $str_fin_contrato = $data_to_update["fin_contrato"] ?? null;
        if (!is_null($str_fin_contrato)) {
            if ($str_fin_contrato == "indf") {
                $tmp_data["fin_contrato"] = null;
            }
            else {
                $tmp_data["fin_contrato"] = new DateTime($str_fin_contrato);
            }
        }
        else {
            $tmp_data["fin_contrato"] = $this->data["fin_contrato"];
        }
        $tmp_data["carrera_adscripcion"] = $data_to_update["carrera_adscripcion"] ?? $this->data["id_carrera_adscripcion"];

        $this->data_tmp = $tmp_data;

        return $this->_save();
    }



    /**
     * @param null $filtro
     * @return array|mixed
     * @throws ProfesorException
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["id", "nivel_adscripcion", "tipo_contrato", "categoria", "inicio_contrato", "fin_contrato", "id_carrera_adscripcion", "carrera_adscripcion", "id_usuario", "usuario"];
        if (is_null($filtro)) { // si el fitro el nulo, regresamos todos los datos
            $dataReturn = $this->data;
            $dataReturn["inicio_contrato"] = $this->_data_return("inicio_contrato");
            $dataReturn["fin_contrato"] = $this->_data_return("fin_contrato");
        } else {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new ProfesorException("La llave $filtro, no es permitida. Verifica la documentación", 6274);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new ProfesorException("La llave $filtro, no es permitida. Verifica la documentación", 6282);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }
        }
        return $dataReturn;
    }

    /**
     * @return Usuario
     * @throws UsuarioNoExistenteException
     */
    public function get_usuario() : Usuario {
        return Usuario::get_usuario_by_id($this->data["id_usuario"]);
    }

    /**
     * @return Carrera
     * @throws CarreraNoExistenteException
     */
    public function get_carrera_adscripcion() : Carrera {
        return Carrera::get_carrera_by_id($this->data["id_carrera_adscripcion"]);
    }

    /**
     * @param Carrera $carrera
     * @param String|null $fecha_de_asignacion
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorNoAgregadoException
     */
    public function agrega_carrera_para_impartir(Carrera $carrera, ?String $fecha_de_asignacion=null): bool
    {
        $imparte_en = ImpartenEn::impartenEn_by_profesor($this, $this->SqlOp);
        return $imparte_en->agrega_carrera($carrera);
    }

    /**
     * @param Carrera $carrera
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     */
    public function quita_carrera_para_impartir(Carrera $carrera) {
        $imparte_en = ImpartenEn::impartenEn_by_profesor($this, $this->SqlOp);
        return $imparte_en->quita_carrera($carrera);
    }

    /**
     * Método que obtiene un array con los Id de las carreras en donde el profesor imparte materias
     * @param bool $exepto_adscripcion bandera para quitar de la lista la carrera de adscripción del profesor
     * @return array Id de las materias en donde imparte materias
     * @throws ParametrosNoValidosException
     */
    public function get_carreras_de_imparticion(bool $exepto_adscripcion=false) : array {
        $imparte_en = ImpartenEn::impartenEn_by_profesor($this);
        if (!$exepto_adscripcion) {
            return $imparte_en->get_carreras();
        } else {
            $tmpArray = array();
            foreach ($imparte_en->get_carreras() as $carrera) {
                if ($carrera != $this->data["id_carrera_adscripcion"]) {
                    $tmpArray[] = $carrera;
                }
            }
            return $tmpArray;
        }
    }

    /**
     * Método mágico que regresa la representación de la instancia como un String
     * @return string
     */
    public function __toString() {
        return strval($this->data["id"]);
    }

    //******************************************************************************************************************
    //************************* invterfaz privada de la clase **********************************************************
    //******************************************************************************************************************

    /**
     * Método que obtiene los datos del profesor desde la base de datos utilizando el identificador único como campo de búsqueda
     * @param int $id
     * @return bool
     * @throws Exception
     */
    private function _get_profesor_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT profesor.id as id, profesor.nivel_adscripcion as nivel_adscripcion, profesor.tipo_contrato as tipo_contrato, profesor.categoria as categoria, profesor.inicio_contrato as inicio_contrato, profesor.fin_contrato as fin_contrato, profesor.carrera_adscripcion as id_carrera, profesor.usuario as id_usuario FROM profesor WHERE profesor.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $inicio_contrato = new DateTime($res[0]["inicio_contrato"]);
                $fin_contrato = (is_null($res[0]["fin_contrato"])) ? null : new DateTime($res[0]["fin_contrato"]);

                $this->_asigna_campos_privados($id, $res[0]["nivel_adscripcion"], $res[0]["tipo_contrato"], $res[0]["categoria"], $inicio_contrato, $fin_contrato, $res[0]["id_carrera"], $res[0]["id_usuario"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que asigna valores a los campos privados de la clase.
     * @param int $id
     * @param String $nivel_adscripcion
     * @param String $tipo_contrato
     * @param String $categoria
     * @param DateTime $inicio_contrato
     * @param DateTime|null $fin_contrato
     * @param int $id_carrera
     * @param int $id_usuario
     */
    private function _asigna_campos_privados(int $id, String $nivel_adscripcion, String $tipo_contrato, String $categoria, DateTime $inicio_contrato, ?DateTime $fin_contrato, int $id_carrera, int $id_usuario) {
        $this->data["id"] = $id;
        $this->data["nivel_adscripcion"] = $nivel_adscripcion;
        $this->data["tipo_contrato"] = $tipo_contrato;
        $this->data["categoria"] = $categoria;
        $this->data["inicio_contrato"] = $inicio_contrato;
        $this->data["fin_contrato"] = $fin_contrato;
        $this->data["id_carrera_adscripcion"] = $id_carrera;
        $this->data["id_usuario"] = $id_usuario;
    }

    /**
     * Método que obtiene los datos del profesor desde la base de datos utilizando el correo electrónico como campo de búsqueda
     * @param String $correo
     * @return bool
     * @throws Exception
     */
    private function _get_profesor_by_email(String $correo): bool
    {
        $ban = false;
        $sqlquery = "SELECT profesor.id, profesor.nivel_adscripcion, profesor.tipo_contrato, profesor.categoria, profesor.inicio_contrato, profesor.fin_contrato, profesor.carrera_adscripcion as id_carrera, profesor.usuario as id_usuario FROM profesor, usuario WHERE profesor.usuario = usuario.id and usuario.email = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "s", [$correo]);

            if (count($res) == 1) {

                $inicio_contrato = new DateTime($res[0]["inicio_contrato"]);
                $fin_contrato = (is_null($res[0]["fin_contrato"])) ? null : new DateTime($res[0]["fin_contrato"]);

                $this->_asigna_campos_privados($res[0]["id"], $res[0]["nivel_adscripcion"], $res[0]["tipo_contrato"], $res[0]["categoria"], $inicio_contrato, $fin_contrato, $res[0]["id_carrera"], $res[0]["id_usuario"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * @param Usuario $usuario
     * @return bool
     * @throws Exception
     */
    private function _get_profesor_by_usuario(Usuario $usuario): bool
    {
        $ban = false;
        $sqlquery = "SELECT profesor.id, profesor.nivel_adscripcion, profesor.tipo_contrato, profesor.categoria, profesor.inicio_contrato, profesor.fin_contrato, profesor.carrera_adscripcion as id_carrera_adscripcion FROM profesor WHERE profesor.usuario = ?";
        $id_usuario = $usuario->get_data("id");
        try {

            $res = $this->SqlOp->exec($sqlquery, "i", [$id_usuario]);

            if (count($res) == 1) {
                $dt_inicio_contrato = new DateTime($res[0]["inicio_contrato"]);
                $dt_fin_contrato = is_null($res[0]["fin_contrato"]) ? null : new DateTime($res[0]["fin_contrato"]);

                $this->_asigna_campos_privados($res[0]["id"], $res[0]["nivel_adscripcion"], $res[0]["tipo_contrato"], $res[0]["categoria"], $dt_inicio_contrato, $dt_fin_contrato, $res[0]["id_carrera_adscripcion"], $id_usuario);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _save() : bool {
        $ban = false;
        if ($this->is_new) {
            if (!$this->_crear_profesor_en_db()) {
                throw new ProfesorException("No fue posible guardar el perfil de profesor en la base de datos", 6025);
            }
            else {
                $ban = true;
            }
        }
        else {
            if (!$this->_actualizar_datos_profesor_en_db()) {
                throw new ProfesorException("No fue posible actualizar el registro del profesor, verifique los datos", 6026);
            }
            else {
                $ban = true;
            }
        }

        return $ban;
    }

    private function _crear_profesor_en_db() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO profesor (nivel_adscripcion, tipo_contrato, categoria, inicio_contrato, fin_contrato, carrera_adscripcion, usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";

        try {
            $str_inicio_contrato = $this->data_tmp["inicio_contrato"]->format("Y-m-d");
            $str_fin_contrato = (is_null($this->data_tmp["fin_contrato"])) ? null : $this->data_tmp["fin_contrato"]->format("Y-m-d");

            $ban = $this->SqlOp->exec($sqlquery, "sssssii", [$this->data_tmp["nivel_adscripcion"], $this->data_tmp["tipo_contrato"], $this->data_tmp["categoria"], $str_inicio_contrato, $str_fin_contrato, $this->data_tmp["id_carrera_adscripcion"], $this->data_tmp["id_usuario"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            echo $e->getMessage();
            $ban = false;
        }

        return $ban;
    }

    private function _actualizar_datos_profesor_en_db() : bool {
        $ban = false;
        $sqlquery = "UPDATE profesor SET nivel_adscripcion = ?, tipo_contrato = ?, categoria = ?, inicio_contrato = ?, fin_contrato = ?, carrera_adscripcion = ? WHERE id = ?";

        try {
            $str_inicio_contrato = $this->data_tmp["inicio_contrato"]->format("Y-m-d");
            $str_fin_contrato = (is_null($this->data_tmp["fin_contrato"])) ? null : $this->data_tmp["fin_contrato"]->format("Y-m-d");

            $ban = $this->SqlOp->exec($sqlquery, "sssssii", [$this->data_tmp["nivel_adscripcion"], $this->data_tmp["tipo_contrato"], $this->data_tmp["categoria"], $str_inicio_contrato, $str_fin_contrato, $this->data_tmp["carrera_adscripcion"], $this->data["id"]]) == 1;

            if ($ban) {
                $this->_asigna_campos_privados($this->data["id"], $this->data_tmp["nivel_adscripcion"], $this->data_tmp["tipo_contrato"], $this->data_tmp["categoria"], $this->data_tmp["inicio_contrato"], $this->data_tmp["fin_contrato"],$this->data_tmp["carrera_adscripcion"], $this->data["id_usuario"]);
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _get_carreras_donde_imparte_materias() : array {
        $imparteEn = ImpartenEn::impartenEn_by_profesor($this);
        $tmpIdsCarreras = array();
        try {
            $tmpIdsCarreras = $imparteEn->get_carreras();
        } catch (CarreraNoExistenteException | ParametrosNoValidosException $e) {
            $tmpIdsCarreras = array();
        }

        return $tmpIdsCarreras;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT profesor.id as id FROM profesor";
        $allowed_keys = ["nivel_adscripcion", "tipo_contrato", "categoria", "inicio_contrato", "fin_contrato", "carrera_adscripcion", "nombre", "full_name","bibtex_style_name"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) {
                throw new LlaveDeBusquedaIncorrectaException("El fitro debe ser un diccionario con llaves permitidas, verifica la documentación", 6234);
            } else {
                $sqlquery .= " WHERE ";
                $tmpArray = array();
                foreach (array_keys($filtro) as $key) {
                    if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 6237);

                    switch ($key) {
                        case "carrera_adscripcion":
                            $tmpArray[] = $this->_block_query_carrera($filtro["carrera_adscripcion"]);
                            break;
                        case "nivel_adscripcion":
                        case "tipo_contrato":
                        case "categoria":
                        case "carrera_adscripcion":
                            $isString = true;
                            if ($key == "nivel_adscripcion") $allowed_vals = array_keys(Profesor::obten_niveles_de_ascripcion());
                            if ($key == "tipo_contrato") $allowed_vals = array_keys(Profesor::obten_tipos_contrato());
                            if ($key == "categoria") $allowed_vals = array_keys(Profesor::obten_categorias());
                            $tmpArray[] = $this->_block_query($filtro[$key], $key, $allowed_vals, $isString);
                            break;
                        case "fin_contrato":
                        case "inicio_contrato":
                            $tmpArray[] = $this->_block_query_contrato($filtro[$key], $key);
                            break;
                        case "nombre":
                            $sqlquery = str_replace("FROM profesor", "FROM profesor, usuario", $sqlquery);
                            $tmpArray[] = " profesor.usuario = usuario.id AND (lower(usuario.nombre) LIKE LOWER('%" . $filtro["nombre"] . "%') OR LOWER(usuario.apellidos) LIKE LOWER('%" . $filtro["nombre"] ."%')) ";
                            break;
                        case "full_name":
                            $sqlquery = str_replace("FROM profesor", "FROM profesor, usuario", $sqlquery);
                            $tmpArray[] = " profesor.usuario = usuario.id AND (CONCAT(usuario.apellidos, ' ', usuario.nombre) LIKE '%" . $filtro["full_name"] . "%')";
                            break;
                        case "bibtex_style_name":
                            $sqlquery = str_replace("FROM profesor", "FROM profesor, usuario", $sqlquery);
                            $tmpArray[] = " profesor.usuario = usuario.id AND (CONCAT(SUBSTRING_INDEX(usuario.apellidos, ' ', 1), '-', SUBSTRING_INDEX(usuario.apellidos, ' ', -1), ', ', usuario.nombre) LIKE '%" . $filtro["bibtex_style_name"] . "%')";
                            break;
                        default:
                            echo "$key aún no implementada";
                            break;
                    }
                }
                $sqlquery .= implode(" AND ", $tmpArray);
            }
        }
        return $sqlquery . " GROUP BY profesor.id";
    }

    private function _block_query_carrera($block) {
        $strBlock = "";
        if (!is_array($block)) {
            if (DataChecker::check_instance_of($block, "Carrera")) {
                $strBlock = "profesor.carrera_adscripcion = " . $block->get_data("id");
            } elseif (is_numeric($block)) {
                $tmpCarrera = Carrera::get_carrera_by_id($block);
                $strBlock = "profesor.carrera_adscripcion = " . $tmpCarrera->get_data("id");
            } else {
                throw new CarreraException("Se esperaba el Id o una Instancia de la Clase Carrera", 6629);
            }
        } else {
            $tmpArray = array();
            foreach($block as $e) {
                if (DataChecker::check_instance_of($e, "Carrera")) {
                    $tmpArray[] = "profesor.carrera_adscripcion = " . $e->get_data("id");
                } elseif (is_numeric($e)) {
                    $tmpCarrera = Carrera::get_carrera_by_id($e);
                    $tmpArray[] = "profesor.carrera_adscripcion = " . $tmpCarrera->get_data("id");
                } else {
                    throw new CarreraException("Se esperaba el Id o una Instancia de la Clase Carrera", 6640);
                }
            }
            $strBlock = "(" . implode(" OR ", $tmpArray) . ")";
        }
        return $strBlock;
    }

    private function _block_query_contrato($contrato, String $str_attr) : String {
        $strRestult = null;
        if (is_null($contrato)) {
            $strRestult = "$str_attr = NULL";
        } else {
            if (!is_array($contrato)) {
                if ($contrato == "indf") $strRestult = "$str_attr = NULL";
                else {
                    try {
                        $dt_finContrato = new DateTime($contrato);
                        $strRestult = "$str_attr = " . $dt_finContrato->format("Y/m/d");
                    } catch (Exception $e) {
                        throw new LlaveDeBusquedaIncorrectaException("La fecha de contrato no es correcta, verificala");
                    }
                }
            } else { // Si es un array
                if (count($contrato) != 2) throw new LlaveDeBusquedaIncorrectaException("La llave de búsqueda de contrato no esta bien formada, verifica la documentación", 6285);
                else { // deben ser 2 fechas:
                    try {
                        $dt_Desde = new DateTime($contrato[0]);
                        $dt_hasta = new DateTime($contrato[1]);
                        $strRestult = "($str_attr BETWEEN " . $dt_Desde->format("Y/m/d") . " AND " . $dt_hasta->format("Y/m/d") . ")";
                    } catch (Exception $e) {
                        throw new LlaveDeBusquedaIncorrectaException("El formato de las fechas de bpusqueda de contrato, no son correctas, verifica la documentación", 6291);
                    }
                }
            }
        }

        return $strRestult;
    }


    private function _block_query($block, String $str_attr, array $allowed_vals, bool $isString=true) : String {
        $strResult = "";

        if (!is_array($block)) {
            if (!in_array($block, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es correcto, varificalo", 266);
            $strBlock = ($isString) ? "'$block'" : strval($block);
            $strResult = "profesor.$str_attr = $strBlock";
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!in_array($item, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es correcto, verificalo", 6272);
                $strItem = ($isString) ? "'$item'" : strval($item);
                $tmpArray[] = "profesor.$str_attr = $strItem";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) . ")";
        }

        return $strResult;
    }

    private function _get_all_ids(string $_create_sqlquery) : array {
        return $this->SqlOp->exec($_create_sqlquery);
    }

    private function _data_return(String $key) {
        if ($key == "id_carrera_adscripcion" || $key == "carrera_adscripcion") {
            $dataReturn = $this->data["id_carrera_adscripcion"];
        } elseif ($key == "id_usuario" || $key == "usuario") {
            $dataReturn = $this->data["id_usuario"];
        } elseif ($key == "inicio_contrato" || $key == "fin_contrato") {
            $dataReturn = (!is_null($this->data[$key])) ? $this->data[$key]->format("Y/m/d") : "";
        } else {
            $dataReturn = $this->data[$key];
        }

        return $dataReturn;
    }
}