<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\imparten\ImpartenEn;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\Exceptions\GeneralException;

$json = new CConstructorJSON();

try {
    CRequestsSesion::inits();

    if (isset($_GET["id_carrera"])) {
        $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_carrera"], "Id de la carrera", false, false, false, false));
    } elseif (isset($_GET["clv_carrera"])) {
        $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_GET["clv_carrera"], "Clave de la Carrera", 10, false, false, false));
    } else {
        throw new CarreraException("Se esperaba el Id o la Clave de la Carrera", -6001);
    }

    $impartenEn = ImpartenEn::impartenEn_by_carrera($carrera);

    $profsQueImparten = $impartenEn->get_profesores();
    $profPrestamo = array();
    $profAdscritos = array();
    foreach (Profesor::get_all(["carrera_adscripcion" => $carrera]) as $profeAdscrito) {
        $profAdscritos[] = $profeAdscrito["id"];
    }

    foreach ($profsQueImparten as $p) {
        if (!in_array($p, $profAdscritos)) {
            $tmpProf = Profesor::get_profesor_by_id($p)->get_data();
            $tmpProf["usuario"] = Usuario::get_usuario_by_id($tmpProf["id_usuario"])->get_data(["id", "nombre", "apellidos", "email"]);
            unset($tmpProf["id_usuario"]);
            $tmpProf["carrera"] = Carrera::get_carrera_by_id($tmpProf["id_carrera_adscripcion"])->get_data();
            unset($tmpProf["id_carrera_adscripcion"]);
            $profPrestamo[] = $tmpProf;
        }
    }

    $json->agregaDatos($profPrestamo);
    $json->estableceExito(true);

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();