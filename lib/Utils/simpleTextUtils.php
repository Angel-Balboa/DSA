<?php
namespace dsa\lib\Utils;

class SimpleTextUtils {

    public static function periodo_int2text(int $periodo, bool $short = false) {
        $txtPeriodo = "";
        switch ($periodo) {
            case 1:
                $txtPeriodo = $short ? "Ene-Abr" : "Enero - Abril";
                break;
            case 2:
                $txtPeriodo = $short ? "May-Ago" : "Mayo - Agosto";
                break;
            case 3:
                $txtPeriodo = $short ? "Sep-Dic" : "Septiembre - Diciembre";
                break;
            default:
                $txtPeriodo = "ERROR";
                break;
        }
        return $txtPeriodo;
    }
}
