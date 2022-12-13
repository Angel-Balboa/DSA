<?php

include_once("../../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\actividad_investigacion\ActividadInvestigacion;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaException;
use dsa\api\model\planeacion_asesoria\PlaneacionAsesoria;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $actividades = array();
    $dataActividad = array();

    $planeacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_planeacion"] ?? null, "Id de Planeación Académica", false, false, false, false));


    $actividades["gestion"] = Actividad::get_all(["planeacion_academica" => $planeacion, "tipo" => "GESTION"]);
    $actividades["capacitacion"] = Actividad::get_all(["planeacion_academica" => $planeacion, "tipo" => "CAPACITACION"]);
    $actividades["vinculacion"] = Actividad::get_all(["planeacion_academica" => $planeacion, "tipo" => "VINCULACION"]);
    $actividades["promocion"] = Actividad::get_all(["planeacion_academica" => $planeacion, "tipo" => "PROMOCION"]);
    $actividadesInvestigacion = ActividadInvestigacion::get_all(["planeacion_academica" => $planeacion]);

    try {
        $planeacionAsesoria = PlaneacionAsesoria::get_planeacionAsesoria_by_planeacionAcademica($planeacion);
        $dataActividad["asesorias"] = $planeacionAsesoria->get_data(["id", "institucional_estancia", "institucional_estadia", "empresarial_estancia", "empresarial_estadia"]);
    } catch (PlaneacionAsesoriaException $e) {
        $planeacionAsesoria = null;
        $dataActividad["asesorias"] = array("id" => -1, "institucional_estancia" => 0, "institucional_estadia" => 0, "empresarial_estancia" => 0, "empresarial_estadia" => 0);
    }


    foreach (array_keys($actividades) as $tipoActividad) {
        $dataActividad[$tipoActividad] = array("actividades" => array());

        $sumaHoras = 0;
        foreach ($actividades[$tipoActividad] as $id) {
            $tmpActividad = Actividad::get_actividad_academica_by_id($id);
            $dataActividad[$tipoActividad]["actividades"][] = $tmpActividad->get_data(["id", "descripcion", "empresa_receptora", "horas", "evidencia"]);
            $sumaHoras += intval($tmpActividad->get_data("horas"));
        }
        $dataActividad[$tipoActividad]["horas_totales"] = $sumaHoras;
    }

    if (count($dataActividad["promocion"]["actividades"]) < 1) {
        $dataActividad["promocion"]["actividades"][] = array("id"=>-1, "descripcion"=>"", "empresa_receptora"=>"", "horas"=>0, "evidencia"=>"");
    }

    $dataActividad["investigacion"] = array("horas_totales" => 0, "actividades" => array());
    foreach ($actividadesInvestigacion as $id) {
        $dataActividad["investigacion"]["actividades"][] = ActividadInvestigacion::get_actividadInvestigacion_by_id($id)->get_data(["id", "actividad", "tipo", "avance_actual", "avance_esperado", "fecha_termino"]);
    }

    $dataActividad["planeacion"] = $planeacion->get_data();

    $json->agregaDatos($dataActividad);
    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
