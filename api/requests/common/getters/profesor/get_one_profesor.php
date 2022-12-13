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
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\api\controller\sesion\CRequestsSesion;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//inicio de obtener un profesor
try
{
    $sesion = CRequestsSesion::inits();

    if (isset($_GET["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false, false));
    } elseif (isset($_GET["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"], false));
    } else {
        throw new ProfesorException("Se esperaba el Id o el Email del profesor", -6003);
    }

    $datosprofesor = $profesor->get_data();
    $datosprofesor["usuario"] = Usuario::get_usuario_by_id($datosprofesor["id_usuario"])->get_data();
    $json->estableceExito(true);
    $json->agregaDatos($datosprofesor);
}catch(GeneralException $e)
{
    $json->agregaDatosError($e->getMessage(),$e->getCode());
}

$json->enviarJSON();