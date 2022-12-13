<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Exceptions\GeneralException;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    if (isset($_GET["id_plan"])) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_plan"], "Id del plan de estudios", false, false, false, false));
    } elseif ($_GET["clv_plan"]) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_GET["clv_plan"], "Clave del plan de estudios", 50, false, false, false));
    } else {
        throw new PlanDeEstudioException("Se esperaba el Id o la Clave del plan de Estudios", -7000);
    }

    $datosPlan = $plan->get_data();
    $datosPlan["carrera"] = Carrera::get_carrera_by_id($datosPlan["id_carrera"])->get_data();
    unset($datosPlan["id_carrera"]);

    $json->agregaDatos($datosPlan);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
