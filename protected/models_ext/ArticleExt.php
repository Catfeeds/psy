<?php 
/**
 * 文章类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.5)
 */
class ArticleExt extends Article{
    /**
     * @var array 状态
     */
    static $status = array(
        0 => '禁用',
        1 => '启用',
        2 => '回收站',
    );

    static $keywordsSwitch = array(
        0 => '关闭',
        1 => '开启',
    );

    /**
     * @var array 状态按钮样式
     */
    static $statusStyle = array(
        0 => 'btn btn-sm btn-warning',
        1 => 'btn btn-sm btn-primary',
        2 => 'btn btn-sm btn-danger'
    );
    /**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'cate'=>array(self::BELONGS_TO, 'TagExt', 'cid'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            // array('name', 'unique', 'message'=>'{attribute}已存在')
        ));
    }

    /**
     * 返回指定AR类的静态模型
     * @param string $className AR类的类名
     * @return CActiveRecord Admin静态模型
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function afterFind() {
        parent::afterFind();
        if(!$this->image) {
            $this->image = SiteExt::getAttr('qjpz','articlenopic');
        }
    }

    public function beforeValidate() {
        if($this->getIsNewRecord())
            $this->created = $this->updated = time();
        else
            $this->updated = time();
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
                'order' => 'sort desc',
            ),
            'undeleted' => array(
                'condition' => 'deleted=0',
                'order' => 'sort desc',
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

    public function getYw()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'cid=:cate',
            // 'order' => 'id ASC',
            'params' => array(':cate'=>'44')
        ));
        return $this;
    }

    public function getFw()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'cid=:cate',
            // 'order' => 'id ASC',
            'params' => array(':cate'=>'45')
        ));
        return $this;
    }

    public function getJs()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'cid=:cate',
            // 'order' => 'id ASC',
            'params' => array(':cate'=>'51')
        ));
        return $this;
    }

    public function getLx()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'cid=:cate',
            // 'order' => 'id ASC',
            'params' => array(':cate'=>'52')
        ));
        return $this;
    }

    public function getNormal()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'cid!=:cate1 and cid!=:cate2',
            'order' => 'id ASC',
            'params' => array(':cate1'=>'19',':cate2'=>'20')
        ));
        return $this;
    }
}