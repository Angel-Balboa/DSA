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
    $meg=MateriaEnGrupo::get_MEG_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_meg"] ?? null, "Id Materia En Grupo", false, false, false, false));
    $admin = new CMateriaEnGrupo(Usuario::get_usuario_by_id($sesion->id_usuario));
    if(isset($_POST["materia"])){
        $newData["materia"]=Materia::get_materia_by_id(CValidadorDeEntradas::validarEnteros($_POST["materia"] ?? null, "Id Materia", false, false, false, false));
    }
    if(isset($_POST["id_profesor"])){
        $newData["profesor"]=Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"]?? null, "Id Profesor", false, false, false, false));
    }
    if(isset($_POST["grupo"])){
        $newData["grupo"]=Grupo::get_grupo_by_id(CValidadorDeEntradas::validarEnteros($_POST["grupo"]?? null, "Id Grupo", false, false, false, false));
    }
    $newData["modificador_horas"]=$_POST["modificador_horas"];
    $newData["alumnos_estimados"]=$_POST["alumnos_estimados"];
    $newData["equivalente"]="";
    if ($admin->Actualizar_Datos($meg, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos de la materia");
    }
} catch (GeneralException $e) {

    $json->agregaDatosError($e->getMessage(), $e->getCode());
}
$json->enviarJSON();

