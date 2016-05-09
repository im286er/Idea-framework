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
define("ROOT_PATH",rtrim('./'));	//文件夹方式，项目的根路径，也就是网站根目录
defined("APP_PATH")         or define("APP_PATH", ROOT_PATH . "Application");			//网站应用路径
defined("SYS_PATH")         or define("SYS_PATH", ROOT_PATH . "System");				//框架路径
defined('CONFIG_FILE')      or define('CONFIG_FILE', APP_PATH."/Common/Config.php");	//包含系统配置文件
defined('FUNC_FILE')        or define('FUNC_FILE'  , APP_PATH."/Common/Functions.php");	//用户函数文件
file_exists(CONFIG_FILE) ? $Config = include CONFIG_FILE : $Config = include SYS_PATH.'/Common/Config.php';	//如果用户配置文件不存在，引入系统默认配置文件
file_exists(FUNC_FILE) ? require FUNC_FILE : false;			//引入用户自定义方法文件
define("ENTRANCE",basename("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"]));	//入口文件名
//系统运行
require SYS_PATH . '/Kernel/Kernel.class.php';
Kernel::run();