<?php
/**
 * 模型对象的单例化
 * @Description	
 * @Copyright  	Copyright(c) 2016
 * @Author		Alan
 * @E-mail		20874823@qq.com
 * @Datetime	2016/03/21 16:05:15
 * @Version		1.0
 */
class Singleton{
	/**
	 * 获得模型的单例对象
	 * 针对所有模型 调用方法 $UserModel=Singleton::Model('User');
	 * 可直接使用定义在Fonctions.php内的方法Model(模型名,模型方法名);进行调用该类
	 * @param $_model_name 需要得到单利对象的模型的名字，例如"User"或者"UserModel"
	 * @return object 该模型类的单例对象
	 */
	private $_model_name;
	public static function getModelObject($_model_name){
		//储存所有的模型方法
		static $model_lists=array();	//'User'=>Object(UserModel)
		//判断该模型类是否已经实例化对象
		if(!isset($model_lists[$_model_name])){
			//该模型类对象不存在，则实例化
			$model_class_name=$_model_name.'Model';
			$model_lists[$_model_name]=new $model_class_name();
		}
		//返回获取的模型对象
		return $model_lists[$_model_name];
	}
}