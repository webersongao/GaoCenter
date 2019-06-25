<?php
/**
*
*/
class AWS_PLUGIN
{
    /**
     *
     * @access private
     * @var array
     */
    private $_listeners = array();
     /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct($_plugin,$method)
    {
        $plugins = AWS_MODEL::model('plugin')->getList();
        if($plugins)
        {
            foreach($plugins as $plugin)
            {//假定每个插件文件夹中包含一个actions.php文件，它是插件的具体实现
                $class_file=ADDON_PATH .$plugin['name'].'/'.$plugin['name'].'.php';
                if (@file_exists($class_file)){
                    require_once($class_file);
                    $class =  $plugin['name'];  
                    $cls=new $class($this,$method);
                    
                      if (class_exists($class)){
                        $methods=get_class_methods($class);
                        foreach ($methods as $key => $value) {
                            if(in_array($value, array('__construct','install','uninstall','enable','disable','setup','is_post','model','crumb','publish_approval_valid')))
                                unset($methods[$key]);
                            else
                                $this->add($_plugin,$value,$cls);
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * @param string $hook
     * @param object $reference
     * @param string $method
     */
   public  function add($plu,$name,$func){
        $GLOBALS['hookList'][$plu][$name]=$func;
    }
    /**
    *
     * @param string $hook 钩子的名称
     * @param mixed $data 钩子的入参
     *    @return mixed
     */
  function trigger($hook,$method,$data=''){ 
     foreach ($GLOBALS['hookList'][$hook] as $k => $v) {
        if($k==$method){
           return $v->$k($data);
        }
    }
  }

  function get_config($plugin){
        $config_file=ADDON_PATH .$plugin.'/config.php';
        if (@file_exists($config_file)){
            $config=require_once($config_file);
            return $config;
     }
  } 
  function get_model($plugin){
        $model_name=$plugin."_model";
        $class_file=ADDON_PATH .$plugin.'/'.$plugin."_model.php";
        if (@file_exists($class_file)){
            require_once($class_file);
            $model=new $model_name;
            return  $model;
     }
  }
  
  function get_info($plugin){
        $info_file=ADDON_PATH .$plugin.'/info.php';
        if (@file_exists($info_file)){
            $config=require_once($info_file);
            return $config;
     }
  }
}