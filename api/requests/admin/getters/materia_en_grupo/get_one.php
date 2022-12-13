<?php

include_once("../../../../../init.php");

use dsa\api\model\materia_en_grupo\MateriaEnGrupo;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoNoExisteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $idMateriaEnGrupo = CValidadorDeEntradas::validarString($_GET["id_materia_en_grupo"] ?? null, "ID Materia en Grupo", 25, false, false, false);
    $dataMateriaEnGrupo = MateriaEnGrupo::get_MEG_by_id($idMateriaEnGrupo)->get_data();
    $dataMateriaEnGrupo["director"] = Usuario::get_usuario_by_id($dataMateriaEnGrupo["id_materia_en_grupo"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataMateriaEnGrupo);
} catch (ValorNoNumericoException | ValoresEnterosNoValidosException | MateriaEnGrupoNoExisteException | ValoresDeCadenaNoValidosException | UsuarioNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();