<?php

//include
include_once("../../../../../init.php");

//use o importaciones de clases ocupadas
use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaException;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//* Inicio de obtener todas las actividades academicas

try
{

    $dataactividadacademica = array();

    foreach (Actividad::get_all() as $id)
    {
        $dataactividadacademica[] = Actividad::get_actividad_academica_by_id($id)->get_data();
    }

    $json->agregaDatos($dataactividadacademica);
    $json->estableceExito(true);
}catch(ActividadAcademicaException | ActividadAcademicaNoExistenteException $e)
{
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();