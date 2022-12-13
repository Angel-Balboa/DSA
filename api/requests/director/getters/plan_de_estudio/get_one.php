<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {
    $clvPlan = CValidadorDeEntradas::validarString($_GET["clv_plan"] ?? null, "Clave del Plan de Estudios", 50, false, false, false);
    $dataPlan = PlanDeEstudio::get_planDeEstudio_by_clave($clvPlan)->get_data();
    $tmpCarrera = Carrera::get_carrera_by_id($dataPlan["id_carrera"]);

    unset($dataPlan["id_carrera"]);

    $dataPlan["carrera"] = $tmpCarrera->get_data(["id", "clave", "nombre"]);

    $json->agregaDatos($dataPlan);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
