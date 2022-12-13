<?php
namespace dsa\lib\ValidadorDeEntradas;

use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresDeCadenaNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnOpcionesNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValoresEnterosNoValidosException;
use dsa\lib\ValidadorDeEntradas\Exceptions\ValorNoNumericoException;

class CValidadorDeEntradas
{
    /**
     * Método que valida las entradas string.
     * @param $val Mixed a validar
     * @param string $nombre Nombre canonico de la variable a analizar
     * @param int $maxSize default=25; tamaño máximo permitido para esa variable
     * @param bool $base64_decoding bandera para obtener el valor decodificado en base 64
     * @param bool $allowedNull bandera para permitir o no valores nulos
     * @param bool $allowedEmpty bandera para permitir o no cadenas vacias
     * @return string
     * @throws ValoresDeCadenaNoValidosException
     */
    public static function validarString($val, string $nombre, int $maxSize=50, bool $base64_decoding=false, bool $allowedNull=false, bool $allowedEmpty=false) {

        if ($base64_decoding) {
            $val = base64_decode($val);
        }

        if (strlen($val) > $maxSize) {
            throw new ValoresDeCadenaNoValidosException("La cadena: $nombre exede del tamaño permitido", 9001);
        }

        if (!$allowedNull) {
            if (is_null($val)) {
                throw new ValoresDeCadenaNoValidosException("La cadena: $nombre no puede ser nula.", 9002);
            }
        }

        if (!$allowedEmpty) {
            if (strlen($val) < 1) {
                throw new ValoresDeCadenaNoValidosException("La cadena: $nombre no puede ser vacia", 9003);
            }
        }

        return $val;
    }

    /**
     * Método que valida un correo válido
     * @param $val
     * @return mixed
     * @throws ValoresDeCadenaNoValidosException
     */
    public static function validarEmail($val, bool $base64_decoding=false) {
        return filter_var(self::validarString($val, "correo", 250, $base64_decoding,false, false), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Método que limita la entrada a un conjunto de opciones
     * @param $val
     * @param array $opcionesValidas
     * @return mixed
     * @throws ValoresEnOpcionesNoValidosException
     */
    public static function validarOpciones($val, array $opcionesValidas) {
        if (!in_array($val, $opcionesValidas)) {
            throw new ValoresEnOpcionesNoValidosException("La opción $val no es válida.");
        }

        return $val;
    }

    /**
     * @param $val
     * @param string $nombre
     * @param bool $base64_decoding
     * @param bool $permiteNulo
     * @param bool $permiteNegativos
     * @param bool $permiteCero
     * @return false|mixed|string
     * @throws ValorNoNumericoException
     * @throws ValoresEnterosNoValidosException
     */
    public static function validarEnteros($val, string $nombre, bool $base64_decoding=false, bool $permiteNulo=false, bool $permiteNegativos=false, bool $permiteCero=true) {

        if ($base64_decoding) {
            $val = base64_decode($val);
        }

        if (!is_numeric($val))
        {
            if (!is_null($val))
            {
                throw new ValorNoNumericoException("El valor enviado no es un número, verifica los datos. ($val)");
            }
        }

        if (!$permiteNulo) {
            if (is_null($val))
            {
                throw new ValoresEnterosNoValidosException("$nombre no puede ser nulo o vacio");
            }
        }

        if (!$permiteNegativos) {
            if ($val < 0) {
                throw new ValoresEnterosNoValidosException("$nombre no puede ser negativo");
            }
        }

        if (!$permiteCero) {

            if (!is_null($val))
            {
                if ($val < 1) {
                    throw new ValoresEnterosNoValidosException("$nombre no pude ser cero");
                }
            }
        }

        return $val;
    }
}