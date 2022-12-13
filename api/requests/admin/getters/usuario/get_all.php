<?php

include_once("../../../../../init.php");

use dsa\api\model\usuario\Usuario;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();
$data_usuarios = array();

try {
    foreach (Usuario::get_all() as $id) {
        $data_usuarios[] = Usuario::get_usuario_by_id($id)->get_data();
    }
    $json->estableceExito(true);
    $json->agregaDatos($data_usuarios);
} catch (UsuarioException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();