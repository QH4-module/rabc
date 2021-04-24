<?php
/**
 * File Name: RoleCascaderOptionData.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/22 1:30 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\rabc\models\role;


use qh4module\rabc\HpRabc;
use qh4module\rabc\models\BkRoleActiveRecord;
use qh4module\rabc\models\BkRoleRelationModel;
use qh4module\token\TokenFilter;
use QTTX;
use qttx\helper\ArrayHelper;
use qttx\web\Model;

class CascaderOptionData extends Model
{
    /**
     * @var bool 是否只允许自己所关联的和下属角色选中
     */
    public $only_own_enable;

    /**
     * @var bool 是否只允许自己下属角色选中
     * 只有 only_own_enable 为 true ,该参数才有效
     */
    public $only_children_enable;


    private $result_role = [];

    private $result_relation = [];

    private $relation_role = [];

    /**
     * @inheritDoc
     */
    public function run()
    {
        $user_id = TokenFilter::getPayload('user_id');

        $result_role = QTTX::$app->db
            ->select(['id', 'name'])
            ->from(BkRoleActiveRecord::tableName())
            ->where('del_time=0')
            ->query();
        // 格式化成 id=>name 的格式
        $this->result_role = ArrayHelper::map($result_role, 'id', 'name');

        // 获取角色关联表所有信息
        $this->result_relation = BkRoleRelationModel::getAll();

        // 获取用户关联的所有角色,包括下级
        $this->relation_role = HpRabc::getUserRelationChildrenRole($user_id, !!$this->only_children_enable);

        return [
            [
                'children' => $this->getChildren(1),
                'label' => '系统管理员',
                'value' => 1,
                'checkboxDisabled' => $user_id != 1,
            ]
        ];
    }


    private function getChildren($pid)
    {
        $ary = [];

        foreach ($this->result_relation as $rel) {
            if ($rel['parent_id'] == $pid && isset($this->result_role[$rel['role_id']])) {
                $temp = [
                    'label' => $this->result_role[$rel['role_id']],
                    'value' => $rel['role_id'],
                ];
                if ($this->only_own_enable) {
                    $temp['checkboxDisabled'] = !in_array($rel['role_id'], $this->relation_role);
                }

                $children = $this->getChildren($rel['role_id']);
                if (!empty($children)) {
                    $temp['children'] = $children;
                }
                $ary[] = $temp;
            }
        }
        return $ary;
    }
}