<?php

include_once("../../../../../init.php");

use \dsa\api\controller\admin\CPlaneacionAcademica;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$admin = new CPlaneacionAcademica();
$json = new CConstructorJSON();

try {

    $PTCs = Profesor::get_all(["tipo_contrato" => "P.T.C"]);

    $periodo = CValidadorDeEntradas::validarOpciones($_GET["periodo"] ?? 3, [1, 2, 3]);
    $anio = CValidadorDeEntradas::validarEnteros($_GET["anio"] ?? null, "Año de la carga académica", false, true, false, false);

    foreach ($PTCs as $ptc) {
        $tmpPTC = Profesor::get_profesor_by_id($ptc["id"]);

        $admin->crea_nueva_planeacion_academica($tmpPTC, $periodo, $anio);
    }

    $json->estableceExito(true);
    $json->agregaMensajeDeExito("Se han generado las Planeaciones Académicas a todos los PTCs");
} catch (GeneralException $ex) {
    $json->agregaDatosError($ex->getMessage(), $ex->getCode());
}

$json->enviarJSON();


