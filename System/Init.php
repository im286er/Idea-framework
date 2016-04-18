<?php
/**
 * 初始化
 * @Description	
 * @Copyright  	Copyright(c) 2016
 * @Author		Alan
 * @E-mail		20874823@qq.com
 * @Datetime	2016/03/24 19:36:57
 * @Version		1.0
 */
//文件夹方式，项目的根路径，也就是网站根目录
define("ROOT_PATH",rtrim('./'));
defined("APP_PATH")         or define("APP_PATH", ROOT_PATH . "Application");
defined("SYS_PATH")         or define("SYS_PATH", ROOT_PATH . "System");//框架路径
//包含系统配置文件
defined('CONFIG_FILE')      or define('CONFIG_FILE', APP_PATH."/Common/Config.php");
//用户函数文件
defined('FUNC_FILE')        or define('FUNC_FILE'  , APP_PATH."/Common/Functions.php");
//是否开启安全模式，默认开启
defined('OPEN_SAFE_MODEL')  or define('OPEN_SAFE_MODEL', true);
//配置信息
file_exists(CONFIG_FILE) ? $Config = include CONFIG_FILE : $Config = include SYS_PATH.'/Common/Config.php';
//引入用户自定义方法文件
file_exists(FUNC_FILE) ? require FUNC_FILE : false;
//入口文件名
define("ENTRANCE",basename("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"]));
//系统运行
require SYS_PATH . '/Kernel/Kernel.class.php';
Kernel::run();