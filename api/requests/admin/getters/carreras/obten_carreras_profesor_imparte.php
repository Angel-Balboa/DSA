<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {
    $profData = array();
    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false,false, false, false));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"]));
    } elseif (isset($_GET["id_usuario"])) {
        $profesor = Profesor::get_profesor_by_usuario(Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_usuario"], "Id del usuario del profesor", false, false, false, false)));
    } else {
        throw new ProfesorException("Se debe proporcionar el Id del CProfesor, su email o el Id del usuario del profesor", -85);
    }

    $profData["carrera_adscripcion"] = Carrera::get_carrera_by_id($profesor->get_data("carrera_adscripcion"))->get_data(["id", "nombre", "clave"]);
    $profData["carreras_donde_imparte"] = array();

    foreach ($profesor->get_carreras_de_imparticion() as $id_carrera) {
        if ($id_carrera != $profesor->get_data("carrera_adscripcion")) {
            $profData["carreras_donde_imparte"][] = Carrera::get_carrera_by_id($id_carrera)->get_data(["id", "nombre", "clave"]);
        }
    }

    $json->estableceExito(true);
    $json->agregaDatos($profData);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
