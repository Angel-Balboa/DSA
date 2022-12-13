<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CUsuario;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
$newData = array();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CUsuario(Usuario::get_usuario_by_id($sesion->id_usuario));
    if (isset($_POST["id_usuario"])) {
        $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_usuario"], "Id del usuario", false, false, false, false));
    } elseif (isset($_POST["email_usuario"])) {
        $usuario = Usuario::get_usuario_by_email(CValidadorDeEntradas::validarEmail($_POST["email_usuario"]));
    } else {
        throw new UsuarioException("Se debe proporcionar el Id o el email del usuario a actualizar", -81);
    }

    if (isset($_POST["nuevo_email_usuario"])) {
        $newData["email"] = CValidadorDeEntradas::validarEmail($_POST["nuevo_email_usuario"]);
    }

    if (isset($_POST["nuevo_tipo_usuario"])) {
        $newData["tipo"] = CValidadorDeEntradas::validarOpciones($_POST["nuevo_tipo_usuario"], ["profesor", "director", "RRHH"]);
    }

    if (isset($_POST["nuevo_status_usuario"])) {
        $newData["activo"] = $_POST["nuevo_status_usuario"] == "true";
    }

    if (isset($_POST["nuevo_nombre_usuario"])) {
        $newData["nombre"] = CValidadorDeEntradas::validarString($_POST["nuevo_nombre_usuario"], "Nombre del usuario", 150);
    }

    if (isset($_POST["nuevo_apellidos_usuario"])) {
        $newData["apellidos"] = CValidadorDeEntradas::validarString($_POST["nuevo_apellidos_usuario"], "Apellidos del usuario", 150);
    }

    if (isset($_POST["nuevo_telefono_usuario"])) {
        $newData["telefono"] = CValidadorDeEntradas::validarString($_POST["nuevo_telefono_usuario"], "Teléfono del usuario", 10, false, false, false);
    }

    if (isset($_POST["nueva_extension_usuario"])) {
        $newData["extension"] = CValidadorDeEntradas::validarString($_POST["nueva_extension_usuario"], "Extensión Institucional del Usuario", 4, false, false, false);
    }

    if (isset($_POST["nueva_foto_usuario"])) {
        $newData["foto"] = CValidadorDeEntradas::validarString($_POST["nueva_foto_usuario"], "Imagen de Perfil del usuario", 250, false, false, false);
    }

    if ($admin->actualiza_datos_usuario($usuario, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos del Usuario con éxito");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
