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

class menus extends AWS_ADMIN_CONTROLLER{
	public function setup(){
        TPL::assign('menu_list',  $this->fetch_menu_list());
	}

	public function index_action(){
		$page = $_GET['page']?$_GET['page']:1;
		$where = '';
		if(defined('SYSTEM_LANG')){
			$hideMenu=[57,58,59,60];
			$where = 'id not in ('.implode(',',$hideMenu).')';
		}
		if ($lists = $this->model('article')->fetch_page('menu', $where, 'id DESC', $page, 15)){
			$total_rows = $this->model('article')->found_rows();
		}
		foreach ($lists as $key => $value) {
			$lists[$key]['ptitle']=$this->model('article')->fetch_one('menu','title','id='.$value['pid']);
		}
		TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
			'base_url' => get_js_url('/admin/menus/'),
			'total_rows' => $total_rows,
			'per_page' => 15
		))->create_links());
        TPL::assign('lists',  $lists);
		TPL::output('admin/menu/index');
	}

	public function edit_action(){
		$id=intval($_GET['id']);
		if($id){
			$info=$this->model('article')->fetch_row('menu','id='.$id);
		}else{
			$info=null;
		}
		$where = 'pid=0';
		if(defined('SYSTEM_LANG')){
			$hideMenu=[57,58,59,60];
			$where = $where.' and id not in ('.implode(',',$hideMenu).')';
		}
		$menu=$this->model('article')->fetch_all('menu',$where);
        TPL::assign('info',  $info);
        TPL::assign('menu',  $menu);

		TPL::output('admin/menu/edit');
	}

	public function lock_menus_action(){
		$ret=$this->model('article')->update('menu',array('status'=>intval($_POST['status'])),'id='.intval($_POST['id']));
		if($ret!==false){
            H::ajax_json_output(AWS_APP::RSM(null, 1,null));
		}
	}
	public function save_menu_action(){
		$_POST['id']=intval($_POST['id']);
		$_POST['title']=trim($_POST['title']);
		$msg=$_POST['id']?'修改成功':'添加成功';
		unset($_POST['_post_type']);
		if(empty($_POST['title']))
            H::ajax_json_output(AWS_APP::RSM(null, -1,AWS_APP::lang()->_t('标题不能为空')));
        if(!$_POST['id'])
        $ret=$this->model('article')->insert('menu',$_POST);
		else
        $ret=$this->model('article')->update('menu',$_POST,'id='.$_POST['id']);
		if($ret!==false)
            H::ajax_json_output(AWS_APP::RSM(null, -1,$msg));
	}

	public  function remove_action(){
		if(count($_POST['ids'])<1)
            H::ajax_json_output(AWS_APP::RSM(null, -1,'请选择要删除的数据'));
        $ids=implode(',',$_POST['ids']);
        $ret=$this->model('article')->delete('menu',"id in ($ids)");
		if($ret!==false)
            H::ajax_json_output(AWS_APP::RSM(null, 1,null));
   	}
     Static public function getsub ($cate, $name = 'children', $pid = 0) {
            $arr = array();
            foreach ($cate as $v) {
                if ($v['pid'] == $pid) {
                    $v[$name] = self::getsub($cate, $name, $v['id']);
                    $arr[] = $v;
                }
            }
            return $arr;
        }  	
}