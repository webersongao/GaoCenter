<?php
/**
 * WeCenter Framework
 *
 * An open source application development framework for PHP 5.2.2 or newer
 *
 * @package     WeCenter Framework
 * @author      WeCenter Dev Team
 * @copyright   Copyright (c) 2011 - 2014, WeCenter, Inc.
 * @license     http://www.wecenter.com/license/
 * @link        http://www.wecenter.com/
 * @since       Version 1.0
 * @filesource
 */

/**
 * WeCenter APP 封禁IP
 *
 * @package     WeCenter
 * @subpackage  App
 * @category    Model
 * @author      Laushow
 */


if (!defined('IN_ANWSION')) {
    die;
}

class banip_class extends AWS_MODEL
{
    /**
     * @description [封禁IP]
     * @param $ip
     * @author Laushow
     * @datatime 2018/10/9 16:09
     */
    function ban_ip($uid,$ip)
    {
        if(!empty($uid) && !empty($ip)){
            return $this->insert('ban_ip',['ip'=>$ip,'uid'=>$uid,'time'=>time()]);
        } else {
            return false;
        }
    }

    /**
     * @description [检测IP是否已经封禁]
     * @param $ip
     * @author Laushow
     * @datatime 2018/10/9 16:16
     */
    public function check_ip($ip){
        $row = $this->fetch_row('ban_ip',"ip='".$ip."'");
        if($row){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @description [解封IP]
     * @param $uid
     * @param $ip
     * @author Laushow
     * @datatime 2018/10/9 16:36
     */
    public function unban_ip($uid, $ip)
    {
        if (!empty($uid) && !empty($ip)) {
            return $this->delete('ban_ip', "ip='$ip' and uid=$uid");
        } else {
            return false;
        }
    }

}
