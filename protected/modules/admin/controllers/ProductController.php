<?php
/**
 * 产品控制器
 */
class ProductController extends AdminController{
	
	public $cates = [];

	public $cates1 = [];

	public $controllerName = '';

	public $modelName = 'ProductExt';

	public function init()
	{
		parent::init();
		$this->controllerName = '产品';
		$this->cates = CHtml::listData(TagExt::model()->normal()->findAll("cate='pcate'"),'id','name');
		// $this->cates1 = CHtml::listData(TeamExt::model()->normal()->findAll(),'id','name');
	}
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$cate1='')
	{
		$modelName = $this->modelName;
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
                $criteria->addSearchCondition('name', $value);
            } 
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("{$time_type}>=:beginTime");
            $criteria->addCondition("{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
		if($cate) {
			$criteria->addCondition('cid=:cid');
			$criteria->params[':cid'] = $cate;
		}
		if($cate1) {
			$criteria->addCondition('tid=:cid');
			$criteria->params[':cid'] = $cate1;
		}
		$infos = $modelName::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'cate1'=>$cate1,'infos'=>$infos->data,'cates'=>$this->cates,'cates1'=>$this->cates1,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,]);
	}

	public function actionEdit($id='')
	{
		$modelName = $this->modelName;
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
// var_dump(11);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['list']);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('edit',['cates'=>$this->cates,'article'=>$info,'cates1'=>$this->cates1,]);
	}

	public function actionImagelist($hid)
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = ProductExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		if(Yii::app()->request->getIsPostRequest()) {
			AlbumExt::model()->deleteAllByAttributes(['pid'=>$house->id]);
			$values = Yii::app()->request->getPost("TkExt",[]);
			$urls = $values['album'];
			// $type = $values['type'];
			$sort = $values['sort'];
			if($urls) {
				foreach ($urls as $key => $value) {
					$model =  new AlbumExt;
					$model->pid = $house->id;
					$model->url = $value;
					$model->sort = $sort[$key];
					// $model->type = $type[$key];
					$model->save();
				}
			}
			$this->redirect('list');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('pid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = AlbumExt::model()->getList($criteria,20);
		// var_dump($houses->dat);exit;
		// $this->render('imagelist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
		$this->render('images',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	public function actionEditImage()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'AlbumExt';
		$this->controllerName = '产品相册';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			if($info->save()) {
				$this->setMessage('操作成功','success',['imagelist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('imageedit',['article'=>$info,'hid'=>$hid]);
	}
}