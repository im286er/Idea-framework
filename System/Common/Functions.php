<?php
//自定义Model方法，对模型类进行单例化
function Model($modelName='',$modelMethot=null){
	if(!isset($modelMethot)){
    	return Singleton::getModelObject($modelName);
    }else{
    	return Singleton::getModelObject($modelName)->$modelMethot();
    }
}

function load_tpl($tpl, $open_token = true)
{
    $tpl = trim($tpl);
    if (!file_exists($tpl))
    {
        if (APP_DEBUG)
            die('template file: '.$tpl.' not exists. ');
        else
            die('template file not exists. ');
    }
    $cache_file = RUNTIME_CACHE.md5($tpl).'.php';
    if (!file_exists($cache_file) || filemtime($cache_file) < filemtime($tpl) || $open_token)
    {
        $content = file_get_contents($tpl);
        $content = str_replace("\r", '', $content);
        $content = str_replace("\n", '', $content);
        $token_key = substr(SITE_URL, 0, -1).$_SERVER['REQUEST_URI'];
        foreach ($_REQUEST as $k => $v)
        {
            if ($k == HIDDEN_TOKEN_NAME)
                continue;
            $token_key .= $k;
        }
        $token_key = md5($token_key);
        if ($open_token && count($_POST))
        {
            if (!isset($_SESSION[$token_key]) || !isset($_SESSION[HIDDEN_TOKEN_NAME]) || !isset($_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]]))
            {
                $val = md5(microtime());
                if (!isset($_SESSION[HIDDEN_TOKEN_NAME]) || !isset($_REQUEST[HIDDEN_TOKEN_NAME]))
                {
                    $_SESSION[HIDDEN_TOKEN_NAME] = $token_key;
                }
                $_SESSION[$token_key] = $val;
            }
            $content = preg_replace('/<form(.*?)>(.*?)<\/form>/i', '<form$1><input type="hidden" value="'.$_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]].'" name="'.HIDDEN_TOKEN_NAME.'"/>$2</form>', $content);
        }

        //parse include
        $ret = preg_match_all('/<\{\s*include\s*=\s*"(.*?)"\}>/i', $content, $match);
        if ($ret)
        {
            foreach ($match[1] as $k => $v)
            {
                $tArr = explode('/', $v);
                $tCount = count($tArr);
                if ($tCount == 3)
                    $content = str_ireplace($match[0][$k], '<?php require_once(load_tpl(APP_TPL."'.$tArr[0].'".\'/\'."'.$tArr[2].'".\'.html\')); ?>', $content);
                elseif ($tCount == 2)
                    $content = str_ireplace($match[0][$k], '<?php require_once(load_tpl(APP_TPL."'.$tArr[0].'".\'/\'."'.$tArr[1].'".\'.html\')); ?>', $content);
                unset($tArr);
            }
        }
        $content = preg_replace('/<\{\$(\w*?)\}>/i', '<?php echo \$${1}; ?>', $content);
        $content = preg_replace('/\{\s*u(.*?)\}/i', '<?php echo U${1}; ?>', $content);
        $content = preg_replace('/<\{\s*if\s*(.*?)\s*\}>/i', '<?php if(${1}) { ?>', $content);
        $content = preg_replace('/<\{\s*else\s*if\s*(.*?)\s*\}>/i', '<?php } elseif(${1}) { ?>', $content);
        $content = preg_replace('/<\{\s*else\s*\}>/i', '<?php } else { ?>', $content);
        $content = preg_replace('/<\{\s*\/if\s*\}>/i', '<?php } ?>', $content);
        $content = preg_replace('/<\{\s*loop(.*?)\s*\}>/i', '<?php foreach${1} { ?>', $content);
        $content = preg_replace('/<\{\s*\/loop\s*\}>/i', '<?php } ?>', $content);
        $content = preg_replace('/<\{\s*foreach(.*?)\s*\}>/i', '<?php foreach${1} { ?>', $content);
        $content = preg_replace('/<\{\s*\/foreach\s*\}>/i', '<?php } ?>', $content);
        $content = compress_html($content);
        file_put_contents($cache_file, $content);
    }
    return $cache_file;
}


function compress_html($string) {
    $string = str_replace("\r\n", '', $string);
    $string = str_replace("\n", '', $string);
    $string = str_replace("\t", '', $string);
    $pattern = array (
                    "/> *([^ ]*) *</",
                    "/[\s]+/",
                    "/<!--[\\w\\W\r\\n]*?-->/",
                    "'/\*[^*]*\*/'"
                    );
    $replace = array (
                    ">\\1<",
                    " ",
                    "",
                    ""
                    );
    return preg_replace($pattern, $replace, $string);
}
//get client IP if $num is true return int number else return ip address by string
//if invalid ip address return unknown
//note: this function maybe get Agent IP
function getIp($num = false)
{
    if (!isset($_SERVER['REMOTE_ADDR']))
        return 'unknown';
    else
    {
        $ip = trim($_SERVER['REMOTE_ADDR']);
        if (!ip2long($ip))
            return 'unknown';
        else
        {
            if ($num)
                return printf( '%u', ip2long($ip));
            else
                return $ip;
        }
    }
}

//safe model filter variable from $_REQUEST / $_POST / $_GET / $_COOKIE / $_SERVER
//default open safe model
function safe()
{
    if (!OPEN_SAFE_MODEL)
        return;
    if (is_array($_REQUEST) && !empty($_REQUEST))
    {
        foreach ($_REQUEST as $k => $v)
        {
            $is_get = isset($_GET[$k]) ? true : false;
            $is_post = isset($_POST[$k]) ? true : false;
            $v = trim($v);
            unset($_REQUEST[$k], $_GET[$k], $_POST[$k]);
            $k = trim($k);
            $k = urldecode($k);
            $v = urldecode($v);

            if ($k != addslashes($k) || $k != strip_tags($k) || htmlspecialchars($k) != $k || (strpos($k, '%') !== false))
                die('you are too young too simple, you ip:'.getIp());
            //integer value
            if (stripos($k, 'i_') === 0)
                $v = intval($v);
            //float value
            elseif (stripos($k, 'f_') === 0)
                $v = floatval($v);
            //double value
            elseif (stripos($k, 'd_') === 0)
                $v = doubleval($v);
            //text value
            elseif (stripos($k, 't_') === 0)
                $v = trim(strip_tags($v));
            //html value
            elseif (stripos($k, 'h_') === 0)
                $v = '<pre>'.trim(htmlspecialchars($v)).'</pre>';
            if ($is_get)
                $_GET[$k] = $v;
            if ($is_post)
                $_POST[$k] = $v;
            $_REQUEST[$k] = $v;
        }
    }

    if (is_array($_SERVER) && !empty($_SERVER))
    {
        foreach ($_SERVER as $k => $v)
        {
            if (is_array($v))
                continue;
            $v = trim($v);
            $k = trim($k);

            if ($k != addslashes($k) || $k != strip_tags($k) || htmlspecialchars($k) != $k || (strpos($k, '%') !== false))
                die('you are too young too simple, you ip:'.getIp());
        }
    }

    if (is_array($_COOKIE) && !empty($_COOKIE))
    {
        foreach ($_COOKIE as $k => $v)
        {
            $v = trim($v);
            unset($_COOKIE[$k]);
            $k = trim($k);
            $k = urldecode($k);
            $v = urldecode($v);

            if ($k != addslashes($k) || $k != strip_tags($k) || htmlspecialchars($k) != $k || (strpos($k, '%') !== false))
                die('you are too young too simple, you ip:'.getIp());
            //integer value
            if (stripos($k, 'i_') === 0)
                $v = intval($v);
            //float value
            elseif (stripos($k, 'f_') === 0)
                $v = floatval($v);
            //double value
            elseif (stripos($k, 'd_') === 0)
                $v = doubleval($v);
            //text value
            elseif (stripos($k, 't_') === 0)
                $v = trim(strip_tags($v));
            //html value
            elseif (stripos($k, 'h_') === 0)
                $v = trim(htmlspecialchars($v));
            $_COOKIE[$k] = $v;
        }
    }
}
