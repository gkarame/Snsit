<?php
Yii::import("xupload.models.XUploadForm");
class DocumentsController extends Controller
{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array(
			'accessControl', 
			'postOnly + delete', 
		);
	}
	public function init()
	{
		parent::init();
	}
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array(
						'index','view', 'create','update', 'delete', 'upload',
						'getDocuments'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}	
	public function actions()
	{
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.XUploadSessionAction',
				 'path' =>Yii::app() -> getBasePath() . "/../uploads/tmp/",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp/",
		 		 'stateVariable' => 'documents'
			 ),
		 );
	}	
	public function actionCreate($id_model, $tname, $action, $category)
	{
		$model = new Documents;	$model->id_model = (int) $id_model;	$model->model_table = $tname;
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					"/documents/create?id_model=$id_model&tname=$tname&action=$action&category=$category" => array(
							'label'=>Yii::t('translations','New Document'),
							'url' => array('documents/create', 'id_model'=>$id_model, 'tname' => $tname, 'action' => $action , 'category' => $category),
							'itemOptions'=>array('class'=>'link'),
							'subtab' => '',
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		if (isset($_POST['Documents']))
		{
			$model->attributes = $_POST['Documents'];
			if ($model->save()) 
			{  
				$id=$id_model;	Yii::app()->db->createCommand("DELETE FROM projects_alerts WHERE id_project=".$id."")->execute();
				$projects =   Yii::app()->db->createCommand()
					->select('*')
					->from('projects')
					->where('id='.$id.' and (id_type = 26 || id_type = 27) && status='.Projects::STATUS_ACTIVE.' && (deactivate_alerts IS NULL OR deactivate_alerts <> "Yes")')
					->queryAll();
				$alerts =   Yii::app()->db->createCommand()
					->select('*')
					->from('alerts')
					->queryAll();	$alertsData = array();
				foreach ($projects as $key => $project)
				{
					$projectAlerts = array();
					foreach ($alerts as $alert)
					{
						if ($alert['id_category'] == $project['id_type'])
						{
							$name = 'test'.$alert['id'];
							if (method_exists('ProjectsAlerts', $name)) {	$projectAlerts[] = ProjectsAlerts::$name($project);	}
						}
					}
					$projectAlerts = array_filter($projectAlerts);	$projectAlerts = implode(',', $projectAlerts);
					$alertsData[] = "(".$project['id'].',"'.$projectAlerts."\", NOW())";
				}
				if (!empty($alertsData))
				{
					Yii::app()->db->createCommand("INSERT INTO  projects_alerts(`id_project`, `alerts`, `date`) VALUES ".implode(',', $alertsData))->execute();
				}
				Utils::closeTab(Yii::app()->createUrl('documents/create', array('id_model' => $id_model, 'tname' => $tname, 'action' => $action,'category'=>$category)),true);
				$this->action_menu = Yii::app()->session['menu'];
				$this->redirect(array($model->model_table.'/'.$action, 'id'=>$model->id_model, 'active' => $model->id_category));
			}
		}
		$this->render('manage', array('model' => $model, 'module_category' => $category));
	}
	public function actionUpdate($id, $action)
	{
		$model = $this->loadModel($id);
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/documents/update/'.$model->id => array(
						'label'=> $model->document_title,
						'url' => array('documents/update', 'id'=>$model->id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => '',
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		if (isset($_POST['Documents']))
		{
			$model->attributes = $_POST['Documents'];
			if ($model->getFile(true) == NULL) {	$model->file = '';	}			
			if ($model->save()) {
				Utils::closeTab(Yii::app()->createUrl('documents/update', array('id' => $model->id)));
				$this->redirect(array($model->model_table.'/'.$action, 'id'=>$model->id_model, 'active' => $model->id_category));
			}
		}		
		$this->render('manage', array('model' => $model));
	}
	public function actionDelete()
	{
		if (isset($_POST['docs']) && is_array($_POST['docs'])){
			$ct = 0;
			foreach ($_POST['docs'] as $id)
			{
				$model = $this->loadModel($id);
				$filePath = dirname(Yii::app()->request->scriptFile).'/uploads/'.$model->model_table.'/'.$model->id_model.'/documents/'.$model->file;
				Utils::deleteFile($filePath);
				if ($model->delete())
					$ct++;
			}
			echo json_encode(array('status'=>'success', 'count' => $ct));
		}
		Yii::app()->end();
	}	
	public function actionGetDocuments($id, $id_model, $model_table, $action)
	{
		if (Yii::app()->request->isAjaxRequest)
		{
			$module_category = DocumentsCategories::model()->findByPk($id);
			$condition = 'id_category IN ('. implode(',', DocumentsCategories::getSubCategories($model_table, $id)).') AND id_model = :model AND model_table = :table';
			$params = array(
				':model' => (int) $id_model,
				':table' => $model_table
			);
			$criteria = new CDbCriteria;	$criteria->condition = $condition;	$criteria->params = $params;
			$documents = new CActiveDataProvider('Documents', array(
					'criteria' => $criteria,
					'pagination'=>array(
							'pageSize' => Utils::getPageSize(),
					),
					'sort'=>array(
							'defaultOrder'=>'uploaded DESC',
					),
			));
			if (!isset($_GET['ajax'])) {
				Yii::app()->clientScript->scriptMap=array(
				'jquery.js'=>false,
				'jquery.min.js' => false,
				);
				echo json_encode(
					array(
						'status' => 'success', 
						'list' => $this->renderPartial('_list', array('documents' => $documents, 'action' => $action), true, true)
					)
				);
			} else {
				$this->render('_list', array('documents' => $documents, 'action' => $action));
			}
		}
		Yii::app()->end();
	}
	public function actionIndex()
	{
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/documents/index' => array(
					'label'=>Yii::t('translations','Documents'),
					'url' => array('documents/index'),
					'itemOptions'=>array('class'=>'link'),
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$this->render('index');
	}
	public function loadModel($id)
	{
		$model = Documents::model()->with('category')->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}	
}?>