<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\grupo\Grupo;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $dataGrupo = array();
    foreach (Grupo::get_all(2) as $id) {
        $dataGrupo[] = Grupo::get_grupo_by_id($id)->get_data(["id", "clave"]);
    }
    $json->agregaDatos($dataGrupo);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();