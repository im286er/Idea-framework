<?php
/**
 * 网站验证码类
 * @copyright   Copyright(c) 2015
 * @author      Alan <ords.cn/t.qq.com/yunfei-z>
 * @E-mail		20874823@qq.com
 * @touch date	2015/12/27
 * @version     1.0
 */
class ValidateCode{
	private $codeCheck;
	public  $width;
	public  $height;
	public  $codeNum;
	public  $codeType;
	public  $fontType;
	public  $fontSize;
	/**		
	 *		
	 *		@param int $width		
	 *		@param int $height		
	 *		@param int $codeNum		
	 *		@param int $codeType	
	 *
	 */
	/**
	 * 构造函数，成员属性初始化
	 * @param integer $fontType 字体设置
	 * @param integer $fontSize 字符大小
	 * @param integer $width    验证码宽度
	 * @param integer $height   验证码高度
	 * @param integer $codeNum  验证码字符个数
	 * @param integer $codeType 验证码类型：0(数字) 1(数字+小写字母) 2(数字+大小写字母)
	 */
	function __construct($fontType=5,$fontSize=20,$width=100,$height=40,$codeNum=4,$codeType=1){
		$this->width    = $width;
		$this->height   = $height;
		$this->codeNum  = $codeNum;
		$this->codeType = $codeType;
		$this->fontType = $this->selectFont($fontType);
		$this->fontSize = $fontSize;
	}
	/**
	 * 通过数字代号设置字体
	 * @param  integer 字体名
	 * @return string  字体文件路径
	 */
	private function selectFont($fontNum=1){
		return SYS_PATH . '/Libraries/Fonts/'.$fontNum.'.TTF';
	}
	/**
	 * 验证码生成函数
	 * @return 验证码字符
	 */
	private function createCode(){
		$code = "3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKMNPQRSTUVWXY";	//去除容易混淆字符012 oO lL zZ 
		$type = array(6,29,52);		//定义类型位置
		//生成验证码字符
		for ($i=0;$i<$this->codeNum;$i++){ 
			$this->codeCheck.= $code[mt_rand(0,$type[$this->codeType])];
		}
	}
	/**
	 * 创建背景/画布
	 * @return resource 验证码背景
	 */
	private function createBg(){
		//1、创建画布
 		$this->image=imagecreatetruecolor ($this->width,$this->height);	//新建一个真彩色图像
 		$bgcolor=imagecolorallocate ($this->image,255,255,255);		//为一幅图像分配颜色
 		imagefill($this->image,0,0,$bgcolor);		//填充画布
	}
	/**
	 * 图像绘制步骤：
	 * 添加字符干扰
	 */
	private function createImage(){
		//$str=$this->getCode();
		//2、开始绘画
		//添加干扰点
		for($i=0;$i<200;$i++){
			$pointcolor = imagecolorallocate($this->image, mt_rand(120,255), mt_rand(120,255), mt_rand(120,255));
			imagesetpixel($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),$pointcolor);
		}
		//添加干扰线
		for($i=0;$i<8;$i++){
			$linecolor = imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
			imageline($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$linecolor);
		}
		//绘制雪花干扰
		for ($i=0; $i < 50; $i++) { 
			$startcolor = imagecolorallocate($this->image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
			imagestring($this->image,mt_rand(0,255),mt_rand(1,$this->width),mt_rand(0,$this->height),"*",$startcolor);
		}
		//使用外部字体
		for($i=0;$i<$this->codeNum;$i++){
			$fontcolor = imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,155), mt_rand(0,255));
			imagettftext($this->image,$this->fontSize,mt_rand(-20,20),($this->width/$this->codeNum)*$i+mt_rand(4,8),$this->height/1.45+mt_rand(1,5),$fontcolor,$this->fontType,$this->codeCheck[$i]);
		}
 	}
 	/**
 	 * 输出图像
 	 * @return resource 返回png格式图片
 	 */
	private function outImage(){
		//3、输出图像
		header("Content-Type:image/png");
		//以 PNG 格式将图像输出到浏览器或文件
		imagepng($this->image);
		//4、释放资源（销毁图片）
		imagedestroy($this->image);
	}
	/**
	 * 获取验证码图片
	 * @return resource 验证码图片
	 */
	public function getImage(){
		$this->createBg();
		$this->createCode();
		$this->createImage();
		$this->outImage();
	}
	/**
	 * 获取验证码内容
	 * @return string 验证码内容
	 */
	public function getCode(){
		$this->createCode();
		return strtolower($this->codeCheck);
	}
 }
