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
                $data = ['open_id'=>$cont['openid'],'session_key'=>$cont['session_key'],'uid'=>'','is_user'=>0,'phone'=>'','is_zxs'=>0];
                if($openid) {
                    $user = UserExt::getUserByOpenId($openid);
                    if($user) {
                        $data['uid'] = $user->id;
                        $data['is_user'] = $user->is_jl;
                        $data['phone'] = $user->phone;
                        $user->type==2 && $data['is_zxs'] = 1;
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
        $lys = Yii::app()->request->getPost('ly','');
        $data['zc'] = Yii::app()->request->getPost('zc','');
        $data['mid'] = Yii::app()->request->getPost('zz','');
        $data['edu'] = Yii::app()->request->getPost('edu','');
        $data['price'] = Yii::app()->request->getPost('price','');
        $data['wx'] = Yii::app()->request->getPost('wx','');
        $data['sex'] = Yii::app()->request->getPost('sex','1');
        $data['is_edit'] = Yii::app()->request->getPost('is_edit','0');
        $data['id_pic_main'] = Yii::app()->request->getPost('id_pic_main','');
        $data['id_pic_sec'] = Yii::app()->request->getPost('id_pic_sec','');
        $data['price_note'] = Yii::app()->request->getPost('price_note','');
        $times = Yii::app()->request->getPost('times','');
        $data['type'] = 2;
        if(!$data['uid']) {
            $this->returnError('参数错误');
        }
        if(!$data['is_edit'] && ($user = UserExt::model()->findByPk($data['uid'])) && $user->type==2){
            $this->returnError('该用户已存在');
        } else {
            $user = UserExt::model()->findByPk($data['uid']);
            $obj = $user;
            unset($data['uid']);
            $obj->attributes = $data;
            // $data['is_edit'] && $obj->zxs_status = 0;
            if(!$obj->save()) {
                $this->returnError(current(current($obj->getErrors())));
            } else {
                // if($times = json_decode($times,true)) {
                //     // var_dump(count($times));
                //     foreach ($times as $key => $value) {
                //         $tm = new UserTimeExt;
                //         $tm->uid = $obj->id;
                //         $tm->week = $value['week'];
                //         $tm->begin = $value['time_area'];
                //         if(!$tm->save()) {
                //             return $this->returnError(current(current($tm->getErrors())));
                //         }
                //     }
                // }
                UserTagExt::model()->deleteAllByAttributes(['uid'=>$obj->id]);
                if($lys = explode(',', $lys)) {
                    // var_dump($lys);exit;
                    foreach ($lys as $key => $value) {
                        $tm = new UserTagExt;
                        $tm->uid = $obj->id;
                        $tm->tid = $value;
                        if(!$tm->save()) {
                            return $this->returnError(current(current($tm->getErrors())));
                        }
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
            $form_id = Yii::app()->request->getPost('form_id',0);
            $data['onoroff'] = Yii::app()->request->getPost('onoroff',0);
            if(!$data['uid'] || !$data['pid'] || !$data['end']) {
                return $this->returnError('参数错误');
            }
            $order = new OrderExt;
            $order->attributes = $data;
            if(!$order->save()) {
                $this->returnError(current(current($order->getErrors())));
            } else {
                $userit = UserExt::model()->findByPk($data['pid']);
                $userut = UserExt::model()->findByPk($data['uid']);
                $res = SmsExt::sendMsg('支付成功',$userit->phone,['name'=>$userut->name,'time'=>$data['begin']]);
                Yii::log(json_encode($res));
               
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
                    'id'=>$value->id,
                    'oid'=>$iuser->id,
                    'name'=>$iuser->name,
                    'phone'=>$iuser->phone,
                    'status'=>GradeExt::model()->find("uid=$uid and oid=".$iuser->id)?'已评分':'',
                    'onoroff'=>$value->onoroff==1?'线上咨询':'线下咨询',
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
        $data['order_id'] = Yii::app()->request->getPost('order_id',0);
        $data['num'] = Yii::app()->request->getPost('num','');
        $data['note'] = Yii::app()->request->getPost('note','');
        $data['is_nm'] = Yii::app()->request->getPost('is_nm','');
        if(!$data['uid'] || !$data['oid'] || !is_numeric($data['num'])) {
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
                if(!$iuser)
                    continue;
                // var_dump($iuser['id']);exit;
                $tags = [];
                $iuser['zx_mode']==0 && $tags[] = '可线下咨询';
                if($iuser['ly']) {
                    $tags[] = TagExt::model()->findByPk($iuser['ly'])->name;
                }
                if($iuser['zc']) {
                    $tags[] = TagExt::model()->findByPk($iuser['zc'])->name;
                }
                 // var_dump($iuser['id']);exit;
                // $num += $value->price;
                $pf = false;
                if(GradeExt::model()->find("uid=$uid and oid=".$iuser['id']." and order_id=".$value->id)) {
                    $pf = true;
                }
                $data[] = [
                    'id'=>$value->id,
                    'name'=>$iuser->name,
                    'oid'=>$iuser->id,
                    'image'=>ImageTools::fixImage($iuser->image),
                    'phone'=>$iuser->phone,
                    'tags'=>$tags,
                    'price'=>$value->price,
                    'status'=>!$pf?OrderExt::$status[$value->status]:'已评分',
                    'day'=>date('Y-m-d',$value->begin),
                    'begin'=>date('H',$value->begin),
                    'end'=>date('H',$value->end),
                    'onoroff'=>$value->onoroff==1?'线上咨询':'线下咨询',
                ];
            }
        }
        // $newdata = ['num'=>$num,'list'=>$data];
        $this->frame['data'] = $data;
    }

    public function actionOrderInfo($id='',$uid='',$oid='')
    {
        $value = OrderExt::model()->findByPk($id);
        $iuser = UserExt::model()->findByPk($oid);
        $tags = [];
        $iuser['zx_mode']==0 && $tags[] = '可线下咨询';
        if($iuser['ly']) {
            $tags[] = TagExt::model()->findByPk($iuser['ly'])->name;
        }
        if($iuser['zc']) {
            $tags[] = TagExt::model()->findByPk($iuser['zc'])->name;
        }
        $gr = GradeExt::model()->find("oid=".$oid." and uid=".$uid." and order_id=$id");
        // var_dump("oid=".$iuser->id." and uid=".$value->uid);exit;
        // $num += $value->price;
        $data = [
            'id'=>$id,
            'name'=>$iuser->name,
            'oid'=>$iuser->id,
            'image'=>ImageTools::fixImage($iuser->image),
            'phone'=>$iuser->phone,
            'note'=>isset($gr->note)?$gr->note:'',
            'num'=>$gr?$gr->num:'',
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
        $outarr = [];
        if($orders = OrderExt::model()->findAll("pid=$uid and status=0")) {
            
            foreach ($orders as $key => $value) {
                $tmp2 = [
                    'day'=>date('m/d',$value->begin),
                    'begin'=>date('H',$value->begin),
                    'end'=>date('H',$value->end),
                ];
                $outarr[] = $tmp2;
                // var_dump($value->id,$tmp);exit;
            }
        }
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
                    // var_dump($week);exit;
                    $tmp['day'] = date('m/d',$daytime);
                    $tmp['week'] = '周'.$weekarray[$week-1];
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
        // var_dump($outarr);exit();
        if($outarr && $data) {
            foreach ($outarr as $o) {
                foreach ($data as $k=> $d) {
                    if($o['day'] == $d['day']) {
                        $lst = range($o['begin'], $o['end']);
                        foreach ($d['list'] as $k1=> $l) {
                            if(in_array($l['time'], $lst)) {
                                $data[$k]['list'][$k1]['can_use'] = 0;
                            }
                        }
                    }
                }
            }
        }
            
        $this->frame['data'] = ['price'=>$user->price,'list'=>$data,'place'=>$user->place,'off_price'=>$user->off_price,'can_edit'=>$user->zx_mode?"0":"1"];
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

    public function actionAddReport()
    {
        $obj = new ReportExt;
        $uid = Yii::app()->request->getPost('uid','');
        $note = Yii::app()->request->getPost('note','');
        $obj->uid = $uid;
        $obj->reason = $note;
        $obj->save();
    }

    public function actionGetConfig()
    {
        $data = [];
        $data['phone'] = SiteExt::getAttr('qjpz','tel');
        $this->frame['data'] = $data;
    }

    public function actionGetUserInfo($uid)
    {
        $user = UserExt::model()->findByPk($uid);
        $data = [];
        foreach (['name','image','id_card','company','work_year','area','street','zx_mode','content','place','mid','edu','sex','id_pic_main','id_pic_sec','wx'] as $key => $value) {
            $data[$value] = $user->$value;
        }
        // $data['zz'] = $data['mid'];
        !$data['zx_mode'] && $data['zx_mode'] = 1;
        $data['image'] && $data['image'] = ImageTools::fixImage($data['image']);
        $data['id_pic_main'] && $data['id_pic_main'] = ImageTools::fixImage($data['id_pic_main']);
        $data['id_pic_sec'] && $data['id_pic_sec'] = ImageTools::fixImage($data['id_pic_sec']);
        $data['ly'] = [];
        if($tags = $user->tags) {
            foreach ($tags as $key => $value) {
                $data['ly'][] = $value['tid'];
            }
        }
        $this->frame['data'] = $data;

    }

    public function actionGetContact($uid='')
    {
        $user = UserExt::model()->findByPk($uid);
        if($user) {
            $this->frame['data'] = [
                'name'=>$user->name,'phone'=>$user->phone,'wx'=>$user->wx
            ];
        }
    }

    public function actionCheckCanIn($uid='')
    {
        $user = UserExt::model()->findByPk($uid);
        if($user) {
            if($user->type==2) {
                $this->returnError('您已是平台认证咨询师！');
            }
        }
    }

    public function actionGetZxsTime($uid)
    {
        $user = UserExt::model()->findByPk($uid);
        $data = [];
        if($times = $user->times) {
            // var_dump(1);exit;
            foreach ($times as $key => $value) {
                $tmp['week'] = $value['week']-1;
                $tmp['time_area'] = $value['begin']-1;
                // $tmp['end'] = $value['end'];
                $data[] = $tmp;
                // unset($tmp);
            }
        }
        $this->frame['data'] = $data;
    }

    public function actionSetZxsTime()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $uid = Yii::app()->request->getPost('uid',0);
            if(!$uid) {
                return $this->returnError('参数错误');
            }
            $times = Yii::app()->request->getPost('times','');
            UserTimeExt::model()->deleteAllByAttributes(['uid'=>$uid]);
            if($times = json_decode($times,true)) {
                // var_dump(count($times));
                foreach ($times as $key => $value) {
                    $tm = new UserTimeExt;
                    $tm->uid = $uid;
                    $tm->week = $value['week']+1;
                    $tm->begin = $value['time_area']+1;
                    if(!$tm->save()) {
                        return $this->returnError(current(current($tm->getErrors())));
                    }
                }
            }
        }
    }

    public function actionGetZxsPrice($uid)
    {
        $user = UserExt::model()->findByPk($uid);
        $this->frame['data'] = ['price'=>$user->price,'off_price'=>$user->off_price];
    }

    public function actionSetZxsPrice()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $uid = Yii::app()->request->getPost('uid',0);
            if(!$uid) {
                return $this->returnError('参数错误');
            } else {
                $user = UserExt::model()->findByPk($uid);
            }
            $price = Yii::app()->request->getPost('price','');
            $user->price = $price;
            $user->off_price = Yii::app()->request->getPost('off_price','');
            $user->save();
        }
    }

    public function sendMsg($form_id='',$openid='',$k1='',$k2='',$k3='',$k4='')
    {
        if($token = $this->getAt()) {
            // $openid = SiteExt::getAttr('qjpz','openid');
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
                $data['data']['keyword1']['value'] = $k1;
                $data['data']['keyword2']['value'] = $k2;
                $data['data']['keyword3']['value'] = $k3;
                $data['data']['keyword4']['value'] = $k4;
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

    public function actionPay()
    {
        
    }

}
