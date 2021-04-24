<?php
/**
 * File Name: TraitRabcController.php
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


use qh4module\city\HpCity;
use qh4module\rabc\models\privilege\MainMenu;
use qh4module\rabc\models\privilege\ParsePrivilegeYml;

trait TraitRabcController
{

    /**
     * 解析权限 yml 文件
     * 文件需要放在 runtime 目录下
     * @return array
     */
    public function actionParseYml()
    {
        $model = new ParsePrivilegeYml();

        return $this->runModel($model);
    }


    /**
     * 获取城市数据树,用于 CascaderSelect 组件
     * @return array
     */
    public function actionCityOptionData()
    {
        $result = HpCity::optionData([1, 2, 3], ['id' => 'value', 'name' => 'label']);

        return $this->RS($result);
    }

    /**
     * 获取角色数据树, 用于 CascaderSelect 组件
     * @return array
     */
    public function actionRoleCascaderOptionData()
    {
        $model = new \qh4module\rabc\models\role\CascaderOptionData();

        return $this->runModel($model);
    }

    /**
     * 获取权限资源数据树,用于 tree 组件
     * @return array
     */
    public function actionPrivilegeCascaderOptionData()
    {
        $model = new \qh4module\rabc\models\privilege\CascaderOptionData();

        return $this->runModel($model);
    }

    /**
     * 获取用户权限和主菜单
     * @return array
     */
    public function actionMainMenu()
    {
        $model = new MainMenu();

        return $this->runModel($model);
    }

    /**
     * 分页获取用户数据
     * @return array [total,list,page,limit]
     */
    public function actionUserIndex()
    {
        $model = new \qh4module\rabc\models\user\Index();

        return $this->runModel($model);
    }

    /**
     * 新增一条用户数据
     */
    public function actionUserCreate()
    {
        $model = new \qh4module\rabc\models\user\Create();

        return $this->runModel($model);
    }

    /**
     * 更新单条用户数据
     */
    public function actionUserUpdate()
    {
        $model = new \qh4module\rabc\models\user\Update();

        return $this->runModel($model);
    }

    /**
     * 删除多条用户数据
     */
    public function actionUserDelete()
    {
        $model = new \qh4module\rabc\models\user\Delete();

        return $this->runModel($model);
    }

    /**
     * 获取一条用户记录的详细信息
     * @return array
     */
    public function actionUserDetail()
    {
        $model = new \qh4module\rabc\models\user\Detail();

        return $this->runModel($model);
    }


    /**
     * 分页获取角色数据
     * @return array [total,list,page,limit]
     */
    public function actionRoleIndex()
    {
        $model = new \qh4module\rabc\models\role\Index();

        return $this->runModel($model);
    }

    /**
     * 新增一条角色数据
     */
    public function actionRoleCreate()
    {
        $model = new \qh4module\rabc\models\role\Create();

        return $this->runModel($model);
    }

    /**
     * 更新单条角色数据
     */
    public function actionRoleUpdate()
    {
        $model = new \qh4module\rabc\models\role\Update();

        return $this->runModel($model);
    }

    /**
     * 删除多条角色数据
     */
    public function actionRoleDelete()
    {
        $model = new \qh4module\rabc\models\role\Delete();

        return $this->runModel($model);
    }

    /**
     * 获取一条角色记录的详细信息
     * @return array
     */
    public function actionRoleDetail()
    {
        $model = new \qh4module\rabc\models\role\Detail();

        return $this->runModel($model);
    }

    /**
     * 分页获取权限资源数据
     * @return array [total,list,page,limit]
     */
    public function actionPrivilegeIndex()
    {
        $model = new \qh4module\rabc\models\privilege\Index();

        return $this->runModel($model);
    }

    /**
     * 更新单条权限资源数据
     * @return array
     */
    public function actionPrivilegeUpdate()
    {
        $model = new \qh4module\rabc\models\privilege\Update();

        return $this->runModel($model);
    }

    /**
     * 获取一条权限资源记录的详细信息
     * @return array
     */
    public function actionPrivilegeDetail()
    {
        $model = new \qh4module\rabc\models\privilege\Detail();

        return $this->runModel($model);
    }
}