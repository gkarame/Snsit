<?php
class GroupsController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + deleteUser',
		
		);
	}
	public function init(){
		parent::init();
	}
	public function accessRules(){
		return array(
			array('allow', 
				'actions'=>array(
						'index','view','create','update', 'savePermissions', 
						'getUnssignedUsers', 'assignUsers','moveUser', 'deleteUser',
						'getOtherGroups', 'saveEmailNotifications', 'saveDefaultTasks', 'getDefaultTasksTabs','changeDefaultTasks',
						'DeleteDefaultTasks'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny', 
				'users'=>array('*'),
			),
		);
	}
	public function actionView($id){
		$model = $this->loadModel($id);
		$arr = Utils::getShortText($model->name);
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu, 
			array(
				'/groups/view/'.$id => array(
					'label'=>$arr['text'],
					'url' => array('groups/view', 'id'=>$id),
					'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->name : ''),
					'subtab' =>  $this->getSubTab(),
					'order' => Utils::getMenuOrder()+1
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = $this->getSubTab();		
		$this->render('view',array(
			'model'=>$model,
		));
	}
	public function actionCreate(){
		$model=new Groups;
		if (isset($_POST['Groups'])){
			$model->attributes=$_POST['Groups'];
			if ($model->save()) {
				echo json_encode(array('status'=>'success'));	
			}else{
				echo json_encode(array('status'=>'failure', 'errors'=>$model->getErrors()));
			}
		}
		Yii::app()->end();
	}
	public function actionUpdate($id){
		$model=$this->loadModel($id);
		$arr = Utils::getShortText($model->name);
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/groups/update/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('groups/update', 'id'=>$id),
							'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->name : ''),
							'subtab' =>  $this->getSubTab(),
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = $this->getSubTab();		
		if(isset($_POST['Groups'])){
			$model->attributes=$_POST['Groups'];
			if ($model->save()){
				Utils::closeTab(Yii::app()->request->url);
				$this->redirect(array(Utils::getMenuOrder(true)));
			}
		}
		$this->render('update',array(
			'model'=>$model,
		));
	}
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('groups-list')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/groups/index' => array(
					'label'=>Yii::t('translations','Groups'),
					'url' => array('groups/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;			
		$model=new Groups('search');
		$model->unsetAttributes();  
		if(isset($_GET['Groups']))
			$model->attributes=$_GET['Groups'];
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionSavePermissions() {
		if(!isset($_POST['id'])) {
			exit;
		}
		Permissions::save($_POST);
		echo CJSON::encode(array('status'=>'success'));
		exit;
	}
	public function actionGetUnssignedUsers(){
		if (!isset($_POST['id']))	{
			exit;
		}
		$users = Users::getUnassignedUsers((int)$_POST['id']);	
		echo CJSON::encode(array(
				'status'=>'success',
				'div'=>$this->renderPartial('_unassigned_users', array('users'=>$users), true)));
		exit;
	}
	public function actionAssignUsers($id)	{
		if (empty($_POST)){
			exit;
		}
		$ids = $_POST['checked'];	$nr = 0;
		foreach($ids as $uid) {
			$ug = new UserGroups;	$ug->id_group 	= $id;	$ug->id_user 	= $uid;
			if ($ug->save()){
				$nr++;
			}
		}
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been assigned!',
			));
		exit;
	}
	public function actionDeleteUser($id){
		if (Yii::app()->request->isAjaxRequest) {
			UserGroups::model()->deleteByPk($id);
			exit;
		}
	}
	public function actionGetOtherGroups($id){
		$id_usergroup = (int)$_POST['id_usergroup'];
		$criteria = new CDbCriteria();
		$criteria->condition = 'id <> :id';
		$criteria->params = array(':id' => $id);
		$groups = Groups::model()->findAll($criteria);
		echo CJSON::encode(array(
				'status'=>'success',
				'div'=>$this->renderPartial('_groups_list', array('groups'=>$groups, 'id_usergroup'=>$id_usergroup), true))
		);
		exit;
	}	
	public function actionMoveUser(){
		if (!isset($_POST['new_group'], $_POST['id_usergroup'])){
			exit;
		}
		$ug = UserGroups::model()->findByPk((int)$_POST['id_usergroup']);
		$ug->id_group = (int)$_POST['new_group'];
		if (!$ug->save()) {
			$ug->delete();	
		}
		echo CJSON::encode(array(
					'status'=>'success',
		));
		exit;	
	}
	public function getEmailNotificationTabs() {
		$notifs = EmailNotifications::model()->findAll('not_in_groups=0 order by name');
		$id_group = Yii::app()->controller->actionParams['id']; 
		Yii::app()->controller->renderPartial('_email_notifications_tab', array('notifs'=>$notifs, 'id_group'=>$id_group));
	}
	public function getDefaultTasksTabs(){
		$tasks = DefaultTasks::getTasksGroupedByParent();
		$id_group = Yii::app()->controller->actionParams['id'];
		Yii::app()->controller->renderPartial('_default_tasks_tab', array('tasks'=>$tasks, 'id_group'=>$id_group));
	}
	public function actionSaveEmailNotifications(){
		if (!isset($_POST['verify']))
			exit;
		$id_group = Yii::app()->controller->actionParams['id']; 
		EmailNotificationsGroups::model()->deleteAll('id_group = :id_group', array(':id_group'=>$id_group));
		if (isset($_POST['checked'])){
			foreach ($_POST['checked'] as $check){
				$unique_name = EmailNotifications::getUniqueNameByNotificationId($check);
				if ($unique_name == 'support_desk_general_permission') {
					$ids = EmailNotifications::getIdsByModule('support_desk');
					foreach ($ids as $id){
						$model = new EmailNotificationsGroups;
						$model->id_group = $id_group;
						$model->id_email_notification = $id['id'];
						$model->save();
					}					
				} else {
					$model = new EmailNotificationsGroups;
					$model->id_group = $id_group;
					$model->id_email_notification = $check;
					$model->save();
				}		
			}
		}
		echo CJSON::encode(array('status'=>'success'));
		exit;
	}
	public function actionSaveDefaultTasks(){
		$array_default_taskss = array();	$array_tasks = array();	$users_id = "";	$values_tasks ="";
		$unu = 1;	$to_delete_ar = array();
		if (!isset($_POST['verify']))
			exit;
		$id_group = Yii::app()->controller->actionParams['id'];		
		$tasks = Yii::app()->db->createCommand("SELECT id_default_task from default_tasks_group where id_group = '{$id_group}'")->queryAll();		
		foreach($tasks as $task)
			$array_default_taskss[] = $task['id_default_task'] ;		
		DefaultTasksGroup::model()->deleteAll('id_group = :id_group', array(':id_group'=>$id_group));
		if (isset($_POST['checked'])){
			foreach ($_POST['checked'] as $check){
				$model = new DefaultTasksGroup();
				$model->id_group = $id_group;
				$model->id_default_task = $check;
				$array_tasks[] = $check;
				$model->save();
				
			}
		}
		$users = Yii::app()->db->createCommand("SELECT id_user from user_groups where id_group = '{$id_group}'")->queryAll();
			foreach ($users as $user)
				$users_id[] = $user['id_user'];		
		$array_add_tasks = array_diff($array_tasks,$array_default_taskss);
		$array_delete_tasks = array_diff($array_default_taskss,$array_tasks);
		if($array_add_tasks != null){			
			$times = Yii::app()->db->createCommand("SELECT * FROM timesheets WHERE status = 'New'  AND id_user IN (" . implode(',', $users_id) . ")")->queryAll();
			foreach ($times as $time){
				foreach ($array_add_tasks as $to_insert )
					$values_tasks[] = '('.$to_insert.','.$time['id_user'].',"0.00","",'.$time['id'].',"'.$time['week_start'].'",1)';					
			}
			Yii::app()->db->createCommand('INSERT  INTO user_time (id_task,id_user,amount,comment,id_timesheet,date,`default`) VALUES '.implode(',',$values_tasks))->execute();
		}
		if($array_delete_tasks != null){
			foreach ($array_delete_tasks as $to_delete){
				$uid = Yii::app()->db->createCommand("SELECT id_task FROM user_time WHERE amount > '0' AND id_task = '$to_delete' LIMIT 1")->queryScalar();
				if($uid != null)
					array_push($to_delete_ar,$uid);
				else 
					Yii::app()->db->createCommand("DELETE FROM user_time WHERE id_task = '$to_delete' AND id_user IN (" . implode(',', $users_id) . ")")->execute();
			}
			if($to_delete_ar != null){
				echo CJSON::encode(array('status'=>'to_delete','id_to_delete'=>$to_delete_ar,'users'=>$users_id));
				exit;
			}				
		}
		echo CJSON::encode(array('status'=>'success'));
		exit;
	}
	public function actionChangeDefaultTasks()	{
		if (!isset($_POST['id_default_task']))
			exit;
		$id = $_POST['id_default_task'];
		$value = $_POST['value'];
		Yii::app()->db->createCommand("UPDATE default_tasks SET billable='{$value}' WHERE id='{$id}'")->execute();
		echo CJSON::encode(array('status'=>'success'));
		exit;
	}
	public function actionDeleteDefaultTasks(){
		$id_tasks = $_POST['id_tasks'];
		$users = $_POST['users'];
		if(isset($_POST['id_group'])){
			foreach($id_tasks as $id){
				$model = new DefaultTasksGroup();
				$model->id_group = $_POST['id_group'];
				$model->id_default_task = $id;
				$model->save();
			}
		}else{
			foreach($id_tasks as $id_task)
				Yii::app()->db->createCommand("DELETE FROM user_time WHERE id_task = '$id_task' AND id_user IN (" . implode(',', $users) . ")")->execute();
		}
		echo CJSON::encode(array('status'=>'success'));
		exit;
	}
	public function loadModel($id)	{
		$model=Groups::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='groups-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}?>
