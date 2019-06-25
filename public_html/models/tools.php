<?php
/**
 * WeCenter Framework
 *
 * An open source application development framework for PHP 5.2.2 or newer
 *
 * @package     WeCenter Framework
 * @author      WeCenter Dev Team
 * @copyright   Copyright (c) 2011 - 2014, WeCenter, Inc.
 * @license     http://www.wecenter.com/license/
 * @link        http://www.wecenter.com/
 * @since       Version 1.0
 * @filesource
 */

/**
 * WeCenter APP 函数类
 *
 * @package     WeCenter
 * @subpackage  App
 * @category    Model
 * @author      WeCenter Dev Team
 */


if (!defined('IN_ANWSION'))
{
    die;
}

class tools_class extends AWS_MODEL
{
     public function checkSmsCode($mobile,$chk){
         $sms = AWS_APP::session()->send_info[$mobile];
            if($sms['mobile'] == $mobile){
                if(time() > $sms['expire']){
                        H::ajax_json_output(AWS_APP::RSM(null, -1, "短信验证码已过期"));
                }
                if($sms['code'] != $chk){
                        H::ajax_json_output(AWS_APP::RSM(null, -1, "短信验证码输入有误"));
                }
                if($sms['type']==1)
                  return true;
                else
                 H::ajax_json_output(AWS_APP::RSM(null, 1, 'ok'));
            }else{
                 H::ajax_json_output(AWS_APP::RSM(null, -1, "接收验证码手机号和输入手机号不一致"));
            }
      }
}