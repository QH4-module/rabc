DROP TABLE IF EXISTS `tbl_bk_relation_role_privilege`;

CREATE TABLE IF NOT EXISTS `tbl_bk_relation_role_privilege`
(
    `id`           INT         NOT NULL AUTO_INCREMENT,
    `role_id`      VARCHAR(64) NOT NULL,
    `privilege_id` VARCHAR(64) NOT NULL,
    `del_time`     BIGINT      NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-角色权限关联表';

CREATE INDEX `fk_role_id_index` ON `tbl_bk_relation_role_privilege` (`role_id` ASC);

CREATE INDEX `fk_privilege_id_index` ON `tbl_bk_relation_role_privilege` (`privilege_id` ASC);
