<?php
class ConfigController extends ApiController{
	public function actionIndex()
	{
		$data = [];
		// $oths = CacheExt::gas('wap_all_config','AreaExt',0,'wap配置缓存',function (){
	 //            $tmp = [
		// 			'tel'=>ImageTools::fixImage(SiteExt::getAttr('qjpz','tel')),
		// 			'qq'=>SiteExt::getAttr('qjpz','qq'),
		// 			'addr'=>SiteExt::getAttr('qjpz','addr'),
		// 			'boss_name'=>SiteExt::getAttr('qjpz','boss_name'),
		// 			'productnotice'=>SiteExt::getAttr('qjpz','productnotice'),
		// 		];
		//             return $tmp;
		//         });
		// $data = array_merge($oths,['site_name'=>Yii::app()->file->sitename]);
		$data['isUser'] = false;
		if($openid = Yii::app()->request->getQuery('openid',0)) {
			$user = UserExt::model()->find("openid='$openid'");
			if($user) {
				$data['isUser'] = true;
				$data['type'] = $user->type;
			}
			
		}
		$this->frame['data'] = $data;
	}
}