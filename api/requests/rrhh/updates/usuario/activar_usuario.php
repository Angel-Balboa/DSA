<?php

include_once("../../../../../init.php");

use dsa\api\controller\rrhhs\CUsuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\model\usuario\Usuario;

$admin = new CUsuario();
$json = new CConstructorJSON();

try {
    if (isset($_GET["id_usuario"])) {
        $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_usuario"], "Id del Usuario", false, false, false, false));
    } elseif (isset($_GET["email_usuario"])) {
        $usuario = Usuario::get_usuario_by_email(CValidadorDeEntradas::validarEmail($_GET["email_usuario"], false));
    } else {
        throw new GeneralException("Se esperaba el Id o el Email del usuario", -30);
    }

    if ($admin->activa_usuario($usuario)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha activado al usuario: $usuario");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
