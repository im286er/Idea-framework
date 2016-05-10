<?php
/**
 * Idea框架路由类
 * @Description    
 * @Copyright     Copyright(c) 2016
 * @Author        Alan
 * @E-mail        20874823@qq.com
 * @Datetime      2016/04/04 14:30:43
 * @Version       1.0
 */
class Router {
    /**
     * 加载配置信息
     */
    public function __construct() {
        global $Config;
        $this->config=$Config;
        $this->analysisUrl();
    }
    /**
     * 解析url,参数分发，并定义参数常量
     * @param string MODULE 模块名
     * @param string __MODULE__ 模块URL
     * 
     */
    protected function analysisUrl() {
        if ( $this->config['URL_MODE'] == '1' ) {
            //如果使用了伪静态，显示index.php
            if ( $this->config['REWRITE'] === true ) {
                //服务器方式(URL)，项目的根路径，也就是网站根目录
                define("__ROOT__",rtrim("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"],ENTRANCE . "/"));
            }else{
                define("__ROOT__",trim("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"]));
            }
            //var_dump(basename("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"]));
            //var_dump(__ROOT__);

                        //模块（分组、平台）
            if ( !isset($this->config['DEFAULT_MODULE']) ) {
                $this->config['DEFAULT_MODULE'] = 'Home';
            }
            $current_module = isset($_GET['m']) ? $_GET['m'] : $this->config['DEFAULT_MODULE'];
            define('MODULE', ucwords($current_module));

            define('__MODULE__',__ROOT__ . '?m='. MODULE);
            //如果用户没有定义控制器，则默认Index
            if (!isset($this->config['DEFAULT_CONTROLLER'])) {
                $this->config['DEFAULT_CONTROLLER'] = 'Index';
            }
            //通过判断url里面是否存在c参数，如果没有则设为默认控制器
            $current_controller = isset($_GET['c']) ? $_GET['c'] : $this->config['DEFAULT_CONTROLLER'];
            //当前控制器
            define('CONTROLLER', ucwords($current_controller));
            define('__CONTROLLER__',__ROOT__ . '?m='. MODULE.'&c='.CONTROLLER);
            
            //方法动作
            if (!isset($this->config['DEFAULT_ACTION'])) {
                $this->config['DEFAULT_ACTION'] = 'user';
            }
            $current_action = isset($_GET['a']) ? $_GET['a'] : $this->config['DEFAULT_ACTION'];
            define('ACTION', $current_action);
            define('__ACTION__', __ROOT__  . '?m='. MODULE . '&c=' . CONTROLLER .'&a=' .ACTION);
            //模块文件夹
            $moduleDir=APP_PATH.'/'.MODULE;
            //控制器文件
            $controllerFile=APP_PATH.'/'.MODULE.'/'.'Controller'.'/'.CONTROLLER.'Controller.class.php';
            if( !is_dir($moduleDir) ){
                echo "<h2>Error：模块不存在</h2>错误信息：不存在的模块".MODULE.'<br>';
                echo "模块目录不存在：".$moduleDir.'<br>';
                exit;
            }
            elseif ( !file_exists($controllerFile) ) {
                echo "<h2>Error：控制器不存在</h2>错误信息：不存在的控制器".CONTROLLER.'Controller<br>';
                echo "控制器文件不存在：".$controllerFile.'<br>';
                exit;
            }
            
            //实例化控制器类，调用其方法动作
            $controller_class_name = CONTROLLER . 'Controller';
            //require './application/Home/'.$controller_class_name.'.class.php';
            $controller = new $controller_class_name();
            //调用动作方法
            $action_method_name = ACTION ;
            if( !method_exists($controller,$action_method_name) ){
                echo "<h2>Error：方法不存在</h2>错误信息：不存在的方法" . $action_method_name . '<br>';
                echo "不存在的方法位置：" . $controllerFile . '/' . $action_method_name . '<br>';
                exit;
            }
            //开始实例方法
            $controller->$action_method_name(); //可变方法
            
        }
        //PATHINFO
        elseif ( $this->config['URL_MODE'] == '2' ) {
            if ( $this->config['REWRITE']===true ) {
                //服务器方式(URL)，项目的根路径，也就是网站根目录
                define("__ROOT__",rtrim("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"],ENTRANCE . "/"));
            }else{
                define("__ROOT__",trim("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"]));
            }
            
            //var_dump(ENTRANCE);
            //用户是否设置PATHINFO分隔符，如果灭有则默认为'/'
            if(!isset($this->config['PATH_SEPARATOR'])){
                $this->config['PATH_SEPARATOR']='/';
            }
            if(isset($_SERVER['PATH_INFO'])){
                $paths = explode($this->config['PATH_SEPARATOR'], trim($_SERVER['PATH_INFO'], '/'));
                $urlModule     = array_shift($paths);
                $urlController = array_shift($paths);
                $urlAction     = array_shift($paths);

            }
            if (!isset($this->config['DEFAULT_MODULE'])) {
                $this->config['DEFAULT_MODULE'] = 'Home';
            }
            $current_module = isset($urlModule) ? $urlModule : $this->config['DEFAULT_MODULE'];

            define('MODULE', ucwords($current_module));

            define('__MODULE__',__ROOT__ . '/'. MODULE);
            //echo $_SERVER['PATH_INFO'];
            //如果用户没有定义控制器，则默认Index
            if (!isset($this->config['DEFAULT_CONTROLLER'])) {
                $this->config['DEFAULT_CONTROLLER'] = 'Index';
            }
            //通过判断url里面是否存在c参数，如果没有则设为默认控制器
            $current_controller = isset($urlController) ? $urlController : $this->config['DEFAULT_CONTROLLER'];
            //当前控制器
            define('CONTROLLER', ucwords($current_controller));
            define('__CONTROLLER__',__ROOT__  . '/'. MODULE.$this->config['PATH_SEPARATOR'].CONTROLLER);
            
            //方法动作
            if (!isset($this->config['DEFAULT_ACTION'])) {
                $this->config['DEFAULT_ACTION'] = 'user';
            }
            $current_action = isset($urlAction) ? $urlAction : $this->config['DEFAULT_ACTION'];
            define('ACTION', $current_action);
            define('__ACTION__', __ROOT__  .'/'. MODULE . $this->config['PATH_SEPARATOR'] . CONTROLLER .$this->config['PATH_SEPARATOR'] .ACTION);
            //模块文件夹
            $moduleDir=APP_PATH.'/'.MODULE;
            //控制器文件
            $controllerFile=APP_PATH.'/'.MODULE.'/'.'Controller'.'/'.CONTROLLER.'Controller.class.php';
            if(!is_dir($moduleDir)){
                echo "<h2>Error：模块不存在</h2>错误信息：不存在的模块".MODULE.'<br>';
                echo "模块目录不存在：".$moduleDir.'<br>';
                exit;
            }
            elseif (!file_exists($controllerFile)) {
                echo "<h2>Error：控制器不存在</h2>错误信息：不存在的控制器".CONTROLLER.'Controller<br>';
                echo "控制器文件不存在：".$controllerFile.'<br>';
                exit;
            }
            
            //实例化控制器类，调用其方法动作
            $controller_class_name = CONTROLLER . 'Controller';
            
            $controller = new $controller_class_name();

            //调用动作方法
            $action_method_name = ACTION ;
            if(!method_exists($controller,$action_method_name)){
                echo "<h2>Error：方法不存在</h2>错误信息：不存在的方法".$action_method_name.'<br>';
                echo "不存在的方法位置：".$controllerFile.$this->config['PATH_SEPARATOR'].$action_method_name.'<br>';
                exit;
            }
            //开始实例方法
            $controller->$action_method_name(); //可变方法
        }
        elseif ($this->config['URL_MODE'] == '3') {
            //服务器方式(URL)，项目的根路径，也就是网站根目录
            define("__ROOT__",rtrim("http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"],ENTRANCE));
            //用户是否设置PATHINFO分隔符，如果灭有则默认为'/'
            if(!isset($this->config['PATH_SEPARATOR'])){
                $this->config['PATH_SEPARATOR']='/';
            }
            if(isset($_SERVER['PATH_INFO'])){
                $paths   = explode($this->config['PATH_SEPARATOR'], trim($_SERVER['PATH_INFO'], '/'));
                $urlModule=array_shift($paths);
                $urlController=array_shift($paths);
                $urlAction=array_shift($paths);

            }
            if (!isset($this->config['DEFAULT_MODULE'])) {
                $this->config['DEFAULT_MODULE'] = 'Home';
            }
            $current_module = !empty($urlModule) ? $urlModule : $this->config['DEFAULT_MODULE'];

            define('MODULE', ucwords($current_module));

            define('__MODULE__',__ROOT__ . '/'. MODULE);
            //echo $_SERVER['PATH_INFO'];
            //如果用户没有定义控制器，则默认Index
            if (!isset($this->config['DEFAULT_CONTROLLER'])) {
                $this->config['DEFAULT_CONTROLLER'] = 'Index';
            }
            //通过判断url里面是否存在c参数，如果没有则设为默认控制器
            $current_controller = isset($urlController) ? $urlController : $this->config['DEFAULT_CONTROLLER'];
            //当前控制器
            define('CONTROLLER', ucwords($current_controller));
            define('__CONTROLLER__',__ROOT__  . '/'. MODULE.$this->config['PATH_SEPARATOR'].CONTROLLER);
            
            //方法动作
            if (!isset($this->config['DEFAULT_ACTION'])) {
                $this->config['DEFAULT_ACTION'] = 'user';
            }
            $current_action = isset($urlAction) ? $urlAction : $this->config['DEFAULT_ACTION'];
            define('ACTION', $current_action);
            define('__ACTION__', __ROOT__  .'/'. MODULE . $this->config['PATH_SEPARATOR'] . CONTROLLER .$this->config['PATH_SEPARATOR'] .ACTION);
            //模块文件夹
            $moduleDir=APP_PATH.'/'.MODULE;
            //控制器文件
            $controllerFile=APP_PATH.'/'.MODULE.'/'.'Controller'.'/'.CONTROLLER.'Controller.class.php';
            if(!is_dir($moduleDir)){
                echo "<h2>Error：模块不存在</h2>错误信息：不存在的模块".MODULE.'<br>';
                echo "模块目录不存在：".$moduleDir.'<br>';
                exit;
            }
            elseif (!file_exists($controllerFile)) {
                echo "<h2>Error：控制器不存在</h2>错误信息：不存在的控制器".CONTROLLER.'Controller<br>';
                echo "控制器文件不存在：".$controllerFile.'<br>';
                exit;
            }
            
            //实例化控制器类，调用其方法动作
            $controller_class_name = CONTROLLER . 'Controller';
            
            $controller = new $controller_class_name();

            //调用动作方法
            $action_method_name = ACTION ;
            if(!method_exists($controller,$action_method_name)){
                echo "<h2>Error：方法不存在</h2>错误信息：不存在的方法".$action_method_name.'<br>';
                echo "不存在的方法位置：".$controllerFile.$this->config['PATH_SEPARATOR'].$action_method_name.'<br>';
                exit;
            }
            //开始实例方法
            $controller->$action_method_name(); //可变方法
        }
    }

    
}
