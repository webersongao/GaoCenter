ALTER TABLE `[#DB_PREFIX#]question` ADD `is_recommend` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `[#DB_PREFIX#]question` ADD INDEX ( `is_recommend` );
