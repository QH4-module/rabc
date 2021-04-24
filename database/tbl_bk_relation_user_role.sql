DROP TABLE IF EXISTS `tbl_bk_relation_user_role`;

CREATE TABLE IF NOT EXISTS `tbl_bk_relation_user_role`
(
    `id`       INT         NOT NULL AUTO_INCREMENT,
    `user_id`  VARCHAR(64) NOT NULL,
    `role_id`  VARCHAR(64) NOT NULL,
    `del_time` BIGINT      NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-用户角色关联表';

CREATE INDEX `fk_user_id_index` ON `tbl_bk_relation_user_role` (`user_id` ASC);

CREATE INDEX `fk_role_id_index` ON `tbl_bk_relation_user_role` (`role_id` ASC);

INSERT INTO `tbl_bk_relation_user_role` (`id`, `user_id`, `role_id`, `del_time`)
VALUES (1, '1', '1', 0);
