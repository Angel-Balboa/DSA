<?php

include_once("../../../../../init.php");


use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\api\controller\profesor\CDisponibilidad;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {

    $sesion = CRequestsSesion::inits();

    if (isset($_POST["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"], "Id del CProfesor"));
    } elseif (isset($_POST["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_POST["email_profesor"], false));
    } else {
        throw new ProfesorException("No se ha podido obtener al profesor, se esperaba el Id o el correo del profesor", -103);
    }

    $dia = intval(CValidadorDeEntradas::validarEnteros($_POST["dia"] ?? null, "Día a asignar", false, false, false, true));
    $hora = intval(CValidadorDeEntradas::validarEnteros($_POST["hora"] ?? null, "Hora a asignar", false, false, false, true));

    $cdisp = new CDisponibilidad($profesor);

    if ($cdisp->cambia_disponbilidad($dia, $hora)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha cambiado la disponbilidad con éxito");
    } else {
        $json->agregaDatosError("Error desconocido", -1000);
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
