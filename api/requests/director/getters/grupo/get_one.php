<?php

include_once("../../../../../init.php");

use dsa\api\model\grupo\Grupo;
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
    $dataGrupo["profesor"] = Usuario::get_usuario_by_id($dataGrupo["id_profesor"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataGrupo);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | GrupoNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();