<?php

include_once("../../../../../init.php");

use dsa\api\model\materia\Materia;
use dsa\api\model\materia_en_grupo\MateriaEnGrupo;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoException;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoNoExisteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try {
    $dataMateriaEnGrupo = array();
    foreach (MateriaEnGrupo::get_all_by_profesor($_GET['id_profesor']) as $id) {
        $dataMateriaEnGrupo[] = MateriaEnGrupo::get_MEG_by_id($id)->get_data();
    }
    $json->agregaDatos($dataMateriaEnGrupo);
    $json->estableceExito(true);
} catch (MateriaEnGrupoNoExisteException | MateriaEnGrupoException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();