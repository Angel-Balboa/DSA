<?php

include_once("../../../../../init.php");

use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\profesor\Profesor;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\controller\profesor\CProductoCientifico;

$json = new CConstructorJSON();

try {
    CRequestsSesion::inits();

    if (isset($_POST["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"], "Id del Profesor", false, false, false, false));
        unset($_POST["id_profesor"]);
    } elseif (isset($_POST["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_POST["email_profesor"]));
        unset($_POST["email_profesor"]);
    } else {
        throw new ProfesorException("Se esperaba el Id o el Email del profesor", -5000);
    }

    $admin = new CProductoCientifico($profesor);

    $json->estableceExito($admin->crea_producto_cientifico($_POST));

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
