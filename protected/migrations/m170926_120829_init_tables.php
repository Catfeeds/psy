<?php

class m170926_120829_init_tables extends CDbMigration
{
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$sql=
<<<EOF
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent` int(10) DEFAULT '0' COMMENT '父级ID',
  `name` varchar(16) NOT NULL COMMENT '区域名',
  `pinyin` varchar(25) NOT NULL DEFAULT '' COMMENT '拼音',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `map_lng` decimal(60,6) NOT NULL DEFAULT '0.000000' COMMENT '坐标经度',
  `map_lat` decimal(60,6) NOT NULL DEFAULT '0.000000' COMMENT '坐标纬度',
  `map_zoom` tinyint(3) NOT NULL DEFAULT '12' COMMENT '地图缩放层级',
  `deleted` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧数据id',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='区域表';


DROP TABLE IF EXISTS `recom`;
CREATE TABLE `recom` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `cid` tinyint(2) NOT NULL DEFAULT '0',
  `related_id` int(10) NOT NULL DEFAULT '0',
  `sort` tinyint(2) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` int(10) NOT NULL,
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `hid` int(10) NOT NULL DEFAULT '0',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` int(10) NOT NULL,
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `site`;
CREATE TABLE `site` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  `created` int(10) NOT NULL,
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sms`;
CREATE TABLE `sms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) NOT NULL DEFAULT '',
  `code` varchar(10) NOT NULL DEFAULT '',
  `created` int(10) NOT NULL,
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) NOT NULL COMMENT '标签名称',
  `cate` varchar(20) NOT NULL COMMENT '分类标识',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `max` varchar(100) NOT NULL DEFAULT '',
  `min` varchar(100) NOT NULL DEFAULT '',
  `pinyin` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `parent` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `lesson`;
CREATE TABLE `lesson` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `enroll`;
CREATE TABLE `enroll` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `enroll_price`;
CREATE TABLE `enroll_price` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `consult`;
CREATE TABLE `consult` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `arrange`;
CREATE TABLE `arrange` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `class`;
CREATE TABLE `class` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `visit`;
CREATE TABLE `visit` (
	`id`  int(10) NOT NULL AUTO_INCREMENT ,
	`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态' ,
	`sort`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序' ,
	`deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除' ,
	`created`  int(10) NOT NULL COMMENT '添加时间' ,
	`updated`  int(10) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

EOF;
		$this->execute($sql);
		$this->refreshTableSchema('tk');
	}

	public function safeDown()
	{
		return false;
	}
	
}