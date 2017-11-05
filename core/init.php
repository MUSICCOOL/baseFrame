<?php

class init
{
    public static $config = array();

    public static function getConfig($map)
    {
        if (empty(self::$config)) {
            include_once(ROOT_PATH . "core" . DS . "config.php");
            self::$config = $config;
        }
        $map = explode(".", trim($map, '.'));
        $arr = self::$config;
        foreach ($map as $k) {
            $arr = $arr[$k];
        }
        return $arr;
    }

}
