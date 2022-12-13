<?php

namespace dsa\_conf;

class Config
{
    public static function get_template_path() : String {
        return $GLOBALS["dsa_root"] . '/templates';
    }
}