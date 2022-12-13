<?php

namespace dsa\lib\Utils;

class DataChecker
{

    public static function isAssoc(array $array) : bool {
        if (array() === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function check_required_fields(array $incoming_dictionary, array $required_fields) : bool {
        $ban = true;
        $keys = array_keys($incoming_dictionary);

        foreach ($required_fields as $field) {
            if (!in_array($field, $keys)) {
                $ban = false;
                break;
            }
        }
        return $ban;
    }

    public static function check_instance_of($object, String $spectedClass) : bool {

        if (is_string($object) || is_numeric($object)) {
            return false;
        } else {
            $full_class = get_class($object);
            $str_paths = explode("\\", $full_class);
            $length_paths = count($str_paths);

            return $str_paths[$length_paths - 1] == $spectedClass;
        }
    }
}