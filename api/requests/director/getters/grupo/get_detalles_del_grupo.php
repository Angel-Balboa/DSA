<?php

include_once("../../../../../init.php");

use dsa\api\model\grupo\Grupo;
use dsa\api\model\materia\Materia;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\materia_en_grupo\MateriaEnGrupo;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\Exceptions\GeneralException;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    $grupo = Grupo::get_grupo_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_grupo"] ?? null, "Id del grupo", false, false, false, false));

    $datosGrupo = $grupo->get_data();

    $materiasEnGrupo = array();

    foreach (MateriaEnGrupo::get_all(["grupo" => $grupo]) as $id) {
        $tmpMateriaEnGrupo = MateriaEnGrupo::get_MEG_by_id($id)->get_data(["id", "id_materia"]);
        $tmpMateriaEnGrupo["materia"] = Materia::get_materia_by_id($tmpMateriaEnGrupo["id_materia"])->get_data(["id", "nombre", "clave"]);

        unset($tmpMateriaEnGrupo["id_materia"]);

        $materiasEnGrupo[] = $tmpMateriaEnGrupo;
    }

    $datosGrupo["materias"] = $materiasEnGrupo;

    $json->agregaDatos($datosGrupo);
    $json->estableceExito(true);
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
