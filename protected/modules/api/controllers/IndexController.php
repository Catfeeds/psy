<?php
class IndexController extends ApiController
{
    public function actionConfig()
    {
        // 站点颜色 tab 文字和图案 站点名
        $data = [
            'color'=>Yii::app()->file->color,
            'sitename'=>Yii::app()->file->sitename,
            'phone'=>SiteExt::getAttr('qjpz','tel'),
            // 'sitename'=>Yii::app()->file->sitename,
        ];
        $this->frame['data'] = $data;
    }

    public function actionIndex()
    {
        $data = [];
        $res = ArticleExt::model()->undeleted()->findAll("show_place=1");
        if($res) {
            foreach ($res as $key => $value) {
                $data[] = ['id'=>$value->id,'title'=>$value->title,'desc'=>$value->desc,'image'=>ImageTools::fixImage($value->image)];
            }
        }
        // var_dump($res);exit;
        $this->frame['data'] = $data;
    }

    public function actionDecode()
    {
        include_once "wxBizDataCrypt.php";
        $appid = SiteExt::getAttr('qjpz','appid');
        $sessionKey = $_POST['accessKey'];
        $encryptedData = $_POST['encryptedData'];
        $iv = $_POST['iv'];
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $data = json_decode($data,true);
            $this->frame['data'] = $data['phoneNumber'];
            echo $data['phoneNumber'];
            Yii::app()->end();
            // print($data . "\n");
        } else {
            echo '';
            Yii::app()->end();
        }
    }

    public function actionGetOpenId($code='')
    {
        $appid=SiteExt::getAttr('qjpz','appid');
        $apps=SiteExt::getAttr('qjpz','apps');
        if(!$appid||!$apps) {
            echo json_encode(['open_id'=>'','msg'=>'参数错误']);
            Yii::app()->end();
        }
        // $res = HttpHelper::get("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$apps&js_code=$code&grant_type=authorization_code");
        $res = HttpHelper::getHttps("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$apps&js_code=$code&grant_type=authorization_code");
        if($res){
            $cont = $res['content'];
            if($cont) {
                $cont = json_decode($cont,true);
                $openid = $cont['openid'];
                $data = ['open_id'=>$cont['openid'],'session_key'=>$cont['session_key'],'uid'=>'','is_user'=>0,'phone'=>''];
                if($openid) {
                    $user = UserExt::getUserByOpenId($openid);
                    if($user) {
                        $data['uid'] = $user->id;
                        $data['is_user'] = $user->is_jl;
                        $data['phone'] = $user->phone;
                    }
                    echo json_encode($data);
                }
                Yii::app()->end();
            }
                
        }
    }

    public function actionGetUserTags()
    {
        $this->frame['data'] = Yii::app()->params['edu'];
    }

    public function actionSetUser()
    {
        $data['openid'] = Yii::app()->request->getPost('openid','');
        $data['name'] = Yii::app()->request->getPost('name','');
        $data['sex'] = Yii::app()->request->getPost('sex','');
        $data['year'] = Yii::app()->request->getPost('year','');
        $data['edu'] = Yii::app()->request->getPost('edu','');
        $data['city'] = Yii::app()->request->getPost('city','');
        $data['pro'] = Yii::app()->request->getPost('pro','');
        $data['area'] = Yii::app()->request->getPost('area','');
        $data['street'] = Yii::app()->request->getPost('street','');
        $data['type'] = 1;
        if(!$data['openid']) {
            $this->returnError('参数错误');
        }
        if($user = UserExt::getUserByOpenId($data['openid'])){
            // $this->returnError('该用户已存在');
            $obj = $user;
        } else {
            $obj = new UserExt;
            
        }
        $obj->attributes = $data;
        $obj->is_jl = 1;
        // $obj->area = 
        // if($area = AreaExt::model()->find("name='".$data['pro']."'")) {
        //     $data['area'] = $area->id;
        // } else {
        //     $area = new AreaExt;
        //     $area->name = $data['pro'];
        //     $area->save();
        //     $data['area'] = $area->id;
        // }
        // if($street = AreaExt::model()->find("name='".$data['city']."'")) {
        //     $data['street'] = $street->id;
        // } else {
        //     $street = new AreaExt;
        //     $street->parent = $area->id;
        //     $street->name = $data['city'];
        //     $street->save();
        //     $data['street'] = $street->id;
        // }
        if(!$obj->save()) {
            $this->returnError(current(current($obj->getErrors())));
        } else {
            $this->frame['data'] = $obj->id;
        }

    }

    public function actionSetZxs()
    {
        $data['uid'] = Yii::app()->request->getPost('uid','');
        $data['image'] = Yii::app()->request->getPost('image','');
        $data['id_card'] = Yii::app()->request->getPost('id_card','');
        $data['company'] = Yii::app()->request->getPost('company','');
        $data['work_year'] = Yii::app()->request->getPost('work_year','');
        $data['area'] = Yii::app()->request->getPost('area','');
        $data['street'] = Yii::app()->request->getPost('street','');
        $data['zx_mode'] = Yii::app()->request->getPost('mode','');
        $data['content'] = Yii::app()->request->getPost('content','');
        $data['place'] = Yii::app()->request->getPost('place','');
        $data['ly'] = Yii::app()->request->getPost('ly','');
        $data['zc'] = Yii::app()->request->getPost('zc','');
        $data['mid'] = Yii::app()->request->getPost('zz','');
        $data['edu'] = Yii::app()->request->getPost('edu','');
        $data['price'] = Yii::app()->request->getPost('price','');
        $data['price_note'] = Yii::app()->request->getPost('price_note','');
        $times = Yii::app()->request->getPost('times','');
        $data['type'] = 2;
        if(!$data['uid']) {
            $this->returnError('参数错误');
        }
        if(($user = UserExt::model()->findByPk($data['uid'])) && $user->type==2){
            $this->returnError('该用户已存在');
        } else {
            $obj = $user;
            unset($data['uid']);
            $obj->attributes = $data;
            if(!$obj->save()) {
                $this->returnError(current(current($obj->getErrors())));
            } else {
                if($times) {
                    foreach ($times as $key => $value) {
                        $tm = new UserTimeExt;
                        $tm->uid = $data['uid'];
                        $tm->week = $value['week'];
                        $tm->begin = $value['time_area'];
                        $tm->save();
                    }
                }
            }
        }
    }

    public function actionGetIntro()
    {
        $info = ArticleExt::model()->find(['condition'=>'type=3','order'=>'updated desc']);
        if($info) {
            $this->frame['data'] = $info->attributes;
        }
    }

    public function actionAddOrder()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $data['uid'] = Yii::app()->request->getPost('uid',0);
            $data['pid'] = Yii::app()->request->getPost('pid',0);
            $data['price'] = Yii::app()->request->getPost('price',0);
            $data['begin'] = Yii::app()->request->getPost('begin',0);
            $data['end'] = Yii::app()->request->getPost('end',0);
            if(!$data['uid'] || !$data['pid'] || !$data['end']) {
                return $this->returnError('参数错误');
            }
            $order = new OrderExt;
            $order->attributes = $data;
            if(!$order->save()) {
                $this->returnError(current(current($order->getErrors())));
            } 
        }
    }

    public function actionPriceList($uid='')
    {
        $user = UserExt::model()->findByPk($uid);
        $infos = OrderExt::model()->findAll(['condition'=>"status=1 and pid=$uid",'order'=>'updated desc']);
        $data = [];
        $num = 0;
        if($infos) {
            foreach ($infos as $key => $value) {
                $num += $value->price;
                $data[] = [
                    'name'=>$value->user->name,
                    'time'=>date("Y-m-d H:i:s",$value->updated),
                    'price'=>$value->price,
                ];
            }
        }
        $newdata = ['num'=>$num,'list'=>$data];
        $this->frame['data'] = $newdata;
    }

    public function actionUserList($uid='')
    {
        $user = UserExt::model()->findByPk($uid);
        $infos = OrderExt::model()->findAll(['condition'=>"pid=$uid",'order'=>'updated desc']);
        $data = [];
        // $num = 0;
        if($infos) {
            foreach ($infos as $key => $value) {
                $iuser = $value->user;
                // $num += $value->price;
                $data[] = [
                    'name'=>$iuser->name,
                    'phone'=>$iuser->phone,
                    // 'price'=>$value->price,
                    'begin'=>date('m-d H:i',$value->begin),
                    'end'=>date('m-d H:i',$value->end),
                ];
            }
        }
        // $newdata = ['num'=>$num,'list'=>$data];
        $this->frame['data'] = $data;
    }

    public function actionSetGrade()
    {
        $data['uid'] = Yii::app()->request->getPost('uid',0);
        $data['oid'] = Yii::app()->request->getPost('oid',0);
        $data['num'] = Yii::app()->request->getPost('num','');
        $data['note'] = Yii::app()->request->getPost('note','');
        $data['is_nm'] = Yii::app()->request->getPost('is_nm','');
        if(!$data['uid'] || !$data['uid'] || !$data['num']) {
            return $this->returnError('参数错误');
        }
        $order = new GradeExt;
        $order->attributes = $data;
        if(!$order->save()) {
            $this->returnError(current(current($order->getErrors())));
        } 
    }

    public function actionGetGrade($uid,$oid)
    {
        if($obj = GradeExt::model()->find("uid=$uid and oid=$oid")) {
            $this->frame['data'] = ['num'=>$obj->num,'gradeName'=>$value->buser->name,'note'=>$value->note];
        } else {
            return $this->returnError('尚未评价');
        }
    }

    public function actionOrderList($uid='')
    {
        $user = UserExt::model()->findByPk($uid);
        $infos = OrderExt::model()->findAll(['condition'=>"uid=$uid",'order'=>'updated desc']);
        $data = [];
        // $num = 0;
        if($infos) {
            foreach ($infos as $key => $value) {
                $iuser = $value->product;
                $tags = [];
                $iuser['zx_mode']==0 && $tags[] = '可线下咨询';
                if($iuser['ly']) {
                    $tags[] = TagExt::model()->findByPk($iuser['ly'])->name;
                }
                if($iuser['zc']) {
                    $tags[] = TagExt::model()->findByPk($iuser['zc'])->name;
                }
                // $num += $value->price;
                $data[] = [
                    'id'=>$value->id,
                    'name'=>$iuser->name,
                    'image'=>ImageTools::fixImage($iuser->image),
                    'phone'=>$iuser->phone,
                    'tags'=>$tags,
                    'price'=>$value->price,
                    'status'=>OrderExt::$status[$value->status],
                    'day'=>date('Y-m-d',$value->begin),
                    'begin'=>date('H',$value->begin),
                    'end'=>date('H',$value->end),
                ];
            }
        }
        // $newdata = ['num'=>$num,'list'=>$data];
        $this->frame['data'] = $data;
    }

    public function actionOrderInfo($id='')
    {
        $value = OrderExt::model()->findByPk($id);
        $iuser = $value->product;
        $tags = [];
        $iuser['zx_mode']==0 && $tags[] = '可线下咨询';
        if($iuser['ly']) {
            $tags[] = TagExt::model()->findByPk($iuser['ly'])->name;
        }
        if($iuser['zc']) {
            $tags[] = TagExt::model()->findByPk($iuser['zc'])->name;
        }
        // $num += $value->price;
        $data = [
            'id'=>$id,
            'name'=>$iuser->name,
            'image'=>ImageTools::fixImage($iuser->image),
            'phone'=>$iuser->phone,
            'tags'=>$tags,
            'price'=>$value->price,
            'status'=>OrderExt::$status[$value->status],
            'day'=>date('Y-m-d',$value->begin),
            'begin'=>date('H',$value->begin),
            'end'=>date('H',$value->end),
        ];
        $this->frame['data'] = $data;
    }

    public function actionCheckOrder($id='')
    {
        $value = OrderExt::model()->findByPk($id);
        $value->status=1;
        $value->save();
    }

    public function actionGetTime($uid='')
    {
        $user = UserExt::model()->findByPk($uid);
        $data = [];
        $weekarray=array("一","二","三","四","五","六","日");
       

        if($times = $user->times) {
            $weekarr = [];
            foreach ($times as $key => $value) {
                $weekarr[$value['week']][] = $value['begin'];
            }
            // $weekarr = array_unique($weekarr);
             // 往后一周的日期
            foreach (range(1, 7) as $key => $value) {
                $daytime = time() + $value*86400;
                $week = date('w',$daytime);
                // 星期日
                if($week==0) {
                    $week = 7;
                }
                if(in_array($week, array_keys($weekarr))) {
                    $tmp['day'] = date('m/d',$daytime);
                    $tmp['week'] = '周'.$weekarray[$week];
                    $timearrange = $weekarr[$week];
                    $canusertime = [];
                    foreach ($timearrange as $timearea) {
                        $paramstime = Yii::app()->params['time_area'][$timearea];
                        list($begintime,$endtime) = explode('-', $paramstime);
                        foreach (range($begintime,$endtime) as $t) {
                            $canusertime[] = $t;
                        }
                    }
                    foreach (range(0, 24) as $t) {
                        if(in_array($t, $canusertime)) {
                            $list[] = ['time'=>$t,'can_use'=>1];
                        } else {
                            $list[] = ['time'=>$t,'can_use'=>0];
                        }
                    }
                    $tmp['list'] = $list;
                    unset($list);
                    $data[] = $tmp;
                }

            }
        }
        $this->frame['data'] = ['price'=>$user->price,'list'=>$data];
    }

    public function actionSetPay($openid='',$price='',$body='预约支付')
    {
        $res = Yii::app()->wxPay->setPay($body,$price,$openid);
        // var_dump($res);exit;
        if($res) {
            $this->frame['data'] = $res;
        }
    }

    public function actionXcxLogin()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $phone = Yii::app()->request->getPost('phone','');
            $openid = Yii::app()->request->getPost('openid','');
            $name = Yii::app()->request->getPost('name','');
            if(!$phone||!$openid) {
                $this->returnError('参数错误');
                return false;
            }
            if($phone) {
                $user = UserExt::model()->find("phone='$phone'");
            } elseif($openid) {
                $user = UserExt::model()->find("openid='$openid'");
            }
        // $phone = '13861242596';
            if($user) {
                if($openid&&$user->openid!=$openid){
                    $user->openid=$openid;
                    $user->save();
                }
                
            } else {
                $user = new UserExt;
                $user->phone = $phone;
                $user->openid = $openid;
                $user->name = $name?$name:$this->get_rand_str();
                $user->status = 1;
                // $user->is_true = 0;
                $user->type = 1;
                $user->pwd = md5('123456');
                $user->save();

                // $this->returnError('用户尚未登录');
            }
            $model = new ApiLoginForm();
            $model->isapp = true;
            $model->username = $user->phone;
            $model->password = $user->pwd;
            // $model->obj = $user->attributes
            $model->login();
            $this->staff = $user;
            $data = [
                'id'=>$this->staff->id,
                'phone'=>$this->staff->phone,
                'name'=>$this->staff->name,
                'type'=>$this->staff->type,
                // 'is_true'=>$this->staff->is_true,
                // 'company_name'=>$this->staff->is_true==1?($this->staff->companyinfo?$this->staff->companyinfo->name:'独立经纪人'):'您尚未实名认证',
            ];
            $this->frame['data'] = $data;
        }
    }
}
