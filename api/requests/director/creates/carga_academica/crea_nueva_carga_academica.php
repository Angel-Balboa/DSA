<?php
include_once("../../../../../init.php");

use dsa\api\model\usuario\Usuario;
use dsa\api\controller\direc\CCargaAcademica;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DateUtils;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    if (isset($_POST["id_plan"])) {
        $planDeEstudio = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_plan"], "Id del Plan de Estudios", false, false, false, false));
    } elseif (isset($_POST["clave_plan_de_estudio"])) {
        $planDeEstudio = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_POST["clave_plan_de_estudio"], "Clave del Plan de Estudios", 50, false, false, false));
    } else {
        throw new GeneralException("Se esperaba el Id o la clave del plan de estudios", -6003);
    }

    $fecha_inicio = CValidadorDeEntradas::validarString($_POST["fecha_inicio"] ?? null, "Fecha de incio", 10, false, false,false);
    $fecha_final = CValidadorDeEntradas::validarString($_POST["fecha_cierre"] ?? null, "Fecha de cierre", 10, false, false,false);
    $periodo = CValidadorDeEntradas::validarOpciones($_POST["periodo"] ?? 3, [1, 2, 3]);
    $anio = CValidadorDeEntradas::validarEnteros($_POST["anio"] ?? DateUtils::current_year(), "Año de la carga académica", false, false, false, false);

    if ($anio < DateUtils::current_year() || $anio > DateUtils::current_year()+2) {
        throw new GeneralException("El año de la carga académica no puede ser menor al año actual o o superior a dos años al actual", -15);
    }

    $admin = new CCargaAcademica(Usuario::get_usuario_by_id($sesion->id_usuario));

    if ($admin->crea_carga_academica($planDeEstudio, $fecha_inicio, $fecha_final, $periodo, $anio)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha generado una nueva carga académica para el plan de estudio: $planDeEstudio");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

