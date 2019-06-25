<?php
/**
 * WeCenter Framework
 *
 * An open source application development framework for PHP 5.2.2 or newer
 *
 * @package		WeCenter Framework
 * @author		WeCenter Dev Team
 * @copyright	Copyright (c) 2011 - 2014, WeCenter, Inc.
 * @license		http://www.wecenter.com/license/
 * @link		http://www.wecenter.com/
 * @since		Version 1.0
 * @filesource
 */

/**
 * WeCenter APP 函数类
 *
 * @package		WeCenter
 * @subpackage	App
 * @category	Libraries
 * @author		WeCenter Dev Team
 */


/**
 * 获取头像地址
 *
 * 举个例子：$uid=12345，那么头像路径很可能(根据您部署的上传文件夹而定)会被存储为/uploads/000/01/23/45_avatar_min.jpg
 *
 * @param  int
 * @param  string
 * @return string
 */
function get_avatar_url($uid, $size = 'min')
{
	$uid = intval($uid);

	if (!$uid)
	{
		return G_STATIC_URL . '/common/avatar-' . $size . '-img.png';
	}

	foreach (AWS_APP::config()->get('image')->avatar_thumbnail as $key => $val)
	{
		$all_size[] = $key;
	}

	$size = in_array($size, $all_size) ? $size : $all_size[0];

	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);

	if (file_exists(get_setting('upload_dir') . '/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, - 2) . '_avatar_' . $size . '.jpg'))
	{
		return get_setting('upload_url') . '/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, - 2) . '_avatar_' . $size . '.jpg';
	}
	else
	{
		return G_STATIC_URL . '/common/avatar-' . $size . '-img.png';
	}
}

/**
 * 获取文章封面图地址
 *
 * 举个例子：$id=12345，那么头像路径很可能(根据您部署的上传文件夹而定)会被存储为/uploads/000/01/23/45_avatar_min.jpg
 *
 * @param  int
 * @param  string
 * @return string
 */
function get_article_cover_url($id, $size = 'min')
{
    $id = intval($id);

	if (!$id)
	{
        return false;
	}

	foreach (AWS_APP::config()->get('image')->avatar_thumbnail as $key => $val)
	{
		$all_size[] = $key;
	}

	$size = in_array($size, $all_size) ? $size : $all_size[0];

    $id = sprintf("%09d", $id);
	$dir1 = substr($id, 0, 3);
	$dir2 = substr($id, 3, 2);
	$dir3 = substr($id, 5, 2);


	if (file_exists(get_setting('upload_dir') . '/article/cover/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($id, - 2) . '_article_cover_' . $size . '.jpg'))
	{
		return get_setting('upload_url') . '/article/cover/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($id, - 2) . '_article_cover_' . $size . '.jpg';
	}
    else
    {
        return false;
    }
}
function hook($plugin,$method,$params = []){
	$h=new AWS_PLUGIN($plugin,$method);
    return $h->trigger($plugin,$method,$params);
}
function recurse_copy($src,$dst) {  // 原目录，复制到的目录
 
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
function get_hook_config($plugin){
	$h=new AWS_PLUGIN($plugin,$method='');
    return $h->get_config($plugin);
}
function get_hook_info($plugin){
	$h=new AWS_PLUGIN($plugin,$method='');
    return $h->get_info($plugin);
}
function get_hook_model($plugin){
	$h=new AWS_PLUGIN($plugin,$method='');

    return $h->get_model($plugin);
}
/**
 * 分割字符串 
 * @param String $str  要分割的字符串 
 * @param int $length  指定的长度 
 * @param String $end  在分割后的字符串块追加的内容 
 */ 
function mb_chunk_split($q,$string, $length, $end='\r\n', $once = false){ 
    // $string = iconv('gb2312', 'utf-8', $string); 
    $array = array(); 
    $strlen = mb_strlen($string); 
    $b=0;
    while($strlen){ 
        $array[] = mb_substr($string, 0, $length, "utf-8"); 
        if($once) 
            return $array[0] . $end; 
        $string = mb_substr($string, $length, $strlen, "utf-8"); 
        $strlen = mb_strlen($string); 
    } 
	 foreach ($array as $key => $value) {
	 	if(stripos($value,$q)){
	 		$b=$key;
	 	    break;
	 	}
	 }
	 $data=array_slice($array,$b,3);	 
    return count($data)>1?implode(" ", $data).'...':implode(" ", $data); 
}   

function substr_text($str, $start=0, $length, $charset="utf-8", $suffix="...")
{
	if(function_exists("mb_substr")){
		return mb_substr($str, $start, $length, $charset).$suffix;
	}
	elseif(function_exists('iconv_substr')){
		return iconv_substr($str,$start,$length,$charset).$suffix;
	}
	$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	return $slice.$suffix;
}


/**
 * 附件url地址，实际上是通过一定格式编码指配到/app/file/main.php中，让download控制器处理并发送下载请求
 * @param  string $file_name 附件的真实文件名，即上传之前的文件名称，包含后缀
 * @param  string $url 附件完整的真实url地址
 * @return string 附件下载的完整url地址
 */
function download_url($file_name, $url)
{
	return get_js_url('/file/download/file_name-' . base64_encode($file_name) . '__url-' . base64_encode($url));
}

// 检测当前操作是否需要验证码
function human_valid($permission_tag)
{
	if (! is_array(AWS_APP::session()->human_valid))
	{
		return FALSE;
	}

	if (! AWS_APP::session()->human_valid[$permission_tag] or ! AWS_APP::session()->permission[$permission_tag])
	{
		return FALSE;
	}

	foreach (AWS_APP::session()->human_valid[$permission_tag] as $time => $val)
	{
		if (date('H', $time) != date('H', time()))
		{
			unset(AWS_APP::session()->human_valid[$permission_tag][$time]);
		}
	}

	if (sizeof(AWS_APP::session()->human_valid[$permission_tag]) >= AWS_APP::session()->permission[$permission_tag])
	{
		return TRUE;
	}

	return FALSE;
}

function set_human_valid($permission_tag)
{
	if (! is_array(AWS_APP::session()->human_valid))
	{
		return FALSE;
	}

	AWS_APP::session()->human_valid[$permission_tag][time()] = TRUE;

	return count(AWS_APP::session()->human_valid[$permission_tag]);
}
function get_user_name_by_uid($uid){
	return AWS_APP::model('account')->get_name($uid);
}
/**
 * 仅附件处理中的preg_replace_callback()的每次搜索时的回调
 * @param  array $matches preg_replace_callback()搜索时返回给第二参数的结果
 * @return string  取出附件的加载模板字符串
 */
function parse_attachs_callback($matches)
{
	if ($attach = AWS_APP::model('publish')->get_attach_by_id($matches[1]))
	{
		TPL::assign('attach', $attach);

		return TPL::output('question/ajax/load_attach', false);
	}
}

/**
 * 获取主题图片指定尺寸的完整url地址
 * @param  string $size
 * @param  string $pic_file 某一尺寸的图片文件名
 * @return string           取出主题图片或主题默认图片的完整url地址
 */
function get_topic_pic_url($size = null, $pic_file = null)
{
	if ($sized_file = AWS_APP::model('topic')->get_sized_file($size, $pic_file))
	{
		return get_setting('upload_url') . '/topic/' . $sized_file;
	}

	if (! $size)
	{
		return G_STATIC_URL . '/common/topic-max-img.png';
	}

	return G_STATIC_URL . '/common/topic-' . $size . '-img.png';
}

/**
 * 获取专题图片指定尺寸的完整url地址
 * @param  string $size     三种图片尺寸 max(100px)|mid(50px)|min(32px)
 * @param  string $pic_file 某一尺寸的图片文件名
 * @return string           取出专题图片的完整url地址
 */
function get_feature_pic_url($size = null, $pic_file = null)
{
	if (! $pic_file)
	{
		return false;
	}
	else
	{
		if ($size)
		{
			$pic_file = str_replace(AWS_APP::config()->get('image')->feature_thumbnail['min']['w'] . '_' . AWS_APP::config()->get('image')->feature_thumbnail['min']['h'], AWS_APP::config()->get('image')->feature_thumbnail[$size]['w'] . '_' . AWS_APP::config()->get('image')->feature_thumbnail[$size]['h'], $pic_file);
		}
	}

	return get_setting('upload_url') . '/feature/' . $pic_file;
}

function get_host_top_domain()
{
	$host = strtolower($_SERVER['HTTP_HOST']);

	if (strpos($host, '/') !== false)
	{
		$parse = @parse_url($host);
		$host = $parse['host'];
	}

	$top_level_domain_db = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me', 'jp', 'uk', 'ws', 'eu', 'pw', 'kr', 'io', 'us', 'cn');

	foreach ($top_level_domain_db as $v)
	{
		$str .= ($str ? '|' : '') . $v;
	}

	$matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";

	if (preg_match('/' . $matchstr . '/ies', $host, $matchs))
	{
		$domain = $matchs['0'];
	}
	else
	{
		$domain = $host;
	}

	return $domain;
}

function parse_link_callback($matches)
{
	if (preg_match('/^(?!http).*/i', $matches[1]))
	{
		$url = 'http://' . $matches[1];
	}
	else
	{
		$url = $matches[1];
	}

	if (stristr($url, 'http://%'))
	{
		return false;
	}

	if (stristr($url, 'http://&'))
	{
		return false;
	}

	if (!preg_match('#^(http|https)://(?:[^<>\"]+|[a-z0-9/\._\- !&\#;,%\+\?:=]+)$#iU', $url))
	{
		return false;
	}

	if (is_inside_url($url))
	{
		return '<a href="' . $url . '">' . FORMAT::sub_url($matches[1], 50) . '</a>';
	}
	else
	{
		return '<a href="' . $url . '" rel="nofollow" target="_blank">' . FORMAT::sub_url($matches[1], 50) . '</a>';
	}
}

function parse_link_callback_bbcode($matches)
{
	if (preg_match('/^(?!http).*/i', $matches[1]))
	{
		$url = 'http://' . $matches[1];
	}
	else
	{
		$url = $matches[1];
	}

	if (stristr($url, 'http://%'))
	{
		return false;
	}

	if (stristr($url, 'http://&'))
	{
		return false;
	}

	if (!preg_match('#^(http|https)://(?:[^<>\"]+|[a-z0-9/\._\- !&\#;,%\+\?:=]+)$#iU', $url))
	{
		return false;
	}

	return '[url=' . $url . ']' . FORMAT::sub_url($matches[1], 50) . '[/url]';
}

function is_inside_url($url)
{
	if (!$url)
	{
		return false;
	}

	if (preg_match('/^(?!http).*/i', $url))
	{
		$url = 'http://' . $url;
	}

	$domain = get_host_top_domain();

	if (preg_match('/^http[s]?:\/\/([-_a-zA-Z0-9]+[\.])*?' . $domain . '(?!\.)[-a-zA-Z0-9@:;%_\+.~#?&\/\/=]*$/i', $url))
	{
		return true;
	}

	return false;
}

function get_weixin_rule_image($image_file, $size = '')
{
	return AWS_APP::model('weixin')->get_weixin_rule_image($image_file, $size);
}

function import_editor_static_files()
{
	TPL::import_js('js/ueditor/ueditor.config.js');
	TPL::import_js('js/ueditor/ueditor.all.min.js');
	// TPL::import_js('js/ueditor/lang/zh-cn/zh-cn.js');
}

function get_chapter_icon_url($id, $size = 'max', $default = true)
{
	if (file_exists(get_setting('upload_dir') . '/chapter/' . $id . '-' . $size . '.jpg'))
	{
		return get_setting('upload_url') . '/chapter/' . $id . '-' . $size . '.jpg';
	}
	else if ($default)
	{
		return G_STATIC_URL . '/common/help-chapter-' . $size . '-img.png';
	}

	return false;
}

function base64_url_encode($parm)
{
	if (!is_array($parm))
	{
		return false;
	}

	return strtr(base64_encode(json_encode($parm)), '+/=', '-_,');
}

function base64_url_decode($parm)
{
	return json_decode(base64_decode(strtr($parm, '-_,', '+/=')), true);
}

function remove_assoc($from, $type, $id)
{
	if (!$from OR !$type OR !is_digits($id))
	{
		return false;
	}

	return $this->query('UPDATE ' . $this->get_table($from) . ' SET `' . $type . '_id` = NULL WHERE `' . $type . '_id` = ' . $id);
}
/**
 * 字符串截取，支持中文和其他编码（可去除HTML标签之后再截取）
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @param string $strip_tags 是否去除HTML标签
 * @return string
 */
function chinese_msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true,$strip_tags=true) {
    if($strip_tags)
    {
        $str = strip_tags($str);
    }
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
 
    if($suffix==true)
    {
        if(function_exists('mb_strlen'))
        {
            if(mb_strlen($str,$charset)>$length)
                $slice = $slice.'...';
        }
        else
        {$slice = 'mb_strlen未安装！';}
    }
    return  $slice;
}
