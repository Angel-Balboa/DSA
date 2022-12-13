<?php

include_once("../../../../../init.php");

use dsa\api\controller\usuario\UsuarioAdministrador;
use dsa\api\model\usuario\Exceptions\TipoDeUsuarioNoValidoException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$admin = new UsuarioAdministrador();
$json = new CConstructorJSON();

try {
    $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["usuario_id"] ?? null, "Id de usuario", true, false, false, false));
    if ($admin->cambia_tipo_de_usuario($usuario, CValidadorDeEntradas::validarString($_POST["usuario_tipo"] ?? null, "tipo de usuario"))) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado el tipo de usuario con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | UsuarioException | UsuarioNoExistenteException | TipoDeUsuarioNoValidoException | CConnexionException | SQLTransactionException | ValoresDeCadenaNoValidosException  $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();