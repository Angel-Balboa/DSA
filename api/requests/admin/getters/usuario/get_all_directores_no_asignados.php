<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\lib\constructorJSON\CConstructorJSON;

$json = new CConstructorJSON();
$directores_no_asignados = array();

try {
    foreach (Usuario::get_all(["tipo" => "director","activo"=>true]) as $id) {
        try {
            Carrera::get_carrera_by_director(Usuario::get_usuario_by_id($id));
        } catch (CarreraNoExistenteException $e) {
            $directores_no_asignados[] = Usuario::get_usuario_by_id($id)->get_data(["id", "nombre", "apellidos", "email"]);
        }
    }
    $json->estableceExito(true);
    $json->agregaDatos($directores_no_asignados);
} catch (UsuarioException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
