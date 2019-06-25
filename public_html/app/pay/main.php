<?php
/*
+--------------------------------------------------------------------------
|   Anwsion [#RELEASE_VERSION#]
|   ========================================
|   by Anwsion dev team
|   (c) 2011 - 2012 Anwsion Software
|   http://www.anwsion.com
|   ========================================
|   Support: zhengqiang@gmail.com
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
		$rule_action['rule_type'] = 'black'; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
		$rule_action['actions'] = array();

		return $rule_action;
	}

	public function setup()
	{
		HTTP::no_cache_header();

		$this->crumb('Loading...', '/pay/');
	}
	public function add_account_action(){//给没有账户的用户添加账户
		$this->model('account')->get_users_uid();
	}
	public function check_show_action(){
		$question_id=intval($_POST['question_id']);
		$ret=$this->model('reward')->update('question',array('is_reward'=>2),'question_id='.$question_id);
		if($ret!==false)
			H::ajax_json_output(AWS_APP::RSM('进入公示期', '-1', null));
	}
	public function email_notice_action(){
			$this->model('reward')->email_notice();
	}
	public function set_best_answer_action(){

			$this->model('reward')->set_best_answer();
	}
	public function allot_money_by_question_action(){
		
			$this->model('reward')->allot_money_by_question();
	}
	public function auto_set_show_action(){
			$this->model('reward')->auto_set_show();

	}
    public function add_action(){
		if (!(float)($_POST['money'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择金额')));
		}
		$reg='/^[0-9]+(.[0-9]{1,2})?$/';
		preg_match($reg,trim($_POST['money']),$match);
		if(!$match[0]){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入正确的金额,最多保留两位小数')));
		}
		if (!(float)($_POST['money'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写金额')));
		}
        
		$uf = $this->model('account')->fetch_row('user_account', 'uid = ' . intval($this->user_id));
		if(!$uf){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('未找到用户附属信息')));
		}

		if($_POST['pay_method'] == 3 && $uf['balance'] < $_POST['money']){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('余额不足，选择其他支付方式')));
		}
        $reward['both_id']=trim($_POST['both_id']);
        $reward['main_uid']=$this->user_id;
        $reward['passive_uid']=trim($_POST['passive_uid']);
        $reward['cate']=trim($_POST['cate']);
        $reward['type']=trim($_POST['pay_method']);
        $reward['money']=trim($_POST['money']);
        $reward['remarks']=trim($_POST['remarks']);
        $reward['out_trade_no']=time();
        $reward['addtime']=time();
        $data['driver']=$_POST['pay_method']>1?($_POST['pay_method']==2?'alipay':'wechat'):'yepay';
        $data['gateway']='scan';
        $data['uid']=$this->user_id;
        $data['out_trade_no']=time();
        $data['money']=$_POST['pay_method']==2?$_POST['money']*100:$_POST['money'];
        $data['remarks']=$_POST['remarks'];
        $data['notify_url']= $_POST['notify_url'];
        if($_POST['ssid']==$_SESSION['ssid']){
        	$_SESSION['ssid']='';
	        $oret=$this->model('reward')->insert('user_reward',$reward);
	         $ret=$this->model('pay_unipay')->dopay($data);
	        $rets=[
	            'img_url'=> $ret,
	            'item_id'=>$oret,
	            'check_url'=>'/pay/checkstatus/',
	        ];
	         H::ajax_json_output($rets);

        }else{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请不要重复提交')));
        }

    } 
	public function index_action(){
		// $recharge['id']=20;
		// 	$order['pay_time']=time();
		// 	$order['pay_status']=1;
		// 	$order['qrcode']='';
		// 	$order['trade_no']='2018101022001473450517595155';//支付宝订单号
		// 	if(!$this->model('reward')->update('user_finance', $order,'id= "'.$recharge['id'].'"')){
  //               error_log("欧阳调试>>>" . print_r($order, true), 3,  './QQQ2.php'); 								
		// 	}
	}
	public function notify_action(){
                // error_log("欧阳调试>>>" . print_r($_POST, true), 3,  './QQQ.php'); 
		  $order['pay_time']=time();
          $order['pay_status']=1;
          $order['trade_status']='TRADE_SUCCESS';
          $order['trade_no']='1539072469';//支付宝订单号
          $order['out_trade_no']='1539072469';//支付宝订单号
    	  // $this->model('pay_unipay')->updateHandle('TRADE_SUCCESS','XS20180813162240153414856021156',$order);die;
		$this->model('pay_unipay')->callbalk();
	}
	public function finance_notify_action(){
		$this->model('pay_unipay')->finance_callbalk();
	}
	 function givetip_action(){
		if (!(float)($_POST['money'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择提现金额')));
		}
		$reg='/^[0-9]+(.[0-9]{1,2})?$/';
		preg_match($reg,trim($_POST['money']),$match);
		if(!$match[0]){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入正确的金额,最多保留两位小数')));
		}

	}

		public function checkstatus_action(){
			$oid=intval($_POST['order_id']);
			if(isset($_GET['type']) && $_GET['type']==2){
			$status=$this->model('reward')->get_finance_info($oid);
				if($status['pay_status']==1){
					$arr['msg']='支付成功';
					if(isset($_GET['type']) && $_GET['type']==2)
					$arr['url']=get_js_url('/question/'.$status['question_id']);
					H::ajax_json_output(AWS_APP::RSM(null, 1, $arr));
				}
			}else{
				$status=$this->model('reward')->get_order_info($oid);
				if($status==1){
					$arr['msg']='支付成功';
					H::ajax_json_output(AWS_APP::RSM(null, 1, $arr));
					}
			}
	}

	//悬赏支付
	public function add_finance_action()
	{
		if (!(float)($_POST['money'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择金额')));
		}
		$reg='/^[0-9]+(.[0-9]{1,2})?$/';
		preg_match($reg,trim($_POST['money']),$match);
		if(!$match[0]){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入正确的金额,最多保留两位小数')));
		}
		if (!$this->user_id){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请先登录')));
		}
		if (!(float)($_POST['money'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写金额')));
		}
        
		$uf = $this->model('account')->fetch_row('user_account', 'uid = ' . intval($this->user_id));
		if(!$uf){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('未找到用户附属信息')));
		}

		if($_POST['pay_method'] == 1 && $uf['balance'] < $_POST['money']){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('余额不足，选择其他支付方式')));
		}
		if($_POST['money']<1){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('悬赏金额不能低于一元')));
		}
		$question=$this->model('question')->fetch_row('question','question_id='. $_POST['question_id'] );
		$out_trade_no = 'XS'.date('YmdHis').time().rand(10000,99999);
		$datas = array(
			'uid' => $this->user_id,
			'out_trade_no' => $out_trade_no,
			'question_id' => $_POST['question_id'] ? $_POST['question_id'] : 0,
			'answer_id' => 0,
			'reward_day' => $_POST['reward_time'],
			'type' =>1,
			'pay_way' => $_POST['pay_method'],
			'money' =>$_POST['pay_method']>1?($_POST['pay_method']==2?$_POST['money']*100:$_POST['money']):$_POST['money'],
			'pay_status' =>0,
			'addtime' => time(),
			'pay_time' => 0,
			'remarks' =>'发布悬赏问题-'.$question['question_content']
		);
		$reward['out_trade_no']=$datas['out_trade_no'];
		$reward['remarks']=$datas['remarks'];
		$reward['money']=$datas['money'];
		$reward['uid']=$datas['uid'];
        $reward['driver']=$datas['pay_way']>1?($datas['pay_way']==2?'alipay':'wechat'):'yepay';
        $reward['gateway']='scan';
        $reward['notify_url']=$_POST['notify_url'];
        if($_POST['ssid']==$_SESSION['ssid']) {
            $_SESSION['ssid'] = '';
            $oret = $this->model('reward')->insert('user_finance', $datas);
            $ret = $this->model('pay_unipay')->dopay($reward);

            $rets = [
                'img_url' => $ret,
                'item_id' => $oret,
                'check_url' => '/pay/checkstatus/?type=2',
            ];
            // var_dump($_POST);die;
            H::ajax_json_output($rets);
        }else{
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请不要重复提交')));
            }
		
	}

	public function havepass_action(){
		$have=$this->model('reward')->fetch_row('user_account','uid='.$this->user_id);
		$data['ret']=$have['deal_pwd']?1:0;
		$data['money']=$have['balance']>trim($_POST['money'])?1:-1;
		H::ajax_json_output($data);
	}
	public function checkpass_action(){
		$have=$this->model('reward')->fetch_row('user_account','uid='.$this->user_id);
		$pass=trim($_POST['pass']);
		// var_dump(md5(md5($pass).$have['deal_salt'])==$have['deal_pwd']);die;
		if(md5(md5($pass).$have['deal_salt'])==$have['deal_pwd']){
			$ret=1;
		}else{
			$ret=2;
		}
			H::ajax_json_output($ret);
	}	

		public function orders_action(){
		$uid=$_GET['uid'];
		$page=intval($_GET['page'])+1;
		$offset=($page-1)*10;
		$data=$this->model('reward')->get_order($this->user_id,$offset);	
 		TPL::assign('order_list', $data);
		TPL::output('people/ajax/orders');
	}
}