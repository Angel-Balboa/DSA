<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CCarrera;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\carrera\Exceptions\UsuarioDirectorYaAsignadoACarreraException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CCarrera(Usuario::get_usuario_by_id($sesion->id_usuario));
    $id_nuevoDicretor = CValidadorDeEntradas::validarEnteros($_GET["id_nuevo_director"] ?? null, "Id Nuevo Director", false, false, false, false);
    $clv_carrera = CValidadorDeEntradas::validarString($_GET["clv_carrera"] ?? null, "Clave de carrera", 10, false, false, false);

    $nuevoDirector = Usuario::get_usuario_by_id($id_nuevoDicretor);
    $carrera = Carrera::get_carrera_by_clave($clv_carrera);

    if ($admin->actualiza_director_de_carrera($nuevoDirector, $carrera)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha asignado a $nuevoDirector como director de la carrera con clave: $clv_carrera");
    }
} catch (ValorNoNumericoException|ValoresEnterosNoValidosException|ValoresDeCadenaNoValidosException|CarreraNoExistenteException|UsuarioException|UsuarioDirectorYaAsignadoACarreraException|CarreraException|UsuarioNoExistenteException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();
