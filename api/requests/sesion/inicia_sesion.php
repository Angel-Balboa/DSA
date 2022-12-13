<?php

include_once("../../../init.php");

use dsa\api\controller\sesion\CSesion;
use dsa\api\model\usuario\Exceptions\ContrasenaNoValidaException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioNoPermitidoException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

$json = new CConstructorJSON();

try {
    $email_user = CValidadorDeEntradas::validarEmail($_POST["username"] ?? null);
    $passw = CValidadorDeEntradas::validarString($_POST["password"] ?? null, "Contraseña del Usuario");

    $sesion = CSesion::getInstance();

    if ($sesion->autorizar_acceso_con_sesion($email_user, $passw)) {
        $json->estableceExito(true);
        $json->agregaDatos(["url" => $sesion->url2go(), "s" => $_SESSION]);
    }
} catch (UsuarioNoExistenteException | ValoresDeCadenaNoValidosException | ContrasenaNoValidaException|UsuarioException|UsuarioNoPermitidoException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();