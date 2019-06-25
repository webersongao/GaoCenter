<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/27
 * Time: 10:40
 */

if (!defined('IN_ANWSION')) {
    die;
}

class Advertising extends AWS_ADMIN_CONTROLLER
{
    public function setup()
    {
        $this->crumb(AWS_APP::lang()->_t('广告管理'), "admin/help/list/");
        if (!$this->user_info['permission']['is_administortar']) {
            H::redirect_msg(AWS_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }
        TPL::assign('menu_list', $this->fetch_menu_list());
    }


    public function list_action()
    {
        $on = $_GET['on']?$_GET['on']:'';
        $id = $_GET['id']?$_GET['id']:'';
        if($on == 'edit'){
            $info = $this->model('ad')->fetch_row('ad','id='.$id);
        }
        $adlist = $this->model('ad')->fetch_all('ad');
        foreach($adlist as $row){
            if($row['type'] == 1){
                $data['index'][] = $row;
            } else {
                $data['other'][] = $row;
            }
        }
        TPL::assign('info', $info?$info:[]);
        TPL::assign('list', $data?$data:[]);
        TPL::assign('on', $on);
        TPL::assign('ad_id', $id);
        TPL::output('admin/advertising/index');
    }
}