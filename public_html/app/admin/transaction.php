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

if (! defined('IN_ANWSION'))
{
	die();
}

class transaction extends AWS_ADMIN_CONTROLLER
{
	public function setup()
	{

		TPL::assign('menu_list',$this->fetch_menu_list());
	}


	public function trading_action(){
		$type=isset($_GET['type'])?intval($_GET['type']):1;
		$page=intval($_GET['page']);
		$table=$type==2?'sysaccount':'order_detail';
		$data=$this->model('pay_unipay')->get_all_orders($table,$page);
		TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
			'base_url' => get_js_url('/admin/transaction/trading/type-'.$type),
			'total_rows' => $data['count'],
			'per_page' =>15
		))->create_links());
		TPL::assign('list', $data['data']);
		TPL::output('admin/trade/trading');
	}
	public function apply_action(){
		TPL::assign('menu_list', $this->fetch_menu_list());
		if ($apply_list1 = $this->model('reward')->fetch_page('user_withdraw', '`status`=0', 'id DESC', intval($_GET['page']), $this->per_page))
		{
			$apply_total1 = $this->model('reward')->found_rows();
		}
		if ($apply_list2 = $this->model('reward')->fetch_page('user_withdraw', '`status`=1', 'id DESC', intval($_GET['page']), $this->per_page))
		{
			$apply_total2 = $this->model('reward')->found_rows();
		}
		if ($apply_list3 = $this->model('reward')->fetch_page('user_withdraw', '`status`=2', 'id DESC', intval($_GET['page']), $this->per_page))
		{
			$apply_total3 = $this->model('reward')->found_rows();
		}
		if ($apply_list4 = $this->model('reward')->fetch_page('user_withdraw', '`status`=3', 'id DESC', intval($_GET['page']), $this->per_page))
		{
			$apply_total4 = $this->model('reward')->found_rows();
		}
		TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
			'base_url' => get_js_url('/admin/reward/apply/') . implode('__', $url_param),
			'total_rows' => $apply_total,
			'per_page' => $this->per_page
		))->create_links());
		$this->crumb(AWS_APP::lang()->_t('提现申请'), 'admin/reward/apply/');

		TPL::assign('apply_total1', $apply_total1);
		TPL::assign('apply_total2', $apply_total2);
		TPL::assign('apply_total3', $apply_total3);
		TPL::assign('apply_total4', $apply_total4);
		TPL::assign('list1', $apply_list1);
		TPL::assign('list2', $apply_list2);
		TPL::assign('list3', $apply_list3);
		TPL::assign('list4', $apply_list4);
		TPL::output('admin/trade/apply');
	}
	public function preview_action(){
		$id=intval($_GET['id']);
		$data=$this->model('pay_unipay')->get_detail_order($id);
		TPL::assign('data', $data);
		TPL::output('admin/trade/preview');
	}
	public function previews_action(){
		$id=intval($_GET['id']);
		if ($apply = $this->model('reward')->fetch_row('user_withdraw',"`id`=".$id."" ))
		$apply['user_name']=$this->model('account')->get_user_info_by_uid($apply['uid'])['user_name'];
		TPL::assign('apply', $apply);
		TPL::output('admin/trade/previews');
	}

	public function export_excel_action(){
		$ids=implode(',',$_POST['cash']);
        $list=$this->model('reward')->fetch_all('user_withdraw',"status=0 and id in ($ids)");           
		$this->model('reward')->update('user_withdraw',array('status'=>1),"id in ($ids)");
		$this->model('excel')->export($list);
	}
	public function carry_action(){
		$id=intval($_GET['id']);
		$this->model('reward')->update('user_withdraw',array('status'=>3),'id='.$id);
		$mantotal=$this->model('reward')->fetch_one('sysaccount','total','','id desc');
		$user_withdraw=$this->model('reward')->fetch_row('user_withdraw','id='.$id);
		$this->model('reward')->insert('sysaccount',array(
				'money'=>$user_withdraw['fee'],
				'total'=>$mantotal+$user_withdraw['fee'],
				'uid'=>$user_withdraw['uid'],
				'addtime'=>time(),
				'remark'=>'提现手续费',
				'type'=>1,							
		));//个人
        H::ajax_json_output(AWS_APP::RSM(null, 1,null));
	}
}