<?php

include_once("../../../../../init.php");

use dsa\api\model\usuario\Usuario;
use dsa\api\controller\direc\CPlaneacionAcademica;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Utils\DateUtils;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    $periodo = CValidadorDeEntradas::validarOpciones($_POST["periodo"] ?? null, [1, 2, 3]);
    $anio = CValidadorDeEntradas::validarOpciones($_POST["anio"] ?? null, range(DateUtils::current_year() - 2, DateUtils::current_year() + 1));

    $usuario = Usuario::get_usuario_by_id($sesion->id_usuario);
    $admin = new CPlaneacionAcademica($usuario);

    $admin->solicitar_planeacion_academica_PTCs($periodo, $anio);
    $json->estableceExito(true);
    $json->agregaMensajeDeExito("Se han solicitado las planeaciones con éxito");

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();