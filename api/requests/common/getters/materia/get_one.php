<?php

include_once("../../../../../init.php");

use dsa\api\model\materia\Exceptions\MateriaException;
use dsa\api\model\materia\Materia;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {

    if (isset($_GET["id_materia"])) {
        $materia = Materia::get_materia_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_materia"], "Id de la materia", false, false, false, false));
    } elseif ((isset($_GET["id_plan"]) || (isset($_GET["clv_plan"]))) && isset($_GET["clv_materia"])) {
        if (isset($_GET["id_plan"])) {
            $plan = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_plan"], "Id del Plan de Estudios", false, false, false, false));
        } else {
            $plan = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_GET["clv_plan"], "Clave del Plan de Estudios", 50, false, false, false));
        }
        $materia = Materia::get_materia_by_clave($plan, CValidadorDeEntradas::validarString($_GET["clv_materia"], "Clave de la Materia", 20));
    } else {
        throw new MateriaException("No se ha podido obtener los datos de la Materia, verifica.", -101);
    }

    $dataMateria = $materia->get_data();
    $json->estableceExito(true);
    $json->agregaDatos($dataMateria);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();