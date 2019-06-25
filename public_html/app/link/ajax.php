<?php
if (!defined('IN_ANWSION'))
{
	die;
}

class ajax extends AWS_CONTROLLER //继承一个基类
{
	
public function get_access_rule()
{
    $rule_action['rule_type'] = 'white';
    if ($this->user_info['permission']['visit_site'])
    {
        $rule_action['actions'][] = 'apply';
    }
    return $rule_action;
}

//当用户点击“提交申请”时的处理
public function apply_action()
{
    $site_name = $_POST['site_name'];
    $site_url = $_POST['site_url'];
    $admin_contact = $_POST['admin_contact'];
    $site_desc = $_POST['site_desc'];
    //此处就是控制器（C）指派模型（M）的过程，表示调用models\link.php中的is_exist_url()
    //用于判断该网站地址是否已经存在
    if ($this->model('link')->is_exist_url($site_url))
    {
        H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('该网站已经提交过，请勿重复提交！')));
    }
    //对提交的参数进行简单的判断
    if (trim($site_name) == '')
    {
        H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入网站名称！')));
    }
    if (trim($site_url) == '')
    {
        H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入网站地址！')));
    }
    if (trim($admin_contact) == '')
    {
        H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入联系方式！')));
    }  
    // 网址 正则检查 
    if (!preg_match('/(http|https):\/\/([\w\d\-_]+[\.\w\d\-_]+)[:\d+]?([\/]?[\w\/\.]+)/i', $site_url)) {
        H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请检查网站地址！')));
    }   

    //表示调用models\link.php中的apply()，将申请数据插入数据库中
    $this->model('link')->apply($site_name,$site_url,$admin_contact,$site_desc) ;
    //H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang()->_t('申请成功，等待管理员审核！')));
	H::ajax_json_output(AWS_APP::RSM(array(
                'url' => get_js_url('/publish/link_wait_approval/')
            ), 1, null));
}   



}




?>