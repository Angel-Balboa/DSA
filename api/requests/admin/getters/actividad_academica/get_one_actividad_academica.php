<?php

//Include
include_once("../../../../../init.php");

//use o importaciones de clases ocupadas
use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaException;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaNoExistenteException;
use dsa\api\model\actividad_academica\Exceptions\DatosDeActividadAcademicaException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//* Inicio de obtener una actividad academica
try
{

    $claveactividadacademica = CValidadorDeEntradas::validarString($_GET["clv_actividad"] ?? null, "Clave Actividad Academica", 25, false, false);
    $dataactividad_academica = Actividad::get_actividad_academica_by_id($claveactividadacademica)->get_data();
    $dataactividad_academica["profesor"] = Usuario::get_usuario_by_id($dataactividad_academica["id_profesor"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataactividad_academica);
}catch (ActividadAcademicaException | ActividadAcademicaNoExistenteException | DatosDeActividadAcademicaException | UsuarioNoExistenteException | ValoresDeCadenaNoValidosException | UsuarioException $e)
{
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();