<?php

include_once("../../../../../init.php");

use dsa\api\model\materia\Materia;
use dsa\api\model\materia\Exceptions\MateriaException;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try {
    $dataMateria = array();
    foreach (Materia::get_all() as $id) {
        $dataMateria[] = Materia::get_materia_by_id($id)->get_data();
    }
    $json->agregaDatos($dataMateria);
    $json->estableceExito(true);
} catch (MateriaNoExistenteException | MateriaException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();