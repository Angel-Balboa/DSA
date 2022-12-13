<?php
include_once("../../../../../init.php");

use dsa\api\controller\direc\CCargaAcademica;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DateUtils;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
$newData = array();
try {

    $sesion = CRequestsSesion::inits();

    if (isset($_POST["id_carga"])) {
        $cargaAcademica = CargaAcademica::get_cargaAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_carga"], "Id de Carga Académica", false, false, false, false));
    } elseif ((isset($_POST["id_plan"]) || isset($_POST["clv_plan"])) && isset($_POST["periodo_carga"]) && isset($_POST["anio_carga"])) {
        $plan = isset($_POST["id_plan"]) ? PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_plan"], "Id del Plan de Estudios", false, false, false, false)) : PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_POST["clve_plan"], "Clave del Plan de Estudios", 50, false, false, false));
        $periodo = CValidadorDeEntradas::validarOpciones($_POST["periodo_carga"], [1, 2, 3]);
        $anio = CValidadorDeEntradas::validarEnteros($_POST["anio_carga"], "Año de la carga académica", false, false, false, false);

        $cargaAcademica = CargaAcademica::get_cargaAcademica_by_periodo($plan, $periodo, $anio);
    } else {
        throw new GeneralException("No se ha podido obtener datos para la carga académica", -16);
    }

    if (isset($_POST["nuevo_plan"])) {
        $newData["id_plan_estudios"] = CValidadorDeEntradas::validarEnteros($_POST["nuevo_plan"], "Id del nuevo plan de estudios para la Carga Académica", false, false, false, false);
    }

    if (isset($_POST["periodo"])) {
        $newData["periodo"] = CValidadorDeEntradas::validarOpciones($_POST["periodo"], [1, 2, 3]);
    }

    if (isset($_POST["anio"])) {
        $newData["anio"] = CValidadorDeEntradas::validarEnteros($_POST["anio"], "Nuevo año de la Carga Académica", false, false, false,false);
        if ($newData["anio"] < DateUtils::current_year() || $newData["anio"] > DateUtils::current_year() + 2) {
            throw new GeneralException("El nuevo año no puede ser menor al año actual o mayor a dos años", -17);
        }
    }

    if (isset($_POST["fecha_inicio"])) {
        $newData["fecha_inicio"] = CValidadorDeEntradas::validarString($_POST["fecha_inicio"], "Nueva fecha de inicio de la carga académica", 10, false, false, false);
    }

    if (isset($_POST["fecha_cierre"])) {
        $newData["fecha_final"] = CValidadorDeEntradas::validarString($_POST["fecha_cierre"], "Nueva fecha de cierre de la carga académica", 10, false, false, false);
    }

    $admin = new CCargaAcademica(Usuario::get_usuario_by_id($sesion->id_usuario));

    if ($admin->actualiza_datos_de_carga_academica($cargaAcademica, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos de la Carga Académica: $cargaAcademica");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
