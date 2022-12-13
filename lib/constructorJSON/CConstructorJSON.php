<?php

namespace dsa\lib\constructorJSON;


class CConstructorJSON
{
    private $jsonDict;

    public function __construct()
    {
        $this->jsonDict = array("exito" => false, "respuesta_exito" => array("mensaje_exito" => "", "datos" => null)
        , "respuesta_error" => array("mensaje_error" => "", "codigo_error" => 0));
    }

    public function estableceExito(bool $estado=false) {
        $this->jsonDict["exito"] = $estado;
    }

    public function agregaMensajeDeExito(string $msg) {
        $this->jsonDict["respuesta_exito"]["mensaje_exito"] = $msg;
    }

    public function agregaDatos(array $datos) {
        $this->jsonDict["respuesta_exito"]["datos"] = $datos;
    }

    public function agregaDatosError(string $error_msg, int $codigo_error) {
        $this->jsonDict["respuesta_error"]["mensaje_error"] = $error_msg;
        $this->jsonDict["respuesta_error"]["codigo_error"] = $codigo_error;
    }

    public function enviarJSON() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($this->jsonDict);
    }
}