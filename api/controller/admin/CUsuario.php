<?php

namespace dsa\api\controller\admin;

use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\profesor\Exceptions\FechaDeContratoException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Exceptions\ProfesorYaExistenteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Exceptions\TipoDeUsuarioNoValidoException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioYaExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Carrera;
use dsa\lib\Utils\GeneradorAleatorio;


class CUsuario extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Método que genera el registro de un nuevo usuario y que genera el registro de perfil de profesor.
     * @param String $email
     * @param String $pw
     * @param String $nombre
     * @param String $apellidos
     * @param String|null $telefono
     * @param String|null $extencion
     * @param String|null $foto
     * @param Carrera $carrera
     * @param String $nivel_adscripcion
     * @param String $tipo_contrato
     * @param String $categoria
     * @param String|null $inicio_contrato
     * @param String|null $fin_contrato
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws FechaDeContratoException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     * @throws ProfesorYaExistenteException
     * @throws TipoDeUsuarioNoValidoException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     * @throws UsuarioYaExistenteException
     */
    public function crea_usuario_profesor(String $email, String $pw, String $nombre, String $apellidos, ?String $telefono, ?String $extencion, ?String $foto, Carrera $carrera, String $nivel_adscripcion, String $tipo_contrato, String $categoria, ?String $inicio_contrato, ?String $fin_contrato) : bool {
        $ban = false;
        try {
            $tmpProfesor = Profesor::get_profesor_by_email($email);
            unset($tmpProfesor);
            throw new ProfesorYaExistenteException("Ya existe un profesor con el correo: $email", 12001);
        } catch (ProfesorNoExisteException $e) {
            try {
                $tmpUsuario = Usuario::get_usuario_by_email($email);
                unset($tmpUsuario);
                throw new UsuarioYaExistenteException("Ya existe un usuario con el correo: $email", 12002);
            } catch (UsuarioNoExistenteException $e) {
                $tmpUsuario = Usuario::create_new_user($email, $pw, $nombre, $apellidos, $telefono, $extencion, $foto, "profesor", true, $this->Msql);
                $tmpProfesor = Profesor::crear_nuevo_profesor($tmpUsuario, $carrera, $nivel_adscripcion, $tipo_contrato, $categoria, is_null($inicio_contrato) ? "now" : $inicio_contrato, $fin_contrato);
                if ($tmpProfesor) $ban = true;
            }
        }

        return $ban;
    }

    public function crea_usuario_director(String $email, String $pw, String $nombre, String $apellidos, ?String $telefono, ?String $extencion, ?String $foto) : bool {
        $ban = false;

        try {
            $tmpUsuario = Usuario::get_usuario_by_email($email);
            unset($tmpUsuario);
            throw new UsuarioYaExistenteException("Ya existe un usuario con el correo: $email", 12002);
        } catch (UsuarioNoExistenteException $e) {
            $tmpUsuario = Usuario::create_new_user($email, $pw, $nombre, $apellidos, $telefono, $extencion, $foto, "director", true, $this->Msql);
        }

        return isset($tmpUsuario);
    }

    public function crea_usuario_RRHH(String $email, String $pw, String $nombre, String $apellidos, ?String $telefono, ?String $extencion, ?String $foto) : bool {
        $ban = false;

        try {
            $tmpUsuario = Usuario::get_usuario_by_email($email);
            unset($tmpUsuario);
            throw new UsuarioYaExistenteException("Ya existe un usuario con el correo: $email", 12002);
        } catch (UsuarioNoExistenteException $e) {
            $tmpUsuario = Usuario::create_new_user($email, $pw, $nombre, $apellidos, $telefono, $extencion, $foto, "RRHH", true, $this->Msql);
        }

        return isset($tmpUsuario);
    }

    /**
     * @param Usuario $usuario
     * @param Carrera $carrera_adscripcion
     * @param String $nivel_adscripcion
     * @param String $tipo_contrato
     * @param String $categoria
     * @param String|null $inicio_contrato
     * @param String|null $fin_contrato
     * @return bool
     * @throws ProfesorNoExisteException
     * @throws ProfesorYaExistenteException
     * @throws CarreraNoAgregadaException
     * @throws ProfesorNoAgregadoException
     * @throws FechaDeContratoException
     * @throws ProfesorException
     * @throws TipoDeUsuarioNoValidoException
     * @throws UsuarioException
     */
    public function crea_perfil_de_profesor_a_usuario(Usuario $usuario, Carrera $carrera_adscripcion, String $nivel_adscripcion, String $tipo_contrato, String $categoria, ?String $inicio_contrato, ?String $fin_contrato) : bool {
        $ban = false;

        try {
            $tmpProfesor = Profesor::get_profesor_by_email($usuario->get_data()["email"]);
            unset($tmpProfesor);
            throw new ProfesorYaExistenteException("Ya existe un perfil de profesor para el usuario $usuario", 12006);
        } catch (ProfesorNoExisteException $e) {
            $tmpProfesor = Profesor::crear_nuevo_profesor($usuario, $carrera_adscripcion, $nivel_adscripcion, $tipo_contrato, $categoria, is_null($inicio_contrato) ? "now" : $inicio_contrato, $fin_contrato, $this->Msql);
        }

        return isset($tmpProfesor);
    }

    public function activa_usuario(Usuario $usr) : bool {
        $ban = false;
        if (!$usr->activo()) {
            $usuario = Usuario::get_usuario_by_id($usr->get_data("id"), $this->Msql);
            $new_data = ["activo" => true];
            $ban = $usuario->actualiza_datos_de_usuario($new_data);
        }
        return $ban;
    }

    public function desactiva_usuario(Usuario $usr) : bool {
        $ban = false;
        if ($usr->activo()) {
            $usuario = Usuario::get_usuario_by_id($usr->get_data("id"), $this->Msql);
            $new_data = ["activo" => false];
            $ban = $usuario->actualiza_datos_de_usuario($new_data);
        }
        return $ban;
    }

    public function reestablece_contrasena_a_usuario(Usuario $usr, ?String $nueva_contrasena=null) : ?String {
        $new_pass = is_null($nueva_contrasena) ? GeneradorAleatorio::generarContrasenaAleatoria() : $nueva_contrasena;
        $usuario = Usuario::get_usuario_by_id($usr->get_data("id"), $this->Msql);

        if ($usuario->reestablece_contrasena($new_pass)) {
            return $new_pass;
        }

        return null;
    }

    public function cambia_tipo_de_usuario(Usuario $usuario, String $nuevo_tipo) : bool {
        $tmpUsuario = Usuario::get_usuario_by_id($usuario->get_data("id"), $this->Msql);
        $newData = ["tipo" => $nuevo_tipo];
        return $tmpUsuario->actualiza_datos_de_usuario($newData);
    }

    public function actualiza_datos_usuario(Usuario $usr, array $newData) : bool {
        $usuario = Usuario::get_usuario_by_id($usr->get_data("id"), $this->Msql);
        return $usuario->actualiza_datos_de_usuario($newData);
    }
}