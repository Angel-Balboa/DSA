<?php

include_once("../../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try {
    $dataPlaneacionAcademica = array();
    foreach (PlaneacionAcademica::get_all() as $id) {
        $dataPlaneacionAcademica[] = planeacion_academica\PlaneacionAcademica::get_PlaneacionAcademica_by_id($id)->get_data();
    }
    $json->agregaDatos($dataPlaneacionAcademica);
    $json->estableceExito(true);
} catch (PlaneacionAcademicaNoExistenteException | PlaneacionAcademicaException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();