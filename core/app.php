<?php
// 自动引入类文件
include_once(ROOT_PATH . "vendor/autoload.php");

// 定义控制器和操作方法的名称
$c = PROJECT . "\\controller\\" . (empty($_GET['c']) ? 'index' : $_GET['c']) . "Controller";
$a = empty($_GET['a']) ? 'index' : $_GET['a'];

try {
    // 调用类方法 分发
    call_user_func_array(array(new $c, $a), array());
} catch (\Exception $e) {
    echo '发生未知错误！<a href="/">返回</a>';
}
