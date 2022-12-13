<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CProfesor;
use dsa\api\model\carrera\Carrera;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\model\usuario\Usuario;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CProfesor(Usuario::get_usuario_by_id($sesion->id_usuario));
    if (isset($_POST["id_usuario"])) {
        $usuario = Usuario::get_usuario_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_usuario"], "Id del usuario", false, false, false, false));
    } elseif (isset($_POST["email_usuario"])) {
        $usuario = Usuario::get_usuario_by_email(CValidadorDeEntradas::validarEmail($_POST["email_usuario"], false));
    } else {
        throw new GeneralException("Se esperaba el Id o el correo del usuario", -20);
    }

    if (isset($_POST["id_carrera"])) {
        $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_carrera"], "Id de la carrera", false, false, false, false));
    } elseif (isset($_POST["clave_carrera"])) {
        $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_POST["clave_carrera"], "Clave de la Carrera", 10, false, false, false));
    } else {
        throw new GeneralException("Se esperaba el Id o la clave de la carrera", -21);
    }

    $nivel_adscripcion = CValidadorDeEntradas::validarString($_POST["nivel_adscripcion"] ?? "Ing", "Nivel de adscripcion", 10, false, false, false);
    $tipo_contrato = CValidadorDeEntradas::validarString($_POST["tipo_contrato"] ?? 'P.A', "Tipo de contrato", 5, false, false, false);
    $categoria = CValidadorDeEntradas::validarString($_POST["categoria"] ?? 'A', "Categoria del profesor", 1, false, false, false);
    $fecha_inicio_contrato = CValidadorDeEntradas::validarString($_POST["inicio_contrato"] ?? "now", "Fecha de inicio de contrato", 10, false, false, false);
    $fecha_fin_contrato = CValidadorDeEntradas::validarString($_POST["fin_contrato"] ?? null, "Fecha de fin de contrato", 10, false, true, true);
    $fecha_fin_contrato = strlen($fecha_fin_contrato) != 10 ? null : $fecha_fin_contrato;

    $tmpProfesor = $admin->crea_perfil_profesor_a_usuario($usuario, $carrera, $nivel_adscripcion, $tipo_contrato, $categoria, $fecha_inicio_contrato, $fecha_fin_contrato);

    if ($tmpProfesor) {
        $tmpProfesorData = $tmpProfesor->get_data(["id", "nivel_adscripcion", "tipo_contrato", "categoria", "inicio_contrato", "fin_contrato"]);
        $tmpCarreraAdscripcion = Carrera::get_carrera_by_id($tmpProfesor->get_data("carrera_adscripcion"));
        $tmpProfesorData["carrera_adscripcion"] = $tmpCarreraAdscripcion->get_data(["id", "nombre"]);
        $tmpProfesorData["imparteEn"] = array();

        foreach ($tmpProfesor->get_carreras_de_imparticion() as $id_carrera) {
            $tmpProfesorData["imparteEn"][] = Carrera::get_carrera_by_id($id_carrera)->get_data(["id", "nombre", "clave"]);
        }
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha generado el perfil de profesor para el usuario: $usuario");
        $json->agregaDatos($tmpProfesorData);
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
