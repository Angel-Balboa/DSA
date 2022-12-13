<?php

//Include
include_once("../../../../../init.php");

//use o importaciones de clases ocupadas
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\api\model\actividad_investigacion\ActividadInvestigacion;
use dsa\api\model\actividad_investigacion\Exceptions\ActividadInvestigacionException;
use dsa\api\model\actividad_investigacion\Exceptions\ActividadInvestigacionNoExisteException;
use dsa\api\model\actividad_investigacion\Exceptions\DatosDeActividadInvestigacionException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;

//variable para el constructor de JSON
$json = new CConstructorJSON();

//* Inicio de obtener una actividad de investigacion
try
{
    $claveactividadinvestacion = CValidadorDeEntradas::validarString($_GET["claveactividadinvestacion"] ?? null,"Clave Actividad Investigacion", 25, false, false);
    $dataactividad_investigacion = ActividadInvestigacion::get_actividadInvestigacion_by_id($claveactividadinvestacion)->get_data();
    $dataactividad_investigacion["profesor"] = Usuario::get_usuario_by_id($dataactividad_investigacion["id_profesor"])->get_data(["nombre", "apellidos"]);
    $json->estableceExito(true);
    $json->agregaDatos($dataactividad_investigacion);
}catch( ActividadInvestigacionException | ActividadInvestigacionNoExisteException | DatosDeActividadInvestigacionException | UsuarioNoExistenteException | ValoresDeCadenaNoValidosException $e)
{
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();