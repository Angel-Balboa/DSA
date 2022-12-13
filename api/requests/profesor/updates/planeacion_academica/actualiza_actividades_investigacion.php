<?php

include_once ("../../../../../init.php");

use dsa\api\controller\profesor\CActividadAcademica;
use dsa\api\model\actividad_investigacion\ActividadInvestigacion;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
// use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    // CRequestsSesion::inits();

    if (isset($_POST["id_planeacion"])) {
        $planeacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"], "Id de la Planeación Académica", false, false, false, false));
    } else {
        throw new PlaneacionAcademicaException("Se esperaba el Id de la Planeación Acadécmia", -160);
    }

    $actividadesFromPost = actividades_from_form($_POST);

    $actividadesActuales = array();
    foreach(ActividadInvestigacion::get_all(["planeacion_academica" => $planeacion]) as $id) {
        $actividadesActuales[] = ActividadInvestigacion::get_actividadInvestigacion_by_id($id);
    }

    // se sepran las activides nuevas (id < 0) y las actividades existentes (id > 0). Para guardarlas en la base de datos
    $actividadesNuevas = array();
    $actividadesExistentes = array();
    $idActividadesExistente = array();
    foreach($actividadesFromPost as $actividad) {
        if ($actividad["id"] <= 0) {
            $actividadesNuevas[] = $actividad;
        } else {
            $actividadesExistentes[] = $actividad;
            $idActividadesExistente[] = $actividad["id"];
        }
    }

    //obtenemos las actividades a eliminar. Las actividades a eliminar son aquellas que existen en la base de datos pero no vienen de las que se envian en el formulario
    $actividadesAEliminar = array();
    $actividadesAActualizar = array();
    foreach ($actividadesActuales as $actividad) {
        if (!in_array($actividad->get_data("id"), $idActividadesExistente)) {
            $actividadesAEliminar[] = $actividad;
        } else {
            $actividadesAActualizar[] = $actividad;
        }
    }

    $admin = new CActividadAcademica(Profesor::get_profesor_by_id($planeacion->get_data("id_profesor")));

    // guardamos las actividades nuevas
    foreach($actividadesNuevas as $actividadNueva) {
        try {
            $fechaTermino = new DateTime($actividadNueva["fecha_tentativa"]);
        } catch (Exception $e) {
            $fechaTermino = new DateTime("now");
        }

        $admin->crea_actividad_investigacion($planeacion, $actividadNueva["actividad"], $actividadNueva["tipo"], $actividadNueva["avance_actual"], $actividadNueva["avance_esperado"], $fechaTermino);
    }

    // eliminamos las actividades
    foreach ($actividadesAEliminar as $actividad) {
        $admin->elimina_actividad_investigacion($actividad);
    }

    // actualizamos las actividades que se tengan que actualizar
    foreach ($actividadesAActualizar as $actividad) {
        $tmpIdActividad = $actividad->get_data("id"); // obtenemos el id de la actividad a actualizar

        // el índice de donde se encuentran los nuevo datos
        $indexNewData = -1;
        for($i=0; $i<count($actividadesFromPost); $i++) {
            if ($tmpIdActividad == $actividadesFromPost[$i]["id"]) {

                try {
                    $fechaTermino = new DateTime($actividadesFromPost[$i]["fecha_tentativa"]);
                } catch (Exception $e) {
                    $fechaTermino = new DateTime("now");
                }

                $newData = array("actividad" => $actividadesFromPost[$i]["actividad"], "tipo" => $actividadesFromPost[$i]["tipo"], "avance_actual" => $actividadesFromPost[$i]["avance_actual"], "avance_esperado" => $actividadesFromPost[$i]["avance_esperado"], "fecha_termino" => $fechaTermino);
                $oldData = $actividad->get_data();

                if ($oldData["actividad"] != $newData["actividad"] || $oldData["tipo"] != $newData["tipo"] || $oldData["avance_actual"] != $newData["avance_actual"] || $oldData["avance_esperado"] != $newData["avance_esperado"] || $oldData["fecha_termino"] != $newData["fecha_termino"]->format("Y/m/d")) {
                    $admin->actualiza_actividad_investigacion($actividad, $newData);
                }
                break;
            }
        }
    }

    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();

/************************** DEFINICIÓN DE FUNCIONES EXCLUSIVAS PARA ESTE SCRIPT *********************************/

function actividades_from_form(array $postArray) : array {
    $tmpArray = array();

    for($i=0; $i < count($postArray["id_actividad_investigacion"]); $i++) {
        $tmpArray[] = array("id" => $postArray["id_actividad_investigacion"][$i], "actividad" => $postArray["descripcionActividadInvestigacion"][$i], "tipo" => $postArray["tipoActividadInvestigacion"][$i], "avance_actual" => $postArray["avanceActualActividadInvestigacion"][$i], "avance_esperado" => $postArray["avanceEsperadoActividadInvestigacion"][$i], "fecha_tentativa" => $postArray["fechaTentativaActividadInvestigacion"][$i]);
    }

    return $tmpArray;
}