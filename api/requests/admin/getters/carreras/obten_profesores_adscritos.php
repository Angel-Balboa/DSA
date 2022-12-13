<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();
$dataProfesores = array();
try {
    $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_GET["clv_carrera"] ?? null, "Clave de la carrera", 10, false, false, false));

    foreach ($carrera->get_profesores_adscritos() as $id_profesor) {
        $tmpProfesor = Profesor::get_profesor_by_id($id_profesor);
        $tmpUsuario = Usuario::get_usuario_by_id($tmpProfesor->get_data("id_usuario"));
        $tmpData = $tmpUsuario->get_data(["id", "nombre", "apellidos"]);
        $tmpData["id_usuario"] = $tmpData["id"];
        $tmpData["tipo_contrato"] = $tmpProfesor->get_data("tipo_contrato");
        $tmpData["id_profesor"] = $id_profesor;
        unset($tmpData["id"]);
        $dataProfesores[] = $tmpData;
    }
    $json->agregaDatos($dataProfesores);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
