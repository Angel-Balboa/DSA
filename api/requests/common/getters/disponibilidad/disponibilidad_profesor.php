<?php

include_once("../../../../../init.php");

use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\disponibilidad\Disponibilidad;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$tmpFiltro = array();
$filtro = null;
$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del CProfesor"));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"], false));
    } else {
        throw new ProfesorException("No se ha podido obtener al profesor, se esperaba el Id o el correo del profesor", -103);
    }

    if (isset($_GET["dia"])) {
        $tmpFiltro["dia"] = intval($_GET["dia"]);
    }

    if (isset($_GET["hora"])) {
        $tmpFiltro["hora"] = intval($_GET["hora"]);
    }

    if (count($tmpFiltro) > 0) {
        $filtro = $tmpFiltro;
    }

    $disp = Disponibilidad::get_disponibilidad_by_profesor($profesor);
    $disp_data = count($tmpFiltro)>0 ? array($disp->get_data($filtro)) : $disp->get_data($filtro);

    $json->agregaDatos($disp_data);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
