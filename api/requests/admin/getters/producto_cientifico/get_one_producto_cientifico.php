<?php

//Include
include_once("../../../../../init.php");

//use o importaciones de clases que se ocupan
use dsa\api\model\producto_cientifico\ProductoCientifico;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoException;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoNoExisteException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

//variable para el constructor de JSON
$json = new CConstructorJSON();

try
{
    $clvproductocientifico = CValidadorDeEntradas::validarString($_GET["clv_productocientifico"] ?? null, "Clave Producto Cientifico", 25, false, false);
    $dataprodutocientifico = ProductoCientifico::get_productoCientifico_by_id($clvproductocientifico)->get_data();
    $json->estableceExito(true);
    $json->agregaDatos($dataprodutocientifico);

} catch (ProductoCientificoNoExisteException | ProductoCientificoException | LlaveDeBusquedaIncorrectaException | ValoresDeCadenaNoValidosException $e) {
    $json->agregaDatosError($e->getMessage(),$e->getCode());
}

$json->enviarJSON();