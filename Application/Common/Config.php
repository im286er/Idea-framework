<?php
/**
 * 公共配置信息
 */
return array(
	/**
	 * 数据库信息
	 */
	'DB_MS'              => 'mysql',		//数据库类型
	'DB_HOST'            => '127.0.0.1',	//数据库服务器地址
	'DB_PORT'            => '3306',			//数据库端口
	'DB_USER'            => 'root',			//数据库登陆名
	'DB_PASSWORD'        => '',				//数据库密码
	'DB_CHARSET'         => 'utf8',			//字符集编码
	'DB_NAME'            => '',				//选择数据库名
	
	/**
	 * 设置默认操作
	 */
	'DEFAULT_MODULE'     => 'Home',			//默认模块（平台、分组）
	'DEFAULT_CONTROLLER' => 'Index',		//默认控制器
	'DEFAULT_ACTION'     => 'index',		//默认控制器方法
	/**
	 * URL路由配置
	 */
	'URL_MODE'           => '1',		//1  普通模式	如：http://ideait.net/index.php?m=Home&c=User&a=login
										//2  PATHINFO 	如：http://ideait.net/index.php/Home/User/login
	'PATH_SEPARATOR'     => '/',		//URL分割符，在开启PATHINFO模式下有效，如：http://ideait.net/index.php/Home-User-login
	'REWRITE'            => false,		//伪静态
	);