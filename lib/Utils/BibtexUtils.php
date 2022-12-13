<?php

namespace dsa\lib\Utils;

use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoException;

class BibtexUtils
{
    public static function crea_bibtex(array $newData) : String {
        $required_keys = ["citation-key", "type_product"];

        foreach($required_keys as $required_key) {
            if (!in_array($required_key, array_keys($newData)) or !isset($newData[$required_key])) throw new ProductoCientificoException("La clave $required_key es obligatoria", 15051);
        }

        $bibtex = "@" . $newData["type_product"] . "{" . $newData["citation-key"] . ",";
        unset($newData["citation-key"]);
        unset($newData["type_product"]);

        $tmpArray = array();
        foreach ($newData as $key => $val) {
            $tmpArray[] = "$key={" . $val . "}";
        }

        $bibtex .= implode(", ", $tmpArray) . "}";

        return $bibtex;
    }
}