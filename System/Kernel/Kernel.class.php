<?php
/**
 * 系统核心类
 * @Description	系统运行加载类
 * @Copyright  	Copyright(c) 2016
 * @Author		Alan
 * @E-mail		20874823@qq.com
 * @Datetime	2016/04/18 16:39:07
 * @Version		1.0
 */
class Kernel{
	/**
	 * 系统运行
	 */
    public static function run(){
    	self::_getClassList();
    	// 注册自动加载ideaAutoload方法
		spl_autoload_register('self::ideaAutoload');
		self::setHeader();
		require SYS_PATH.'/Common/Functions.php';
		new Router();
    }
    /**
     * 设置编码和时区
     */
    protected static function setHeader(){
		header("Content-type:text/html;Charset=utf-8");
		date_default_timezone_set("PRC");
	}
	/**
	 * 获取系统类地址映射表
	 * @return array 系统类地址映射表
	 */
    private static function _getClassList(){
		$class_list=array(
			'Router'     => SYS_PATH . '/Kernel/Router.class.php',
			'Singleton'  => SYS_PATH . '/kernel/Singleton.class.php',
			'PDODB'      => SYS_PATH . '/Kernel/PDODB.class.php',
			'Controller' => SYS_PATH . '/Kernel/Controller.class.php',
			'Model'      => SYS_PATH . '/Kernel/Model.class.php',
			'Template'   => SYS_PATH . '/Kernel/Template.class.php',
			);
		return $class_list;
    }	
	/**
	 * IdeaPHP框架自动加载方法（2种加载方式）
	 * @param string $class_name 自动加载类/接口名
	 */
	public static function ideaAutoload($class_name=''){
		$class_list=self::_getClassList();
		if (isset($class_list[$class_name])) {
			require $class_list[$class_name];
		}
		//规则加载(模型类)
		elseif('Model'==substr($class_name,-5)){
			//var_dump($class_name);
			require APP_PATH . '/' . MODULE . '/Model/' . $class_name . '.class.php';		
		}
		elseif('Controller'==substr($class_name,-10)){
			//var_dump($class_name);
			require APP_PATH . '/' . MODULE .'/Controller/'. $class_name .'.class.php';
		}else{
			require SYS_PATH . '/Libraries/' . $class_name .'.class.php';
		}
	}
}
