<?php
/**
 * 数据库操作类
 * @Description	PDO操作
 * @Copyright  	Copyright(c) 2016
 * @Author		Alan
 * @E-mail		20874823@qq.com
 * @Datetime	2016/04/18 16:39:39
 * @Version		1.0
 */
class PDODB{
	//运行属性
	private $_dsn;
	private $_options;
	public $pdo;
	//私有静态变量，存储对象
	private static $_instance;
	//使该类不能在类外实例化对象
	private function __construct($CONFIG=array()){
		$this->_initServer($CONFIG);	//初始化数据库连接信息
		$this->_newPDO();		//获取PDO对象

	}
	//设置私有，防止对象通过克隆方法产生对象
	private function __clone(){

	}
	//获取当前DAO对象
	public static function getInstance($CONFIG=array()){
		if(!self::$_instance instanceof self){
			self::$_instance=new self($CONFIG);
		}
		return self::$_instance;
	}
	/**
	 * 初始化数据库连接信息
	 * @param  array $CONFIG $CONFIG=array('DB_HOST'=>'localhost','DB_PORT'=>3306,...)
	 */
	private function _initServer($CONFIG){
		//如果用户没有设置，则使用系统默认信息
		$this->DB_MS       = isset($CONFIG['DB_MS'])       ? $CONFIG['DB_MS']       : 'mysql';
		$this->DB_HOST     = isset($CONFIG['DB_HOST'])     ? $CONFIG['DB_HOST']     : 'localhost';
		$this->DB_PORT     = isset($CONFIG['DB_PORT'])     ? $CONFIG['DB_PORT']     : 3306;
		$this->DB_USER     = isset($CONFIG['DB_USER'])     ? $CONFIG['DB_USER']     : 'root';
		$this->DB_PASSWORD = isset($CONFIG['DB_PASSWORD']) ? $CONFIG['DB_PASSWORD'] : '';
		$this->DB_CHARSET  = isset($CONFIG['DB_CHARSET'])  ? $CONFIG['DB_CHARSET']  : 'utf8';
		$this->DB_NAME     = isset($CONFIG['DB_NAME'])     ? $CONFIG['DB_NAME']     : '';
	}
	/**
	 * 获取PDO对象的操作
	 */
	private function _newPDO(){
		//设置参数
		$this->_setDSN();		//设置数据源
		$this->_setOptions();	//设置选项
		$this->_getPDO();		//得到PDO对象
	}
	private function _setDSN(){
		$this->_dsn="$this->DB_MS:host=$this->DB_HOST;dbname=$this->DB_NAME";
	}
	private function _setOptions(){
		$this->_options=array(
			PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES $this->DB_CHARSET"
			);
	}
	private function _getPDO(){
		try{
			$this->pdo=new PDO($this->_dsn,$this->DB_USER,$this->DB_PASSWORD,$this->_options);
		}
		catch(PDOException $error){
			echo "<h2>Error：数据库连接错误</h2>错误信息：数据库连接失败。<br>";
                echo "请确认数据库服务器配置信息！<br>";
                exit;
		}
	}
	/**
	 * 执行SQL
	 * @param  string $sql [description]
	 * @return [type]      [description]
	 */
	public function query($sql=''){
		$sql=ltrim($sql);//去掉左边空白字符
		//查询类
		if ( strtolower(substr($sql,0,6)) == 'select' || strtolower(substr($sql,0,4))=='show' || strtolower(substr($sql,0,4)) == 'desc' ) {
			$result=$this->pdo->query($sql);
		}else{
			//非查询类。返回布尔值
			$result=$this->pdo->exec($sql)!==false;
		}
		//如果执行失败，报告错误信息，并停止
		if ($result === false) {
			//执行失败，结果就是false
			$error_info=$this->pdo->errorInfo();
			echo "<h2>Error：SQL执行失败</h2>错误信息：".$error_info[2]."</font><br>";
			echo "错误SQL：".$sql."<br>";
			exit;
		}else{
			return $result;
		}
	}
	/**
	 * 获取一个记录
	 * @param  string $sql 		SQL语句
	 * @return string $string 	
	 */
	public function getOne($sql=''){
		$result=$this->query($sql);
		$string=$result->fetchColumn();
		$result->closeCursor();
		return $string;
	}
		/**
	 * 获取一行记录
	 * @param  string $sql [description]
	 * @return [type]      [description]
	 */
	public function getRow($sql=''){
		$result=$this->query($sql);
		$row=$result->fetch(PDO::FETCH_ASSOC);
		$result->closeCursor();
		return $row;
	}
	/**
	 * 获取全部记录
	 * @param  string $sql [description]
	 * @return [type]      [description]
	 */
	public function getAll($sql=''){
		$result=$this->query($sql);
		$rows=$result->fetchAll(PDO::FETCH_ASSOC);
		$result->closeCursor();
		return $rows;
	}
	/**
	 * 防止SQL注入
	 * @param  string $str [description]
	 * @return [type]      [description]
	 */
	public function escapeSql($str=''){
		return $this->pdo->quote($str);
	}
}