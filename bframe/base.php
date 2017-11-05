<?php

// 初始化常量
define('FRAME_VERSION', '1.0.0');
define('BFRAME_START', microtime(true));

define('DS', DIRECTORY_SEPARATOR);
define('EXT', '.php');

define('BFRAME_PATH', __DIR__ . DS);
define('BFRAME_LIB_PATH', BFRAME_PATH . 'library' . DS);
define('BFRAME_CORE_PATH', BFRAME_LIB_PATH . 'bframe' . DS);

defined('ROOT_PATH') or define('ROOT_PATH', dirname(realpath(APP_PATH)));
defined('PROJECT') or define('PROJECT', 'app');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS . PROJECT . DS);
defined('CONFIG_PATH') or define('CONFIG_PATH', ROOT_PATH . 'config' . DS); // 配置文件目录
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);

defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH .'runtime' . DS);
defined('CACHE_PATH') or define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'logs' . DS);
defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);

defined('APP_DEBUG') or define('APP_DEBUG', false);
defined('ENV_PREFIX') or define('ENV_PREFIX', 'PHP_'); // 环境变量的配置前缀

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

// 加载环境变量配置文件
if (is_file(ROOT_PATH . '.env')) {
    $env = parse_ini_file(ROOT_PATH . '.env', true);
    foreach ($env as $key => $val) {
        $name = ENV_PREFIX . strtoupper($key);
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $item = $name . '_' . strtoupper($k);
                putenv("$item=$v");
            }
        } else {
            putenv("$name=$val");
        }
    }
}