<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CProfesor;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DataChecker;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();
$newData = array();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CProfesor(Usuario::get_usuario_by_id($sesion->id_usuario));
    if (isset($_POST["nuevo_nivel_adscripcion"])) {
        $newData["nivel_adscripcion"] = CValidadorDeEntradas::validarOpciones($_POST["nuevo_nivel_adscripcion"], ["Lic.", "Ing.", "M.C.", "M.A.", "Dr."]);
    }

    if (isset($_POST["nuevo_tipo_contrato"])) {
        $newData["tipo_contrato"] = CValidadorDeEntradas::validarOpciones($_POST["nuevo_tipo_contrato"], ["P.A", "P.T.C"]);
    }

    if (isset($_POST["nueva_categoria"])) {
        $newData["categoria"] = CValidadorDeEntradas::validarOpciones(strtoupper($_POST["nueva_categoria"]), ['A', 'B', 'C', 'D']);
    }

    if (isset($_POST["nuevo_inicio_contrato"])) {

        if (strlen($_POST["nuevo_inicio_contrato"]) != 10) {
            $newData["inicio_contrato"] = null;
        } else {
            $newData["inicio_contrato"] = CValidadorDeEntradas::validarString($_POST["nuevo_inicio_contrato"], "Nuevo inicio de contrato del profesor", 10, false, false, false);
        }
    }

    if (isset($_POST["nuevo_fin_de_contrato"])) {

        if (strlen($_POST["nuevo_fin_de_contrato"]) != 10) {
            $newData["fin_contrato"] = "indf";
        } else {
            $newData["fin_contrato"] = CValidadorDeEntradas::validarString($_POST["nuevo_fin_de_contrato"], "Nuevo fin de contrato del profesor", 10, false, false, false);
        }
    }

    if (isset($_POST["nueva_carrera_adscripcion"])) {
        $tmpCarrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_POST["nueva_carrera_adscripcion"], "Id de la nueva carrera de adscripción", false, false, false, false));
        $newData["carrera_adscripcion"] = $tmpCarrera->get_data("id");
    }

    $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"] ?? null, "Id del profesor", false, false, false, false));

    if ($admin->actualiza_datos($profesor, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos del profesor");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();