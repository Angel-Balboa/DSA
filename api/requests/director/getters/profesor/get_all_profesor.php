<?php

//include
include_once("../../../../../init.php");

//use o importaciones de clases importadas
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;

//Variable para el constuctor de JSON
$json = new CConstructorJSON();

//Inicio para obtener todos los prestamos
try
{
    //variable para guardar los datos
    $datosprofesor = array();

    foreach (Profesor::get_all() as $id)
    {
        $datosprofesor[] = Profesor::get_profesor_by_id($id)->get_data();
    }

    $json->agregaDatos($datosprofesor);
    $json->estableceExito(true);
}catch (ProfesorException | ProfesorNoExisteException $e)
{
    $json->agregaDatosError($e->getCode(), $e->getMessage());
}

$json->enviarJSON();
