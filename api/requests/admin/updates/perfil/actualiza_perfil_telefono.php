<?php

include_once("../../../../../init.php");

use dsa\api\controller\usuario\UsuarioAdministrador;
use dsa\api\model\perfil\Exceptions\PerfilException;
use dsa\api\model\perfil\Exceptions\PerfilNoExisteException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\usuario\Usuario;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$admin = new UsuarioAdministrador();
$json = new CConstructorJSON();

try {
    $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["usuario_id"] ?? null, "Id de usuario", true, false, false, false));
    $telefono = CValidadorDeEntradas::validarString($_POST["perfil_telefono"] ?? "", "Teléfono Celular", 10, false, false, true);
    $new_data = array("telefono" => empty($telefono) ? "NULL" : $telefono);
    if ($admin->actualiza_perfil_usuario($usuario, $new_data)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado el teléfono celular con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (UsuarioException | UsuarioNoExistenteException | ValoresDeCadenaNoValidosException | PerfilException | PerfilNoExisteException | ValorNoNumericoException | ValoresEnterosNoValidosException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
