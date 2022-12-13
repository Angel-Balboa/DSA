<?php

include_once("../../../../../init.php");

use dsa\api\model\producto_cientifico\ProductoCientifico;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    CRequestsSesion::inits();

    $id_producto = CValidadorDeEntradas::validarEnteros($_GET["id_producto"] ?? null, "Id del producto Científico", false, false, false, false, false);

    $producto = ProductoCientifico::get_productoCientifico_by_id($id_producto);

    $json->agregaDatos($producto->get_data());
    $json->estableceExito(true);
}catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
