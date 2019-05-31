<?php
Yii::import("xupload.models.XUploadForm");
class FullsupportController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl',
			'postOnly + delete + deleteContact',
		);
	}
	public function init()	{
		parent::init();
	}
	public function accessRules(){
		return array(
			array('allow', 
				'actions'=>array(
						'index','ManageAfterItem','CreateAfterItem','deleteAfterItem','CreateSundayItem','ManageSundayItem','deleteSundayItem'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin ',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}	
	public function actions(){
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.CustomXUploadAction',
				 'path' =>Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'customers_conn'
			 ),
		 );
	}	
	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('customers-list')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$searchArray = isset($_GET['fullsupport']) ? $_GET['fullsupport'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/fullsupport/index' => array(
						'label'=>Yii::t('translations', 'Support Schedule'),
						'url' => array('fullsupport/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new FullSupport('search');
		$model->unsetAttributes();  
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionCreateAfterItem(){
		$model = new FullSupport();
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
			$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
			$model->to_date = date('Y/m/d',strtotime($_POST['FullSupport']['to_date']));
			$model->type='402';					
   			if ($model->save()){	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_after', array(
	            	'model'=> $model  	
					), true, true)));
	        	exit;	
			}
   		}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_after', array(
            	'model'=> $model 
				), true, true)));
        exit;
	}	
	public function actionCreateSundayItem(){
		$model = new FullSupport();
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
			$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
			$model->type='403';					
   			if ($model->save()){	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_sunday', array(
	            	'model'=> $model  	
					), true, true)));
	        	exit;	
			}
   		}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_sunday', array(
            	'model'=> $model 
				), true, true)));
        exit;
	}	
	public function actionManageAfterItem($id){
		$model = FullSupport::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
   			$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
					$model->to_date = date('Y/m/d',strtotime($_POST['FullSupport']['to_date']));
					$model->type='402';
   			if ($model->save()){
					$model->save();					
					echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_after', array(
		            	'model'=> $model
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_after', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}	
	public function actionManageSundayItem($id){
		$model = FullSupport::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
   			$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
			$model->type='403';
   			if ($model->save()){
					$model->save();					
					echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_sunday', array(
		            	'model'=> $model
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_sunday', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}	
	public function actionDeleteAfterItem($id){
		$model = FullSupport::model()->findByPk($id);
		if ($model===null)
			echo json_encode(array('status'=>'error', 'error' => '404'));	
		if ($model->delete()) 
		{
						echo json_encode(array('status'=>'saved'));	
		} 
	}
public function actionDeleteSundayItem($id)	{
		$model = FullSupport::model()->findByPk($id);
		if ($model===null)
			echo json_encode(array('status'=>'error', 'error' => '404'));	
		if ($model->delete()) {
						echo json_encode(array('status'=>'saved'));	
		} 
	}		
}?>