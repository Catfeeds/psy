<?php
class DataController extends ApiController
{
	public function actionIndex()
	{
		$user = new UserExt;
		$user->name = 'zt';
		$user->pwd = md5('123456');
		$user->save();
	}
}