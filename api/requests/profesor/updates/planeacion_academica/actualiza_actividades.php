<?php

include_once ("../../../../../init.php");

use dsa\api\controller\profesor\CActividadAcademica;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\profesor\Profesor;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\controller\sesion\CRequestsSesion;


$json = new CConstructorJSON();

try {
    CRequestsSesion::inits();

    if (isset($_POST["id_planeacion"])) {
        $planeacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"], "Id de la Planeación Académica", false, false, false, false));
    } else {
        throw new PlaneacionAcademicaException("Se esperaba el Id de la Planeación Acadécmia", -160);
    }

    // obtenemos el tipo de actividad
    $tipoActividadAcademica = CValidadorDeEntradas::validarOpciones($_POST["tipo_actividad_academica"] ?? null, ["GESTION", "CAPACITACION", "VINCULACION"]);

    // obtenemos y separamos la información de cada actividad
    $actividadesFromPost = actividades_from_form($_POST, $tipoActividadAcademica); // actividades enviadas desde un formulario (post)

    $actividadesActuales = array();
    foreach (Actividad::get_all(["planeacion_academica" => $planeacion, "tipo" => $tipoActividadAcademica]) as $id) {
        $actividadesActuales[] = Actividad::get_actividad_academica_by_id($id);
    }
    // Se separan las actividades nuevas (id < 0) y las actividades existentes (id > 0). Para Guardarlas en la Base de datos
    $actividadesNuevas = array();
    $actividadesExistentes = array();
    $idsActividadesExistentes = array();
    foreach($actividadesFromPost as $actividad) {
        if ($actividad["id"] <= 0) {
            $actividadesNuevas[] = $actividad;
        } else {
            $actividadesExistentes[] = $actividad;
            $idsActividadesExistentes[] = $actividad["id"];
        }
    }

    // obtenemos las actividades a eliminar. Las actividades a eliminar son aquellas que existen en la base de datos pero no vienen las que se envian en el formulario
    $actividadesAEliminar = array();
    $actividadesAActualizar = array();
    foreach($actividadesActuales as $actividad) {
        if (!in_array($actividad->get_data("id"), $idsActividadesExistentes)) {
            $actividadesAEliminar[] = $actividad;
        } else {
            $actividadesAActualizar[] = $actividad;
        }
    }


    $admin = new CActividadAcademica(Profesor::get_profesor_by_id($planeacion->get_data("id_profesor")));

    // guardamos las nuevas actividdes
    foreach ($actividadesNuevas as $actividad) {
        $empresaRecetora = $actividad["empresa_receptora"] ?? null;
        $admin->crea_actividad($planeacion, $actividad["descripcion"], $actividad["horas"], $actividad["evidencia"], $tipoActividadAcademica, $empresaRecetora);
    }

    // eliminamos las actividades
    foreach ($actividadesAEliminar as $actividad) {
        $admin->elimina_actividad($actividad);
    }

    // actualizamos las que se tengan que actualizar

    foreach ($actividadesAActualizar as $actividad) {
        $tmpIdActividad = $actividad->get_data("id"); //obtenemos el id de la actividad a actualizar

        // el indice de donde se encuentran los nuevos datos
        $indexNewData = -1;
        for ($i=0; $i<count($actividadesFromPost); $i++) {
            if ($tmpIdActividad == $actividadesFromPost[$i]["id"]) {
                $newData = array("descripcion" => $actividadesFromPost[$i]["descripcion"], "horas" => $actividadesFromPost[$i]["horas"], "evidencia" => $actividadesFromPost[$i]["evidencia"], "empresa_receptora" => $actividadesFromPost[$i]["empresa_receptora"] ?? $actividad->get_data("empresa_receptora"));

                if ($tipoActividadAcademica == "VINCULACION") {
                    if ($actividad->get_data("descripcion") != $newData["descripcion"] || $actividad->get_data("horas") != $newData["horas"] || $actividad->get_data("evidencia") != $newData["evidencia"] || $newData["empresa_receptora"] != $actividad->get_data("empresa_receptora")) {
                        $admin->actualiza_actividad($actividad, $newData);
                    }
                } else {
                    unset($newData["empresa_receptora"]);
                    if ($actividad->get_data("descripcion") != $newData["descripcion"] || $actividad->get_data("horas") != $newData["horas"] || $actividad->get_data("evidencia") != $newData["evidencia"]) {
                        $admin->actualiza_actividad($actividad, $newData);
                    }
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

/**
 * @param array $postArray
 * @param String $tipoActividad
 * @return array
 * @throws ActividadAcademicaException
 */
function actividades_from_form(array $postArray, String $tipoActividad) : array {
    $tmpArray = array();
    // obtenemos la cantidad de actividades
    for($i=0; $i < count($postArray["hdnIdActividad"]); $i++) {
        switch ($tipoActividad) {
            case "GESTION":
            case "CAPACITACION":
                $tmpArray[] = array("id" => $postArray["hdnIdActividad"][$i], "descripcion" => $postArray["descripcionActividad"][$i], "horas" => $_POST["horasActividad"][$i], "evidencia" => $postArray["evidenciaActividad"][$i]);
                break;
            case "VINCULACION":
                $tmpArray[] = array("id" => $postArray["hdnIdActividad"][$i], "descripcion" => $postArray["descripcionActividad"][$i], "empresa_receptora" => $postArray["empresaReceptoraActividad"][$i], "horas" => $postArray["horasActividad"][$i], "evidencia" => $postArray["evidenciaActividad"][$i]);
                break;
            default:
                throw new ActividadAcademicaException("No es posible obtener actividades del tipo $tipoActividad, verifica la documentación", -300);
                break;
        }
    }

    return $tmpArray;
}
