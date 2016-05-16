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
		$body="<h1>框架已成功部署，</h1>";
		$body.="<h1>欢迎使用 Idea framework!</h1>";
		$body.="
			<a href='http://ideait.net' target='_blank'>Idea官网  </a>
			<a href='http://www.kancloud.cn/yunfei_z/framework/136200' target='_blank'>在线手册  </a>
			<a href='".__ROOT__."?m=Admin' target='_blank'>后台管理</a>";
		$this->assign("ispdo",$isPdo);
		$this->assign("body",$body);
		$this->display("index");
		
	}
}