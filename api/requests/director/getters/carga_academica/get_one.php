<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    $carga = CargaAcademica::get_cargaAcademica_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_carga"] ?? null, "Id de Carga Académica", false, false, false, false));

    $dataCarga = $carga->get_data();
    $dataCarga["plan"] = PlanDeEstudio::get_planDeEstudio_by_id($dataCarga["id_plan_estudios"])->get_data(["id", "clave", "nombre"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataCarga);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();