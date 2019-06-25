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

class withdraw_class extends AWS_MODEL
{
	public function get_withdraw_info_by_id($withdraw_id){
		if (!is_digits($withdraw_id))
		{
			return false;
		}

		static $withdraws;

		if (!$withdraws[$withdraw_id])
		{
			$withdraws[$withdraw_id] = $this->fetch_row('user_withdraw', 'id = ' . $withdraw_id);
		}

		return $withdraws[$withdraw_id];
	}

	public function update_withdraw_attrib_fields($data, $id){  
		$this->begin_transaction();	
    	try{
			$withdraw = $this->db()->fetchRow("SELECT * FROM ".$this->get_table('user_withdraw')." WHERE id={$id} for update");
			if($withdraw['status'] != 0){
				die('该条提现信息已处理');
				$this->roll_back();									
			}
			if($data['status'] == 2){
				//退款
				$money = $withdraw['money'] + $withdraw['fee'];
				$this->query_all("UPDATE ".$this->get_table('user_account')." SET balance=balance+{$money} where uid ={$withdraw['uid']}");
			}
			$data['updatetime'] = time();
			if(!$this->update('user_withdraw', $data, 'id = ' . intval($id))){
				die('更新数据失败');
				$this->roll_back();								
			}
			
			$this->commit();
			return true;
			
		}catch (Exception $e){
			die($e->getMessage());
			$this->roll_back();			
		}
    }

    public function remove_withdraw($withdraw_id){
		if (!$article_info = $this->get_withdraw_info_by_id($withdraw_id))
		{
			return false;
		}

		return $this->update('user_withdraw', array(
			'hide' => 1,
			'updatetime' => time()
		), 'id = ' . intval($withdraw_id));
	}

	public function get_month_withdraw_info_by_uid($uid,$start,$end){
		if (!is_digits($uid)){
			return false;
		}
		$start = date('Y-m-01 00:00:00'); 
        $end = date('Y-m-d 23:59:59', strtotime("$start +1 month -1 day"));

		$num = $this->count('user_withdraw', 'uid = ' . $uid.' AND addtime BETWEEN '.strtotime($start).' AND '.strtotime($end));
		return $num;
	}

	public function insert_withdraw_datas($datas){
		if (!$datas){
			return false;
		}
		
		$this->begin_transaction();	
		try{
			$attrib = $this->db()->fetchRow("SELECT * FROM ".$this->get_table('user_account')." WHERE uid={$datas['uid']} for update");

			if(!$this->insert('user_withdraw', $datas)){
				die('插入提现信息失败');
				$this->roll_back();				
			}

			$money = $datas['money'] + $datas['fee'];
			$this->query_all("UPDATE ".$this->get_table('user_account')." SET balance=balance-{$money} where uid ={$datas['uid']}");

			$this->commit();
			return true;

		}catch (Exception $e){
			die($e->getMessage());
			$this->roll_back();			
		}
	}
}