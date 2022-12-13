<?php

include_once("../../../../../init.php");

use dsa\api\model\carrera\Carrera;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $clvCarrera = CValidadorDeEntradas::validarString($_GET["clv_carrera"] ?? null, "Clave Carrera", 25, false,false, false);
    $dataCarrera = Carrera::get_carrera_by_clave($clvCarrera)->get_data();
    $dataCarrera["director"] = Usuario::get_usuario_by_id($dataCarrera["id_director"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataCarrera);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | CarreraNoExistenteException | ValoresDeCadenaNoValidosException | UsuarioNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();