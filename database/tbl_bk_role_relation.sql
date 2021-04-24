DROP TABLE IF EXISTS `tbl_bk_role_relation`;

CREATE TABLE IF NOT EXISTS `tbl_bk_role_relation`
(
    `id`          INT AUTO_INCREMENT,
    `role_id`     VARCHAR(64)   NOT NULL,
    `parent_id`   VARCHAR(64)   NOT NULL,
    `del_time`    BIGINT        NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-角色关系表';

CREATE INDEX `parent_id_index` ON `tbl_bk_role_relation` (`parent_id` ASC);

CREATE INDEX `role_id_index` ON `tbl_bk_role_relation` (`role_id` ASC);


