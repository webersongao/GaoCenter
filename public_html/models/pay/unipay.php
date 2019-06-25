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
 
class pay_unipay_class extends AWS_MODEL{
	public function __construct(){
		spl_autoload_register(function ($class) {
		    if (stripos($class, 'Pay\\') === 0) {
		        list($search, $replace) = [['\\', 'Pay/'], ['/', 'src/']];
		        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace($search, $replace, $class) . '.php';
		        file_exists($filename) && include $filename;
		    }
		});
		$this->config = require(__DIR__ . '/config.php');	
		require(__DIR__ . '/src/Qrcode/phpqrcode.php');
		require_once __DIR__ . '/log.php';
		//初始化日志
		$logHandler= new CLogFileHandler(__DIR__ . "/logs/".date('Y-m-d').'.log');
		$log = Log::Init($logHandler, 15);
	}

	public function alipay($data){
		$options = [
		    'out_trade_no' => $data['out_trade_no'], 
		    'total_amount' => $data['money'], 
		    'subject'      => $data['remarks'], 
		];
		$this->config['alipay']['notify_url']=$data['notify_url'];
		$pay = new \Pay\Pay($this->config);
        try {
			$result = $pay->driver('alipay')->gateway($data['gateway'])->apply($options);
            if($data['gateway']=='scan'){
                $name='/uploads/pay/'.$data['out_trade_no'].'.jpg';
                $filename ='.'.$name;
                $this->qr_code($result['qr_code'],$filename);
                return $name;
            }
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
	}
	public function wxpay($data){
		$options = [
		    'out_trade_no'     => $data['out_trade_no'], 
		    'total_fee'        =>1, 
		    'body'             => $data['remarks'],
		    'spbill_create_ip' => '127.0.0.1', 
		];
		$this->config['wechat']['notify_url']=$data['notify_url'];
		$pay = new \Pay\Pay($this->config);
        try {
            $result = $pay->driver('wechat')->gateway($data['gateway'])->apply($options);
            if($data['gateway']=='scan'){
                $name='/uploads/pay/'.$data['out_trade_no'].'.jpg';
                $filename ='.'.$name;
                $this->qr_code($result,$filename);
                return $name;
            }
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
	}
	public function yepay($data){
		// $this->query("UPDATE ".$this->model('account')->get_table('user_account')." SET balance=balance-{$data['money']} where uid ={$data['uid']}");
		$order['pay_time']=time();
		$order['pay_status']=1;
		$order['out_trade_no']=$data['out_trade_no'];
		$order['money']=$data['money'];
		$order['subject']=$data['remarks'];
		$order['trade_status']='OK';
		HTTP::request($data['notify_url'],'POST',$order);
	}
    public function qr_code($url,$filename){
        QRcode::png($url,$filename);
    }

    public function dopay($data){

        if($data['driver']=='alipay'){
           return $this->alipay($data);
        }
        if($data['driver']=='wechat'){
            return $this->wxpay($data);
        }
        if($data['driver']=='yepay'){

            return $this->yepay($data);
        }
    }
	public function get_order($uid,$offset){

		$data=$this->model('account')->fetch_all('order_detail','uid='.$uid,'time desc',10,$offset);

		return $data;
	}
	public function get_detail_order($id){
		$detail = $this->model('account')->fetch_row('order_detail',"`id`=".$id."" );
		switch ($detail['relation_type']) {
			case 1:
				$consume_detail=$this->model('account')->fetch_row('consume_detail',"`id`=".$detail['relation_id']."" );
				$item=$this->model('account')->fetch_row('consult',"`id`=".$consume_detail['relation_id']."" );;
				$detail['item']['title']='<a style="color:#000;" target="__blank" href="/consult/'.$item['question_id'].'">'.$item['question_content'].'</a>';
				$detail['item']['id']=$item['id'];
				break;
			case 2:
				$reward_detail=$this->model('account')->fetch_row('user_reward',"`id`=".$detail['relation_id']."" );
				if($reward_detail['cate']==2){
					$item=$this->model('account')->fetch_row('article',"`id`=".$reward_detail['both_id']."" );
					$detail['item']['title']='<a style="color:#000;" target="__blank" href="/article/'.$item['id'].'">'.$item['question_content'].'</a>';
					$detail['item']['id']=$item['id'];
				}else{
					$question_id=$this->model('account')->fetch_one('answer','question_id',"`answer_id`=".$reward_detail['both_id']."" );
					$item=$this->model('account')->fetch_row('question',"`question_id`=".$question_id."" );
					$detail['item']['title']='<a style="color:#000;" target="__blank" href="/question/'.$item['question_id'].'">'.$item['question_content'].'</a>';
					// $detail['item']['id']=$item['question_id'];
				}
			case 5:
				$finance_detail=$this->model('account')->fetch_row('user_finance',"`id`=".$detail['relation_id']."" );
				$item=$this->model('account')->fetch_row('question',"`question_id`=".$finance_detail['question_id']."" );
				$detail['item']['title']='<a style="color:#000;" target="__blank" href="/question/'.$item['question_id'].'">'.$item['question_content'].'</a>';
				// $detail['item']['id']=$item['question_id'];
				break;			
			default:
				# code...
				break;
		}
		return $detail;
	}

	public function get_all_orders($table,$page){
		$data['data']=$this->model('account')->fetch_all($table,'','id desc',calc_page_limit($page, 15));
		$data['count']=$this->model('account')->count($table);
		return $data;
	}
    public function get_pay_obj(){
    return new \Pay\Pay($this->config);
  }
  public function add_order_detail($data){
  	return $this->model('account')->insert('order_detail',$data);
  }
  public function callbalk(){
  		$_POST=!empty($_POST)?$_POST:file_get_contents('php://input');
		$out_trade_no=$_POST['out_trade_no'];//本系统订单号
		$recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_reward')." WHERE out_trade_no='{$out_trade_no}' for update");
		$title=$recharge['cate']==1?'问题回复打赏':'文章打赏';
   		$adddetail=[
  			'title'=>$title.'收入',
  			'no'=>$recharge['out_trade_no'],
  			'trade_no'=>$recharge['trade_no'],
  			'consume_type'=>5,
  			'mode'=>$recharge['type'],
  			'relation_type'=>2,
  			'relation_id'=>$recharge['id'],
  			'amount'=>$recharge['money'],
  			'time'=>$_POST['pay_time'],
  			'status'=>1,
  			'uid'=>$recharge['passive_uid'],
  			'type'=>1
  		];
  		$detail=[
  			'title'=>$title.'支出',
  			'no'=>$recharge['out_trade_no'],
  			'trade_no'=>$recharge['trade_no'],
  			'consume_type'=>6,
  			'mode'=>$recharge['type'],
  			'relation_type'=>2,
  			'relation_id'=>$recharge['id'],
  			'amount'=>$recharge['money'],
  			'time'=>$_POST['pay_time'],
  			'status'=>1,
  			'uid'=>$recharge['main_uid'],
  			'type'=>2
  		];
  		$this->add_order_detail($adddetail);
  		$this->add_order_detail($detail);
  	if ($_POST['trade_status'] == 'OK'){
  		$this->model('reward')->update('user_reward',array('pay_status'=>1,'pay_time'=>$_POST['pay_time']),'out_trade_no='.$_POST['out_trade_no']);
		$this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance+{$recharge['money']} where uid ={$recharge['passive_uid']}");//个人
		$this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance-{$recharge['money']} where uid ={$recharge['main_uid']}");//个人

  	}
  	$pay = new \Pay\Pay($this->config);
	if ($pay->driver('alipay')->gateway()->verify($_POST)) {
		if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
			$order['pay_time']=strtotime($_POST['gmt_payment']);
			$order['pay_status']=1;
			$order['qrcode']='';
			$order['trade_no']=$_POST['trade_no'];//支付宝订单号
			$out_trade_no=$_POST['out_trade_no'];//本系统订单号
			$this->model('reward')->begin_transaction();
			// $recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_reward')." WHERE out_trade_no='{$out_trade_no}' for update");
    		//根据订单号 查找订单信息
			if($recharge['pay_status'] != 0){
				die('已处理');
				$this->model('reward')->roll_back();										
			}
			if($recharge['both_id']){
					@unlink($recharge['qrcode']);
					//查找用户信息
					$user = $this->model('reward')->query_row("SELECT uid FROM ".$this->model('reward')->get_table('user_account')." WHERE uid={$recharge['passive_uid']}");
					if(!$user){
						die('找不到用户附属信息');	
						$this->model('reward')->roll_back();									
					}
					if(!$this->model('reward')->update('user_reward', $order, 'out_trade_no = "' .$out_trade_no.'"')){
						die('更新数据失败');
						$this->model('reward')->roll_back();									
					}
					$this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance+{$recharge['money']} where uid ={$recharge['passive_uid']}");//个人
					$this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance-{$recharge['money']} where uid ={$recharge['main_uid']}");//个人
			}
			$this->model('reward')->commit();
			echo "success";die;	
	    }else{
			$arr = array(
					'pay_status' => 2,
					'pay_time' => time(),
					'remarks' => '失败'
				);
			$this->model('reward')->update('user_reward', $arr, 'out_trade_no = "' .$out_trade_no.'"');
		}
	}
			if($verify = $pay->driver('wechat')->gateway('scan')->verify(file_get_contents('php://input'))){
				$order['pay_time']=strtotime($verify['time_end']);
				$order['pay_status']=1;
				$order['qrcode']='';
				$order['trade_no']=$verify['transaction_id'];
				$out_trade_no = $verify['out_trade_no'];//本系统订单号
				if ($verify['return_code'] == 'SUCCESS') {
					$order['pay_time']=strtotime($verify['time_end']);
					$order['pay_status']=1;
					$order['qrcode']='';
					$order['trade_no']=$verify['transaction_id'];
					$out_trade_no = $verify['out_trade_no'];//本系统订单号
					$this->model('reward')->begin_transaction();
					$recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_reward')." WHERE out_trade_no='{$out_trade_no}' for update");
		    		//根据订单号 查找订单信息
					if($recharge['pay_status'] != 0){
						die('已处理');
						$this->model('reward')->roll_back();										
					}
					if($recharge['both_id']){
							@unlink($recharge['qrcode']);
							//查找用户信息
							$user = $this->model('reward')->query_row("SELECT uid FROM ".$this->model('reward')->get_table('user_account')." WHERE uid={$recharge['passive_uid']}");
							if(!$user){
								die('找不到用户附属信息');	
								$this->model('reward')->roll_back();									
							}
							if(!$this->model('reward')->update('user_reward', $order, 'out_trade_no = "' .$out_trade_no.'"')){
								die('更新数据失败');
								$this->model('reward')->roll_back();									
							}
							$this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance+{$recharge['money']} where uid ={$recharge['passive_uid']}");//个人
					}
					$this->model('reward')->commit();
					echo "success";die;	
			    }else{
					$arr = array(
							'pay_status' => 2,
							'pay_time' => time(),
							'remarks' => '失败'
						);
					$this->model('reward')->update('user_reward', $arr, 'out_trade_no = "' .$out_trade_no.'"');
				}
			}
  }

  public function finance_callbalk(){
  		$_POST=!empty($_POST)?$_POST:file_get_contents('php://input');
		$out_trade_no=$_POST['out_trade_no'];//本系统订单号
		$recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_finance')." WHERE out_trade_no='{$_POST['out_trade_no']}' for update");

		// $recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_reward')." WHERE out_trade_no='{$out_trade_no}' for update");
   		$adddetail=[
  			'title'=>$recharge['remarks'],
  			'no'=>$recharge['out_trade_no'],
  			'trade_no'=>$recharge['trade_no'],
  			'consume_type'=>5,
  			'mode'=>$recharge['pay_way'],
  			'relation_type'=>2,
  			'relation_id'=>$recharge['id'],
  			'amount'=>$recharge['money'],
  			'time'=>$recharge['pay_time'],
  			'status'=>1,
  			'uid'=>$recharge['uid'],
  			'type'=>2
  		];

  		$this->add_order_detail($adddetail);


  	if ($_POST['trade_status'] == 'OK'){
		// $recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_finance')." WHERE out_trade_no='{$_POST['trade_no']}' for update");
  		$ret=$this->model('reward')->update('user_finance',array('pay_status'=>1,'pay_time'=>$_POST['pay_time']),"out_trade_no='".$_POST['out_trade_no']."'");
		$this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance-{$recharge['money']} where uid ={$recharge['uid']}");//个人
		$question_info = $this->model('question')->get_question_info_by_id($recharge['question_id']);
		if(!$question_info){
			die('无该问题信息');
			$this->roll_back();					
		}
		$mantotal=$this->model('reward')->fetch_one('sysaccount','total','','id desc');
                // error_log("欧阳调试>>>" . print_r($mantotal, true), 3,  './QQQq.php'); 
		$this->model('reward')->insert('sysaccount',array(
				'money'=>$recharge['money'],
				'total'=>$recharge['money']+$mantotal,
				'uid'=>$recharge['uid'],
				'addtime'=>time(),
				'remark'=>'悬赏入账',
				'type'=>1,							
		));//个人
		$question_arr = array(
			'reward_money' => $recharge['money'],
			'reward_time' => $recharge['reward_day'],
			'is_reward' => 1,
			'is_del' => 0,
		);
		//更新问题表 悬赏的相关字段
		if(!$this->model('question')->update('question', $question_arr, 'question_id = '.$recharge['question_id'])){
			die('更新问题数据失败');
			$this->roll_back();
		}
		if(!$this->update('posts_index',array('is_del'=>0), 'post_type =question and post_id= '.$recharge['question_id'])){
			die('更新问题数据失败');
			$this->roll_back();
		}		
  	}
  	$pay = new \Pay\Pay($this->config);
	if ($pay->driver('alipay')->gateway()->verify($_POST)) {
		if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
			$out_trade_no=$_POST['out_trade_no'];//本系统订单号
			$this->model('reward')->begin_transaction();
			// $recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_finance')." WHERE out_trade_no='{$out_trade_no}' for update");

    		//根据订单号 查找订单信息
			if($recharge['pay_status'] != 0){
				die('已处理');
				$this->model('reward')->roll_back();										
			}
			@unlink($recharge['qrcode']);

			//查找用户附属信息
			$user = $this->model('reward')->query_row("SELECT uid FROM ".$this->model('reward')->get_table('user_account')." WHERE uid={$recharge['uid']}");
			if(!$user){
				die('找不到用户附属信息');	
				$this->model('reward')->roll_back();									
			}

			$question_info = $this->model('question')->get_question_info_by_id($recharge['question_id']);
			if(!$question_info){
				die('无该问题信息');
				$this->model('recharge')->roll_back();							
			}
		$mantotal=$this->model('reward')->fetch_one('sysaccount','total','','id desc');
		// $mantotal=$$mantotal?$mantotal:0;
			// $this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance+{$recharge['money']} where uid ={$recharge['uid']}");//个人
			$this->model('reward')->insert('sysaccount',array(
					'money'=>$recharge['money'],
					'total'=>$recharge['money']+$mantotal,
					'uid'=>$recharge['uid'],
					'addtime'=>time(),
					'remark'=>'悬赏入账',
					'type'=>1,							
			));//个人
			$order['pay_time']=time();
			$order['pay_status']=1;
			$order['qrcode']='';
			$order['trade_no']=$_POST['trade_no'];//支付宝订单号
                // error_log("欧阳调试>>>" . print_r($order, true), 3,  './QQQ2.php'); 
			if(!$this->model('reward')->update('user_finance', $order,'id= "'.$recharge['id'].'"')){

				die('更新数据失败');
				$this->model('reward')->roll_back();									
			}
			$question_arr = array(
				'reward_money' => $recharge['money'],
				'reward_time' => $recharge['reward_day'],
				'is_reward' => 1,
				'is_del' => 0,
			);
			// $this->model('reward')->update("user_finance",array('qrcode'=>''),'id='.$recharge['id']);//个人

			if(!$this->model('reward')->update('question', $question_arr, 'question_id = '.$recharge['question_id'])){
				die('更新问题数据失败');
				$this->model('recharge')->roll_back();											
			}
			if(!$this->model('reward')->update('posts_index',array('is_del'=>0), 'post_type =question and post_id= '.$recharge['question_id'])){
				die('更新问题数据失败');
				$this->roll_back();
			}	

			$this->model('reward')->commit();
			echo "success";die;	
	    }else{
			$arr = array(
					'pay_status' => 2,
					'pay_time' => time(),
					'remarks' => '失败'
				);
			$this->model('reward')->update('user_finance', $arr, 'out_trade_no = "' .$out_trade_no.'"');
		}
	}
			if($verify = $pay->driver('wechat')->gateway('scan')->verify(file_get_contents('php://input'))){
				$order['pay_time']=strtotime($verify['time_end']);
				$order['pay_status']=1;
				$order['qrcode']='';
				$order['trade_no']=$verify['transaction_id'];
				$out_trade_no = $verify['out_trade_no'];//本系统订单号
				if ($verify['return_code'] == 'SUCCESS') {
					$order['pay_time']=strtotime($verify['time_end']);
					$order['pay_status']=1;
					$order['qrcode']='';
					$order['trade_no']=$verify['transaction_id'];
					$out_trade_no = $verify['out_trade_no'];//本系统订单号
					$this->model('reward')->begin_transaction();
			$recharge = $this->model('reward')->query_row("SELECT * FROM ".$this->model('reward')->get_table('user_finance')." WHERE out_trade_no='{$out_trade_no}' for update");

    		//根据订单号 查找订单信息
			if($recharge['pay_status'] != 0){
				die('已处理');
				$this->model('reward')->roll_back();										
			}
			@unlink($recharge['qrcode']);

			//查找用户附属信息
			$user = $this->model('reward')->query_row("SELECT uid FROM ".$this->model('reward')->get_table('user_account')." WHERE uid={$recharge['uid']}");
			if(!$user){
				die('找不到用户附属信息');	
				$this->model('reward')->roll_back();									
			}

			$question_info = $this->model('question')->get_question_info_by_id($recharge['question_id']);
			if(!$question_info){
				die('无该问题信息');
				$this->model('recharge')->roll_back();							
			}
			$mantotal=$this->model('reward')->fetch_one('sysaccount','total','','id desc');
			// $this->model('reward')->query_all("UPDATE ".$this->model('reward')->get_table('user_account')." SET balance=balance+{$recharge['money']} where uid ={$recharge['uid']}");//个人
			$this->model('reward')->insert('sysaccount',array(
					'money'=>$recharge['money'],
					'total'=>$recharge['money']+$mantotal,
					'uid'=>$recharge['uid'],
					'addtime'=>time(),
					'remark'=>'悬赏入账',
					'type'=>1,							
			));//个人

                error_log("欧阳调试>>>" . print_r($order, true), 3,  './QQQ2.php'); 
			if(!$this->model('reward')->update('user_finance', $order,'id= "'.$recharge['id'].'"')){

				die('更新数据失败');
				$this->model('reward')->roll_back();									
			}
			$question_arr = array(
				'reward_money' => $recharge['money'],
				'reward_time' => $recharge['reward_day'],
				'is_reward' => 1,
				'is_del' => 0,
			);
			// $this->model('reward')->update("user_finance",array('qrcode'=>''),'id='.$recharge['id']);//个人

			if(!$this->model('reward')->update('question', $question_arr, 'question_id = '.$recharge['question_id'])){
				die('更新问题数据失败');
				$this->model('recharge')->roll_back();											
			}
			if(!$this->model('reward')->update('posts_index',array('is_del'=>0), 'post_type =question and post_id= '.$recharge['question_id'])){
				die('更新问题数据失败');
				$this->roll_back();
			}	

			$this->model('reward')->commit();
			echo "success";die;	
			    }else{
					$arr = array(
							'pay_status' => 2,
							'pay_time' => time(),
							'remarks' => '失败'
						);
					$this->model('reward')->update('user_finance', $arr, 'out_trade_no = "' .$out_trade_no.'"');
				}
			}
  }
}