<?php
/**
 * 模板引擎
 * @Description IdeaPHP框架内置
 * @Copyright   Copyright(c) 2016
 * @Author      Alan
 * @E-mail      20874823@qq.com
 * @Datetime    2016/04/16 21:06:58
 * @Version     1.0
 */
class Template{
    private static $instance       = null;             //模板实例
    private        $templateFile   = '';               //当前模板文件名
    private        $compileFile    = '';               //当前编译文件名
    private        $cacheFile      = '';               //当前缓存文件名
    private        $vars           = array();          //内部使用的临时变量
    public         $templatePath   = '';       //定义模板文件存放的目录  
    public         $compilePath    = '';        //定义通过模板引擎组合后文件存放目录
    public         $cachePath      = 'cache';          //定义通过模板引擎组合后文件存放目录
    public         $includePath    = '';              //定义通过模板引擎组合后文件存放目录
    public         $leftTag        = '{{';             //在模板中嵌入动态数据变量的左定界符号
    public         $rightTag       = '}}';             //在模板中嵌入动态数据变量的右定界符号
    public         $openCache      = false;             //是否开启缓存
    public         $cacheLifeTime  = 1;               //缓存文件有效时间
    public         $templateSuffix = '.html';          //模板文件后缀名
    //私有的构造函数, 不允许直接创建对象
    private function __construct(){}
    public static function GetInstance(){
        if(is_null(self::$instance)){
            self::$instance = new Template();
        }
            return self::$instance;
    }
    /**
     * 变量注入
     * @param  string $var   模板内变量名
     * @param  string $value 需要传递的值
     * @return [type]        [description]
     */
    public function assign($var,$value){
        if (isset($var)&&!empty($var)) {
            $this->vars[$var]=$value;
        }else{
            echo "请设置模板变量";
            exit;
        }
        if(!isset($value)){
            echo "请设置模板变量值";
        }
    }
    public function display($file){
        $this->fectch($file);
    }
    /**
     * 获取模板内容
     * @param  string $file 模板文件名
     * @return [type]       [description]
     */
    public function fectch($file){
        $this->initDir($file);
        //设置模板文件
        $this->templateFile=$this->templatePath.'/'.$file.$this->templateSuffix;
        if (!file_exists($this->templateFile)) {
            echo "<h2>Error：模块文件不存在</h2>错误信息：请检查文件或文件路径是否存在！".MODULE.'<br>';
            echo "模板文件不存在：",$this->templateFile;
            exit;
        }
        //编译文件
        $this->compileFile=$this->compilePath.'/'.md5($file).'_'.str_replace('/', '_', $file).'.php';
        //缓存时间
        $this->cacheLifeTime = $this->cacheLifeTime * 60;
        //缓存文件
        $this->cacheFile=$this->cachePath.'/'.md5($file).'_'.str_replace('/', '_', $file).'.html';
        //如果开启了缓存
        if ($this->openCache) {
            //缓存文件和编译文件都要存在
            if (file_exists($this->cacheFile) && file_exists($this->compileFile)) {
                //判断模板文件是否修改过，判断编译文件是否修改过
                if (filemtime($this->compileFile) >= filemtime($this->templateFile) && filemtime($this->cacheFile) + $this->cacheLifeTime >= filemtime($this->compileFile)) {
                    //载入缓存文件
                    include $this->cacheFile;
                    return;
                }
            }
        }
        //编译文件内容
        $this->templateContent=file_get_contents($this->templateFile);
        //如果编译文件不存在或模板文件被修改重新编译 
        if (!file_exists($this->compileFile)||filemtime($this->compileFile)<filemtime($this->templateFile)) {
            //mkdir($this->compilePath.'/'.$file);
            //引入编译方法
            $this->_compile($this->compileFile);
        }
        //载入编译文件
        include $this->compileFile;
        if ($this->openCache) {
            file_put_contents($this->cacheFile,ob_get_contents());
            ob_end_clean();
            include $this->cacheFile;
        }
    }
    /**
     * 初始化缓存目录
     * @param  string $file 模板文件名
     */
    private function initDir($file){
        if(!is_readable($this->compilePath)){
            is_file($this->compilePath) or mkdir($this->compilePath,755,true); 
        }
        if(!is_readable($this->cachePath)){
            is_file($this->cachePath) or mkdir($this->cachePath,755,true); 
        }
    }
    //解析普通变量
    private function parseVar(){
        $patten = '/('.$this->leftTag.')\$(([\w]+))('.$this->rightTag.')/';
        if (preg_match($patten,$this->templateContent)) {
            $this->templateContent = preg_replace($patten,"<?php echo \$this->vars['$2'];?>",$this->templateContent);
        }
    }
    private function parseForeach() {
        $pattenForeach = '/('.$this->leftTag.')foreach\s+\$([\w]+)\(([\w]+),([\w]+)\)('.$this->rightTag.')/';
        $pattenEndForeach = '/('.$this->leftTag.')\/foreach('.$this->rightTag.')/';
        $pattenVar = '/('.$this->leftTag.')\$([\w]+)\.([\w]+)([\w\-\>\+]*)('.$this->rightTag.')/';
        if (preg_match($pattenForeach,$this->templateContent)) {
            if (preg_match($pattenEndForeach,$this->templateContent)) {
                $this->templateContent = preg_replace($pattenForeach,"<?php foreach (\$this->vars['$2'] as \$$3=>\$$4) { ?>",$this->templateContent);
                $this->templateContent = preg_replace($pattenEndForeach,"<?php } ?>",$this->templateContent);
                if (preg_match($pattenVar,$this->templateContent)) {
                    $this->templateContent = preg_replace($pattenVar,"<?php echo \$$3$4; ?>",$this->templateContent);
                }
            } else {
                exit('ERROR：foreach语句必须有结尾标签！');
            }
        }
    }
    //解析include语句
    private function parseInclude() {
        $patten = '/\{include\s+file=\"(.+)\"\}/';
        if (preg_match($patten,$this->templateContent,$file)) {
            if (!file_exists($this->includePath.$file[1])) {
                echo "<h2>Error：包含文件出错</h2>错误信息：请检查文件或文件路径是否存在！<br>";
                echo "出错路径：".$this->includePath.$file[1]."<br>";
                exit;
            }
            $this->templateContent = preg_replace($patten,"<?php include \"$this->includePath\" . '$1'; ?>",$this->templateContent);
        }
    }
    /**
     * 代码注释
     * 使用方法，html中   
     * **这儿写注释内容**
     */
    private function parsCommon() {
        $patten = '/\*\*(.*)\*\*/';
        if (preg_match($patten,$this->templateContent)) {
            $this->templateContent = preg_replace($patten,"<?php /* $1 */ ?>",$this->templateContent);
        }
    }
    /**
     * 模板解析操作
     * @param  [type] $compileFile [description]
     * @return [type]              [description]
     */
    public function _compile($compileFile){
        if (!$this->templateContent=file_get_contents($this->templateFile)) {
            echo "模板读取失败";
        }
        //解析模板内容
        $this->parseVar();
        $this->parseIf();
        $this->parseForeach();
        $this->parseInclude();
        $this->parsCommon();
        //编译文件生成
        if (!file_put_contents($this->compileFile,$this->templateContent)) {
            echo "编译文件生成错误";
        }
    }
}