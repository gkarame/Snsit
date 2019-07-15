<?php

class TimesheetsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function init()
	{
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - Time Sheets');
	}
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('generate', 'sendUnsubmittedTimesheets','CreateRequest', 'RejectPHWeekendTasks' , 'checkvalidation','getTimeSheetGrid' , 'sendUnsubmittedTimesheetsForPerson' ,'sendVactionsNotRequested' , 'sendTimesheetSnapshots' ,'getprojectdevtasksonly' ,'getprojectdevtasks', 'getParentDefaulttasks'),
				'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','insertasks','view', 'current', 'saveItem', 'copyLast','ValidateInternalProject', 'ValidateProject', 'validatePublicHoliday','validateDayOff','validateRSRNumber', 'validateSRNumber','validatedepNumber','validateIRNumber','VacationDaysHRRequest',       'ValidateAllowedDays','changeTimesheetStatus', 'deleteItemFromTimesheet', 'approval', 'getTasks', 'addTasks','getDefaultTasks',
					 'addDefaultTasks','getCustomersNamesCS', 'approveTasks', 'rejectTasks','rejectTasksValid','sendrejectemails','getValidation', 'checkIfPendingTimesheets'),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular timesheet with all the user tasks organized into phases and projects
	 * @param integer $id the ID of the model to be displayed
	 * @author Romeo Onisim
	 */
	public function actionView($id)
	{	
		$id = (int)$id;
		$model = $this->loadModel($id);
		if ($model->id_user != Yii::app()->user->id)
		{
		 	throw new CHttpException(404,'The requested page does not exist.');
		}
		$timeSheetTasks = $model->getUserTimesheetTasks($id);
		$defaultTasks = $model->getUserTimesheetTasks($id, 1);
		$maintenance_tasks = $model->getUserTimesheetTasks($id, 2);
		$timeSheetInternal = $model->getUserTimesheetTasks($id, 3);

		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/timesheets/view/'.$id => array(
								'label'=> 'Time Sheet #'.$model->timesheet_cod,
								'url' => array('timesheets/view', 'id' => $id),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => '',
								'order' => Utils::getMenuOrder()+1,
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		if(isset($_POST['refreshTimesheet']))
		{
			$this->renderPartial('_timesheet_table',array(
					'model'=>$model,
					'data'=>$timeSheetTasks,
					'default_tasks'=>$defaultTasks,
					'maintenance_tasks'=>$maintenance_tasks,
					'internal_projects'=>$timeSheetInternal
			), false, true);
		}
		else
		{
			$this->render('view',array(
					'model'=>$model,
					'data'=>$timeSheetTasks,
					'default_tasks'=>$defaultTasks,
					'maintenance_tasks'=>$maintenance_tasks,
					'internal_projects'=>$timeSheetInternal
			));
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Timesheets;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Timesheets']))
		{
			$model->attributes=$_POST['Timesheets'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all user timesheets
	 * @author Romeo Onisim
	 */
	public function actionIndex()
	{
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/timesheets/index' => array(
								'label'=>Yii::t('translations', 'My Time Sheets'),
								'url' => array('timesheets/index'),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1,
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		
		$model = new Timesheets('search');
		$model->unsetAttributes();  // clear any default values
		
		if (isset($_GET['Timesheets']))
		{
			$model->attributes = $_GET['Timesheets'];
		}
		
		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Timesheets the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Timesheets::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Timesheets $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='timesheets-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * The current user timesheet
	 * @author Romeo Onisim
	 */
	public function actionCurrent()
	{
		$id = (int)Timesheets::model()->getLatestUserTimesheetId($id=1);
		
		if ($id !== 0)
		{
			$this->redirect(array('timesheets/view/' . $id));
		}
		else
		{
			$this->render('current');
		}
	}
	
	/**
	 * Generate the next timesheets
	 * @author Romeo Onisim
	 */
	public function actionGenerate($id = 1)
	{
		$users = Users::getActiveNotAdminUsesId();
		foreach($users as $user)		
		{
			if ($id == 0)
			{
				Timesheets::generateTimesheetForUser($user['id'], true);
			}
			else 
			{
				Timesheets::generateTimesheetForUser($user['id']);	
			}
		}
	}
	
	public function actioninsertasks()
	{

		$nextTimesheetId=4620;
		$id_user=70;
			$defaultTasks = Users::getUserDefaultTasksById($id_user);
					$values = '';
					foreach($defaultTasks as $defaultTask)
					{
						$values .= '(' . $id_user . ', ' . $defaultTask['id_task'] . ', ' . $nextTimesheetId . ', "' . date('Y-m-d H:i:s', strtotime('next sunday')) . '", 1),';
					}
					
					// if string is not empty insert all values into the table
					if($values != '')
					{
						$values = rtrim($values, ",");
						$nextUserTimes = Yii::app()->db->createCommand('INSERT INTO user_time(id_user, id_task, id_timesheet, date, `default`) VALUES ' . $values)->execute();
					}
	}
	
	public function actiongetCustomersNamesCS()
	{
		// $ids = $_POST['ids_customers'];
		// $comment = $_POST['comm'];
		
		// $ids=implode(',', $ids);
		// $names= Yii::app()->db->createCommand("select name from customers where id in (".$ids.")")->queryAll();
		
		// foreach ($names as $key => $name) {

		// 	if (strpos(strtolower($comment), (strtolower($name['name']).':')) == false) {
				
		// 	    $comment.=''.$name['name'].':                                                               ';
		// 	}
		// }
		
		// echo json_encode(array('comment'=>$comment));
		// 							exit;
	}
	/**
	 * Save into an item
	 * @author Romeo Onisim
	 */
	public function actionSaveItem()
	{
		if(count($_POST) > 0)
		{
			$ticket_id= $_POST['ticket']; 
			$rsr_id= $_POST['rsr']; 
			$inst_id= $_POST['inst']; 
			$customer= $_POST['customer']; 
			$reason= $_POST['reason']; 
			$id_task = (int)$_POST['id_task'];
			$date = date('Y-m-d H:i:s', strtotime($_POST['date']));
			$hours = (float)$_POST['hours'];
			$hours = round($hours * 4) / 4;
			$hours = number_format($hours, 2, '.', '');
			$comment = $_POST['comment'];
			$default = $_POST['isDefault'];
			$id_timesheet = $_POST['id_timesheet'];
			$radio=$_POST['radio'];
			$fbr= $_POST['fbr'];
			$lieu_of = trim($_POST['lieu_of']);
			if($lieu_of != '')
			{
				$myDateTime = DateTime::createFromFormat('d/m/Y', $lieu_of);
				$lieu_of = $myDateTime->format('Y-m-d 00:00:00');
			}
			Timesheets::updateOrCreateNewItem($id_task, $date, Yii::app()->user->id, $default, $id_timesheet, $hours, $comment, $lieu_of, $ticket_id,$inst_id, $reason , $customer, $radio, $fbr, $rsr_id);
		/*	if($radio == 'no' &&  $hours >0 )
			{	self::sendAlertNoDoc($id_task, $hours, $comment); }*/

			$returnArr['hours'] = $hours;
			$returnArr['comment'] = $comment;
			$returnArr['lieu_of'] = $lieu_of;
			$returnArr['ticket'] = $ticket_id;
			$returnArr['inst'] = $inst_id;
			$returnArr['rsr'] = $rsr_id;
			$returnArr['customer'] = $customer;
			$returnArr['reason'] = $reason;
			$returnArr['radio'] = $radio;
			
			echo json_encode($returnArr);
		}			
	}
	
	
	public function actionValidateInternalProject()
	{	
		$id_task = (int)$_POST['id_task'];
		$stat= Yii::app()->db->createCommand("select p.status	from internal p , internal_tasks pt	where p.id=pt.id_internal and pt.id=".$id_task ."")->queryScalar();
					
		$returnArr['stat'] = $stat;		
			
		echo json_encode($returnArr);
				
	}
	public function actionValidateProject()
	{	
		$id_task = (int)$_POST['id_task'];
		$stat= Yii::app()->db->createCommand("select status	from projects p , projects_phases pp , projects_tasks pt	where p.id=pp.id_project and pt.id_project_phase=pp.id and pt.id=".$id_task ."")->queryScalar();
					
		$returnArr['stat'] = $stat;		
			
		echo json_encode($returnArr);
				
	}

	public function actionvalidateRSRNumber()
	{	
		$nosr= array();
		$tickets = explode(',', $_POST['rsr']);
		
		$task=$_POST['task'];

		//print_r($task);exit;

			 $name= Yii::app()->db->createCommand("select name from default_tasks where id='".$task."'  ")->queryScalar();
			 $id_customer= Customers::getIdbyName($name); 

		foreach ($tickets as $value) {
			$count= Yii::app()->db->createCommand("select count(distinct id) from rsr where id='".$value."' and id_customer='".$id_customer."' ")->queryScalar();
			if($count<1){
				array_push($nosr, $value);

			}
		}
						if(count($nosr)==0){ 
								
							echo json_encode(array('status'=>'success'));
									exit;

						}else{
							
						echo json_encode(array('status'=>'failure'));
									exit;
						}					
	}

	public function actionvalidateSRNumber()
	{	
		$nosr= array();
		$tickets = explode(',', $_POST['ticket']);
		
		$task=$_POST['task'];

		//print_r($task);exit;

			 $name= Yii::app()->db->createCommand("select name from default_tasks where id='".$task."'  ")->queryScalar();
			 $id_customer= Customers::getIdbyName($name); 

		foreach ($tickets as $value) {
			$count= Yii::app()->db->createCommand("select count(distinct id) from support_desk where id='".$value."' and id_customer='".$id_customer."' ")->queryScalar();
			if($count<1){
				array_push($nosr, $value);

			}
		}
						if(count($nosr)==0){ 
								
							echo json_encode(array('status'=>'success'));
									exit;

						}else{
							
						echo json_encode(array('status'=>'failure'));
									exit;
						}					
	}
	public function sendAlertNoFBR($tasks, $timesheet){

		$subject = "Missing FBR Attachment";
		$notif = EmailNotifications::getNotificationByUniqueName('fbr_doc');
		if ($notif != NULL) 
    	{
    		$txt="Dear All,<br /><br /> Time has been logged by <b>".Users::getNameById( Yii::app()->user->id)."</b> on timesheet#<b>".$timesheet."</b> on the below FBRs tasks which do not have an available documentation on SNSit</b>:<br><ul>";
			Yii::app()->mailer->ClearAddresses();		
			foreach ($tasks as $task) {
				$pid=ProjectsTasks::getProjectByTask($task['id_task']);
				$pname=Projects::getNameById($pid);

				$pm= Projects::getProjectManagerEmail($pid);
	    		$bm= Projects::getBusinessManagerEmail($pid);

	    		Yii::app()->mailer->AddAddress($pm);
				Yii::app()->mailer->AddAddress($bm);	
				
				$txt.="<li><b>Project:</b> ".$pname." - <b>FBR</b>: ".ProjectsTasks::getTaskDescByid($task['id_task'])."</li>";
			}
			$txt.="</ul>Best Regards,<br />SNSit";
				   					
			$to_replace = array(
			'{body}'
			);

			$replace = array(						
				$txt
			);

			$body = str_replace($to_replace, $replace, $notif['message']);
						
			$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);						
			foreach($emails as $email){
				if (!empty($email))
				{	//Yii::app()->mailer->AddCCs($email); 
					Yii::app()->mailer->AddAddress($email);
				}
			} 					
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
				
		}
	}
	public function sendAlertNoDoc($timesheet)
	{	
		$subject = "Non-documented Change(s)";
		$notif = EmailNotifications::getNotificationByUniqueName('nondocumented_changes');
		if ($notif != NULL) 
    	{
			$projects= ProjectsTasks::getProjectsPerTimesheet($timesheet);
			foreach ($projects as $project) {
				$pname=Projects::getNameById($project['id_project']);
				$tasks= ProjectsTasks::getNondocumentedTasksPerProject($project['id_project'], $timesheet);
				$comment='';
				foreach ($tasks as $key => $task) {
	    			$res=Yii::app()->db->createCommand("SELECT id FROM `undocumented_tasks` where id_task=".$task['id']." and id_project= ".$project['id_project'])->queryScalar();
					if(empty($res))
					{
						Yii::app()->db->createCommand("INSERT INTO undocumented_tasks (id_project, id_task) VALUES(".$project['id_project'].",".$task['id'].")")->execute();		
						$res=Yii::app()->db->createCommand("SELECT id FROM `undocumented_tasks` where id_task=".$task['id']." and id_project= ".$project['id_project'])->queryScalar();					
					}
					$comment.= '<a href="'.Yii::app()->createAbsoluteUrl('projects/updateDocument/?id='.$res).'">'.$task['comment'].'</a> ,';
	    		}
	    		$comment = substr($comment, 0, (strlen($comment)-1) );

				$txt="Dear All,<br /><br />The following timesheet effort related to non-documented change(s) has been logged by <b>".Users::getNameById( Yii::app()->user->id)."</b>:<br><ul>";
				$txt.="<li><b>Project:</b> ".$pname." </li>";
				$txt.="<li><b>Customer:</b> ".Customers::getNameById(Projects::getCustomerByProject($project['id_project']))." </li>";

				$hours=  array_sum(array_column($tasks,'amount'));
				//$descriptions= array_column($tasks, 'comment');
				//$comment= implode(", ",$descriptions);
				$fbrs= implode(" ",array_column($tasks, 'fbr'));

	    		$txt.="<li><b>Task(s) Time:</b> ".$hours." </li>";
	    		$txt.="<li><b>Task(s) Description:</b> ".$comment." </li>";
	    		if(!empty($fbrs) && trim($fbrs)!='')
	    		{
	    			$fbrs= implode(", ",array_column($tasks, 'fbr'));
	    			$txt.="<li><b>FBR(s):</b> ".$fbrs." </li>";
	    		}

	    		

	    		$txt.="</ul>Best Regards,<br />SNSit";
				if ($notif != NULL) 
	    		{    					
	    					$to_replace = array(
							'{body}'
								);

	    						$replace = array(						
								$txt
								);

						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						$pm= Projects::getProjectManagerEmail($project['id_project']);
	    				$bm= Projects::getBusinessManagerEmail($project['id_project']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->AddAddress($pm);
						//Yii::app()->mailer->AddAddress($bm);						
						foreach($emails as $email){
							if (!empty($email))
							{	//Yii::app()->mailer->AddCCs($email); 
								//Yii::app()->mailer->AddAddress($email);
							}
						} 					
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send(true);
				}

			}
		}
	}	
	public function actionvalidatedepNumber()
	{	
		$nosr= array();
		$dep = explode(',', $_POST['dep']);
		foreach ($dep as $value) {
			$count= Yii::app()->db->createCommand("select count(*) from deployments where id=".$value." ")->queryScalar();
			if($count<1)
			{
				array_push($nosr, $value);

			}
		}
						if(count($nosr)==0){ 
								
							echo json_encode(array('status'=>'success3'));
									exit;

						}else{
							
						echo json_encode(array('status'=>'failure'));
									exit;
						}					
	}
	public function actionvalidateIRNumber()
	{	
		$nosr= array(); $nosr2=array();
		$inst = explode(',', $_POST['inst']);

		foreach ($inst as $value) {
			$count= Yii::app()->db->createCommand("select count(*) from installation_requests where id=".$value." ")->queryScalar();
			if($count<1)
			{
				array_push($nosr, $value);

			}
		}
		foreach ($inst as $value) {
			$count= Yii::app()->db->createCommand("select count(*) from installation_requests where id=".$value." and maintenance=0 and customer!=177")->queryScalar();
			if($count>0)
			{
				array_push($nosr2, $value);

			}
		}
						if(count($nosr)==0 && count($nosr2)==0 ){ 
								
							echo json_encode(array('status'=>'success2'));
									exit;

						}else if(count($nosr)!=0) {
							
							echo json_encode(array('status'=>'unvalid'));
									exit;
						}else if(count($nosr2)!=0)
						{
								echo json_encode(array('status'=>'project'));
									exit;
						}	else{
								echo json_encode(array('status'=>'failure'));
									exit;
						}				
	}
public function actionvalidateDayOff()
	{		
		$date=$_POST['date'];
		$time=$_POST['time'];
		$entrydate=$_POST['entrydate'];
		$date= str_replace('/','-',$date);
		$fDate = date('Y-m-d', strtotime($date));
		$entrydatef = date('Y-m-d', strtotime($entrydate));
		$id_user=Yii::app()->user->id;
		$count= Yii::app()->db->createCommand("select sum(amount) from user_time where id_user=".$id_user." and  date='".$fDate."'  and (id_task not in (11,12,13,14,15)  or `default`=0)  ")->queryScalar();
		$checkused= Yii::app()->db->createCommand("select sum(amount) from user_time where id_user=".$id_user." and  date!='".$entrydatef."' and id in  (select id_user_time from lieu_of where date like '".$fDate."%' )")->queryScalar();
		$count= $count-$checkused;
		$remaining= $count-$time;
		if($count==0 ){ 								
							echo json_encode(array('status'=>'failure', 'message'=>'In lieu of is not a valid working day!' ));
									exit;
						}else if($count<$time)
						{
							echo json_encode(array('status'=>'failure', 'message'=>'In lieu of hours do not cover the requested time'));
									exit;
						}
						else if($remaining<0)
						{
							echo json_encode(array('status'=>'failure', 'message'=>'In lieu of hours do not cover the requested time'));
									exit;
						}
						else{							
						echo json_encode(array('status'=>'success'));
									exit;
						}					
	}
public function actionvalidatePublicHoliday()
	{	
	
		$date=$_POST['phdate'];
		$fDate = date('Y-m-d', strtotime($date));

		$id_user=Yii::app()->user->id;
		$branch=Users::getBranchByUser($id_user);


		$count= Yii::app()->db->createCommand("select count(1) from public_holidays ph , codelkups c where ph.office=c.codelkup and c.id_codelist='13' and c.id='".$branch."' and ph.date='".$fDate."' ")->queryScalar();
		$support= (int)Yii::app()->db->createCommand("select  count(1) from fullsupport where primary_contact='".$id_user."' and from_date ='".$fDate."' and type='403' ")->queryScalar();
		if($count==0){ 
								
							echo json_encode(array('status'=>'success' ,'support'=>$support  ));
									exit;

						}else{
							
						echo json_encode(array('status'=>'failure' ,'support'=>$support ));
									exit;
						}					
	}

	


	public function actionVacationDaysHRRequest() {
		$year=$_POST['selected']; 
		$id_user=Yii::app()->user->id;
		$sum = 0;
		$days_allowed=0;
		$valid=1;
		$year = $year;
		$id_user = $id_user;
		$id_vac = 13;
		$sum = (int) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE id_user = ".$id_user." AND `default`=1 AND id_task =".$id_vac." AND YEAR(`date`) = ".$year."")->queryScalar();
		$sum= $sum/8;
		$sum += (int) Yii::app()->db->createCommand("SELECT SUM(DATEDIFF(r.endDate+1,r.startDate)) from requests r where r.type=91 and r.user_id=".$id_user." ")->queryScalar();
		echo $sum."<br />";
		$days_allowed=(int) YII::app()->db->createCommand("SELECT uhd.vacation_days_allowed from user_hr_details uhd  WHERE uhd.id_user=".$id_user." ")->queryScalar();

		$diff=$days_allowed-$sum;
		echo $diff."<br />";
		if($diff<=0)
			$valid=0;

		$returnArr['valid']=$valid;
		echo json_encode($returnArr);

	}
	



	public static function actionValidateAllowedDays() {
		
		$year = $_POST['year']; 
		$id_user=Yii::app()->user->id;
		$sum = 0;
		$days_allowed=0;
		$valid=1;
		$month = (int)$month;
		$year = (int)$year;
		$id_user = (int) $id_user;
		if ($month < 10) $month = "0".$month;
		$id_vac = 13;
		

		$sum += (int) Yii::app()->db->createCommand("SELECT DATEDIFF(r.endDate,r.startDate) from requests r where r.type=91 and r.user_id='$id_user' ")->queryScalar();
		
		$days_allowed=(int) YII::app()->db->createCommand("SELECT uhd.vacation_days_allowed from user_hr_details uhd  WHERE uhd.id_user='$id_user' ")->queryScalar();

		$diff=$days_allowed-$sum;


		if($diff<=0)
			$valid=0;

		echo json_encode($valid);

	}





	
	/**
	 * Copy last item infos
	 * @author Romeo Onisim
	 */
	public function actionCopyLast()
	{
		if(count($_POST) > 0)
		{
			$id_task = (int)$_POST['id_task'];
			$date = date('Y-m-d H:i:s', strtotime($_POST['date']));
			$default = $_POST['isDefault'];
			
			$lastItemByDate = Timesheets::getLatestItemDataByDate($id_task, $date, Yii::app()->user->id, $default);
			
			if($lastItemByDate !== false)
			{
				echo json_encode($lastItemByDate);
			}
			else
			{
				echo json_encode(array('error' => 1));
			}
			exit;
		}
	}

	public function actionCreateRequest()
	{	
		
		if(isset($_GET['id_timesheet'])){

			$id_timesheet=$_GET['id_timesheet'];

		}else{

		}
		
		$results=Timesheets::getUserTimebyTimesheet($id_timesheet);
		
		foreach ($results as $value) {

		$count= Yii::app()->db->createCommand("SELECT count(1)  from requests r where r.type='567' and r.startDate='".$value['date']."' and r.user_id='".$value['id_user']."'  ")->queryScalar();
			if($count==0){
						$insert = Yii::app()->db->createCommand()->insert('requests', array(
								'user_id'=> $value['id_user'],
								'startDate'=> $value['date'],
								'endDate'=> $value['date'],
								'type'=> '567',
								'status' => '1',
								'description' =>  $value['reason'],
								
						));
					}
		}

			$this->redirect(array('index'));
		
	}

	public function sendEmailOffLeaves($id_user)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('days_off');
					$subject = 'Days Off Alert';
	
    				if ($notif != NULL) 
    				{
    					$lm=EmailNotificationsGroups::getLMNotificationUsers($id_user);
    					$txt='Dear line manager, <br/><br/>Kindly note that <b>'.Users::getNameById($id_user).'</b> has taken so far more than 5 days Off in the past month.<br/>';

    					$to_replace = array(
						'{body}'
							);

    						$replace = array(						
							$txt
							);

					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->ClearAddresses();
					Yii::app()->mailer->AddAddress($lm);					
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);

						}
	}


	public function sendEmailSickLeaves($id_user, $daysoff)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('sick_leaves');
					$subject = 'Sick Leaves Alert';
	
    				if ($notif != NULL) 
    				{
    					$lm=EmailNotificationsGroups::getLMNotificationUsers($id_user);
    					$txt='Dear line manager, <br/><br/>Kindly note that <b>'.Users::getNameById($id_user).'</b> has taken so far '.Utils::formatNumber($daysoff).' days of Sick Leaves this year.<br/>';

    					$to_replace = array(
						'{body}'
							);

    						$replace = array(						
							$txt
							);

					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->ClearAddresses();
					Yii::app()->mailer->AddAddress($lm);					
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);

						}
	}
	
	/**
	 * Changes the timesheet status based on the status variable
	 */
	public function actionChangeTimesheetStatus()
	{
		if (isset($_POST['id_timesheet'], $_POST['status']))
		{
			$id_timesheet = (int)$_POST['id_timesheet'];
			$phorweekend=Timesheets::checkifPHorWeekEnd($id_timesheet);
			if($phorweekend>0){
				Timesheets::sendNotificationsEmails($id_timesheet);
			}
			$status = Timesheets::changeTimesheetStatus($id_timesheet, $_POST['status']);
			$id_user= Yii::app()->user->id;		

			if ($status > 0)
			{
				if ($_POST['status'] == Timesheets::STATUS_SUBMITTED)
				{
					//houda
					Yii::app()->db->createCommand("UPDATE user_time SET status = 0 WHERE id_timesheet = '{$id_timesheet}'")->execute();
					Timesheets::checkifGolive($id_timesheet, $id_user);	
					Timesheets::timesheetvalidate($id_timesheet, $id_user);	
					$query="SELECT SUM(uts.amount)/8  FROM `user_time` uts ,users u ,user_personal_details upd ,codelkups c where uts.id_user=u.id and upd.id_user=u.id and c.id_codelist='12' and upd.unit=c.id and uts.`default`='1' and uts.id_task in (select id from `default_tasks` where id_parent='2') and uts.amount>0 and u.active='1' AND u.id='".$id_user."' and uts.id_task=12 and YEAR(date)=YEAR(CURRENT_DATE())";
					$daysoff= Yii::app()->db->createCommand($query)->queryScalar();
					if ($daysoff >5)
					{
						self::sendEmailSickLeaves($id_user, $daysoff);
					}
					$query="SELECT SUM(uts.amount)/8 FROM `user_time` uts ,users u ,user_personal_details upd ,codelkups c where uts.id_user=u.id and upd.id_user=u.id and c.id_codelist='12' and upd.unit=c.id and uts.`default`='1' and uts.id_task in (select id from `default_tasks` where id_parent='2') and uts.amount>0 and u.active='1' AND u.id='".$id_user."' and uts.id_task=11 and date > (CURRENT_DATE()- INTERVAL 30 DAY)";
					$dayoff= Yii::app()->db->createCommand($query)->queryScalar();
					if ($dayoff >5)
					{
						self::sendEmailOffLeaves($id_user);
					}
					$undocumented= Yii::app()->db->createCommand("select count(1) from user_time WHERE id_timesheet = '{$id_timesheet}' and documented='no'")->queryScalar();
					if($undocumented > 0)
					{
						self::sendAlertNoDoc($id_timesheet); 
					}	
					$uploaddoc= Yii::app()->db->createCommand("select * from user_time WHERE id_timesheet= ".$id_timesheet ."  and amount>0  and `default`=0 and id_task in (select id from projects_tasks where type=1 and id not in (select fbr from documents  where fbr is not null)) group by id_task ")->queryAll();
					if(!empty($uploaddoc))
					{
						self::sendAlertNoFBR($uploaddoc, $id_timesheet); 
					}

				}
				else if ( $_POST['status'] == Timesheets::STATUS_NEW)
				{
						Yii::app()->db->createCommand()->delete('timesheet_validations', 'id_timesheet=:id', array(':id'=>$id_timesheet));
						echo json_encode(array('success' => 1, 'status' => Timesheets::STATUS_NEW));
				}else
				{
					Yii::app()->db->createCommand()->delete('timesheet_validations', 'id_timesheet=:id', array(':id'=>$id_timesheet));
					echo json_encode(array('success' => 1));
				}
			}
			else
			{
				echo json_encode(array('error' => 1));
			}
			exit;
		}
	}	
	/**
	 * Deletes a task or multiple tasks based on the type(task or project)
	 * @author Romeo Onisim
	 */
	public function actionDeleteItemFromTimesheet()
	{
		if(count($_POST) > 0)
		{
			$id_timesheet = $_POST['id_timesheet'];
			$id_user = Yii::app()->user->id;
			$type = $_POST['type'];
			$id = (int)$_POST['id'];
			
			$result = Timesheets::deleteItemFromTimesheet($id_timesheet, $id_user, $id, $type);
			$total = $this->refreshTotal($id_timesheet,$result['week_start'], 'total');
			$total_billable = $this->refreshTotal($id_timesheet,$result['week_start'], 'billable');
			
			switch($type) {
			case 'task':
				$project_id = Yii::app()->db->createCommand()
					->select('projects_phases.id_project')
									->from('user_time')
									->leftJoin('projects_tasks', 'projects_tasks.id = user_time.id_task')
									->leftJoin('user_task', 'user_task.id_task= user_time.id_task')
									->leftJoin('projects_phases', 'projects_phases.id = projects_tasks.id_project_phase')
									->leftJoin('projects', 'projects_phases.id_project = projects.id')
									->where('id_user=:id_user AND id_timesheet=:id_timesheet', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ))
									//->group('user_time.id_task')
									->queryRow();
				$total_project = $this->refreshTotal($id_timesheet,$result['week_start'], 'project',$project_id['id_project']);
				$rfr = Timesheets::getTotalTimes($id_timesheet);
				$total_bill_last = Timesheets::getTotalBillableTimes($id_timesheet);
				$total_per_project = Timesheets::getCurrentProjectTotalTime($id_timesheet,$project_id['id_project']);
				echo json_encode(array('status'=>'task','total'=>$total,'total_bill'=>$total_billable,'total_project'=>$total_project,'total_time'=>$rfr,'total_bill_last'=>$total_bill_last,'total_per_project'=>$total_per_project));
				break;
			case 'project':
				$rfr = Timesheets::getTotalTimes($id_timesheet);
				$total_bill_last = Timesheets::getTotalBillableTimes($id_timesheet);
				echo json_encode(array('status'=>'project','total'=>$total,'total_bill'=>$total_billable,'total_time'=>$rfr,'total_bill_last'=>$total_bill_last));
				break;	
			case 'default_task':
				$parent_id = Yii::app()->db->createCommand()
				->select('default_tasks.id_parent')
				->from('user_time')
				->leftJoin('default_tasks', 'default_tasks.id = user_time.id_task')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND `default`=1', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ))
				->queryScalar();
				$total_project = $this->refreshTotal($id_timesheet, $result['week_start'], 'default_project', $parent_id);
				$rfr = Timesheets::getTotalTimes($id_timesheet);
				$total_bill_last = Timesheets::getTotalBillableTimes($id_timesheet);
				$total_per_project = Timesheets::getCurrentProjectTotalTime($id_timesheet,$parent_id, '', 1);
				echo json_encode(array('status'=>'default_task','total'=>$total,'total_bill'=>$total_billable,'total_project'=>$total_project,'total_time'=>$rfr,'total_bill_last'=>$total_bill_last,'total_per_project'=>$total_per_project));
				break;
			case 'default_project':
				$rfr = Timesheets::getTotalTimes($id_timesheet);
				$total_bill_last = Timesheets::getTotalBillableTimes($id_timesheet);
				echo json_encode(array('status'=>'default_project','total'=>$total,'total_bill'=>$total_billable,'total_time'=>$rfr,'total_bill_last'=>$total_bill_last));
				break;			
			}
		}
	}
	public function build_sorter($key, $key2 = null) 
	{
	    return function ($a, $b) use ($key, $key2) 
	    {
	    	$result = strnatcmp($a[$key], $b[$key]);
	    	if ($key2 != null && $result == 0)
	    	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
	
	public function actionApproveTasks()
	{
		if (isset($_POST['tasks']))
			{
				$tasks = array();
				$timesheets = array();


				foreach ($_POST['tasks'] as $task_time) 
				{
					$explode = explode('-', $task_time);
					if (count($explode) == 2) 
					{
						$tasks[] = (int) $explode[0];
						$timesheets[] = (int) $explode[1];	
					}
				}

				if (!empty($tasks))
				{	
					foreach ($tasks as $key => $value) {
						
						$id_task=UserTime::getTaskByIdUT($value);
						
						if(UserTime::checkifDefault($value)=='0'){
						$project_id=Projects::getProjectId($id_task);
						$type=Projects::getId('id_type',$project_id);
							$TM=Projects::getTMstatus($project_id);
							$customer_id=Projects::getCustomerByProject($project_id);
													
						if(($type=='27' || $type=='26' || $type=='28') && $TM=='0' && $customer_id<>'177'){
							
							$budgetedMDs=Projects::getBudgetedMD($project_id);
							$estimatedMDs=Projects::getEstimatedMD($project_id);

							$id_phase=ProjectsPhases::getPhaseIdByIdTask($id_task);
							$actualMDs=Projects::getApprovedActualDaysOf($id_phase);

							$currentMDs= (float) Yii::app()->db->createCommand("SELECT IFNULL(sum(amount),0)/8 FROM user_time WHERE `default`=0 AND amount > 0 and id=".$value." ")->queryScalar();
							$inculdingOffsetMDS=ProjectsPhases::getIncludingOffsetMDByPhase($id_phase);
							$phasename=ProjectsPhases::getPhaseByIdTask($id_task);

							if(round($budgetedMDs, 1)>round($estimatedMDs, 1)){
									
								$pname=Projects::getId('name',$project_id);

								echo json_encode(array('status'=>'failure', 'error'=>'Please adjust the Total Estimated Man Days on the Tasks screen to match the Total Budgeted Man Days for project <b>'.$pname.'</b>.'));
									exit;
							}

							if(round(($actualMDs+$currentMDs),2)>$inculdingOffsetMDS){
							//houda	
								$pname=Projects::getId('name',$project_id);
								$phasename=ProjectsPhases::getPhaseByIdTask($id_task);
								echo json_encode(array('status'=>'failure', 'error'=>'Time sheet entries for Project <b>'.$pname.'</b> and phase <b>'.$phasename.'</b> exceed the total estimated man days. Please request an offset.'));
									exit;
							}


						}
							}
								
					}
				
					foreach ($tasks as $key => $value) {
						
							$count=Yii::app()->db->createCommand("select distinct type , id_timesheet from timesheet_validations where id_user_time='".$value."' and checked<>'1' ")->queryRow();
					
							$message=" Validation Failed on timesheet ".$count['id_timesheet']." " ;

							if($count){

								$mes=Yii::app()->db->createCommand("select description from validations where id='".$count['type']."' ")->queryScalar();
								 $message .=" ".$mes." . Please check the validations on the top of this page before approving the time sheet entries.";
								 echo json_encode(array('status'=>'failure', 'error'=>$message));
									exit;
							}	

							}

								
					


					$tasks = implode(',', $tasks);
					
					Yii::app()->db->createCommand("UPDATE user_time SET status='1' WHERE id IN ($tasks)")->execute();
					$approved = array();
					$in_approval = array();
					foreach ($timesheets as $timesheet) 
					{
						$uid = Yii::app()->db->createCommand("SELECT id FROM user_time WHERE id_timesheet={$timesheet} AND status=0 AND amount > 0 LIMIT 1")->queryScalar();
						if ($uid == false) 
						{
							$approved[] = $timesheet;
								
						}
						else 
						{
							$in_approval[] = $timesheet;
						}	
					}
					if (!empty($approved))
						Yii::app()->db->createCommand("UPDATE timesheets SET status='".Timesheets::STATUS_APPROVED."' WHERE id IN (".implode(',', $approved).")")->execute();
					if (!empty($in_approval)){
						Yii::app()->db->createCommand("UPDATE timesheets SET status='".Timesheets::STATUS_IN_APPROVAL."' WHERE id IN (".implode(',', $in_approval).")")->execute();	
					}
					echo json_encode(array('status' => 'success', 'approved' => $approved,'in_approval' => $in_approval));
					exit;
				}
		}
		echo json_encode(array('status' => 'failed'));
		exit;
	}


	public function actionRejectTasksValid()
	{
		if (isset($_POST['tasks']))
		{
			$tasks = array();
			$timesheets = array();
			foreach ($_POST['tasks'] as $task_time) 
			{
				$explode = explode('-', $task_time);
				if (count($explode) == 2) 
				{
					$tasks[] = (int) $explode[0];
					$timesheets[] = (int) $explode[1];	
				}
			}

			$newArray= array();
			$newArray = array_count_values($timesheets);


			if (count($newArray)>1) {

				
				echo json_encode(array('status' => 'failed')); exit ;

			}else {
				echo json_encode(array('status' => 'success'));exit;
			}

				}
		echo json_encode(array('status' => 'failed'));
		exit;
	}
	
	
	public function actionRejectTasks()
	{	
		

		if (isset($_POST['tasks']))
		{
			$tasks = array();
			$timesheets = array();
			foreach ($_POST['tasks'] as $task_time) 
			{
				$explode = explode('-', $task_time);
				if (count($explode) == 2) 
				{
					$tasks[] = (int) $explode[0];
					$timesheets[] = (int) $explode[1];	
				}
			}

			
				
			if (!empty($tasks))
			{
				$tasks = implode(',', $tasks);
				$timesheets = implode(',', $timesheets);
				
				Yii::app()->db->createCommand("UPDATE user_time SET status='-1' WHERE id IN ($tasks)")->execute();
				Yii::app()->db->createCommand("UPDATE timesheets SET status='Rejected' WHERE id IN ( {$timesheets} )")->execute();
					
				echo json_encode(array('status' => 'success'));
				exit;
			}
		}
		echo json_encode(array('status' => 'failed'));
		exit;
	}
	
	public function actionRejectPHWeekendTasks()
	{	
		
	
		if(isset($_GET['id_timesheet'])){

			$id_timesheet=$_GET['id_timesheet'];

		}else{

		}
		
		$results=Timesheets::getUserTimebyTimesheet($id_timesheet);
		
		foreach ($results as $value) {

				Yii::app()->db->createCommand("UPDATE user_time SET status='-1' WHERE id='".$value['id']."' ")->execute();
			
							}
		Yii::app()->db->createCommand("UPDATE timesheets SET status='Rejected' WHERE id='".$id_timesheet."' ")->execute();
			$this->redirect(array('index')); exit;
	}

	public function actionApproval()
	{
		if(!GroupPermissions::checkPermissions('timesheets-timesheets_approval'))
		{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_REQUEST['SearchApprovalForm']) ? $_REQUEST['SearchApprovalForm'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
					'/timesheets/approval' => array(
							'label'=> 'Time Sheet Approval',
							'url' => array('timesheets/approval'),
							'itemOptions'=>array('class'=>'link'),
							'subtab' => '',
							'order' => Utils::getMenuOrder()+1,
							'search' => $searchArray,
					)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model = new SearchApprovalForm('search');
		$model->unsetAttributes();  // clear any default values
		$user = Yii::app()->user->id; $internal= false;
		if(!empty($searchArray) && isset($searchArray['id_project']) && substr($searchArray['id_project'], -1) == 'i' )
		{
			$searchArray['id_project'] = substr($searchArray['id_project'], 0, -1);
			$internal= true;
		}
		$select = "SELECT ut.*, 
					t.id as id_timesheet, t.timesheet_cod as code,t.status as timesheet_status, 
					u.firstname, u.lastname, 
				 	pt.id as id_task, pt.description as task, p.id as id_project, p.name as project 
				 FROM projects p 
				 LEFT JOIN projects_phases pp ON pp.id_project=p.id 
				 LEFT JOIN projects_tasks pt ON pt.id_project_phase=pp.id
				 LEFT JOIN user_time ut ON ut.id_task=pt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user";
		$where = " WHERE (p.project_manager='{$user}' || (p.business_manager='{$user}' AND t.id_user=p.project_manager)) AND ut.amount > 0 AND t.id_user <> '{$user}' AND ut.status = 0  and ut.`default`=0 ";
		if (!empty($searchArray) && !$internal)
		{
			$model->attributes = $searchArray;
			//if ($model->validate())
			{
				if (isset($model->id_customer) && !empty($model->id_customer))
				{
					$where .= " AND p.customer_id = {$model->id_customer}";
					$model->customer_name = Customers::getNameById($model->id_customer);
				}
				if (isset($model->id_project)  && !empty($model->id_project))
				{
					$where .= " AND p.id = {$model->id_project}";
				}
				if (isset($model->from) && !empty($model->from))
				{
					$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date >= '{$from}'";    
				}
				if (isset($model->to) && !empty($model->to))
				{
					$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date <= '{$to}'";
				}
				if (isset($model->status) && !empty($model->status))
				{
					$where .= " AND t.status = {$model->status}";	
				}
				else
				{
					$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
				}
				if (isset($model->id_user) && !empty($model->id_user))
				{
					$model->user_name = Users::getUsername($model->id_user);
					$where .= " AND ut.id_user = {$model->id_user}";
				}	
			}
			/*else 
			{
				$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
			}*/
			
		}
		else 
		{
			$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";	
		}
		$order = " ORDER BY ut.date ASC";

		
		 $user_time = Yii::app()->db->createCommand($select.$where.$order)->queryAll(); 
		
		$projects = array();
		foreach ($user_time as $row)
		{
			$projects[$row['id_project']]['project'] = $row['project'];
			$projects[$row['id_project']]['users'][$row['id_user']]['username'] = $row['firstname'].' '.$row['lastname'];
			$projects[$row['id_project']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['name'] = $row['code'];
			$projects[$row['id_project']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['times'][] = $row;
		}
			
		uasort($projects, $this->build_sorter('project'));

		$select_internal = "SELECT ut.*, 
					t.id as id_timesheet, t.timesheet_cod as code,t.status as timesheet_status, 
					u.firstname, u.lastname, 
				 	pt.id as id_task, pt.description as task, p.id as id_project, p.name as project 
				 FROM internal p 
				 LEFT JOIN internal_tasks pt ON pt.id_internal=p.id
				 LEFT JOIN user_time ut ON ut.id_task=pt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user";
		$where = " WHERE p.project_manager='{$user}' AND ut.amount > 0 AND ut.status = 0  and ut.`default`=3 ";
		if (!empty($searchArray)  && $internal)
		{
			
			$model->attributes = $searchArray;	
		
			
			//if ($model->validate())
			{			

				if (isset($model->id_project)  && !empty($model->id_project))
				{
					$where .= " AND p.id = {$model->id_project}";
				}
				if (isset($model->from) && !empty($model->from) )
				{
					$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date >= '{$from}'";    
				}
				if (isset($model->to) && !empty($model->to))
				{
					$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date <= '{$to}'";
				}
				if (isset($model->status) && !empty($model->status))
				{
					$where .= " AND t.status = {$model->status}";	
				}
				else
				{
					$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
				}
				if (isset($model->id_user) && !empty($model->id_user))
				{
					$model->user_name = Users::getUsername($model->id_user);
					$where .= " AND ut.id_user = {$model->id_user}";
				}	
			}
			/*else 
			{
				$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
			}*/
			
		}
		else 
		{
			$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";	
		}
		$order = " ORDER BY ut.date ASC";

		//print_r($select_internal.$where.$order);exit;

		$user_time_internal = Yii::app()->db->createCommand($select_internal.$where.$order)->queryAll(); 
		
		$internal_projects = array();
		foreach ($user_time_internal as $row)
		{
			$internal_projects[$row['id_project']]['project'] = $row['project'];
			$internal_projects[$row['id_project']]['users'][$row['id_user']]['username'] = $row['firstname'].' '.$row['lastname'];
			$internal_projects[$row['id_project']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['name'] = $row['code'];
			$internal_projects[$row['id_project']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['times'][] = $row;
		}
			
		uasort($internal_projects, $this->build_sorter('project'));




		$select_maintenance = "SELECT ut.*, 
					t.id as id_timesheet, t.timesheet_cod as code,t.status as timesheet_status, 
					u.firstname, u.lastname, 
				 	ms.id as id_task, ss.service as task, m.id_maintenance as id_project, CONCAT(c.name, ' Support - ',cl.codelkup) as project 
				 FROM maintenance_services ms 
				 LEFT JOIN maintenance m ON m.id_maintenance=ms.id_contract 
				 LEFT JOIN support_services ss ON ss.id=ms.id_service
				 LEFT JOIN customers c ON c.id=m.customer
				 LEFT JOIN codelkups cl ON cl.id=m.support_service
				 LEFT JOIN user_time ut ON ut.id_task=ms.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user
				 LEFT JOIN user_personal_details upd ON u.id = upd.id_user  ";
		$where_maintenance = " WHERE upd.line_manager = {$user}  AND ut.amount > 0 AND t.id_user <> {$user} AND ut.status = 0 and ut.default=2 ";
		if (!empty($searchArray)  && !$internal)
		{
			$model->attributes = $searchArray;
			//if ($model->validate())
			{
				if (isset($model->id_customer) && !empty($model->id_customer))
				{
					$where_maintenance .= " AND m.customer= {$model->id_customer}";
					$model->customer_name = Customers::getNameById($model->id_customer);
				}
				if (isset($model->id_project) && !empty($model->id_project))
				{
					//$where_maintenance .= " AND p.id = {$model->id_project}";
				}
				if (isset($model->from) && !empty($model->from))
				{
					$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date >= '{$from}'";    
				}
				if (isset($model->to) && !empty($model->to))
				{
					$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
					$where_maintenance .= " AND ut.date <= '{$to}'";
				}
				if (isset($model->status) && !empty($model->status))
				{
					$where_maintenance .= " AND t.status = {$model->status}";	
				}
				else
				{
					$where_maintenance .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
				}
				if (isset($model->id_user) && !empty($model->id_user))
				{
					$model->user_name = Users::getUsername($model->id_user);
					$where_maintenance .= " AND ut.id_user = {$model->id_user}";
				}	
			}
			/*else 
			{
				$where_maintenance .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
			}*/
			
		}
		else 
		{
			$where_maintenance .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";	
		}
		$order_maintenance = " ORDER BY ut.date ASC";


		
		 $user_time_maintenance = Yii::app()->db->createCommand($select_maintenance.$where_maintenance.$order_maintenance)->queryAll(); 
		
		$maintenance_tasks = array();
		foreach ($user_time_maintenance as $row)
		{
			$maintenance_tasks[$row['id_project']]['project'] = $row['project'];
			$maintenance_tasks[$row['id_project']]['users'][$row['id_user']]['username'] = $row['firstname'].' '.$row['lastname'];
			$maintenance_tasks[$row['id_project']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['name'] = $row['code'];
			$maintenance_tasks[$row['id_project']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['times'][] = $row;
		}
			
		uasort($maintenance_tasks, $this->build_sorter('project'));

		
		$select_default = "SELECT ut.*, dt.id as id_default_task,dt.name as name, dt.id_parent as id_parent,m.name as parent_phase,
				 t.timesheet_cod as code,u.firstname,u.lastname
				 FROM default_tasks dt
				 LEFT JOIN default_tasks m ON m.id = dt.id_parent
				 LEFT JOIN user_time ut ON ut.id_task=dt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON t.id_user = u.id
				 LEFT JOIN user_personal_details upd ON u.id = upd.id_user  
				 ";
		$where_default = " WHERE upd.line_manager = {$user}  AND ut.amount > 0 AND t.id_user <> {$user} AND ut.status = 0 and dt.id_parent is not null and ut.default=1 ";
		if (!empty($searchArray)  && !$internal)
		{
			$model->attributes = $searchArray;
			//if ($model->validate())
			{
				if (isset($model->from) && !empty($model->from))
				{
					$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
					$where_default .= " AND ut.date >= '{$from}'";    
				}
				if (isset($model->to) && !empty($model->to))
				{
					$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
					$where_default .= " AND ut.date <= '{$to}'";
				}
				if (isset($model->status) && !empty($model->status))
				{
					$where_default .= " AND t.status = {$model->status}";	
				}
				else
				{
					$where_default .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
				}
				if (isset($model->id_user) && !empty($model->id_user))
				{
					$where_default .= " AND ut.id_user = {$model->id_user}";
				}	
			}
			/*else 
			{
				$where_default .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
			}*/
		}
		else 
		{
			$where_default .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";	
		}
		$order = " ORDER BY ut.date ASC";
		$group = " group by m.id";

	
		 $user_time1 = Yii::app()->db->createCommand($select_default.$where_default)->queryAll(); 
		
		$default_tasks = array();
		foreach ($user_time1 as $row)
		{
			$default_tasks[$row['id_parent']]['parent_phase'] = $row['parent_phase'];
			$default_tasks[$row['id_parent']]['users'][$row['id_user']]['username'] = $row['firstname'].' '.$row['lastname'];
			$default_tasks[$row['id_parent']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['name'] = $row['code'];
			$default_tasks[$row['id_parent']]['users'][$row['id_user']]['timesheets'][$row['id_timesheet']]['times'][] = $row;
		}
			
		uasort($default_tasks, $this->build_sorter('parent_phase'));
		if (isset($_REQUEST['SearchApprovalForm']))
		{
			$html = $this->renderPartial('_approval_grid', array('projects' => $projects,'default_tasks'=>$default_tasks,'maintenance_tasks'=>$maintenance_tasks, 'internal_projects'=>$internal_projects), true, true);
			echo json_encode(array('status' => 'success', 'html' => $html));
			exit;
		}

		$line_manager= Yii::app()->user->id;
	
		$validations = Yii::app()->db->createCommand("select DISTINCT t.id, u.id_user, t.id_user_time ,u.id_timesheet ,t.type from timesheet_validations t , user_time u where u.id=t.id_user_time and u.id_user in (select id_user from user_personal_details p where p.line_manager='".$line_manager."') and checked=0 and t.type is not null	union select DISTINCT t.id, u.id_user, t.id_user_time ,u.id_timesheet ,t.type from timesheet_validations t , user_time u where u.id_timesheet=t.id_timesheet and t.id_user_time=0 and u.id_user in (select id_user from user_personal_details p where p.line_manager='".$line_manager."') and checked=0 and t.type is not null  group by  u.id_user, t.id_user_time ,u.id_timesheet ,t.type order by 5")->queryAll(); 
		$this->render('approval',array(
			'model' => $model, 
			'projects' => $projects,
			'default_tasks'=>$default_tasks,
			'maintenance_tasks'=>$maintenance_tasks,
			'internal_projects'=>$internal_projects,
			'validations'=>$validations
		));
	}
	/**
	 * Returns the list of tasks with their phases also
	 * @author Romeo Onisim
	 */
	public function actionGetTasks()
	{ 
		if (count($_POST) > 0)
		{
			$id_project = $_POST['id_project'];
			if(isset($id_project) && !empty($id_project))
			{
				$id_project = trim($id_project);
			}
			$id_user = Yii::app()->user->id; 
			if($id_project!='Support' && $id_project!='CA'){
			$tasks = Yii::app()->db->createCommand("SELECT CONCAT('m',ms.id) as id , m.support_service as id_phase, CONCAT(cu.name,' ',c.codelkup) as phase_name, ss.service as name 
									FROM maintenance_services ms
										LEFT JOIN maintenance m on m.id_maintenance=ms.id_contract
										LEFT JOIN customers cu on m.customer=cu.id
										LEFT JOIN support_services ss on ms.id_service=ss.id
										LEFT JOIN codelkups c on m.support_service=c.id
								WHERE m.support_service in ('501','502') and m.status='Active' and CONCAT(cu.name,' ',c.codelkup) like '%".$id_project."%'	and ss.access='Yes'							
								UNION								
								SELECT CONCAT('p',pt.id) as id, pp.id as id_phase, pp.description as phase_name, pt.description as name 
									FROM projects_tasks pt 
										LEFT JOIN projects_phases pp on pt.id_project_phase=pp.id
										LEFT JOIN projects p on pp.id_project=p.id
										LEFT JOIN user_task as ut on pt.id = ut.id_task
								WHERE ut.id_user='".$id_user."' and p.name like '%".$id_project."%' and pt.description not in ('FBRXXX','INTXXX')
								UNION								
								SELECT CONCAT('i',pt.id) as id, '7' as id_phase, 'Task(s)' as phase_name, pt.description as name 
									FROM internal_tasks pt 
										LEFT JOIN internal p on pt.id_internal=p.id
										LEFT JOIN user_internal as ut on pt.id = ut.id_task
								WHERE ut.id_user='".$id_user."' and p.name like '%".$id_project."%'
								UNION	
								SELECT  CONCAT('c',dt.id) as id , '828' as id_phase , 'CA', dt.name as description 
										FROM customers c ,default_tasks dt
								WHERE  dt.id_parent='828' and c.status=1 and c.name like '%".$id_project."%' and dt.name like '%".$id_project."%' 
									UNION	
								SELECT  CONCAT('d',dt.id) as id , '27' as id_phase , 'Support', name as description 
										FROM default_tasks dt  
								WHERE id_parent ='27' and id_maintenance is null and  name like '%".$id_project."%' group by description
									UNION
								SELECT  CONCAT('r',dt.id) as id , '1324' as id_phase , 'RSR', name as description 
										FROM default_tasks dt  
								WHERE id_parent ='1324' and id_maintenance is null and  name like '%".$id_project."%' group by description
												order by id_phase asc	")->queryAll();
			}else if ($id_project=='Support'){
				$tasks = Yii::app()->db->createCommand("SELECT  CONCAT('d',dt.id) as id , '27' as id_phase , 'Support' as phase_name , name 
										FROM default_tasks dt  
								WHERE id_parent ='27' and id_maintenance is null and  name like '%".$id_project."%'	  group by name
								UNION
									SELECT  CONCAT('d',dt.id) as id , '27' as id_phase , 'Support' as phase_name, name 
										FROM default_tasks dt  
								WHERE id_parent ='27' and id_maintenance is null and  'Support' like  '%".$id_project."%' 	 group by name  				
													")->queryAll();

			}else if ($id_project=='RSR'){
				$tasks = Yii::app()->db->createCommand("SELECT  CONCAT('r',dt.id) as id , '1324' as id_phase , 'RSR' as phase_name , name 
										FROM default_tasks dt  
								WHERE id_parent ='1324' and id_maintenance is null and  name like '%".$id_project."%'	  group by name
								UNION
									SELECT  CONCAT('r',dt.id) as id , '1324' as id_phase , 'RSR' as phase_name, name 
										FROM default_tasks dt  
								WHERE id_parent ='1324' and id_maintenance is null and  'RSR' like  '%".$id_project."%' 	 group by name  				
													")->queryAll();

			}else
			{
				 
				
				$tasks = Yii::app()->db->createCommand("SELECT CONCAT('c',dt.id) as id , '828' as id_phase , 'CA' as phase_name, dt.name as name
										FROM customers c ,default_tasks dt
								WHERE  c.name=dt.name and dt.id_parent='828' and c.status=1 and  'CA' like  '%".$id_project."%' 
									group by name ")->queryAll();
			}
		
			echo json_encode($tasks);
		}
	}
	/**
	 * The add default task functionality from the timesheets
	 * @author Iuliana Mihaies
	 */
	public function actionGetDefaultTasks()
	{
		$tasks_maintenance =  Yii::app()->db->createCommand()
			->select('t.id, t.name, t.billable, m.name as parent, m.id as id_parent')
			->from('default_tasks t')
			->join('default_tasks m', 'm.id = t.id_parent')
			->where('t.id_parent = ' . DefaultTasks::SUPPORT_TASK .' AND t.id_maintenance IS NULL order by t.name')
			->queryAll();
		/*$tasks_maintenance =  Yii::app()->db->createCommand()
			->select('t.id, c.name, t.billable, m.name as parent, m.id as id_parent')
			->from('default_tasks t')
			->join('default_tasks m', 'm.id = t.id_parent')
			->join('maintenance mn', 'mn.id_maintenance = t.id_maintenance')
			->join('customers c', 'c.id = mn.customer')
			->join('customers_contacts cc', 'cc.id_customer = mn.customer')
			->where('t.id_parent = ' . DefaultTasks::SUPPORT_TASK .' AND t.id_maintenance IS NOT NULL AND cc.access = "Yes"')
			->group('c.id')
			->queryAll();*/
		echo json_encode($tasks_maintenance);
	}
	/**
	 * The add task functionality from the timesheets
	 * @author Romeo Onisim
	 */
	public function actionAddTasks()
	{
		if(count($_POST) > 0)
		{
			$tasks = $_POST['tasks'];
			
			$id_timesheet = $_POST['id_timesheet'];
			$id_user = Yii::app()->user->id;
			$timesheet = Timesheets::model()->findByPk($id_timesheet);
			
			foreach($tasks as $id_task)
			{
				$type=substr($id_task,0,1);
				if($type=='p'){
					$id_task=substr($id_task, 1);
					// see if the task is already in the timesheet
					$count = UserTime::model()->count('id_task=' . $id_task . ' AND id_user=' . $id_user . ' AND `default`="0" AND id_timesheet = ' . $id_timesheet);
					if($count == '0')
					{	

						$insert = Yii::app()->db->createCommand()->insert('user_time', array(
								'id_task'=> $id_task,
								'id_user'=> $id_user,
								'amount' => '0.00',
								'comment'=> '',
								'id_timesheet' => $id_timesheet,
								'date'=> $timesheet->week_start,
								'default'=> 0
						));
					}
				}
				if($type=='i'){
					$id_task=substr($id_task, 1);
					// see if the task is already in the timesheet
					$count = UserTime::model()->count('id_task=' . $id_task . ' AND id_user=' . $id_user . ' AND `default`="3" AND id_timesheet = ' . $id_timesheet);
					if($count == '0')
					{	

						$insert = Yii::app()->db->createCommand()->insert('user_time', array(
								'id_task'=> $id_task,
								'id_user'=> $id_user,
								'amount' => '0.00',
								'comment'=> '',
								'id_timesheet' => $id_timesheet,
								'date'=> $timesheet->week_start,
								'default'=> 3
						));
					}
				}

				if($type=='d' || $type=='r'){
					$id_task=substr($id_task, 1);
					// see if the task is already in the timesheet
					$count = UserTime::model()->count('id_task=' . $id_task . ' AND id_user=' . $id_user . ' AND `default`="1" AND id_timesheet = ' . $id_timesheet);
					if($count == '0')
					{	

						$insert = Yii::app()->db->createCommand()->insert('user_time', array(
								'id_task'=> $id_task,
								'id_user'=> $id_user,
								'amount' => '0.00',
								'comment'=> '',
								'id_timesheet' => $id_timesheet,
								'date'=> $timesheet->week_start,
								'default'=> 1
						));
					}
				}

				if($type=='c'){
					$id_task=substr($id_task, 1);
					// see if the task is already in the timesheet
					$count = UserTime::model()->count('id_task=' . $id_task . ' AND id_user=' . $id_user . ' AND `default`="1" AND id_timesheet = ' . $id_timesheet);
					if($count == '0')
					{	

						$insert = Yii::app()->db->createCommand()->insert('user_time', array(
								'id_task'=> $id_task,
								'id_user'=> $id_user,
								'amount' => '0.00',
								'comment'=> '',
								'id_timesheet' => $id_timesheet,
								'date'=> $timesheet->week_start,
								'default'=> 1
						));
					}
				}

				if($type=='m'){
					$id_task=substr($id_task, 1);
					// see if the task is already in the timesheet
					$count = UserTime::model()->count('id_task=' . $id_task . ' AND id_user=' . $id_user . ' AND `default`="2" AND id_timesheet = ' . $id_timesheet);
					if($count == '0')
					{	

						$insert = Yii::app()->db->createCommand()->insert('user_time', array(
								'id_task'=> $id_task,
								'id_user'=> $id_user,
								'amount' => '0.00',
								'comment'=> '',
								'id_timesheet' => $id_timesheet,
								'date'=> $timesheet->week_start,
								'default'=> 2
						));
					}
				}
			}
		}
	}
	/**
	 * The add default task functionality from the timesheets
	 * @author Iuliana Mihaies
	 */
	public function actionAddDefaultTasks()
	{
		if(count($_POST) > 0)
		{
			$tasks = $_POST['tasks'];
			$id_timesheet = $_POST['id_timesheet'];
			$id_user = Yii::app()->user->id;
			$timesheet = Timesheets::model()->findByPk($id_timesheet);
			
			foreach($tasks as $id_task)
			{
				// see if the task is already in the timesheet
				$count = UserTime::model()->count('id_task=' . $id_task . ' AND id_user=' . $id_user . ' AND `default`="0" AND id_timesheet = ' . $id_timesheet);
				if($count == '0')
				{
					$insert = Yii::app()->db->createCommand()->insert('user_time', array(
							'id_task'=> $id_task,
							'id_user'=> $id_user,
							'amount' => '0.00',
							'comment'=> '',
							'id_timesheet' => $id_timesheet,
							'date'=> $timesheet->week_start,
							'default'=> 1
					));
				}
			}
		}
	}
	
	
	/**
	 * Send pending timesheets to all the groups that selected to receive this email
	 * @author Romeo Onisim
	 */
	public function actionSendUnsubmittedTimesheets()
	{
		$notif = EmailNotifications::getNotificationByUniqueName('unsubmitted_timesheets');
		if ($notif != NULL)
		{
			$cont = Timesheets::getPendingTimesheetsForAllUsers();
			
			if($cont != '')
			{
				$subject = 'Pending Time Sheets';
				$to_replace = array('{timesheets_pending}');
				$replace = array(Timesheets::getPendingTimesheetsForAllUsers());
				$body = str_replace($to_replace, $replace, $notif['message']);
				
					
					
				$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
				foreach($emails as $email)
				{
					if($email != '')
					{
						Yii::app()->mailer->AddAddress($email);
					}
				}
				
				Yii::app()->mailer->Subject  = $subject;
				Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				Yii::app()->mailer->Send(true);
			}
		}
	}
	public function actionsendTimesheetSnapshots()
	{
		$monday = strtotime('Monday last week');
		$sunday = strtotime('+6 days', $monday);
		
		$start_datetime = date('Y-m-d 00:00:00', $monday);
		$finish_datetime = date('Y-m-d 23:59:59', $sunday);
		$start_date = date('Y-m-d', $monday);
		$finish_date = date('Y-m-d', $sunday);
		
		//1-
		  $listOpenTimesheets = Timesheets::getOpenTimesheet();

		//2- BILLABILITY ALL
		  $listBillabiltyAll = Utils::formatNumber(Timesheets::getBillability());
		  $listBillabiltyAll12 = Utils::formatNumber(Timesheets::getBillabilityTwelveMonthsBack());
		//2- BILLABILITY PS
		  $listBillabiltyPS= Utils::formatNumber(Timesheets::getBillabilityByTeam('PS'));
		  $listBillabiltyPS12 = Utils::formatNumber(Timesheets::getBillabilityByTeamTwelveMonth('PS'));
		//2- BILLABILITY CS
		  $listBillabiltyCS= Utils::formatNumber(Timesheets::getBillabilityByTeam('CS'));
		  $listBillabiltyCS12 = Utils::formatNumber(Timesheets::getBillabilityByTeamTwelveMonth('CS'));
		//2- BILLABILITY OPS 
		  $listBillabiltyOPS= Utils::formatNumber(Timesheets::getBillabilityByTeam('OPS'));
		  $listBillabiltyOPS12 = Utils::formatNumber(Timesheets::getBillabilityByTeamTwelveMonth('OPS'));

		//3- Average Working Hours ALL
		  $listAverageWorkAll= Utils::formatNumber(Timesheets::getAverageWorkingHourperHeadperDay());
		  $listAverageWorkAll12 = Utils::formatNumber(Timesheets::getAverageWorkingHourperHeadperTwelveMonth());
		//3- Average Working Hours OPS
		  $listAverageWorkOPS= Utils::formatNumber(Timesheets::getAverageWorkingHourperTeamperDay('OPS'));
		  $listAverageWorkOPS12 = Utils::formatNumber(Timesheets::getAverageWorkingHourperTeamperTwelveMonth('OPS'));
		//3- Average Working Hours TECH
		  $listAverageWorkTECH= Utils::formatNumber(Timesheets::getAverageWorkingHourperTeamperDay('TECH'));
		  $listAverageWorkTECH12 = Utils::formatNumber(Timesheets::getAverageWorkingHourperTeamperTwelveMonth('TECH'));

		//4-Vacation 
		  $vac=Utils::formatNumber(Timesheets::getLeaveDuringLastMonth('13'));
		  $vac12=Utils::formatNumber(Timesheets::getLeaveDuringLastTwelveMonth('13'));

		//5-Sick Leaves 
		  $sic=Utils::formatNumber(Timesheets::getLeaveDuringLastMonth('12'));
		  $sic12=Utils::formatNumber(Timesheets::getLeaveDuringLastTwelveMonth('12'));

		//6- Day Offs 
		  $do=Utils::formatNumber(Timesheets::getLeaveDuringLastMonth('11'));
		  $do12=Utils::formatNumber(Timesheets::getLeaveDuringLastTwelveMonth('11'));

		//7- Travel / E-mail and Time Sheet Management 
		  $trav=Utils::formatNumber(Timesheets::getSNSINTERNALDuringLastMonth());
		  $trav12= Utils::formatNumber(Timesheets::getSNSINTERNALDuringLastTwelveMonth());

		//8- Top 5 Resource with Leaves
		  $topleaves=Timesheets::getTopResourceswithLeaves();

		//9- Top 5 Non Billable Resources
 		  $topnonbillable=Timesheets::getTop5NonBillableResources();

 		//10- Most Active Projects 
 		  $mostactive= Timesheets::getMostActiveProject();


		
		
		$notif = EmailNotifications::getNotificationByUniqueName('timesheet_snapshot');
		if ($notif != NULL)
		{
			$subject = $notif['name'];
			$currentMonth = date('F');
			

			$to_replace = array(
					'{month}',
					'{listOpenTimesheets}',

					'{listBillabiltyAll}',
					'{listBillabiltyAll12}',
					'{listBillabiltyPS}',
					'{listBillabiltyPS12}',
					'{listBillabiltyCS}',					
					'{listBillabiltyCS12}',				
					'{listBillabiltyOPS}',
					'{listBillabiltyOPS12}',

					'{listAverageWorkAll}',
					'{listAverageWorkAll12}',
					'{listAverageWorkOPS}',
					'{listAverageWorkOPS12}',
					'{listAverageWorkTECH}',					
					'{listAverageWorkTECH12}',	

					'{vac}',
					'{vac12}',

					'{sic}',
					'{sic12}',

					'{do}',
					'{do12}',


					'{trav}',
					'{trav12}',

					'{topleaves}',
					'{topnonbillable}',
					'{mostactive}'
			);
			$replace = array(
					Date('F', strtotime($currentMonth . " last month")),
					$listOpenTimesheets,

					$listBillabiltyAll,
					$listBillabiltyAll12,
					$listBillabiltyPS,
					$listBillabiltyPS12,
					$listBillabiltyCS,					
					$listBillabiltyCS12,					
					$listBillabiltyOPS,
					$listBillabiltyOPS12,

					$listAverageWorkAll,
					$listAverageWorkAll12,
					$listAverageWorkOPS,
					$listAverageWorkOPS12,
					$listAverageWorkTECH,					
					$listAverageWorkTECH12,	

					$vac,
					$vac12,

					$sic,
					$sic12,

					$do,
					$do12,


					$trav,
					$trav12,

					$topleaves,
					$topnonbillable,
					$mostactive
					
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)
			{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}
			
			//Yii::app()->mailer->AddCcs('Mario.Ghosn@sns-emea.com');
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
		
		echo $body;
	}  
	
	/**
	 * Send email to each user when they have pending timesheets an also to his line manager if he has one
	 * @author Romeo Onisim
	 */
	public function actionSendUnsubmittedTimesheetsForPerson()
	{
		$notif = EmailNotifications::getNotificationByUniqueName('personal_unsubmitted_timesheets');
		$usersArr = array();
		if ($notif != NULL)
		{
			$uTimesheets = Timesheets::getPendingTimesheetsForEachUser();
			$users = Timesheets::getPendingTimesheetsUsers();
			foreach($users as $user)
			{
				if 	(!EmailNotifications::isTimesheetNotificationSent($user['id_user'], 'timesheets', $notif['id'])){
					if($user['email'] != '' && !in_array($user['id_user'], $usersArr))
					{
						$lm="";
						$usersArr[] = $user['id_user'];
						
						$replaceName = $user['firstname'] ;
						$replaceDiv = Timesheets::showUserTimesheetsFromArray($uTimesheets, $user['id_user']);
						
						$subject = 'Pending Time Sheets';
						$to_replace = array(
									'{name}', 
									'{timesheets_pending}'
						);
						
						$replace = array(
									$replaceName,  
									$replaceDiv
						);
						
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->ClearCcs();
						$lm=Yii::app()->db->createCommand("select upd.email as email from user_personal_details upd where upd.id_user=(select line_manager from user_personal_details where id_user=".$user['id_user']." ) limit 1")->queryScalar();;		
						Yii::app()->mailer->AddAddress($user['email']);
						if (!empty($lm))
						{
							//Yii::app()->mailer->AddCcs($lm);
							Yii::app()->mailer->AddAddress($lm);
						}
						
						//Yii::app()->mailer->AddCcs("houda.nasser@sns-emea.com");
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send(true);	
						EmailNotifications::saveSentTimesheetNotification($user['id_user'], 'timesheets', $notif['id']);					
					}
				}
			}
		}
	}
	
	/**
	 * Refresh Total 
	 * @author Iuliana Mihaies
	 */
	public function refreshTotal($id_timesheet,$week_start,$type,$id_project = null)
	{
		$t = array();
		$t_b = array();
		for ($i=0; $i<7;$i++)
		{
			switch ($type){
			case 'total':
				$t[date('Y-m-d H:i:s', strtotime($week_start . ' + '.$i.' day'))] = Timesheets::getTotalTimes($id_timesheet, $week_start. ' + '.$i.' day');
				break;
			case 'billable':
				$t[date('Y-m-d H:i:s', strtotime($week_start . ' + '.$i.' day'))] = Timesheets::getTotalBillableTimes($id_timesheet, $week_start. ' + '.$i.' day');
				break;
			case 'default_project':
				$t[date('Y-m-d H:i:s', strtotime($week_start . ' + '.$i.' day'))] = Timesheets::getCurrentProjectTotalTime($id_timesheet, $id_project, $week_start. ' + '.$i.' day', 1);
				break;
			default:
				$t[date('Y-m-d H:i:s', strtotime($week_start . ' + '.$i.' day'))] = Timesheets::getCurrentProjectTotalTime($id_timesheet, $id_project, $week_start. ' + '.$i.' day');
				break;
			}
		}
		return $t;
	}
	
	public function actionGetValidation(){
		$date = array();
		for($i = 0 ; $i<= 6; $i++)
			$date[] = date('Y-m-d H:i:s', strtotime($_POST['date'] . ' + '.$i.' day'));
		echo json_encode( array('date'=>$date));
		exit;
	}
	
	/**
	 * returns number of timesheets with status new for the current user (logged in)
	 */
	public function actionCheckIfPendingTimesheets() {
		if(isset($_POST['id']))
		{
			$id= (int)$_POST['id'];
			$model =  Expenses::model()->findByPk($id);
			$count= Timesheets::getPendingTimesheetsCount();
			
			if ($count>1){

					echo json_encode(array('error' => 1, 'message'=> 'You have more than 1 Time Sheet Pending, Cannot create a new Expense Sheet'));			
				exit;
			}
			$is_billable = ExpensesDetails::getNumberBillableItem($model->id);
				if ($is_billable == 1 && $model->number_file == 0) {
					echo json_encode(array('error' => 1, 'message'=> "You have at least one billable item but you haven't uploaded a file"));		
				 exit;
				}

			$count2= Timesheets::getPendingTimesheetsCount2($model->startDate, $model->endDate);
		//	 print_r($count2);exit;
			if ($count2>0){

					echo json_encode(array('error' => 1, 'message'=> 'You have at least one timesheet falling under the same timeframe.'));			
				exit;
			}
			else{ 
				echo json_encode(array('status' =>'success'));			
				exit;
			}
		}		
	}

	public function actionsendrejectemails() {
			
		if(isset($_POST['reason']) && $_POST['reason']!=' ' && $_POST['reason']!=''){
					$reason=$_POST['reason'];
					$tasks=$_POST['tasks'];
						$body ="Your timesheet <a href=".Yii::app()->createAbsoluteUrl('timesheets/view', array('id'=>$tasks)).">".$tasks."</a> has been rejected for the following reason:  <br/> ".$reason." <br/> Thank you.";
				
			$select="select upd.email from timesheets t ,user_personal_details upd where t.id_user=upd.id_user and t.id=".$tasks." ";
			$rejected= Yii::app()->db->createCommand($select)->queryScalar();
			//add url
			$manager= Yii::app()->db->createCommand("select upd.email from user_personal_details upd  where upd.id_user=".Yii::app()->user->id." ")->queryScalar();
				$subject = "Timesheet - ".$tasks." has been rejected";


    			Yii::app()->mailer->ClearAddresses();
    			Yii::app()->mailer->ClearCcs();
				Yii::app()->mailer->AddAddress($rejected);
				//Yii::app()->mailer->AddCcs($manager);
				Yii::app()->mailer->AddAddress($manager);
				Yii::app()->mailer->Subject  = $subject;
				Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				Yii::app()->mailer->Send(true);
			

			}
				echo json_encode(array('status' => 'success'));
		}


	
	public function  actionsendVactionsNotRequested() {

		$list = Timesheets::getVactionsNotRequested();
		
			
		
		$notif = EmailNotifications::getNotificationByUniqueName('vacationnotrequested');
		if ($notif != NULL)
		{
			$subject = $notif['name'];
		
			$to_replace = array(
					'{list}',
					
			);
			$replace = array(
				
					$list
					
			);

			if (!empty($list)){
			$body = str_replace($to_replace, $replace, $notif['message']);
			 }else {
			  $body="There are no resources that has submitted vacations on their timesheets without requesting it from SNSit."; 
			} 

		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)
			{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}
			
			Yii::app()->mailer->Subject  = "Vacation submitted but not requested";
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
		
		echo $body;
	}  


	public static function actiongetParentDefaulttasks(){

			$id_task=$_POST['id_task'];		

			$result = Yii::app()->db->createCommand("SELECT id_parent FROM `default_tasks` where id='".$id_task."' ")->queryScalar();

			if ($result==3)
			{
				$name = Yii::app()->db->createCommand("SELECT name FROM `default_tasks` where id='".$id_task."' ")->queryScalar();
				if($name == 'Installation')
					$result=3.1;
			}

				if($id_task==229 || $id_task==412){ $result=0; }
			echo json_encode(array('id_parent' =>$result ));
 		}
 		public static function actiongetprojectdevtasksonly(){
 			$id_task= $_POST['id_task'];
			$team= Users::getUnitId(Yii::app()->user->id);
			if($team == 117 || $team == 116)
			{
				$result = Yii::app()->db->createCommand("select * from projects_tasks pt, projects_phases pp, projects p where pt.id=".$id_task." and pt.id_project_phase = pp.id and  pp.id_project= p.id and ((pt.description='Development' and pp.id_phase=7 ) or p.id_type = 28)")->queryScalar();
			}else{
				$result=0;
			}
			echo json_encode(array('count' =>$result));
 		}
 		public static function actiongetprojectdevtasks(){
			$id_task= $_POST['id_task'];	
			$date= $_POST['date'];	
			$team= Users::getUnitId(Yii::app()->user->id);
			$stat=''; $data=0;
			if($team == 117 || $team == 116)
			{
				$result = Yii::app()->db->createCommand("select count(1) from projects_tasks pt, projects_phases pp where pt.id=".$id_task."  and pt.id_project_phase = pp.id  and ( ( (pt.description like '%FBR%' or pt.type=1) and 0=(select count(1) from user_time where id_task=".$id_task."  and documented ='yes' and id_user=".Yii::app()->user->id.") ) or (pt.description='Development' and pp.id_phase in(6,7)) )")->queryScalar();
				$stat= Yii::app()->db->createCommand("select IFNULL(documented,'') as documented, IFNULL(fbr,'') as fbr  from user_time where id_task=".$id_task." and date='".date('Y-m-d', strtotime($date))."' and id_user=".Yii::app()->user->id." ")->queryRow();
			}else{
				$result=0;
			}
			if(ProjectsTasks::getNameById($id_task) == 'Development')
			{
				$data=1;
			}
			//print_r("select documented from user_time where id_task=".$id_task." and date=".date('Y-m-d', strtotime($date))." and id_user=".Yii::app()->user->id." ");exit;
			echo json_encode(array('count' =>$result, 'rad' => $stat['documented'], 'fbr'=> $data, 'dfbr' => $stat['fbr']  ));
 		}


 	public function actiongetTimeSheetGrid()
	{
		
	 	$timesheetid=$_POST['timesheet']; 
	 	$user=$_POST['user'];

	 
		$model = $this->loadModel($timesheetid);
		$data = Timesheets::getUserTimesheetTasksGrid($timesheetid,$user,0);
		$defaultTasks = Timesheets::getUserTimesheetTasksGrid($timesheetid,$user, 1);
		$maintenance_tasks = Timesheets::getUserTimesheetTasksGrid($timesheetid,$user,2);
	
	 	
	 	$timesheet=Timesheets::getUserTimesheetTasksGrid($timesheetid,$user);

		$weekendtit=(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452')?'weekendtit':'' ;

		$weekendtit2=(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452')?'weekendtit':'' ;

		$weekend=(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452')?'weekend':'' ;
		$weekend2=(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452')?'weekend':'' ;

		$weekendtask=(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452')?'weekendtask':'' ;
		$weekendtask2=(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452')?'weekendtask':'' ;


		$days1=$model->week_start." + 1 day";
		$days2=$model->week_start." + 2 day";
		$days3=$model->week_start." + 3 day";
		$days4=$model->week_start." + 4 day";
		$days5=$model->week_start." + 5 day";
		$days6=$model->week_start." + 6 day";

		$date1= date('Y-m-d', strtotime($days1));
		$date2= date('Y-m-d', strtotime($days2));
		$date3= date('Y-m-d', strtotime($days3));
		$date4= date('Y-m-d', strtotime($days4));
		$date5= date('Y-m-d', strtotime($days5));
		$date6= date('Y-m-d', strtotime($days6));


		$timesheetgrid="  
		<div id='timesheet_items_content' class='grid border-grid bordertop0' style='width:930px !important;'>
		<div class='thead'>
		<div class='column250 item side-borders side-borders-first bold'>Project Name</div>
		<div class='column80 item side-borders bold ".$weekendtit."'>SUN ". date('j', strtotime($model->week_start))."</div>
		<div class='column80 item side-borders bold'>MON ". date('j', strtotime($model->week_start. ' + 1 day'))."</div>
		<div class='column80 item side-borders bold'>TUE ". date('j', strtotime($model->week_start. ' + 2 day'))."</div>
		<div class='column80 item side-borders bold'>WED ". date('j', strtotime($model->week_start. ' + 3 day'))."</div>
		<div class='column80 item side-borders bold'>THU ". date('j', strtotime($model->week_start. ' + 4 day'))."</div>
		<div class='column80 item side-borders bold ".$weekendtit2."'>FRI ". date('j', strtotime($model->week_start. ' + 5 day'))."</div>
		<div class='column80 item side-borders bold weekendtit'>SAT ". date('j', strtotime($model->week_start. ' + 6 day'))."</div>
		<div class='column80 item side-borders side-borders-last bold'>TOTAL</div>
		</div>";
 $showPrj = 0; $prjArr = array(); $taskArr = array(); $prj = 0;

foreach ($data as $task) {
	
	if ($task['id_project'] != $prj && !in_array($task['id_project'], $prjArr)) { 
		
		$prj = $task['id_project']; array_push($prjArr, $task['id_project']);

	$day0=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $model->week_start);
	$day1=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days1);
	$day2=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days2);
	$day3=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days3);
	$day4=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days4);
	$day5=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days5);
	$day6=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days6);
if(Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'])>0){
		
			$timesheetgrid.="	<div class='thead project_thead'> 
				<div class='column250 item side-borders side-borders-first projectName'>
					
					<span class='plus-minus' data-collapsed='1' href='javascript:void(0)'></span>&nbsp;&nbsp;&nbsp;
					".$task['project_name']."</div>

				<div class='column80 item side-borders ".$weekend."' data-date='". date('Y-m-d H:i:s', strtotime($model->week_start))."' >".$day0."</div>
				
				<div class='column80 item side-borders' data-date='".$date1."' > ".$day1."</div>
				
				<div class='column80 item side-borders' data-date='".$date2."' > ".$day2."</div>
				
				<div class='column80 item side-borders' data-date='".$date3."' > ".$day3."</div>
				
				<div class='column80 item side-borders' data-date='".$date4."' > ".$day4."</div>
				
				<div class='column80 item side-borders ".$weekend2." ' data-date='".$date5."' > ".$day5."</div>
				
				<div class='column80 item side-borders weekend' data-date='".$date6."'> ".$day6."</div>
				<div class='column80 item side-borders side-borders-last'>".Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'])."</div>
			</div>";
			}
}
if (!in_array($task['id_task'], $taskArr)) {

 array_push($taskArr, $task['id_task']);


	$resources0=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $model->week_start);
	$resources1=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days1);
	$resources2=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days2);
	$resources3=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days3);
	$resources4=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days4);
	$resources5=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days5);
	$resources6=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days6);



		if(Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'])>0){

			if(Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'])>0){

	$timesheetgrid.="<div class='thead task_thead' data-id='". $task['id_task']."' data-default='0' data-billable='". $task['task_billabillity']."'>
				<div class='column250 item side-borders side-borders-first'>&nbsp&nbsp&nbsp". $task['task_description']."</div>
					<div class='column80 item side-borders ".$weekendtask."'>
					
						<input type='text' readonly='readonly' name='' value='". $resources0['amount']."' data-date='". date('Y-m-d H:i:s', strtotime($model->week_start))."' data-comment='". $resources0['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
					
						<input type='text' readonly='readonly' name='' value='". $resources1['amount']."' data-date='".$date1."' data-comment='". $resources1['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
					
						<input type='text' readonly='readonly' name='' value='". $resources2['amount']."' data-date='".$date2."' data-comment='". $resources2['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
					
						<input type='text' readonly='readonly' name='' value='". $resources3['amount']."' data-date='".$date3."' data-comment='". $resources3['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
						
						<input type='text' readonly='readonly' name='' value='". $resources4['amount']."' data-date='".$date4."' data-comment='". $resources4['comment'] ."' />
					</div>
					<div class='column80 item side-borders ".$weekendtask2."'>
					
						<input type='text' readonly='readonly' name='' value='". $resources5['amount']."' data-date='".$date5."' data-comment='". $resources5['comment'] ."' />
					</div>
					<div class='column80 item side-borders weekendtask'>
					
						<input type='text' readonly='readonly' name='' value='". $resources6['amount']."' data-date='".$date6."' data-comment='". $resources6['comment'] ."' />
					</div>	
				<div class='column80 item side-borders side-borders-last'><span>". Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'])."</span></div>
			</div>";
		}

}

}

}

foreach ($maintenance_tasks as $task) {
	
	if ($task['id_project'] != $prj && !in_array($task['id_project'], $prjArr)) { 
		
		$prj = $task['id_project']; array_push($prjArr, $task['id_project']);

	$day0=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $model->week_start);
	$day1=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days1);
	$day2=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days2);
	$day3=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days3);
	$day4=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days4);
	$day5=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days5);
	$day6=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'], $days6);
if(Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'])>0){
		
			$timesheetgrid.="	<div class='thead project_thead'> 
				<div class='column250 item side-borders side-borders-first projectName'>
					
					<span class='plus-minus' data-collapsed='1' href='javascript:void(0)'></span>&nbsp;&nbsp;&nbsp;
					".$task['project_name']."</div>

				<div class='column80 item side-borders ".$weekend."' data-date='". date('Y-m-d H:i:s', strtotime($model->week_start))."' >".$day0."</div>
				
				<div class='column80 item side-borders' data-date='".$date1."' > ".$day1."</div>
				
				<div class='column80 item side-borders' data-date='".$date2."' > ".$day2."</div>
				
				<div class='column80 item side-borders' data-date='".$date3."' > ".$day3."</div>
				
				<div class='column80 item side-borders' data-date='".$date4."' > ".$day4."</div>
				
				<div class='column80 item side-borders ".$weekend2." ' data-date='".$date5."' > ".$day5."</div>
				
				<div class='column80 item side-borders weekend' data-date='".$date6."'> ".$day6."</div>
				<div class='column80 item side-borders side-borders-last'>".Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'])."</div>
			</div>";
			}
}
if (!in_array($task['id_task'], $taskArr)) {

 array_push($taskArr, $task['id_task']);


	$resources0=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $model->week_start);
	$resources1=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days1);
	$resources2=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days2);
	$resources3=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days3);
	$resources4=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days4);
	$resources5=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days5);
	$resources6=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'], $days6);



		if(Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $task['id_project'])>0){

			if(Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'])>0){

	$timesheetgrid.="<div class='thead task_thead' data-id='". $task['id_task']."' data-default='0' data-billable='". $task['task_billabillity']."'>
				<div class='column250 item side-borders side-borders-first'>&nbsp&nbsp&nbsp". $task['task_description']."</div>
					<div class='column80 item side-borders ".$weekendtask."'>
					
						<input type='text' readonly='readonly' name='' value='". $resources0['amount']."' data-date='". date('Y-m-d H:i:s', strtotime($model->week_start))."' data-comment='". $resources0['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
					
						<input type='text' readonly='readonly' name='' value='". $resources1['amount']."' data-date='".$date1."' data-comment='". $resources1['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
					
						<input type='text' readonly='readonly' name='' value='". $resources2['amount']."' data-date='".$date2."' data-comment='". $resources2['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
					
						<input type='text' readonly='readonly' name='' value='". $resources3['amount']."' data-date='".$date3."' data-comment='". $resources3['comment'] ."' />
					</div>
					<div class='column80 item side-borders'>
						
						<input type='text' readonly='readonly' name='' value='". $resources4['amount']."' data-date='".$date4."' data-comment='". $resources4['comment'] ."' />
					</div>
					<div class='column80 item side-borders ".$weekendtask2."'>
					
						<input type='text' readonly='readonly' name='' value='". $resources5['amount']."' data-date='".$date5."' data-comment='". $resources5['comment'] ."' />
					</div>
					<div class='column80 item side-borders weekendtask'>
					
						<input type='text' readonly='readonly' name='' value='". $resources6['amount']."' data-date='".$date6."' data-comment='". $resources6['comment'] ."' />
					</div>	
				<div class='column80 item side-borders side-borders-last'><span>". Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $task['id_task'])."</span></div>
			</div>";
		}

}

}

}


 $parentsArr = array(); 

 foreach($defaultTasks as $default_task) { 


	$default0=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $model->week_start,1);
	$default1=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $days1,1);
	$default2=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $days2,1);
	$default3=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $days3,1);
	$default4=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $days4,1);
	$default5=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $days5,1);
	$default6=Timesheets::getCurrentProjectTotalTimeGrid($model->id, $user, $default_task['id_project'], $days6,1);


 	if(!in_array($default_task['parent'], $parentsArr)) {

 		 array_push($parentsArr, $default_task['parent']); 
 		 if(Timesheets::getCurrentProjectTotalTimeGrid($model->id,$user, $default_task['id_project'], '', 1) >0){
 		
	$timesheetgrid.="<div class='thead project_thead default'> 
				<div class='column250 item side-borders side-borders-first'>
				
					<span class='plus-minus' data-collapsed='1' href='javascript:void(0)'></span>&nbsp;&nbsp;&nbsp;
					". $default_task['parent']."
				</div>
				
				<div class='column80 item side-borders ".$weekendtask."' data-date='".date('Y-m-d H:i:s', strtotime($model->week_start))."'>
					". $default0."
				</div>
				
				<div class='column80 item side-borders' data-date='". $date1."'>
					". $default1."
				</div>
				
				<div class='column80 item side-borders' data-date='".$date2."'>
					". $default2."
				</div>
			
				<div class='column80 item side-borders' data-date='".$date3."'>
					". $default3."
				</div>
				
				<div class='column80 item side-borders' data-date='".$date4."'>
					". $default4."
				</div>
				
				<div class='column80 item side-borders  ".$weekendtask2."' data-date='".$date5."'>
					". $default5."
				</div>
				
				<div class='column80 item side-borders weekendtask' data-date='".$date6."'>
					". $default6."
				</div>
				<div class='column80 item side-borders side-borders-last'>
					".Timesheets::getCurrentProjectTotalTimeGrid($model->id,$user, $default_task['id_project'], '', 1)."					
				</div>
			</div>";
		}

 		}


	$res0=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $model->week_start ,1);
	$res1=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $days1,1);
	$res2=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $days2,1);
	$res3=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $days3,1);
	$res4=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $days4,1);
	$res5=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $days5,1);
	$res6=Timesheets::getUserTimeOnTaskByDayGrid($model->id, $user, $default_task['id'], $days6,1);
 

 	if(Timesheets::getUserTimeOnTaskByDayGrid($model->id,$user, $default_task['id'], '', 1)>0){
	$timesheetgrid.="<div class='thead task_thead default' data-id='". $default_task['id']."' data-default='1' data-billable='". $default_task['billable']."'>
			<div class='column250 tss item side-borders side-borders-first' style='line-height:14px'>". $default_task['name']."</div>
				<div class='column80 tss item side-borders ".$weekendtask."'>
				
					<input type='text' readonly='readonly' name='' value='".$res0['amount']."' />
				</div>
				<div class='column80 tss item side-borders '>
					
					<input type='text' readonly='readonly' name='' value='".$res1['amount']."' />
				</div>
				<div class='column80 tss item side-borders '>
				
					<input type='text' readonly='readonly' name='' value='".$res2['amount']."'  />
				</div>
				<div class='column80 tss item side-borders '>
				
					<input type='text' readonly='readonly' name='' value='".$res3['amount']."'  />
				</div>
				<div class='column80 tss item side-borders '>
				
					<input type='text' readonly='readonly' name='' value='".$res4['amount']."' />
				</div>
				<div class='column80 tss item side-borders  ". $weekendtask2 ."'>
				
					<input type='text' readonly='readonly' name='' value='". $res5['amount']."'  />
				</div>
				<div class='column80 tss item side-borders  weekendtask'>
				
					<input type='text' readonly='readonly' name='' value='". $res6['amount']."'  />
				</div>	
			<div class='column80 tss item side-borders side-borders-last'><span>". Timesheets::getUserTimeOnTaskByDayGrid($model->id,$user, $default_task['id'], '', 1) ."</span></div>
		</div>";

	}
	}

	$total0=Timesheets::getTotalTimesGrid($model->id, $user, $model->week_start);
	$total1=Timesheets::getTotalTimesGrid($model->id, $user, $days1);
	$total2=Timesheets::getTotalTimesGrid($model->id, $user, $days2);
	$total3=Timesheets::getTotalTimesGrid($model->id, $user, $days3);
	$total4=Timesheets::getTotalTimesGrid($model->id, $user, $days4);
	$total5=Timesheets::getTotalTimesGrid($model->id, $user, $days5);
	$total6=Timesheets::getTotalTimesGrid($model->id, $user, $days6);

	$totalbill0=Timesheets::getTotalBillableTimesGrid($model->id, $user, $model->week_start);
	$totalbill1=Timesheets::getTotalBillableTimesGrid($model->id, $user, $days1);
	$totalbill2=Timesheets::getTotalBillableTimesGrid($model->id, $user, $days2);
	$totalbill3=Timesheets::getTotalBillableTimesGrid($model->id, $user, $days3);
	$totalbill4=Timesheets::getTotalBillableTimesGrid($model->id, $user, $days4);
	$totalbill5=Timesheets::getTotalBillableTimesGrid($model->id, $user, $days5);
	$totalbill6=Timesheets::getTotalBillableTimesGrid($model->id, $user, $days6);

	$billable0=Timesheets::calculateBillabilityGrid($total0,$user,$totalbill0);
	$billable1=Timesheets::calculateBillabilityGrid($total1,$user,$totalbill1);
	$billable2=Timesheets::calculateBillabilityGrid($total2,$user,$totalbill2);
	$billable3=Timesheets::calculateBillabilityGrid($total3,$user,$totalbill3);
	$billable4=Timesheets::calculateBillabilityGrid($total4,$user,$totalbill4);
	$billable5=Timesheets::calculateBillabilityGrid($total5,$user,$totalbill5);
	$billable6=Timesheets::calculateBillabilityGrid($total6,$user,$totalbill6);

$timesheetgrid.="<div class='project_thead hide'></div>
	<div class='thead total_hours'>
		<div class='column250 item side-borders side-borders-first bold'>Total</div>
			
			<div class='column80 item side-borders' data-date='".date('Y-m-d H:i:s', strtotime($model->week_start))."'>".$total0."</div>
			
			<div class='column80 item side-borders' data-date='". $date1."'>".$total1."</div>
			
			<div class='column80 item side-borders' data-date='". $date2."'>".$total2."</div>
		
			<div class='column80 item side-borders' data-date='". $date3."'>".$total3."</div>
			
			<div class='column80 item side-borders' data-date='". $date4."'>".$total4."</div>
		
			<div class='column80 item side-borders' data-date='". $date5."'>".$total5."</div>
			
			<div class='column80 item side-borders' data-date='". $date6."'>".$total6."</div>
		<div class='column80 item side-borders side-borders-last '>". Timesheets::getTotalTimesGrid($model->id,$user)."</div>
	</div>
	<div class='thead total_billable'>
		<div class='column250 item side-borders side-borders-first bold'>Total Billable</div>
			
			<div class='column80 item side-borders' data-date='".date('Y-m-d H:i:s', strtotime($model->week_start))."'>".$totalbill0."</div>
		
			<div class='column80 item side-borders' data-date='". $date1."'>".$totalbill1."</div>
			
			<div class='column80 item side-borders' data-date='". $date2."'>".$totalbill2."</div>
			
			<div class='column80 item side-borders' data-date='". $date3."'>".$totalbill3."</div>
		
			<div class='column80 item side-borders' data-date='". $date4."'>".$totalbill4."</div>
		
			<div class='column80 item side-borders' data-date='". $date5."'>".$totalbill5."</div>
			
			<div class='column80 item side-borders' data-date='". $date6."'>".$totalbill6."</div>
		<div class='column80 item side-borders side-borders-last'>". Timesheets::getTotalBillableTimesGrid($model->id,$user)."</div>
	</div>
	<div class='thead billability'>
		<div class='column250 item side-borders side-borders-first bold'>Billabillity %</div>
			
			<div class='column80 item side-borders' data-date='".date('Y-m-d H:i:s', strtotime($model->week_start))."'>".$billable0."</div>
		
			<div class='column80 item side-borders' data-date='". $date1."'>". $billable1."</div>
		
			<div class='column80 item side-borders' data-date='". $date2."'>". $billable2."</div>
			
			<div class='column80 item side-borders' data-date='". $date3."'>". $billable3."</div>
		
			<div class='column80 item side-borders' data-date='". $date4."'>". $billable4."</div>
		
			<div class='column80 item side-borders' data-date='". $date5."'>". $billable5."</div>
		
			<div class='column80 item side-borders' data-date='". $date6."'>". $billable6."</div>
		<div class='column80 item side-borders side-borders-last'>". Timesheets::calculateBillabilityGrid(Timesheets::getTotalTimesGrid($model->id,$user),$user,Timesheets::getTotalBillableTimesGrid($model->id,$user))."</div>
	</div>";



	$timesheetgrid.=" </div> ";
	

	 	echo json_encode(array('status'=>'success', 'timesheetgrid' =>$timesheetgrid, 'period'=>date('d/m/Y', strtotime($model->week_start)).' To '. date('d/m/Y', strtotime($model->week_start. ' + 6 day')) ));


	}

	public static function actioncheckvalidation(){

			$validation=$_POST['val'];		
			Yii::app()->db->createCommand("UPDATE timesheet_validations SET checked = 1 WHERE id = '{$validation}'")->execute();
		 	echo json_encode(array('status'=>'success'));
 		}
	}
	