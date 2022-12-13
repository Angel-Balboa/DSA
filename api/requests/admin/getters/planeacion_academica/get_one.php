<?php

include_once("../../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $idPlaneacionAcademica = CValidadorDeEntradas::validarString($_GET["id_planeacion_academica"] ?? null, "ID Planeación Academica", 25, false, false, false);
    $dataPlaneacionAcademica = PlaneacionAcademica::get_PlaneacionAcademica_by_id($idPlaneacionAcademica)->get_data();
    $dataPlaneacionAcademica["director"] = Usuario::get_usuario_by_id($dataPlaneacionAcademica["id_director"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataPlaneacionAcademica);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | PlaneacionAcademicaNoExistenteException | ValoresDeCadenaNoValidosException | UsuarioNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();