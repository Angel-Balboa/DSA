<?php

include_once("../../../../../init.php");

use dsa\api\model\grupo\Grupo;
use dsa\api\model\grupo\Exceptions\GrupoException;
use dsa\api\model\grupo\Exceptions\GrupoNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try {
    $dataGrupo = array();
    foreach (Grupo::get_all() as $id) {
        $dataGrupo[] = Grupo::get_grupo_by_id($id)->get_data();
    }
    $json->agregaDatos($dataGrupo);
    $json->estableceExito(true);
} catch (GrupoNoExistenteException | GrupoException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();