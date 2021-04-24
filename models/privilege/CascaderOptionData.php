<?php
/**
 * File Name: CascaderOptionData.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/22 9:38 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\rabc\models\privilege;


use qh4module\rabc\HpRabc;
use qh4module\rabc\models\BkPrivilegeActiveRecord;
use qttx\helper\ArrayHelper;
use qttx\web\Model;

class CascaderOptionData extends Model
{
    /**
     * @var bool 是否只返回自己相关的权限资源
     * 权限资源是单继承,下级权限是从自己的衍生而来,所以只取自己直接关联的权限就可以
     */
    public $only_own;


    public function run()
    {
        if ($this->only_own) {
            $ids = HpRabc::getUserRelationPrivilege();
        }

        $sql = \QTTX::$app->db
            ->select(['id as value', 'parent_id', 'name as label','id as key'])
            ->from(BkPrivilegeActiveRecord::tableName());

        if ($this->only_own) {
            if (!isset($ids) || !is_array($ids)) {
                return [];
            }
            $sql->whereIn('id', $ids);
        }

        $result = $sql->where('del_time=0')
            ->orderBy([
                'level' => 'asc',
                'sort' => 'desc',
                'id' => 'asc',
            ])
            ->query();

        return ArrayHelper::formatTree($result, '', 'parent_id', 'value');
    }
}