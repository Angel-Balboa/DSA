<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CProfesor;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$admin = new CProfesor();
$json = new CConstructorJSON();

try {
    $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"] ?? null, "Id del profesor", false, false, false, false));
    $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_GET["clv_carrera"] ?? null, "Clave de la carrera", 10, false, false, false));

    if ($admin->quitar_asignacion_de_materia_a_profesor($profesor, $carrera)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha quitado la carrera: $carrera de la lista del profesor: $profesor");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
