<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\direc\CSolicitudPrestamo;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    if (isset($_POST["clv_carrera"])) {
        $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_POST["clv_carrera"], "Clave de la carrera", 10,false,false, false));
    } elseif (isset($_POST["id_carrera"])) {
        $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_carrera"], "Id de la carrera", false,false,false,false));
    } else {
        throw new CarreraException("Se esperaba el Id o la clave de la carrera", -6005);
    }

    $carreraObjetivo = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_POST["carrera_objetivo"] ?? null, "Clave de la carrera Objetivo", 10, false, false, false));

    $profesorObjetivo = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["profesor_objetivo"] ?? null, "Id del Profesor objetivo", false, false, false, false));

    $admin = new CSolicitudPrestamo(Usuario::get_usuario_by_id($sesion->id_usuario));

    $admin->crea_solicitud_prestamo($carreraObjetivo, $profesorObjetivo);

    $json->estableceExito(true);
    $json->agregaMensajeDeExito("Se ha agregado con éxito la solicitud de prestamo");
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();