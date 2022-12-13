<?php
include_once("../../../../../init.php");

use dsa\api\controller\usuario\UsuarioAdministrador;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$admin = new UsuarioAdministrador();
$json = new CConstructorJSON();

try {
    $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["usuario_id"] ?? null, "Id de usuario", true, false, false, false));
    if ($admin->cambia_estado_activo_de_usuario($usuario)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha modificado el estatus de activo del usuario");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (UsuarioException | UsuarioNoExistenteException | ValorNoNumericoException | ValoresEnterosNoValidosException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
