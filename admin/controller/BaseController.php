<?php

namespace admin\controller;

use system\controller;

class BaseController extends controller
{
    public function __construct()
    {
        parent::__construct();

        $this->checkLogin();
    }

    protected function checkLogin()
    {
        if (PROJECT == 'admin' && ($_GET['c'] != 'login')) {
            if (empty($_SESSION['admin'])) {
                $url = APP_FILE . '?c=login&a=login';
                header("Location:$url");
            }
        }
        return true;
    }
}
