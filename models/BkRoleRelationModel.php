<?php
/**
 * File Name: BkRoleRelationModel.php
 * Automatically generated by QGC tool
 * @date: 2021-04-22 14:47:33
 * @version: 4.0.4
 */

namespace qh4module\rabc\models;


use QTTX;
use qttx\web\Model;

/**
 * Class BkRoleRelationModel
 * @package qh4module\rabc\models
 * @description 数据表tbl_bk_role_relation的Validate模型
 */
class BkRoleRelationModel extends Model
{

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
			[['id', 'del_time'], 'integer'], 
			[['role_id', 'parent_id'], 'string', ['max' => 64]]
		];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLangs()
    {
        return [
			'id' => 'ID', 
			'role_id' => '角色', 
			'parent_id' => '上级'
		];
    }

    /**
     * 获取表中的所有数据
     * @return mixed|string|null
     */
    public static function getAll()
    {
        $key = RabcRedis::role_relation_table();
        if (QTTX::$app->redis) {
            $result = QTTX::$app->redis->get($key);
            if($result) return json_decode($result,true);
        }

        $result = QTTX::$app->db
            ->select(['role_id','parent_id'])
            ->from(BkRoleRelationActiveRecord::tableName())
            ->where('del_time=0')
            ->query();
        if (QTTX::$app->redis && $result) {
            QTTX::$app->redis->set($key, json_encode($result));
        }

        return $result;
    }

    /**
     * 获取指定角色的所有子级
     * @param array|string $role_ids 指定角色
     * @return array 返回一维数据,非树状
     */
    public static function getChildren($role_ids = [])
    {
        $result_relation = self::getAll();
        if(empty($result_relation)) return [];
        if (is_string($role_ids)) {
            $role_ids = [$role_ids];
        }
        return self::_get_children($result_relation, $role_ids);
    }


    private static function _get_children(&$result,$pids)
    {
        $ary = [];
        foreach ($result as $item) {
            if (in_array($item['parent_id'],$pids)) {
                $ary[] = $item['role_id'];
                $temp = self::_get_children($result, [$item['role_id']]);
                $ary = array_merge($ary, $temp);
            }
        }
        return $ary;
    }
}
