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

class reward_class extends AWS_MODEL
{
	public function get_user_reward($table,$page,$where){
		$data['data'] = $this->fetch_page($table, $where, 'id DESC', $page, 15);
		$data['count'] = $this->count($table);
		return $data;
	}
	public function auto_set_show(){
		$question_list = $this->fetch_all('question', "reward_money > 0 AND is_reward =1 ");
		foreach ($question_list as $k => $v) {
			
				$haveanswer=$this->model('answer')->count('answer','question_id='.$v['question_id']);//回答信息
				if($haveanswer){
				$answer=$this->model('answer')->fetch_all('answer','question_id='.$v['question_id'].' and is_best=1');//回答信息
				foreach ($answer as $key => $value) {				
					if(0<count($answer) and count($answer)<3 and $v['reward_time']<=time() and $value['best_time']<$v['reward_time']){
							$this->update('question',array('is_reward'=>2,'best_time'=>time()),'question_id='.$v['question_id']);
					}
				}
			}elseif(!$haveanswer and time()>$v['reward_time']){//没有答案且悬赏日期结束则退款

		 			$this->refund_money($v['question_id'],$v['published_uid'],$v['reward_money']);
					$this->model('email')->send($v['published_uid'],'悬赏到期','“你发出的悬赏问题【'.$v['question_content'].'】 已到期，无人回答，悬赏金额已退回你的余额。', get_js_url('/question/'.$v['question_id'] ),$v['question_content']);

		 		}

			}
	}
	//定时任务设定最佳回复
	public function set_best_answer(){
		$question_list = $this->fetch_all('question', "reward_money > 0 AND is_reward =1 ");

		// var_dump($question_list);die;
		foreach ($question_list as $k => $v) {
				$have=$this->model('answer')->count('answer','question_id='.$v['question_id']);//回答信息
		// var_dump(!$have and time()>$v['reward_time']);die;

				if($have and time()>=$v['reward_time']){
					if($v['reward_time']+3*24*60*60<=time()){//用户和管理员都没有在期限内设置最佳回复系统自动设定
						$have_best=$this->model('answer')->count('answer','question_id='.$v['question_id'].' and is_best=1');//判断有没有最佳回复
						if($have_best>0){
								$this->model('answer')->update('question',array('is_reward'=>2,'best_time'=>time()),'question_id='.$v['question_id']);
							}else{
							$top_three_answers=$this->get_top_three($v['question_id']);//取前三，有一取一有二取二
							foreach ($top_three_answers as $key => $value) {
								$this->update('answer',array('is_best'=>1,'best_uid'=>0,'best_time'=>time()),'answer_id='.$value['answer_id']);
								$this->update('question',array('is_reward'=>2,'best_time'=>time()),'question_id='.$value['question_id']);
							}
						}
					}
		 		}
			}		
		}

	public function email_notice($uid){
		$question_list = $this->fetch_all('question', "reward_money > 0 AND is_reward =1 ");
		foreach ($question_list as $key => $value) {
			$have=$this->model('answer')->count('answer','question_id='.$value['question_id']);//回答信息
			$have_best=$this->model('answer')->count('answer','question_id='.$value['question_id'].' and is_best=1');//判断有没有最佳回复
				if($have and time()>=$value['reward_time'] and !$have_best){
						$this->model('email')->send($value['published_uid'],'设置最佳答案','您有悬赏到期（有回答）未设定最佳答案--您的问题需要选择最佳答案，请在24小时之内完成操作。', get_js_url('/question/'.$value['question_id'] ),$value['question_content']);
				}
		}

	}			
	//退款处理
	public function refund_money($question_id,$uid,$money){
		$user_balance=$this->fetch_one('user_account','balance','uid='.$uid);//账户余额
		$mager_balance=$this->fetch_one('sysaccount','total','','id desc');//平台账户余额
		$this->insert('sysaccount',array('total' => $mager_balance - $money,'type'=>2,'money'=>$money,'remark'=>'退款处理','uid'=>$uid,'addtime'=>time()));
		$this->insert('user_refund',array('both_id' => $question_id,'type'=>2,'money'=>$money,'remarks'=>'退款处理','uid'=>$uid,'addtime'=>time()));
		$this->update('user_account',array('balance' => $user_balance + $money), 'uid='.$uid);
        $this->update('question',array('is_reward' => 3), 'question_id='.$question_id);

        //通知用户处理。。。
	}
	public function get_shanger($id,$type){
		$reward=$this->fetch_all('user_reward','pay_status=1 and both_id='.$id.' and cate='.$type,'money desc',15);
			$data['count']=$this->count('user_reward','pay_status=1 and both_id='.$id.' and cate='.$type);
			$data['total']=$this->sum('user_reward','money','pay_status=1 and both_id='.$id.' and cate='.$type);

		foreach ($reward as $key => $value) {
				$reward[$key]['img']=get_avatar_url($value['main_uid'],'mid');
				$reward[$key]['user_name']=get_user_name_by_uid($value['main_uid']);
	
			// $total+=$value['money'];
		}
			$data['data']=$reward;
		return $data;
	}
	public function get_banlce($uid){
		return $this->fetch_row('user_account','uid='.$uid);
	}

	public function get_user_withdraw($uid){
		return $this->fetch_row('user_withdraw','uid='.$uid,'id desc');
	}
	public function get_reward_total(){
		return $this->count('question','is_reward>0');
	}
	public function get_reward($question_id){
		return $this->fetch_one('user_finance','reward_day','question_id='.$question_id);
	}
	public function get_user_finance($question_id){
		return $this->fetch_row('user_finance','question_id='.$question_id);
	}
	public function get_finance($page){
		return $this->fetch_all('question','is_reward!=0 and is_reward<>3','is_reward asc,add_time desc',get_setting('contents_per_page'),$page);
	}
	public function get_name($uid){
		return $this->fetch_one('users','user_name','uid='.$uid);
	}
	public function get_title($id,$cat){
		if($cat==2)
		return $this->fetch_one('article','title','id='.$id);
		else
		return $this->fetch_one('question','question_content','question_id='.$id);
	}
	public function get_appeal($question_id){
		return $this->fetch_one('answer_appeal','question_id','question_id='.$question_id.' and status=0');
	}
	public function get_order_info($id){
		return $this->fetch_one('user_reward','pay_status','id='.$id);
	}
	public function get_money($id,$cate){
		 $money=$this->query_all('select sum(money) as money from '.$this->get_table('user_reward').' where pay_status=1 and both_id='.$id.' and cate='.$cate);
		 return $money[0]['money'];
	}
	public function get_finance_info($id){
		return $this->fetch_row('user_finance','id='.$id);
	}

	public function allot_money_by_question(){
		$now=get_setting('publicity')*24*60*60;
		$time=time();
		$question_list = $this->fetch_all('question', "reward_money > 0 AND is_reward =2 and (best_time+$now)<$time");
		// var_dump(time());
		// var_dump($question_list);die;
		if($question_list){
			foreach ($question_list as $k => $v) {
				$this->top_three_money($v['question_id'],$v['reward_money']);//前三平分赏金，有一取一 有二取二
			}
		}
	}
	//有最佳回复者没有申诉分配赏金
	public function allot_answer($uid,$money,$answer_id,$question_id){
		$answer_user=$this->model('answer')->get_answer_by_id($answer_id);
		$answer_balance=$this->fetch_one('user_account','balance','uid='.$answer_user['uid']);
		$sys_balance=$this->fetch_one('sysaccount','total','','id desc');
        $this->update('user_account',array('balance' => $answer_balance + $money), 'uid='.$answer_user['uid']);
        $this->insert('sysaccount',array('total' => $sys_balance - $money,'type'=>2,'money'=>$money,'remark'=>'悬赏金分配处理','uid'=>$uid));
        $this->update('question',array('is_reward' => 4), 'question_id='.$question_id);
        $this->insert('finance_detail',array('money' => $money,'item_id'=>$question_id,'addtime'=>time(),'remarks'=>'悬赏金分配','uid'=>$uid));

        //通知用户处理。。。
	}

	//前三平分
	public function top_three_money($question_id,$money){

		if($answer_list = $this->fetch_all('answer', "question_id=$question_id and is_best=1")){
			$count=count($answer_list);
            $avg_float_money=0;
			$avg_int_money=(intval($money)/$count);
			$avg_float_money=floor($avg_int_money*100)/100; 
            if($avg_int_money * $count< $money){
                $avg_float_money = $avg_int_money + ($money - $avg_int_money * 3);//平均金融
            }
					// var_dump($avg_float_money);die;
			foreach ($answer_list as $key => $value) {
				$answer_balance=$this->fetch_one('user_account','balance','uid='.$value['uid']);//前三回复者账户余额
				$mager_balance=$this->fetch_one('sysaccount','total','','id desc');//平台账户余额
      		    $this->update('user_account',array('balance' => $answer_balance + $avg_float_money), 'uid='.$value['uid']);
			    $this->insert('sysaccount',array('total' => $mager_balance - $avg_float_money,'type'=>2,'money'=>$avg_float_money*$count,'remark'=>'悬赏金分配处理','uid'=>$value['uid'],'addtime'=>time()));
       			$this->update('question',array('is_reward' => 4), 'question_id='.$question_id);
      			$this->insert('finance_detail',array('money' => $avg_float_money,'item_id'=>$question_id,'addtime'=>time(),'remarks'=>'悬赏金分配','uid'=>$value['uid']));
       			 //通知用户处理。。。

			}
		}

	}

	//取前三
	public function get_top_three($question_id){
		$answer= $this->query_all("select * from ".$this->get_table('answer')." where question_id=$question_id group by uid order by agree_count desc,add_time desc");//按用户分组取出该用户最佳答案
		$voting= intval(get_setting('voting_weight'));//投票权重
		$participate= intval(get_setting('participate_weight'));//参与权重
		$user_info=array();
		$info=array();

		foreach ($answer as $key => $value) {
			$reputation=$this->fetch_one('users','reputation','uid='.$value['uid']);//威望
			$reply_count=$this->count('answer_comments','answer_id="'.$value['answer_id'].'"');//回答回复数量
			$against_value=$value['agree_count']-$value['against_count'];//投票值
			$against_count=$value['agree_count']+$value['against_count'];//投票次数
			$user_info[$key]['socre']=$voting*($against_value*$reputation)+$voting*($against_count+$reply_count);
			$user_info[$key]['uid']=$value['uid'];
			$user_info[$key]['answer_id']=$value['answer_id'];
			$user_info[$key]['add_time']=$value['add_time'];
			$user_info[$key]['question_id']=$value['question_id'];
		}
		
		  array_slice(array_multisort($info['socre'], SORT_DESC,$info['add_time'], SORT_DESC, $user_info),0,3);
		  return array_slice($user_info,0,3);
	}

	public function add_order($data){
		$order['question_id']=$data['question_id'];
		$order['uid']=$data['uid'];
		$order['type']=1;
		$order['money']=$data['money'];
		$order['reward_day']=$data['reward_day'];
		$order['out_trade_no']=time();
		$order['pay_way']=$data['pay_way'];
		$order['pay_status']=1;
		$order['addtime']=time();
		$ret=$this->insert('user_finance',$order);
		if($ret){
			return $ret;
		}
	}

	public function get_recharge_info_by_id($table,$recharge_id){
		if (!is_digits($recharge_id))
		{
			return false;
		}

		static $recharges;

		if (!$recharges[$recharge_id])
		{
			$recharges[$recharge_id] = $this->fetch_row($table, 'id = ' . $recharge_id);
		}

		return $recharges[$recharge_id];
	}

	//余额支付
	public function insert_recharge_datas($data){
		if (!$data){
			return false;
		}
		$this->begin_transaction(); 
        try{
            $user = $this->db()->fetchRow("SELECT * FROM ".$this->get_table('user_account')." WHERE uid={$data['uid']} for update");
           
            if(!$user){
            	die('未找到用户附属信息'); 
                $this->roll_back();                               
            }

            $recharge_id = $this->insert('user_finance', $data);
            if(!$recharge_id){
            	die('插入数据失败');
                $this->roll_back();                               
            }

            $handle = 0;

            if($data['pay_way'] == 3){   
            	//更新用户余额
            	$this->query_all("UPDATE ".$this->get_table('user_account')." SET balance=balance-{$data['money']} where uid ={$data['uid']}");
            		
				$question_info = $this->model('question')->get_question_info_by_id($data['question_id']);
				if(!$question_info){
					die('无该问题信息');
					$this->roll_back();					
				}

				$question_arr = array(
					'reward_money' => $data['money'],
					'reward_time' => $data['reward_day'],
					'is_reward' => 1,
				);
				//更新问题表 悬赏的相关字段
				if(!$this->model('question')->update('question', $question_arr, 'question_id = '.$data['question_id'])){
					die('更新问题数据失败');
					$this->roll_back();
									
				}
				$handle = 1;
            }

            $arr = array('recharge_id'=>$recharge_id,'handle'=>$handle);

            $this->commit();
            return $arr;
            
        }catch (Exception $e){
            $this->roll_back();
            die($e->getMessage());
        }
		
	}



}