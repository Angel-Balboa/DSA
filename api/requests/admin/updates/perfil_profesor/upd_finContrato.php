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
    $str_nuevaFechaDecontrato = null;
    $email_usuario = CValidadorDeEntradas::validarEmail($_POST["usuario_email"] ?? null, false);
    $nuevaFechaDeContrato = CValidadorDeEntradas::validarString($_POST["profesor_fin_contrato"] ?? null, "Fecha de inicio de contrato", 10, false, true, true);

    if (empty($nuevaFechaDeContrato)) {
        $str_nuevaFechaDecontrato = "indf";
    } else {
        $dt_nuevaFechaDecontrato = new DateTime($nuevaFechaDeContrato);
        $str_nuevaFechaDecontrato = $dt_nuevaFechaDecontrato->format("Y/m/d");
    }

    $newData = array("fin_contrato" => $str_nuevaFechaDecontrato);

    if ($admin->actualiza_datos_perfil_profesor($email_usuario, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado la fecha de fin de contrato del profesor con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (ValoresDeCadenaNoValidosException | ProfesorException | ProfesorNoExisteException | Exception $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();


