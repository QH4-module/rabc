DROP TABLE IF EXISTS `tbl_bk_user_info`;

CREATE TABLE IF NOT EXISTS `tbl_bk_user_info`
(
    `user_id`     VARCHAR(64)   NOT NULL,
    `nick_name`   VARCHAR(20)   NULL COMMENT '昵称',
    `avatar`      VARCHAR(500)  NULL COMMENT '头像',
    `gender`      TINYINT       NULL COMMENT '1 男 2女',
    `birthday`    DATE          NULL COMMENT '生日',
    `description` VARCHAR(1000) NULL COMMENT '个人简介',
    `city_id`     INT           NULL COMMENT '所在地区',
    PRIMARY KEY (`user_id`)
)
    ENGINE = InnoDB
    COMMENT = 'rabc-用户信息';

INSERT INTO `tbl_bk_user_info` (`user_id`, `nick_name`, `gender`, `city_id`)
VALUES ('1', '管理员', 0, 100000);

