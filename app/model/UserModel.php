<?php

namespace app\model;

use system\model;

//继承父类并创建数据库链接
class UserModel extends model
{
    //所操作的数据表 可选
    protected $table = "user";
    //表的自增ID 可选
    protected $primaryKey = "user_id";

    const PAASSWORD_MIN_LENGTH     = 6;  // 密码最小位数

    const DEFAULT_USER_AVATAR = '/public/img/default-avatar.jpg';

    const DEFAULT_USER_PAYWAI = [
        'zhifubao' => '/public/img/zhifubao.png',
        'weixin'   => '/public/img/weixin.png',
    ];

    const PASSWORD_RESET_QUESTION = [
        0 => '我永不会忘记的话语',
        1 => '你喜欢的人的名字',
        2 => '我最喜欢的一本书的名字',
        3 => '我崇拜的偶像的名字',
        4 => '我的座右铭',
        5 => '我最感恩的人',
        6 => '我的梦想职业',
    ];
}
