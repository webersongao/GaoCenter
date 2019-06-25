<?php
 return array (
  'config'=>[
  'register_valid_type' => 
  array (
    'title' => '新用户注册验证类型',
    'type' => 'radio',
    'value' => 'mobile',
    'options' => 
    array (
      'mobile' => '手机注册',
      'double_certification' => '手机注册 + 邮箱注册',
    ),
  ),
  'login_type' => 
  array (
    'title' => '登录类型',
    'type' => 'radio',
    'value' => 'double',
    'options' => 
    array (
      'mobile' => '手机登录',
      'email' => '邮箱登录',
      'double' => '手机登录 + 邮箱登录',
    ),
  )
],
  'name' => 'mobile_regist',
  'title' => '手机短信注册',
  'intro' => '手机短信注册',
  'author' => 'wecenter',
  'version' => '1.0.0',
  'state' => 0,

);