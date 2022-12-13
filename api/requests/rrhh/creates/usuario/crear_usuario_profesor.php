<?php

include_once("../../../../../init.php");

use dsa\api\controller\rrhhs\CUsuario;
use dsa\api\model\carrera\Carrera;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\GeneradorAleatorio;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;

$admin = new CUsuario();
$json = new CConstructorJSON();

try {
    $email_usuario = CValidadorDeEntradas::validarEmail($_POST["usuario_email"] ?? null, false);
    $nombre = CValidadorDeEntradas::validarString($_POST["perfil_nombre"] ?? null, "Nombre del usuario", 150, false, false, false);
    $apellidos = CValidadorDeEntradas::validarString($_POST["perfil_apellidos"] ?? null, "Apellidos del usuario", 150, false, false, false);
    $telefono = CValidadorDeEntradas::validarString($_POST["perfil_telefono"] ?? null, "Teléfono del usuario", 10, false, true, true);
    $telefono = strlen($telefono) != 10 ? null : $telefono;

    $extension = CValidadorDeEntradas::validarString($_POST["perfil_extension"] ?? null, "Extensión institucional del usuario", 4, false, true, true);
    $extension = strlen($extension) != 4 ? null : $extension;

    $carrera = Carrera::get_carrera_by_id(CValidadorDeEntradas::validarEnteros($_POST["perfil_profesor_carrera_adscripcion"] ?? null, "Id de carrera de adscripcion", false, false, false, false));
    $nivel_adscripcion = CValidadorDeEntradas::validarOpciones($_POST["perfil_profesor_nivel_adscripcion"] ?? "Ing.", ["Lic.", "Ing.", "M.C.", "M.A.", "Dr."]);
    $tipo_contrato = CValidadorDeEntradas::validarOpciones($_POST["perfil_profesor_tipo_contrato"] ?? "P.A", ["P.A", "P.T.C"]);
    $categoria = CValidadorDeEntradas::validarOpciones($_POST["perfil_profesor_categoria"] ?? "A", ["A", "B", "C", "D"]);
    $inicio_contrato = CValidadorDeEntradas::validarString($_POST["perfil_profesor_inicio_contrato"] ?? "now", "Fecha de inicio de contrato", 10, false, false, false);
    $fin_contrato = CValidadorDeEntradas::validarString($_POST["perfil_profesor_contrato_indefinido"] ?? null, "Fecha de fin de contrato", 10, false, true, true);

    $password = GeneradorAleatorio::generarContrasenaAleatoria();

    if ($admin->crea_usuario_profesor($email_usuario, $password, $nombre, $apellidos, $telefono, $extension, null, $carrera, $nivel_adscripcion, $tipo_contrato, $categoria, $inicio_contrato, $fin_contrato)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se ha generado el nuevo usuario con el email: $email_usuario");
        $json->agregaDatos(["pasw" => $password]);
    }
} catch (GeneralException $ex) {
    $json->agregaDatosError($ex->getMessage(), $ex->getCode());
}

$json->enviarJSON();
