<?php
/**
 * File Name: Delete.php
 * Automatically generated by QGC tool
 * @date: 2021-04-21 11:09:02
 * @version: 4.0.4
 */

namespace qh4module\rabc\models\user;


use qh4module\rabc\HpRabc;
use qh4module\rabc\models\BkRelationUserRoleActiveRecord;
use qh4module\rabc\models\BkUserActiveRecord;
use qh4module\rabc\models\BkUserModel;
use QTTX;

/**
 * Class Delete
 * 从tbl_bk_user表的删除数据
 * @package qh4module\rabc\models\models\user
 */
class Delete extends BkUserModel
{
    /**
     * @var string[]|int[] 接收参数,必须：主键
     */
    public $ids;
    
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['ids'],'required'],
            [['ids'], 'array', 'type' => function ($value) {
                return is_string($value) || is_numeric($value);
            }],
        ];
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $db = QTTX::$app->db;

        $db->beginTrans();

        try {

            $result = BkUserActiveRecord::find($db)
                ->whereIn('id', $this->ids)
                ->all();
            if (empty($result)) {
                $this->addError('ids', '删除的条目不存在');
                return false;
            }

            /**
             * @var $item BkUserActiveRecord
             */
            foreach ($result as $item) {
                // 伪删除
                $item->del_time = time();
                $item->update();
            }

            // 删除角色关联
            $db->update(BkRelationUserRoleActiveRecord::tableName())
                ->col('del_time', time())
                ->whereIn('user_id', $this->ids)
                ->where('del_time=0')
                ->query();

            // 清理缓存
            foreach ($this->ids as $item) {
                HpRabc::delRedisUserInfo($item);
            }

            $db->commitTrans();

            return true;

        } catch (\Exception $exception) {
            $db->rollBackTrans();

            throw $exception;
        }
    }
}