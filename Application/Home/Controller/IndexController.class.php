<?php
/**
 * 首页控制器，继承公共控制器类
 */
class IndexController extends CommonController{
	/**
	 * 可删除，框架配置信息输出
	 * @return [type] [description]
	 */
	public function index(){
		$isPdo=extension_loaded('pdo_mysql');
		$ok="<h1>框架已成功部署，</h1>";
		$ok.="<h1>欢迎使用 Idea framework!</h1>";
		$ok.="
			<a href='http://ideait.net' target='_blank'>Idea官网  </a>
			<a href='http://www.kancloud.cn/yunfei_z/framework/136200' target='_blank'>在线手册  </a>
			<a href='".__ROOT__."?m=Admin' target='_blank'>后台管理</a>";
		$no='<h2>Warning：服务器环境错误！</h2>
	错误信息：没有开启pdo_mysql扩展！<br>
	帮助信息：请尝试打开php.ini将 ;extension=php_pdo_mysql.dll 前面的分号删除';
		require (self::TEMPLATE_PATH . 'index.php');
	}
}