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

if (!defined('IN_ANWSION')) {
    die;
}

class ad_class extends AWS_MODEL
{

    /**
     * @description [添加广告]
     * @author Laushow
     * @datatime 2018/9/27 14:44
     */
    public function add_ad($data)
    {
        return $this->insert('ad', $data);
    }

    public function edit_ad($data, $adid = '')
    {
        return $this->update('ad', $data, 'id=' . $adid);
    }
}
