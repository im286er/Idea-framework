<?php
/**
 * 后台公共控制器，继承控制器基类
 */
class CommonController extends Controller{
	//定义模板目录
    const TEMPLATE_PATH=APP_PATH  . '/' . MODULE . '/View/Default/';
    public function __construct(){
        parent::__construct();
    }
}
