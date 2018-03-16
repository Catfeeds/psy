<?php
class OrderController extends ApiController
{
	public function actionList()
	{
		$datas = $datas['list'] = [];
		$cid = (int)Yii::app()->request->getQuery('cid',0);
		$uid = (int)Yii::app()->request->getQuery('uid',0);
		$save = (int)Yii::app()->request->getQuery('save',0);
		$page = (int)Yii::app()->request->getQuery('page',1);
		$limit = (int)Yii::app()->request->getQuery('limit',20);
		$kw = $this->cleanXss(Yii::app()->request->getQuery('kw',''));
		$criteria = new CDbCriteria;
		$criteria->order = 'sort desc,updated desc';
		$criteria->limit = $limit;
		if($kw) {
			$criteria->addSearchCondition('name',$kw);
		}
		if($cid) {
			$criteria->addCondition("cid=:cid");
			$criteria->params[':cid'] = $cid;
		}
		if($save&&$uid) {
			$ids = [];
			$saeids = Yii::app()->db->createCommand("select pid from save where uid=$uid")->queryAll();
			if($saeids) {
				foreach ($saeids as $key => $value) {
					$ids[] = $value['pid'];
				}
			}
			$criteria->addInCondition('id',$ids);
		}
		$ress = ProductExt::model()->normal()->getList($criteria);
		$infos = $ress->data;
		$pager = $ress->pagination;
		if($infos) {
			foreach ($infos as $key => $value) {
				$data['list'][] = [
					'id'=>$value->id,
					'name'=>Tools::u8_title_substr($value->name,20),
					'price'=>$value->price,
					'old_price'=>$value->old_price,
					'ts'=>$value->shortdes,
					'image'=>ImageTools::fixImage($value->image,370,250),
				];
			}
		}
		$data['num'] = $pager->itemCount;
		$data['page_count'] = $pager->pageCount;
		$data['page'] = $page;

		$this->frame['data'] = $data;
	}

	public function actionInfo($id='',$openid='')
	{
		$info = ProductExt::model()->findByPk($id);
		$data = $info->attributes;
		$images = $info->images;
		if($images) {
			foreach ($images as $key => $value) {
				$data['images'][] = ImageTools::fixImage($value->url); 
			}
		}
		$data['is_save'] = 0;
		if($openid) {
			$user = UserExt::getUserByOpenId($openid);
			if($uid = $user->id) {
				$data['is_save'] = SaveExt::model()->count("pid=$id and uid=$uid")?1:0;
			}
		}
		if($confs = $info->data_conf) {
			$fields = Yii::app()->file->getFields();
			$confs = json_decode($confs,true);
			$ids = $tagname = [];
			foreach ($confs as $key => $value) {
				$ids[] = $value;
			}
			$criteria = new CDbCriteria;
			$criteria->select = 'id,name';
			$criteria->addInCondition('id',$ids);

			$tags = TagExt::model()->findAll($criteria);
			if($tags) {
				foreach ($tags as $key => $value) {
					$tagname[$value['id']] = $value['name'];
				}
			}
			// var_dump($tags[0]['attributes']);exit;
			foreach ($confs as $key => $value) {
				$data['params'][$fields[$key]] = $tagname[$value];
			}
			$data['created'] = date('Y-m-d',$data['created']);
			$data['updated'] = date('Y-m-d',$data['updated']);
			
		}
		$this->frame['data'] = $data;
	}

	public function actionGetCates()
	{
		$data = [];
		$ress = TagExt::model()->normal()->findAll("cate='pcate'");
		if($ress) {
			foreach ($ress as $key => $value) {
				$data[] = ['id'=>$value->id,'name'=>$value->name];
			}
		}
		$this->frame['data'] = $data;
	}

	public function actionAddOrder()
	{
		$data['pid'] = Yii::app()->request->getPost('pid','');
        $data['username'] = Yii::app()->request->getPost('username','');
        $data['note'] = Yii::app()->request->getPost('note','');
        $data['phone'] = Yii::app()->request->getPost('phone','');
        $openid = Yii::app()->request->getPost('openid','');
        if(!$data['pid']||!$openid) {
        	$this->returnError('参数错误');
        } else {
        	$product = ProductExt::model()->findByPk($data['pid']);
        	$product && $data['pname'] = $product->name;
        }
        if($user = UserExt::getUserByOpenId($openid)) {
        	$user->true_name = $data['username'];
        	$user->phone = $data['phone'];
        	$user->save();
        }
        $order = new OrderExt;
		$order->attributes = $data;
		if(!$order->save()) {
            $this->returnError(current(current($order->getErrors())));
        }
	}	

    public function actionAddSave($pid='',$openid='')
    {
        if($pid&&$openid) {
            $staff = UserExt::getUserByOpenId($openid);
            if($save = SaveExt::model()->find('pid='.(int)$pid.' and uid='.$staff->id)) {
                SaveExt::model()->deleteAllByAttributes(['pid'=>$pid,'uid'=>$staff->id]);
                $this->returnSuccess('取消收藏成功');
            } else {
                $save = new SaveExt;
                $save->uid = $staff->id;
                $save->pid = $pid;
                $save->save();
                $this->returnSuccess('收藏成功');
            }
        }else {
            $this->returnError('请登录后操作');
        }
    }

}