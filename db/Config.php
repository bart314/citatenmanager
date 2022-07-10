<?php

namespace App\Database;

class Config
{
    static $confArray;

    public static function read($name)
    {
        return self::$confArray[$name];
    }

    public static function write($name, $value)
    {
        self::$confArray[$name] = $value;
    }

}

Config::write('db.host', '127.0.0.1');
Config::write('db.basename', 'citaten');
Config::write('db.user', 'citaten');
Config::write('db.password', 'hoppekee');
