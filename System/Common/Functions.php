<?php
/**
 * 内置公共方法
 * @Description    公共方法
 * @Copyright      Copyright(c) 2016
 * @Author         Alan
 * @E-mail         20874823@qq.com
 * @Datetime       2016/05/09 15:34:16
 * @Version        1.0
 */
//自定义Model方法，对模型类进行单例化
function Model($modelName=null,$modelMethot=null){
	if(!isset($modelMethot)){
    	return Singleton::getModelObject($modelName);
    }else{
    	return Singleton::getModelObject($modelName)->$modelMethot();
    }
}
//类库加载函数
function Loader($className=null,$classDir=null){
    if (!isset($classDir)) {
        $classPath=SYS_PATH . '/Libraries/' . $className.'.class.php';
    }else{
        $classPath=SYS_PATH . '/Libraries/' . trim($classDir,'/') . '/' . $className .'.class.php';
    }
    if (!file_exists($classPath)) {
        echo "<h2>Error：所加载类不存在</h2>错误信息：不存在的类". $className ."<br>";
        echo "请检查类文件是否存在目录不存在：".$classPath.'<br>';
        exit;
    }else{
        require_once $classPath;
    }
}