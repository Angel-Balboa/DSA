<?php

//Include
include_once("../../../../../init.php");

//use o importaciones de clases que se ocupan
use dsa\api\model\prestamo\Exceptions\SolicitudNoEncontradaException;
use dsa\api\model\prestamo\Exceptions\SolicitudPrestamoException;
use dsa\api\model\prestamo\SolicitudPrestamo;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\usuario\Usuario;
use dsa\lib\Exceptions\FormatoDeFechaException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//inicio de obtener un prestamo
try
{
    $clvprestamo = CValidadorDeEntradas::validarString($_GET["clv_prestamo"] ?? null, "Clave Prestamo",25, false, false);
    $dataprestamos = SolicitudPrestamo::get_solicitudDePrestamo_by_id($clvprestamo)->get_data();
    $dataprestamos["director"] = Usuario::get_usuario_by_id($dataprestamos["id_director"])->get_data(["nombre","apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataprestamos);
}catch (ValoresDeCadenaNoValidosException | SolicitudNoEncontradaException | SolicitudPrestamoException | ProfesorNoExisteException | UsuarioException | UsuarioNoExistenteException | FormatoDeFechaException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();