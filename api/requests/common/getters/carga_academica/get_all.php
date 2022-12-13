<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();
$filtro = null;

try {

    if (isset($_GET["clv_plan"])) {
        $filtro["plan_estudios"] = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_GET["clv_plan"], "clave del Plan de Estudios", 50, false, false, false));
    } elseif (isset($_GET["id_plan"])) {
        $filtro["plan_estudios"] = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_plan"], "Id del Plan de Estudios", false, false, false, false));
    }

    if (isset($_GET["anio_plan"])) {
        $filtro["anio"] = CValidadorDeEntradas::validarEnteros($_GET["anio_plan"], "Año de registro de la carga", false, false, false, false);
    }

    if (isset($_GET["periodo_plan"])) {
        $filtro["periodo"] = CValidadorDeEntradas::validarEnteros($_GET["periodo_plan"], "Periodo de la carga académica", false, false, false, false);
    }

    $dataCargaAcademica = array();
    foreach (CargaAcademica::get_all($filtro) as $id) {
        $dataCargaAcademica[] = CargaAcademica::get_cargaAcademica_by_id($id)->get_data();
    }
    $json->agregaDatos($dataCargaAcademica);
    $json->estableceExito(true);
} catch (CargaAcademicaNoExistenteException | CargaAcademicaException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();