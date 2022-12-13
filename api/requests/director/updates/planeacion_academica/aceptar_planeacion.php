<?php

include_once("../../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
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

    $planeacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"] ?? null, "Id de Planeacion Académica", false, false, false, false));

    $usuario = Usuario::get_usuario_by_id($sesion->id_usuario);
    $admin = new CPlaneacionAcademica($usuario);

    $admin->aceptar_planeacion($planeacion);
    $json->estableceExito(true);
    $json->agregaMensajeDeExito("Se ha aceptado con éxito la planeación");

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
