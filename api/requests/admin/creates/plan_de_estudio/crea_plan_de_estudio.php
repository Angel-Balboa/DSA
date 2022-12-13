<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CPlanDeEstudio;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudiosYaExisteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Utils\DateUtils;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnOpcionesNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;


$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CPlanDeEstudio();
    $clv_carrera = CValidadorDeEntradas::validarString($_POST["clv_carrera"] ?? null, "Clave de carrera", 10);
    $nombre = CValidadorDeEntradas::validarString($_POST["nombre_plan"] ?? null, "Nombre del Plan de Estudios", 250, false);
    $anio = CValidadorDeEntradas::validarEnteros($_POST["anio_plan"] ?? DateUtils::current_year(), "Año de registro del plan");
    $clave_plan = CValidadorDeEntradas::validarString($_POST["clv_plan"] ?? null, "Clave del Nuevo Plan de Estudios", 50, false);
    $nivel_plan = CValidadorDeEntradas::validarOpciones($_POST["nivel_plan"] ?? null, ["Ing", "M.I.", "Esp", "P.A.", "Lic"]);

    $carrera = Carrera::get_carrera_by_clave($clv_carrera);

    if ($admin->crea_nuevo_plan_de_estudio($carrera, $nombre, $anio, $clave_plan, $nivel_plan)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha creado el plan de estudio: $nombre, con la clave $clave_plan");
    }
} catch (ValoresDeCadenaNoValidosException|PlanDeEstudiosYaExisteException|PlanDeEstudioNoExistenteException|PlanDeEstudioException|ValoresEnOpcionesNoValidosException|ValoresEnterosNoValidosException|ValorNoNumericoException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
