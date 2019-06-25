DROP TABLE `[#DB_PREFIX#]edm_unsubscription`;

DELETE FROM `[#DB_PREFIX#]system_setting` WHERE `varname` = 'request_route';
DELETE FROM `[#DB_PREFIX#]system_setting` WHERE `varname` = 'request_route_sys_1';
DELETE FROM `[#DB_PREFIX#]system_setting` WHERE `varname` = 'request_route_sys_2';
DELETE FROM `[#DB_PREFIX#]system_setting` WHERE `varname` = 'weixin_subscribe_message';

ALTER TABLE `[#DB_PREFIX#]weixin_reply_rule` ADD `event_key` VARCHAR( 32 ) NULL DEFAULT '';
ALTER TABLE `[#DB_PREFIX#]weixin_reply_rule` ADD INDEX ( `event_key` );
ALTER TABLE `[#DB_PREFIX#]weixin_reply_rule` ADD `is_subscribe` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `[#DB_PREFIX#]weixin_reply_rule` ADD INDEX ( `is_subscribe` );
ALTER TABLE `[#DB_PREFIX#]users_attrib` CHANGE `qq` `qq` BIGINT( 15 ) NULL DEFAULT NULL;
