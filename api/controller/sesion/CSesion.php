<?php

namespace dsa\api\controller\sesion;

use dsa\api\controller\sesion\Exceptions\SesionNoInizializadaException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Exceptions\ContrasenaNoValidaException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioNoPermitidoException;
use dsa\api\model\usuario\Usuario;

class CSesion
{
    private ?Sesion $sesion;

    private function __construct() {
        $this->sesion = null;
    }

    public static function getInstance() {
        return new CSesion();
    }

    public static function inits(bool $redirect=true, String $url2go='../../logout.php') {
        $tmpSesion = new CSesion();
        $tmpSesion->initInstance();

        if (!$tmpSesion->is_logged) {
            if ($redirect) header("Location: $url2go");
        }

        return $tmpSesion;
    }

    private function initInstance() {
        $this->sesion = Sesion::getInstance();
    }

    /**
     * Método que inicia la sesión del usuario, si éste existe y ha ingresado la contraseña correcta.
     * @param String $email
     * @param String $passw
     * @return bool
     * @throws ContrasenaNoValidaException
     * @throws UsuarioNoExistenteException
     * @throws UsuarioNoPermitidoException
     */
    public function autorizar_acceso_con_sesion(String $email, String $passw): bool
    {
        $ban = false;

        $usuario = Usuario::get_usuario_by_email($email);

        if ($usuario->autorizar_acceso($passw)) {
            $this->sesion = Sesion::getInstance();
            $this->sesion->id_usuario = $usuario->get_data("id");
            $this->sesion->nombre_usuario = $usuario->get_data(["nombre", "apellidos"]);
            $this->sesion->tipo_usuario = $usuario->get_data("tipo");
            $this->sesion->is_logged = true;

            if ($this->sesion->tipo_usuario == "profesor") {
                $profesor = Profesor::get_profesor_by_usuario($usuario);
                $this->sesion->id_profesor = $profesor->get_data("id");
                $this->sesion->nivel_adscripcion = $profesor->get_data("nivel_adscripcion");
            }

            $ban = true;
        }

        return $ban;
    }

    public function url2go() {
        $url = "logout.php";
        if ($this->sesion->is_logged) {

            switch ($this->sesion->tipo_usuario) {
                case 'admin':
                    $url = "v/admin/index.php";
                    break;
                case 'profesor':
                    $url = "v/profesor/index.php";
                    break;
                case 'RRHH':
                    $url = "v/rrhh/index.php";
                    break;
                case 'director':
                    $url = 'v/director/index.php';
                    break;
                default:
                    $url = 'logout.php';
                    break;
            }
        }

        return $url;
    }

    public function __get($name) {
        if (!is_null($this->sesion)) {
            return $this->sesion->$name;
        } else {
            throw new SesionNoInizializadaException("La sesión no ha sido inicializada", 21001);
        }
    }
}