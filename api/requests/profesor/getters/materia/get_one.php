<?php

include_once("../../../../../init.php");

use dsa\api\model\materia\Materia;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $clvMateria = CValidadorDeEntradas::validarString($_GET["clv_materia"] ?? null, "Clave Materia", 25, false, false, false);
    $dataMateria = Materia::get_materia_by_clave($clvMateria)->get_data();
    $dataMateria["profesor"] = Usuario::get_usuario_by_id($dataMateria["id_materia"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataMateria);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | MateriaNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();