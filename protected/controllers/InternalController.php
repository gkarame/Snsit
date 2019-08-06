<?php
class InternalController extends Controller{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
		);
	}
	public function accessRules(){
		return array(
			array(
				'allow',
				'actions'=>array('sendWeeklyUpdate', 'sendBiWeeklyUpdate'),
				'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(
						'upload','AssignUsers','UnassignUsers','GetAssignedUsers','GetUnssignedUsers','AssignRecipients','GetUnssignedRecipients','task',
						'index','UpdateEtd','view','create','ManageTasks','CreateTasks','DeleteTask','UpdateTasks','update', 'delete','updateHeader',
							),
				'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
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
				 'path' => Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'internal'
			 ),
		 );
	}
	public function actionView($id){
		if(!GroupPermissions::checkPermissions('general-internal'))	{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$id = (int) $id;
		$model = $this->loadModel($id);
		$subtab = $this->getSubTab(Yii::app()->createUrl('internal/view', array('id' => $id)));
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/internal/view/'.$id => array(
					'label'=> ''.$model->name,
					'url' => array('internal/view', 'id' => $model->id),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1,
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		Yii::app()->clientScript
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/globalize.min.js',CClientScript::POS_HEAD)
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/dx.chartjs.js',CClientScript::POS_HEAD)
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-core.js',CClientScript::POS_HEAD) //<!-- required -->
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-viz-core.js',CClientScript::POS_HEAD) //<!-- required -->
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-viz-charts.js',CClientScript::POS_HEAD);//<!-- dxChart -->
		$this->jsConfig->current['activeTab'] = $subtab;
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));		
	}
	public function actionCreate(){
		if (!GroupPermissions::checkPermissions('general-internal','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/internal/create' => array(
					'label'=> 'Add Internal Project',
					'url' => array('internal/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model=new Internal();
		if (isset($_POST['Internal']))	{

			$model->attributes = $_POST['Internal'];
			$users = array();		
			if(isset($_POST['Internal']['recipients']) && $_POST['Internal']['recipients'] !=null){
				$cc= $_POST['Internal']['recipients'];
				foreach ($cc as $user)
				{	$users[] = $user; }
				$users = array_filter($users);
				$users = implode(',',$users);
				$model->recipients=$users;
			}
			if(isset($_POST['Internal']['project_manager']) && !empty($_POST['Internal']['project_manager']))
			{ $model->project_manager= Users::getIdByName($_POST['Internal']['project_manager']); }
			if(isset($_POST['Internal']['business_manager']) && !empty($_POST['Internal']['business_manager']))
			{ $model->business_manager= Users::getIdByName($_POST['Internal']['business_manager']); }
			$model->adddate = date('Y-m-d H:i:s');
			if ($model->save())	{
				Internal::sendEmailNew($model);
				Utils::closeTab(Yii::app()->createUrl('internal/create'));
				$this->action_menu = Yii::app()->session['menu'];
				$this->redirect(array('internal/view', 'id'=>$model->id));
			}else{
				$model->project_manager=$_POST['Internal']['project_manager'];
				$model->business_manager=$_POST['Internal']['business_manager'];
			}
		}
		$this->render('create',array(
			'model'=>$model,
			'dep_date'=>isset($_POST['Internal']['dep_date'])?$_POST['Internal']['dep_date']:"",
			'id_customer'=>isset($_POST['Internal']['id_customer'])?$_POST['Internal']['id_customer']:""
		));
	}
	public function actionUpdateHeader($id){
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		if (isset($_POST['Internal']))
		{
			$oldstat= $model->status;
			$model->attributes = $_POST['Internal'];
			if($model->status == 1 && Internal::validateallTasksclosed($id) != 0){
				echo json_encode(array_merge(array(
							'status' => 'fail',
							'errormsg' => 'Cannot close project before closing all tasks'
					), $extra));
				exit;
			}
			if ($model->save()){
				$closed=0;
				if($oldstat != $model->status && $model->status == 1)
				{
					$model->close_date=  date('Y-m-d H:i:s');
					$closed=1;
					$this->sendProjectclosed($model->id, $model->name, $model->project_manager, $model->eta, $model->estimated_effort, $model->recipients, $model->business_manager);
				}
				$model->save();
					echo json_encode(array_merge(array(
							'status' => 'saved',
							'closed' => $closed,
							'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
					), $extra));
					Yii::app()->end();
				}
		}	
		Yii::app()->clientScript->scriptMap=array(
		'jquery.js'=>false,
		'jquery.min.js' => false,
		'jquery-ui.min.js' => false,
		);
		echo json_encode(array_merge(array(
				'status'=>'success',
				'html'=>$this->renderPartial('_edit_header_content', array('model'=> $model), true, true)
		), $extra));
	}

	public static function sendProjectclosed($id, $name, $project_manager, $eta, $effort, $recipients, $bm){
		$notif = EmailNotifications::getNotificationByUniqueName('internal_project_closed');
		if ($notif != NULL) {
        	$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/>Internal project <b>".$name."</b> has been Closed.<br/><br/>Performance Summary:<br/>";
           	$message.= "- <b>PM:</b> ".Users::getNameById($project_manager)."<br/>- <b>BM:</b> ".Users::getNameById($bm)."<br/>- <b>ETD:</b> ".date('d/m/Y', strtotime($eta))."<br/>";
           	$message.= "- <b>Total MDs:</b> ".$effort." MDs<br/>- <b>Actual MDs:</b> ".Utils::formatNumber(Internal::getTimeSpentPerProject($id))." MDs";
	        $message.= "<br/><br/>Best Regards,<br/>SNSit";
	        $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
            $resources= Internal::getAssignUsersPerProject($id);

            if(!empty($recipients))
            {
                	$recipients =  explode(',', $recipients);
	                foreach($recipients as $recipient){
						$emr = UserPersonalDetails::getEmailById($recipient);
						if (!empty($emr)) {
		                  //  Yii::app()->mailer->AddCCs($emr);
							Yii::app()->mailer->AddAddress($emr);
		                }
					}
			}

			$bmemail = UserPersonalDetails::getEmailById($bm);
				if (!empty($bmemail)) {
                    Yii::app()->mailer->AddAddress($bmemail);
                }


			$pm = UserPersonalDetails::getEmailById($project_manager);
				if (!empty($pm)) {
                    Yii::app()->mailer->AddAddress($pm);
                }
			foreach($resources as $user){
				$em = UserPersonalDetails::getEmailById($user['id']);
				if (!empty($em)) {
	                    Yii::app()->mailer->AddAddress($em);
	                }
				}
			$emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
            foreach($emails as $email)	{
				Yii::app()->mailer->AddAddress($email);
			}
            Yii::app()->mailer->Subject =  $notif['name'];
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);     
        }
	}
	public function actionUpdate($id){
		$model=$this->loadModel($id);
		if (isset(Yii::app()->session['menu']) && $new == 1) {
				Utils::closeTab(Yii::app()->createUrl('internal/create'));
				$this->action_menu = Yii::app()->session['menu'];
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/internal/update/'.$id => array(
						'label'=> ''.$model->name,
						'url' => array('internal/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => $subtab,
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		if (isset($_POST['Internal'])){
			$model->attributes=$_POST['Internal'];
			if ($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('update',array(
			'model'=>$model,
		));
	}
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('general-internal')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['Internal']) ? $_GET['Internal'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/internal/index' => array(
						'label'=>Yii::t('translations', 'Internal Projects'),
						'url' => array('internal/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Internal('search');
		$model->unsetAttributes(); 
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function loadModel($id){
		$model=Internal::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model){
		if (isset($_POST['ajax']) && $_POST['ajax']==='internal-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionDelete($id){
		$this->loadModel($id)->delete();
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}	
	public function actionUpdateEtd(){		
		$startdate= $_POST['start'];
		
		$tasks = $_POST['checktask'];
		$stasks = implode(',', $tasks);

		if(empty($startdate) || trim($startdate) == ''){
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'ETD Date must be set.'
				)));
				exit;
		}
		$nr = Yii::app()->db->createCommand("update internal_tasks set eta = '".$startdate."' where id in (".$stasks.") ")->execute();
		echo json_encode(array_merge(array(
					'status'=>'success','message'=>'ETD Date is set.'
				)));
				exit;
	}
	public function actionAssignRecipients($id){
		if (empty($_POST)){
			exit;
		}
		$ids = $_POST['checked']; 
		$model=Internal::model()->findByPk($id);
		$susers = implode(',', $ids);
		if(!empty($model->recipients))
		{
			$susers = $susers .','.$model->recipients;
		}
		
		if (!empty($ids)){
			Yii::app()->db->createCommand("UPDATE internal SET recipients= '".$susers."' WHERE id='{$id}'")->execute();
		}

		echo CJSON::encode(array(
				'status'=>'success',
				'message' => 'Recipients have been added!',
				'names'=> Internal::getUsersNames($susers),
			));
		exit;
	}
	public function actionAssignUsers($id){
		if (empty($_POST)){
			exit;
		}
		$ids = $_POST['checked']; 
		$tasks = $_POST['checktask'];
		$stasks = implode(',', $tasks);
		$susers = implode(',', $ids);
		$taskss = array();
		$user_times = array();	
		$current_user=Yii::app()->user->id;
		
		if (!empty($ids)){
			$all_timesheets = Yii::app()->db->createCommand("SELECT id, id_user, week_start FROM timesheets WHERE id_user IN ($susers) AND status='New'")->queryAll();
			$timesheets = array();
			foreach ($all_timesheets as $key => $ts){
				$timesheets[$ts['id_user']][$key]['id'] = $ts['id'];
				$timesheets[$ts['id_user']][$key]['day'] = $ts['week_start'];
			}
		}		
		foreach ($ids as $uid) {
			foreach ($tasks as $task) {
				$this->sendAssignment($uid, $task); 
				$taskss[] = '('.$uid.','.$task.')';
				if (isset($timesheets[$uid])){
					foreach ($timesheets[$uid] as $ts){
						$user_times[] = '('.$uid.','.$task.','.$ts['id'].',"0.00","'.$ts['day'].'",3,0'.')';												
					}
				}
			}
		}		
		if (!empty($taskss)){
			$nr = Yii::app()->db->createCommand('INSERT IGNORE INTO user_internal (id_user, id_task) VALUES '.implode(',',$taskss))
								->execute();
		}		
		if (!empty($user_times)){
			Yii::app()->db->createCommand('INSERT IGNORE INTO user_time (id_user, id_task, id_timesheet, amount, date, `default`, status)
			 					VALUES '.implode(',',$user_times))
						  ->execute();
		}		
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been assigned!'
			));
		exit;
	}
	public  function sendAssignment($user, $task){
        $tasks = Yii::app()->db->createCommand("SELECT * FROM internal_tasks WHERE id=".$task." ")->queryRow();   
        $emailsent = Yii::app()->db->createCommand("SELECT count(1) FROM internal_tasks_emails WHERE id_task=".$task." and id_user=".$user." ")->queryScalar();     
        $notif = EmailNotifications::getNotificationByUniqueName('internal_assign');
        if ($notif != NULL && !empty($tasks) && $emailsent == 0) {

                $to_replace = array(
                    '{body}'
                );
                $message = ''; 

                $message .= "Dear ".Users::getfirstNameById($user).",<br/><br/> Kindly note that you have been assigned on the task below under the internal project <b>".Internal::getNameById($tasks['id_internal'])."</b>:";
                $message .=" <br/><br/>- <b>Task:</b> ".$tasks['description'];
                $message .=" <br/>- <b>Priority:</b> ".InternalTasks::getPriority($tasks['priority']);
                $message .=" <br/>- <b>ETA:</b> ".$tasks['eta']."<br/>- <b>EMDs:</b> ".$tasks['estimated_effort']."<br/>- <b>Remarks:</b> ".$tasks['notes'];
                $message .=" <br/><br/>Thank you,<br/>SNSit";
                
                $replace = array(
                    $message                    
                );
                $body    = str_replace($to_replace, $replace, $notif['message']);
				
				Yii::app()->mailer->ClearAddresses();
				$pm = UserPersonalDetails::getEmailById($user);
				if (!empty($pm)) {
                    Yii::app()->mailer->AddAddress($pm);
                }
                

                Yii::app()->mailer->Subject =  "Internal Project Assignment";
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                Yii::app()->mailer->Send(true);
                $nr = Yii::app()->db->createCommand("insert into internal_tasks_emails (id_user, id_task) values ( ".$user.", ".$task.") ")->execute();
           
        }
    }
	public function actionGetAssignedUsers(){
		if (isset($_POST['checktask'])){
			$bring_user = $_POST['checktask'];
			$nr = "";
			foreach ($bring_user as $bu){
				$nr = $nr.','.$bu;
			}
			$users = InternalTasks::getAsignUsers(substr($nr,1));			
			echo CJSON::encode(array(
					'status'=>'success',
					'div'=>$this->renderPartial('_assign_users', array('users'=>$users), true)));
		}else{
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one task!',
			));
		}
		exit;
	}
	public function actionUnassignUsers($id){
		if (!isset($_POST['checktask'], $_POST['checked'])){
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one task and one user!',
			));
			exit;
		}
		$nr = 0;		$tasks = implode(',', $_POST['checktask']);
		$users = implode(',', $_POST['checked']);		
		$nr = Yii::app()->db->createCommand("DELETE FROM user_internal WHERE id_task IN ($tasks) and id_user in ($users)")->execute();
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been unassigned!'
			));
		exit;	
	}
	public function actionGetUnssignedUsers(){
		$id_internal = $_POST['id_internal'];
		if (isset($_POST['checktask'])){
			$users = InternalTasks::getAllUsers();	
				echo CJSON::encode(array(
						'status'=>'success',
						'div'=>$this->renderPartial('_unassigned_users', array('users'=>$users), true)));			
		}else{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one task!',
			));
		}
		exit;
	}
	public function actionGetUnssignedRecipients(){
		$id_internal = $_POST['id_internal'];
		$users = InternalTasks::getRecip($id_internal);	
			echo CJSON::encode(array(
				'status'=>'success',
			'div'=>$this->renderPartial('_recipients_users', array('users'=>$users), true)));			
		exit;
	}
	public function actionUpdateTasks($id=null){
    	if (isset($_POST['save'])){	
    	}else{
	    	$id = (int)$id;
	    	$model = InternalTasks::model()->findByPk($id);
	    	$oldstat= $model->status;
	    	$model->attributes = $_POST['InternalTasks'];	  		
	    	if (empty($model->description)){
				$model->addCustomError('description', $model->description.' cannot be blank');
			}
    		if (empty($model->status)){
				$model->addCustomError('status', $model->status.' cannot be blank');
			}
			if (empty($model->priority)){
				$model->addCustomError('priority', $model->priority.' cannot be blank');
			}
    		if (empty($model->eta)){
				$model->addCustomError('estimated_effort', $model->eta.' cannot be blank');
			}
		    if ($model->save())
		  	{	
		  		if($oldstat != $model->status && $model->status == 3)
		  		{
		  			$model->close_date= date('Y-m-d H:i:s');
		  			$model->save();
		  		}
		  		echo json_encode(array('status'=>'saved' ,
		  			'totalDays'=>Internal::getTasksEMDs($model->id_internal)));	}
		  	else {
		  		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		  	}
    	}
	  	exit;
    }
    public function actionCreateTasks(){
    	$model = new InternalTasks();
    	$model->attributes = $_POST['InternalTasks'];
    	$model->id_internal = $_POST['id_internal'];
    	if ($model->save()) {
	  		echo json_encode(array('status'=>'saved',
	  				'totalDays'=>Internal::getTasksEMDs($_POST['id_internal']),
	  		));
	    } else {
	  		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
	  	}    	
	  	exit;
    }
    public function actionDeleteTask($id  = null){	
		if ($id != null){
			$item = InternalTasks::model()->findByPk($id);

			if ($item===null)
				exit;
			$task_day = InternalTasks::getTimeSpent($id);
			if($task_day!=0)
			{
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'Time is already logged on this task!',
				));
				exit;
			}else{
				$tot= Internal::getTasksEMDs($item->id_internal);
				Yii::app()->db->createCommand("DELETE FROM user_internal WHERE id_task =".$item->id." ")->execute();
				Yii::app()->db->createCommand("DELETE FROM user_time WHERE id_task =".$item->id." and `default`=3 ")->execute();
				$item->delete();			
				echo CJSON::encode(array(
					'status'=>'success',
					'id_phase'=>1,
	  				'totalDays'=>$tot,
				));
			}
		}else{

			if (isset($_POST['checktask'])){
				$r = array();				
				$id_task = $_POST['checktask'];
				$nodelete = 0;
				$internal='';
				foreach ($id_task as $idt){
						$item = InternalTasks::model()->findByPk($idt);
						$task_day = InternalTasks::getTimeSpent($idt);
						if($task_day!=0)
						{
							$nodelete=1;
						}else{
							$internal= $item->id_internal;
							Yii::app()->db->createCommand("DELETE FROM user_internal WHERE id_task =".$item->id." ")->execute();
							Yii::app()->db->createCommand("DELETE FROM user_time WHERE id_task =".$item->id." and `default`=3  ")->execute();
							$item->delete();
						}							
					}
					if($nodelete == 1)
					{
						echo CJSON::encode(array(
								'status'=>'fail',
								'message' => 'Time is already logged on the task!',
							));
							exit;
					}
					$tot= Internal::getTasksEMDs($internal);
				echo CJSON::encode(array(
					'status'=>'success',
					'id_phase'=>1,
	  				'totalDays'=>$tot,
				));
			}else{
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one task!',
				));
			}
		} 
	}

	public function actionManageTasks($id = NULL){
    	if (!isset($_POST['id_internal']))
    		exit;    	
    	if ($id == NULL) {
    		$model = new InternalTasks();
    		$model->status=0;
    		$model->priority=0;
  			$model->id_internal = (int)$_POST['id_internal'];
    	} else {
    		$id = (int)$id;
    		$model = InternalTasks::model()->findByPk($id);
    	}    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_task_form', array(
            	'model'=> $model,
    			'id_internal'=>$model->id_internal,
    			'id'=>$model->id
        	), true, true)));
       exit;
    }
    public function actiontask($id){		
		
		$model=InternalTasks::model()->findByPk($id);		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/internal/task/'.$id => array(
						'label'=>Yii::t('translations', 'Task# '.$id),
						'url' => array('internal/task', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->render('task',array('model'=>$model,	));
	}
    public function actionsendNoProgressAlert(){
        $tasks = Yii::app()->db->createCommand("SELECT * FROM internal WHERE internal.status= 0 and 0 = (select count(1) from internal_tasks where id_internal= internal.id and date(close_date) >= (current_date()- interval 1 WEEK) )")->queryAll();        
        $notif = EmailNotifications::getNotificationByUniqueName('internal_noprogress');
        if ($notif != NULL && !empty($tasks)) {
            foreach ($tasks as $select) {
                $to_replace = array(
                    '{body}'
                );
                $message = ''; 

                $message .= "Dear All,<br/><br/> Kindly note that no tasks were closed in the past week on the internal project <b>".$select['name']."</b>.";
                $message .=" <br/><br/>Thank you,<br/>SNSit";
                
                $replace = array(
                    $message                    
                );
                $body    = str_replace($to_replace, $replace, $notif['message']);
				
				Yii::app()->mailer->ClearAddresses();
				$pm = UserPersonalDetails::getEmailById($select['project_manager']);
				if (!empty($pm)) {
                    Yii::app()->mailer->AddAddress($pm);
                }
                
                $bm = UserPersonalDetails::getEmailById($select['business_manager']);
				if (!empty($bm)) {
                    Yii::app()->mailer->AddAddress($bm);
                }
                $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
                foreach($emails as $email)	{
					Yii::app()->mailer->AddAddress($email);
				}

                Yii::app()->mailer->Subject =  'Internal Project '.$select['name'].' Progress';
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                Yii::app()->mailer->Send(true);
            }
        }
    }
    public function actionsendWeeklyUpdate()
    {
        $tasks = Yii::app()->db->createCommand("SELECT * FROM internal WHERE status= 0")->queryAll();        
        $notif = EmailNotifications::getNotificationByUniqueName('internal_weekly');
        if ($notif != NULL && !empty($tasks)) {
            foreach ($tasks as $select) {
            	if(!EmailNotifications::isNotificationPAlertSent($select['id'] , 'internal project alerts', $notif['id'])){
	                $to_replace = array(
	                    '{body}'
	                );
	                $message = ''; $rec='';
	                $resources= Internal::getAssignUsersPerProject($select['id']);
	                if(!empty($resources))
	                {
	                	$rec=implode(', ', array_column($resources, 'name'));
	                }
	                $lastweekdone= InternalTasks::closedTaskslastTWOweekorAll($select['id']);
	                $closed= InternalTasks::closedTaskslastTWOweekorAll($select['id'], false);
	                $alltasks= InternalTasks::getCountTasks($select['id']);
	                $pending= $alltasks-$closed;

	                if($closed>0){ $rate=$closed*100/$alltasks; }else{ $rate= 0; }

	                $message .= "Dear All,<br/><br/> Kindly find below an update on the internal project:<br/><br/>";
	                $message .= "- <b>Project Name:</b> <a href='".Yii::app()->createAbsoluteUrl('internal/view', array('id'=>$select['id']))."'>".$select['name']."</a><br/>- <b>Created On:</b> ".date('d/m/Y', strtotime($select['adddate']))." <br/>- <b>PM:</b> ".Users::getNameById($select['project_manager'])." <br/>- <b>BM:</b> ".Users::getNameById($select['business_manager'])." <br/>"; 
	                $message .= "- <b>Assigned Resources:</b> ".$rec." <br/>- <b>Closed Tasks (Last Two Weeks):</b> ".$lastweekdone."<br/>";
	                $message .= "- <b>Completion Rate:</b> ".Utils::formatNumber($rate,2)." %<br/>- <b>Pending Tasks:</b> ".$pending."<br/>- <b>Total Closed Tasks:</b> ".$closed."<br/>- <b>Actual MDs:</b> ".Utils::formatNumber(Internal::getTimeSpentPerProject($select['id']))."<br/>- <b>Last Task Closed:</b> ".Internal::getlastClosed($select['id'])." ";
	                
	                $taskinfo= InternalTasks::closedTaskslastTWOweekorAllInfo($select['id']);
	                if(!empty($taskinfo))
	                {
	                	$message .= "<br/><br/><b>Closed Tasks (Last Two Weeks):</b><br/><br/><table border='1'  style='font-family:Calibri;' ><tr><th style='width:300px;'>Task</th><th style='width:100px;'>Priority</th><th style='width:400px;'>Remarks</th><th style='width:300px;'>Assignee(s)</th></tr>";
	                	foreach($taskinfo as $t){
	                		$assignee= InternalTasks::getAllUsersTaskForEmail($t['id']);
	                		$message.="<tr><td>".$t['description']."</td> <td>".InternalTasks::getPriority($t['priority'])."</td> <td>".$t['notes']."</td><td>".$assignee."</td></tr>";
	                	}
	                	$message .= "</table>";
	                }

	                $message .="<br/><br/>Thank you,<br/>SNSit";
	                
	                $replace = array(
	                    $message                    
	                );
	                $body    = str_replace($to_replace, $replace, $notif['message']);
					
					Yii::app()->mailer->ClearAddresses();
					Yii::app()->mailer->ClearCcs();
					$pm = UserPersonalDetails::getEmailById($select['project_manager']);
					if (!empty($pm)) {
	                   Yii::app()->mailer->AddAddress($pm);
	                }
	                $email_bm=UserPersonalDetails::getEmailById($select['business_manager']);
	                if (!empty($email_bm)) {
							Yii::app()->mailer->AddAddress($email_bm);
					}
					foreach($resources as $user){
						if($select['project_manager'] != $user['id'] && $select['business_manager'] != $user['id'])
						{
							$em = UserPersonalDetails::getEmailById($user['id']);
							if (!empty($em)) {
			                   //Yii::app()->mailer->AddCCs($em);
							   Yii::app()->mailer->AddAddress($em);
			                }
			            }
					}
	                $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
	                foreach($emails as $email)	{
	                	if($pm != $email && $email_bm != $email)
						{
							//Yii::app()->mailer->AddCCs($email);
							   Yii::app()->mailer->AddAddress($email);
						}
					}


	                if(!empty($select['recipients']))
	                {
	                	$recipients =  explode(',', $select['recipients']);
		                foreach($recipients as $recipient){
		                	$f=true;
		                	if(!empty($emails) && in_array($recipient, $emails))
		                	{
		                		$f=false;
		                	}
		                	if(!empty($resources) && in_array($recipient, $resources))
		                	{
		                		$f=false;
		                	}
		                	if(!empty($select['project_manager']) && $recipient == $select['project_manager'])
		                	{
		                		$f=false;
		                	}
		                	if(!empty($select['business_manager']) && $recipient == $select['business_manager'])
		                	{
		                		$f=false;
		                	}
		                	if($f)
		                	{
								$emr = UserPersonalDetails::getEmailById($recipient);
								if (!empty($emr)) {
				                   // Yii::app()->mailer->AddCCs($emr);
									Yii::app()->mailer->AddAddress($emr);
				                }	
		                	}
						}
					}

	                Yii::app()->mailer->Subject =  'IP '.$select['name'].' Weekly Update';
	                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
	                if (Yii::app()->mailer->Send(true)){
							EmailNotifications::saveSentNotification($select['id'] , 'internal project alerts', $notif['id']);
						}    
           		}
            }
        }
    } 

    public function actionsendBiWeeklyUpdate(){$tasks = Yii::app()->db->createCommand("SELECT * FROM internal WHERE status= 0")->queryAll();        
        $notif = EmailNotifications::getNotificationByUniqueName('internal_biweekly');
        if ($notif != NULL && !empty($tasks)) {
        	$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/>Kindly find below an update on all active internal projects:<br/><br/>";
            $message .= "<table  style='font-family:Calibri;border:1px solid #000000;font-size: 13 px;border-collapse:collapse;' ><tr  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'><th  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Project</th><th  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>PM</th><th  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>BM</th><th  style='font-family:Calibri;border:1px solid #000000;font-size: 13 px;width:15%;'>Assigned Resources</th><th  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Completion Rate</th><th  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Actual MDs</th><th style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Pending Tasks</th><th style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Closed Tasks</th><th style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Last 2 Weeks</th><th style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>Last Update</th></tr>";
            foreach ($tasks as $select) {  
            	$rec='';              
                $resources= Internal::getAssignUsersPerProject($select['id']);
                if(!empty($resources))
                {
                	$rec=implode(', ', array_column($resources, 'name'));
                }
                $closed= InternalTasks::closedTaskslastweekorAll($select['id'], false);
                $alltasks= InternalTasks::getCountTasks($select['id']);
                $pending= $alltasks-$closed;
				$lastweekdone= InternalTasks::closedTaskslastTWOweekorAll($select['id']);
                if($closed>0){ $rate=$closed*100/$alltasks; }else{ $rate= 0; }
                $message .= "<tr  style='    border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'><td  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'><a href='".Yii::app()->createAbsoluteUrl('internal/view', array('id'=>$select['id']))."'>".$select['name']."</a></td><td  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>".Users::getNameById($select['project_manager'])."</td><td  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>".Users::getNameById($select['business_manager'])."</td><td  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;'>".$rec."</td><td  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;text-align:center;'>".Utils::formatNumber($rate,2)."%</td><td  style='font-family:Calibri;border:1px solid #000000;font-size: 13 px;text-align:center;border-collapse:collapse;'>".Utils::formatNumber(Internal::getTimeSpentPerProject($select['id']))."</td><td  style='border-collapse:collapse;font-family:Calibri;border:1px solid #000000;font-size: 13 px;text-align:center;'>".$pending."</td><td  style='font-family:Calibri;border:1px solid #000000;font-size: 13 px;text-align:center;border-collapse:collapse;'>".$closed."</td><td  style='font-family:Calibri;border:1px solid #000000;font-size: 13 px;text-align:center;border-collapse:collapse;'>".$lastweekdone."</td><td style='font-family:Calibri;border:1px solid #000000;font-size: 13 px;border-collapse:collapse;'>".Internal::getlastupdate($select['id'])."</td></tr>"; 
			}
			$message .=" </table><br/><br/>Thank you,<br/>SNSit";                
            
            $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
			$emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
            foreach($emails as $email)	{
				Yii::app()->mailer->AddAddress($email);
			}
			//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
            Yii::app()->mailer->Subject =  $notif['name'];
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);     
        }
    } 
}  ?>


