<?php
/**
 * 后台公共控制器，继承控制器基类
 */
class CommonController extends Controller{
    public function __construct(){
        parent::__construct();
        //实例化内置模板引擎
        $this->tp= Template::GetInstance();
        $this->setTemplate();
    }
    //返回display();方法
    public function display($file){
        $this->tp->display($file);
    }
    //返回assign();方法
    public function assign($var,$value){
        $this->tp->assign($var,$value);
    }//配置模板参数
    public function setTemplate(){
        $this->tp->leftTag  = '{{';
        $this->tp->rightTag  = '}}';  
        $this->tp->templatePath   = APP_PATH  . '/' . MODULE . '/View/Default';     //定义模板文件存放的目录  
        $this->tp->compilePath    = ROOT_PATH . 'Cache/' .MODULE;      //定义通过模板编译文件存放目录
        $this->tp->includePath    = APP_PATH . '/' . MODULE . '/View/Default';     //定义模板包含函数路径
        $this->tp->templateSuffix = '.html';                    //模板文件后缀
    }

}
