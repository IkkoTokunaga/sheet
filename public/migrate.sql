CREATE TABLE users (
	id int auto_increment NOT NULL COMMENT 'id',
	user_name varchar(100) NOT NULL COMMENT '名前',
	email VARCHAR(255) NOT NULL COMMENT 'email',
	password varchar(100) NOT NULL,
	CONSTRAINT users_PK PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

DROP TABLE `sheets`;

DROP TABLE `sheet_details`;

CREATE TABLE `sheets` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`line_no` int(11) DEFAULT NULL COMMENT 'ライン番号',
	`user_id` int(11) NOT NULL COMMENT 'ユーザーid',
	`sheet_name` varchar(255) NOT NULL COMMENT 'シート名',
	`detail_count` int(11) DEFAULT '0' COMMENT '明細の数',
	`created_at` datetime NOT NULL COMMENT '作成日',
	`updated_at` datetime NOT NULL COMMENT '更新日',
	`deleted_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE `sheet_details` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`line_no` int(11) NOT NULL COMMENT 'ライン番号',
	`user_id` int(11) NOT NULL COMMENT 'ユーザーid',
	`sheet_id` int(11) NOT NULL COMMENT 'シートのid',
	`sheet_status` varchar(255) NOT NULL COMMENT 'ステータス',
	`register_name` varchar(255) NOT NULL COMMENT '登録者',
	`amender_name` varchar(255) DEFAULT NULL COMMENT '修正者',
	`inspector_name` varchar(255) DEFAULT NULL COMMENT '確認者',
	`title` varchar(255) NOT NULL COMMENT 'タイトル',
	`ask` text COMMENT '依頼内容',
	`report` text COMMENT '対応内容',
	`created_at` datetime NOT NULL COMMENT '作成日',
	`updated_at` datetime NOT NULL COMMENT '更新日',
	`deleted_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE `reports` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`sheet_id` int(11) NOT NULL COMMENT 'シートのid',
	`user_id` int(11) NOT NULL COMMENT 'ユーザーid',
	`created_at` datetime NOT NULL COMMENT '作成日',
	`updated_at` datetime NOT NULL COMMENT '更新日',
	`deleted_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
	`client_name` varchar(255) COMMENT 'クライアント名',
	`system_name` varchar(255) COMMENT 'システム名',
	`create_name` varchar(255) COMMENT '作成者',
	`create_date` varchar(255) COMMENT '作成日',
	`modify_date` varchar(255) COMMENT '対応日',
	`finish_date` varchar(255) COMMENT '完了日',
	`signer1` varchar(255),
	`signer2` varchar(255),
	`signer3` varchar(255),
	`signer4` varchar(255),
	`signer5` varchar(255),
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE `report_details` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`line_no` int(11) DEFAULT NULL COMMENT 'ライン番号',
	`sheet_id` int(11) NOT NULL COMMENT 'シートのid',
	`sheet_detail_id` int(11) NOT NULL COMMENT 'シート明細のid',
	`user_id` int(11) NOT NULL COMMENT 'ユーザーid',
	`created_at` datetime NOT NULL COMMENT '作成日',
	`updated_at` datetime NOT NULL COMMENT '更新日',
	`deleted_flg` int(11) DEFAULT '0' COMMENT '削除フラグ',
	`issue` text COMMENT '問題',
	`cause` text COMMENT '原因',
	`measures` text COMMENT '対策',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE `mails` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`line_no` int(11) DEFAULT NULL COMMENT 'ライン番号',
	`user_id` int(11) NOT NULL COMMENT 'ユーザーid',
	`title` varchar(255) NOT NULL COMMENT 'タイトル',
	`body` text COMMENT '本文',
	`created_at` datetime NOT NULL COMMENT '作成日',
	`updated_at` datetime NOT NULL COMMENT '更新日',
	`daleted_at` datetime DEFAULT NULL COMMENT '削除日',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;