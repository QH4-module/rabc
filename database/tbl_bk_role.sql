DROP TABLE IF EXISTS `tbl_bk_role`;

CREATE TABLE IF NOT EXISTS `tbl_bk_role`
(
    `id`          VARCHAR(64)  NOT NULL COMMENT 'ID',
    `name`        VARCHAR(45)  NOT NULL COMMENT '名称',
    `desc`        VARCHAR(200) NULL COMMENT '说明',
    `create_by`   VARCHAR(64)  NOT NULL COMMENT '创建人',
    `create_time` BIGINT       NOT NULL COMMENT '创建时间',
    `is_fixed`    TINYINT      NOT NULL COMMENT '不可变',
    `del_time`    BIGINT       NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-角色表';

INSERT INTO `tbl_bk_role` (`id`, `name`, `desc`, `create_by`, `create_time`, `is_fixed`, `del_time`)
VALUES ('1', '系统管理员', '系统默认的管理员角色', 1, 0, 1, 0);
