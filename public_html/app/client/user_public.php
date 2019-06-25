<?php
if (!defined('IN_ANWSION')) {
    die;
}

class user_public extends AWS_CONTROLLER
{
    public function get_access_rule()
    {
        $rule_action['rule_type'] = 'white';
        $rule_action['actions']   = array(
            'register_process',
            'login_process',
        );
        return $rule_action;
    }

    public function setup()
    {
        // HTTP::no_cache_header();
        
        if (!$this->model('client')->verify_signature(get_class(), $_GET['mobile_sign'])) {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('验签失败')));
        }
    }

    public function login_process_action()
    {
        if (get_setting('ucenter_enabled') == 'Y') {
            if (!$user_info = $this->model('ucenter')->login($_POST['user_name'], $_POST['password'])) {
                $user_info = $this->model('account')->check_login($_POST['user_name'], $_POST['password']);
            }
        } else {
            $user_info = $this->model('account')->check_login($_POST['user_name'], $_POST['password']);
        }

        if (!$user_info) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确的帐号或密码')));
        } else {
            if ($user_info['forbidden'] == 1) {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('抱歉, 你的账号已经被禁止登录')));
            }

            if (get_setting('site_close') == 'Y' and $user_info['group_id'] != 1) {
                H::ajax_json_output(AWS_APP::RSM(null, -1, get_setting('close_notice')));
            }

            if (get_setting('register_valid_type') == 'approval' and $user_info['group_id'] == 3) {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站点会员注册需要经过管理员审核,请耐心等待')));
            } else {
                //if ($_POST['net_auto_login'])
                //{
                $expire = 60 * 60 * 24 * 360;
                //}

                $this->model('account')->update_user_last_login($user_info['uid']);
                // $this->model('account')->setcookie_logout();

                $this->model('account')->setcookie_login($user_info['uid'], $_POST['user_name'], $_POST['password'], $user_info['salt'], $expire);

                $valid_email    = 1;
                $is_first_login = 0;
                if (get_setting('register_valid_type') == 'email' and !$user_info['valid_email']) {
                    $valid_email = 0;
                } else if ($user_info['is_first_login'] and !$_POST['_is_mobile']) {
                    $is_first_login = 1;
                }

                $user_info['avatar_file'] = get_avatar_url($user_info['uid'], 'max');

                H::ajax_json_output(AWS_APP::RSM(array(
                    'uid'            => $user_info['uid'],
                    'user_name'      => $user_info['user_name'],
                    'avatar_file'    => $user_info['avatar_file'],
                    'question_count' => $user_info['question_count'],
                    'article_count'  => $user_info['article_count'],
                    'answer_count'    => $user_info['answer_count'],
                    'sex'    => $user_info['sex'],
                    'expireInterval'    =>  strtotime('+15 day'),
                    'mobile'    => $user_info['mobile'],
                    'fans_count'    => $user_info['fans_count'],
                    'friend_count'    => $user_info['friend_count'],
                    'province'    => $user_info['province'],
                    'integral'    => $user_info['integral'],
                    'city'    => $user_info['city'],
                    'group_id'    => $user_info['group_id'],
                    'topic_focus_count'    => $user_info['topic_focus_count'],
                    'reputation'    => $user_info['reputation'],
                    'valid_email'    => $valid_email,
                    'is_first_login' => $is_first_login,
                ), 1, null));
            }
        }
    }

    public function register_process_action()
    {
        if (get_setting('register_type') == 'close') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站目前关闭注册')));
        } else if (get_setting('register_type') == 'invite' and !$_POST['icode']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站只能通过邀请注册')));
        } else if (get_setting('register_type') == 'weixin') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站只能通过微信注册')));
        }

        if ($_POST['icode']) {
            if (!$invitation = $this->model('invitation')->check_code_available($_POST['icode']) and $_POST['email'] == $invitation['invitation_email']) {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邀请码无效或与邀请邮箱不一致')));
            }
        }

        if (trim($_POST['user_name']) == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入用户名')));
        } else if ($this->model('account')->check_username($_POST['user_name'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名已经存在')));
        } else if ($check_rs = $this->model('account')->check_username_char($_POST['user_name'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名包含无效字符')));
        } else if ($this->model('account')->check_username_sensitive_words($_POST['user_name']) or trim($_POST['user_name']) != $_POST['user_name']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名中包含敏感词或系统保留字')));
        }

        if ($this->model('account')->check_email($_POST['email'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('E-Mail 已经被使用, 或格式不正确')));
        }

        if (strlen($_POST['password']) < 6 or strlen($_POST['password']) > 16) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('密码长度不符合规则')));
        }

        // if (! $_POST['agreement_chk'])
        // {
        //     H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('你必需同意用户协议才能继续')));
        // }

        // 检查验证码
        // if (!AWS_APP::captcha()->is_validate($_POST['seccode_verify']) AND get_setting('register_seccode') == 'Y')
        // {
        //     H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请填写正确的验证码')));
        // }

        if (get_setting('ucenter_enabled') == 'Y') {
            $result = $this->model('ucenter')->register($_POST['user_name'], $_POST['password'], $_POST['email']);

            if (is_array($result)) {
                $uid = $result['user_info']['uid'];
            } else {
                H::ajax_json_output(AWS_APP::RSM(null, -1, $result));
            }
        } else {
            $uid = $this->model('account')->user_register($_POST['user_name'], $_POST['password'], $_POST['email']);
        }

        if ($_POST['email'] == $invitation['invitation_email']) {
            $this->model('active')->set_user_email_valid_by_uid($uid);

            $this->model('active')->active_user_by_uid($uid);
        }

        // $this->model('account')->setcookie_logout();
        // $this->model('account')->setsession_logout();

        if ($_POST['icode']) {
            $follow_users = $this->model('invitation')->get_invitation_by_code($_POST['icode']);
        } else if (HTTP::get_cookie('fromuid')) {
            $follow_users = $this->model('account')->get_user_info_by_uid(HTTP::get_cookie('fromuid'));
        }

        if ($follow_users['uid']) {
            $this->model('follow')->user_follow_add($uid, $follow_users['uid']);
            $this->model('follow')->user_follow_add($follow_users['uid'], $uid);

            $this->model('integral')->process($follow_users['uid'], 'INVITE', get_setting('integral_system_config_invite'), '邀请注册: ' . $_POST['user_name'], $follow_users['uid']);
        }

        if ($_POST['icode']) {
            $this->model('invitation')->invitation_code_active($_POST['icode'], time(), fetch_ip(), $uid);
        }

        if (get_setting('register_valid_type') == 'N' or (get_setting('register_valid_type') == 'email' and get_setting('register_type') == 'invite')) {
            $this->model('active')->active_user_by_uid($uid);
        }

        $user_info = $this->model('account')->get_user_info_by_uid($uid);

        if (get_setting('register_valid_type') == 'N' or $user_info['group_id'] != 3 or $_POST['email'] == $invitation['invitation_email']) {
            $valid_email = 1;
        } else {
            AWS_APP::session()->valid_email = $user_info['email'];
            $this->model('active')->new_valid_email($uid);
            $valid_email = 0;
        }

        $this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $_POST['password'], $user_info['salt']);

        H::ajax_json_output(AWS_APP::RSM(array(
            'uid'            => $user_info['uid'],
            'user_name'      => $user_info['user_name'],
            'avatar_file'    => $user_info['avatar_file'],
            'question_count' => $user_info['question_count'],
            'article_count'  => $user_info['article_count'],
            'answer_count'    => $user_info['answer_count'],
            'sex'    => $user_info['sex'],
            'expireInterval'    =>  strtotime('+15 day'),
            'mobile'    => $user_info['mobile'],
            'fans_count'    => $user_info['fans_count'],
            'friend_count'    => $user_info['friend_count'],
            'province'    => $user_info['province'],
            'integral'    => $user_info['integral'],
            'city'    => $user_info['city'],
            'group_id'    => $user_info['group_id'],
            'topic_focus_count'    => $user_info['topic_focus_count'],
            'reputation'    => $user_info['reputation'],
            'valid_email'    => $valid_email,
            'is_first_login' => $is_first_login,
        ), 1, null));
    }

}
