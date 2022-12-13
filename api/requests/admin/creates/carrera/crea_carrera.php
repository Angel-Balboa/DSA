<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CCarrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\carrera\Exceptions\UsuarioDirectorYaAsignadoACarreraException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioNoHabilitadoComoDirectorException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnOpcionesNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$admin = new CCarrera();
$json = new CConstructorJSON();

try {
    $id_usuario = CValidadorDeEntradas::validarEnteros($_POST["sctAgregarDirectorCarrera"] ?? null, "Id Usuario Director", false, false, false, false);
    $nombre = CValidadorDeEntradas::validarString($_POST["txtAgregarNombreCarrera"] ?? null, "Nombre de la carrera", 250, false, false, false);
    $clave = CValidadorDeEntradas::validarString($_POST["txtAgregarClaveCarrera"] ?? null, "Clave de la carrera", 10, false, false, false);
    $nivel = CValidadorDeEntradas::validarOpciones($_POST["sctAgregarNivelCarrera"] ?? null, ["Ing", "Lic", "M.I."]);

    $director = Usuario::get_usuario_by_id($id_usuario);

    $nuevaCarrera = $admin->crea_nueva_carrera($director, $nombre, $clave, $nivel);

    if (isset($nuevaCarrera)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha creado la carrera : $nombre con la clave $clave");
    }
} catch (ValorNoNumericoException|ValoresEnterosNoValidosException|ValoresDeCadenaNoValidosException|ValoresEnOpcionesNoValidosException|UsuarioNoExistenteException|CarreraException|CarreraNoExistenteException|UsuarioDirectorYaAsignadoACarreraException|UsuarioNoHabilitadoComoDirectorException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();