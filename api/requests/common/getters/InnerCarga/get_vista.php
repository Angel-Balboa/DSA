<?php

//Include
include_once("../../../../../init.php");

//use o importaciones de clases que se ocupan
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;

//variable para el contructor de JSON
$json = new CConstructorJSON();

//* Inicio de obtener todos los planes de estudio
try
{
    $CargaMaterias = array();
    // Variable para guardar los datos en un array
    $CargaMaterias=PlanDeEstudio::get_planDeEstudio_by_id2($_GET["id_carrera"]);
    $json->agregaDatos($CargaMaterias);
    $json->estableceExito(true);
}catch(PlanDeEstudioNoExistenteException | PlanDeEstudioException $e)
{
    $json->agregaDatosError($e->getCode(), $e->getMessage());
}

$json->enviarJSON();