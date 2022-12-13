<?php

namespace dsa\api\controller\rrhhs;

use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\profesor\Exceptions\FechaDeContratoException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Exceptions\ProfesorYaExistenteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\usuario\Exceptions\TipoDeUsuarioNoValidoException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioYaExistenteException;
use dsa\api\model\usuario\Usuario;

class CUsuario extends RecursoHumano
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
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
     * @throws ProfesorNoExisteException
     * @throws ProfesorYaExistenteException
     * @throws UsuarioYaExistenteException
     * @throws CarreraNoAgregadaException
     * @throws ProfesorNoAgregadoException
     * @throws FechaDeContratoException
     * @throws ProfesorException
     * @throws TipoDeUsuarioNoValidoException
     * @throws UsuarioException
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

    /**
     * @param Usuario $usr
     * @return bool
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    public function activa_usuario(Usuario $usr) : bool {
        $ban = false;
        if (!$usr->activo()) {
            $usr_data = $usr->get_data(["id", "tipo"]);
            if ($usr_data["tipo"] != "profesor") throw new UsuarioException("No es posible activar a un usuario no profesor", -31);

            $usuario = Usuario::get_usuario_by_id($usr_data["id"], $this->Msql);
            $new_data = ["activo" => true];
            $ban = $usuario->actualiza_datos_de_usuario($new_data);
        }
        return $ban;
    }

    /**
     * @param Usuario $usr
     * @return bool
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    public function desactiva_usuario(Usuario $usr) : bool {
        $ban = false;
        if ($usr->activo()) {
            $usr_data = $usr->get_data(["id", "tipo"]);
            if ($usr_data["tipo"] != "profesor") throw new UsuarioException("No es posible desactivar a un usuario no profesor", -31);

            $usuario = Usuario::get_usuario_by_id($usr_data["id"], $this->Msql);
            $new_data = ["activo" => false];
            $ban = $usuario->actualiza_datos_de_usuario($new_data);
        }
        return $ban;
    }

    /**
     * @param Usuario $usr
     * @param array $newData
     * @return bool
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    public function actualiza_datos_usuario(Usuario $usr, array $newData) : bool {
        $usuario = Usuario::get_usuario_by_id($usr->get_data("id"), $this->Msql);
        return $usuario->actualiza_datos_de_usuario($newData);
    }
}