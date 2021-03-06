<?php
/**
 * File Name: Create.php
 * Automatically generated by QGC tool
 * @date: 2021-04-22 13:16:58
 * @version: 4.0.4
 */

namespace qh4module\rabc\models\role;


use qh4module\rabc\HpRabc;
use qh4module\rabc\models\BkRelationRolePrivilegeActiveRecord;
use qh4module\rabc\models\BkRoleRelationActiveRecord;
use qh4module\rabc\models\RabcRedis;
use qttx\helper\ArrayHelper;
use qh4module\rabc\models\BkRoleActiveRecord;
use qh4module\rabc\models\BkRoleModel;
use qh4module\token\TokenFilter;
use QTTX;

/**
 * Class Create
 * tbl_bk_role表新增一条数据
 * @package qh4module\rabc\models\role
 */
class Create extends BkRoleModel
{
    /**
     * @var array 接收参数,上级id
     */
    public $parent_ids = [];
    
    /**
     * @var string 接收参数,必须：名称
     */
    public $name;

    /**
     * @var string 接收参数,非必须：说明
     */
    public $desc;

    /**
     * @var array 接收参数,权限id
     */
    public $privilege_ids;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['parent_ids','name'],'required'],
            [['parent_ids'], 'customer', 'callback' => function ($value) {
                $roles = HpRabc::getUserRelationChildrenRole(null,false);
                if (empty(array_diff($value, $roles))) {
                    return true;
                }
                return '上级超出授权范围';
            }],
            [['privilege_ids'],'customer','callback'=>function($value){
                $privileges = HpRabc::getUserRelationPrivilege();
                if (empty(array_diff($value, $privileges))) {
                    return true;
                }
                return '权限超出授权范围';
            }]
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

            $id = QTTX::$app->snowflake->id();

            // 插入角色表
            $this->insertRole($id, $db);

            // 插入角色关联表
            $this->insertRelation($id, $db);

            // 插入权限
            $this->insertPrivilege($id, $db);

            // 清理缓存
            RabcRedis::clearRedis();

            $db->commitTrans();

            return true;

        } catch (\Exception $exception) {
            $db->rollBackTrans();
            throw $exception;
        }

    }

    protected function insertRole($id,$db)
    {
        $model = new BkRoleActiveRecord();
        $model->id = $id;
        $model->name = $this->name;
        $model->desc = $this->desc;
        $model->create_by = TokenFilter::getPayload('user_id');
        $model->create_time = time();
        $model->is_fixed = 0;
        $model->del_time = 0;

        return $model->insert($db);
    }

    protected function insertRelation($id,$db)
    {
        foreach ($this->parent_ids as $pid) {
            $model = new BkRoleRelationActiveRecord();
            $model->role_id = $id;
            $model->parent_id = $pid;
            $model->del_time = 0;
            $model->insert($db);
        }
    }

    protected function insertPrivilege($id,$db)
    {
        if(empty($this->privilege_ids)) return;

        foreach ($this->privilege_ids as $prv_id) {
            $model = new BkRelationRolePrivilegeActiveRecord();
            $model->role_id = $id;
            $model->privilege_id = $prv_id;
            $model->del_time = 0;
            $model->insert($db);
        }
    }
}