<?php
namespace bframe;

// 加载基础文件
require __DIR__ . '/base.php';

// 载入Loader类
require BFRAME_CORE_PATH . 'Loader.php';

// 注册自动加载
\bframe\Loader::register();

// 注册错误和异常处理机制
\bframe\Error::register();