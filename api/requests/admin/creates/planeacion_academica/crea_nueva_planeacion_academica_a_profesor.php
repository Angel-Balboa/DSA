<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CPlaneacionAcademica;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$admin = new CPlaneacionAcademica();
$json = new CConstructorJSON();

try {
    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false, false));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"], false));
    } else {
        throw new GeneralException("Se esperaba el Id del profesor o su correo electrónico", -18);
    }

    $periodo = CValidadorDeEntradas::validarOpciones($_GET["periodo"] ?? 3, [1, 2, 3]);
    $anio = CValidadorDeEntradas::validarEnteros($_GET["anio"] ?? null, "Año de la carga académica", false, true, false, false);

    $nuevaPlaneacion = $admin->crea_nueva_planeacion_academica($profesor, $periodo, $anio);

    if (isset($nuevaPlaneacion)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha generado la Planeacion Académica al CProfesor: $profesor");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
