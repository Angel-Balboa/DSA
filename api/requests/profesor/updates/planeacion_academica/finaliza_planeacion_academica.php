<?php

include_once ("../../../../../init.php");

use dsa\api\controller\profesor\CActividadAcademica;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    CRequestsSesion::inits();

    $planeacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"] ?? null, "Id de la Planeacion Académica", false, false, false, false));

    $admin = new CActividadAcademica(Profesor::get_profesor_by_id($planeacion->get_data("id_profesor")));

    if ($admin->finaliza_planeacion_academica($planeacion)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha finalizado con éxito la Planeación Académica");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
