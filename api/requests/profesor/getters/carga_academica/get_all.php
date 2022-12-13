<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaException;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try {
    $dataCargaAcademica = array();
    foreach (CargaAcademica::get_all() as $id) {
        $dataCargaAcademica[] = CargaAcademica::get_cargaAcademica_by_id($id)->get_data();
    }
    $json->agregaDatos($dataCargaAcademica);
    $json->estableceExito(true);
} catch (CargaAcademicaNoExistenteException | CargaAcademicaException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();