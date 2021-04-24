DROP TABLE IF EXISTS `tbl_bk_user`;

CREATE TABLE IF NOT EXISTS `tbl_bk_user`
(
    `id`             VARCHAR(64)  NOT NULL,
    `account`        VARCHAR(100) NULL COMMENT '账号',
    `mobile`         VARCHAR(15)  NULL COMMENT '手机号',
    `email`          VARCHAR(150) NULL COMMENT '邮箱',
    `password`       CHAR(32)     NULL COMMENT '密码',
    `salt`           CHAR(8)      NULL COMMENT '密码混淆随机数',
    `create_by`      VARCHAR(64)  NOT NULL COMMENT '创建人',
    `create_time`    BIGINT       NOT NULL COMMENT '创建时间',
    `wechat_unionid` VARCHAR(150) NULL,
    `wechat_openid`  VARCHAR(150) NULL,
    `qq_id`          VARCHAR(150) NULL,
    `alipay_id`      VARCHAR(150) NULL,
    `weibo_id`       VARCHAR(150) NULL,
    `apple_id`       VARCHAR(150) NULL,
    `state`          TINYINT      NOT NULL DEFAULT 1 COMMENT '1正常2禁止登录3账号异常',
    `del_time`       BIGINT       NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 1000
    COMMENT = 'rabc-用户主表';

CREATE INDEX `account_index` ON `tbl_bk_user` (`account` ASC);

CREATE INDEX `mobile_index` ON `tbl_bk_user` (`mobile` ASC);

CREATE UNIQUE INDEX `account_UNIQUE` ON `tbl_bk_user` (`account` ASC);

CREATE UNIQUE INDEX `mobile_UNIQUE` ON `tbl_bk_user` (`mobile` ASC);

INSERT INTO `tbl_bk_user` (`id`, `account`, `password`, `salt`, `create_by`, `create_time`, `state`, `del_time`)
VALUES ('1', 'admin', '687c95569a71a7c42ffea53325e9e1c5', 'a1d3cc9b', '1', 0, 1, 0);
