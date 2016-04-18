<?php
/**
 * 基础模型类
 * @Description	IdeaPHP框架
 * @Copyright  	Copyright(c) 2016
 * @Author		Alan
 * @E-mail		20874823@qq.com
 * @Datetime	2016/03/20 17:32:12
 * @Version		1.0
 */
class Model{
	//存储实例化的数据库操作对象
	protected $dao;
	protected $pdo;
	public function __construct(){
		$this->_initDao();
		$this->pdo=$this->dao->pdo;	//pdo对象
	}
	/**
	 * _initDao 初始化数据库操作对象
	 * DAO:Data Access Object 数据操作对象
	 */
	protected function _initDao(){
		global $Config;
		$this->dao=PDODB::getInstance($Config);
	}
}