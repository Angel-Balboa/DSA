<?php

namespace dsa\lib\conexionSQL;

/**
 * Class CConfiguracionSQL
 * @package temphum_monitor\modelo\conexionSQL
 *
 * Clase que se encarga de obtener los datos de configuración para la conexión a la base de datos.
 */
class CConfiguracionSQL
{
    private static $instance;
    private $data;

    /**
     * CConfiguracionSQL constructor.
     * @param string $tipo: tipo de usuario con el que se realizará la conexión a la base de datos.
     */
    private function __construct(String $tipo="default")
    {
        $json = file_get_contents(__DIR__ . '/app.json');
        $dataTmp = json_decode($json, true);
        $this->data = ["DataBase" => $dataTmp["DataBase"]["Schema"], "Host" => $dataTmp["DataBase"]["Host"], "User" => $dataTmp["DataBase"]["TypeUser"][$tipo]["User"], "Pass" => $dataTmp["DataBase"]["TypeUser"][$tipo]["Pass"]];

        unset($dataTmp);
    }

    /**
     * @param string $tipo
     * @return CConfiguracionSQL: instancia de la case configuración que permitirá obtener los datos de configuración
     */
    public static function getInstance($tipo="default")
    {
        if (self::$instance == null)
        {
            self::$instance = new CConfiguracionSQL($tipo);
        }

        return self::$instance;
    }

    public function get($key)
    {
        return $this->data[$key];
    }
}
?>