<?php
/**
 * File Name: ParsePrivilegeYml.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021-01-20 5:28 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\rabc\models\privilege;


use qh4module\rabc\models\BkPrivilegeActiveRecord;
use qh4module\rabc\models\RabcRedis;
use qh4module\token\TokenFilter;
use QTTX;
use qttx\basic\Loader;
use qttx\components\db\DbModel;
use qttx\helper\StringHelper;
use qttx\web\Model;


class ParsePrivilegeYml extends Model
{
    public function run()
    {
        //  仅在dev模式可用
        if (!ENV_DEV) return false;

        $file = StringHelper::combPath(Loader::getAlias('@libs'), 'privilege.yml');

        $yml = yaml_parse_file($file);

        $db = QTTX::$app->db;

        $db->beginTrans();

        try {

            $db->update(BkPrivilegeActiveRecord::tableName())
                ->cols(['del_time' => time()])
                ->where('del_time=0')
                ->query();

            foreach ($yml as $row) {
                if (!$this->parseRow($row, [], [], $db)) {
                    return false;
                }
            }

            RabcRedis::clearRedis();

            $db->commitTrans();

            return true;

        } catch (\Exception $exception) {

            $db->rollBackTrans();

            throw $exception;
        }

    }

    /**
     * 解析并插入单行数据
     * @param array $row
     * @param array $pids
     * @param DbModel $db
     * @return mixed
     */
    private function parseRow($row, $pids, $keys, $db)
    {
        if (!isset($row['key'])) {
            unset($row['children']);
            $this->addError('id', '每条配置都必须带有key参数: ' . json_encode($row));
            return false;
        }

        $id = isset($row['id']) ? $row['id'] : QTTX::$app->snowflake->id();
        $pids[] = $id;
        $keys[] = $row['key'];

        $uid = TokenFilter::getPayload('user_id');
        if(empty($uid)) $uid = 1;
        $cols = [
            'id' => $id,
            'parent_id' => sizeof($pids) >= 2 ? $pids[sizeof($pids) - 2] : '',
            'id_path' => implode(',', $pids),
            'key' => $row['key'],
            'key_path' => implode('.', $keys),
            'name' => isset($row['name']) ? $row['name'] : '',
            'desc' => isset($row['desc']) ? $row['desc'] : '',
            'type' => isset($row['type']) ? $row['type'] : 0,
            'level' => isset($row['level']) ? $row['level'] : 0,
            'icon' => isset($row['icon']) ? $row['icon'] : '',
            'path' => isset($row['path']) ? $row['path'] : '',
            'create_time' => time(),
            'create_by' => $uid,
            'sort' => 0,
            'del_time' => 0
        ];

        $db->insert(BkPrivilegeActiveRecord::tableName())
            ->cols($cols)
            ->query();

        if (isset($row['children'])) {
            foreach ($row['children'] as $item) {
                if (!$this->parseRow($item, $pids, $keys, $db)) {
                    return false;
                }
            }
        }

        return true;
    }
}
