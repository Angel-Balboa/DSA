<?php

include_once("../../../../../init.php");

use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DateUtils;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    $tmpFiltro = array();

    if (isset($_GET["id_plan"])) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_plan"], "Id del Plan de Estudios", false, false, false, false));

        $tmpFiltro["plan_estudios"] = $plan;
    } elseif (isset($_GET["clv_plan"])) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_GET["clv_plan"], "Clave del Plan de Estudios", 50, false, false, false));
        $tmpFiltro["plan_estudios"] = $plan;
    } else {
        throw new PlanDeEstudioException("Se esperaba el Id o la Clave del Plan de Estudios", -6050);
    }

    if (isset($_GET["periodo"])) {
        $tmpFiltro["periodo"] = CValidadorDeEntradas::validarOpciones($_GET["periodo"], [1, 2, 3]);
    }

    if (isset($_GET["anio"])) {
        $tmpFiltro["anio"] = CValidadorDeEntradas::validarOpciones($_GET["anio"], range(2010, DateUtils::current_year()+1));
    } else {
        $tmpFiltro["anio"] = range(DateUtils::current_year()-2, DateUtils::current_year()+1);
    }

    $dataCargas = array();
    foreach(CargaAcademica::get_all($tmpFiltro) as $id) {
        $tmpDataCarga = CargaAcademica::get_cargaAcademica_by_id($id)->get_data();
        unset($tmpDataCarga["id_plan_estudios"]);
        $dataCargas[$tmpDataCarga["anio"]][] = $tmpDataCarga;
    }

    $json->agregaDatos($dataCargas);
    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
