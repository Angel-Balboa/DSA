<?php

namespace dsa\lib\Utils;

class GeneradorAleatorio
{
    /** Método que permite la generacion de cadena de caracteres aleatoria.
     * @param int $lenght tamaño de la cadena
     * @return string cadena aleatoria
     */
    public static function generarContrasenaAleatoria(int $lenght=16)
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $passwd = "";

        for ($i=0; $i < $lenght; $i++)
        {
            $passwd .= substr($str, rand(0,62), 1);
        }

        return $passwd;
    }
}