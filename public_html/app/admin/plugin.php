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

if (! defined('IN_ANWSION'))
{
	die();
}

class plugin extends AWS_ADMIN_CONTROLLER
{
	public function setup(){
		TPL::assign('menu_list', $this->fetch_menu_list());
	}
	public function index_action(){
		$addons = $this->model('plugin')->getList();
		TPL::assign('list', $addons);
		TPL::output('admin/plugin/index');
	}


    public function install_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $class=ADDON_PATH . $addon_name.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->install($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}


    }
    public function uninstall_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $class=ADDON_PATH . $addon_name.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->uninstall($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}
    }
    public function enable_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $class=ADDON_PATH . $addon_name.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->enable($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}
    }
    public function disable_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $class=ADDON_PATH . $addon_name.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->disable($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}
    }
    public function config_action(){
        $addon_name=trim($_GET['addon_name']);
        $config=ADDON_PATH . $addon_name.'/config.php';
        $info=ADDON_PATH . $addon_name.'/info.php';
        if(file_exists($config))
        $config=require_once($config);
        $info=require_once($info);
		TPL::assign('configs', $config);
		TPL::assign('info', $info);
		TPL::output('admin/plugin/config');
    }
    public function save_config_action(){
        $addon_name=trim($_POST['addon_name']);
        $config=ADDON_PATH . $addon_name.'/config.php';
        if(file_exists($config))
        $config=require($config);
    	$post=[];
        foreach ($_POST['config'] as $key => $value) {
            $config[$key]['value']=$value;
        }
       file_put_contents(ADDON_PATH . "{$addon_name}/config.php", "<?php\n return " . var_export($config,TRUE).';');
        H::ajax_json_output(AWS_APP::RSM(null, -1, '配置成功'));
    }

    public function save_tab_config_action(){
        $addon_name=trim($_POST['addon_name']);
        $config=ADDON_PATH . $addon_name.'/config.php';
        if(file_exists($config))
        $config=require($config);
        $post=[];
        foreach ($_POST['config'] as $key => $value) {
            foreach ($value as $k => $v) {
            $config['group'][$key]['config'][$k]['value']=$v;
            }
        }
       file_put_contents(ADDON_PATH . "{$addon_name}/config.php", "<?php\n return " . var_export($config,TRUE).';');
        H::ajax_json_output(AWS_APP::RSM(null, -1, '配置成功'));        
    }
}