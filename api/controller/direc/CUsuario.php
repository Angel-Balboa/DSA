<?php

namespace dsa\api\controller\direc;

use dsa\api\model\usuario\Usuario;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;

class CUsuario extends Director
{
    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
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
}