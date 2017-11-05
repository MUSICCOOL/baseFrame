<?php

namespace system;

class controller
{
    protected $params = [];

    protected $view = null;

    protected $siteSet = null;

    protected $user = null;

    public function __construct()
    {
        $view         = new view();
        $this->view   = $view->view;
        $this->params = $this->paramsFilter();
    }

    protected function paramsFilter()
    {
        $params = [];
        /* 过滤所有GET过来变量 */
        foreach ($_GET as $get_key => $get_var) {
            $params[strtolower($get_key)] = $this->get_str($get_var);
        }

        /* 过滤所有POST过来的变量 */
        foreach ($_POST as $post_key => $post_var) {
            $params[strtolower($post_key)] = $this->get_str($post_var);
        }
        return $params;
    }

    protected function get_str($string)
    {
        if (!get_magic_quotes_gpc()) {
            return addslashes($string);
        }
        return trim($string);
    }

    protected function redirect($c, $a, $url = '')
    {
        if (!empty($url)) {
            header("Location:" . $url);
        }
        if (PROJECT == 'app') {
            header("Location:" . "/" . $c . "/" . $a . '.html');
        } else if (PROJECT == 'admin') {
            header("Location:" . APP_FILE . "?c=" . $c . "&a=" . $a);
        }
        exit();
    }

    protected function alert($data)
    {
        if (is_array($data) && isset($data['code'])) {
            $string = constant::$codeMsg[$data['code']];
        } else {
            $string = $data;
        }
        echo '<script>alert("' . $string . '");history.go(-1);</script>';
        exit();
    }


    protected function renderJson($data = array())
    {
        header('Content-type: application/json');
        if (!isset($data['code'])) {
            $data['code'] = constant::SUCCESS;
        }
        if (!isset($data['msg'])) {
            $msg         = constant::$codeMsg[$data['code']];
            $data['msg'] = empty($msg) ? '' : $msg;
        }
        if (isset($this->params['callback'])) {
            echo isset($this->params['callback']) . '(' . json_encode($data) . ')';
        } else {
            echo json_encode($data);
        }
        exit();
    }


    protected function renderView($template, $data = array())
    {
        // 渲染模板
        $template = $this->view->loadTemplate($template . '.html');
        echo $template->render($data);
        exit();
    }


    protected function renderError($data = array(), $template = 'error')
    {
        if (!isset($data['code'])) {
            $data['code'] = constant::FALSE;
        }
        if (!isset($data['msg'])) {
            $msg         = constant::$codeMsg[$data['code']];
            $data['msg'] = empty($msg) ? '' : $msg;
        }
        $this->renderView($template, $data);
    }

    public function imgUpload($file, $allowTypes, $path)
    {
        if (empty($file['name'])) {
            $this->renderError(['code' => constant::IMAGE_UPLOAD_ERROR]);
        }

        if (!in_array($file['type'], $allowTypes)) {
            $this->renderError(['code' => constant::IMAGE_TYPE_ERROR]);
        }

        if (constant::IMAGE_ALLOW_UPLOAD_SIZE < $file['size']) {
            $this->renderError(['code' => constant::IMAGE_SIZE_ERROR]);
        }


        if (empty($path)) {
            $path = constant::IMAGE_UPLOAD_PATH;
        }

        $filename = md5(date('YmdHis') . rand(1000, 9999)) . substr($file['name'], strpos($file['name'], '.'));

        // 访问路径
        $url = $path . $filename;
        // 存储路径
        $savePath = ROOT_PATH . $url;

        try {
            move_uploaded_file($file["tmp_name"], $savePath);  // 文件存储
        } catch (\Exception $e) {
            $this->renderError(['code' => constant::IMAGE_UPLOAD_ERROR]);
        }

        // 返回文件访问路径(不含域名)
        return $url;
    }

    public function fileUp($file, $allowTypes, $path)
    {
        if (empty($file['name'])) {
            $this->renderJson(['code' => constant::FILE_UPLOAD_ERROR]);
        }

        if (!in_array($file['type'], $allowTypes)) {
            $this->renderJson(['code' => constant::FILE_TYPE_ERROR]);
        }

        if (constant::FILE_ALLOW_UPLOAD_SIZE < $file['size']) {
            $this->renderJson(['code' => constant::FILE_SIZE_ERROR]);
        }

        $filename = md5(date('YmdHis') . rand(1000, 9999)) . substr($file['name'],
                strpos($file['name'], '.'));

        // 访问路径
        $url = $path . $filename;
        // 存储路径
        $savePath = ROOT_PATH . $url;

        try {
            move_uploaded_file($file["tmp_name"], $savePath);  // 文件存储
        } catch (\Exception $e) {
            $this->renderJson(['code' => constant::FILE_UPLOAD_ERROR]);
        }

        // 返回文件访问路径(不含域名)
        return $url;
    }

    public function upBase64Image($data, $allowTypes = array(), $path)
    {
        $data = explode("data:", $data);
        unset($data[0]);
        foreach ($data as $key => $v) {
            $data[$key] = 'data:' . $v;
        }

        $img_urls = [];

        foreach ($data as $value) {
            if (preg_match('/^(data:\s*image\/)(\w+)(;base64,)/', $value, $matches)) {
                $type = $matches[2];
            } else {
                $this->renderJson(['code' => constant::IMAGE_UPLOAD_ERROR]);
            }

            if (!in_array($type, $allowTypes)) {
                $this->renderJson(['code' => constant::IMAGE_TYPE_ERROR]);
            }

            $img      = str_replace($matches[0], '', $value);
            $img      = base64_decode($img);
            $filename = md5(date('YmdHis') . rand(1000, 9999)) . '.' . $type;

            // 访问路径
            $url = $path . $filename;
            // 存储路径
            $savePath = ROOT_PATH . $url;

            try {
                file_put_contents($savePath, $img);
                $img_urls[] = $url;
            } catch (\Exception $e) {
                $this->renderJson(['code' => constant::IMAGE_UPLOAD_ERROR]);
            }
        }

        return $img_urls;
    }
}
