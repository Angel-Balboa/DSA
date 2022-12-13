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
    $extension = CValidadorDeEntradas::validarString($_POST["perfil_extension"] ?? "", "Extensión Institucional", 4, false, false, true);
    $new_data = array("extencion" => empty($extension) ? "NULL" : $extension);
    if ($admin->actualiza_perfil_usuario($usuario, $new_data)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado la extension institucional con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (UsuarioException | UsuarioNoExistenteException | ValoresDeCadenaNoValidosException | PerfilException | PerfilNoExisteException | ValorNoNumericoException | ValoresEnterosNoValidosException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

