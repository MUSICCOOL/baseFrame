<?php
/*
  Author: Xu Wenmeng
  Author Email: xuwenmeng@hotmail.com
  Description: 视图类
  Version: 1.0
*/

namespace system;

class view
{
    public $view = null;

    public function __construct()
    {
        //使用composer安装的twig模板引擎
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(APP_PATH . DS . "view" . DS);
        $twig   = new \Twig_Environment($loader, array(//'cache' => APP_PATH . DS . "cache",
        ));
        if (!empty($_SESSION['username'])) {
            $twig->addGlobal('USERNAME', $_SESSION['username']);
        }
        if (!empty($_SESSION['admin'])) {
            $twig->addGlobal('USERNAME', $_SESSION['admin']);
        }
        $twig->addGlobal('APP_FILE', APP_FILE);

        if ($_SERVER['HTTPS'] != "on") {
            $url_type = 'http';
        } else {
            $url_type = 'https';
        }

        $this->view = $twig;
    }
}
