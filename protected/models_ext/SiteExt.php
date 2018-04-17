<?php 
/**
 * 站点配置类
 * 数据结构为 name为 qjpz value为 属性分类的key-value组成的json数据
 * @author steven.allen <[<email address>]>
 * @date(2017.2.13)
 */
class SiteExt extends Site{

    // 属性
    public static $cates = [
        'appid'=>'',
        'apps'=>'',
        'mch_id'=>'',
        'mch_key'=>'',
        'indeximages'=>'',
        'productnopic'=>'',
        'articlenopic'=>'',
        'usernopic'=>'',
        'tel'=>'',
        'qq'=>'',
        'addr'=>'',
        'boss_name'=>'',
        'productnotice'=>'',
        'sitecolor'=>'',
        'sitename'=>'',
        'temid'=>'',
    ];
    public static $cateName = [
        'qjpz' => '全局配置',
        'sen'=>'敏感词配置',
    ];

    // 属性分类
    public static $cateTag = [
        'qjpz'=> [
            'indeximages'=>['type'=>'multiImage','max'=>5,'name'=>'首页轮播图'],
            'productnopic'=>['type'=>'image','max'=>1,'name'=>'默认产品图片'],
            'articlenopic'=>['type'=>'image','max'=>1,'name'=>'默认文章图片'],
            'usernopic'=>['type'=>'image','max'=>1,'name'=>'默认用户头像'],
            'appid'=>['type'=>'text','name'=>'appid'],
            'apps'=>['type'=>'text','name'=>'appsecret'],
            'temid'=>['type'=>'text','name'=>'模板ID'],
            'mch_id'=>['type'=>'text','name'=>'商户ID'],
            'mch_key'=>['type'=>'text','name'=>'商户支付key'],
            'tel'=>['type'=>'text','name'=>'站点电话'],
            'qq'=>['type'=>'text','name'=>'站点qq'],
            'addr'=>['type'=>'text','name'=>'地址'],
            'boss_name'=>['type'=>'text','name'=>'老板名字'],
            'productnotice'=>['type'=>'text','name'=>'产品备注'],
            'sitecolor'=>['type'=>'text','name'=>'站点颜色'],
            'sitename'=>['type'=>'text','name'=>'站点名'],
            ],
        'sen'=>[
            'sen'=>['type'=>'text','name'=>'敏感词'],
        ],
    ];

	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            // 'baike'=>array(self::BELONGS_TO, 'BaikeExt', 'bid'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            array(implode(",", array_keys(self::$cates)) ,'safe'),
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
            )
        );
    }

    // 重写get魔术方法
    public function __get($value)
    {
        if(in_array($value, array_keys(self::$cates))) {
            $dc = json_decode($this->value,true);
            if($dc && isset($dc[$value])) {
                return $dc[$value];
            }
        } else {
            return parent::__get($value);
        }
    }

    // 重写set魔术方法
    public function __set($name, $value)
    {
        if(isset(self::$cates[$name])) {
            if(is_array($this->value))
                $data_conf = $this->value;
            else
                $data_conf = CJSON::decode($this->value);
            self::$cates[$name] = $value;
            $data_conf[$name] = $value;
            $this->value = json_encode($data_conf);
        }
        else
            parent::__set($name, $value);
    }

    /**
     * 通过name获取
     */
    public function getSiteByCate($cate)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'name=:cate',
            'order' => 'id ASC',
            'params' => array(':cate'=>$cate)
        ));
        return $this;
    }

    /**
     * [getAttr 获取配置]
     * @param  string $cate [类别]
     * @param  string $attr [属性]
     * @return [type]       [description]
     */
    public static function getAttr($cate='',$attr='')
    {
        if(!in_array($attr, array_keys(SiteExt::$cates)))
            return '';
        $model = self::model()->getSiteByCate($cate)->find();
return isset($model)&&$model->$attr?$model->$attr:'';
    }

}