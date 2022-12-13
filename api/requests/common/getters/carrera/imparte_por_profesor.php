<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Exceptions\GeneralException;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false, false));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"]));
    } else {
        throw new ProfesorException("Se esperaba el Id o el Email del profesor", -6007);
    }
    $imparteEn = array();

    foreach ($profesor->get_carreras_de_imparticion() as $id) {
        $imparteEn[] = Carrera::get_carrera_by_id($id)->get_data();
    }

    $json->agregaDatos($imparteEn);
    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
