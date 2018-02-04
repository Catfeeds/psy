<?php
class ConfigController extends ApiController{
	public function actionIndex()
	{
		$oths = CacheExt::gas('wap_all_config','AreaExt',0,'wap配置缓存',function (){
	            $tmp = [
					'tel'=>ImageTools::fixImage(SiteExt::getAttr('qjpz','tel')),
					'qq'=>SiteExt::getAttr('qjpz','qq'),
					'addr'=>SiteExt::getAttr('qjpz','addr'),
					'boss_name'=>SiteExt::getAttr('qjpz','boss_name'),
					'productnotice'=>SiteExt::getAttr('qjpz','productnotice'),
				];
		            return $tmp;
		        });
		$data = array_merge($oths,['site_name'=>Yii::app()->file->sitename]);
		$this->frame['data'] = $data;
	}
}