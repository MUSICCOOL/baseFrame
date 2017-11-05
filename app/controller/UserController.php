<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\UserModel;

class UserController extends BaseController
{
    public function doRegister()
    {
        $username         = $this->params['username'];
        $nickname         = $this->params['nickname'];
        $email            = $this->params['email'];
        $password         = $this->params['password'];
        $password_confirm = $this->params['password_confirm'];

        $this->pwdConfirm($password, $password_confirm);
        $this->checkEmail($email);
        $this->checkUsername($username);
        $this->checkNickname($nickname);

        $user           = new UserModel();
        $user->username = $username;
        $user->nickname = $nickname;
        $user->password = md5($password);
        $user->email    = $email;

        // 设置默认头像
        $user->avatar = UserModel::DEFAULT_USER_AVATAR;

        if ($user->save()) {
            $_SESSION['username'] = $username;
            $this->renderJson();
        } else {
            $this->renderJson(['code' => ConstantModel::FALSE]);
        }
    }

    /**
     * 确认密码是否一致
     *
     * @param $pwd
     * @param $pwd_reset
     */
    public function pwdConfirm($pwd, $pwd_confirm)
    {
        if (strlen($pwd) < UserModel::PAASSWORD_MIN_LENGTH) {
            $this->renderJson(['code' => ConstantModel::PASSWORD_LEN_ERROR]);
        }

        if ($pwd != $pwd_confirm) {
            $this->renderJson(['code' => ConstantModel::PASSWORD_CONFIRMED_ERROR]);
        }
    }

    public function pwdReset()
    {
        $user = UserModel::find($this->params['user_id']);

        if (strlen($this->params['new_password']) < UserModel::PAASSWORD_MIN_LENGTH) {
            $this->alert(ConstantModel::$codeMsg[ConstantModel::PASSWORD_LEN_ERROR]);
        }

        if ($this->params['new_password'] != $this->params['conf_password']) {
            $this->alert(ConstantModel::$codeMsg[ConstantModel::PASSWORD_CONFIRMED_ERROR]);
        }
        $user->password = md5($this->params['new_password']);
        if (! $user->save()) {
            $this->alert(ConstantModel::$codeMsg[ConstantModel::PASSWORD_CHANGE_ERROR]);
        }
    }

    /**
     * 判断用户名是否存在
     *
     * @param $username
     */
    public function checkUsername($username)
    {
        $user   = new UserModel();
        $result = $user->where('username', $username)->first();
        if (!empty($result)) {
            $this->renderJson(['code' => ConstantModel::USERNAME_ALREADY_EXISTS]);
        }
    }

    /**
     * 判断昵称是否存在
     *
     * @param $nickrname
     */
    public function checkNickname($nickname)
    {
        $user   = new UserModel();
        $result = $user->where('nickname', $nickname)->first();
        if (!empty($result)) {
            $this->renderJson(['code' => ConstantModel::NICKNAME_ALREADY_EXISTS]);
        }
    }

    /**
     * 判断邮箱格式以及是否已注册
     *
     * @param $email
     */
    public function checkEmail($email)
    {
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            $this->renderJson(['code' => ConstantModel::EMAIL_FORMAT_ERROR]);
        }
        $user   = new UserModel();
        $result = $user->where('email', $email)->first();
        if (!empty($result)) {
            $this->renderJson(['code' => ConstantModel::EMAIL_ALREADY_EXISTS]);
        }

    }
}
