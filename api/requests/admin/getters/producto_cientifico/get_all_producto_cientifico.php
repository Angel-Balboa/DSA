<?php

//include
include_once("../../../../../init.php");

//use o importaciones de clases que se ocupan
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoException;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoNoExisteException;
use dsa\api\model\producto_cientifico\ProductoCientifico;


//variable para el constructor de JSON
$json = new CConstructorJSON();

// Inicio para obtener todos los prestamos
try {
    //variable para guardar los datos
    $dataprodutocientifico = array();
    foreach(ProductoCientifico::get_all() as $id)
    {
        $dataprodutocientifico[]=ProductoCientifico::get_productoCientifico_by_id($id)->get_data();
    }

    //print_r(ProductoCientifico::get_all());

    $json->agregaDatos($dataprodutocientifico);
    $json->estableceExito(true);

} catch(ProductoCientificoException | ProductoCientificoNoExisteException | LlaveDeBusquedaIncorrectaException $e)
{
    $json->agregaDatosError($e->getMessage(),$e->getCode());
}

$json->enviarJSON();