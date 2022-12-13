<?php

include_once("../../../../../init.php");

use dsa\api\model\usuario\Usuario;
use dsa\api\model\grupo\Grupo;
use dsa\api\model\materia\Materia;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\direc\CMateriaEnGrupo;
use dsa\lib\Exceptions\GeneralException;
use dsa\api\controller\sesion\CRequestsSesion;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    $grupo = Grupo::get_grupo_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_grupo"] ?? null, "Id del Grupo", false, false, false, false));

    $materia = Materia::get_materia_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_materia"] ?? null, "Id único de la Materia", false, false, false, false));

    $profesor = null;
    if (isset($_POST["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"], "Id del Profesor", false, false, false, false));
    } elseif (isset($_POST["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_POST["email_profesor"], false));
    }

    $alumnos_estimados = 30;
    if (isset($_POST["alumnos_estimados"])) {
        $alumnos_estimados = intval($_POST["alumnos_estimados"]);
    }

    $modificador = 0;
    if (isset($_POST["modificador"])) {
        $modificador = intval($_POST["modificador"]);
    }

    $admin = new CMateriaEnGrupo(Usuario::get_usuario_by_id($sesion->id_usuario));

    $tmpMeg = $admin->crea_nueva_MeG($grupo, $materia, $profesor, $alumnos_estimados, $modificador);

    if (!is_null($tmpMeg)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha generado ");
        $tmpDataMeg = $tmpMeg->get_data();
        $tmpDataMeg["materia"] = $materia->get_data(["id", "nombre", "clave"]);
        $json->agregaDatos($tmpDataMeg);
    }

} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
