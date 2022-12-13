<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Utils\DateUtils;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\Exceptions\GeneralException;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {

    $sesion = CRequestsSesion::inits();

    if (isset($_GET["id_carrera"])) {
        $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_carrera"], "Id de la carrera", false, false, false, false));
    } elseif (isset($_GET["clv_carrera"])) {
        $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_GET["clv_carrera"], "Clave de la Carrera", 10, false, false, false));
    } else {
        throw new CarreraException("Se esperaba el Id o la clave de la Carrera", -6025);
    }

    $periodo = CValidadorDeEntradas::validarOpciones($_GET["periodo"] ?? null, [1, 2, 3]);
    $anio = CValidadorDeEntradas::validarOpciones($_GET["anio"] ?? null, range(DateUtils::current_year() - 2, DateUtils::current_year() + 1));

    $idsProfesoresPTC = Profesor::get_all(["carrera_adscripcion" => $carrera, "tipo_contrato" => "P.T.C"]);

    $allPlaneaciones = array();
    foreach ($idsProfesoresPTC as $id) {
        $tmpProfesor = Profesor::get_profesor_by_id($id["id"]);
        $tmpDataProfesor = $tmpProfesor->get_data(["id", "nivel_adscripcion"]);
        $tmpUsuario = Usuario::get_usuario_by_id($tmpProfesor->get_data("id_usuario"));

        foreach (PlaneacionAcademica::get_all(["profesor" => $tmpProfesor, "periodo" => $periodo, "anio" => $anio]) as $id) {
            $tmpDataPlaneacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id($id)->get_data(["id", "estado"]);
            $tmpDataPlaneacion["profesor"] = $tmpDataProfesor;
            $tmpDataPlaneacion["usuario"] = $tmpUsuario->get_data(["id", "nombre", "apellidos"]);

            $allPlaneaciones[] = $tmpDataPlaneacion;
        }
    }

    $json->agregaDatos($allPlaneaciones);
    $json->estableceExito(true);
}catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
