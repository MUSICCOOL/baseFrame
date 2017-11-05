<?php

namespace admin\controller;

use system\paginator;
use admin\model\AdminModel;

class AdminController extends BaseController
{
    public function index()
    {
        $page      = empty($this->params['page']) ? 1 : $this->params['page'];
        $count     = empty($this->params['count']) ? AdminModel::DEFAULT_COUNT : $this->params['count'];
        $start     = ($page - 1) * $count;
        $data      = AdminModel::orderBy('created_at', 'desc')->skip($start)->take($count)->get();
        $total     = AdminModel::count();
        $paginator = new paginator($total, $count, $page);
        $pageSet   = $paginator->page();
        // 渲染模板
        $this->renderView('admin/list', ['admins' => $data, 'paginator' => $pageSet]);
    }

    public function add()
    {
        // 渲染模板
        $this->renderView('admin/add');
    }

    public function doAdd()
    {
        $admin           = new AdminModel();
        $admin->username = $this->params['username'];
        $admin->password = md5($this->params['password']);
        $admin->phone    = $this->params['phone'];
        $admin->email    = $this->params['email'];
        try {
            if ($admin->save()) {
                $this->index();
            }
        } catch (\Exception $e) {
            echo '该用户名已存在！';
            echo '<a href="' . APP_FILE . '?c=admim&a=add">返回</a>';
        }
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = AdminModel::destroy($ids);
        if ($deleted) {
            echo json_encode([]);
        }
    }
}
