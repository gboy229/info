<?php

use think\Lang;
use think\Request;
use think\Db;
use think\Hook;

/**
 * 时间格式化
 * @param  string $time 时间戳
 * @param  string $format 时间格式
 * @return date
 */
function getdatetime($time, $format = 'Y-m-d H:i:s')
{
    return date($format, $time);
}


/**
 * 是否ajax请求
 */
function is_ajax()
{

    return Request::instance()->isAjax();
}

/**
 * 是否post请求
 */
function is_post()
{

    return Request::instance()->isPost();
}


/**
 * 电子邮箱格式判断
 * @param  string $email 字符串
 * @return boolean
 */
function is_email($email) {
    if (!empty($email)) {
        return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email);
    }
    return FALSE;
}

/**
 * 手机号码格式判断
 * @param string $string
 * @return boolean
 */
function is_mobile($string) {
    if (!empty($string)) {
        return preg_match('/^1[3|4|5|6|7|8|9][0-9]\d{8}$/', $string);
    }
    return FALSE;
}

/**
 * 邮政编码格式判断
 * @param string $string
 * @return boolean
 */
function is_zipcode($string) {
    if (!empty($string)) {
        return preg_match('/^[0-9][0-9]{5}$/', $string);
    }
    return FALSE;
}


/**
 * 获取语言变量值
 * @param string $name 语言变量名
 * @param string $lang 模块
 * @return mixed
 */
function L($name, $module = '')
{
    return Lang::to_get($name, $module);
}


/**
 * 通用提示页
 * @param  string  $msg 提示消息（支持语言包变量）
 * @param  integer $status 状态（0：失败；1：成功）
 * @param  string  $extra 附加数据
 * @param  string  $format 返回类型
 * @return mixed
 */
function showmessage($message, $jumpUrl = '-1', $status = 0, $extra = '', $format = '')
{
    if(empty($format)) {
        $format = is_ajax() ? 'json' : 'html';
    }

    switch($format) {
        case 'html':

            if(!defined('IN_ADMIN')) {
                if($jumpUrl == '-1' || $jumpUrl == '') {
                    echo "<script>history.go(-1);</script>";
                } else {
                    redirect($jumpUrl);
                }

            } else {


                $view = new \think\View();


                if(stripos($jumpUrl, 'formhash') === false) {

                    //$jumpUrl = $jumpUrl . config('pathinfo_depr') . 'formhash' . config('pathinfo_depr') . FORMHASH;

                    if(stripos($jumpUrl, '?') === false){
                        $pathinfo_depr='?';
                    }else{
                        $pathinfo_depr='&';
                    }

                    $jumpUrl = $jumpUrl . $pathinfo_depr.'formhash='  . FORMHASH;
                }


                echo $view->fetch(ROOT_PATH . 'public/template/showmessage.tpl', ['message' => $message, 'url' => $jumpUrl]);

            }

            break;
        case 'json':
            $result = array('status' => $status, 'referer' => $jumpUrl, 'message' => $message, 'result' => $extra);
            echo json_encode($result);
            exit;
            break;
        default:
            # code...
            break;
    }
    exit;
}


function apijson($msg,$status=0,$data=[]){

    $array=['status'=>$status,'msg'=>$msg,'data'=>$data];
    echo  json_encode($array,JSON_UNESCAPED_UNICODE);
    exit();
}




/**
 * 将list_to_tree的树还原成列表
 * @param  array  $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list 过渡用的中间数组，
 * @return array          返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if(is_array($tree)) {
        $refer = array();
        foreach($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @param array  $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author
 */
function list_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
{
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if($root == $parentId) {
                $tree[$data[$pk]] = &$list[$key];
            } else {
                if(isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][$data[$pk]] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array  $list 查询结果
 * @param string $field 排序的字段名
 * @param array  $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if(is_array($list)) {
        $refer = $resultSet = array();
        foreach($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}


/**
 * [more_array_unique 二维数组去重保留key值]
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function more_array_unique($arr)
{
    foreach($arr[0] as $k => $v) {
        $arr_inner_key[] = $k;
    }
    foreach($arr as $k => $v) {
        $v = join(',', $v);
        $temp[$k] = $v;
    }
    $temp = array_unique($temp);
    foreach($temp as $k => $v) {
        $a = explode(',', $v);
        $arr_after[$k] = array_combine($arr_inner_key, $a);
    }
    return $arr_after;
}

/**
 * 指定某字段的值删除二维数据
 * @param $multi_array 二维数组
 * @param $keys 指定键名
 * @param $value 指定值
 * @return array
 */
function multi_array_value_del(&$multi_array, $keys, $value)
{
    if($multi_array && is_array($multi_array)) {

        foreach($multi_array as $key => $val) {

            foreach($val as $k => $v) {

                if($v[$keys] == $value) {

                    unset($multi_array[$key][$k]);

                }
            }
            $multi_array[$key] = array_values($multi_array[$key]);
        }
    }
    return $multi_array;
}


/**
 * 多维数组合并（支持多数组）
 * @return array
 */
function array_merge_multi()
{
    $args = func_get_args();
    $array = array();
    foreach($args as $arg) {
        if(is_array($arg)) {
            foreach($arg as $k => $v) {
                if(is_array($v)) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : array();
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }
    return $array;
}


/**
 * 对多位数组进行排序
 * @param $multi_array 数组
 * @param $sort_key需要传入的键名
 * @param $sort排序类型
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC)
{
    if(is_array($multi_array)) {
        foreach($multi_array as $row_array) {
            if(is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return FALSE;
            }
        }
    } else {
        return FALSE;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
}

/**
 * 对多位数组重置键
 * @param $ar
 */
function multi_array_reset_key(&$ar) {
    if(! is_array($ar)) return;
    foreach($ar as $k=>&$v) {
        if(is_array($v)) multi_array_reset_key($v);
        if($k == 'children') $v = array_values($v);
    }
}


/**
 * XML转数组
 * @param string  $arr
 * @param boolean $isnormal
 * @return array
 */
function xml2array(&$xml, $isnormal = FALSE)
{

    $xml_parser = new \Org\Xml($isnormal);


    $data = $xml_parser->parse($xml);
    $xml_parser->destruct();
    return $data;
}


/**
 * 转换数据为HTML代码
 * @param array $data 数组
 */
function array2html($data) {
    if (is_array($data)) {
        $str = '[';
        foreach ($data as $key=>$val) {
            if (is_array($val)) {
                $str .= "'$key'=>".array2html($val).",";
            } else {
                if (strpos($val, '$')===0) {
                    $str .= "'$key'=>$val,";
                } else {
                    $str .= "'$key'=>'".daddslashes($val)."',";
                }
            }
        }
        $str = trim($str, ',');
        return $str.']';
    }
    return false;
}


/**
 * 序列化
 * @param mixed $string 原始信息
 * @param intval $force
 * @return mixed
 */
function daddslashes($string, $force = 1) {
    if(is_array($string)) {
        $keys = array_keys($string);
        foreach($keys as $key) {
            $val = $string[$key];
            unset($string[$key]);
            $string[addslashes($key)] = daddslashes($val, $force);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}


/**
 * 列队遍历文件夹
 * @access public
 * @param array   $dir 路径
 * @param boolean $is_absolute 是否显示绝定路径
 * @return array
 */
function read_dir_queue($dir, $is_absolute = false)
{
    $files = array();
    $queue = array($dir);
    while($data = each($queue)) {
        $path = $data['value'];
        if(is_dir($path) && $handle = opendir($path)) {
            while($file = readdir($handle)) {
                if($file == '.' || $file == '..')
                    continue;
                if(!$is_absolute) {
                    $files[] = $real_path = $file;
                } else {
                    $files[] = $real_path = $path . DS . $file;
                }

                if(is_dir($real_path))
                    $queue[] = $real_path;
            }
        }
        closedir($handle);
    }
    return $files;
}


/**
 * 字符串加解密
 * @param  string  $string 原始字符串
 * @param  string  $operation 加解密类型
 * @param  string  $key 密钥
 * @param  integer $expiry 有效期
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key != '' ? $key : '#$%^&*(DFGHJ)');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}


/**
 * 随机字符串
 * @param int $length 长度
 * @param int $numeric 类型(0：混合；1：纯数字)
 * @return string
 */
function random($length, $numeric = 0)
{
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    if($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}


/**
 * 获取客户端IP
 * @return string
 */
function getip()
{
    static $realip = NULL;
    if($realip !== NULL) {
        return $realip;
    }
    if(isset($_SERVER)) {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第x个非unknown的有效IP字符? */
            foreach($arr as $ip) {
                $ip = trim($ip);
                if($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if(isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '127.0.0.1';
            }
        }
    } else {
        if(getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '127.0.0.1';
    return $realip;
}


/**
 * 字节格式化
 * @access public
 * @param string $size 字节
 * @return string
 */
function byte_Format($size)
{
    $kb = 1024;          // Kilobyte
    $mb = 1024 * $kb;    // Megabyte
    $gb = 1024 * $mb;    // Gigabyte
    $tb = 1024 * $gb;    // Terabyte

    if($size < $kb)
        return $size . 'B';

    else if($size < $mb)
        return round($size / $kb, 2) . 'KB';

    else if($size < $gb)
        return round($size / $mb, 2) . 'MB';

    else if($size < $tb)
        return round($size / $gb, 2) . 'GB'; else
        return round($size / $tb, 2) . 'TB';
}



function stripslashes_array(&$array) {
    while(list($key,$var) = each($array)) {
        if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) {
            if (is_string($var)) {
                $array[$key] = stripslashes($var);
            }
            if (is_array($var))  {
                $array[$key] = stripslashes_array($var);
            }
        }
    }
    return $array;
}


function number_string($str){


    if(preg_match('/^[0-9a-zA-Z]+$/',$str)){
        return true;
    }else{
        return false;
    }

}


/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) {
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);

    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, '', $string);
    }
    return $string;
}


/*
 * 函数说明：截取指定长度的字符串
 * (UTF-8专用 汉字和大写字母长度算1，其它字符长度算0.5)
 *
 * @param  string  $sourcestr  原字符串
 * @param  int     $cutlength  截取长度
 * @param  string  $etc        省略字符...
 * @return string              截取后的字符串
 */

function restrlen($sourcestr, $cutlength = 10, $etc = '...') {
    $returnstr = '';
    $i = 0;
    $n = 0.0;
    $str_length = strlen($sourcestr); //字符串的字节数
    while (($n < $cutlength) and ( $i < $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = ord($temp_str); //得到字符串中第$i位字符的ASCII码
        if ($ascnum >= 252) { //如果ASCII位高与252
            $returnstr = $returnstr . substr($sourcestr, $i, 6); //根据UTF-8编码规范，将6个连续的字符计为单个字符
            $i = $i + 6; //实际Byte计为6
            $n++; //字串长度计1
        } elseif ($ascnum >= 248) { //如果ASCII位高与248
            $returnstr = $returnstr . substr($sourcestr, $i, 5); //根据UTF-8编码规范，将5个连续的字符计为单个字符
            $i = $i + 5; //实际Byte计为5
            $n++; //字串长度计1
        } elseif ($ascnum >= 240) { //如果ASCII位高与240
            $returnstr = $returnstr . substr($sourcestr, $i, 4); //根据UTF-8编码规范，将4个连续的字符计为单个字符
            $i = $i + 4; //实际Byte计为4
            $n++; //字串长度计1
        } elseif ($ascnum >= 224) { //如果ASCII位高与224
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 and $ascnum <= 90 and $ascnum != 73) { //如果是大写字母 I除外
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } elseif (!(array_search($ascnum, array(37, 38, 64, 109, 119)) === FALSE)) { //%,&,@,m,w 字符按1个字符宽
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，这些字条计成一个高位字符
        } else { //其他情况下，包括小写字母和半角标点符号
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数计1个
            $n = $n + 0.5; //其余的小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($i < $str_length) {
        $returnstr = $returnstr . $etc; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 *  完全过虑PHP，JS，css
 *
 * @access    public
 * @param     string  $str  需要过滤的字符串
 * @return    string
 */
function clearhtml($str) {

    $str = strip_tags($str);

    //首先去掉头尾空格
    $str = trim($str);

    //接着去掉两个空格以上的
    $str = preg_replace('/\s(?=\s)/', '', $str);

    //最后将非空格替换为一个空格
    $str = preg_replace('/[\n\r\t]/', ' ', $str);

    $str = str_replace(array('&nbsp;', '　'), '', $str);

    return trim($str);
}



/**
 * 根据日期生成唯一订单号
 * @param boolean $refresh 	是否刷新再生成
 * @return string
 */
function order_sn($refresh = FALSE) {
    if ($refresh == TRUE) {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
    }
    return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
}


/**
 * 恢复数据库
 * @param $file  sql文件
 *
 */

function db_restore_file($file)
{
    $sqls = file_get_contents($file);
    $sqlarr = explode(";\n", $sqls);
    foreach($sqlarr as &$sql) {
        Db::execute($sql);
    }
}

/**
 * 设置configs.php
 * @param $data 数组
 * @return bool|int
 */
function set_conifg($data,$file='configs')
{
    $file = APP_PATH . 'extra/'.$file.'.php';
    if(file_exists($file)) {
        $configs = include $file;
    }
    $configs = array_merge($configs, $data);
    return file_put_contents($file, "<?php\t \n\n return  " . var_export($configs, true) . ";");
}

/**
 * 强制下载
 * @param string $filename 文件名
 * @param string $content 内容
 */
function force_download_content($filename, $content)
{
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: binary");
    header("Content-Disposition: attachment; filename=$filename");
    echo $content;
    exit ();
}


/**
 * 列出本地目录的文件
 * @param string $path
 * @param string $pattern
 * @return array
 */
function list_file($path, $pattern = '*')
{
    if(strpos($pattern, '|') !== false) {
        $patterns = explode('|', $pattern);
    } else {
        $patterns [0] = $pattern;
    }
    $i = 0;
    $dir = array();
    if(is_dir($path)) {
        $path = rtrim($path, '/') . '/';
    }
    foreach($patterns as $pattern) {
        $list = glob($path . $pattern);
        if($list !== false) {
            foreach($list as $file) {
                $dir [$i] ['filename'] = basename($file);
                $dir [$i] ['path'] = dirname($file);
                $dir [$i] ['pathname'] = realpath($file);
                $dir [$i] ['owner'] = fileowner($file);
                $dir [$i] ['perms'] = substr(base_convert(fileperms($file), 10, 8), -4);
                $dir [$i] ['atime'] = fileatime($file);
                $dir [$i] ['ctime'] = filectime($file);
                $dir [$i] ['mtime'] = filemtime($file);
                $dir [$i] ['size'] = filesize($file);
                $dir [$i] ['type'] = filetype($file);
                $dir [$i] ['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                $dir [$i] ['isDir'] = is_dir($file);
                $dir [$i] ['isFile'] = is_file($file);
                $dir [$i] ['isLink'] = is_link($file);
                $dir [$i] ['isReadable'] = is_readable($file);
                $dir [$i] ['isWritable'] = is_writable($file);
                $i++;
            }
        }
    }
	$cmp_func = function ($a,$b){
			if( ($a["isDir"] && $b["isDir"]) || (!$a["isDir"] && !$b["isDir"]) ){
			return  $a["filename"]>$b["filename"]?1:-1;
		}else{
			if($a["isDir"]){
				return -1;
			}else if($b["isDir"]){
				return 1;
			}
			if($a["filename"]  ==  $b["filename"])  return  0;
			return  $a["filename"]>$b["filename"]?-1:1;
			}
		};
    usort($dir, $cmp_func);
    return $dir;
}


/**
 * 删除文件夹
 * @author rainfer <81818832@qq.com>
 * @param string
 * @param int
 */
function remove_dir($dir, $time_thres = -1)
{
    foreach(list_file($dir) as $f) {
        if($f ['isDir']) {
            remove_dir($f ['pathname'] . '/');
        } else if($f ['isFile'] && $f ['filename']) {
            if($time_thres == -1 || $f ['mtime'] < $time_thres) {
                @unlink($f ['pathname']);
            }
        }
    }
}



/**
 * 通过CURL发送HTTP请求
 * @param $url 请求URL
 * @param $postFields  发送参数
 * @return mixed|string
 */
function curl_post($url,$postFields){
    $postFields = json_encode($postFields);
    $ch = curl_init ();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8'
        )
    );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt( $ch, CURLOPT_TIMEOUT,1);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
    $ret = curl_exec ( $ch );
    if (false == $ret) {
        $result = curl_error(  $ch);
    } else {
        $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        if (200 != $rsp) {
            $result = "请求状态 ". $rsp . " " . curl_error($ch);
        } else {
            $result = $ret;
        }
    }
    curl_close ( $ch );
    return $result;
}

function http_post_data($url, $data) {

    //将数组转成json格式
    $data = json_encode($data);

    //1.初始化curl句柄
    $ch = curl_init();

    //2.设置curl
    //设置访问url
    curl_setopt($ch, CURLOPT_URL, $url);

    //捕获内容，但不输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //模拟发送POST请求
    curl_setopt($ch, CURLOPT_POST, 1);

    //发送POST请求时传递的参数
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    //设置头信息
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data))
    );

    //3.执行curl_exec($ch)
    $return_content = curl_exec($ch);

    //如果获取失败返回错误信息
    if($return_content === FALSE){
        $return_content = curl_errno($ch);
    }

    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //4.关闭curl
    curl_close($ch);

    return array($return_code, $return_content);
}


function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ( $post_data as $k => $v )
    {
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}




function sendsms($mobile,$content){




    return true;


}




/**
 * 一般用于发送验证码
 * @param $mobile 手机号码
 * @param string $content 内容
 */
function to_sendsms($mobile,$content='',$code=''){




    $sqlmap = [];
    $sqlmap['mobile'] = $mobile;
    $sqlmap['dateline'] = ['egt',time()-120];
    $vcode = db('vcode')->where($sqlmap)->order('dateline desc')->value('vcode');



    $data=[];

    if($vcode){
        $data['status']=0;
        $data['message']='请稍后再发送';
        $data['result']='';
    }else{

        if(!$content){
            $code=mt_rand(100000,999999);
            $content='您的验证码：'.$code;
        }


        $result=sendsms($mobile,$content);
        if($result!=1){
            $data['status']=0;
            $data['message']='验证码发送失败';
            $data['result']='';
        }else{
            $data['status']=1;
            $data['message']='验证码发送成功';
            $data['result']=$code;
        }

    }

    return $data;

}


/**
 * 验证手机验证码
 * @param $mobile 手机号码
 * @param $code 验证码
 * @param $action 动作
 */
function check_sms($mobile,$code,$action){

    $sqlmap = [];
    $sqlmap['mobile'] = $mobile;
    $sqlmap['action']=$action;
    $sqlmap['dateline'] = ['egt',time()-600];
    $vcode = db('vcode')->where($sqlmap)->order('dateline desc')->value('vcode');

    if($vcode!=$code){
       return false;
    }

    return true;

}


/**
 * 密码检测强度
 * @param $string
 * @return float|int
 */
function password_strength($str){
    $score=0;
    if(preg_match("/[0-9]+/",$str))
    {
        $score ++;
    }
    if(preg_match("/[0-9]{3,}/",$str))
    {
        $score ++;
    }
    if(preg_match("/[a-z]+/",$str))
    {
        $score ++;
    }
    if(preg_match("/[a-z]{3,}/",$str))
    {
        $score ++;
    }
    if(preg_match("/[A-Z]+/",$str))
    {
        $score ++;
    }
    if(preg_match("/[A-Z]{3,}/",$str))
    {
        $score ++;
    }
    if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/",$str))
    {
        $score += 2;
    }
    if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/",$str))
    {
        $score ++ ;
    }
    if(strlen($str) >= 10)
    {
        $score ++;
    }

    return $score;
}


/**
 * 直接执行行为
 * @param $hook 钩子文件
 * @param $action 执行钩子方法
 * @param $params 参数
 */
function hook_exec($hook,$action,$params){

    Hook::exec($hook,$action,$params);

}



/**
 * 错误页面
 * @param $code 状态码
 */
function error_status($code){


    $error_html=ROOT_PATH.'data/error/'.$code.'.html';
    if(!is_file($error_html)){
        http_response_code('404');
    }else{

        http_response_code($code);
        include  ROOT_PATH.'data/error/'.$code.'.html';
    }
    exit();


}


function phone_xx($phone){

    return substr_replace($phone, '****', 3, 4);
}





function array_different($array_1, $array_2) {
    $array_2 = array_flip($array_2); //将数组键值调换

    foreach ($array_1 as $key => $val) {
        if (isset($array_2[$val])) {
            unset($array_1[$key]);
        }
    }

    return $array_1;
}


function build_phone(){

	$arr = array(
		130,131,132,133,134,135,136,137,138,139,
		144,147,
		150,151,152,153,155,156,157,158,159,
		176,177,178,
		180,181,182,183,184,185,186,187,188,189,
	);

	$phone=$arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);

	return $phone;

}


function mb_trim($string, $trim)

{



    $mask = [];

    $trimLength = mb_strlen($trim, mb_internal_encoding());

    for ($i = 0; $i < $trimLength; $i++) {

        $item = mb_substr($trim, $i, 1, mb_internal_encoding());

        $mask[] = $item;

    }



    $len = mb_strlen($string, mb_internal_encoding());

    if ($len > 0) {

        $i = $len - 1;

        do {

            $item = mb_substr($string, $i, 1, mb_internal_encoding());

            if (in_array($item, $mask)) {

                $len--;

            } else {

                break;

            }

        } while ($i-- != 0);

    }



    return mb_substr($string, 0, $len, mb_internal_encoding());

}




function checkBankNo($bankNo) {
    // 奇数之和
    $sumOdd = 0;
    // 偶数之和
    $sumEven = 0;
    // 长度
    $length = strlen($bankNo);
    $wei = [];
    for ($i = 0; $i < $length; $i++) {
        $wei[$i] = substr($bankNo, $length - $i - 1, 1);// 从最末一位开始提取，每一位上的数值
    }
    for ($i = 0; $i < $length / 2; $i++) {
        $sumOdd += $wei[2 * $i];
        if (($wei[2 * $i + 1] * 2) > 9)
            $wei[2 * $i + 1] = $wei[2 * $i + 1] * 2 - 9;
        else
            $wei[2 * $i + 1] *= 2;
        $sumEven += $wei[2 * $i + 1];
    }
    if (($sumOdd + $sumEven) % 10 == 0) {
        return true;
    } else {
        return false;
    }
}


?>