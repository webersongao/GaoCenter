<?php

if (!defined('IN_ANWSION'))
{
	die;
}

class mobile_app_config extends AWS_ADMIN_CONTROLLER
{

	public function setup()
	{
		$admin_menu = (array)AWS_APP::config()->get('admin_menu');

        $admin_menu['mobile_app_config']['select'] = true;

		TPL::assign('menu_list', $admin_menu);
	}

	//配置信息
	public function index_action()
	{
		if($_POST['mobile_app_secret'])
		{
			$errorinfo = $this->model('client')->save_mobile_app_secret(trim($_POST['mobile_app_secret']));
			if (!$errorinfo) {
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('保存设置成功')));
			}else{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('数据异常,保存失败')));
			}
		}
		TPL::output('admin/client/mobile_app_config');
	}	


	//Crash Log 列表
	public function app_log_action()
	{
		$list = $this->model('client')->get_app_log_list('', 'id DESC', $this->per_page, $_GET['page']);

		$total_rows = $this->model('client')->found_rows();

		$url_param = array();

		foreach($_GET as $key => $val)
		{
			if (!in_array($key, array('app', 'c', 'act', 'page')))
			{
				$url_param[] = $key . '-' . $val;
			}
		}

		TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
			'base_url' => get_js_url('/admin/app_log/app_log/') . implode('__', $url_param),
			'total_rows' => $total_rows,
			'per_page' => $this->per_page
		))->create_links());

		TPL::assign('total_rows', $total_rows);
		TPL::assign('list', $list);

		TPL::output('admin/client/app_log');
	}	

}