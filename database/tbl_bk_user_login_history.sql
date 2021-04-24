DROP TABLE IF EXISTS `tbl_bk_user_login_history`;

CREATE TABLE IF NOT EXISTS `tbl_bk_user_login_history`
(
    `id`              VARCHAR(64)  NOT NULL,
    `user_input`      VARCHAR(200) NOT NULL COMMENT '用户输入的用户名相关',
    `from_ip`         VARCHAR(45)  NOT NULL,
    `create_time`     BIGINT       NOT NULL,
    `is_success`      TINYINT      NOT NULL COMMENT '登录是否成功',
    `device_type`     VARCHAR(10)  NULL COMMENT '登录设备类型',
    `device_id`       VARCHAR(200) NULL COMMENT '登录设备编号',
    `ip_fail_num`     INT          NOT NULL COMMENT '同一个ip连续失败次数',
    `input_fail_num`  INT          NOT NULL COMMENT '同一个账号连续失败次数',
    `device_fail_num` INT          NOT NULL COMMENT '同一台设备连续失败次数',
    `user_id`         VARCHAR(64)  NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-用户登录历史';

CREATE INDEX `input_index` ON `tbl_bk_user_login_history` (`user_input` ASC);

CREATE INDEX `ip_index` ON `tbl_bk_user_login_history` (`from_ip` ASC);

CREATE INDEX `fk_tbl_bk_user_login_history_tbl_bk_user1_idx` ON `tbl_bk_user_login_history` (`user_id` ASC);
