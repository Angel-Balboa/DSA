<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CCarrera;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioNoHabilitadoComoDirectorException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnOpcionesNoValidosException;


$newData = array();
$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CCarrera(Usuario::get_usuario_by_id($sesion->id_usuario));
    $carrera = Carrera::get_carrera_by_clave(CValidadorDeEntradas::validarString($_POST["clv_carrera"] ?? null, "Clave de carrera", 10));

    if (isset($_POST["nuevo_director"])) {
        $newData["id_director"] = CValidadorDeEntradas::validarEnteros($_POST["nuevo_director"] ?? null, "Id Nuevo Director", false, false, false, false);
    }

    if (isset($_POST["nuevo_nombre"])) {
        $newData["nombre"] = CValidadorDeEntradas::validarString($_POST["nuevo_nombre"], "nuevo nombre", 250, false);
    }
    if (isset($_POST["nueva_clave"])) {
        $newData["clave"] = CValidadorDeEntradas::validarString($_POST["nueva_clave"], "nueva clave", 10, false);
    }
    if (isset($_POST["nuevo_nivel"])) {
        $newData["nivel"] = CValidadorDeEntradas::validarOpciones($_POST["nuevo_nivel"], ["Ing", "Lic", "M.I."]);
    }

    if ($admin->actualiza_datos($carrera, $newData)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado los datos de la carrera");
    }
} catch (CarreraNoExistenteException|UsuarioNoHabilitadoComoDirectorException|CarreraException|ValoresEnOpcionesNoValidosException|ValoresDeCadenaNoValidosException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
