<?php
/**
 * File Name: BkPrivilegeModel.php
 * Automatically generated by QGC tool
 * @date: 2021-04-22 17:44:09
 * @version: 4.0.4
 */

namespace qh4module\rabc\models;


use QTTX;
use qttx\web\Model;

/**
 * Class BkPrivilegeModel
 * @package qh4module\rabc\models
 * @description 数据表tbl_bk_privilege的Validate模型
 */
class BkPrivilegeModel extends Model
{

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'create_by'], 'string', ['max' => 64]],
            [['id_path', 'key_path'], 'string', ['max' => 1000]],
            [['key', 'name', 'desc', 'path'], 'string', ['max' => 200]],
            [['type', 'level', 'create_time', 'sort', 'del_time'], 'integer'],
            [['icon'], 'string', ['max' => 100]]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLangs()
    {
        return [
            'id' => 'ID',
            'parent_id' => '上级',
            'id_path' => '所有父级ID',
            'key' => '唯一标记',
            'key_path' => '所有父级标记',
            'name' => '名称',
            'desc' => '说明',
            'type' => '类型 ',
            'level' => '级别',
            'icon' => '仅对菜单有效,图标',
            'path' => '关联的路由',
            'create_time' => '创建时间',
            'create_by' => '创建人',
            'sort' => '排序'
        ];
    }


    /**
     * 获取所有权限id
     * @return array|null
     */
    public static function getAllId()
    {
        $key = RabcRedis::privilege_all_id();

        if (QTTX::$app->redis) {
            $result = QTTX::$app->redis->get($key);
            if ($result) return json_decode($result, true);
        }

        $result = QTTX::$app->db
            ->select('id')
            ->from(BkPrivilegeActiveRecord::tableName())
            ->where('del_time=0')
            ->column();

        if (QTTX::$app->redis) {
            QTTX::$app->redis->set($key, json_encode($result));
        }

        return $result;
    }


    /**
     * 获取所有权限 key_path
     * @return array|null
     */
    public static function getAllKeyPath()
    {

        $key = RabcRedis::privilege_all_key_path();

        if (QTTX::$app->redis) {
            $result = QTTX::$app->redis->get($key);
            if ($result) return json_decode($result, true);
        }

        $result = QTTX::$app->db
            ->select('key_path')
            ->from(BkPrivilegeActiveRecord::tableName())
            ->where('del_time=0')
            ->column();

        if (QTTX::$app->redis) {
            QTTX::$app->redis->set($key, json_encode($result));
        }

        return $result;
    }

}
