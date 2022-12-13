<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CProfesor;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();
$arrayCarreras = array();
try {
    $sesion = CRequestsSesion::inits();
    $admin = new CProfesor(Usuario::get_usuario_by_id($sesion->id_usuario));
    $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"] ?? null, "Id del profesor", false, false, false, false));
    $listCarreras = $_POST["lstcarreras"] ?? array();

    foreach ($listCarreras as $carrera) {
        $arrayCarreras[] = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($carrera, "Clave de la carrera: $carrera", 10, false, false, false));
    }

    if (count($listCarreras) < 1) throw new GeneralException("La lista de carreras esta vacia", -10);

    if ($admin->agrega_carreras_para_impartir($profesor, $arrayCarreras)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han asignado las carreras al profesor.");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
