<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CPlanDeEstudio;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioNoExistenteException;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudiosYaExisteException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnOpcionesNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\usuario\Usuario;

$json = new CConstructorJSON();
$newData = array();
try {
    $sesion = CRequestsSesion::inits();
    $admin = new CPlanDeEstudio(Usuario::get_usuario_by_id($sesion->id_usuario));
    $clv_plan = CValidadorDeEntradas::validarString($_POST["clv_plan"] ?? null,"Clave del Plan de Estudios", 50);

    if (isset($_POST["nombre_plan"])) {
        $newData["nombre"] = CValidadorDeEntradas::validarString($_POST["nombre_plan"], "Nuevo nombre del Plan de Estudios", 250);
    }

    if (isset($_POST["anio_plan"])) {
        $newData["anio"] = CValidadorDeEntradas::validarEnteros($_POST["anio_plan"], "Nuevo año de registro");
    }

    if (isset($_POST["nueva_clv_plan"])) {
        $newData["clave"] = CValidadorDeEntradas::validarString($_POST["nueva_clv_plan"], "Nueva Clave del Plan de Estudios", 50);
    }

    if (isset($_POST["nivel_plan"])) {
        $newData["nivel"] = CValidadorDeEntradas::validarOpciones($_POST["nivel_plan"], ["Ing", "M.I.", "Esp", "P.A.", "Lic"]);
    }

    $PlanDeEstudios = PlanDeEstudio::get_planDeEstudio_by_clave($clv_plan);

    if ($admin->actualiza_datos_de_plan($PlanDeEstudios, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos del Plan de estudio.");
    }
} catch (ValoresDeCadenaNoValidosException|ValorNoNumericoException|PlanDeEstudiosYaExisteException|PlanDeEstudioException|PlanDeEstudioNoExistenteException|ValoresEnOpcionesNoValidosException|ValoresEnterosNoValidosException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
} catch (\dsa\api\controller\sesion\Exceptions\SesionNoInizializadaException $e) {
}

$json->enviarJSON();