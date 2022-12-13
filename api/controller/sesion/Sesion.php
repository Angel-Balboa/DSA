<?php

namespace dsa\api\controller\sesion;

use dsa\api\controller\sesion\Exceptions\VariableDeSessionNoExisteException;

class Sesion
{
    const SESSION_STARTED = true;
    const SESSION_NOT_STARTED = false;

    // estado de la session
    private $sessionState = self::SESSION_NOT_STARTED;
    // instancia de la clase
    private static $instance;

    /**
     * Constructor vacio
     */
    private function __construct() {}

    /**
     * Retorna la instancia de la 'Sesion'
     * La session es automáticamente inicializada.
     * @return Sesion
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Sesion();
        }

        self::$instance->startSession();
        return self::$instance;
    }

    public function getState() {
        return $this->sessionState;
    }

    /**
     * Inicia o reinicia la session
     * @return bool Verdadero si la sesión fue inicializada, si no Falso
     */
    public function startSession() {
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            $this->sessionState = session_start();
        }
        return $this->sessionState;
    }

    /**
     * Inicializa o guarda variables en el vector de sesiones de PHP ($_SESSION)
     * Ejemplo: $instance->foo = 'bar'
     *
     * @param $name Nombre de la variable a almacenar en el vector de sesiones
     * @param $value valor a almacenar
     * @return void
     */
    public function __set($name, $value) {
        $_SESSION[$name] = $value;
    }

    /**
     * Obtiene datos desde el vector de sesiones de PHP ($_SESSION)
     * Ejemplo: echo $instance->foo;
     *
     * @param $name mixed de la variable que a obtener
     * @return mixed datos almacenados en el vector de sesiones
     * @throws VariableDeSessionNoExisteException
     */
    public function __get($name) {

        switch ($name) {
            case 'is_logged':
                return $_SESSION["is_logged"] ?? false;
                break;
            default:
                if (isset($_SESSION[$name])) {
                    return $_SESSION[$name];
                } else {
                    throw new VariableDeSessionNoExisteException("La variable $name no esta definida.", 20001);
                }
        }
    }

    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

    public function __unset($name) {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        } else {
            throw new VariableDeSessionNoExisteException("La variable $name no esta definida", 20002);
        }
    }

    public function destroy() {
        if ($this->sessionState == self::SESSION_STARTED) {
            $this->sessionState = !session_destroy();
            unset($_SESSION);

            return !$this->sessionState;
        }

        return false;
    }
}