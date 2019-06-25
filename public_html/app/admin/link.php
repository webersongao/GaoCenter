<?php
if (!defined('IN_ANWSION'))
{
	die;
}

class link extends AWS_ADMIN_CONTROLLER
{
	

public function index_action()
{
	$where = array();
	if($_GET['status'])
	{
		$where[] .= 'status != 0';
	}
	else
	{
		$where[] .= 'status = 0';
	}
	$check_list = $this->model('link')->get_link_list($_GET['page'], $this->per_page, $where);
	
	$total_rows = $this->model('link')->link_list_total;
	
	TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
		'base_url' => get_setting('base_url') . '/?/status-' . $_GET['status'], 
		'total_rows' => $total_rows, 
		'per_page' => $this->per_page
	))->create_links());
		
	$this->crumb(AWS_APP::lang()->_t('友链管理'), "admin/link/");	
	TPL::assign('total_num', $total_rows);
	TPL::assign('list', $check_list);
	TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(1006));
	TPL::output('admin/link/check_list');
}

public function mulit_check_action()
{
	define('IN_AJAX', TRUE);
	
	if (! $_POST['ids'])
	{
		H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请选择需要审核的友链')));
	}
	
	$func = $_POST['batch_type'] . '_link';
	foreach ($_POST['ids'] AS $id)
	{
		$this->model('link')->$func($id);
	}

	H::ajax_json_output(AWS_APP::RSM(null, 1, null));
}
	
public function edit_action()
{
	if ($this->is_post())
	{
		$id = intval($_POST['id']);
		$site_name = $_POST['site_name'];
		$site_url = $_POST['site_url'];
		$status = $_POST['status'];
		$admin_contact = $_POST['admin_contact'];
		$info = $this->model('link')->get_link_info($id);
		if (!$info)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('该友链不存在或已删除')));
		}
		$update_data = array(
			'site_name' => $site_name,
			'site_url' => $site_url,
			'admin_contact' => $admin_contact,
			'status' => $status
		);
		$this->model('link')->update_link($id,$update_data);
		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}
	else
	{
		$id = intval($_GET['id']);
		$info = $this->model('link')->get_link_info($id);
		if (!$info)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('该友链不存在或已删除')));
		}
			
		$this->crumb(AWS_APP::lang()->_t('友链编辑'), "admin/link/edit/");	
		TPL::assign('info', $info);
		TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(1006));
		TPL::output('admin/link/check');
	}
}	


}