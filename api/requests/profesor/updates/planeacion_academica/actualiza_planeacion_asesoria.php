<?php

include_once ("../../../../../init.php");

use dsa\api\controller\profesor\CActividadAcademica;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaException;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaNoExisteException;
use dsa\api\model\planeacion_asesoria\PlaneacionAsesoria;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
// use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
try {

    $institucionalEstancia = CValidadorDeEntradas::validarEnteros($_POST["institucional_estancia"] ?? 0, "Cantidad de alumnos asesorados como asesor instituciona en estancia");
    $institucionalEstadia = CValidadorDeEntradas::validarEnteros($_POST["institucional_estadia"] ?? 0, "Cantidad de alumnos asesorados como asesor instituciona en estadía");
    $empresarialEstancia = CValidadorDeEntradas::validarEnteros($_POST["empresarial_estancia"] ?? 0, "Cantidad de alumnos asesorados como asesor empresarial en estancia");
    $empresarialEstadia = CValidadorDeEntradas::validarEnteros($_POST["empresarial_estadia"] ?? 0, "Cantidad de alumnos asesorados como asesor empresarial en estadía");

    try {
        if (isset($_POST["id_planeacion_asesoria"])) {
            if ($_POST["id_planeacion_asesoria"] < 1) {
                throw new PlaneacionAsesoriaNoExisteException("La planeacion de asesorias no existe", -1);
            } else {
                $planeacionAsesoria = PlaneacionAsesoria::get_planeacionAsesoria_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion_asesoria"], "Id de la Planeación de Asesorias", false, false, false, false));

                // si existe y por lo tanto se actualiza
                $planeacionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id($planeacionAsesoria->get_data("id_planeacion_academica"));

                $admin = new CActividadAcademica(Profesor::get_profesor_by_id($planeacionAcademica->get_data("id_profesor")));

                if ($admin->actualiza_planeacion_asesorias($planeacionAsesoria, $institucionalEstancia, $institucionalEstadia, $empresarialEstancia, $empresarialEstadia)) {
                    $json->agregaMensajeDeExito("Se ha actualizado la planeacion de asesorias");
                    $json->estableceExito(true);
                }
            }
        } elseif (isset($_POST["id_planeacion"])) {
            $planeacionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"], "Id de la Planeacion Académica",  false, false, false));
            $planeacionAsesoria = PlaneacionAsesoria::get_planeacionAsesoria_by_planeacionAcademica($planeacionAcademica);

            // si existe y lo tanto se actualiza
            $admin = new CActividadAcademica(Profesor::get_profesor_by_id($planeacionAcademica->get_data("id_profesor")));

            if ($admin->actualiza_planeacion_asesorias($planeacionAsesoria, $institucionalEstancia, $institucionalEstadia, $empresarialEstancia, $empresarialEstadia)) {
                $json->agregaMensajeDeExito("Se ha actualizado la planeacion de asesorias");
                $json->estableceExito(true);
            }

        } else {
            throw new PlaneacionAsesoriaException("Se esperaba el Id de la Planeación académica o el Id de la planeación de asesorías", -4000);
        }
    } catch (PlaneacionAsesoriaNoExisteException $ex) {
        // no existe se va a crear
        if (isset($_POST["id_planeacion"])) {
            $planeacionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_planeacion"], "Id de la Planeacion Académica",  false, false, false));

            // se crea la nueva planeacion de asesorias
            $admin = new CActividadAcademica(Profesor::get_profesor_by_id($planeacionAcademica->get_data("id_profesor")));

            $admin->crea_planeacion_asesorias($planeacionAcademica, $institucionalEstancia, $institucionalEstadia, $empresarialEstancia, $empresarialEstadia);

            $json->estableceExito(true);
            $json->agregaMensajeDeExito("Se ha generado la nueva planeación de asesorias");
        } else {
            throw new PlaneacionAcademicaException("Se esperaba el Id de la Planeacion Académica", -3001);
        }
    }
} catch (GeneralException $e) {
    if ($e->getCode() == 13131) {
        $json->agregaDatosError("Se necesitan cambiar los valores para realizar el guardado de información", 13131);
    } else {
        $json->agregaDatosError($e->getMessage(), $e->getCode());
    }
}

$json->enviarJSON();