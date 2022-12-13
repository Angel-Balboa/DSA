<?php

include_once("../../../../../init.php");

use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\carrera\Carrera;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();
$filtros = array();
try {
    $dataPlanesEstudios = array();

    if (isset($_GET["clv_carrera"])) {
        $filtros["carrera"] = Carrera::get_carrera_by_clave($_GET["clv_carrera"]);
    }

    if (isset($_GET["id_carrera"]) && is_numeric($_GET["id_carrera"])) {
        $filtros["carrera"] = $_GET["id_carrera"];
    }

    if (isset($_GET["anio_plan"]) && is_numeric($_GET["anio_plan"])) {
        $filtros["anio"] = $_GET["anio_plan"];
    }

    if (isset($_GET["nivel_plan"])) {
        $filtros["nivel"] = $_GET["nivel_plan"];
    }

    if (count($filtros) > 0) {
        $planesEstudio = PlanDeEstudio::get_all($filtros);
    } else {
        $planesEstudio = PlanDeEstudio::get_all();
    }

    foreach($planesEstudio as $id) {
        $dataPlanesEstudios[] = PlanDeEstudio::get_planDeEstudio_by_id($id)->get_data();
    }
    $json->agregaDatos($dataPlanesEstudios);
    $json->estableceExito(true);
} catch (PlanDeEstudioNoExistenteException|PlanDeEstudioException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
