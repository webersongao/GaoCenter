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

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;

use Qcloud\Sms\VoiceFileUploader;
use Qcloud\Sms\FileVoiceSender;
use Qcloud\Sms\TtsVoiceSender;

if (!defined('IN_ANWSION'))
{
    die;
}

class main extends AWS_CONTROLLER{
    public function get_access_rule(){
        $rule_action['rule_type'] = "black";
        return $rule_action;
    }

    public function sendSms_action($data){
        $data['mobile']=trim($_POST['mobile']);
        if(!preg_match("/^1[345789]\d{9}$/",$data['mobile'])){
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('手机格式不正确')));
        }
        $setting=get_setting('sms_config');
        if($setting['dy']['status']!='Y' && $setting['sy']['status']!='Y' && $setting['tx']['status']!='Y'){
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信功能尚未开启')));
        }
        if($setting['dy']['status']=='Y'){
        	if(empty($setting['dy']['accessKeyId']) || empty($setting['dy']['accessKeySecret']) || empty($setting['dy']['SignName']) || empty($setting['dy']['TemplateCode']))
        	    H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
        	else
            $this->send_dy_sms($data,$setting['dy']);
        }elseif($setting['sy']['status']=='Y'){
        	if(empty($setting['sy']['account']) || empty($setting['sy']['pswd']) )
        	    H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
        	else
            $this->send_sy_sms($data,$setting['sy']);
        }elseif($setting['tx']['status']=='Y'){
        	if(empty($setting['tx']['accessKeyId']) || empty($setting['tx']['accessKeySecret']) || empty($setting['tx']['SignName']) || empty($setting['tx']['TemplateCode']))
        	    H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));      	
            $code = "";
            for ($i = 0; $i < 6; $i++) {
                $code .= rand(0, 9);
            }
            $params = array($code,2);   //  2 表示验证码有效时间
            $data['TemplateParam']['code'] = $code;
            $this->send_tx_sms($data,$setting['tx'],$params);
        }
    }

    public function send_dy_sms($data,$account){
        $data['TemplateParam']=array('code'=>rand(1000,9999));
        $params = array();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = $account['accessKeyId'];
        $accessKeySecret =  $account['accessKeySecret'];
        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $data['mobile'];
        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $account['SignName'];
        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $account['TemplateCode'];
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $data['TemplateParam'];
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        $content = $this->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );
        if (!isset($content) || $content->Code != 'OK') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, "短信发送失败"));
        }else{
        $session_data = array(
            'mobile' => $data['mobile'],
            'code' =>$data['TemplateParam']['code'],
            'type' => 1,
            'next_send_time' => time() + 300,
            'expire' => time() + 300
        );
        AWS_APP::session()->send_info[$data['mobile']] = $session_data;
            H::ajax_json_output(AWS_APP::RSM(null, 1, "短信发送成功"));
        }
    }

    public function send_sy_sms($data,$account){
            $post_data = array();
            $post_data['account'] = $account['account'];   //示远帐号
            $post_data['pswd'] =$account['pswd'];   //示远密码
            $post_data['msg'] =urlencode('您的验证码是：168168。'); //短信内容，需要用urlencode编码下，注意内容中的逗号请使用中文状态下的逗号
            $post_data['mobile'] = $data['mobile']; //手机号码，多个号码使用","分割
            $post_data['product'] = ''; //产品ID(不用填写)
            $post_data['needstatus']='false'; //是否需要状态报告，需要true，不需要false
            $post_data['extno']='';  //扩展码(不用填写)
            $post_data['resptype']='json'; 
            $url='http://send.18sms.com/msg/HttpBatchSendSM';
            $o='';
            foreach ($post_data as $k=>$v)
            {
               $o.="$k=".urlencode($v).'&';
            }
            $post_data=substr($o,0,-1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 如果需要将结果直接返回到变量里，那加上这句
            $result = curl_exec($ch);
            $result=json_decode($result,true);
            if($result['result']>0){
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信发送失败')));
            }else{
                $session_data = array(
                    'mobile' => $data['mobile'],
                    'code' => 168168,
                    'type' => 0,
                    'next_send_time' => time() + 300,
                    'expire' => time() + 300
                );
                AWS_APP::session()->send_info[$data['mobile']] = $session_data;
                H::ajax_json_output(AWS_APP::RSM(null, '1', AWS_APP::lang()->_t('短信发送成功')));

            }
    }

    public function send_tx_sms($data,$account,$params = array())
    {
        if(!$data['mobile'] || !$account['TemplateCode']){
            return false;
        }

        // 短信应用SDK AppID
        $appid = $account['accessKeyId']; // 1400开头

        // 短信应用SDK AppKey
        $appkey = $account['accessKeySecret'];

        // 签名
        $smsSign = $account['SignName'];

        // 模板id
        $templateCode = $account['TemplateCode'];

        // 指定模板ID单发短信
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $result = $ssender->sendWithParam("86", $data['mobile'], $templateCode,
                $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result,true);
            if($rsp['errmsg'] == 'OK'){
                $session_data = array(
                    'mobile' => $data['mobile'],
                    'code' =>$data['TemplateParam']['code'],
                    'type' => 1,
                    'next_send_time' => time() + 300,
                    'expire' => time() + 300
                );
                AWS_APP::session()->send_info[$data['mobile']] = $session_data;
                H::ajax_json_output(AWS_APP::RSM(null, 1, "短信发送成功"));
            }else{
                H::ajax_json_output(AWS_APP::RSM(null, -1, "短信发送失败"));
            }
            return $rsp;
        } catch(\Exception $e) {
            var_dump($e);die;
        }
    }


 public function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false)
    {
        $apiParams = array_merge(array(
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http') . "://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function encode($str){
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $rtn = curl_exec($ch);
        if ($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
     public function checkSmsCode_action($mobile,$chk){
        $mobile=$mobile?$mobile:trim($_POST['mobile']);
        $chk=$chk?$chk:trim($_POST['code']);
        $re = $this->model('tools')->checkSmsCode($mobile,$chk);
        if($re){
            H::ajax_json_output(AWS_APP::RSM(null, 1, 'ok'));
        }
      }
 
    public function checkstatus_action(){

         // H::ajax_json_output(1);
    }  
    public function havepass_action(){
        $have=$this->model('account')->fetch_row('user_account','uid='.$this->user_id);
        $data['ret']=$have['deal_pwd']?1:0;
        $data['balance']=$have['balance'];
        H::ajax_json_output($data);
    }
    public function checkpass_action(){
        $have=$this->model('account')->fetch_row('user_account','uid='.$this->user_id);
        $pass=trim($_POST['pass']);
        if(md5(md5($pass).$have['deal_salt'])==$have['deal_pwd']){
            $ret=1;
        }else{
            $ret=2;
        }
            H::ajax_json_output($ret);
    }

    public function add_account_action(){
        $ret=$this->model('account')->get_users_uid();
         H::redirect_msg(AWS_APP::lang()->_t('添加用户账户完成'), '/');
        
    }
}