<?php

namespace dsa\lib\conexionSQL;

use dsa\lib\conexionSQL\Exceptions\CConnexionException;

/**
 * Class CConexionMySQL
 * @package temphum_monitor\modelo\conexionSQL
 *
 * Clase que se encarga de generar la conexión a la base de datos.
 */
class CConexionMySQL
{
    private $Config; /* Instancia de la clase CConfiguracionSQL */
    protected $bdCon; /* Puntero-conexión a la base de datos */

    /**
     * CConexionMySQL constructor.
     * @param string $tipoUsuario Tipo de usuario con el que se realizará la conexión
     */
    public function __construct(String $tipoUsuario="default")
    {
        $this->Config = CConfiguracionSQL::getInstance($tipoUsuario); /* se obtiene una instancia de la clase
 CConfiguraciónSQL */
        $this->bdCon = null;
        try{
            $this->conectarABaseDatos();
        }
        catch (CConnexionException $e)
        {
            $e->guardarLog(); /* en caso de error se guardará un log con la descripción del problema */
        }
    }

    /**
     * Destructor de la clase
     */
    public function __destruct()
    {
        if (!is_null($this->bdCon)) $this->bdCon->close(); // se cierra la conexión a la base de datos.
    }

    /**
     * Método que realiza la conexión a la base de datos utilizando los datos de configuración. En caso de errror se
     * guarda un mensaje en el log.
     *
     * @throws CConnexionException
     */
    private function conectarABaseDatos()
    {
        $this->bdCon = new \mysqli($this->Config->get("Host"), $this->Config->get("User"), $this->Config->get("Pass"), $this->Config->get("DataBase"));

        if ($this->bdCon->connect_error)
        {
            echo "No se pudo contectar a la base de datos";
            $this->bdCon = null;
            throw new CConnexionException("No se pudo contectar a la base de datos");
        }
    }
}