<?php
session_start();

ini_set('date.timezone','Asia/Shanghai');

// 这个定义linux和windows中的引入路径的分隔符 linux \ windows /
define("DS", DIRECTORY_SEPARATOR);

// 定义根目录
define("ROOT_PATH", __DIR__ . DS);

// 定义项目路径
define("PROJECT", 'admin');
define("APP_PATH", __DIR__ . DS . '/' . PROJECT . '/');

// 定义入口文件
$file = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], DS) + 1);
define("APP_FILE", $file);

error_reporting(E_ALL ^ E_NOTICE);

// 引入程序入口文件
require_once('./bframe/XFrame.php');
