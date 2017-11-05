create database baseframe;

create table `admin`(
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员Id',
     `username` char(15) NOT NULL DEFAULT '' COMMENT '管理员用户名',
     `password` char(255) NOT NULL DEFAULT '' COMMENT '管理员密码',
     `phone` char(11) NOT NULL DEFAULT '' COMMENT '管理员电话',
     `email` char(50)  NOT NULL DEFAULT '' COMMENT '管理员邮箱',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `admin`(username, password) value ('admin', md5('123456'));

create table `kvmap`(
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT '键值对Id',
     `key_name` char(50) NOT NULL DEFAULT '' COMMENT '键名',
     `key_title` char(255) NOT NULL DEFAULT '' COMMENT '键标题',
     `key_value` char(255) NOT NULL DEFAULT '' COMMENT '键值',
     `remark` char(15) NOT NULL DEFAULT '' COMMENT '备注',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`),
     UNIQUE INDEX `key_name` (`key_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `kvmap`(key_name, key_title, key_value) value ('site_name', '站点名称', 'baseFrame框架');
insert into `kvmap`(key_name, key_title, key_value) value ('site_title', '站点标题', 'baseFrame框架');
insert into `kvmap`(key_name, key_title, key_value) value ('site_keywords', '站点关键词', 'baseFrame');
insert into `kvmap`(key_name, key_title, key_value) value ('site_description', '站点描述', '一个由许文猛开发的框架');

create table `user`(
     `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户Id',
     `username` char(15) NOT NULL DEFAULT '' COMMENT '用户名',
     `nickname` char(15) NOT NULL DEFAULT '' COMMENT '用户昵称',
     `password` char(255) NOT NULL DEFAULT '' COMMENT '用户密码',
     `email` char(50)  NOT NULL DEFAULT '' COMMENT '用户邮箱',
     `avatar` text  COMMENT '用户头像',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`user_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;