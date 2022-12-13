<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try
{
    $CargaMaterias = array();
    // Variable para guardar los datos en un array
    $CargaMaterias=CargaAcademica::get_planDeEstudio_by_id2($_GET["id_profesor"]);
    $json->agregaDatos($CargaMaterias);
    $json->estableceExito(true);
}catch(PlanDeEstudioNoExistenteException | PlanDeEstudioException $e) {
    $json->agregaDatosError($e->getCode(), $e->getMessage());

}

$json->enviarJSON();