<?php

include_once("../../../../../init.php");

use dsa\api\controller\usuario\UsuarioAdministrador;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

$admin = new UsuarioAdministrador();
$json = new CConstructorJSON();

try {
    $email_usuario = CValidadorDeEntradas::validarEmail($_POST["usuario_email"] ?? null, false);
    $nuevoTipoContrato = CValidadorDeEntradas::validarString($_POST["profesor_tipo_contrato"] ?? null, "Tipo de contrato", 5, false, false, false);
    $newData = array("tipo_contrato" => $nuevoTipoContrato);

    if ($admin->actualiza_datos_perfil_profesor($email_usuario, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado el Tipo de contrato del profesor con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (ValoresDeCadenaNoValidosException | ProfesorException | ProfesorNoExisteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();