<?php

include_once("../../../../../init.php");

use dsa\api\controller\usuario\UsuarioAdministrador;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

$admin = new UsuarioAdministrador();
$json = new CConstructorJSON();

try {
    $profesor_correo = CValidadorDeEntradas::validarEmail($_POST["usuario_correo"] ?? null, false);
    $se_agrega = $_POST["se_agrega"] ?? null;
    $json->agregaDatos(array($se_agrega));
    $id_carrera = CValidadorDeEntradas::validarEnteros($_POST["id_carrera"] ?? null, "Carrera", false, false, false, false);

    if (!is_null($se_agrega)) {
        $profesor = Profesor::get_profesor_by_email($profesor_correo);
        $carrera = Carrera::get_carrera_by_id($id_carrera);
        if ($se_agrega == "true") {
            $admin->asigna_profesor_para_impartir_en_carreras($profesor, array($carrera));
            $json->estableceExito(true);
            $json->agregaMensajeDeExito("Se asignó la carrera con éxito");
        } else {
            $admin->quitar_asignacion_de_profesor_para_impartir_en_carrera($profesor, $carrera);
            $json->estableceExito(true);
            $json->agregaMensajeDeExito("Se ha quitado la asignación de la carrera con éxito");
        }
    } else {
        throw new \Exception("El parámetro se_agrega no ha sido enviado", 2);
    }
}


catch (ValoresDeCadenaNoValidosException | ValorNoNumericoException | ValoresEnterosNoValidosException | ProfesorNoExisteException | CarreraNoExistenteException | CarreraException | CarreraNoAgregadaException | ProfesorNoAgregadoException | \Exception $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();