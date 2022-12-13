<?php

include_once("../../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\planeacion_asesoria\PlaneacionAsesoria;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;


$json = new CConstructorJSON();

try {
    $idPlaneacionAcademica = CValidadorDeEntradas::validarEnteros($_GET["id_planeacion_academica"], "Id de la Planeacion Academica", false, false, false, false);

    $planeaionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id($idPlaneacionAcademica);

    $dataPlaneacionAsesoria = PlaneacionAsesoria::get_planeacionAsesoria_by_planeacionAcademica($planeaionAcademica)->get_data();

    $dataPlaneacionAsesoria["director"] = Usuario::get_usuario_by_id($dataPlaneacionAsesoria["id_director"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataPlaneacionAsesoria);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();