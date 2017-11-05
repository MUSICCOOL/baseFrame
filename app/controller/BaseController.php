<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\MapModel;
use app\model\UserModel;
use system\controller;

class BaseController extends controller
{
    protected $needCheckLogin = [
        'account' => [],
        'project' => [
            'add'      => 'alert',
            'download' => 'alert',
        ],
        'comment' => [
            'sendComment' => 'json',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->checkLogin();
    }

    protected function checkLogin()
    {
        $c = isset($this->params['c']) ?: '';
        $a = isset($this->params['a']) ?: '';
        if (PROJECT == 'app' && isset($this->needCheckLogin[$c])) {
            if (empty($_SESSION['username'])) {
                if (isset($this->needCheckLogin[$c][$a]) || empty($this->needCheckLogin[$c])) {
                    if ($this->needCheckLogin[$c][$a] == 'json') {
                        $this->renderJson(['code' => ConstantModel::NOT_LOGIN_YET_ERROR]);
                    } else {
                        $this->alert(ConstantModel::$codeMsg[ConstantModel::NOT_LOGIN_YET_ERROR]);
                    }
                }
            } else {
                $this->params['username'] = $_SESSION['username'];
                $user                     = new UserModel();
                $this->user               = $user->where('username', $_SESSION['username'])->first();
            }
        }

        if (!empty($_SESSION['username'])) {
            $this->params['username'] = $_SESSION['username'];
            $user                     = new UserModel();
            $this->user               = $user->where('username', $_SESSION['username'])->first();
        }
        return true;
    }



    protected function renderView($template, $data = array())
    {
        $data['site_set'] = $this->getSiteSet();
        if (!empty($this->user)) {
            $data['user'] = $this->user;
        }
        // 渲染模板
        echo $this->view->render($template . '.html', $data);
        exit();
    }


    protected function getSiteSet()
    {
        $model   = new MapModel();
        $records = $model->whereIn('key_name', MapModel::$siteKey)->get();
        $site    = [];
        foreach ($records as $record) {
            $site[$record['key_name']] = $record['key_value'];
        }
        return $site;
    }
}
