<?php
//自定义Model方法，对模型类进行单例化
function Model($modelName='',$modelMethot=null){
	if(!isset($modelMethot)){
    	return Singleton::getModelObject($modelName);
    }else{
    	return Singleton::getModelObject($modelName)->$modelMethot();
    }
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
