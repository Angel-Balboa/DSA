<?php

include_once("../../../../../init.php");

use dsa\api\model\grupo\Exceptions\GrupoException;
use dsa\api\model\grupo\Grupo;
use dsa\api\controller\direc\CGrupo;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\Exceptions\GeneralException;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();

    $grupo = Grupo::get_grupo_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_grupo"] ?? null, "Id del Grupo", false, false, false, false));

    if ($grupo->esta_finalizado()) {
        throw new GrupoException("El grupo ya se encuentra finalizado", -8560);
    } else {
        $admin = new CGrupo(Usuario::get_usuario_by_id($sesion->id_usuario));
        $admin->finalizar_grupo($grupo);

        $json->agregaMensajeDeExito("Se ha finalizado el grupo: $grupo");
        $json->estableceExito(true);

    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
