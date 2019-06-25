<?php

if (!defined('IN_ANWSION'))
{
	die;
}

class link_class extends AWS_MODEL
{

var $link_list_total = 0;

//插入数据，第四步调用的就是这个函数了，将数据插入到表中
function apply($site_name,$site_url,$admin_contact,$site_desc)
{       
    $this->insert('links', 
    array(
        'site_name' => $site_name,
        'site_url' => $site_url,
        'admin_contact' => $admin_contact,
        'site_desc' => $site_desc,
        'time' => time()
    ));
}

//检查该URL是否存在
function is_exist_url($site_url)
{       
    //根据site_url字段从表aws_links中获取id，如果存在将会返回一个id，如果不存在则返回空
    //aws_links的sql在文章末尾放出
    return $this->fetch_one('links', 'id', "site_url = '" . $site_url . "'");
}

public function get_link_info($id)
{			
	return $this->fetch_row('links', 'id = ' . intval($id));	
}

public function approval_link($id)
{
	if (!$info = $this->get_link_info($id))
	{
		H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('该友链不存在或已删除！')));
	}
	$this->shutdown_query("UPDATE " . $this->get_table('links') . " SET status = 1 WHERE id = " . $id);
}
// 添加拒绝方法
public function decline_link($id)
{
	if (!$info = $this->get_link_info($id))
	{
		H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('该友链不存在或已删除！')));
	}
	$this->shutdown_query("UPDATE " . $this->get_table('links') . " SET status = 2 WHERE id = " . $id);
}

public function remove_link($id)
{
	if (!$info = $this->get_link_info($id))
	{
		H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('该友链不存在或已删除！')));
	}
	$this->delete('links', "id = " . intval($id));
}

public function get_link_list($page, $per_page, $where)
{
	if ($link_list = $this->fetch_page('links', implode(' AND ', $where), 'id DESC', $page, $per_page))
	{
		$this->link_list_total = $this->found_rows();
		return $link_list;
	}
}

public function get_link_list_all($limit)
{
	$sql = "SELECT * FROM " . $this->get_table('links') . " WHERE status = 1";
	return $this->query_all($sql,$limit);
}

public function update_link($id,$arr)
{
	$this->update('links',$arr, "id = " . $id);
}

}

?>

