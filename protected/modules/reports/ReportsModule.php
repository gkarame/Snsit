<?php

class ReportsModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'reports.models.*',
			'reports.components.*',
		));

        /*
        * Author: Mike
        * Date: 25.06.19
        * Fix 404 error when link from module
        */
		$url = explode('/',Yii::app()->request->getUrl());
		$controller = Yii::app()->createController($url[2]);
		if (isset($controller)){
            Yii::app()->request->redirect(str_ireplace('/reports','',Yii::app()->request->getUrl()));
        }
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
