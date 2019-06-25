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

class find_password extends AWS_CONTROLLER
{

    public function get_access_rule()
    {
        $rule_action['rule_type'] = 'black'; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
        $rule_action['actions'] = array();

        return $rule_action;
    }

    public function setup()
    {
        $this->crumb(AWS_APP::lang()->_t('找回密码'), '/account/find_password/');

        TPL::import_css('css/register.css');
    }

    public function index_action()
    {
        if(get_hook_info('mobile_regist')['state'] == 1){
            switch ($_GET['type'])
            {
                case 'mobile':
                    $type = 'mobile'; //当前验证方式
                    $other_valid = array('type'=>'email','name'=>'邮箱验证');   //  另一种验证方式
                    break;

                case 'email':
                        $type = 'email';
                        $other_valid = array('type'=>'mobile','name'=>'短信验证');
                    break;

                default :
                    $type = 'email';
                    $other_valid = array('type'=>'mobile','name'=>'短信验证');
            }
        }else{
            $type = 'email';
            $other_valid = array();
        }

        $action_link = $type == 'mobile' ? 'account/ajax/first_find_password/' : 'account/ajax/request_find_password/';

        TPL::assign('type', $type);
        TPL::assign('other_valid', $other_valid);
        TPL::assign('action_link', $action_link);

        TPL::output('account/find_password/index');
    }

    public function next_find_password_action()
    {
        if(!$_SERVER['HTTP_REFERER'])
        {
            HTTP::redirect('/account/find_password/');
        }

        if (is_mobile())
        {
            HTTP::redirect('/m/find_password_modify/?type='.$_GET['type'].'&key=' . $_GET['key']);

        }

        $type = $_GET['type'] ?  $_GET['type'] : '';
        $action_link = $type == 'mobile' ? 'account/ajax/find_password_modify_final/' : 'account/ajax/find_password_modify/';

        TPL::assign('action_link', $action_link);
        TPL::output('account/find_password/modify');
    }

    public function process_success_action()
    {
        TPL::assign('email', AWS_APP::session()->find_password);

        TPL::output('account/find_password/process_success');
    }

    public function modify_action()
    {
        if (is_mobile())
        {
            HTTP::redirect('/m/find_password_modify/?type='.$_GET['type'].'&key=' . $_GET['key']);
        }

        if(!$_GET['type']){
            if (!$active_code_row = $this->model('active')->get_active_code($_GET['key'], 'FIND_PASSWORD'))
            {
                H::redirect_msg(AWS_APP::lang()->_t('链接已失效'), '/');
            }

            if ($active_code_row['active_time'] OR $active_code_row['active_ip'])
            {
                H::redirect_msg(AWS_APP::lang()->_t('链接已失效'), '/');
            }
        }



        TPL::output('account/find_password/modify');
    }
}