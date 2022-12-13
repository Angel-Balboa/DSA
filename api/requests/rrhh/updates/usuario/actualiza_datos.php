<?php

include_once("../../../../../init.php");

use dsa\api\controller\rrhhs\CUsuario;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$admin = new CUsuario();
$json = new CConstructorJSON();
$newData = array();

try {

    if (isset($_POST["id_usuario"])) {
        $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_usuario"], "Id del Usuario", false, false, false, false));
    } elseif (isset($_POST["email_usuario"])) {
        $usuario = Usuario::get_usuario_by_email(CValidadorDeEntradas::validarEmail($_POST["email_usuario"], false));
    } else {
        throw new UsuarioException("Se debe proporcionar el Id o el email del usuario a actualizar", -81);
    }

    if (isset($_POST["nuevo_email_usuario"])) {
        $newData["email"] = CValidadorDeEntradas::validarEmail($_POST["nuevo_email_usuario"]);
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

    if ($admin->actualiza_datos_usuario($usuario, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos del Usuario con éxito");
    }
} catch (UsuarioException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();


