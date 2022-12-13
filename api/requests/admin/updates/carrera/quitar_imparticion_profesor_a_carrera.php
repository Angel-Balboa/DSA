<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CCarrera;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CCarrera(Usuario::get_usuario_by_id($sesion->id_usuario));
    if (isset($_GET["id_carrera"])) {
        $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_carrera"], "Id de la carrera", false, false, false, false));
    } elseif (isset($_GET["clave_carrera"])) {
        $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_GET["clave_carrera"], "Clave de la carrera", 10, false, false, false));
    } else {
        throw new GeneralException("Se esperaba la clave o el identificador de la carrera", -12);
    }

    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false, false));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"]));
    } else {
        throw new GeneralException("Se esperaba el Id del profesor o el correo del profesor", -13);
    }

    if ($admin->quita_profesor_de_imparticion($carrera, $profesor)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha quitado el profesor $profesor para impartir en la carrera $carrera");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

