<?php
namespace dsa\lib\conexionSQL;

use dsa\lib\conexionSQL\CConexionMySQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;

class COperacionesSQL extends CConexionMySQL
{

    private $sentenciaSQL;
    private $sentenciaTipos;

    /**
     * COperacionesSQL constructor.
     * @param string $tipoUsuario tipoo de usuario para conección de la base de datos.
     */
    private function __construct(String $tipoUsuario="default")
    {
        parent::__construct($tipoUsuario);

        $this->sentenciaSQL = null;
        $this->sentenciaTipos = null;
    }

    public static function getInstance(?COperacionesSQL &$cop=null) : COperacionesSQL {
        if (is_null($cop)) {
            $tipoUsuario = "default";

            if (isset($_SESSION["tipo_usuario"])) {
                switch ($_SESSION["tipo_usuario"]) {
                    case 'admin':
                        $tipoUsuario = "admi";
                        break;
                    case 'director':
                        $tipoUsuario = "direc";
                        break;
                    case 'RRHH':
                        $tipoUsuario = "rrhh";
                        break;
                    case 'profesor':
                        $tipoUsuario = "profe";
                        break;
                    default:
                        $tipoUsuario = "default";
                        break;
                }
            }
            return new COperacionesSQL($tipoUsuario);
        } else {
            return $cop;
        }
    }

    /**
     * Destructor de la clase
     */
    public function __destruct()
    {
        if (!is_null($this->sentenciaSQL)) $this->sentenciaSQL->close();
        parent::__destruct();
    }

    public function obtenerUltimoIdInsertado() {
        return $this->bdCon->insert_id;
    }

    /**
     * @param String $sqlquery
     * @param String|null $types
     * @param array|null $vars
     * @return array|false|int|mixed|null
     * @throws CConnexionException
     * @throws SQLTransactionException
     */
    public function exec(String $sqlquery, ?String $types=null, ?array $vars=null) {

        switch (strtoupper(explode(" ", $sqlquery)[0])) {
            case "SELECT":
                if (is_null($types)) {
                    return $this->ejecutarNoTranscactionalSimple($sqlquery);
                } else {
                    if (is_null($vars)) throw new SQLTransactionException("No se han proporcionado los datos de consulta", -101);
                    if (strlen($types) != count($vars)) throw new SQLTransactionException("No coinciden los tipos y la cantidad de valores", -102);

                    $this->prepararSentenciaSQL($sqlquery, $types);
                    return call_user_func_array([$this, 'ejecutarNoTranscactional'], $vars);
                }
                break;
            case "INSERT":
            case "UPDATE":
            case "DELETE":
                if (is_null($types)) {
                    return $this->ejecutarTransactionalSimple($sqlquery);
                } else {
                    if (is_null($vars)) throw new SQLTransactionException("No se han proporcionado los datos de consulta", -101);
                    if (strlen($types) != count($vars)) throw new SQLTransactionException("No coinciden los tipos y la cantidad de valores", -102);

                    $this->prepararSentenciaSQL($sqlquery, $types);
                    return call_user_func_array([$this, 'ejecutarTransactional'], $vars);
                }
                break;
            default:
                throw new SQLTransactionException("Operación no implementada", -100);
        }
    }

    /**
     * Método que permite ejecutar una sentencia no-transaccional
     * @param string $sql query a ser ejecutada
     * @return array|null resultado de la consulta (select)
     */
    private function ejecutarNoTranscactionalSimple(string $sql)
    {
        $res =  $this->bdCon->query($sql);

        $results = array();

        if ($res) {
            while ($fila = $res->fetch_assoc())
            {
                $results[] = $fila;
            }
        }

        return $results;
    }

    /**
     * Método que permite ejecutar una sentencia transaccional
     * @param string $sql query para ser ejecutada
     * @return int|null número de final afectadas a partir de la query
     * @throws SQLTransactionException
     */
    private function ejecutarTransactionalSimple(string $sql)
    {
        $res = $this->bdCon->query($sql);

        if ($res === TRUE)
        {
            return $this->bdCon->affected_rows;
        }
        else
        {
            throw new SQLTransactionException($this->bdCon->error);
        }
    }

    /**
     * Métódo que prepara una sentencia SQL para su ejecución
     * @param string $sentenciaSQL Cadena que contiene la sentencia a ser ejecutada
     * @param $tipos Una cadena que contiene uno o más caracteres que especifican los tipos para el correspondiente
     * enlazado de variables (i: entero, d: double, s: string, b: blob y se envía en paquetes)
     * @throws CConnexionException
     */
    private function prepararSentenciaSQL($sentenciaSQL, $tipos)
    {
        if (!is_null($this->sentenciaSQL)) $this->sentenciaSQL->close();
        $this->sentenciaSQL = $this->bdCon->prepare($sentenciaSQL);

        if ($this->sentenciaSQL === false)
        {
            echo $this->bdCon->error;
            $this->sentenciaSQL = null;
            throw new CConnexionException("La sentencia SQL no es correcta. Verifiquela");
        }

        $this->sentenciaTipos = $tipos;
    }

    /**
     * Método que ejecuta una sentencia de tipo transaccional (DELETE, UPDATE, INSERT) ya preparada mediante
     * COperacionesSQL::prepararSentenciaSQL
     * @param mixed lista de valores / variables que serán enlazadas a los tipos del método COperacionesSQL::prepararSentenciaSQL
     * @return int Número de filas afectadas por la sentencia preparada.
     * @throws SQLTransactionException
     */
    private function ejecutarTransactional()
    {
        $num_args = func_num_args();
        $args_list = func_get_args();

        $a_params = array();

        $a_params[] = $this->sentenciaTipos;

        for($i = 0; $i < $num_args; $i++)
        {
            $a_params[] = & $args_list[$i];
        }

        call_user_func_array(array($this->sentenciaSQL, 'bind_param'), $a_params);
        $res = -1;

        try {
            if ($this->sentenciaSQL->execute()===false)
            {
                throw new SQLTransactionException($this->sentenciaSQL->errno . " - " . $this->sentenciaSQL->error);
            }
            else {
                $res = $this->sentenciaSQL->affected_rows;
            }
        }
        catch (\Exception $e) {
            throw new SQLTransactionException("Error desconocido.", 666);
        }

        if ($res < 0 || is_null($res))
        {
            throw new SQLTransactionException("Error al realizar la sentecia" . " - " . $this->sentenciaSQL->error);
        }

        return $res;
    }

    /**
     * Método que ejecuta una sentencia de tipo NO transaccional (SELECT) ya preparada mediante
     * COperacionesSQL::prepararSentenciaSQL
     * @param mixed lista de valores / variables que serán enlazadas a los tipos del método COperacionesSQL::prepararSentenciaSQL
     * @return array Con las filas obtenidas mediate la ejecución de la sentencia preparada
     * @throws SQLTransactionException
     */
    private function ejecutarNoTranscactional()
    {
        $num_args = func_num_args();
        $args_list = func_get_args();

        $a_params = array();

        $a_params[] = $this->sentenciaTipos;

        for($i = 0; $i < $num_args; $i++)
        {
            $a_params[] = & $args_list[$i];
        }

        call_user_func_array(array($this->sentenciaSQL, 'bind_param'), $a_params);

        if ($this->sentenciaSQL->execute()===false)
        {
            throw new SQLTransactionException($this->sentenciaSQL->errno . " - " . $this->sentenciaSQL->error);
        }

        $res = $this->sentenciaSQL->get_result();

        if ($res === false)
        {
            throw new SQLTransactionException($this->sentenciaSQL->errno . " - " . $this->sentenciaSQL->error);
        }

        $results = array();

        while ($fila = $res->fetch_assoc())
        {
            $results[] = $fila;
        }

        return $results;
    }
}