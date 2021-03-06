<?php
/**
 * File Name: Update.php
 * Automatically generated by QGC tool
 * @date 2021-03-16 11:25:21
 * @version 4.0.0
 */

namespace qh4module\rabc\models\privilege;


use qh4module\rabc\models\BkRelationRolePrivilegeActiveRecord;
use qh4module\rabc\models\RabcRedis;
use qttx\components\db\DbModel;
use qttx\helper\ArrayHelper;
use qh4module\rabc\models\BkPrivilegeActiveRecord;
use qh4module\rabc\models\BkPrivilegeModel;

/**
 * Class Update
 * 更新tbl_bk_privilege表单条数据
 * @package qh4module\rabc\models\privilege
 */
class Update extends BkPrivilegeModel
{

    /**
     * @var string 接收参数,必须：ID
     */
    public $id;

    /**
     * @var string 接收参数,必须：名称
     */
    public $name;

    /**
     * @var string 接收参数,非必须：说明
     */
    public $desc;

    /**
     * @var int 接收参数,必须：类型
     */
    public $type;

    /**
     * @var string 接收参数,非必须：图标
     */
    public $icon;

    /**
     * @var int 接收参数,必须：排序
     */
    public $sort;

    /**
     * @var array 接收参数,关联的角色id
     */
    public $role_ids;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['id', 'name', 'type', 'sort'], 'required'],
        ], parent::rules());
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $db = \QTTX::$app->db;

        $db->beginTrans();

        try {

            // 更新权限资源
            if (!$this->updatePrivilege($db)) {
                return false;
            }

            // 更新关联角色
            $this->updateRole($db);

            // 清理缓存
            RabcRedis::clearRedis();

            $db->commitTrans();

            return true;

        } catch (\Exception $exception) {
            $db->rollBackTrans();
            \QTTX::$app->log->error($exception);
            return false;
        }
    }


    private function updatePrivilege($db)
    {
        $model = BkPrivilegeActiveRecord::findOne($this->id);
        if (empty($model)) {
            $this->addError('id', '更新条目不存在');
            return false;
        }

        $model->name = $this->name;
        $model->desc = $this->desc;
        $model->type = $this->type;
        $model->icon = $this->icon;
        $model->sort = $this->sort;
        $model->update($db);

        return true;
    }


    /**
     * @param $db DbModel
     */
    private function updateRole($db)
    {
        $db->update(BkRelationRolePrivilegeActiveRecord::tableName())
            ->col('del_time', time())
            ->whereArray(['privilege_id' => $this->id])
            ->where('del_time=0')
            ->query();

        if (empty($this->role_ids)) return;

        foreach ($this->role_ids as $rid) {
            $model = new BkRelationRolePrivilegeActiveRecord();
            $model->role_id = $rid;
            $model->privilege_id = $this->id;
            $model->del_time = 0;
            $model->insert($db);
        }
    }
}

