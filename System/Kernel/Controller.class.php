<?php
/**
 * 控制器基类
 * @Description    所有控制器类都基于该类
 * @Copyright      Copyright(c) 2016
 * @Author         Alan
 * @E-mail         20874823@qq.com
 * @Datetime       2016/04/18 16:38:18
 * @Version        1.0
 */
class Controller{
    protected $tp = null;
	public function __construct(){
	}
    /**
     * 直接跳转方法
     * @access protected
     * @param string $jumpUrl 跳转地址
     */
    protected function direct($jumpUrl='') {
        header("Location:$jumpUrl");
        exit;
    }
    /**
     * 等待提示跳转
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 跳转地址
     * @param int $waitTime 跳转时间(单位：秒)
     */
    protected function wait($jumpUrl='',$message='',$waitTime=3) {
        header("Refresh:$waitTime;URL=$jumpUrl");
        echo $message;
        exit;
    }
}