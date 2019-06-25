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

// 定义 Cookies 作用域
define('G_COOKIE_DOMAIN','');
define('ADDON_PATH',ROOT_PATH.'plugins/');

// 定义 Cookies 前缀
define('G_COOKIE_PREFIX','ywbz');

// 定义应用加密 KEY (字母或数字)
define('G_SECUKEY','vf13WHsdXx2cyfab');
define('G_COOKIE_HASH_KEY', 'rvBj98bQhkHjd278hv');

define('G_INDEX_SCRIPT', '?/');

define('X_UA_COMPATIBLE', 'IE=edge,Chrome=1');

// GZIP 压缩输出页面
define('G_GZIP_COMPRESS', FALSE);

// Session 存储类型 (db, file)
define('G_SESSION_SAVE', 'db');

// Session 文件存储路径
define('G_SESSION_SAVE_PATH', '');