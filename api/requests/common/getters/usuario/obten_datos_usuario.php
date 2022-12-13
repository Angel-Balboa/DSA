<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {
    $usuario = null;
    if (isset($_POST["id_usuario"])) {
        $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_usuario"], "Id de usuario", false, false, false, false));
    } elseif (isset($_POST["email_usuario"])) {
        $usuario = Usuario::get_usuario_by_email(CValidadorDeEntradas::validarEmail($_POST["email_usuario"], false));
    } else {
        throw new UsuarioException("Se debe proporcionar el email o el id del usuario", -80);
    }
    $datosUsuario = $usuario->get_data();

    try {
        $profesor = Profesor::get_profesor_by_usuario($usuario);
        $datosUsuario["profesor"] = $profesor->get_data(["id", "nivel_adscripcion", "tipo_contrato", "categoria", "inicio_contrato", "fin_contrato"]);
        $tmpCarreraAdscripcion = Carrera::get_carrera_by_id($profesor->get_data("carrera_adscripcion"));
        $datosUsuario["profesor"]["carrera_adscripcion"] = $tmpCarreraAdscripcion->get_data(["id", "nombre"]);
        $datosUsuario["profesor"]["imparteEn"] = array();

        foreach ($profesor->get_carreras_de_imparticion() as $id_carrera) {
            $datosUsuario["profesor"]["imparteEn"][] = Carrera::get_carrera_by_id($id_carrera)->get_data(["id", "nombre", "clave"]);
        }
    } catch (ProfesorNoExisteException $e) {
        ;
    }

    $json->agregaDatos($datosUsuario);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();