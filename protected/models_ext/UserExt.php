<?php 
/**
 * 用户类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class UserExt extends User{
    /**
     * @var array 状态
     */
    static $status = array(
        0 => '禁用',
        1 => '启用',
        2 => '回收站',
    );
    /**
     * @var array 状态按钮样式
     */
    static $statusStyle = array(
        0 => 'btn btn-sm btn-warning',
        1 => 'btn btn-sm btn-primary',
        2 => 'btn btn-sm btn-danger'
    );
    public static $ids = [
        '1'=>'总代公司',
        '2'=>'分销公司',
        '3'=>'独立中介',
    ];
    public static $sex = [
    '未知','男','女'
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            // 'houseInfo'=>array(self::BELONGS_TO, 'HouseExt', 'house'),
            'news'=>array(self::HAS_MANY, 'ArticleExt', 'uid'),
            'times'=>array(self::HAS_MANY, 'UserTimeExt', 'uid'),
            'comments'=>array(self::HAS_MANY, 'GradeExt', 'oid'),
            'product'=>array(self::BELONGS_TO, 'ProductExt', 'pid'),
            'areaInfo'=>array(self::BELONGS_TO, 'AreaExt', 'area'),
            'streetInfo'=>array(self::BELONGS_TO, 'AreaExt', 'street'),

        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            array('phone', 'unique', 'message'=>'{attribute}已存在'),
            array('id_card', 'unique', 'message'=>'该身份证已存在'),
            array('phone', 'phonerule'),
        ));
    }

    public function phonerule()
    {
        $phone = $this->phone;
        if(!preg_match("/^1[34578]\d{9}$/", $phone)){
            $this->addError('phone', '请输入正确的手机号');
        }
    }
    /**
     * 返回指定AR类的静态模型
     * @param string $className AR类的类名
     * @return CActiveRecord Admin静态模型
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeValidate() {
        if($this->getIsNewRecord()) {
            $this->created = $this->updated = time();
        }
        else {
            $this->updated = time();
        }
        if($this->area && $areaInfo = $this->areaInfo) {
            $this->area_name = $areaInfo->name;
        }
        if($this->street && $streetInfo = $this->streetInfo) {
            $this->street_name = $streetInfo->name;
        }
        return parent::beforeValidate();
    }

    /**
     * 命名范围
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'sorted' => array(
                'order' => "{$alias}.sort desc,{$alias}.updated desc",
            ),
            'normal' => array(
                'condition' => "{$alias}.status=1 and {$alias}.deleted=0",
                'order'=>"{$alias}.sort desc,{$alias}.updated desc",
            ),
            'undeleted' => array(
                'condition' => "{$alias}.deleted=0",
                // 'order'=>"{$alias}.sort desc,{$alias}.updated desc",
            ),
        );
    }

    /**
     * 绑定行为类
     */
    public function behaviors() {
        return array(
            'CacheBehavior' => array(
                'class' => 'application.behaviors.CacheBehavior',
                'cacheExp' => 0, //This is optional and the default is 0 (0 means never expire)
                'modelName' => __CLASS__, //This is optional as it will assume current model
            ),
            'BaseBehavior'=>'application.behaviors.BaseBehavior',
        );
    }

    public static function getUserByOpenId($value='')
    {
        return UserExt::model()->find("openid='$value'");
    }

}