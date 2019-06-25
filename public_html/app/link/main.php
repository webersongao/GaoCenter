<?php
if (!defined('IN_ANWSION'))
{
	die;
}

class main extends AWS_CONTROLLER //继承一个基类
{
	
//这个是系统默认的权限判断，目前只需关系$rule_action这个数组元素
public function get_access_rule()
{
  $rule_action['rule_type'] = 'white';
  if ($this->user_info['permission']['visit_site'])
  {
      $rule_action['actions'][] = 'apply'; //表示在非登录情况下可以访问，这个apply行为被允许操作
  }
  return $rule_action;
}


//默认行为，当没有指定具体行为时，将默认执行这个
function index_action() 
{             

}

public function apply_action() 
{
  //根据MVC构架，控制器指负责显示某个视图(V)，和使用哪个模型（M）
  //此处只需输出view\default\link\ajax_apply.tpl.htm这个页面给用户，即用户点击申请后所看到的页面
  TPL::output('link/ajax_apply');
} 


}




?>