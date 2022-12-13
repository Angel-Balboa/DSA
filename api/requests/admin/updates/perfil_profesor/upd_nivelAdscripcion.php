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
    $nuevoNivelAdscripcion = CValidadorDeEntradas::validarString($_POST["profesor_nivel_adscripcion"] ?? null, "Adscripción", 10, false, false, false);
    $newData = array("nivel_adscripcion" => $nuevoNivelAdscripcion);

    if ($admin->actualiza_datos_perfil_profesor($email_usuario, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha actualizado el Nivel de Adscripción del profesor con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", 1);
    }
} catch (ValoresDeCadenaNoValidosException | ProfesorException | ProfesorNoExisteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();