<?php

include_once("../../../../../init.php");

use dsa\api\model\materia\Materia;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\materia_en_grupo\MateriaEnGrupo;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {

    $sesion = CRequestsSesion::inits();

    $meg = MateriaEnGrupo::get_MEG_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_meg"] ?? null, "Id de la Materia En Grupo", false, false, false, false));

    $dataMeg = $meg->get_data();

    $dataMateria = Materia::get_materia_by_id($dataMeg["id_materia"])->get_data(["id", "clave", "nombre", "horas_totales"]);

    if (!is_null($dataMeg["id_profesor"])) {
        $tmpProfesor = Profesor::get_profesor_by_id($dataMeg["id_profesor"]);
        $tmpUsuario = $tmpProfesor->get_usuario();

        $dataProfesor = array("id" => $tmpProfesor->get_data("id"), "nivel_adscripcion" => $tmpProfesor->get_data("nivel_adscripcion"), "nombre" => $tmpUsuario->get_data("nombre"), "apellidos" => $tmpUsuario->get_data("apellidos"));
    } else {
        $dataProfesor = array("id" => -1, "nivel_adscripcion" => "", "nombre" => "Profesor", "apellidos" => "Pendiente");
    }

    $dataMeg["profesor"] = $dataProfesor;
    $dataMeg["materia"] = $dataMateria;

    unset($dataMeg["id_materia"]);
    unset($dataMeg["id_profesor"]);

    $json->agregaDatos($dataMeg);

    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();