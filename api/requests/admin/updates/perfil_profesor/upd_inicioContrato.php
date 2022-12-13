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
    $dt_nuevaFechaDecontrato = null;
    $email_usuario = CValidadorDeEntradas::validarEmail($_POST["usuario_email"] ?? null, false);
    $nuevaFechaDeContrato = CValidadorDeEntradas::validarString($_POST["profesor_inicio_contrato"] ?? null, "Fecha de inicio de contrato", 10, false, false, true);

    if (empty($nuevaFechaDeContrato)) {
        $dt_nuevaFechaDecontrato = new \DateTime();
    } else {
        $dt_nuevaFechaDecontrato = new \DateTime($nuevaFechaDeContrato);
    }

    $newData = array("inicio_contrato" => $dt_nuevaFechaDecontrato->format("Y/m/d"));

    if ($admin->actualiza_datos_perfil_profesor($email_usuario, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado la fecha de inicio de contrato del profesor con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (ValoresDeCadenaNoValidosException | ProfesorException | ProfesorNoExisteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

