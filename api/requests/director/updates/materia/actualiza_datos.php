<?php

include_once("../../../../../init.php");

use dsa\api\controller\direc\CMateria;
use dsa\api\model\materia\Exceptions\MateriaException;
use dsa\api\model\materia\Materia;
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

    if (isset($_POST["id_materia"])) {
        $materia = Materia::get_materia_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_materia"], "Id de la materia", false, false, false, false));
    } elseif ((isset($_POST["id_plan"]) || isset($_POST["clv_plan"])) && isset($_POST["clv_materia"])) {
        if (isset($_POST["id_plan"])) {
            $plan = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_plan"], "Id del plan de Estudios", false, false, false, false));
        } else {
            $plan = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_POST["clv_plan"], "Clave del Plan de Estudios", 50, false, false,false));
        }

        $materia = Materia::get_materia_by_clave($plan, CValidadorDeEntradas::validarString($_POST["clv_materia"], "Clave de la Materia", 20, false, false, false));
    } else {
        throw new MateriaException("No se ha podido obtener la información de la materia", -102);
    }

    if (isset($_POST["nueva_clave"])) {
        $newData["clave"] = CValidadorDeEntradas::validarString($_POST["nueva_clave"], "Nueva clave de la materia", 20);
    }

    if (isset($_POST["nuevo_nombre"])) {
        $newData["nombre"] = CValidadorDeEntradas::validarString($_POST["nuevo_nombre"], "Nuevo nombre de la materia", 150);
    }

    if (isset($_POST["nuevos_creditos"])) {
        $newData["creditos"] = CValidadorDeEntradas::validarEnteros($_POST["nuevos_creditos"], "Nueva cantidad de creditos de la materia", false, false, false, false);
    }

    if (isset($_POST["nuevas_horas_totales"])) {
        $newData["horas_totales"] = CValidadorDeEntradas::validarEnteros($_POST["nuevas_horas_totales"], "Horas totales de la materia al cuatrimestre.", false, false, false, false);
    }

    if (isset($_POST["nuevo_cuatrimestre"])) {
        $newData["cuatrimestre"] = CValidadorDeEntradas::validarEnteros($_POST["nuevo_cuatrimestre"], "Nuevo cuatrimestre de la materia", false, false, false, true);
    }

    if (isset($_POST["nueva_posicion_h"])) {
        $newData["posicion_h"] = CValidadorDeEntradas::validarEnteros($_POST["nueva_posicion_h"], "Nueva Posicion horizontal en el mapa curricular", false, false, false, false);
    }

    if (isset($_POST["nuevo_tipo"])) {
        $newData["tipo"] = CValidadorDeEntradas::validarOpciones($_POST["nuevo_tipo"], ["Básica", "Especialidad", "Valores", "Inglés"]);
    }

    $admin = new CMateria(Usuario::get_usuario_by_id($sesion->id_usuario));

    if ($admin->actualiza_datos($materia, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos de la materia.");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

