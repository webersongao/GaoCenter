<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/


if (!defined('IN_ANWSION'))
{
	die;
}

class main extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = 'black';
		$rule_action['actions'] = array();

		return $rule_action;
	}

	public function index_action()
	{
		if (!$page_info = $this->model('page')->get_page_by_url_token($_GET['id']) OR $page_info['enabled'] == 0)
		{
			HTTP::error_404();
		}

		if ($page_info['title'])
		{
			TPL::assign('page_title', $page_info['title'].' - 淘币虎社区');
		}

		if ($page_info['keywords'])
		{
			TPL::set_meta('keywords', $page_info['keywords']);
		}

		if ($page_info['description'])
		{
			TPL::set_meta('description', $page_info['description']);
		}

		TPL::assign('page_info', $page_info);

		TPL::output('page/index');
	}


    public function pay_action(){
    	$setting=get_setting('pay_config');
    	    	$money=get_setting('money_config');
    	$money['money']=explode(',',$money['money']);
		TPL::assign('setting', $setting);
		TPL::assign('money', $money);
		TPL::output('page/pay');
    }
    public function scan_action(){

		TPL::output('page/scan');
    }     
}