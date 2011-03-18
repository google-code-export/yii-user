<?php

class LogoutController extends Controller
{
	public $defaultAction = 'logout';

	/**
	* Logout the current user and redirect to returnLogoutUrl.
	*/
	public function actionLogout($redirect = null)
	{
		Yii::app()->user->logout();
		$this->redirect($redirect? urldecode($redirect) : Yii::app()->controller->module->returnLogoutUrl);
	}

}