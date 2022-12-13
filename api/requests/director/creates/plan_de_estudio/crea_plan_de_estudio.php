<?php

include_once("../../../../../init.php");

use dsa\api\controller\direc\CPlanDeEstudio;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DateUtils;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $nombre = CValidadorDeEntradas::validarString($_POST["nombre_plan"] ?? null, "Nombre del nuevo Plan de Estudios", 150, false, false, false);
    $anio = CValidadorDeEntradas::validarEnteros($_POST["anio_plan"] ?? DateUtils::current_year(), "Año de registro del Plan de Estudios", false, false, false, false);
    $clave = CValidadorDeEntradas::validarString($_POST["clave_plan"] ?? null, "Clave del nuevo Plan de Estudios", 50, false, false, false);
    $nivel = CValidadorDeEntradas::validarOpciones($_POST["nivel_plan"] ?? "Ing", ["Ing", "Esp", "P.A.", "Lic"]);

    $admin = new CPlanDeEstudio(Usuario::get_usuario_by_id($sesion->id_usuario));

    $tmpPlan = $admin->crea_nuevo_plan_de_estudio($nombre, $anio, $clave, $nivel);

    if (isset($tmpPlan)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha generado el Plan de Estudio: $nombre con clave $clave");
    }
} catch(GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
