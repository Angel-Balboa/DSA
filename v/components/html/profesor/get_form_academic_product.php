<?php

include_once ("../../../../init.php");

use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\FormsAcademicProduct;
use dsa\api\model\producto_cientifico\ProductoCientifico;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;


$array_tipos = ["Article", "Book", "Booklet", "Conference", "InBook", "InCollection", "InProceedings", "Manual", "MasterThesis", "Misc", "PhdThesis", "Proceedings", "TechReport", "Unpublished"];

if (isset($_GET["id_producto"])) {
    try {
        $producto = ProductoCientifico::get_productoCientifico_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_producto"], "Id del Producto Cientifico", false, false, false, false));
        $dataProd = $producto->get_data();
        $form_product = new FormsAcademicProduct($dataProd["entries"]["_type"]);
        $form_product->get_form($dataProd);
    } catch (GeneralException $e) {
        $form_product = new FormsAcademicProduct("Misc");
        $form_product->get_form();
    }
} else {
    $index = $_GET["index"] ?? 9;
    if ($index < 0 || $index > count($array_tipos)-1 ) {
        $form_product = new FormsAcademicProduct("Misc");
    } else {
        $form_product = new FormsAcademicProduct($array_tipos[intval($index)]);
    }
    $form_product->get_form();
}






