<?php
/**
 * File Name: Index.php
 * Automatically generated by QGC tool
 * @date: 2021-04-21 11:09:02
 * @version: 4.0.4
 */

namespace qh4module\rabc\models\user;


use qh4module\rabc\HpRabc;
use qh4module\rabc\models\BkUserInfoActiveRecord;
use qh4module\token\TokenFilter;
use QTTX;
use qttx\helper\ArrayHelper;
use service\qh_generate_code\SorterValidator;
use qh4module\rabc\models\BkUserActiveRecord;
use qh4module\rabc\models\BkUserModel;

/**
 * Class Index
 * 分页获取tbl_bk_user表的数据
 * @package qh4module\rabc\models\models\user
 */
class Index extends BkUserModel
{
    /**
     * @var int 页数,从1开始
     */
    public $page = 1;

    /**
     * @var int 每页显示数量
     */
    public $limit = 10;

    /**
     * @var array 接收参数,排序规则
     * 格式:['id'=>'asc','name'=>'desc'],
     */
    public $sorter = [];
    
    /**
     * @var string 接收参数，筛选字段：ID
     */
    public $id;

    /**
     * @var string 接收参数，筛选字段：账号
     */
    public $account;

    /**
     * @var string 接收参数，筛选字段：手机号
     */
    public $mobile;

    /**
     * @var string 接收参数，筛选字段：邮箱
     */
    public $email;

    /**
     * @var string 接收参数，筛选字段
     */
    public $wechat_unionid;

    /**
     * @var string 接收参数，筛选字段
     */
    public $wechat_openid;

    /**
     * @var string 接收参数，筛选字段
     */
    public $qq_id;

    /**
     * @var string 接收参数，筛选字段
     */
    public $alipay_id;

    /**
     * @var string 接收参数，筛选字段
     */
    public $weibo_id;

    /**
     * @var string 接收参数，筛选字段
     */
    public $apple_id;

    /**
     * @var int 接收参数，筛选字段：状态
     */
    public $state;


    /**
     * @inheritDoc
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['page', 'limit'], 'integer'],
            [['sorter'],'sorter'],
        ], parent::rules());
    }

    /**
     * @inheritDoc
     */
    public function attributeLangs()
    {
        return ArrayHelper::merge(
            parent::attributeLangs(),
            [
                'page' => '页数',
                'limit' => '每页条数',
                'sorter' => '排序规则',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        // 所有的字段,根据列表显示进行删减
        $fields = ['`ta`.`id`','`ta`.`account`','`ta`.`mobile`','`ta`.`email`','`ta`.`password`','`ta`.`salt`','`ta`.`create_by`','`ta`.`create_time`','`ta`.`wechat_unionid`','`ta`.`wechat_openid`',
            '`ta`.`qq_id`','`ta`.`alipay_id`','`ta`.`weibo_id`',
            '`ta`.`apple_id`','`ta`.`state`',
            'tb.nick_name as create_by_name',
        ];

        $children_ids = $this->getUserId();
        // 构建基础查询
        $tb_info = BkUserInfoActiveRecord::tableName();
        $sql = BkUserActiveRecord::find()
            ->select($fields)
            ->leftJoin("$tb_info as tb", 'ta.create_by=tb.user_id')
            ->whereIn('id', $children_ids);

        // 追加筛选条件
        if ($this->id) {
            $sql->where('`ta`.`id`= :id339')
                ->bindValue('id339',$this->id);
        }if ($this->account) {
            $sql->where('`ta`.`account` like :account797')
                ->bindValue('account797', "%{$this->account}%");
        }if ($this->mobile) {
            $sql->where('`ta`.`mobile` like :mobile338')
                ->bindValue('mobile338', "%{$this->mobile}%");
        }if ($this->email) {
            $sql->where('`ta`.`email` like :email396')
                ->bindValue('email396', "%{$this->email}%");
        }if ($this->wechat_unionid) {
            $sql->where('`ta`.`wechat_unionid`= :wechat_unionid777')
                ->bindValue('wechat_unionid777',$this->wechat_unionid);
        }if ($this->wechat_openid) {
            $sql->where('`ta`.`wechat_openid`= :wechat_openid915')
                ->bindValue('wechat_openid915',$this->wechat_openid);
        }if ($this->qq_id) {
            $sql->where('`ta`.`qq_id`= :qq_id593')
                ->bindValue('qq_id593',$this->qq_id);
        }if ($this->alipay_id) {
            $sql->where('`ta`.`alipay_id`= :alipay_id629')
                ->bindValue('alipay_id629',$this->alipay_id);
        }if ($this->weibo_id) {
            $sql->where('`ta`.`weibo_id`= :weibo_id689')
                ->bindValue('weibo_id689',$this->weibo_id);
        }if ($this->apple_id) {
            $sql->where('`ta`.`apple_id`= :apple_id346')
                ->bindValue('apple_id346',$this->apple_id);
        }if ($this->state) {
            $sql->where('`ta`.`state`= :state726')
                ->bindValue('state726',$this->state);
        }

        // 追加排序
        if ($this->sorter) {
            $sql->orderBy(SorterValidator::format2Mode1($this->sorter));
        }

        // 获取分页结果
        list($total, $data) = $sql
            ->where('`ta`.`del_time`= :del_time755')
            ->bindValue('del_time755',0)
            ->asArray()
            ->pageLimit($this->page, $this->limit);

        return array(
            'total' => $total,
            'list' => $data,
            'page' => $this->page,
            'limit' => $this->limit
        );
    }

    /**
     * 获取应该展示的所有用户id
     * 包括自己 和 下属用户 以及 自己创建的用户
     * @return array|mixed
     */
    protected function getUserId()
    {
        $user_id = TokenFilter::getPayload('user_id');
        $children_ids = HpRabc::getUserRelationChildrenUser($user_id);
        if(empty($children_ids)) $children_ids = [];
        $children_ids[] = $user_id;

        $result = QTTX::$app->db
            ->select('id')
            ->from(BkUserActiveRecord::tableName())
            ->whereArray(['create_by' => $user_id])
            ->where('del_time=0')
            ->column();
        if ($result) {
            return array_unique(array_merge($children_ids, $result));
        }else{
            return array_unique($children_ids);
        }
    }
}
