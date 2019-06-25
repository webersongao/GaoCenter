<?php

if (!defined('IN_ANWSION')) {
    die;
}
class plugin_class extends AWS_MODEL {
   /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList($addon_dir = '') {
        if (!$addon_dir) $addon_dir = ADDON_PATH;
        $dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
        
        if ($dirs === FALSE || !file_exists($addon_dir)) {
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        
        $addons = array();
        $ndirs = "'" . implode("','", $dirs) . "'";
        $ddirs = array_flip($dirs);
        $ddirs = array_flip($ddirs);

        foreach ($ddirs as $key => $value) {
            $info=ADDON_PATH."{$value}/info.php";
            if(file_exists($info)){
                $addons[]=require_once(ADDON_PATH."{$value}/info.php");
              }
        }
        return $addons;
    }
    public function install($addon_name){
       $config= require(ADDON_PATH."{$addon_name}/info.php");
       if($config['type']==1){
        // var_dump(substr(php_uname(), 0, 7));die;
        if(substr(php_uname(), 0, 7) == "Windows" and is_dir(ADDON_PATH."{$addon_name}/source")){
           recurse_copy(ADDON_PATH."{$addon_name}/source",ROOT_PATH);
        }else{
          $source=ADDON_PATH."{$addon_name}/source/*";
          $target=ROOT_PATH;
          $target=substr($target,0,strlen($target)-1);
          // $command = "cp -r $source  $target"; 
          // exec($command,$array); //执行命令
        }
       }
         $_sql= file_get_contents(ADDON_PATH . "{$addon_name}/install.sql");
         if($_sql){
             $sql= explode(";\r", str_replace(array('[#DB_PREFIX#]', "\n"), array($this->get_prefix(), "\r"), $_sql));
            foreach (array_filter($sql) as $_value) {
              if(!empty(trim($_value)))
                $this->query($_value.';');
            }
         }
       $config['state'] = 1;
       file_put_contents(ADDON_PATH . "{$addon_name}/info.php", "<?php\n return " . var_export($config,TRUE).';');
       return true;
    }
    public function uninstall($addon_name){
         $_sql= file_get_contents(ADDON_PATH . "{$addon_name}/uninstall.sql");
         if($_sql){
             $sql= explode(";\r", str_replace(array('[#DB_PREFIX#]', "\n"), array($this->get_prefix(), "\r"), $_sql));
            foreach ($sql as $_value) {
              if(trim($_value))
                $this->query($_value.';');
            }
         }
       $dbconfig= require(ADDON_PATH."{$addon_name}/info.php");
       $dbconfig['state'] = 2;
       file_put_contents(ADDON_PATH . "{$addon_name}/info.php", "<?php\n return " . var_export($dbconfig,TRUE).';');
       return true;
    }
    public function enable($addon_name){
       $dbconfig= require(ADDON_PATH."{$addon_name}/info.php");
       $dbconfig['state'] = 1;
       file_put_contents(ADDON_PATH . "{$addon_name}/info.php", "<?php\n return " . var_export($dbconfig,TRUE).';');
       return true;
    }
    public function disable($addon_name){
       $dbconfig= require(ADDON_PATH."{$addon_name}/info.php");
       $dbconfig['state'] = 0;
       file_put_contents(ADDON_PATH . "{$addon_name}/info.php", "<?php\n return " . var_export($dbconfig,TRUE).';');
       return true;
    }

}

