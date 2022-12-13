<?php

//Include

include_once("../../../../../init.php");

//use o importaciones de clases que se ocupan
use dsa\api\model\prestamo\Exceptions\SolicitudNoEncontradaException;
use dsa\api\model\prestamo\SolicitudPrestamo;
use dsa\api\model\prestamo\Exceptions\SolicitudPrestamoException;
use dsa\lib\constructorJSON\CConstructorJSON;


//variable para el constructor de JSON
$json = new CConstructorJSON();

// * Inicio para obtener todos los prestamos

try
{
    //variable para guardar los datos
    $dataprestamos = array();

    foreach(SolicitudPrestamo::get_all() as $id)
    {
        $dataprestamos[] = SolicitudPrestamo::get_solicitudDePrestamo_by_id($id)->get_data();
    }

    $json->agregaDatos($dataprestamos);
    $json->estableceExito(true);
}catch(SolicitudPrestamoException | SolicitudNoEncontradaException $e)
{
    $json->agregaDatosError($e->getCode(), $e->getMessage());
}

$json->enviarJSON();