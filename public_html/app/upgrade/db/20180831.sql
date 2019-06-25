CREATE TABLE IF NOT EXISTS `[#DB_PREFIX#]app_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `content` text COMMENT '内容',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=[#DB_ENGINE#] DEFAULT CHARSET=utf8 COMMENT='APP崩溃异常信息' AUTO_INCREMENT=1;

INSERT IGNORE INTO `[#DB_PREFIX#]system_setting` (`varname`, `value`) VALUES ('enable_column', 's:1:"N";');
INSERT IGNORE INTO `[#DB_PREFIX#]system_setting` (`varname`, `value`) VALUES ('weixin_build_account', 's:1:"N";');
UPDATE `[#DB_PREFIX#]system_setting` SET  `varname` = 'sina_akey', `value` = '' WHERE `varname` = 'sina_akey\', NULL';
UPDATE `[#DB_PREFIX#]system_setting` SET  `varname` = 'sina_skey', `value` = '' WHERE `varname` = 'sina_skey\', NULL';
