<?php
class TagController extends ApiController{
	public function actionIndex($cate='')
	{
		if($cate == 'wzlm') {
			$this->frame['data'] = CacheExt::gas('tag_wzlm','AreaExt',0,'顶部标签缓存',function (){
		            return Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='wzlm' order by sort asc")->queryAll();
		        });
		}
		elseif($cate) {
			$this->frame['data'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='$cate' order by sort asc")->queryAll();
		}
	}
	public function actionArea()
	{
		$this->frame['data'] = CacheExt::gas('psy_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		        });
	}
	public function actionPublishTags()
	{
		$areas = CacheExt::gas('psy_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		            });
		$tags = CacheExt::gas('wap_publish_tags','AreaExt',0,'wap发布房源标签',function (){
					$wylx['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='wylx' order by sort asc")->queryAll();
					$wylx['name'] = 'wylx';

					$zxzt['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='zxzt' order by sort asc")->queryAll();
					$zxzt['name'] = 'zxzt';
					$sfprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='sfprice' order by sort asc")->queryAll();
					$sfprice['name'] = 'sfprice';
					return [$wylx,$zxzt,$sfprice];
		});
		$tags[] = ['name'=>'area','list'=>$areas];
		$tags[] = ['name'=>'mode','list'=>Yii::app()->params['dllx']];
		$this->frame['data'] = $tags;

	}
	public function actionList($cate='plotFilter')
	{
		switch ($cate) {
			case 'plotFilter':
				$area = [];
				$area['name'] = '城市';
				$area['filed'] = 'area';
				$areas = CacheExt::gas('psy_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		        });
            	$area['list'] = $areas;
            	$ots = CacheExt::gas('psy_all_filters_new','AreaExt',0,'wap筛选标签缓存',function (){
	            	$aveprice = [];
					$aveprice['name'] = '模式';
					$aveprice['filed'] = 'mode';
					$aveprice['list'] = [['id'=>1,'name'=>'仅线上咨询'],['id'=>2,'name'=>'支持线上线下咨询']];

					$sfprice = [];
					$sfprice['name'] = '领域';
					$sfprice['filed'] = 'ly';
					$sfprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='ly' order by sort asc")->queryAll();

					$zcss = [];
					$zcss['name'] = '价格';
					$zcss['filed'] = 'price';
					$zcss['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='price' order by sort asc")->queryAll();

					$sort = [];
					$sort['name'] = '排序';
					$sort['filed'] = 'sort';
					$sort['list'] = [
						['id'=>4,'name'=>'热度倒序'],
						['id'=>1,'name'=>'评分倒序'],
						['id'=>2,'name'=>'工作年限倒序'],
						['id'=>3,'name'=>'默认排序'],
					];

					$edu = [];
					$edu['name'] = '学历';
					$edu['filed'] = 'edu';
					foreach (Yii::app()->params['edu'] as $key => $value) {
						$edu['list'][] = ['id'=>$key,'name'=>$value];
					}

					$zz = [];
					$zz['name'] = '资质';
					$zz['filed'] = 'zz';
					foreach (Yii::app()->params['zz'] as $key => $value) {
						$zz['list'][] = ['id'=>$key,'name'=>$value];
					}
					// $edu['list'] = Yii::app()->;

					$more = [];
					$more['name'] = '更多';
					$more['list'] = [$sort,$edu,$zz];
					return [$sfprice,$zcss,$more];
				});
				// var_dump($ots);exit;
				array_unshift($ots,$area);
            	$this->frame['data'] = $ots;
				break;
			case 'old':
				$area = [];
				$area['name'] = '区域';
				$area['filed'] = 'area';
				$areas = CacheExt::gas('psy_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		        });
            	$area['list'] = $areas;
            	$ots = CacheExt::gas('psy_all_filters','AreaExt',0,'wap筛选标签缓存',function (){
	            	$aveprice = [];
					$aveprice['name'] = '模式';
					$aveprice['filed'] = 'mode';
					$aveprice['list'] = [['id'=>0,'name'=>'请选择咨询模式'],['id'=>1,'name'=>'仅线上咨询'],['id'=>2,'name'=>'支持线上线下咨询']];
					$sfprice = [];
					$sfprice['name'] = '领域';
					$sfprice['filed'] = 'ly';
					$sfprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='ly' order by sort asc")->queryAll();
					$zcss = [];
					$zcss['name'] = '专长';
					$zcss['filed'] = 'zc';
					$zcss['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='zc' order by sort asc")->queryAll();
					$sort = [];
					$sort['name'] = '排序';
					$sort['filed'] = 'sort';
					$sort['list'] = [
						['id'=>1,'name'=>'评分从高到低'],
						['id'=>2,'name'=>'工作年限倒序'],
						['id'=>3,'name'=>'默认排序'],
					];
					$edu = [];
					$edu['name'] = '学历';
					$edu['filed'] = 'edu';
					$edu['list'][] = ['id'=>0,'name'=>'请选择学历'];
					foreach (Yii::app()->params['edu'] as $key => $value) {
						$edu['list'][] = ['id'=>$key,'name'=>$value];
					}
					$zz = [];
					$zz['name'] = '资质';
					$zz['filed'] = 'zz';
					$zz['list'][] = ['id'=>0,'name'=>'请选择资质'];
					foreach (Yii::app()->params['zz'] as $key => $value) {
						$zz['list'][] = ['id'=>$key,'name'=>$value];
					}
					// $edu['list'] = Yii::app()->;
					$more = [];
					$more['name'] = '更多';
					$more['list'] = [$sfprice,$edu,$zz,$sort];
					return [$aveprice,$zcss,$more];
				});
				// var_dump($ots);exit;
				array_unshift($ots,$area);
            	$this->frame['data'] = $ots;
				break;
			default:
				# code...
				break;
		}
	}

	public function addChild($areas)
    {
        $count = count($areas);
        for ($i = 0;$i<$count;$i++){
            if($child = $areas[$i]->childArea){
                $child = $this->addChild($child);
            }
            //将对象转换成数组
            $areas[$i] = $areas[$i]->attributes;
            if($child){
                $areas[$i]['childAreas']=$child;
            }
        }
        return $areas;
    }
}