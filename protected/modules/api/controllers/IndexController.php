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
                $data = ['open_id'=>$cont['openid'],'session_key'=>$cont['session_key'],'uid'=>''];
                if($openid) {
                    $user = UserExt::getUserByOpenId($openid);
                    if($user) {
                        $data['uid'] = $user->id;
                    }
                    echo json_encode($data);
                }
                Yii::app()->end();
            }
                
        }
    }

    public function actionSetUser()
    {
        $data['openid'] = Yii::app()->request->getPost('openid','');
        $data['name'] = Yii::app()->request->getPost('name','');
        $data['sex'] = Yii::app()->request->getPost('sex','');
        $data['pro'] = Yii::app()->request->getPost('pro','');
        $data['city'] = Yii::app()->request->getPost('city','');
        if(!$data['openid']) {
            $this->returnError('参数错误');
        }
        if($user = UserExt::getUserByOpenId($data['openid'])){
            $this->returnError('该用户已存在');
        } else {
            $obj = new UserExt;
            $obj->attributes = $data;
            if(!$obj->save()) {
                $this->returnError(current(current($obj->getErrors())));
            } else {
                $this->frame['data'] = $obj->id;
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
}
