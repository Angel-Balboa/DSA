<?php

include_once("../../../../../init.php");

use dsa\api\model\materia\Materia;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\materia\Exceptions\MateriaException;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

$filtro = null;

try {
    $dataMateria = array();

    if (isset($_GET["clv_plan"])) {
        $filtro["plan"] = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_GET["clv_plan"], "Clave del Plan de Estudios", 50, false, false, false));
    } elseif (isset($_GET["id_plan"])) {
        $filtro["plan"] = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_plan"], "Id del Plan de Estudios", false, false, false, false));
    }

    if (isset($_GET["cuatrimestre_materia"])) {
        $filtro["cuatrimestre"] = CValidadorDeEntradas::validarEnteros($_GET["cuatrimestre_materia"], "Cuatrimestre de la materia", false, false, false, false);
    }

    if (isset($_GET["tipo_materia"])) {
        $filtro["tipo"] = CValidadorDeEntradas::validarOpciones($_GET["tipo_materia"], array("Básica", "Especialidad", "Valores", "Inglés"));
    }

    $group_by = null;
    if (isset($_GET["group_by"])) {
        $group_by = CValidadorDeEntradas::validarOpciones($_GET["group_by"], ["cuatrimestre", "tipo"]);
    }

    if (is_null($group_by)) {
        foreach (Materia::get_all($filtro) as $id) {
            $dataMateria[] = Materia::get_materia_by_id($id)->get_data();
        }
    } else {
        foreach (Materia::get_all($filtro) as $id) {
            $tmpData = Materia::get_materia_by_id($id)->get_data();
            $dataMateria[$tmpData[$group_by]][] = $tmpData;
        }
    }

    $json->agregaDatos($dataMateria);
    $json->estableceExito(true);
} catch (MateriaNoExistenteException | MateriaException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();