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
				'actions'=>array('generate', 'sendUnsubmittedTimesheets', 'getTimeSheetGrid' , 'sendUnsubmittedTimesheetsForPerson' ,'sendVactionsNotRequested' , 'sendTimesheetSnapshots' , 'getParentDefaulttasks'),
				'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'current', 'saveItem', 'copyLast', 'ValidateProject', 'validateSRNumber','VacationDaysHRRequest',       'ValidateAllowedDays','changeTimesheetStatus', 'deleteItemFromTimesheet', 'approval', 'getTasks', 'addTasks','getDefaultTasks',
					 'addDefaultTasks', 'approveTasks', 'rejectTasks','rejectTasksValid','sendrejectemails','getValidation', 'checkIfPendingTimesheets'),
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
					'default_tasks'=>$defaultTasks
			), false, true);
		}
		else
		{
			$this->render('view',array(
					'model'=>$model,
					'data'=>$timeSheetTasks,
					'default_tasks'=>$defaultTasks
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
	
	/**
	 * Save into an item
	 * @author Romeo Onisim
	 */
	public function actionSaveItem()
	{
		if(count($_POST) > 0)
		{
			$ticket_id= $_POST['ticket']; 
			$id_task = (int)$_POST['id_task'];
			$date = date('Y-m-d H:i:s', strtotime($_POST['date']));
			$hours = (float)$_POST['hours'];
			$hours = round($hours * 4) / 4;
			$hours = number_format($hours, 2, '.', '');
			$comment = $_POST['comment'];
			$default = $_POST['isDefault'];
			$id_timesheet = $_POST['id_timesheet'];
			// format lieu of
			$lieu_of = trim($_POST['lieu_of']);
			if($lieu_of != '')
			{
				$myDateTime = DateTime::createFromFormat('d/m/Y', $lieu_of);
				$lieu_of = $myDateTime->format('Y-m-d 00:00:00');
			}
			
			Timesheets::updateOrCreateNewItem($id_task, $date, Yii::app()->user->id, $default, $id_timesheet, $hours, $comment, $lieu_of, $ticket_id);
			
			$returnArr['hours'] = $hours;
			$returnArr['comment'] = $comment;
			$returnArr['lieu_of'] = $lieu_of;
			$returnArr['ticket'] = $ticket_id;
			
			echo json_encode($returnArr);
		}			
	}
	
	
	public function actionValidateProject()
	{	
		$id_task = (int)$_POST['id_task'];
		$stat= Yii::app()->db->createCommand("select status	from projects p , projects_phases pp , projects_tasks pt	where p.id=pp.id_project and pt.id_project_phase=pp.id and pt.id=".$id_task ."")->queryScalar();
					
		$returnArr['stat'] = $stat;		
			
		echo json_encode($returnArr);
				
	}

	public function actionvalidateSRNumber()
	{	
		$nosr= array();
		$tickets = explode(',', $_POST['ticket']);

		

		foreach ($tickets as $value) {
			$count= Yii::app()->db->createCommand("select count(distinct id) from support_desk where id='".$value."'")->queryScalar();
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
	
	/**
	 * Changes the timesheet status based on the status variable
	 */
	public function actionChangeTimesheetStatus()
	{
		if (isset($_POST['id_timesheet'], $_POST['status']))
		{
			$id_timesheet = (int)$_POST['id_timesheet'];

			$status = Timesheets::changeTimesheetStatus($id_timesheet, $_POST['status']);
			
			if ($status > 0)
			{
				if ($_POST['status'] == Timesheets::STATUS_SUBMITTED)
				{
					Yii::app()->db->createCommand("UPDATE user_time SET status = 0 WHERE id_timesheet = '{$id_timesheet}'")->execute();
				}
				elseif ( $_POST['status'] == Timesheets::STATUS_NEW)
					echo json_encode(array('success' => 1, 'status' => Timesheets::STATUS_NEW));
				else
					echo json_encode(array('success' => 1));
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
					
					//offset validations
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
							$actualMDs=Projects::getActualMD($project_id);
							$currentMDs= (float) Yii::app()->db->createCommand("SELECT IFNULL(sum(amount),0)/8 FROM user_time WHERE `default`=0 AND amount > 0 and id=".$value." ")->queryScalar();
							$inculdingOffsetMDS=Projects::getIncludingOffsetMD($project_id);
							$phasename=ProjectsPhases::getPhaseByIdTask($id_task);

							if($budgetedMDs>$estimatedMDs){
									
								$pname=Projects::getId('name',$project_id);

								echo json_encode(array('status'=>'failure', 'error'=>'Please adjust the Total Estimated Man Days on the Tasks screen to match the Total Budgeted Man Days for project <b>'.$pname.'</b>.'));
									exit;
							}

							if($actualMDs>$inculdingOffsetMDS){
								
								$pname=Projects::getId('name',$project_id);
								$phasename=ProjectsPhases::getPhaseByIdTask($id_task);
								echo json_encode(array('status'=>'failure', 'error'=>'Time sheet entries for Project <b>'.$pname.'</b> and phase <b>'.$phasename.'</b> exceed the total estimated man days. Please request an offset.'));
									exit;
							}


						}
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
		$user = Yii::app()->user->id;
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
		if (!empty($searchArray))
		{
			$model->attributes = $searchArray;
			if ($model->validate())
			{
				if ($model->id_customer)
				{
					$where .= " AND p.customer_id = {$model->id_customer}";
					$model->customer_name = Customers::getNameById($model->id_customer);
				}
				if ($model->id_project)
				{
					$where .= " AND p.id = {$model->id_project}";
				}
				if ($model->from)
				{
					$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date >= '{$from}'";    
				}
				if ($model->to)
				{
					$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
					$where .= " AND ut.date <= '{$to}'";
				}
				if ($model->status)
				{
					$where .= " AND t.status = {$model->status}";	
				}
				else
				{
					$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
				}
				if ($model->id_user)
				{
					$model->user_name = Users::getUsername($model->id_user);
					$where .= " AND ut.id_user = {$model->id_user}";
				}	
			}
			else 
			{
				$where .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
			}
			
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
		if (!empty($searchArray))
		{
			$model->attributes = $searchArray;
			if ($model->validate())
			{
				if ($model->from)
				{
					$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
					$where_default .= " AND ut.date >= '{$from}'";    
				}
				if ($model->to)
				{
					$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
					$where_default .= " AND ut.date <= '{$to}'";
				}
				if ($model->status)
				{
					$where_default .= " AND t.status = {$model->status}";	
				}
				else
				{
					$where_default .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
				}
				if ($model->id_user)
				{
					$where_default .= " AND ut.id_user = {$model->id_user}";
				}	
			}
			else 
			{
				$where_default .= " AND ( t.status = '".Timesheets::STATUS_SUBMITTED."' OR t.status = '".Timesheets::STATUS_IN_APPROVAL."')";
			}
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
			$html = $this->renderPartial('_approval_grid', array('projects' => $projects,'default_tasks'=>$default_tasks), true, true);
			echo json_encode(array('status' => 'success', 'html' => $html));
			exit;
		}
		$this->render('approval',array(
			'model' => $model, 
			'projects' => $projects,
			'default_tasks'=>$default_tasks
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
			$id_project = (int)$_POST['id_project'];
			$id_user = Yii::app()->user->id;
			
			$tasks = Yii::app()->db->createCommand()
					->select('projects_tasks.id, projects_phases.id as id_phase, projects_phases.description as phase_name, projects_tasks.description as name')
					->from('projects_tasks')
					->join('projects_phases', 'projects_tasks.id_project_phase = projects_phases.id')					
					->join('user_task', 'projects_tasks.id = user_task.id_task')
					->where('user_task.id_user='.$id_user.' and projects_phases.id_project = ' . $id_project)->queryAll();
			
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
		if ($notif != NULL)
		{
			$uTimesheets = Timesheets::getPendingTimesheetsForEachUser();
			$usersArr = array();
			foreach($uTimesheets as $user_pend_timesheet)
			{
				if($user_pend_timesheet['email'] != '' && !in_array($user_pend_timesheet['id_user'], $usersArr))
				{
					$usersArr[] = $user_pend_timesheet['id_user'];
					
					$replaceName = $user_pend_timesheet['firstname'] ;
					$replaceDiv = Timesheets::showUserTimesheetsFromArray($uTimesheets, $user_pend_timesheet['id_user']);
					
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
					
										
					Yii::app()->mailer->AddAddress($user_pend_timesheet['email']);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);
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
		$count= Timesheets::getPendingTimesheetsCount();
		
		if ($count>1){

				echo json_encode(array('error' => 1));			
			exit;
		}else{

			echo json_encode(array('status' => 'success'));
		}
		
	}


	public function actionsendrejectemails() {
			
		if(isset($_POST['reason']) && $_POST['reason']!=' ' && $_POST['reason']!=''){
					$reason=$_POST['reason'];
					$tasks=$_POST['tasks'];
						$body ="Your timesheet ".$tasks." has been rejected for the following reason:  <br/> ".$reason." <br/> Thank you.";
				
			$select="select upd.email from timesheets t ,user_personal_details upd where t.id_user=upd.id_user and t.id=".$tasks." ";
			$rejected= Yii::app()->db->createCommand($select)->queryScalar();
			
			$manager= Yii::app()->db->createCommand("select upd.email from user_personal_details upd  where upd.id_user=".Yii::app()->user->id." ")->queryScalar();
				$subject = "Timesheet - ".$tasks." has been rejected";


    			Yii::app()->mailer->ClearAddresses();
    			Yii::app()->mailer->ClearCcs();
				Yii::app()->mailer->AddAddress($rejected);
				Yii::app()->mailer->AddCcs($manager);
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

			if(isset($_POST['id_task'])){
			$id_task=$_POST['id_task'];		

			$result = Yii::app()->db->createCommand("SELECT id_parent FROM `default_tasks` where id='".$id_task."' ")->queryScalar();
				if($id_task==229 || $id_task==412){ $result=0; }
			echo json_encode(array('id_parent' =>$result ));
		}
 		}




 	public function actiongetTimeSheetGrid()
	{
		
	 	$timesheetid=$_POST['timesheet']; 
	 	$user=$_POST['user'];

	 
		$model = $this->loadModel($timesheetid);
		$data = Timesheets::getUserTimesheetTasksGrid($timesheetid,$user,0);
		$defaultTasks = Timesheets::getUserTimesheetTasksGrid($timesheetid,$user, 1);
	
	 	
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

		$date1= date('Y-m-d H:i:s', strtotime($days1));
		$date2= date('Y-m-d H:i:s', strtotime($days2));
		$date3= date('Y-m-d H:i:s', strtotime($days3));
		$date4= date('Y-m-d H:i:s', strtotime($days4));
		$date5= date('Y-m-d H:i:s', strtotime($days5));
		$date6= date('Y-m-d H:i:s', strtotime($days6));


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
	

	 	echo json_encode(array('status'=>'success', 'timesheetgrid' =>$timesheetgrid ));


	}

	}
