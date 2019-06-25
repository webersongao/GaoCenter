INSERT IGNORE INTO `ask_system_setting` (`varname`, `value`) VALUES ('draft_enabled', 's:1:"N";');
INSERT IGNORE INTO `ask_system_setting` (`varname`, `value`) VALUES ('del_reason', 's:18:"发布不良内容";');
-- alter table `ask_question_comments` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_article` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_article_comments` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_question` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_answer` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_users` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_users_attrib` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_posts_index` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_topic_relation` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_answer_comments` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除';
-- alter table `ask_notification` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除';
-- alter table `ask_user_action_history` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除';
-- alter table `ask_user_action_history_data` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除';
-- alter table `ask_user_action_history_fresh` add  `is_del` tinyint(1) unsigned default 0 comment '是否删除';
-- alter table `ask_user_follow` add column `is_del` tinyint(1) unsigned default 0 comment '是否删除0正常1删除';
-- alter table `ask_users` add column `reason` varchar(255) DEFAULT '' COMMENT '删除/封禁原因';
-- alter table `ask_users` add column `valid_mobile` varchar(255) DEFAULT '' COMMENT '手机认证';
CREATE TABLE IF NOT EXISTS `ask_question_complain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '投诉者id',
  `question_id` int(11) NOT NULL COMMENT '问题id',
  `answer_id` int(11) NOT NULL COMMENT '最佳回复id',
  `stat` tinyint(1) NOT NULL COMMENT '0投诉申请1撤销该条最佳回复2拒绝此投诉',
  `contents` varchar(255) NOT NULL COMMENT '投诉理由',
  `remarks` varchar(255) NOT NULL COMMENT '备注',
  `addtime` int(11) NOT NULL,
  `updatetime` int(11) NOT NULL,
  `hide` tinyint(1) NOT NULL COMMENT '1删除',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `question_id` (`question_id`) USING BTREE,
  KEY `answer_id` (`answer_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='问题投诉表';

INSERT IGNORE INTO `ask_system_setting` ( `varname`, `value`) VALUES ( 'money_config', 'a:2:{s:6:"status";s:1:"1";s:5:"money";s:26:"1,2,5,10,20,50,100,200,500";}');

INSERT IGNORE INTO `ask_system_setting` ( `varname`, `value`) VALUES ( 'pay_config', 'a:3:{s:6:"alipay";a:5:{s:6:"status";s:1:"1";s:5:"debug";s:1:"1";s:6:"app_id";s:0:"";s:10:"public_key";s:0:"";s:11:"private_key";s:0:"";}s:6:"wechat";a:5:{s:6:"status";s:1:"1";s:5:"debug";s:1:"1";s:6:"app_id";s:0:"";s:6:"mch_id";s:0:"";s:7:"mch_key";s:0:"";}s:5:"yxpay";a:1:{s:6:"status";s:1:"1";}}');

INSERT IGNORE INTO `ask_system_setting` ( `varname`, `value`) VALUES ( 'sms_config', 'a:2:{s:2:"dy";a:5:{s:6:"status";s:1:"N";s:11:"accessKeyId";s:0:"";s:15:"accessKeySecret";s:0:"";s:8:"SignName";s:0:"";s:12:"TemplateCode";s:0:"";}s:2:"sy";a:3:{s:6:"status";s:1:"Y";s:7:"account";s:0:"";s:4:"pswd";s:0:"";}}');

CREATE TABLE IF NOT EXISTS `ask_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `cname` varchar(50) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `pid` int(11) DEFAULT '0',
  `unid` varchar(50) DEFAULT NULL,
  `systerm` smallint(1) DEFAULT 0,
  `status` smallint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('1', '概述', 'home', 'admin/', '0', NULL, '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('2', '全局设置', 'setting', NULL, '0', NULL, '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('3', '站点信息', NULL, 'admin/settings/category-site', '2', 'SETTINGS_SITE', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('4', '注册访问', NULL, 'admin/settings/category-register', '2', 'SETTINGS_REGISTER', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('5', '站点设置', NULL, 'admin/settings/category-functions', '2', 'SETTINGS_FUNCTIONS', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('6', '功能设置', NULL, 'admin/settings/category-contents', '2', 'SETTINGS_CONTENTS', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('7', '威望积分', NULL, 'admin/settings/category-integral', '2', 'SETTINGS_INTEGRAL', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('8', '用户权限', NULL, 'admin/settings/category-permissions', '2', 'SETTINGS_PERMISSIONS', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('9', '邮件设置', NULL, 'admin/settings/category-mail', '2', 'SETTINGS_MAIL', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('10', '开放平台', NULL, 'admin/settings/category-openid', '2', 'SETTINGS_OPENID', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('11', '性能优化', NULL, 'admin/settings/category-cache', '2', 'SETTINGS_CACHE', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('12', '界面设置', NULL, 'admin/settings/category-interface', '2', 'SETTINGS_INTERFACE', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('13', '内容管理', 'reply', NULL, '0', NULL, '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('14', '问题管理', NULL, 'admin/question/question_list/', '13', '301', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('15', '文章管理', NULL, 'admin/article/list/', '13', '309', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('16', '话题管理', NULL, 'admin/topic/list/', '13', '303', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('17', '回复管理', NULL, 'admin/comment/answer_list/', '13', '901', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('18', '投诉管理', NULL, 'admin/comment/complain_list/', '13', '899', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('19', '问题评论管理', NULL, 'admin/comment/question_comment_list/', '13', '902', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('20', '回复评论管理', NULL, 'admin/comment/answer_comment_list/', '13', '903', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('21', '文章评论管理', NULL, 'admin/article/comment/', '13', '904', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('22', '用户管理', 'user', NULL, '0', NULL, '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('23', '用户列表', NULL, 'admin/user/list/', '22', '402', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('24', '用户组', NULL, 'admin/user/group_list/', '22', '403', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('25', '批量邀请', NULL, 'admin/user/invites/', '22', '406', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('26', '职位设置', NULL, 'admin/user/job_list/', '22', '407', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('27', '审核管理', 'report', NULL, '0', NULL, '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('28', '内容审核', NULL, 'admin/approval/list/', '27', '300', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('29', '认证审核', NULL, 'admin/user/verify_approval_list/', '27', '401', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('30', '注册审核', NULL, 'admin/user/register_approval_list/', '27', '408', '1', '0');

INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('31', '用户举报', NULL, 'admin/question/report_list/', '27', '306', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('32', '活动管理', 'reply', NULL, '0', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('33', '活动管理', NULL, 'admin/project/project_list/', '32', '310', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('34', '活动审核', NULL, 'admin/project/approval_list/', '32', '311', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('35', '订单管理', NULL, 'admin/project/order_list/', '32', '312', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('36', '内容设置', 'signup', NULL, '0', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('37', '导航设置', NULL, 'admin/nav_menu/', '36', '307', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('38', '分类管理', NULL, 'admin/category/list/', '36', '302', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('39', '专题管理', NULL, 'admin/feature/list/', '36', '304', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('40', '页面管理', NULL, 'admin/page/', '36', '308', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('41', '专栏管理', NULL, 'admin/column/list/', '36', '299', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('42', '帮助中心', NULL, 'admin/help/list/', '36', '305', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('43', '微信微博', 'share', NULL, '0', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('44', '微信多账号管理', NULL, 'admin/weixin/accounts/', '43', '802', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('45', '微信菜单管理', NULL, 'admin/weixin/mp_menu/', '43', '803', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('46', '微信自定义回复', NULL, 'admin/weixin/reply/', '43', '801', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('47', '微信第三方接入', NULL, 'admin/weixin/third_party_access/', '43', '808', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('48', '微信二维码管理', NULL, 'admin/weixin/qr_code/', '43', '805', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('49', '微信消息群发', NULL, 'admin/weixin/sent_msgs_list/', '43', '804', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('50', '微博消息接收', NULL, 'admin/weibo/msg/', '43', '806', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('51', '邮件导入', NULL, 'admin/edm/receiving_list/', '43', '807', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('52', '邮件群发', 'inbox', NULL, '0', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('53', '任务管理', NULL, 'admin/edm/tasks/', '52', '702', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('54', '用户群管理', NULL, 'admin/edm/groups/', '52', '701', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('55', '工具', 'job', NULL, '0', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('56', '系统维护', NULL, 'admin/tools/', '55', '501', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('57', '插件扩展', 'plugin', NULL, '0', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('58', '插件管理', NULL, 'admin/plugin/', '57', '', '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('59', '短信功能', NULL, 'admin/tools/sms/', '57', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('60', '支付功能', NULL, 'admin/tools/pay/', '57', NULL, '1', '0');
INSERT IGNORE INTO `ask_menu` (`id`, `title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('61', '后台菜单', '', 'admin/menus/', '36', NULL, '1', '0');

CREATE TABLE IF NOT EXISTS `ask_user_account` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `frozen_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结余额',
  `deal_pwd` varchar(255) NOT NULL COMMENT '交易密码',
  `deal_salt` varchar(255) NOT NULL COMMENT '盐值',
  `totalRecharge` decimal(11,2) NOT NULL COMMENT '充值的总额'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户账户表';

CREATE TABLE IF NOT EXISTS `ask_ban_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
  `ip` varchar(16) NOT NULL COMMENT 'IP',
  `time` int(10) unsigned  COMMENT '封禁时间',
  PRIMARY KEY (`id`),
  UNIQUE (`ip`),
  index (uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='封禁列表';

INSERT IGNORE INTO `ask_system_setting` (`varname`, `value`) VALUES ('payment', 's:1:"N";');
  CREATE TABLE IF NOT EXISTS `ask_sysaccount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` smallint(1) DEFAULT NULL COMMENT '1入账2出账',
  `money` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `total` decimal(10,2) DEFAULT NULL COMMENT '总余额',
  `uid` int(11) DEFAULT NULL COMMENT '打款人id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ask_user_refund` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT '用户id',
  `both_id` int(10) NOT NULL COMMENT '提现或者问题id',
  `type` tinyint(1) NOT NULL COMMENT '退款类型：1提现2悬赏',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `remarks` varchar(255) NOT NULL COMMENT '备注',
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退款记录';

CREATE TABLE IF NOT EXISTS `ask_user_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `bank` varchar(255) NOT NULL COMMENT '银行名称',
  `open_bank` varchar(255) NOT NULL COMMENT '开户行',
  `username` varchar(255) NOT NULL COMMENT '持卡人姓名',
  `mobile` char(11) NOT NULL COMMENT '银行预留手机号',
  `address` varchar(255) NOT NULL COMMENT '省市区',
  `identity` varchar(255) NOT NULL COMMENT '身份证号',
  `card` varchar(255) NOT NULL COMMENT '卡号',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `status` tinyint(2) NOT NULL COMMENT '状态0申请1通过2拒绝3已转账',
  `remarks` varchar(255) NOT NULL COMMENT '备注',
  `addtime` int(10) NOT NULL,
  `updatetime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户提现表';

CREATE TABLE IF NOT EXISTS `ask_order_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `no` varchar(20) DEFAULT NULL COMMENT '订单号(年月日+8位流水)',
  `trade_no` varchar(255) DEFAULT NULL COMMENT '三方订单号',
  `consume_type` tinyint(1) DEFAULT NULL COMMENT '交易类型(1：充值，2：提现，3：咨询-支付，4：打赏-支付，5：咨询-收入，6：打赏-收入；7：开通会员；8：购买应用；9：悬赏-支出；10悬赏-收入；11退款；0：冻结)',
  `mode` tinyint(1) DEFAULT NULL COMMENT '付款方式(1：余额，2：支付宝，3：微信，4：银联转账)',
  `relation_type` tinyint(1) DEFAULT NULL COMMENT '关联类型(1：咨询，2：打赏，3：开通会员；4：购买应用；5：悬赏;6：提现',
  `relation_id` int(11) DEFAULT NULL COMMENT '关联id',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '交易金额',
  `time` int(10) DEFAULT NULL COMMENT '交易时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '交易状态（0：未完成，1：已完成，2：失败）',
  `money` decimal(11,2) DEFAULT '0.00' COMMENT '交易前金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `type` smallint(6) DEFAULT NULL COMMENT '1增加2减少',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='交易流水';

INSERT IGNORE INTO `ask_system_setting` (`varname`, `value`) VALUES ('payment', 's:1:"N";');

INSERT IGNORE  INTO `ask_menu` (`title`, `cname`,`url`,`pid`) VALUES ('交易管理', 'good','',0);

INSERT IGNORE  INTO `ask_menu`  (`title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) select '交易流水', '', 'admin/transaction/trading/', max(id), NULL, '1', '0' from `ask_menu`;

INSERT IGNORE  INTO `ask_menu`  (`title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) select '提现申请', '', 'admin/transaction/apply/', max(id)-1, NULL, '1', '0' from `ask_menu`;

