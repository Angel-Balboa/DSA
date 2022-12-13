<?php

//include
include_once("../../../../../init.php");

//use o importaciones de clases ocupadas
use dsa\api\model\actividad_investigacion\ActividadInvestigacion;
use dsa\api\model\actividad_investigacion\Exceptions\ActividadInvestigacionException;
use dsa\api\model\actividad_investigacion\Exceptions\ActividadInvestigacionNoExisteException;
use dsa\lib\constructorJSON\CConstructorJSON;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//* Inicio de obtener todas las actividades de investigacion

try
{
    $dataactividad_investigacion = array();

    foreach (ActividadInvestigacion::get_all() as $id)
    {
        $dataactividad_investigacion[] = ActividadInvestigacion::get_actividadInvestigacion_by_id()->get_data();
    }

    $json->agregaDatos($dataactividad_investigacion);
    $json->estableceExito(true);
}catch (ActividadInvestigacionException | ActividadInvestigacionNoExisteException $e)
{
    $json->agregaDatosError($e->getMessage(),$e->getCode());
}

$json->enviarJSON();