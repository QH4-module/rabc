<?php
/**
 * File Name: HpRabc.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/21 10:12 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\rabc;


use qh4module\rabc\models\BkPrivilegeActiveRecord;
use qh4module\rabc\models\BkPrivilegeModel;
use qh4module\rabc\models\BkRelationRolePrivilegeActiveRecord;
use qh4module\rabc\models\BkRelationUserRoleActiveRecord;
use qh4module\rabc\models\BkRoleModel;
use qh4module\rabc\models\BkRoleRelationModel;
use qh4module\rabc\models\BkUserModel;
use qh4module\rabc\models\RabcRedis;
use qh4module\token\TokenFilter;
use QTTX;

class HpRabc
{
    /**
     * 获取用户信息,如果缓存命中,则使用缓存信息
     * 如果缓存未命中,则数据库查询后,写入缓存
     * 这个函数,综合了 user表 和 user_info表 的数据
     * @param $user_id
     * @param array $field 查询的字段,如果为空,则返回全部字段
     * @return array
     */
    public static function getUserInfo($user_id, $field = [])
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');
        return BkUserModel::getOneByRedis($user_id, $field);
    }

    /**
     * 从redis中删除用户信息
     * 个人用户的信息,Rabc内部会自动维护
     * 如果程序中手动修改了用户信息,请手动删除或者修改缓存
     * @param $user_id
     */
    public static function delRedisUserInfo($user_id)
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');
        BkUserModel::delRedisUser($user_id);
    }

    /**
     * 获取用户直接关联的所有角色id
     * @param string $user_id
     * @param bool $is_admin
     * @return array|null
     */
    public static function getUserRelationRole($user_id, &$is_admin = false)
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');
        $roles = BkRoleModel::getUserRelationRole($user_id);
        $is_admin = false;
        if ($roles && in_array('1', $roles)) {
            $is_admin = true;
        }
        return $roles;
    }

    /**
     * 获取用户关联角色和所有下级角色id
     * @param string $user_id
     * @param bool $only_children 结果中是否剔除自己所属角色 true不包含;false包含
     * @param bool $is_admin 输出参数,这个值会被赋予bool型,表示该用户是否是管理员
     * @return array
     */
    public static function getUserRelationChildrenRole($user_id = null, $only_children = false, &$is_admin = null)
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');

        $has_role = BkRoleModel::getUserRelationRole($user_id);
        if (empty($has_role)) return [];

        if (in_array('1', $has_role)) {
            // 管理员角色返回所有
            $is_admin = true;
            return BkRoleModel::getAllId();

        } else {
            // 非管理员进行关联查询
            $is_admin = false;

            $children_role = [];
            $key = RabcRedis::user_relation_children_role();
            // 优先取缓存
            if (QTTX::$app->redis) {
                $result = QTTX::$app->redis->hget($key, $user_id);
                if ($result) {
                    $children_role = json_decode($result, true);
                }
            }
            // 缓存没有则查询
            if (empty($children_role)) {
                $children_role = BkRoleRelationModel::getChildren($has_role);
                if (QTTX::$app->redis && $children_role) {
                    QTTX::$app->redis->hset($key, $user_id, json_encode($children_role));
                }
            }
            if ($only_children) {
                return array_unique($children_role);
            } else {
                return array_unique(array_merge($has_role, $children_role));
            }
        }
    }

    /**
     * 获取用户关联的所有下级用户
     * @param string $user_id
     * @return array|mixed
     */
    public static function getUserRelationChildrenUser($user_id = '')
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');

        $key = RabcRedis::user_relation_children_user();
        // 缓存获取
        if (QTTX::$app->redis) {
            $result = QTTX::$app->redis->hget($key, $user_id);
            if ($result) return json_decode($result, true);
        }

        // 获取所有下级角色,不包含本身所属角色
        $is_admin = false;
        $roles = self::getUserRelationChildrenRole($user_id, true, $is_admin);
        if (empty($roles)) return [];

        if ($is_admin) {
            // 管理员返回所有用户的id
            return BkUserModel::getAllId();
        } else {
            // 非管理员,关联查询
            $result = QTTX::$app->db
                ->select('user_id')
                ->from(BkRelationUserRoleActiveRecord::tableName())
                ->whereIn('role_id', $roles)
                ->where('del_time=0')
                ->column();

            if ($result) $result = array_unique($result);

            if (QTTX::$app->redis && $result) {
                QTTX::$app->redis->hset($key, $user_id, json_encode($result));
            }
            return $result;
        }
    }


    /**
     * 获取用户关联的所有权限
     * @param string $user_id
     * @return array|mixed|null
     */
    public static function getUserRelationPrivilege($user_id = '')
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');

        $key = RabcRedis::user_relation_privilege();
        if (QTTX::$app->redis) {
            $result = QTTX::$app->redis->hget($key, $user_id);
            if ($result) return json_decode($result, true);
        }

        $is_admin = false;
        $roles = self::getUserRelationRole($user_id, $is_admin);
        if (empty($roles)) return [];

        if ($is_admin) {
            // 管理员返回所有权限资源
            return BkPrivilegeModel::getAllId();
        } else {
            $result = QTTX::$app->db
                ->select(['privilege_id'])
                ->from(BkRelationRolePrivilegeActiveRecord::tableName())
                ->whereIn('role_id', $roles)
                ->where('del_time=0')
                ->column();

            if ($result) $result = array_unique($result);

            if (QTTX::$app->redis && $result) {
                QTTX::$app->redis->hset($key, $user_id, json_encode($result));
            }

            return $result;
        }
    }


    /**
     * 获取用户所有权限的 key_path
     * @param string $user_id
     * @return array|null
     * @throws \Exception
     */
    public static function getUserPrivilegeKeyPath($user_id = null)
    {
        if (empty($user_id)) $user_id = TokenFilter::getPayload('user_id');

        // 缓存获取
        $key = RabcRedis::user_privilege_key_path();
        if (QTTX::$app->redis) {
            $result = QTTX::$app->redis->hget($key, $user_id);
            if ($result) return json_decode($result, true);
        }

        $is_admin = false;
        $roles = self::getUserRelationRole($user_id, $is_admin);
        if (empty($roles)) return [];

        if ($is_admin) {
            // 管理员返回所有权限资源
            return BkPrivilegeModel::getAllKeyPath();
        } else {
            $ta = BkRelationRolePrivilegeActiveRecord::tableName();
            $tb = BkPrivilegeActiveRecord::tableName();

            $result = QTTX::$app->db
                ->select(['tb.key_path'])
                ->from("{$ta} as ta")
                ->leftJoin("{$tb} as tb", 'tb.id=ta.privilege_id')
                ->whereIn('ta.role_id', $roles)
                ->where('ta.del_time=0 and tb.del_time=0')
                ->column();

            if ($result) $result = array_unique($result);

            if (QTTX::$app->redis && $result) {
                QTTX::$app->redis->hset($key, $user_id, json_encode($result));
            }

            return $result;
        }
    }
}