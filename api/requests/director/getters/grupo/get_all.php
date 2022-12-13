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

    $carga = CargaAcademica::get_cargaAcademica_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_carga"] ?? null, "Id de carga Académica", false, false, false, false));

    $dataGrupo = array();
    foreach (Grupo::get_all(["carga_academica" => $carga]) as $id) {
        $dataGrupo[] = Grupo::get_grupo_by_id($id)->get_data();
    }
    $json->agregaDatos($dataGrupo);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();