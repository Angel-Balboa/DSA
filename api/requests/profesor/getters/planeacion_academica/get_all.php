<?php

include_once("../../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\Exceptions\GeneralException;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
$filtroAnio = array();
$filtro = array();

try {
    // $sesion = CRequestsSesion::inits();

    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false,false));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"], false));
    } else {
        throw new ProfesorException("Se esperaba el Id o el Email del profesor", -150);
    }

    if (isset($_GET["anio"])) {
        $filtroAnio["anio"] = intval(CValidadorDeEntradas::validarEnteros($_GET["anio"], "Año de la Planeación académica", false, false, false, false));
    } else { // si no se define un año en específico se solicitan los últimos 3 años
        $filtroAnio["anio"] = array();
        $actualYear = intval(date("Y"));
        for($i=$actualYear ; $i > ($actualYear - 3); $i--) {
            $filtroAnio["anio"][] = $i;
        }
    }

    if (isset($_GET["estado"])) {
        $filtro["estado"] = CValidadorDeEntradas::validarOpciones($_GET["estado"], ["iniciada", "edicion", "enviada", "retornada", "finalizada"]);
    }

    if (isset($_GET["periodo"])) {
        $filtro["periodo"] = CValidadorDeEntradas::validarEnteros($_GET["periodo"], "Periodo de la Planeación Académica", false, false, false, false);
    }

    $filtro["profesor"] = $profesor;

    $planeaciones = array();
    foreach ($filtroAnio["anio"] as $anio) {
        $filtro["anio"] = $anio;
        $planXAnio = PlaneacionAcademica::get_all($filtro);

        foreach ($planXAnio as $planeacion) {
            $planeaciones[$anio][] = PlaneacionAcademica::get_PlaneacionAcademica_by_id($planeacion)->get_data();
        }
    }

    $json->agregaDatos($planeaciones);
    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
