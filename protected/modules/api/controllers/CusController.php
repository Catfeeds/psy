<?php
class CusController extends ApiController
{
	public function actionList()
	{
		$datas = $datas['list'] = [];
		$cid = (int)Yii::app()->request->getQuery('cid',0);
		$page = (int)Yii::app()->request->getQuery('page',1);
		$limit = (int)Yii::app()->request->getQuery('limit',20);
		$kw = $this->cleanXss(Yii::app()->request->getQuery('kw',''));
		$criteria = new CDbCriteria;
		$criteria->order = 't.sort desc,t.updated desc';
		$criteria->limit = $limit;
		$criteria->addCondition('t.type=2');
		if($kw) {
			$criteria->addSearchCondition('title',$kw);
		}
		if($cid) {
			$criteria->addCondition("cid=:cid");
			$criteria->params[':cid'] = $cid;
		}
		$ress = ArticleExt::model()->with('cate')->getList($criteria);
		$infos = $ress->data;
		$pager = $ress->pagination;
		if($infos) {
			foreach ($infos as $key => $value) {
				$data['list'][] = [
					'id'=>$value->id,
					'name'=>Tools::u8_title_substr($value->title,20),
					'cate'=>$value->cate->name,
					'date'=>date('m-d',$value->updated),
					// 'price'=>$value->price,
					// 'old_price'=>$value->old_price,
					// 'ts'=>$value->shortdes,
					'image'=>ImageTools::fixImage($value->image,700,360),
				];
			}
		}
		$data['num'] = $pager->itemCount;
		$data['page_count'] = $pager->pageCount;
		$data['page'] = $page;

		$this->frame['data'] = $data;
	}

	public function actionInfo($id)
	{
		$info = ArticleExt::model()->findByPk($id);
		$data = $info->attributes;
		$data['image'] = ImageTools::fixImage($data['image'],700,360);
		$data['created'] = date('Y-m-d',$data['created']);
		$data['updated'] = date('Y-m-d',$data['updated']);
		$this->frame['data'] = $data;
	}
}