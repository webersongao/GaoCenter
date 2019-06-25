<?php

/**
 * 插件测试
 */

class mobile_regist extends AWS_CONTROLLER
{
    public function __construct()
    {
        $this->info = $this->model('plugin')->get_info('mobile_regist');
        $this->config=json_decode($this->info['config'],true);
    }

    /**
     * 插件安装方法
     * @return bool
     */
    public function install($addon_name)
    {
        return $this->model('plugin')->install($addon_name);        //此处方法可以自行实现
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall($addon_name)
    {
        return $this->model('plugin')->uninstall($addon_name);         //此处方法可以自行实现
    }

    /**
     * 插件启用方法
     */
    public function enable($addon_name)
    {
        return $this->model('plugin')->enable($addon_name);             //此处方法可以自行实现
    }

    /**
     * 插件禁用方法
     */
    public function disable($addon_name)
    {
        return $this->model('plugin')->disable($addon_name);             //此处方法可以自行实现
    }

    /**
     * 插件实现方法
     * @return mixed
     */
    public function register($params)
    {
        if ($params['mobile'] == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入手机号')));
        }
        if ($this->model('account')->check_mobile($params['mobile'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('手机号已经被使用')));
        }
        if ($params['smscode'] == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入短信验证码')));
        }
        if (!is_numeric($params['smscode'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确格式的短信验证码')));
        }
        $this->model('tools')->checkSmsCode($params['mobile'],$params['smscode']);
    }


    /**
     * 插件实现方法登陆
     * @return mixed
     */
    public function login($params)
    {
        if ($params['mobile'] == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入手机号')));
        }
        if (!$this->model('account')->check_mobile($params['mobile'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('手机号不存在')));
        }
        if ($params['smscode'] == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入短信验证码')));
        }
        if (!is_numeric($params['smscode'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确格式的短信验证码')));
        }

        $this->model('tools')->checkSmsCode($params['mobile'],$params['smscode']);
    }

    public function find_password($params)
    {
        if ($params['mobile'] == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入手机号')));
        }
        if (!$this->model('account')->check_mobile($params['mobile'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('手机号不存在')));
        }
        if ($params['smscode'] == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入短信验证码')));
        }
        if (!is_numeric($params['smscode'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确格式的短信验证码')));
        }

        $this->model('tools')->checkSmsCode($params['mobile'],$params['smscode']);
    }

    public function test(){
        echo '14144';
    }

}