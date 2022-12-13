<?php

include_once("../../../../../init.php");

use dsa\api\controller\admin\CUsuario;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\GeneradorAleatorio;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\carrera\Carrera;

$admin = new CUsuario();
$json = new CConstructorJSON();
$ban_exito = false;
try {
    $usuario_email = CValidadorDeEntradas::validarEmail($_POST["usuario_email"] ?? null, false);
    $usuario_tipo = CValidadorDeEntradas::validarOpciones($_POST["usuario_tipo"] ?? "", array("profesor", "director", "RRHH"));
    $new_password = GeneradorAleatorio::generarContrasenaAleatoria();
    $perfil_nombre = CValidadorDeEntradas::validarString($_POST["perfil_nombre"] ?? null, "nombre", 150, false, false, false);
    $prefil_apellidos = CValidadorDeEntradas::validarString($_POST["perfil_apellidos"] ?? null, "Apellidos", 150, false, false, false);

    $perfil_telefono = CValidadorDeEntradas::validarString($_POST["perfil_telefono"] ?? null, "Teléfono del usuario", 10, false, true, true);
    $perfil_telefono = strlen($perfil_telefono) != 10 ? null : $perfil_telefono;

    $perfil_extension = CValidadorDeEntradas::validarString($_POST["perfil_extension"] ?? null, "Extensión institucional del usuario", 4, false, true, true);
    $perfil_extension = strlen($perfil_extension) != 4 ? null : $perfil_extension;

    $perfil_foto = null;

    switch ($usuario_tipo) {
        case "director":
            $admin->crea_usuario_director($usuario_email, $new_password, $perfil_nombre, $prefil_apellidos, $perfil_telefono, $perfil_extension, $perfil_foto);
            $ban_exito = true;
            $json->agregaMensajeDeExito("Se ha generado el Nuevo Director con éxito.");
            break;
        case "RRHH":
            $admin->crea_usuario_RRHH($usuario_email, $new_password, $perfil_nombre, $prefil_apellidos, $perfil_telefono, $perfil_extension, $perfil_foto);
            $ban_exito = true;
            $json->agregaMensajeDeExito("Se ha generado el usuario de Recursos Humanos con éxito.");
            break;
        case "profesor":
            $profesor_nivel_adscripcion = CValidadorDeEntradas::validarOpciones($_POST["perfil_profesor_nivel_adscripcion"] ?? "", array_keys(Profesor::obten_niveles_de_ascripcion()));
            $profesor_tipo_contrato = CValidadorDeEntradas::validarOpciones($_POST["perfil_profesor_tipo_contrato"] ?? "", array_keys(Profesor::obten_tipos_contrato()));
            $profesor_categoria = CValidadorDeEntradas::validarOpciones($_POST["perfil_profesor_categoria"] ?? "", array_keys(Profesor::obten_categorias()));
            $profesor_inicio_contrato = CValidadorDeEntradas::validarString($_POST["perfil_profesor_inicio_contrato"] ?? "now", "Fecha Inicio de contrato", 10, false, false, false);
            $profesor_fin_contrato = CValidadorDeEntradas::validarString($_POST["perfil_profesor_contrato_indefinido"] ?? "", "Fecha Fin de contrato", 10, false, false, true);
            $profesor_fin_contrato = strlen($profesor_fin_contrato) != 4 ? null : $profesor_fin_contrato;

            $id_carrera_adscripcion = CValidadorDeEntradas::validarEnteros($_POST["perfil_profesor_carrera_adscripcion"] ?? -1, "Carrera de adscripción", false, false, false, false);
            $carreraAdscripcion = Carrera::get_carrera_by_id($id_carrera_adscripcion);

            $admin->crea_usuario_profesor($usuario_email, $new_password, $perfil_nombre, $prefil_apellidos, $perfil_telefono, $perfil_extension, $perfil_foto, $carreraAdscripcion, $profesor_nivel_adscripcion, $profesor_tipo_contrato, $profesor_categoria, $profesor_inicio_contrato, $profesor_fin_contrato);
            $ban_exito = true;
            break;
        default:
            throw new GeneralException("No se puede realizar esta acción, consulte con el administrador del sistema", -85);
    }

    if ($ban_exito) {
        // todo: Generar la rutina para el envío de la contraseña vía email.
        $json->estableceExito(true);
        $json->agregaDatos(array("pasw" => $new_password));
    }
} catch (GeneralException $e) {
    $json->agregaDatosError($e->getMessage(), $e->getCode());
}

$json->enviarJSON();


