<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();

try {
    $dataCarreras = array();
    foreach (Carrera::get_all() as $id) {
        $dataCarreras[] = Carrera::get_carrera_by_id($id)->get_data();
    }

    foreach($dataCarreras as &$carrera) {
        $carrera["director"] = Usuario::get_usuario_by_id($carrera["id_director"])->get_data(["id", "nombre", "apellidos"]);
        unset($carrera["id_director"]);
    }
    $json->agregaDatos($dataCarreras);
    $json->estableceExito(true);
} catch (CarreraNoExistenteException | CarreraException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

