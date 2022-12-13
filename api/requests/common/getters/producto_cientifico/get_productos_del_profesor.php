<?php

include_once("../../../../../init.php");

use dsa\api\model\producto_cientifico\ProductoCientifico;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\coautor\CoAutor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
try {
    CRequestsSesion::inits();

    if (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"] ?? null, false));
    } elseif (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false, false));
    } else {
        throw new GeneralException("No se ha detectado un selector para el profesor", -30);
    }
    $tmpCoAutor = CoAutor::CoAutor_by_profesor($profesor);
    $productos = array();
    $array_tipos = ["Article", "Book", "Booklet", "Conference", "InBook", "InCollection", "InProceedings", "Manual", "MasterThesis", "Misc", "PhdThesis", "Proceedings", "TechReport", "Unpublished"];

    foreach ($array_tipos as $tipo) {
        $productos[$tipo] = array();
    }

    foreach ($tmpCoAutor->get_productos() as $producto) {
        $tmpDataProducto = ProductoCientifico::get_productoCientifico_by_id($producto["id_producto"])->get_data("entries");
        $productos[$tmpDataProducto["_type"]][] = array("id" => $producto["id_producto"], "posicion" => $producto["posicion"], "entries" => $tmpDataProducto);
    }

    $json->estableceExito(true);
    $json->agregaDatos($productos);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
