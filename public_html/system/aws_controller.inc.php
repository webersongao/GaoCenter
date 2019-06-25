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
 * WeCenter 前台控制器
 *
 * @package		WeCenter
 * @subpackage	System
 * @category	Libraries
 * @author		WeCenter Dev Team
 */
class AWS_CONTROLLER
{
	public $user_id;
	public $user_info;

	public function __construct($process_setup = true)
	{
		//升级程序能访问 不查询表数据
		if (!in_array($_GET['app'], array('upgrade')))
		{
			// 获取当前用户 User ID
			$this->user_id = AWS_APP::user()->get_info('uid');
			$this->check_ban();#封禁IP

			if ($this->user_info = $this->model('account')->get_user_info_by_uid($this->user_id, TRUE))
			{
				$user_group = $this->model('account')->get_user_group($this->user_info['group_id'], $this->user_info['reputation_group']);

				if ($this->user_info['default_timezone'])
				{
					date_default_timezone_set($this->user_info['default_timezone']);
				}

				$this->model('online')->online_active($this->user_id, $this->user_info['last_active']);
			}
			else if ($this->user_id)
			{
				$this->model('account')->logout();
			}
			else
			{
				$user_group = $this->model('account')->get_user_group_by_id(99);

				if ($_GET['fromuid'])
				{
					HTTP::set_cookie('fromuid', $_GET['fromuid']);
				}
			}
			#用户删除
	        if($this->user_info['is_del'] == 1){
	            H::redirect_msg('用户已被管理员删除.');
	        }
			$this->user_info['group_name'] = $user_group['group_name'];
			$this->user_info['permission'] = $user_group['permission'];

			AWS_APP::session()->permission = $this->user_info['permission'];

			if ($this->user_info['forbidden'] == 1)
			{
				$this->model('account')->logout();

				H::redirect_msg(AWS_APP::lang()->_t('抱歉, 你的账号已经被禁止登录'), '/');
			}
			else
			{
				TPL::assign('user_id', $this->user_id);
				TPL::assign('user_info', $this->user_info);
			}

			if ($this->user_id and ! $this->user_info['permission']['human_valid'])
			{
				unset(AWS_APP::session()->human_valid);
			}
			else if ($this->user_info['permission']['human_valid'] and ! is_array(AWS_APP::session()->human_valid))
			{
				AWS_APP::session()->human_valid = array();
			}
		}

        $css = 'common.css';
        if ($this->user_id&&$this->user_info['skin']) {
            $css = $this->user_info['skin'];
        }

		// 引入系统 CSS 文件
		TPL::import_css(array(
			'css/'.$css,
			'css/link.css',
			'js/plug_module/style.css',
		));

		if (get_setting('ui_style') != 'default')
		{
			// 卫博生 引入主题模板css文件
			TPL::import_css('css/'.get_setting('ui_style').'.css');
		}

		if (defined('SYSTEM_LANG'))
		{
			TPL::import_js(base_url() . '/language/' . SYSTEM_LANG . '.js');
		}

		if (HTTP::is_browser('ie', 8))
		{
			TPL::import_js(array(
				'js/jquery.js',
				'js/respond.js'
			));
		}
		else
		{
			TPL::import_js('js/jquery.2.js');
		}

		// 引入系统 JS 文件
		TPL::import_js(array(
			'js/jquery.form.js',
			'js/plug_module/plug-in_module.js',
			'js/aws.js',
			'js/aw_template.js',
			'js/layer/layer.js',
			'js/app.js',
		));

		// 产生面包屑导航数据
		$this->crumb(get_setting('site_name'), base_url());

		// 载入插件
		if ($plugins = AWS_APP::plugins()->parse($_GET['app'], $_GET['c'], 'setup'))
		{
			foreach ($plugins as $plugin_file)
			{
				include $plugin_file;
			}
		}

		if (get_setting('site_close') == 'Y' AND $this->user_info['group_id'] != 1 AND !in_array($_GET['app'], array('admin', 'account', 'upgrade')))
		{
			$this->model('account')->logout();

			H::redirect_msg(get_setting('close_notice'), '/account/login/');
		}

		if ($_GET['ignore_ua_check'] == 'TRUE')
		{
			HTTP::set_cookie('_ignore_ua_check', 'TRUE', (time() + 3600 * 24 * 7));
		}

		// 执行控制器 Setup 动作
		if ($process_setup)
		{
			$this->setup();
		}
	}

    /**
     * @description [封禁IP]
     * @author Laushow
     * @datatime 2018/10/9 17:05
     */
	public function check_ban()
    {
        $banip = $this->model('banip')->check_ip(fetch_ip());
        if ($banip) {
            H::redirect_msg('您的IP已被封禁,请联系管理员.');
        }
    }

	/**
	 * 控制器 Setup 动作
	 *
	 * 每个继承于此类库的控制器均会调用此函数
	 *
	 * @access	public
	 */
	public function setup() {}

	/**
	 * 判断当前访问类型是否为 POST
	 *
	 * 调用 $_SERVER['REQUEST_METHOD']
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function is_post()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * 调用系统 Model
	 *
	 * 于控制器中使用 $this->model('class')->function() 进行调用
	 *
	 * @access	public
	 * @param	string
	 * @return	object
	 */
	public function model($model = null)
	{
		return AWS_APP::model($model);
	}

	/**
	 * 产生面包屑导航数据
	 *
	 * 产生面包屑导航数据并生成浏览器标题供前端使用
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 */
	public function crumb($name, $url = null)
	{
		if (is_array($name))
		{
			foreach ($name as $key => $value)
			{
				$this->crumb($key, $value);
			}

			return $this;
		}

		$name = htmlspecialchars_decode($name);

		$crumb_template = $this->crumb;

		if (strlen($url) > 1 and substr($url, 0, 1) == '/')
		{
			$url = base_url() . substr($url, 1);
		}

		$this->crumb[] = array(
			'name' => $name,
			'url' => $url
		);

		$crumb_template['last'] = array(
			'name' => $name,
			'url' => $url
		);

		TPL::assign('crumb', $crumb_template);

		foreach ($this->crumb as $key => $crumb)
		{
			$title = $crumb['name'] . ' - ' . $title;
		}

		TPL::assign('page_title', htmlspecialchars(rtrim($title, ' - ')));

		return $this;
	}

	public function publish_approval_valid($content = null)
	{
		if ($this->user_info['permission']['is_administortar'] OR $this->user_info['permission']['is_moderator'])
		{
			return false;
		}

		if ($default_timezone = get_setting('default_timezone'))
		{
			date_default_timezone_set($default_timezone);
		}

		if ($this->user_info['permission']['publish_approval'] == 1)
		{
			if (!$this->user_info['permission']['publish_approval_time']['start'] AND !$this->user_info['permission']['publish_approval_time']['end'])
			{
				if ($this->user_info['default_timezone'])
				{
					date_default_timezone_set($this->user_info['default_timezone']);
				}

				return true;
			}

			if ($this->user_info['permission']['publish_approval_time']['start'] < $this->user_info['permission']['publish_approval_time']['end'])
			{
				if (intval(date('H')) >= $this->user_info['permission']['publish_approval_time']['start'] AND intval(date('H')) < $this->user_info['permission']['publish_approval_time']['end'])
				{
					if ($this->user_info['default_timezone'])
					{
						date_default_timezone_set($this->user_info['default_timezone']);
					}

					return true;
				}
			}

			if ($this->user_info['permission']['publish_approval_time']['start'] > $this->user_info['permission']['publish_approval_time']['end'])
			{
				if (intval(date('H')) >= $this->user_info['permission']['publish_approval_time']['start'] OR intval(date('H')) < $this->user_info['permission']['publish_approval_time']['end'])
				{
					if ($this->user_info['default_timezone'])
					{
						date_default_timezone_set($this->user_info['default_timezone']);
					}

					return true;
				}
			}

			if ($this->user_info['permission']['publish_approval_time']['start'] == $this->user_info['permission']['publish_approval_time']['end'])
			{
				if (intval(date('H')) == $this->user_info['permission']['publish_approval_time']['start'])
				{
					if ($this->user_info['default_timezone'])
					{
						date_default_timezone_set($this->user_info['default_timezone']);
					}
					return true;
				}
			}
		}

		if ($this->user_info['default_timezone'])
		{
			date_default_timezone_set($this->user_info['default_timezone']);
		}

		if ($content AND H::sensitive_word_exists($content))
		{
			return true;
		}

		return false;
	}

	/**
	 * API使用设备检测 检测是否为已授权的可信设备
	 */
	public function check_client_access($client_uuid)
	{
		if (!$client_uuid OR !$this->model('client')->verify_signature(get_class(), $client_uuid)) 
		{
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('非可信设备,暂无访问权限')));
		}else{
			return true;
		}

	}
	/**
	 * API接口 检查用户设备 登录状态(有参数验参数，不传就全验)
	 *
	 * 检查用户登录状态并返回状态信息 api_token 暂时固定为 mobile_sign
	 */

	public function check_client_login($user_id = null, $user_token = null, $api_token = null)
	{
		// 1、 参数是否完整
		if (!$user_id OR !$user_token OR !$api_token)  {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('参数不全,请检查配置')));
		}
		// 2、user_id 是否存在
		if (!$user_info = $this->model('account')->get_user_info_by_uid($user_id, TRUE))  {
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('用户不存在')));
		}
		// 3、api_token 是否有效
		if ($api_token AND !$this->model('client')->verify_signature(get_class(), $api_token)) {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('客户端 验签失败')));
		}

		return $user_id;
		// 4、 user_token 是否有效
		/*
		if (!$this->model('client')->verify_signature(get_class(), $_GET['mobile_sign'])) {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('AWS 验签失败')));
        }
        */

	}
}

/**
 * WeCenter 后台控制器
 *
 * @package		WeCenter
 * @subpackage	System
 * @category	Libraries
 * @author		WeCenter Dev Team
 */
class AWS_ADMIN_CONTROLLER extends AWS_CONTROLLER
{
	public $per_page = 20;

	public function __construct()
	{
		parent::__construct(false);

		if ($_GET['app'] != 'admin')
		{
			return false;
		}

		TPL::import_clean();

		if (defined('SYSTEM_LANG'))
		{
			TPL::import_js(base_url() . '/language/' . SYSTEM_LANG . '.js');
		}

		if (HTTP::is_browser('ie', 8))
		{
			TPL::import_js('js/jquery.js');
		}
		else
		{
			TPL::import_js('js/jquery.2.js');
		}

		TPL::import_js(array(
			'admin/js/aws_admin.js',
			'admin/js/aws_admin_template.js',
			'js/jquery.form.js',
			'admin/js/framework.js',
			'admin/js/global.js',
		));

		TPL::import_css(array(
			'admin/css/common.css'
		));

		if (in_array($_GET['act'], array(
			'login',
			'login_process',
		)))
		{
			return true;
		}

		$admin_info = json_decode(AWS_APP::crypt()->decode(AWS_APP::session()->admin_login), true);

		if ($admin_info['uid'])
		{
			if ($admin_info['uid'] != $this->user_id OR $admin_info['UA'] != $_SERVER['HTTP_USER_AGENT'] OR !AWS_APP::session()->permission['is_administortar'] AND !AWS_APP::session()->permission['is_moderator'])
			{
				unset(AWS_APP::session()->admin_login);

				if ($_POST['_post_type'] == 'ajax')
				{
					H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('会话超时, 请重新登录')));
				}
				else
				{
					H::redirect_msg(AWS_APP::lang()->_t('会话超时, 请重新登录'), '/admin/login/url-' . base64_current_path());
				}
			}
		}
		else
		{
			if ($_POST['_post_type'] == 'ajax')
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('会话超时, 请重新登录')));
			}
			else
			{
				HTTP::redirect('/admin/login/url-' . base64_current_path());
			}
		}

		$this->setup();
	}
	public function fetch_menu_list(){
		$c=$_GET['app'].'/'.$_GET['c'].'/'.$_GET['act'].'/';
		if($_GET['act']=='index')
		$c=$_GET['app'].'/'.$_GET['c'].'/';
		if($_GET['act']=='settings')
		$c=$_GET['app'].'/'.$_GET['act'].'/category-'.$_GET['category'];
		return $this->model('admin')->fetch_menu_list($c);
	}
}
