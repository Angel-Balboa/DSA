<?php

include_once("../../../../../init.php");

use dsa\api\model\imparten\ImpartenEn;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\api\controller\admin\CProfesor;
use dsa\api\model\carrera\Carrera;
use dsa\lib\constructorJSON\CConstructorJSON;
use dsa\lib\Exceptions\GeneralException;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\api\controller\sesion\CRequestsSesion;
use dsa\api\model\usuario\Usuario;


$json = new CConstructorJSON();

try {
    $sesion = CRequestsSesion::inits();
    $admin = new CProfesor(Usuario::get_usuario_by_id($sesion->id_usuario));
    if (isset($_POST["id_profesor"])) {
        $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_POST["id_profesor"], "Id de profesor", false, false, false, false));
    } elseif (isset($_POST["email_profesor"])) {
        $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_POST["email_profesor"]));
    } else {
        throw new ProfesorException("Se debe proporcionar el id del profesor o su email", -86);
    }

    $newImparteEn = $_POST["imparteEn"] ?? array(); // nuevo array de asignación
    $oldImarteEn = $profesor->get_carreras_de_imparticion(true); // array actual
    $todasCarreras = array();

    // quitamos la carrera de adscripción del profesor.
    foreach (Carrera::get_all() as $id_carrera) {
        if ($id_carrera != $profesor->get_data("id_carrera_adscripcion")) {
            $todasCarreras[] = $id_carrera;
        }
    }

    $paraQuitar = array();
    $paraAgregar = array();

    // obtenemos las carreras a quitar
    foreach ($oldImarteEn as $old) {
        if (!in_array($old, $newImparteEn)) {
            $paraQuitar[] = Carrera::get_carrera_by_id($old);
        }
    }
    // obtenemos las carreras a agregar
    foreach($newImparteEn as $new) {
        if (!in_array($new, $oldImarteEn)) {
            $paraAgregar[] = Carrera::get_carrera_by_id($new);
        }
    }

    if ($admin->quita_carreras_para_impartir($profesor, $paraQuitar) && $admin->agrega_carreras_para_impartir($profesor, $paraAgregar)) {
        $json->estableceExito(true);
        $json->agregaMensajeDeExito("Se han actualizado correctamente las carreras en donde imparte el profesor");
        $tmpData["carrera_adscripcion"] = $profesor->get_carrera_adscripcion()->get_data(["id", "nombre", "clave"]);
        $tmpData["imparteEn"] = array();

        foreach ($profesor->get_carreras_de_imparticion() as $id_carrera) {
            $tmpData["imparteEn"][] = Carrera::get_carrera_by_id($id_carrera)->get_data(["id", "nombre", "clave"]);
        }

        $json->agregaDatos($tmpData);
    }

} catch (GeneralException $e) {
    echo $e->getMessage();
}

$json->enviarJSON();
