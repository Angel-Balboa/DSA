<?php

include_once("../../../../../init.php");

use dsa\api\model\carga_academica\CargaAcademica;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carga_academica\Exceptions\CargaAcademicaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $idCargaAcademica = CValidadorDeEntradas::validarString($_GET["id_carga_academica"] ?? null, "ID Carga Academica", 25, false, false, false);
    $dataCargaAcademica = CargaAcademica::get_cargaAcademica_by_id($idCargaAcademica)->get_data();
    $dataCargaAcademica["profesor"] = Usuario::get_usuario_by_id($dataCargaAcademica["id_profesor"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataCargaAcademica);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | CargaAcademicaNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();