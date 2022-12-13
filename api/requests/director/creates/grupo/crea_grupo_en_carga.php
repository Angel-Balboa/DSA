<?php

include_once("../../../../../init.php");

use dsa\api\controller\direc\CGrupo;
use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\usuario\Usuario;
use dsa\lib\Exceptions\GeneralException;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();

try {

    $sesion = CRequestsSesion::inits();

    $carga = CargaAcademica::get_cargaAcademica_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_carga"] ?? null, "Id de la Carga Académica", false, false, false, false));

    $clave = CValidadorDeEntradas::validarString($_POST["clave_grupo"] ?? null, "Clave del Nuevo Grupo", 50, false, false,false);

    $cuatrimestre = CValidadorDeEntradas::validarOpciones($_POST["cuatrimestre"], range(1, 10));
    $turno = CValidadorDeEntradas::validarOpciones($_POST["turno"], [1, 2, 3, 4]);

    $fechaInicio = null;
    $fechaCierre = null;

    if (!isset($_POST["inicioPorDefecto"])) {
        $date = new DateTime($_POST["fechaNoDefaultInicio"]);
        $fechaInicio = $date->format("Y-m-d");
        unset($date);
    }

    if (!isset($_POST["cierrePorDefecto"])) {
        $date = new DateTime($_POST["fechaNoDefaultCierre"]);
        $fechaCierre = $date->format("Y-m-d");
        unset($date);
    }

    $admin = new CGrupo(Usuario::get_usuario_by_id($sesion->id_usuario));

    $admin->crea_grupo_en_carga($carga,$clave, $turno, $cuatrimestre, $fechaInicio, $fechaCierre);
    $json->agregaMensajeDeExito("Se ha generado el grupo $clave con éxito");
    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
