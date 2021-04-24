DROP TABLE IF EXISTS `tbl_bk_privilege`;

CREATE TABLE IF NOT EXISTS `tbl_bk_privilege`
(
    `id`          VARCHAR(64)   NOT NULL,
    `parent_id`   VARCHAR(64)   NOT NULL,
    `id_path`     VARCHAR(1000) NOT NULL COMMENT '所有父级ID',
    `key`         VARCHAR(200)  NOT NULL COMMENT '唯一标记',
    `key_path`    VARCHAR(1000) NOT NULL COMMENT '所有父级标记',
    `name`        VARCHAR(200)  NOT NULL COMMENT '名称',
    `desc`        VARCHAR(200)  NULL COMMENT '说明',
    `type`        INT           NOT NULL COMMENT '类型 1 菜单 2页面元素',
    `level`       INT           NOT NULL,
    `icon`        VARCHAR(100)  NULL COMMENT '仅对菜单有效,图标',
    `path`        VARCHAR(200)  NOT NULL COMMENT '关联的路由',
    `create_time` BIGINT        NOT NULL,
    `create_by`   VARCHAR(64)   NOT NULL,
    `sort`        INT           NOT NULL COMMENT '排序,数字越大越靠前',
    `del_time`    BIGINT        NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-后台所有权限';
