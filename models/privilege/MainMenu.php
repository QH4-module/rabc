<?php
/**
 * File Name: MainMenu.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/24 10:07 上午
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
use qh4module\token\TokenFilter;
use QTTX;
use qttx\helper\ArrayHelper;
use qttx\web\Model;

class MainMenu extends Model
{
    public function run()
    {
        $user_id = TokenFilter::getPayload('user_id');

        $privilege_ids = HpRabc::getUserRelationPrivilege($user_id);
        if (empty($privilege_ids)) return ['main_menu' => [], 'privilege_keys' => []];

        $result = QTTX::$app->db
            ->select('*')
            ->from(BkPrivilegeActiveRecord::tableName())
            ->whereIn('id', $privilege_ids)
            ->where('type=1 and del_time=0')
            ->orderBy(['level'=>'asc','sort'=>'desc'])
            ->query();
        if (empty($result)) return ['main_menu' => [], 'privilege_keys' => []];

        // 获取所有的 key_path
        $keys = QTTX::$app->db
            ->select('key_path')
            ->from(BkPrivilegeActiveRecord::tableName())
            ->whereIn('id', $privilege_ids)
            ->where('del_time=0')
            ->column();

        return [
            'main_menu' => ArrayHelper::formatTree($result, ''),
            'privilege_keys' => $keys,
        ];
    }

}