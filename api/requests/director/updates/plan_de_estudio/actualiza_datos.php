<?php

include_once("../../../../../init.php");

use dsa\api\controller\direc\CPlanDeEstudio;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
$newData = array();
try {
    $sesion = CRequestsSesion::inits();

    if (isset($_POST["id_plan_de_estudios"])) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_plan_de_estudios"], "Id del Plan de Estudios", false, false, false));
    } elseif (isset($_POST["clv_plan_de_estudios"])){
        $plan = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_POST["clv_plan_de_estudios"], "Clave del Plan de Estudios", 10, false, false, false));
    } else {
        throw new GeneralException("Se esperaba el Id o la Clave del Plan de Estudios", -52);
    }

    if (isset($_POST["nuevo_nombre_plan"])) {
        $newData["nombre"] = CValidadorDeEntradas::validarString($_POST["nuevo_nombre_plan"], "Nuevo nombre del Plan de Estudios", 150, false, false, false);
    }

    if (isset($_POST["nuevo_anio_plan"])) {
        $newData["anio"] = CValidadorDeEntradas::validarEnteros($_POST["nuevo_anio_plan"], "Nuevo año de registro", false, false, false, false);
    }

    if (isset($_POST["nueva_clave_plan"])) {
        $newData["clave"] = CValidadorDeEntradas::validarString($_POST["nueva_clave_plan"], "Nueva Clave del Plan de Estudios", 10, false, false, false);
    }

    if (isset($_POST["nuevo_nivel_plan"])) {
        $newData["nivel"] = CValidadorDeEntradas::validarOpciones($_POST["nuevo_nivel_plan"], ["Ing", "M.I.", "Esp", "P.A.", "Lic"]);
    }

    $admin = new CPlanDeEstudio(Usuario::get_usuario_by_id($sesion->id_usuario));

    if ($admin->actualiza_datos_de_plan($plan, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos del plan de estudio");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
