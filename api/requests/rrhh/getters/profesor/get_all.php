<?php

include_once("../../../../../init.php");

use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Carrera;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;

$json = new CConstructorJSON();

$dataProfesores = array();
try {
    foreach (Profesor::get_all() as $profesor) {
        $tmpProfesor = Profesor::get_profesor_by_id($profesor["id"]);
        $tmpUsuario = $tmpProfesor->get_usuario();
        $tmpData = $tmpUsuario->get_data();
        $tmpData["profesor"] = $tmpProfesor->get_data(["id", "nivel_adscripcion", "tipo_contrato", "categoria", "inicio_contrato", "fin_contrato"]);
        $tmpData["profesor"]["carrera_adscripcion"] = Carrera::get_carrera_by_id($tmpProfesor->get_data("id_carrera_adscripcion"))->get_data(["id", "nombre", "clave"]);

        $dataProfesores[] = $tmpData;
    }
    $json->estableceExito(true);
    $json->agregaDatos($dataProfesores);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();