<?php

include_once("../../../../../init.php");


use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\carrera\Carrera;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$json = new CConstructorJSON();
$tmpFiltro = array();

//Inicio para obtener todos los prestamos
try
{
    CRequestsSesion::inits();
    if (isset($_GET["clv_carrera"])) {
        $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_GET["clv_carrera"], "Clave de la Carrera", 10, false, false, false));
        $tmpFiltro["carrera_adscripcion"] = $carrera;
    } elseif (isset($_GET["id_carrera"])) {
        $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_carrera"], "Id de la carrera", false, false, false, false));
        $tmpFiltro["carrera_adscripcion"] = $carrera;
    }

    $groupBy = "none";
    if (isset($_GET["groupby"])) $groupBy = CValidadorDeEntradas::validarOpciones($_GET["groupby"], ["tipo_contrato", "nivel_adscripcion", "categoria", "carrera_adscripcion", "none"]);

    $filtro = (count($tmpFiltro) < 1) ? null : $tmpFiltro;

    $datosprofesores = array();
    foreach (Profesor::get_all($filtro) as $profesor)
    {
        $tmpProfesor = Profesor::get_profesor_by_id($profesor["id"])->get_data();
        $tmpProfesor["usuario"] = Usuario::get_usuario_by_id($tmpProfesor["id_usuario"])->get_data(["id", "email", "nombre", "apellidos"]);
        unset($tmpProfesor["id_usuario"]);
        if ($groupBy == "none") {
            $datosprofesores[] = $tmpProfesor;
        } else {
            $datosprofesores[str_replace(".", "", $tmpProfesor[$groupBy])][] = $tmpProfesor;
        }

    }

    $json->agregaDatos($datosprofesores);
    $json->estableceExito(true);
}catch (GeneralException $e)
{
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
