<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\grupo\Grupo;
use dsa\api\model\carrera\Carrera;

use dsa\api\model\plan_de_estudio\PlanDeEstudio;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Exceptions\GrupoNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $idGrupo = CValidadorDeEntradas::validarString($_GET["id_grupo"] ?? null, "ID Grupo", 25, false, false, false);
    $dataGrupo = Grupo::get_grupo_by_id($idGrupo)->get_data();
    $carga = CargaAcademica::get_cargaAcademica_by_id($dataGrupo["id_carga_academica"]);
    $dataCarga = $carga->get_data();
    $dataPlan = PlanDeEstudio::get_planDeEstudio_by_id($dataCarga["id_plan_estudios"])->get_data(["id_carrera"]);
    $dataGrupo["carrera"]=Carrera::get_carrera_by_id($dataPlan["id_carrera"])->get_data("nombre");
    $json->estableceExito(true);
    $json->agregaDatos($dataGrupo);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | GrupoNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();