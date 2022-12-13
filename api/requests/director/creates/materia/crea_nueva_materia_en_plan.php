<?php
include_once("../../../../../init.php");

use dsa\api\controller\direc\CMateria;
use dsa\api\model\plan_de_estudio\Exceptions\PlanDeEstudioException;
use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {

    $sesion = CRequestsSesion::inits();

    if (isset($_POST["clv_plan"])) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_clave(CValidadorDeEntradas::validarString($_POST["clv_plan"], "Clave del Plan de Estudios", 50));
    } elseif (isset($_POST["id_plan"])) {
        $plan = PlanDeEstudio::get_planDeEstudio_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_plan"], "Id del Plan de Estudios", false, false, false, false));
    } else {
        throw new PlanDeEstudioException("Se esperaba la clave o el Id del Plan de estudios", -95);
    }


    $clv_materia = CValidadorDeEntradas::validarString($_POST["clave_materia"] ?? null, "Clave de la nueva materia", 20);
    $nombre = CValidadorDeEntradas::validarString($_POST["nombre_materia"] ?? null, "Nombre de la nueva materia", 150);
    $creditos = CValidadorDeEntradas::validarEnteros($_POST["creditos_materia"] ?? null, "Creditos de la nueva materia", false, false, false, false);
    $cuatrimestre = CValidadorDeEntradas::validarEnteros($_POST["cuatrimestre_materia"] ?? null, "Cuatrimestre de la nueva materia", false, false, false, true);
    $posicion_h = CValidadorDeEntradas::validarEnteros($_POST["posicion_horizontal"] ?? null, "Posición horizontal en el Mapa Curricular", false, false, false, false);
    $horas_totales = CValidadorDeEntradas::validarEnteros($_POST["horas_totales"] ?? null, "Horas totales al cuatrimestre de la materia", false, false, false, false);
    $tipo = CValidadorDeEntradas::validarOpciones($_POST["tipo_materia"] ?? "Especialidad", ["Básica", "Especialidad", "Valores", "Inglés"]);

    $admin = new CMateria(Usuario::get_usuario_by_id($sesion->id_usuario));
    $tmpMateria = $admin->crea_nueva_materia($plan, $clv_materia, $nombre, $creditos, $cuatrimestre, $posicion_h, $horas_totales, $tipo);

    if (isset($tmpMateria)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha creado la materia $nombre con la clave $clv_materia");
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
