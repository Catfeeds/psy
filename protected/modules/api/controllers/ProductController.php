<?php
class ProductController extends ApiController
{
	public function actionList()
	{
		$data = $data['list'] = [];
		$cid = (int)Yii::app()->request->getQuery('cid',0);
		$uid = (int)Yii::app()->request->getQuery('uid',0);
		$save = (int)Yii::app()->request->getQuery('save',0);
		$order = (int)Yii::app()->request->getQuery('order',0);
		$mode = (int)Yii::app()->request->getQuery('mode',0);
		$zc = (int)Yii::app()->request->getQuery('zc',0);
		$edu = (int)Yii::app()->request->getQuery('edu',0);
		$zz = (int)Yii::app()->request->getQuery('zz',0);
		$ly = (int)Yii::app()->request->getQuery('ly',0);
		$area = (int)Yii::app()->request->getQuery('area',0);
		$street = (int)Yii::app()->request->getQuery('street',0);
		$sort = (int)Yii::app()->request->getQuery('sort',0);
		$page = (int)Yii::app()->request->getQuery('page',1);
		$limit = (int)Yii::app()->request->getQuery('limit',20);
		$kw = $this->cleanXss(Yii::app()->request->getQuery('kw',''));
		!$page && $page = 1;
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
		if($mode) {
			$criteria->addCondition("zx_mode=:zx_mode");
			$criteria->params[':zx_mode'] = $mode==1?$mode:0;
		}
		if($zc) {
			$criteria->addCondition("zc=:zc");
			$criteria->params[':zc'] = $zc;
		}
		if($zz) {
			$criteria->addCondition("mid=:zz");
			$criteria->params[':zz'] = $zz;
		}
		if($edu) {
			$criteria->addCondition("edu=:edu");
			$criteria->params[':edu'] = $edu;
		}
		if($ly) {
			$criteria->addCondition("ly=:ly");
			$criteria->params[':ly'] = $ly;
		}
		if($area) {
			$criteria->addCondition("area=:area");
			$criteria->params[':area'] = $area;
		}
		if($street) {
			$criteria->addCondition("street=:street");
			$criteria->params[':street'] = $street;
		}
		if($sort) {
			switch ($sort) {
				case '1':
					$criteria->order = 'pf desc';
					break;
				case '2':
					$criteria->order = 'work_year asc';
					break;
				default:
					$criteria->order = 'sort desc,updated desc';
					break;
			}
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
		if($order&&$uid) {
			$ids = [];
			$saeids = Yii::app()->db->createCommand("select pid from `order` where uid=$uid")->queryAll();
			if($saeids) {
				foreach ($saeids as $key => $value) {
					$ids[] = $value['pid'];
				}
			}
			$criteria->addInCondition('id',$ids);
		}
		$criteria->addCondition('type=2');
		$ress = UserExt::model()->normal()->getList($criteria,$limit);
		$infos = $ress->data;
		$pager = $ress->pagination;
		if($infos) {
			foreach ($infos as $key => $value) {
				$data['list'][] = [
					'id'=>$value->id,
					'name'=>Tools::u8_title_substr($value->name,20),
					'content'=>Tools::u8_title_substr($value->content,60),
					'place'=>$value->street_name,
					'hits'=>$value->hits,
					'pf'=>$value->pf,
					'year'=>date('Y')-$value->work_year+1,
					'zc'=>$value->zc?TagExt::model()->findByPk($value->zc)->name:'',
					'ly'=>$value->ly?TagExt::model()->findByPk($value->ly)->name:'',
					'zz'=>$value->mid?Yii::app()->params['zz'][$value->mid]:'',
					'image'=>ImageTools::fixImage($value->image,370,250),
				];
			}
		}
		$data['num'] = $pager->itemCount;
		$data['page_count'] = $pager->pageCount;
		$data['page'] = $page;
		if(!isset($data['list']))
			$data['list'] = [];
		$this->frame['data'] = $data;
	}

	public function actionInfo($id='',$openid='')
	{
		$info = UserExt::model()->findByPk($id);
		$info->hits += 1;
		$info->save();
		$data = $info->attributes;
		$data['image'] && $data['image'] = ImageTools::fixImage($data['image']);
		$tags = [];
		$data['zx_mode']==0 && $tags[] = '可线下咨询';
		if($data['ly']) {
			$tags[] = TagExt::model()->findByPk($data['ly'])->name;
		}
		if($data['zc']) {
			$tags[] = TagExt::model()->findByPk($data['zc'])->name;
		}
		$data['tags'] = $tags;
		$data['mid'] && $data['zz'] = Yii::app()->params['zz'][$data['mid']];
		$data['edu'] && $data['edu'] = Yii::app()->params['edu'][$data['edu']];
		$data['work_year'] = date('Y')-$data['work_year']+1;
		$data['times'] = $data['comments'] = [];
		if($times = $info->times) {
			// var_dump(1);exit;
			foreach ($times as $key => $value) {
				$tmp['week'] = $value['week'];
				$tmp['time_area'] = $value['begin'];
				// $tmp['end'] = $value['end'];
				$data['times'][] = $tmp;
				unset($tmp);
			}
		}
		if($comments = $info->comments) {
			foreach ($comments as $key => $value) {
				$thisuser = $value->user;
				$tmp['username'] = $value->is_nm?'匿名':$thisuser->name;
				$tmp['image'] = ImageTools::fixImage($value->is_nm?$SiteExt::getAttr('qjpz','usernopic'):$thisuser->image);
				$tmp['note'] = $value['note'];
				$tmp['time'] = date('Y-m-d',$value['updated']);
				$data['comments'][] = $tmp;
				unset($tmp);
			}
		}
		$nowdata = [];
		foreach (['id','name','image','area_name','street_name','tags','zz','company','pf','hits','work_year','content','place','price_note','price','times','comments','phone'] as $key => $value) {
			$nowdata[$value] = $data[$value];
		}
		$this->frame['data'] = $nowdata;
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
        $form_id = Yii::app()->request->getPost('form_id','');
        $openid = Yii::app()->request->getPost('openid','');

        if(!$data['pid']||!$openid) {
        	$this->returnError('参数错误');
        } else {
        	$product = ProductExt::model()->findByPk($data['pid']);
        	$product && $data['pname'] = $product->name;
        }
        if($user = UserExt::getUserByOpenId($openid)) {
        	$data['uid'] = $user->id;
        	$user->true_name = $data['username'];
        	$user->phone = $data['phone'];
        	$user->save();
        }
        $order = new OrderExt;
		$order->attributes = $data;
		if(!$order->save()) {
            $this->returnError(current(current($order->getErrors())));
        } else {
        	// $appid=SiteExt::getAttr('qjpz','appid');
	        // $apps=SiteExt::getAttr('qjpz','apps');
	        // if(!$appid||!$apps) {
	        //     return '';
	        // }
	        // $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$apps";
	        // $res = HttpHelper::getHttps($url);
        	// // $this->returnError($this->getAt());
        	// // var_dump($this->getAt());exit;
        	// // Yii::log($this->getAt());
        	// $this->sendMsg($form_id,$product->name,$data['username'],$data['phone'],$data['note']);
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

    public function sendMsg($form_id,$pname,$username,$phone,$note)
    {
    	if($token = $this->getAt()) {
    		$openid = SiteExt::getAttr('qjpz','openid');
	    	$temid = SiteExt::getAttr('qjpz','temid');
	    	if($openid&&$temid) {
	            // $token = $this->getAT();
	            $data['touser'] = $openid;
	            $data['template_id'] = $temid;
	            $data['form_id'] = $form_id;
	            $data['page'] = '';
	            $data['data']['keyword1']['color'] = '';
	            $data['data']['keyword2']['color'] = '';
	            $data['data']['keyword3']['color'] = '';
	            $data['data']['keyword4']['color'] = '';
	            $data['data']['keyword1']['value'] = $pname;
	            $data['data']['keyword2']['value'] = $username;
	            $data['data']['keyword3']['value'] = $phone;
	            $data['data']['keyword4']['value'] = $note;
	            $data['emphasis_keyword'] = '';
	            $posturl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$token";
	            // var_dump($posturl,$data);exit;
	            // Yii::log($posturl);
	            // Yii::log(json_encode($data));
	            $res = json_decode(HttpHelper::vpost($posturl,json_encode($data)),true);
	            Yii::log(json_encode($res));
	            // $this->frame['data'] = $res['content'];
	        }
    	}
    }

    public function getAT()
    {
    	// $appid=SiteExt::getAttr('qjpz','appid');
     //    $apps=SiteExt::getAttr('qjpz','apps');
     //    if(!$appid||!$apps) {
     //        return '';
     //    }
     //    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$apps";
     //    $res = HttpHelper::getHttps($url);
     //    if($res&&$res['content']) {
     //        $data = json_decode($res['content'],true);
     //        return $data['access_token'];
     //    }
    	$data = Yii::app()->cache->get('accToken') ? Yii::app()->cache->get('accToken') : (object)array('expire_time'=>0,'data'=>'');
    	$ticket = '';
        if ($data->expire_time < time()) {
            $accessToken = $this->getATNow();
            Yii::log($accessToken);
            if($accessToken) {
            	$data->expire_time = time() + 7000;
                $ticket = $data->data = $accessToken;
            	Yii::app()->cache->set('accToken', $data, 7000);
            }
        } else {
            $ticket = $data->data;
        }
        return $ticket;
    }

    public function getATNow()
    {
    	$appid=SiteExt::getAttr('qjpz','appid');
        $apps=SiteExt::getAttr('qjpz','apps');
        if(!$appid||!$apps) {
            return '';
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$apps";
        $res = HttpHelper::getHttps($url);
        $data = json_decode($res['content'],true);
		return $data['access_token'];
    }

}