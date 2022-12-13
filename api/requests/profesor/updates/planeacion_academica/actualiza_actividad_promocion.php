<?php

include_once ("../../../../../init.php");


use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\profesor\CActividadAcademica;
// use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    //CRequestsSesion::inits();
    $horasPromocion = CValidadorDeEntradas::validarEnteros($_POST["horasPromocion"] ?? 0, "Número de horas", false, false, false, true);

    if (isset($_POST["id_actividad_promocion"]) && CValidadorDeEntradas::validarEnteros($_POST["id_actividad_promocion"], "Id de la actividad de Promocion", false, false, true, true) > 0) {
        $actividad = Actividad::get_actividad_academica_by_id($_POST["id_actividad_promocion"]);
        $tmpPlaneacionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id($actividad->get_data("id_planeacion_academica"));
        $tmpProfesor = Profesor::get_profesor_by_id($tmpPlaneacionAcademica->get_data("id_profesor"));
        // se actualiza la actividad

        $admin = new CActividadAcademica($tmpProfesor);

        $newData = array("horas" => $horasPromocion);

        $admin->actualiza_actividad($actividad, $newData);

        $json->estableceExito(true);
    } else {
        if (isset($_POST["id_planeacion"])) {
            $planeacionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"], "Id de la Planeacion Académica", false, false, false, false));
            $tmpProfesor = Profesor::get_profesor_by_id($planeacionAcademica->get_data("id_profesor"));

            $admin = new CActividadAcademica($tmpProfesor);

            $admin->crea_actividad($planeacionAcademica, "Actividad de Promocion", $horasPromocion, "Oficio de comisión", "PROMOCION", "Universidad Politécnica de Victoria");

            $json->estableceExito(true);
        } else {
            throw new PlaneacionAcademicaException("Se esperaba el Id de la Planeación académica", -3008);
        }
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();