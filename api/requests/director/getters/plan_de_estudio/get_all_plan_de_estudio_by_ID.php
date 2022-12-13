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
    // Variable para guardar los datos en un array
    $dataplandeestudio = array();

    foreach (PlanDeEstudio::get_all() as $id)
    {
        $dataplandeestudio[] = PlanDeEstudio::get_planDeEstudio_by_id($id)->get_data();

    }

    $json->agregaDatos($dataplandeestudio);
    $json->estableceExito(true);
}catch(PlanDeEstudioNoExistenteException | PlanDeEstudioException $e)
{
    $json->agregaDatosError($e->getCode(), $e->getMessage());
}

$json->enviarJSON();