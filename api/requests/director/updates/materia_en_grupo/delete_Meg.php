<?php

include_once("../../../../../init.php");

use dsa\api\model\usuario\Usuario;
use dsa\api\model\materia_en_grupo\MateriaEnGrupo;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\direc\CMateriaEnGrupo;
use dsa\lib\Exceptions\GeneralException;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\materia\Materia;
use dsa\api\model\grupo\Grupo;
use dsa\api\model\profesor\Profesor;

$newData = array();
$json = new CConstructorJSON();
try {
    $sesion = CRequestsSesion::inits();
    $admin = new CMateriaEnGrupo(Usuario::get_usuario_by_id($sesion->id_usuario));
    $m=MateriaEnGrupo::get_MEG_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_meg"] ?? null, "Id Materia En Grupo", false, false, false, false));
    if ($admin->Eliminar_MEG($_POST["id_meg"],$m)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha eliminado la materia");
    }
} catch (GeneralException $e) {

    $json->agregaDatosError($e->getMessage(), $e->getCode());
}
$json->enviarJSON();

