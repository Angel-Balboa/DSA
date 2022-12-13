<?php

namespace dsa\api\model\usuario;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Exceptions\ContrasenaNoValidaException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoPermitidoException;
use dsa\api\model\usuario\Exceptions\UsuarioYaExistenteException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\lib\Utils\DataChecker;

class Usuario
{
    const TIPO_PROFESOR = "profesor";
    const TIPO_DIRECTOR = "director";
    const TIPO_RECURSOS_HUMANOS = "RRHH";

    private ?array $data;
    private ?array $tmp_data;
    private ?COperacionesSQL $SqlOp;
    private bool $is_new;

    /**
     * Constructor de la clase Usuario. NO TIENE INTERFAZ PÚBLICA.
     * @param String|null $email Correo electrónico de del usuario
     * @param int|null $id Identificador único del usuario
     * @param bool $is_new
     * @param array|null $data
     * @param COperacionesSQL|null $cop
     * @throws UsuarioNoExistenteException
     */
    private function __construct(String $email=null, int $id=null, bool $is_new=false, array $data=null, ?COperacionesSQL &$cop=null) {

        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->is_new = $is_new;

        $this->data = null;

        if (!$is_new) {
            if (!is_null($email)) {
                if (!$this->_get_usuario_by_email($email)) {
                    throw new UsuarioNoExistenteException("El usuario con email: $email no existe.", 1001);
                }
            }

            if (!is_null($id)) {
                if (!$this->_get_usuario_by_id($id)) {
                    throw new UsuarioNoExistenteException("El usuario con id: $id no existe", 1002);
                }
            }
        }
        else {
            $this->tmp_data = $data;
        }
    }

    //******************************************************************************************************************
    //******************************** INTERFAZ PUBLICA DE LA CLASE ****************************************************
    //******************************************************************************************************************

    /**
     * Método estático que retorna un objetivo de tipo Usuario con los datos de un usuario existente, se utiliza el
     * correo del usuario como campo de búsqueda
     * @param String $email correo electrónico del usuario.
     * @param COperacionesSQL|null $cop
     * @return Usuario
     * @throws UsuarioNoExistenteException
     */
    public static function get_usuario_by_email(String $email, ?COperacionesSQL &$cop=null): Usuario
    {
        return new Usuario($email, null, false, null, $cop);
    }

    /**
     * Método estático que retorna un objetivo de tipo Usuario con los datos de un usuario existente, se utiliza el
     * id del usuario como campo de búsqueda
     * @param int $id identificador único del usuario
     * @param COperacionesSQL|null $cop
     * @return Usuario
     * @throws UsuarioNoExistenteException
     */
    public static function  get_usuario_by_id(int $id, ?COperacionesSQL &$cop=null): Usuario
    {
        return new Usuario(null, $id, false, null, $cop);
    }

    /**
     * Método que genera el registro de un nuevo usuario
     * @param String $email correo electrónico del usuario
     * @param String $pw contraseña del usuario
     * @param String $nombre nombre del usuario
     * @param String $apellidos apellidos del usuario
     * @param String|null $telefono telefono del usuario
     * @param String|null $extension extensión telefónica institucional del usuario
     * @param String|null $foto foto de perfil
     * @param String $tipo tipo de usuario ["profesor", "director", "RRHH"]
     * @param bool $activo bandera de usuario activo
     * @param COperacionesSQL|null $cop Instancia de la clase COperacionesSQL con los permisos indicados.
     * @return Usuario|null
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     * @throws UsuarioYaExistenteException
     */
    public static function create_new_user(String $email, String $pw, String $nombre, String $apellidos, ?String $telefono=null, ?String $extension=null, ?String $foto=null, String $tipo=Usuario::TIPO_PROFESOR, bool $activo=true,  ?COperacionesSQL &$cop=null): ?Usuario
    {
        $tmpUsuario = null;

        try {
            $tmpUsuario = Usuario::get_usuario_by_email($email);
            if (!is_null($tmpUsuario)) {
                unset($tmpUsuario);
                throw new UsuarioYaExistenteException("Ya existe un usuario registrado con el correo: $email", 1007);
            }
        } catch (UsuarioNoExistenteException $e) {
            $tmp_data = array("email" => $email, "password" => $pw, "tipo" => $tipo, "activo" => $activo, "nombre" => $nombre, "apellidos" => $apellidos, "telefono" => $telefono, "extension" => $extension, "foto" => $foto);
            $tmpUsuario  = new Usuario(null, null, true, $tmp_data, $cop);

            return $tmpUsuario->_save() ? Usuario::get_usuario_by_email($email, $cop) : null;
        }

        return null;
    }

    /**
     * @param array|null $filtros
     * @return array
     * @throws UsuarioException
     */
    public static function get_all(?array $filtros=null) : array {
        $tmpUser = new Usuario(null, null,false, null);
        return $tmpUser->_get_all_usuarios($tmpUser->_create_sqlquery($filtros));
   }

    public function get_profesor() : Profesor {
        if (!$this->data["tipo"] == "profesor") {
            throw new UsuarioException("El usario no tiene el perfil de profesor.", 1209);
        }
        return Profesor::get_profesor_by_usuario($this);
    }

    public function get_carrera() : Carrera {
        if (!$this->data["tipo"] == "director") {
            throw new UsuarioException("El usuario no tiene el perfil de director", 12216);
        }

        return Carrera::get_carrera_by_director($this);
    }

    /**
     * @param null $filtro
     * @return array|mixed|null
     * @throws UsuarioException
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["id", "email", "tipo", "activo", "nombre", "apellidos", "telefono", "extension", "foto"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new UsuarioException("La llave: $filtro no es permitida, verifica la documentación", 1230);
                } else {
                    $dataReturn = $this->data[$filtro];
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new UsuarioException("La llave: $key no es permitida, verifica la documentación", 1221);
                    } else {
                        $dataReturn[$key] = $this->data[$key];
                    }
                }
            }
        } else {
          $dataReturn = $this->data;
        }

        return $dataReturn;
    }

    /** Método mágico que retorna el string del objeto, (correo electrónico) del usuario
     * @return String
     */
    public function __toString() : String {
        return $this->data["email"] ?? "";
    }

    /**
     * Método que se encarga de revisar las credenciales del usuario para conceder acceso a sesión  o no.
     * @param String $passw contraseña del usuario
     * @return bool
     * @throws ContrasenaNoValidaException
     * @throws UsuarioNoPermitidoException
     */
    public function autorizar_acceso(String $passw) : bool {

        if (!$this->data["activo"]) {
            throw new UsuarioNoPermitidoException("El usuario no esta activo. Solicite su activación al administrador", 1005);
        }

        if (!$this->_revisar_credenciales($passw)) {
            throw new ContrasenaNoValidaException("La contraseña no es válida", 1003);
        }

        return true;
    }

    /**
     * Método que actualiza la contraseña de usuario solicitando y verificando el password anterior.
     * @param String $old_password contraseña anterior
     * @param String|null $new_password contraseña nueva. En caso de se nula, se generará una contraseña aleatoria
     * @return bool
     * @throws ContrasenaNoValidaException se lanza cuando la contraseña anterior no es válida
     * @throws UsuarioNoPermitidoException se lanza cuando el usuario no esta activo en el sistema.
     */
    public function actualiza_contrasena(String $old_password, String $new_password) : bool {
        $ban = false;
        if ($this->autorizar_acceso($old_password)) {
            $ban = $this->_actualiza_contrasena_en_db($new_password);
        }

        return $ban;
    }

    public function reestablece_contrasena(String $new_password) : bool {
        return $this->_actualiza_contrasena_en_db($new_password);
    }

    /**
     * Método que actualiza los datos del registro de un usuario.
     * @param array $newData Array asosiativo que guarda los nuevo valores del usuario. Las llaves permitidas son: ["email", "tipo", "activo", "nombre",  "apellidos", "telefono", "extension", "foto"]
     * @return bool
     * @throws UsuarioException
     */
    public function actualiza_datos_de_usuario(array $newData) : bool {
        $tmp_data = array();
        $allowed_keys = array("email", "tipo", "activo", "nombre", "apellidos", "telefono", "extension", "foto");
        $newData_keys = array_keys($newData);

        foreach ($newData_keys as $key) {
            if (!in_array($key, $allowed_keys)) {
                throw new UsuarioException("La llave $key no es permitida, verifica la documentación", 1285);
            }
        }

        $tmp_data["email"] = $newData["email"] ?? $this->data["email"];
        if (!filter_var($tmp_data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new UsuarioException("El formato del correo electrónico del usuario, no es válido.", 1321);
        }

        $tmp_data["tipo"] = $newData["tipo"] ?? $this->data["tipo"];
        if (!in_array($tmp_data["tipo"], array("profesor", "director", "RRHH"))) {
            throw new UsuarioException("El tipo de usuario no es permitido, verifica la documentación", 1279);
        }

        $tmp_data["activo"] = $newData["activo"] ?? $this->data["activo"];

        if (!is_bool($tmp_data["activo"])) {
            throw new UsuarioException("El valor de la llave \"activo\" debe ser booleano", 1285);
        }

        $tmp_data["nombre"] = $newData["nombre"] ?? $this->data["nombre"];
        $tmp_data["apellidos"] = $newData["apellidos"] ?? $this->data["apellidos"];

        $tmp_data["telefono"] = $newData["telefono"] ?? $this->data["telefono"];
        if ($tmp_data["telefono"] == "NULL" || is_null($tmp_data["telefono"]) ) {
            $tmp_data["telefono"] = null;
        } else {
            if (strlen($tmp_data["telefono"]) != 10) {
                throw new UsuarioException("El númpero de teléfono deben ser 10 dígitos", 1293);
            }

            if (!is_numeric($tmp_data["telefono"])) {
                throw new UsuarioException("El teléfono debe ser una cadena numérica de 10 dígitos", 1297);
            }
        }

        $tmp_data["extension"] = $newData["extension"] ?? $this->data["extension"];
        if ($tmp_data["extension"] == "NULL" || is_null($tmp_data["extension"])) {
            $tmp_data["extension"] = null;
        } else {
            if (strlen($tmp_data["extension"]) != 4) {
                throw new UsuarioException("El númpero de la extensión telefónica institucional deben ser 4 dígitos", 1303);
            }

            if (!is_numeric($tmp_data["extension"])) {
                throw new UsuarioException("La extensión telefónica institucional debe ser una cadena numérica de 4 dígitos", 1307);
            }
        }

        $tmp_data["foto"] = $newData["foto"] ?? $this->data["foto"];
        if ($tmp_data["foto"] == "NULL") {
            $tmp_data["foto"] = null;
        }

        $this->tmp_data = $tmp_data;

        return $this->_save();
    }

    /**
     * Método que revisa su el tipo de usuario es "director"
     *
     * Ejemplo: \n
     * $usr = Usuario::get_usuario_by_email("email@dominio.com"); \n
     * $usr->es_director()
     * @return bool
     */
    public function es_director() : bool {
        return $this->data["tipo"] == "director";
    }

    public function es_profesor() : bool {
        return ($this->data["tipo"] == "profesor" || $this->data["tipo"] == "director");
    }

    public function es_RRHH() : bool {
        return $this->data["tipo"] == "RRHH";
    }

    public function activo() : bool {
        return $this->data["activo"] ?? false;
    }


    //******************************************************************************************************************
    //******************************* INTERFAZ PRIVADA DE LA CLASE ****************************************************
    //****************************************************************************************************************

    /**
     * Metodo que actualiza la contraseña del usuario
     * @param String $new_password
     * @return bool
     */
    private function _actualiza_contrasena_en_db(String $new_password) : bool {
        $ban = false;
        $sqlquery = "UPDATE usuario SET pword = PASSWORD(?) WHERE id = ?";

        try {
            $ban = ($this->SqlOp->exec($sqlquery, "si", [$new_password, $this->data["id"]]) == 1);
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /** Método que guarda al usuario en la base de datos
     * @return bool
     */
    private function _create_new_user_in_db() : bool {
        $ban = false;
        $sqlquery = "INSERT INTO usuario(email, pword, tipo, nombre, apellidos, telefono, ext, foto) VALUES (?, PASSWORD(?), ?, ?, ?, ?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssssssss", [$this->tmp_data["email"], $this->tmp_data["password"], $this->tmp_data["tipo"], $this->tmp_data["nombre"], $this->tmp_data["apellidos"], $this->tmp_data["telefono"], $this->tmp_data["extension"], $this->tmp_data["foto"]])==1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _actualiza_datos_usuario() : bool {
        $ban = false;
        $sqlquery = "UPDATE usuario SET email = ?, tipo = ?, activo = ?, nombre = ?, apellidos = ?, telefono = ?, ext = ?, foto = ? WHERE id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ssisssssi", [$this->tmp_data["email"],$this->tmp_data["tipo"], $this->tmp_data["activo"], $this->tmp_data["nombre"], $this->tmp_data["apellidos"], $this->tmp_data["telefono"], $this->tmp_data["extension"], $this->tmp_data["foto"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que revisa en la base de datos si las credenciales del usuario son válidas
     * @param String $passw password del usuario
     * @return bool
     */
    private function _revisar_credenciales(String $passw) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) as auth FROM usuario WHERE usuario.email = ? and usuario.pword = password(?) and usuario.activo = TRUE";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ss", [$this->data["email"], $passw])[0]["auth"] == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que obtiene de la base de datos los datos del usuario, se utiliza el id del usuario como campo de búsqueda
     * @param int $id identificador único del usuario
     * @return bool
     */
    private function _get_usuario_by_id(int $id): bool
    {
        $ban = false;
        $sqlquery = "SELECT usuario.email as email, usuario.tipo as tipo, usuario.activo as activo, usuario.nombre as nombre, usuario.apellidos as apellidos, usuario.telefono as telefono, usuario.ext as extension, usuario.foto as foto FROM usuario WHERE usuario.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_assing_user_data($id, $res[0]["email"], $res[0]["tipo"], $res[0]["activo"], $res[0]["nombre"], $res[0]["apellidos"], $res[0]["telefono"], $res[0]["extension"], $res[0]["foto"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que obtiene de la base de datos los datos del usuario, se utiliza el correo del usuario como campo de búsqueda
     * @param String $email
     * @return bool
     */
    private function _get_usuario_by_email(String $email): bool
    {
        $ban = false;
        $sqlquery = "SELECT usuario.id as id, usuario.tipo as tipo, usuario.activo as activo, usuario.nombre as nombre, usuario.apellidos as apellidos, usuario.telefono as telefono, usuario.ext as extension, usuario.foto as foto FROM usuario WHERE email = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "s", [$email]);

            if (count($res) == 1) {
                $this->_assing_user_data($res[0]["id"], $email, $res[0]["tipo"], $res[0]["activo"], $res[0]["nombre"], $res[0]["apellidos"], $res[0]["telefono"], $res[0]["extension"], $res[0]["foto"]);
                $ban = true;
            }
        }
        catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que asigna valores a los atributos privados de la clase pertenecientes al usuario.
     * @param int $id identificador único del usuario
     * @param String $email correo electrónico del usuario
     * @param String $tipo tipo de usuario (se usa a la hora del login)
     * @param int $activo
     */
    private function _assing_user_data(int $id, String $email, String $tipo, int $activo, String $nombre, String $apellidos, ?String $telefono, ?String $extension, ?String $foto) {

        $this->data["id"] = $id;
        $this->data["email"] = $email;
        $this->data["tipo"] = $tipo;
        $this->data["activo"] = $activo == 1;
        $this->data["nombre"] = $nombre;
        $this->data["apellidos"] = $apellidos;
        $this->data["telefono"] = $telefono;
        $this->data["extension"] = $extension;
        $this->data["foto"] = $foto;
    }

    private function _get_all_usuarios(string $sqlquery) : array {
        $ids = array();
        $res = $this->SqlOp->exec($sqlquery);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    /**
     * Método que se encarga de realizar la selección entre creación y actualización del registro de un usario
     * @return bool
     * @throws UsuarioException
     */
    private function _save() : bool {
        $ban = false;
        if ($this->is_new) { // si es un nuevo usuario
            if (!$this->_create_new_user_in_db()) {
                throw new UsuarioException("No se ha podido guardar al usuario en la base de datos, verifique.", 1008);
            }
            else {
                $ban = true;
            }
        }
        else { // si se va a actualizar
            if (!$this->_actualiza_datos_usuario()) {
                throw new UsuarioException("No se ha podido actualizar al usuario, verifique los datos.", 1266);
            } else {
                $ban = true;
            }
        }

        return $ban;
    }

    /**
     * Método que construye una query de consulta de usuarios de acuerdo al array de filtros.
     * @param array $filtro diccionario con los filtros a aplicar.
     * @return String query de consulta
     */
    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT usuario.id FROM usuario";
        $allowed_keys = array("activo", "tipo");

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) {
                throw new UsuarioException("El formato de los filtros no es válido, verifica la documentacion", 1154);
            } else {
                $sqlquery .= " WHERE ";
                $tmpArray = array();
                foreach (array_keys($filtro) as $key) {
                    if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 1153);

                    switch ($key) {
                        case "activo":
                            $tmpArray[] = $this->_block_query_activo($filtro["activo"]);
                            break;
                        case "tipo":
                            $tmpArray[] = $this->_block_query($filtro["tipo"]);
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

    /**
     * @param $block
     * @return String
     * @throws LlaveDeBusquedaIncorrectaException
     */
    private function _block_query($block) : String {
        $allowed_vals = ["profesor", "director", "RRHH"];
        if (!is_array($block)) {
            if (!in_array($block, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $block no es correcto, verificalo", 1173);
            $strResult = "tipo = '$block'";
        } else {
            $tmpArray = array();
            foreach ($block as $item) {
                if (!in_array($item, $allowed_vals)) throw new LlaveDeBusquedaIncorrectaException("El valor $item no es correcto, verificalo", 1178);
                $tmpArray[] = "tipo = '$item'";
            }
            $strResult = "(" . implode(" OR ", $tmpArray) .")";
        }
        return $strResult;
    }

    /**
     * @param $block
     * @return String
     * @throws LlaveDeBusquedaIncorrectaException
     */
    private function _block_query_activo($block) : String {
        $strResult = "";
        if (!is_bool($block)) throw new LlaveDeBusquedaIncorrectaException("El valor " . strval($block) . " no es correcto, verificalo", 1168);

        $str_val = ($block) ? "TRUE" : "FALSE";
        return "activo = $str_val";
    }
}