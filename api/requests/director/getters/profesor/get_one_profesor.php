<?php

//Include
include_once("../../../../../init.php");

//use o importaciones de clases que se ocupan
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//inicio de obtener un profesor
try
{
    $clave_profesor = CValidadorDeEntradas::validarString($_GET["clave_profesor"] ?? null, "Clave CProfesor",25, false, false);
    $datosprofesor = Profesor::get_profesor_by_id($clave_profesor)->get_data();
    $datosprofesor["profesor"] = Usuario::get_usuario_by_id($datosprofesor["id_profesor"])->get_data(["nombre","apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($datosprofesor);
}catch(ProfesorException | ProfesorNoExisteException | ValoresDeCadenaNoValidosException | UsuarioNoExistenteException $e)
{
    $json->agregaDatosError($e->getMessage(),$e->getCode());
}

$json->enviarJSON();