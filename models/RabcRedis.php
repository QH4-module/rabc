<?php
/**
 * File Name: RedisKey.php
 * ©2021 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021-03-02 15:10
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\rabc\models;


use QTTX;

class RabcRedis
{
    private static function format($str)
    {
        $name = QTTX::getConfig('app_name');
        return $name . '_' . $str;
    }

    /**
     * 存储用户的信息
     * 每个用户一条数据,以用户id生成键
     * 该缓存不会被 [clearRedis()] 方法清除,如果用户数据发生变动,需要手动修改或删除数据
     * @param $user_id
     * @return string
     */
    static function user_info($user_id)
    {
        $key = self::format('rabc_user_info:%s:h');
        return sprintf($key, $user_id);
    }

    /**
     * 所有角色id,值是角色id数组或null的json字符串
     * @return string
     */
    static function role_all_id()
    {
        return self::format('rabc_role_all_id:s');
    }

    /**
     * 所有权限资源的id,值是权限资源id数组或null的json字符串
     */
    static function privilege_all_id()
    {
        return self::format('rabc_privilege_all_id:s');
    }

    /**
     * 所有权限资源的key_path,值是权限资源key_path数组或null的json字符串
     */
    static function privilege_all_key_path()
    {
        return self::format('rabc_privilege_all_key_path:s');
    }

    /**
     * 所有用户id,值是用户id数组或null的json字符串
     */
    static function user_all_id()
    {
        return self::format('rabc_user_all_id:s');
    }

    /**
     * 保存用户直接关联的角色
     * 域是用户id,值是角色id数组或null的json字符串
     */
    static function user_relation_role()
    {
        return self::format('rabc_user_roles:h');
    }

    /**
     * 保存用户所有下级角色,不包括直接关联角色
     * 域是用户id,值是角色id数组或null的json字符串
     */
    static function user_relation_children_role()
    {
        return self::format('rabc_user_children_roles:h');
    }

    /**
     * tbl_bk_role_relation
     * @return string
     */
    static function role_relation_table()
    {
        return self::format('rabc_role_relation_table:s');
    }

    /**
     * 用户的关联权限
     * 键是用户id,值是权限资源id数组或null的json字符串
     */
    static function user_relation_privilege()
    {
        return self::format('rabc_user_relation_privilege:h');
    }

    /**
     * 用户所有的下级用户
     * 键是用户id,值是下级用户id数组或null的json字符串
     */
    static function user_relation_children_user()
    {
        return self::format('rabc_user_relation_children_user:h');
    }

    /**
     * 保存用户关联权限的key_path
     * 键是用户id,值是权限资源key_path数组或者null的json字符串
     * @return string
     */
    static function user_privilege_key_path()
    {
        return self::format('rabc_user_privilege_key_path:h');
    }

    /**
     * 清空rabc相关的缓存
     * 因为用户,角色,权限关联复杂,基本上更改任何一项,都需要执行该操作
     */
    public static function clearRedis()
    {
        if (QTTX::$app->redis) {
            QTTX::$app->redis->del([
                static::role_all_id(),
                static::privilege_all_id(),
                static::user_all_id(),
                static::privilege_all_key_path(),
                static::user_relation_role(),
                static::user_relation_children_role(),
                static::role_relation_table(),
                static::user_relation_privilege(),
                static::user_relation_children_user(),
                static::user_privilege_key_path(),
            ]);
        }
    }
}
