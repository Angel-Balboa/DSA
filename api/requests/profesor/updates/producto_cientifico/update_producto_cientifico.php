<?php

include_once ("../../../../../init.php");

use dsa\api\model\producto_cientifico\ProductoCientifico;
use dsa\api\controller\profesor\CProductoCientifico;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {
    CRequestsSesion::inits();

    if (isset($_POST["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"], "Id del Profesor", false, false, false, false));
        unset($_POST["id_profesor"]);
    } elseif (isset($_POST["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_POST["email_profesor"], false));
        unset($_POST["email_profesor"]);
    } else {
        throw new ProfesorException("Se esperaba el Id o el Email del profesor", -6000);
    }


    $producto = ProductoCientifico::get_productoCientifico_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_producto"] ?? null, "Id del Producto cientifico", false, false, false, false));

    if (isset($_POST["id_profesor"])) unset($_POST["id_profesor"]);
    if (isset($_POST["email_profesor"])) unset($_POST["email_profesor"]);
    if (isset($_POST["id_producto"])) unset($_POST["id_producto"]);

    $admin = new CProductoCientifico($profesor);

    $json->estableceExito($admin->actualiza_producto_cientifico($producto, $_POST));

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();