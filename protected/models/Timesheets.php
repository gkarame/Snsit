<?php

/**
 * This is the model class for table "timesheets".
 *
 * The followings are the available columns in table 'timesheets':
 * @property integer $id
 * @property string $timesheet_cod
 * @property integer $id_user
 * @property integer $week
 * @property string $week_start
 * @property string $week_end
 * @property integer $id_status
 *
 * The followings are the available model relations:
 * @property TimesheetStatuses $idStatus
 * @property Users $idUser
 */
class Timesheets extends CActiveRecord
{
	const STATUS_NEW = 'New';
	const STATUS_APPROVED = 'Approved';
	const STATUS_SUBMITTED = 'Submitted';
	const STATUS_REJECTED = 'Rejected';
	const STATUS_IN_APPROVAL = 'In Approval';
	const ITEM_VACATION = 13;
	const ITEM_DAYOFF = 11;
	const ITEM_SICK_LEAVE = 12; 
	public $project_id;	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Timesheets the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'timesheets';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('timesheet_cod, id_user, week, week_start, week_end, id_status', 'required'),
			array('id_user, week, id_status', 'numerical', 'integerOnly'=>true),
			array('timesheet_cod', 'length', 'max'=>5),
			array('documented', 'length', 'max'=>3),
			array('fbr', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, timesheet_cod, id_user, week, week_start, week_end, id_status, fbr', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'timesheet_cod' => 'Timesheet Cod',
			'id_user' => 'Id User',
			'week' => 'Week',
			'week_start' => 'Week Start',
			'week_end' => 'Week End',
			'id_status' => 'Id Status',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * @author Romeo Onisim
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$criteria->select  = 't.*';
		
		$criteria->compare('t.timesheet_cod',$this->timesheet_cod,true);
		$criteria->compare('t.week',$this->week);
		$criteria->compare('t.week_start',$this->week_start,true);
		$criteria->compare('t.week_end',$this->week_end,true);
		$criteria->compare('t.status',$this->status); 

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize' => Utils::getPageSize(),
			),
			'sort'=>array(
					'defaultOrder' => 't.id DESC',
					'attributes' => array('*')
			)
		));
	}
		
	/**
	 * Gets the user timesheets
	 * @param int $id_user 
	 * @return CActiveDataProvider
	 * @author Romeo Onisim
	 */
	public static function userList($id_user)
	{
		$criteria=new CDbCriteria;	
		$criteria->select = 'timesheets.id, timesheets.timesheet_cod, timesheets.id_user, timesheets.week, timesheets.week_start, timesheets.week_end, timesheets.status, user_time.amount';
		$criteria->alias = 'timesheets';
		$criteria->join = 'LEFT JOIN user_time ON user_time.id_timesheet = timesheets.id';
		$criteria->condition = 'timesheets.id_user = '.($id_user ? $id_user : 0);
		$criteria->group = 'timesheets.id';
		
		return new CActiveDataProvider('Timesheets', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'timesheets.week_start DESC',  
             	'attributes' => array(
             		'total_hours'=>array(
		                'asc'=>'user_time.amount',
		                'desc'=>'user_time.amount DESC',
		            ),
             		'*'
             	)
            ),
		));	
	}
	public static function getStatusList()
	{
		return array(
				self::STATUS_NEW => 'New',
				self::STATUS_APPROVED => 'Approved',
				self::STATUS_SUBMITTED => 'Submitted',
				self::STATUS_REJECTED => 'Rejected'
		);
	}
	
	public static function getTimesheetById($id_timesheet)
	{
		return Yii::app()->db->createCommand()
				->select('*')
				->from('timesheets')
				->where('id=:id', array(':id' => $id_timesheet))
				->queryRow();
	}
	
	public static function getLatestUserTimesheetId($id_user)
	{	$id_user= Yii::app()->user->id;
		$currentWeek = date('W');
		
		return Yii::app()->db->createCommand()
				->select('id')
				->from('timesheets')
				->where('id_user=:id_user AND week=:week order by week_start DESC limit 1', array(':id_user' => $id_user, ':week' => $currentWeek))
				->queryScalar();
	}
	public static function getUserTimesheetHours($id_timesheet)
	{
		return Yii::app()->db->createCommand()
				->select('SUM(amount)')
				->from('user_time')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet))
				->queryScalar(); 
	}
	
	public function getUserTimesheetTasks($id_timesheet, $default = 0)
	{
		if ($default == 0)
		{
			return Yii::app()->db->createCommand()
					->select('user_time.*, projects_tasks.billable as task_billabillity, projects_tasks.description as task_description, projects_phases.id_project, projects.name as project_name')
									->from('user_time')
									->leftJoin('projects_tasks', 'projects_tasks.id = user_time.id_task')
									->leftJoin('projects_phases', 'projects_phases.id = projects_tasks.id_project_phase')
									->leftJoin('projects', 'projects_phases.id_project = projects.id')
									->where('id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':default' => $default))
									->order('projects.id')
									->group('user_time.id_task')
									->queryAll();
		}
		
		if ($default == 1)
		{
			return Yii::app()->db->createCommand()
								->select('t.id, t.name, t.billable, m.name as parent, m.id as id_project')
								->from('user_time')
								->join('default_tasks t', 't.id = user_time.id_task')
								->join('default_tasks m', 'm.id = t.id_parent')
								->where('id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default AND t.id_maintenance IS NULL', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':default' => 1))
								->order('t.id_parent asc ,t.name asc')
								->group('user_time.id_task')
								->queryAll();

		}		

		if ($default == 2)
		{						
			return  Yii::app()->db->createCommand()
						->select('user_time.*, "Yes" as task_billabillity, support_services.service as task_description, maintenance.id_maintenance as id_project, CONCAT(customers.name," Support - ",codelkups.codelkup) as project_name')
									->from('user_time')
									->leftJoin('maintenance_services', 'maintenance_services.id = user_time.id_task')
									->leftJoin('support_services', 'support_services.id = maintenance_services.id_service')
									->leftJoin('maintenance', 'maintenance_services.id_contract = maintenance.id_maintenance')
									->leftJoin('customers', 'maintenance.customer = customers.id')
									->leftJoin('codelkups', 'codelkups.id = maintenance.support_service')
									->where('user_time.id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':default' => $default))
									->order('maintenance.id_maintenance')
									->group('user_time.id_task')
								->queryAll();
		}
		if ($default == 3)
		{
			$b='No';
			return Yii::app()->db->createCommand()
					->select('user_time.*, internal_tasks.billable as task_billabillity, internal_tasks.description as task_description, internal_tasks.id_internal as id_project, internal.name as project_name')
									->from('user_time')
									->leftJoin('internal_tasks', 'internal_tasks.id = user_time.id_task')
									->leftJoin('internal', 'internal_tasks.id_internal = internal.id')
									->where('id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default', array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':default' => $default))
									->order('internal.id')
									->group('user_time.id_task')
									->queryAll();
		}
		
	}
	
	public static function getLieuOfDate($id_task, $date)
	{
		$id_user_time = Yii::app()->db->createCommand()
				->select('id')
				->from('user_time')
				->where('id_task=:id_task AND id_user=:id_user AND date=:date', array(':id_task' => $id_task, ':id_user' => Yii::app()->user->id, ':date' => $date))
				->queryScalar(); 
		if($id_user_time !== false)
		{
			return Yii::app()->db->createCommand()
				->select('date')
				->from('lieu_of')
				->where('id_user_time=:id_user_time', array(':id_user_time' => $id_user_time))
				->queryScalar();
		}
		else
		{
			return false;
		}
	}
	
	public static function getUserTimeOnTaskByDay($id_timesheet, $id_task, $date = '', $default = 0)
	{	
		if($date == '')
		{
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_task=:id_task AND user_time.`default`=:default',
					array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_task' => $id_task, ':default' => $default))
					->queryScalar();
			
			return ($amount !== false && $amount !== NULL ? $amount : '0.00');
		}
		else
		{
			$fDate = date('Y-m-d', strtotime($date));
			$data = Yii::app()->db->createCommand()
					->select('user_time.amount, user_time.comment')
					->from('user_time')
					->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_task=:id_task AND date=:date AND user_time.`default`=:default', 
							array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_task' => $id_task, ':default' => $default, ':date' => $fDate))
					->queryRow();
		
			$returnArr['amount'] = ($data['amount'] !== false && $data['amount'] !== NULL ? $data['amount'] : '0.00');
			$returnArr['comment'] = $data['comment'];
			
			return $returnArr;
		}
	}
	
	/**
	 * Gets the sum of times on a phase by date or if the date is not inserted the total time on the current week/timesheet
	 * @param int $id_timesheet
	 * @param int $id_phase
	 * @param string $date
	 * @return string, string
	 * @author Romeo Onisim
	 */
	public static function getCurrentProjectTotalTime($id_timesheet, $id_project, $date = '', $default = 0)
	{
		if($default == 0)
		{
			if($date == '')
			{
					$amount = Yii::app()->db->createCommand("select SUM(user_time.amount) 
				from user_time
				join projects_tasks on user_time.id_task = projects_tasks.id
				join projects_phases on projects_tasks.id_project_phase = projects_phases.id
				where id_user='".Yii::app()->user->id."' AND id_timesheet='".$id_timesheet."' AND id_project='".$id_project."'  AND user_time.`default`=0")
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));
				
				
				
				$amount = Yii::app()->db->createCommand("select SUM(user_time.amount) 
				from user_time
				join projects_tasks on user_time.id_task = projects_tasks.id
				join projects_phases on projects_tasks.id_project_phase = projects_phases.id
				where id_user='".Yii::app()->user->id."' AND id_timesheet='".$id_timesheet."' AND id_project='".$id_project."' AND user_time.date='".$fDate."' AND user_time.`default`=0")
						->queryScalar();
						
			}
			
		}

		if($default == 1)
		{
			if($date == '')
			{
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('default_tasks')
				->leftJoin('user_time', 'default_tasks.id = user_time.id_task AND user_time.default = 1')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_parent=:id_parent',
						array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project))
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));
			
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('default_tasks')
				->join('user_time', 'default_tasks.id = user_time.id_task AND user_time.default = 1')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_parent=:id_parent AND user_time.`date`=:date',
						array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project, ':date' => $fDate))
						->queryScalar();
			}
		}


		if($default == 2)
		{
			if($date == '')
			{
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('maintenance_services')
				->leftJoin('user_time', 'maintenance_services.id = user_time.id_task AND user_time.default = 2')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet and maintenance_services.id_contract=:id_parent',
						array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project))
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));
			
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('maintenance_services')
				->join('user_time', 'maintenance_services.id = user_time.id_task AND user_time.default = 2')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet  and maintenance_services.id_contract=:id_parent AND user_time.`date`="'.$fDate.'"',
						array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project))
						->queryScalar();
			}
			
		}
		if($default == 3)
		{
			if($date == '')
			{
					$amount = Yii::app()->db->createCommand("select SUM(user_time.amount) 
				from user_time
				join internal_tasks on user_time.id_task = internal_tasks.id
				where id_user='".Yii::app()->user->id."' AND id_timesheet='".$id_timesheet."' AND id_internal='".$id_project."'  AND user_time.`default`=3")
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));				
				$amount = Yii::app()->db->createCommand("select SUM(user_time.amount) 
				from user_time
				join internal_tasks on user_time.id_task = internal_tasks.id
				where id_user='".Yii::app()->user->id."' AND id_timesheet='".$id_timesheet."' AND id_internal='".$id_project."' AND user_time.date='".$fDate."' AND user_time.`default`=3")
						->queryScalar();
						
			}
			
		}
		return ( $amount !== NULL ? $amount : '0.00');
	}
	
	/**
	 * Gets the sum of the total time of the tasks by date and if the date is not inserted calculated the total from the week/timesheet
	 * @param int $id_timesheet
	 * @param string $date
	 * @return string, string
	 * @author Romeo Onisim
	 */
	public static function getTotalTimes($id_timesheet, $date = '')
	{
		if($date == '')
		{
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet',
					array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet))
					->queryScalar();
		}
		else
		{
			$fDate = date('Y-m-d', strtotime($date));
				
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND date=:date',
					array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':date' => $fDate))
					->queryScalar();
		}
		
		return ($amount !== NULL ? $amount : '0.00');
	}
	
	/**
	 * Gets the sum of the total billable hours and if the date is not entered the total from that week/timesheet
	 * @param int $id_timesheet
	 * @param string $date
	 * @return string, string
	 * @author Romeo Onisim
	 */
	public static function getTotalBillableTimes($id_timesheet, $date = '')
	{
		if($date == '')
		{
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->leftJoin('projects_tasks', 'user_time.id_task = projects_tasks.id')
			->leftJoin('default_tasks', 'default_tasks.id= user_time.id_task')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND ((projects_tasks.billable = "Yes" AND user_time.default = 0) OR (default_tasks.billable = "Yes" AND user_time.default = 1) OR (user_time.default = 2))',
					array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet))
					->queryScalar();
		}
		else
		{
			$fDate = date('Y-m-d', strtotime($date));
		
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->leftJoin('projects_tasks', 'user_time.id_task = projects_tasks.id')
			->leftJoin('default_tasks', 'default_tasks.id= user_time.id_task')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND date=:date AND ((projects_tasks.billable = "Yes" AND user_time.default = 0) OR (default_tasks.billable = "Yes" AND user_time.default = 1) OR (user_time.default = 2))',
					array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':date' => $fDate))
					->queryScalar();
		}
		
		return ($amount !== NULL ? $amount : '0.00');
	}
	
	/**
	 * Gets the procentage of billability from the total hours and billable hours
	 * @param string $billable
	 * @param string $total
	 * @return string
	 * @author Romeo Onisim
	 */
	public static function calculateBillability($billable, $total)
	{
		$total = (float)$total;
		$billable = (float)$billable;
 		
 		if($total != 0)
 		{
 			return number_format($billable / $total, 2) * 100 . '%';
 		}
 		else
 		{
 			return '0%';
 		}
	}
	
	/**
	 * Generate the timesheet for a user
	 * @param int $id_user
	 * @author Romeo Onisim
	 */
	public static function generateTimesheetForUser($id_user, $currentWeek = false)
	{
		if ($currentWeek == false)
		{
			Timesheets::generateNextTimesheetForUser($id_user);
		}
		else
		{
			Timesheets::generateNewTimesheetForUser($id_user);
		}
	}
	
	/**
	 * Generate the next timesheet for a user plus it's previous timesheet tasks
	 * @param int $id_user
	 * @author Romeo Onisim
	 */
	public static function generateNextTimesheetForUser($id_user)
	{
		// get latest user timesheet
		$latestTimesheet = Timesheets::model()->getLatestUserTimesheetId($id_user);
		
		// if there is a timesheet copy that else create a new one
		if ($latestTimesheet != false)
		{
			try {
				// copy the timesheet
				$currTimesheet = Timesheets::getTimesheetById($latestTimesheet);
				if (count($currTimesheet) > 0)
				{
					$nextTimesheet = Yii::app()->db->createCommand()->insert('timesheets', array(
						'timesheet_cod' => '00000',
						'id_user'		=> $id_user,
						'week'			=> date('W') + 1,
						'week_start'	=> date('Y-m-d H:i:s', strtotime($currTimesheet['week_start'] . ' + 7 days')),
						'week_end'		=> date('Y-m-d H:i:s', strtotime($currTimesheet['week_end'] . ' + 7 days')),
						'status'		=> Timesheets::STATUS_NEW
					));
					
					// change the timesheet code
					if($nextTimesheet == 1)
					{
						$nextTimesheetId = Yii::app()->db->getLastInsertId('timesheets');
						Timesheets::model()->updateByPk($nextTimesheetId, array('timesheet_cod' => Utils::paddingCode($nextTimesheetId)));
						
						// copy the user time records
						$currentTimes = UserTime::getUserCurrentTimesheetTasks($latestTimesheet, $id_user);
	
						// creating the values string
						$values = '';
						foreach($currentTimes as $currentTime)
						{
							$values .= '(' . $id_user . ', ' . $currentTime['id_task'] . ', ' . $nextTimesheetId . ', "' . date('Y-m-d H:i:s', strtotime($currentTime['date'] . ' + 7 days')) . '"),';	
						}
						
						// if string is not empty insert all values into the table
						if($values != '')
						{
							$values = rtrim($values, ",");
							$nextUserTimes = Yii::app()->db->createCommand('INSERT INTO user_time(id_user, id_task, id_timesheet, date) VALUES ' . $values)->execute();
						}
						
						// Inserting the default tasks from user group
						$defaultTasks = Users::getUserDefaultTasksById($id_user);
						$values = '';
						foreach($defaultTasks as $defaultTask)
						{
							$values .= '(' . $id_user . ', ' . $defaultTask['id_task'] . ', ' . $nextTimesheetId . ', "' . date('Y-m-d H:i:s', strtotime($currTimesheet['week_start'] . ' + 7 days')) . '", 1),';
						}
							
						// if string is not empty insert all values into the table
						if($values != '')
						{
							$values = rtrim($values, ",");
							$nextUserTimes = Yii::app()->db->createCommand('INSERT INTO user_time(id_user, id_task, id_timesheet, date, `default`) VALUES ' . $values)->execute();
						}
						echo "Timesheet Code ".Utils::paddingCode($nextTimesheetId)." for user ". $id_user." for week ".(date('W') + 1)." was created.<br />";
					}	
				}
			} catch (Exception $e) {
				echo "Timesheet for user ". $id_user." for week ".(date('W') + 1)." WAS NOT created.<br />";
			}
		}
		else
		{	
			try {
				$newTimesheet = Yii::app()->db->createCommand()->insert('timesheets', array(
						'timesheet_cod' => '00000',
						'id_user'		=> $id_user,
						'week'			=> date('W') + 1,
						'week_start'	=> date('Y-m-d H:i:s', strtotime('next sunday')),
						'week_end'		=> date('Y-m-d H:i:s', strtotime('next saturday', strtotime('next sunday'))),
						'status'		=> Timesheets::STATUS_NEW
				));
			
				if($newTimesheet == 1)
				{
					$nextTimesheetId = Yii::app()->db->getLastInsertId('timesheets');
					Timesheets::model()->updateByPk($nextTimesheetId, array('timesheet_cod' => Utils::paddingCode($nextTimesheetId)));
					
					// Inserting the default tasks from user group
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
					echo "Timesheet Code ".Utils::paddingCode($nextTimesheetId)." for user ". $id_user." for week ".(date('W') + 1)." was created.<br />";
				}
			} catch(Exception $exception) {
				echo "Timesheet for user ". $id_user." for week ".(date('W') + 1)." WAS NOT created.<br />";
			}
		}
	}
	
	/**
	 * Generate a new timesheet for a new user
	 * @param int $id_user
	 * @author Romeo Onisim
	 */
	public static function generateNewTimesheetForUser($id_user)
	{
		try {
			$newTimesheet = Yii::app()->db->createCommand()->insert('timesheets', array(
					'timesheet_cod' => '00000',
					'id_user'		=> $id_user,
					'week'			=> date('W'),
					'week_start'	=> (date('w') == 1 ? date('Y-m-d H:i:s', strtotime('previous sunday')) : date('Y-m-d H:i:s', strtotime('previous sunday'))),
					'week_end'		=> (date('w') == 0 ? date('Y-m-d H:i:s', strtotime('today')) : date('Y-m-d H:i:s', strtotime('next saturday'))),
					'status'		=> Timesheets::STATUS_NEW
			));
				
			if($newTimesheet == 1)
			{
				$nextTimesheetId = Yii::app()->db->getLastInsertId('timesheets');
				Timesheets::model()->updateByPk($nextTimesheetId, array('timesheet_cod' => Utils::paddingCode($nextTimesheetId)));
				
				// Inserting the default tasks from user group
				$defaultTasks = Users::getUserDefaultTasksById($id_user);
				$values = '';
				foreach($defaultTasks as $defaultTask)
				{
					$values .= '(' . $id_user . ', ' . $defaultTask['id_task'] . ', ' . $nextTimesheetId . ', "' . (date('w') == 1 ? date('Y-m-d H:i:s', strtotime('today')) : date('Y-m-d H:i:s', strtotime('previous sunday'))) . '", 1),';
				}
				
				// if string is not empty insert all values into the table
				if($values != '')
				{
					$values = rtrim($values, ",");
					$nextUserTimes = Yii::app()->db->createCommand('INSERT INTO user_time(id_user, id_task, id_timesheet, date, `default`) VALUES ' . $values)->execute();
				}
				echo "Timesheet Code ".Utils::paddingCode($nextTimesheetId)." for user ". $id_user." for week ".(date('W'))." was created.<br />";
			}
		} catch (Exception $e) {
			echo "Timesheet for user ". $id_user." for week ".(date('W'))." WAS NOT created.<br />";
		}
	}
	
	/**
	 * Returns the array with the latest item hours and comment, false if was not found
	 * @param int $id_task
	 * @param string $date
	 * @param int $id_user
	 * @param int $default
	 * @param int $id_timesheet
	 * @return array|false
	 * @author Romeo Onisim
	 */
	public static function getLatestItemDataByDate($id_task, $date, $id_user, $default)
	{
		return  Yii::app()->db->createCommand()
					->select('amount, comment, lieu_of.date as lieu_of,srs')
					->from('user_time')
					->leftJoin('lieu_of', 'lieu_of.id_user_time = user_time.id')
					->where('id_task=:id_task AND user_time.date < :date AND id_user=:id_user AND `default`=:default', 
							array(':id_task' => $id_task, ':date' => $date, ':id_user' => $id_user, ':default' => $default))
					->order('user_time.date DESC')
					->queryRow();	
	}
	
	/**
	 * Updates a timesheet item or creates it if it doesn't exists in the db
	 * @param int $id_task
	 * @param string $date
	 * @param int $id_user
	 * @param int $default
	 * @param int $id_timesheet
	 * @param float $hours
	 * @param string $comment
	 * @return nothing
	 * @author Romeo Onisim
	 */
	public static function updateOrCreateNewItem($id_task, $date, $id_user, $default, $id_timesheet, $hours, $comment, $lieu_of,$ticket_id, $inst_id, $reason , $customer, $radio, $fbr, $rsr_id)
	{	
		if (!empty($customer))
		{
			foreach ($customer as $cust) {
				
				$custName=Customers::getNameById($cust);
				$pos = strpos($comment,$custName);

				if($pos === false) 
				{
				    $comment=  $custName.' '.$comment;
				}
			}
			
		}
		if (!empty($inst_id))
		{
			//$cust=Customers::getNameById($customer);
			$pos = strpos($comment,$inst_id);
			$f2= strpos($comment,'IR# ');

			if($pos === false && $f2 === false) 
			{
			    $comment=  'IR# '.$inst_id.' '.$comment;
			}
		}

		if (!empty($ticket_id))
		{
			//$cust=Customers::getNameById($customer);
			$pos = strpos($comment,$ticket_id);
			$f2= strpos($comment,'SR# ');

			if($pos === false && $f2 === false) 
			{
			    $comment=  'SR# '.$ticket_id.' '.$comment;
			}
		}

		if (!empty($rsr_id))
		{
			//$cust=Customers::getNameById($customer);
			$pos = strpos($comment,$rsr_id);
			$f2= strpos($comment,'RSR# ');

			if($pos === false && $f2 === false) 
			{
			    $comment=  'RSR# '.$rsr_id.' '.$comment;
			}
		}

		$date = date("Y-m-d", strtotime($date));

		$id = Yii::app()->db->createCommand()
				->select('id')
				->from('user_time')
				->where('id_task=:id_task AND date=:date AND id_user=:id_user AND `default`=:default AND id_timesheet =:id_timesheet', array(
						':id_task' => $id_task, ':date' => $date, ':id_user' => $id_user, ':default' => $default, ':id_timesheet' => $id_timesheet))
				->queryScalar();
		
		// if the id wasn't found, create it
		if($id == false)
		{
			if($radio !='')
			{
				$insert = Yii::app()->db->createCommand()->insert('user_time', array(
					'id_task'=> $id_task,
					'id_user'=> $id_user,
					'amount' => $hours,
					'comment'=> $comment,
					'id_timesheet' => $id_timesheet,
					'date'=> $date,
					'default'=> $default,
					'documented' => $radio,
					'fbr' => $fbr,
					'srs' => $ticket_id
				));
			}else{
				$insert = Yii::app()->db->createCommand()->insert('user_time', array(
					'id_task'=> $id_task,
					'id_user'=> $id_user,
					'amount' => $hours,
					'comment'=> $comment,
					'id_timesheet' => $id_timesheet,
					'date'=> $date,
					'default'=> $default,
					'fbr' => $fbr,
					'srs' => $ticket_id
			));
			}
			
			
			$id = Yii::app()->db->getLastInsertID();
			
			if(strlen($lieu_of) > 0 && $id != null)
			{
				$insert = Yii::app()->db->createCommand()->insert('lieu_of', array(
					'id_user_time' 	=> $id,
					'date'		  	=> $lieu_of
				));
			}

			if(strlen($ticket_id) > 0 && $id != null)
			{
				$insert = Yii::app()->db->createCommand()->insert('ticket_id', array(
					'id_user_time' 	=> $id,
					'ticket_id'		  	=> $ticket_id
				));
			}
			if(strlen($rsr_id) > 0 && $id != null)
			{
				$insert = Yii::app()->db->createCommand()->insert('rsr_id', array(
					'id_user_time' 	=> $id,
					'rsr_id'		  	=> $rsr_id
				));
			}
			if(strlen($inst_id) > 0 && $id != null)
			{
				$insert = Yii::app()->db->createCommand()->insert('inst_id', array(
					'id_user_time' 	=> $id,
					'inst_id'		  	=> $inst_id
				));
			}
			
				if(strlen($reason) > 0 && $id != null)
			{
				$insert = Yii::app()->db->createCommand()->insert('user_task_reasons', array(
					'id_user_time' 	=> $id,
					'reason'		  	=> $reason
				));


			}
			if(!empty($customer)  && $id != null)
			{
				foreach ($customer as $cust) {
					$insert = Yii::app()->db->createCommand()->insert('ca_customer', array(
					'id_user_time' 	=> $id,
					'id_customer'		  	=> $cust
				));
				}
				
			}
			
		}
		else
		{
			if($radio !='')
			{
				$update = UserTime::model()->updateAll(array('amount' => $hours, 'comment'=> $comment, 'documented'=> $radio, 'fbr'=> $fbr),
					'id_task=' . $id_task . ' AND date="' . $date . '" AND id_user=' . $id_user . ' AND `default`="' . $default .'" AND id_timesheet = ' . $id_timesheet);
			}
			else{
				$update = UserTime::model()->updateAll(array('amount' => $hours, 'comment'=> $comment , 'fbr'=> $fbr, 'srs'=> $ticket_id),
					'id_task=' . $id_task . ' AND date="' . $date . '" AND id_user=' . $id_user . ' AND `default`="' . $default .'" AND id_timesheet = ' . $id_timesheet);
			
			}
			
			if(strlen($lieu_of) > 0 && $id != null)
			{
				$count = Yii::app()->db->createCommand()
						->select('id')
						->from('lieu_of')
						->where('id_user_time=:id_user_time', array(':id_user_time' => $id))
						->queryScalar();
				
				if($count == '0')
				{
					Yii::app()->db->createCommand()->insert('lieu_of', array(
							'id_user_time' 	=> $id,
							'date'		  	=> $lieu_of
					));
				}
				else
				{
					Yii::app()->db->createCommand()->update('lieu_of', array(
							'date'		  	=> $lieu_of
					), 'id_user_time=:id', array(':id' => $id));
				}
			}

			if(strlen($ticket_id) > 0 && $id != null)
			{
				$count = Yii::app()->db->createCommand()
						->select('id')
						->from('ticket_id')
						->where('id_user_time=:id_user_time', array(':id_user_time' => $id))
						->queryScalar();
				
				if($count == '0')
				{
					Yii::app()->db->createCommand()->insert('ticket_id', array(
							'id_user_time' 	=> $id,
							'ticket_id'		  	=> $ticket_id
					));
				}
				else
				{
					Yii::app()->db->createCommand()->update('ticket_id', array(
							'ticket_id'		  	=> $ticket_id
					), 'id_user_time=:id', array(':id' => $id));
				}
			}

			if(strlen($rsr_id) > 0 && $id != null)
			{
				$count = Yii::app()->db->createCommand()
						->select('id')
						->from('rsr_id')
						->where('id_user_time=:id_user_time', array(':id_user_time' => $id))
						->queryScalar();
				
				if($count == '0')
				{
					Yii::app()->db->createCommand()->insert('rsr_id', array(
							'id_user_time' 	=> $id,
							'rsr_id'		  	=> $rsr_id
					));
				}
				else
				{
					Yii::app()->db->createCommand()->update('rsr_id', array(
							'rsr_id'		  	=> $rsr_id
					), 'id_user_time=:id', array(':id' => $id));
				}
			}

			if(strlen($inst_id) > 0 && $id != null)
			{
				$count = Yii::app()->db->createCommand()
						->select('id')
						->from('inst_id')
						->where('id_user_time=:id_user_time', array(':id_user_time' => $id))
						->queryScalar();
				
				if($count == '0')
				{
					Yii::app()->db->createCommand()->insert('inst_id', array(
							'id_user_time' 	=> $id,
							'inst_id'		  	=> $inst_id
					));
				}
				else
				{
					Yii::app()->db->createCommand()->update('inst_id', array(
							'inst_id'		  	=> $inst_id
					), 'id_user_time=:id', array(':id' => $id));
				}
			}

			if(!empty($customer)  && $id != null)
			{
				//print_r($customer);print_r($id);exit;
				foreach ($customer as $cust) {
					$count = Yii::app()->db->createCommand()
						->select('id')
						->from('ca_customer')
						->where('id_user_time=:id_user_time and id_customer=:customerid', array(':id_user_time' => $id, ':customerid'=> $cust))
						->queryScalar();
				
				if($count == '0')
				{
					Yii::app()->db->createCommand()->insert('ca_customer', array(
							'id_user_time' 	=> $id,
							'id_customer'	=> $cust
					));
				}
				else
				{
					Yii::app()->db->createCommand()->update('ca_customer', array(
							'id_customer'		  	=> $cust
					), 'id_user_time=:id', array(':id' => $id));
				}
				}
				
			}

				if(strlen($reason) > 0 && $id != null)
			{
				$count = Yii::app()->db->createCommand()
						->select('id')
						->from('user_task_reasons')
						->where('id_user_time=:id_user_time', array(':id_user_time' => $id))
						->queryScalar();
				
				if($count == '0')
				{
					Yii::app()->db->createCommand()->insert('user_task_reasons', array(
							'id_user_time' 	=> $id,
							'reason'		  	=> $reason
					));
				}
				else
				{
					Yii::app()->db->createCommand()->update('user_task_reasons', array(
							'reason'		  	=> $reason
					), 'id_user_time=:id', array(':id' => $id));
				}
			}

		}
			
	}
	
	/**
	 * Changes the status of the timesheet
	 * @param int $id_timesheet
	 * @param int $id_user
	 * @param string $status
	 * @author Romeo Onisim
	 */
	public static function changeTimesheetStatus($id_timesheet, $status)
	{
		if (in_array($status,array(Timesheets::STATUS_REJECTED, Timesheets::STATUS_APPROVED, Timesheets::STATUS_IN_APPROVAL)))
		{
			$ts = Timesheets::model()->findByPk($id_timesheet);
			if ($ts->id_user == Yii::app()->user->id)
			{
				return 0;
			}
		}
		return Timesheets::model()->updateByPk($id_timesheet, array('status' => $status));
	}
	
	/**
	 * 
	 * @param unknown $id_timesheet
	 * @param unknown $id_user
	 * @param unknown $id
	 * @param unknown $type
	 * @author Romeo Onisim
	 */
	public static function deleteItemFromTimesheet($id_timesheet, $id_user, $id, $type)
	{
		$j = Yii::app()->db->createCommand("SELECT week_start FROM timesheets WHERE id = '$id_timesheet' AND id_user = '$id_user'")->queryRow();
		switch ($type) {
		case 'task':
			UserTime::model()->deleteAllByAttributes(array('id_timesheet' => $id_timesheet, 'id_task' => $id, 'id_user' => $id_user, 'default' => 0));
			break;
		case 'project':
			$tasks = Yii::app()->db->createCommand()
					->select('projects_tasks.id')
					->from('projects_tasks')
					->join('projects_phases', 'projects_tasks.id_project_phase = projects_phases.id')
					->where('projects_phases.id_project=:id', array(':id' => $id))->queryAll();
			
			foreach($tasks as $task)
			{
				UserTime::model()->deleteAllByAttributes(array('id_timesheet' => $id_timesheet, 'id_task' => $task['id'], 'id_user' => $id_user, 'default' => 0));
			}
			break;
		case 'default_task':
			UserTime::model()->deleteAllByAttributes(array('id_timesheet' => $id_timesheet, 'id_task' => $id, 'id_user' => $id_user, 'default' => 1));
			break;
		case 'default_project':
			$tasks = Yii::app()->db->createCommand()
					->select('default_tasks.id')
					->from('default_tasks')
					->where('default_tasks.id_parent=:id', array(':id' => $id))->queryAll();
			foreach($tasks as $task)
			{
				UserTime::model()->deleteAllByAttributes(array('id_timesheet' => $id_timesheet, 'id_task' => $task['id'], 'id_user' => $id_user, 'default' => 1));
			}
			break;
		}
		return $j;
	}
	
	public static function checkPhaseStartedByPhasenumber($project, $number)
	{
		$project = (int) $project;
		$number = (int) $number;
		return Yii::app()->db
		->createCommand("SELECT id FROM user_time
				WHERE `default`=0 and amount>0 and  id_task IN ( SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp
				ON pp.id = pt.id_project_phase WHERE pp.id_phase is not null and pp.phase_number = {$number}
				AND pp.id_project = {$project}) LIMIT 1")
				->queryScalar();
	}
	
	public static function checkProjectHasTimeByPeriod($project, $period = null)
	{
	$project = (int) $project;
	$first_entry = Yii::app()->db
	->createCommand("SELECT `date` FROM user_time
			WHERE `default`=0  and amount>0 and id_task IN ( SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp
			ON pp.id = pt.id_project_phase WHERE pp.id_project = {$project}) ORDER BY `date` ASC LIMIT 1")
			->queryScalar();
			if ($period != null && $first_entry)
			{
			$tfirst = strtotime($first_entry.' '.$period);
			$now = strtotime("now");
				if ($tfirst <= $now)
				return true;
				return false;
				}
				return $first_entry;
	}
	
	public static function checkFirstPhaseTimeEnteredByPeriod($project, $number, $period = "+ 2 week")
	{
	$project = (int) $project;
	$number = (int) $number;
	$firstdate = Yii::app()->db
	->createCommand("SELECT date FROM user_time
			WHERE `default`=0  and amount>0 and id_task IN (SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp
			ON pp.id = pt.id_project_phase WHERE pp.phase_number = {$number}
			AND pp.id_project = {$project}) ORDER BY date ASC LIMIT 1")
			->queryScalar();
	$now = strtotime("now");
	$fdate = strtotime($firstdate.' '.$period);
		if ($fdate <= $now)
			return true;
			return false;
	}
	

	public static function checkFirstPhaseTimeEnteredByPeriodTech($project, $number, $period = "+ 2 week")
	{
	$project = (int) $project;
	$number = (int) $number;
	$firstdate = Yii::app()->db
	->createCommand("SELECT date FROM user_time
			WHERE `default`=0  and amount>0 and id_user IN (SELECT id_user FROM user_personal_details where unit in (116,117,586)) and id_task IN (SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp
			ON pp.id = pt.id_project_phase WHERE pp.phase_number = {$number}
			AND pp.id_project = {$project}) ORDER BY date ASC LIMIT 1")
			->queryScalar();
	$now = strtotime("now");
	$fdate = strtotime($firstdate.' '.$period);
		if ($fdate <= $now)
			return true;
			return false;
	}


	/**
	 * Gets the email body to send to groups that opted to receive this email
	 * @return string
	 * @author Romeeo Onisim
	 */
	public static function getPendingTimesheetsForAllUsers()
	{
		$timesheets = Yii::app()->db->createCommand()
				->select('users.id as id_user, users.firstname, users.lastname, timesheets.timesheet_cod, timesheets.week_start, timesheets.week_end')
				->from('timesheets')
				->join('users', 'users.id = timesheets.id_user')
				->join('user_personal_details', 'user_personal_details.id_user = users.id')
				->where('user_personal_details.sns_admin=0 and users.active=:active AND timesheets.week < :current_date AND timesheets.status=:status', array(':active' => 1, ':current_date' => date('W'), ':status' => Timesheets::STATUS_NEW))
				->order('firstname,lastname ,week_start')
				->queryAll();
	
		// generate the div for replacing
		$noTimesheets = count($timesheets);
		$userArr = array();
		$k = 1;
		$returnString = '';
		for($i = 0; $i < $noTimesheets; $i++)
		{
			if(!in_array($timesheets[$i]['id_user'], $userArr))
			{
				$userArr[] = $timesheets[$i]['id_user'];
				$returnString .= '<br />'.($k++) . '. ' . $timesheets[$i]['firstname'] . ' ' . $timesheets[$i]['lastname'] . '<br />';
			}
			
			$returnString .= '&nbsp;&nbsp;&nbsp;&nbsp;Time Sheet ' . $timesheets[$i]['timesheet_cod'] . ': From ' . date('d/m/Y', strtotime($timesheets[$i]['week_start'])) . ' To ' . date('d/m/Y', strtotime($timesheets[$i]['week_end']))  . '<br />'; 
		}
		return $returnString;
	}
	public static function getPendingTimesheetsUsers()
	{
		return Yii::app()->db->createCommand()
				->select('distinct(users.id) as id_user, user_personal_details.email, users.firstname, users.lastname, timesheets.timesheet_cod, timesheets.week_start, timesheets.week_end, lm.email as line_manager')
				->from('timesheets')
				->join('users', 'users.id = timesheets.id_user')
				->join('user_personal_details', 'user_personal_details.id_user = users.id')
				->leftJoin('user_personal_details lm', 'lm.id_user = user_personal_details.line_manager')
				->where('user_personal_details.sns_admin=0 and users.active=:active AND timesheets.week < :current_date AND timesheets.status=:status', array(':active' => 1, ':current_date' => date('W'), ':status' => Timesheets::STATUS_NEW))
				->order('timesheets.timesheet_cod asc')
				->group('users.id')
				->queryAll();
	}
	/**
	 * The array for each user to send the pending timesheets
	 * @author Romeo Onisim
	 */
	public static function getPendingTimesheetsForEachUser()
	{
		return Yii::app()->db->createCommand()
				->select('users.id as id_user, user_personal_details.email, users.firstname, users.lastname, timesheets.timesheet_cod, timesheets.week_start, timesheets.week_end, lm.email as line_manager')
				->from('timesheets')
				->join('users', 'users.id = timesheets.id_user')
				->join('user_personal_details', 'user_personal_details.id_user = users.id')
				->leftJoin('user_personal_details lm', 'lm.id_user = user_personal_details.line_manager')
				->where('user_personal_details.sns_admin=0 and users.active=:active AND timesheets.week < :current_date AND timesheets.status=:status', array(':active' => 1, ':current_date' => date('W'), ':status' => Timesheets::STATUS_NEW))
				->order('timesheets.timesheet_cod asc')
				->queryAll();
	}
	public static function showUserTimesheets($id_user)
	{
		$returnString = '';
		$timesheets=Yii::app()->db->createCommand()
				->select('users.id as id_user, user_personal_details.email, users.firstname, users.lastname, timesheets.timesheet_cod, timesheets.week_start, timesheets.week_end, lm.email as line_manager')
				->from('timesheets')
				->join('users', 'users.id = timesheets.id_user')
				->join('user_personal_details', 'user_personal_details.id_user = users.id')
				->leftJoin('user_personal_details lm', 'lm.id_user = user_personal_details.line_manager')
				->where('users.id=:iduser and user_personal_details.sns_admin=0 and users.active=:active AND timesheets.week < :current_date AND timesheets.status=:status', array(':iduser'=>$id_user ,':active' => 1, ':current_date' => date('W'), ':status' => Timesheets::STATUS_NEW))
				->order('timesheets.timesheet_cod asc')
				->queryAll();
		foreach($timesheets as $val)
		{
				$returnString .= 'Time Sheet ' . $val['timesheet_cod'] . ': From ' . date('d/m/Y', strtotime($val['week_start'])) . ' To ' . date('d/m/Y', strtotime($val['week_end']))  . '<br />';
			
		}
		
		return $returnString;
	}
	/**
	 * Gets the body email for each user with his pending timesheets
	 * @param array $values
	 * @param int $id_user
	 * @return string
	 * @author Romeo Onisim
	 */
	public static function showUserTimesheetsFromArray($values, $id_user)
	{
		$returnString = '';
		
		foreach($values as $val)
		{
			if($val['id_user'] == $id_user)
			{
				$returnString .= 'Time Sheet ' . $val['timesheet_cod'] . ': From ' . date('d/m/Y', strtotime($val['week_start'])) . ' To ' . date('d/m/Y', strtotime($val['week_end']))  . '<br />';
			}
		}
		
		return $returnString;
	}
	
	public static function getStatusFunc($id_timesheet){
		$result = Yii::app()->db->createCommand("SELECT status FROM timesheets WHERE id = '$id_timesheet'")->queryScalar();
		return $result;
	}
	
	public static function getValidation($date){
		return $date;
		return date('Y-m-d H:i:s', strtotime($date . ' + 7 days'));
	}
	
	public static function getVacationsDays($id_user, $year, $month) {
		
		$sum = 0;
		$month = (int)$month;
		$year = (int)$year;
		$id_user = (int) $id_user;
		
		if ($month < 10) $month = "0".$month;
		
		
		
		$checkAdmin=Users::getcountAdminUser($id_user);
		if($checkAdmin>0)
		{		//print_r("select startDate, endDate, halfday from requests where type=91 AND YEAR(startDate) = $year AND MONTH(startDate) = $month and user_id= '$id_user'");exit;
				$vacationDays = 0;
				$requests=  Yii::app()->db->createCommand("select startDate, endDate, halfday from requests where type=91 and status='1' AND YEAR(startDate) = $year AND MONTH(startDate) = $month and user_id= '$id_user'")->queryAll(); 
			   
				foreach ($requests as $request)
				{
					$startTimestamp = strtotime($request['startDate']);
					$endTimestamp = strtotime($request['endDate']);
					if ($startTimestamp == $endTimestamp &&  $request['halfday']==1 )
					{
						 $vacationDays = $vacationDays + 0.5;
					}
					else
					{
						for ($i = $startTimestamp; $i <= $endTimestamp; $i = $i + (60 * 60 * 24)) {
							if (date("N", $i) <= 5) $vacationDays = $vacationDays + 1;
						}
					}
				}
				
				return $vacationDays;
  
			
		}
		else
		{
			$id_vac = self::ITEM_VACATION;
			$sum = (int) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE id_user = '$id_user' AND `default`=1 AND id_task = '$id_vac' AND YEAR(`date`) = $year AND MONTH(`date`) = $month")->queryScalar();
			return $sum / 8;
		}
		
	}


	
	// returns the number of timesheets with status new for the current user
	public static function getPendingTimesheetsCount()
	{
		return Yii::app()->db->createCommand()
			->select('COUNT(*)')
			->from('timesheets')
			->where('id_user = :user AND status = :status', array(':user'=>Yii::app()->user->id, ':status'=>Timesheets::STATUS_NEW))
			->queryScalar();
	}
	
	public static function getPendingTimesheetsCount2($start, $end)
	{
		return Yii::app()->db->createCommand()
			->select('COUNT(*)')
			->from('timesheets')
			->where('id_user = :user AND status = :status and ((week_start<= :start and week_end>= :start) ||(week_start<= :end and week_end>= :end) )', array(':user'=>Yii::app()->user->id, ':status'=>Timesheets::STATUS_NEW, 'start'=>$start, 'end'=>$end))
			->queryScalar();
	}

	public static function getAllowedVacationDays($id_user){
		

		$days_allowed=(int) YII::app()->db->createCommand("SELECT upd.annual_leaves from user_personal_details upd  WHERE upd.id_user='$id_user' ")->queryScalar();
		//echo $days_allowed;exit;
		return $days_allowed;
	}


	public static function getVactionsNotRequested()
	{
		$list_users="";
		$results = Yii::app()->db->createCommand("select  utt.id_user , utt.date , utt.comment    from user_time utt 
where  utt.amount>0 
and utt.id_task ='13'
 and utt.default='1'
 and utt.status>='1'  
and utt.id not in (select ut.id from user_time ut , requests r 
										where ut.amount>0 
											and ut.id_task ='13' 
											and ut.default='1' 
											and ut.status='1' 
											and ut.id_user=r.user_id 
											and r.type='91' 
											and ut.date between r.startDate and r.endDate)

")->queryAll();
		


	$list_users.='<br/> <table style="font-family:Calibri"> 
		<tr> 		
		<th class="h2" style=" height:20px; vertical-align:bottom;height:10px;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;border-left:1px solid #567885;">Resource</th> 
		<th class="h2" style="height:20px;  vertical-align:bottom;height:10px;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Vacation Date</th>
		<th class="h2" style="height:20px;  vertical-align:bottom;height:10px;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Comment</th>
		</tr>';
			foreach ($results as $key => $top)
		{ 
			$list_users.= '<tr><td style="border-left:1px solid #567885;text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Users::getNameByid($top['id_user']).'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['date'].'</td> <td style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['comment'].'</td> 		 </tr>';
		}

	
		$list_users.='<tr><td colspan="3" style="text-align:left;border-top:1.5px solid #B20533;padding-left:10px;padding-right:10px"> </td></tr></table>';
		
		return $list_users;
	
	}

	public static function getOpenTimesheet() {

		$result = Yii::app()->db->createCommand("SELECT  count(1) from `timesheets` where  MONTH(week_start)= MONTH(NOW()- INTERVAL 1 MONTH)  and status='New'")->queryScalar();
		
		return $result;		

	}

	public static function getBillability() {

		$result = Yii::app()->db->createCommand("
select  IFNULL(TRUNCATE(bill/tot,2),0) as perc  from 

(select sum(r.amount)*100 as bill from (
select  sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) 
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)  
) as r) as billable 
,
 (select sum(r.amount) as tot from (
select sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)
union all
select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) 

) as r ) as total")->queryScalar();
		return $result;		

	}
	public static function getBillabilityTwelveMonthsBack() {

		$result = Yii::app()->db->createCommand("
select  IFNULL(TRUNCATE(bill/tot,2),0) as perc  from 

(select sum(r.amount)*100 as bill from (
select  sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH) 
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH)  
) as r) as billable 
,
 (select sum(r.amount) as tot from (
select sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH) 
union all
select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH) 
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH) 

) as r ) as total")->queryScalar();
		return $result;		

	}

	public static function getBillabilityByTeam($team) {

		if($team=='PS') {
			 $tech=" and uts.id_user in ("; 
			foreach(UserPersonalDetails::getPS() as $t){
					$tech.=" '".$t['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;
		}

		if($team=='CS') {
			 $cs=" and uts.id_user in ("; 
			foreach(UserPersonalDetails::getCS() as $t){
					$cs.=" '".$t['id']."',";
				}
				$cs.=" 0 ) ";
				$resources=$cs;
		}
		if($team=='OPS') {
			$ops="and uts.id_user in ("; 
			
				foreach(UserPersonalDetails::getOps() as $o){
					$ops.=" '".$o['id']."',";
				}
				$ops.=" 0 ) ";
				$resources=$ops;
		}


		$result = Yii::app()->db->createCommand("
select  IFNULL(TRUNCATE(bill/tot,2),0) as perc  from 

(select sum(r.amount)*100 as bill from (
select  sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) ".$resources."
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)   ".$resources."
) as r) as billable 
,
 (select sum(r.amount) as tot from (
select sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)  ".$resources."
union all
select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)  ".$resources."
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and  MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)  ".$resources."

) as r ) as total")->queryScalar();
		return $result;		

	}

		public static function getBillabilityByTeamTwelveMonth($team) {

		if($team=='PS') {
			 $tech=" and uts.id_user in ("; 
			foreach(UserPersonalDetails::getPS() as $t){
					$tech.=" '".$t['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;
		}

		if($team=='CS') {
			 $cs=" and uts.id_user in ("; 
			foreach(UserPersonalDetails::getCS() as $t){
					$cs.=" '".$t['id']."',";
				}
				$cs.=" 0 ) ";
				$resources=$cs;
		}
		if($team=='OPS') {
			$ops="and uts.id_user in ("; 
			
				foreach(UserPersonalDetails::getOps() as $o){
					$ops.=" '".$o['id']."',";
				}
				$ops.=" 0 ) ";
				$resources=$ops;
		}


		$result = Yii::app()->db->createCommand("
select  IFNULL(TRUNCATE(bill/tot,2),0) as perc  from 

(select sum(r.amount)*100 as bill from (
select  sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH) ".$resources."
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH)   ".$resources."
) as r) as billable 
,
 (select sum(r.amount) as tot from (
select sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH)  ".$resources."
union all
select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH)  ".$resources."
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and  MONTH(uts.date) >= MONTH(NOW()- INTERVAL 12 MONTH)  ".$resources."

) as r ) as total")->queryScalar();
		

		return $result;		

	}

	public static function getLeaveDuringLastMonth($type) {

		$result = Yii::app()->db->createCommand("select SUM(amount)/8 from user_time where id_task in ('".$type."') and `default`='1' and MONTH(date) = MONTH(NOW()- INTERVAL 1 MONTH) and amount>0")->queryScalar();
		return $result;		

	}

	public static function getLeaveDuringLastTwelveMonth($type) {

		$result = Yii::app()->db->createCommand("select (SUM(amount)/8)/12 from user_time where id_task in ('".$type."') and `default`='1' and MONTH(date) >= MONTH(NOW()- INTERVAL 12 MONTH) and amount>0")->queryScalar();
		return $result;		

	}
	
	public static function getTop5NonBillableResources() {

		$result = Yii::app()->db->createCommand("
select t1.id_user , TRUNCATE(((nonbillable*100)/total) ,2) as nbperc from 
(
select r.id_user , sum(r.amount) as nonbillable from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where  u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='No' and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where  u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and dti.billable='No' and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where   u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='No' and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user ) t1
,
(
select r.id_user , sum(r.amount) as total from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where  u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where  u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where  u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH) GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user  order by nbperc desc limit 5 ")->queryAll();
	$nonbillable= " ";
	$i=0;
		foreach ($result as  $value) {
			$i++;
			 $nonbillable.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$i."- ".Users::getNameByid($value['id_user']).":  <b>".$value['nbperc']."% </b>  <br />";
		}
		return $nonbillable;		

	}


	public static function getMostActiveProject() {

		$result = Yii::app()->db->createCommand("
			SELECT pp.id_project,round(SUM(uts.amount),3) as man_days FROM user_time uts  LEFT JOIN projects_tasks pt ON uts.id_task=pt.id LEFT JOIN projects_phases pp  ON pt.id_project_phase=pp.id 
 where uts.`default`='0'   and MONTH(uts.date) = MONTH(NOW()- INTERVAL 1 MONTH)
group by pp.id_project
order by man_days desc limit 3")->queryAll();
		$mostactive=" ";
		$i=0;
		foreach ($result as $value) {
			$i++;
			$mostactive.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$i."- ".Projects::getNameByid($value['id_project']).":  <b>".$value['man_days']."</b> Hours <br >" ;
				
		}
		return $mostactive;		

	}

	public static function getTopResourceswithLeaves() {
		$detail= array();
		$result = Yii::app()->db->createCommand("select SUM(amount)/8 as total ,id_user from user_time where id_task in ('11','12','13') and `default`='1' and MONTH(date) = MONTH(NOW()- INTERVAL 1 MONTH) and amount>0 group by id_user order by total desc limit 5")->queryAll();
		$detailed=" ";
		$top5=" ";
		$i=0;
		foreach ($result as $value) {
			$i++;
			$res = Yii::app()->db->createCommand("select (SUM(amount)/8) as sums , id_task from user_time where id_task in ('11','12','13') and `default`='1' and MONTH(date) = MONTH(NOW()- INTERVAL 1 MONTH) and amount>0 and id_user=".$value['id_user']." group by id_task ")->queryAll();
			$detailed="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$i."- ".Users::getNameByid($value['id_user'])." <b>".Utils::formatNumber($value['total'])."</b> (";
				$typ="";
				foreach ($res as $val) {

					switch ($val['id_task']) {
						case '11':
							$typ="DO";
							break;
						case '12':
							$typ="SL";
							break;	
						case '13':
							$typ="AL";
							break;							
					}
							
							$detailed.=" <b>".Utils::formatNumber($val['sums'])."</b> ".$typ."   ,";
				}
				$detailed.=" ) <br/> ";
				
				$top5 .= $detailed;
			}

		return $top5;		

	}


	public static function getAverageWorkingHourperHeadperDay() {

		$result = Yii::app()->db->createCommand("select (SUM(amount)/21.75) from user_time where amount>0 and id_task not in (select id from default_tasks where id_parent='2') and (MONTH(date)= MONTH(NOW()- INTERVAL 1 MONTH) and YEAR(date)=YEAR(NOW() - INTERVAL 1 MONTH ) )")->queryScalar();
		$countusers=Users::getcountActiveNotAdminUsers();
		
		return $result/$countusers;		

	}


	public static function getAverageWorkingHourperHeadperTwelveMonth() {

		$result = Yii::app()->db->createCommand("select (SUM(amount)/261) from user_time where amount>0 and id_task not in (select id from default_tasks where id_parent='2') and date >= NOW()- INTERVAL 12 MONTH ")->queryScalar();
		$countusers=Users::getcountActiveNotAdminUsers();
		
		return $result/$countusers;		

	}

	public static function getAverageWorkingHourperTeamperDay($team) {

		if($team=='TECH') {
			$tech="and id_user in ("; 
			
				foreach(UserPersonalDetails::getTech() as $o){
					$tech.=" '".$o['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;

				$countusers=count(UserPersonalDetails::getTech());
		}

		if($team=='OPS') {
			$ops="and id_user in ("; 
			
				foreach(UserPersonalDetails::getOps() as $o){
					$ops.=" '".$o['id']."',";
				}
				$ops.=" 0 ) ";
				$resources=$ops;
				$countusers=count(UserPersonalDetails::getOps());
		}

		$result = Yii::app()->db->createCommand("select (SUM(amount)/21.75) from user_time where amount>0 and id_task not in (select id from default_tasks where id_parent='2') and MONTH(date)= MONTH(NOW()- INTERVAL 1 MONTH) ".$resources." ")->queryScalar();
		
		
		return $result/$countusers;		

	}

	public static function getAverageWorkingHourperTeamperTwelveMonth($team) {


		if($team=='TECH') {
			$tech="and id_user in ("; 
			
				foreach(UserPersonalDetails::getTech() as $o){
					$tech.=" '".$o['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;
		}

		if($team=='OPS') {
			$ops="and id_user in ("; 
			
				foreach(UserPersonalDetails::getOps() as $o){
					$ops.=" '".$o['id']."',";
				}
				$ops.=" 0 ) ";
				$resources=$ops;
		}

		$result = Yii::app()->db->createCommand("select (SUM(amount)/261) from user_time where amount>0 and id_task not in (select id from default_tasks where id_parent='2') and MONTH(date) >= MONTH(NOW()- INTERVAL 12 MONTH) ".$resources." ")->queryScalar();
		$countusers=Users::getcountActiveNotAdminUsers();
		
		return $result/$countusers;		

	}

	public static function getSNSINTERNALDuringLastMonth() {

		$result = Yii::app()->db->createCommand("select SUM(amount)/8 from user_time where id_task in ( select id from default_tasks where id_parent='315' ) and `default`='1' and MONTH(date) = MONTH(NOW()- INTERVAL 1 MONTH) and amount>0")->queryScalar();
		return $result;		

	}

	public static function  getSNSINTERNALDuringLastTwelveMonth() {

		$result = Yii::app()->db->createCommand("select (SUM(amount)/8)/12 from user_time where id_task in ( select id from default_tasks where id_parent='315' ) and `default`='1' and MONTH(date) >= MONTH(NOW()- INTERVAL 12 MONTH) and amount>0")->queryScalar();
		return $result;		

	}


	public static function getWorkPerRescWeekend($resc,$from,$to){


		$checkBranch= Users::getBranchByUser($resc);

		if ($checkBranch==56 ||   $checkBranch==452)
		{
			$result = Yii::app()->db->createCommand(" select DISTINCT(date), SUM(amount) as tot from user_time where status=1 and id_task not in (select id from default_tasks where id_parent='2')  and date >='".$from."' and date<='".$to."' and id_user='".$resc."' and amount>0 and weekday(date) in (4,5) GROUP BY date")->queryAll();
		}
		else
		{
			$result = Yii::app()->db->createCommand(" select DISTINCT(date), SUM(amount) as tot from user_time where status=1 and id_task not in (select id from default_tasks where id_parent='2')  and date >='".$from."' and date<='".$to."' and id_user='".$resc."' and amount>0 and weekday(date) in (5,6) GROUP BY date")->queryAll();
		}
		//$result = Yii::app()->db->createCommand("select SUM(amount) from user_time where id_task not in (select id from default_tasks where id_parent='2')  and date >='".$from."' and date<='".$to."' and id_user='".$resc."' and amount>0")->queryScalar();
		
		

		$tot = array_sum(array_column($result,'tot'));

		$workingdays= sizeof($result);
		if ($workingdays==0)
		{
			$workingdays=1;
		}
			
		//print_r($workingdays);exit;
		/*foreach ($result as $res) {
			if ($res['tot']>=8)
			{
				$workingdays++;
			}
		}*/

		//$workingdays=Timesheets::getWorkingDays($from,$to);

		return $tot/$workingdays;	

	}

	public static function getTotalHoursWorkPerRes($resc,$from,$to)
	{

		$result = Yii::app()->db->createCommand(" select DISTINCT(date), SUM(amount) as tot from user_time where id_task not in (select id from default_tasks where id_parent='2')  and date >='".$from."' and date<='".$to."' and id_user='".$resc."' and amount>0  GROUP BY date")->queryAll();
		$tot = array_sum(array_column($result,'tot')); 

		return $tot;	

	}

	public static function getWorkPerResc($resc,$from,$to){


		$checkBranch= Users::getBranchByUser($resc);

		if ($checkBranch==56 ||   $checkBranch==452)
		{
			$result = Yii::app()->db->createCommand(" select DISTINCT(u.date), SUM(u.amount) as tot from user_time u, timesheets t where u.id_task not in (select id from default_tasks where id_parent='2')  
and u.date >='".$from."' and u.date<='".$to."' and u.id_user='".$resc."' and u.amount>0 and weekday(u.date) in (0,1,2,3,6) and t.id=u.id_timesheet and t.`status`='Approved'
GROUP BY u.date")->queryAll();
		}
		else
		{
			$result = Yii::app()->db->createCommand(" select DISTINCT(u.date), SUM(u.amount) as tot from user_time u, timesheets t where u.id_task not in (select id from default_tasks where id_parent='2')  
and u.date >='".$from."' and u.date<='".$to."' and u.id_user='".$resc."' and u.amount>0 and weekday(u.date) in (0,1,2,3,4) and t.id=u.id_timesheet and t.`status`='Approved'
GROUP BY u.date")->queryAll();
		}
		//$result = Yii::app()->db->createCommand("select SUM(amount) from user_time where id_task not in (select id from default_tasks where id_parent='2')  and date >='".$from."' and date<='".$to."' and id_user='".$resc."' and amount>0")->queryScalar();
		
		

		$tot = array_sum(array_column($result,'tot'));

		$workingdays= sizeof($result);

		if ($workingdays==0)
		{
			$workingdays=1;
		}
		//print_r($workingdays);exit;
		/*foreach ($result as $res) {
			if ($res['tot']>=8)
			{
				$workingdays++;
			}
		}*/

		//$workingdays=Timesheets::getWorkingDays($from,$to);

		return $tot/$workingdays;	

	}

	public static function getWorkingDays($startDate, $endDate){

 		$begin=strtotime($startDate);
 		$end=strtotime($endDate);
 		if($begin>$end){
 		 echo "startdate is in the future! <br />";
  			return 0;
 		}else{
   		$no_days=0;
   		$weekends=0;
  		while($begin<=$end){
    		$no_days++; // no of days in the given interval
    		$what_day=date("N",$begin);
     		if($what_day>5) { // 6 and 7 are weekend days
          		$weekends++;
     		};
    		$begin+=86400; // +1 day
  		};
  		$working_days=$no_days-$weekends;
  		return $working_days;
 		}
 	}

 	public static function getBillPerResc($resc,$from,$to){

		$result = Yii::app()->db->createCommand("
select  IFNULL(TRUNCATE(bill/tot,2),0) as perc  from 

(select sum(r.amount)*100 as bill from (
select  sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and date >='".$from."' and date<='".$to."' and id_user='".$resc."'
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes'  and date >='".$from."' and date<='".$to."'   and id_user='".$resc."'
) as r) as billable 
,
 (select sum(r.amount) as tot from (
select sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1'  and date >='".$from."' and date<='".$to."'  and id_user='".$resc."'
union all
select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1'  and date >='".$from."' and date<='".$to."'  and id_user='".$resc."'
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1'  and date >='".$from."' and date<='".$to."'  and id_user='".$resc."'

) as r ) as total")->queryScalar();

 		return $result;

 	}

public  static function getTicketByUTID($id_task, $date){


		$id_user_time = Yii::app()->db->createCommand()
				->select('id')
				->from('user_time')
				->where('id_task=:id_task AND id_user=:id_user AND date=:date', array(':id_task' => $id_task, ':id_user' => Yii::app()->user->id, ':date' => $date))
				->queryScalar(); 

		if($id_user_time !== false)
		{
			return Yii::app()->db->createCommand()
				->select('ticket_id')
				->from('ticket_id')
				->where('id_user_time=:id_user_time', array(':id_user_time' => $id_user_time))
				->queryScalar();
		}
		else
		{
			return false;
		}

}
public  static function getSRsByUTID($id_task, $date){


		return Yii::app()->db->createCommand()
				->select('srs')
				->from('user_time')
				->where('id_task=:id_task AND id_user=:id_user AND date=:date', array(':id_task' => $id_task, ':id_user' => Yii::app()->user->id, ':date' => $date))
				->queryScalar(); 

}
public  static function getIRByUTID($id_task, $date){


		$id_user_time = Yii::app()->db->createCommand()
				->select('id')
				->from('user_time')
				->where('id_task=:id_task AND id_user=:id_user AND date=:date', array(':id_task' => $id_task, ':id_user' => Yii::app()->user->id, ':date' => $date))
				->queryScalar(); 

		if($id_user_time !== false)
		{
			return Yii::app()->db->createCommand()
				->select('inst_id')
				->from('inst_id')
				->where('id_user_time=:id_user_time', array(':id_user_time' => $id_user_time))
				->queryScalar();
		}
		else
		{
			return false;
		}

}

public  static function getCustomerByUTID($id_task, $date){


		$id_user_time = Yii::app()->db->createCommand()
				->select('id')
				->from('user_time')
				->where('id_task=:id_task AND id_user=:id_user AND date=:date', array(':id_task' => $id_task, ':id_user' => Yii::app()->user->id, ':date' => $date))
				->queryScalar(); 

		if($id_user_time !== false)
		{
			return Yii::app()->db->createCommand()
				->select('id_customer')
				->from('ca_customer')
				->where('id_user_time=:id_user_time', array(':id_user_time' => $id_user_time))
				->queryScalar();
		}
		else
		{
			return false;
		}

}
public  static function getReasonByUTID($id_task, $date){


		$id_user_time = Yii::app()->db->createCommand()
				->select('id')
				->from('user_time')
				->where('id_task=:id_task AND id_user=:id_user AND date=:date', array(':id_task' => $id_task, ':id_user' => Yii::app()->user->id, ':date' => $date))
				->queryScalar(); 

		if($id_user_time !== false)
		{
			return Yii::app()->db->createCommand()
				->select('reason')
				->from('user_task_reasons')
				->where('id_user_time=:id_user_time', array(':id_user_time' => $id_user_time))
				->queryScalar();
		}
		else
		{
			return false;
		}

}
public static function getAllTimeSheetTasks()
	{
		$result =  Yii::app()->db->createCommand("select projects.customer_id , projects.id as projectid,projects_tasks.id, projects_phases.id as id_phase, projects_phases.description as phase_name, projects_tasks.description as taskname ,'projects' as tasktype
					from projects_tasks 
					join projects_phases on projects_tasks.id_project_phase = projects_phases.id					
					join user_task on projects_tasks.id = user_task.id_task
					join projects on projects_phases.id_project = projects.id				
					where user_task.id_user='3' 
union

select maintenance.customer , maintenance.id_maintenance, maintenance_services.id , support_services.type , codelkups.codelkup , support_services.service , 'maintenance' as tasktype
				from maintenance_services 
				join support_services on maintenance_services.id_service=support_services.id
				join maintenance on maintenance_services.id_contract= maintenance.id_maintenance
				join codelkups on support_services.type= codelkups.id
				where codelkups.id_codelist='16'")->queryAll();
		$customers = array();

		$tasks="";
		$id_phase="";
		foreach ($result as $i=>$res)
		{
			if($id_phase!=$res['id_phase']){

			$tasks.="<li class='userAssign uProject'>";
			$tasks.="<input type='checkbox' data-id-project='".$res['projectid']."' id='phase_".$res['id_phase']."' value='phase_".$res['id_phase']."' name='phases[]' />";
			$tasks.="<label for='phase_".$res['id_phase']."' class='phaseInput'>".$res['phase_name']."</label> </li>";

			$tasks.="<li class='userAssign uTask'> ";
			$tasks.="<input type='checkbox' data-id-parent='".$res['id_phase']."' id='".$res['id']."' value='".$res['id']."' name='tasks[]' />";
			$tasks.="<label for='".$res['id']."' >".$res['taskname']."</label> </li>";
			
			//$id_phase=$res['id_phase'];
			$tasks=$res['phase_name'];

			}else{

			//$tasks.="<li class='userAssign uTask'> ";
			//$tasks.="<input type='checkbox' data-id-parent='".$res['id_phase']."' id='".$res['id']."' value='".$res['id']."' name='tasks[]' />";
		//	$tasks.="<label for='".$res['id']."' >".$res['taskname']."</label> </li>";
					$tasks=$res['taskname'];
			}
										
									
			$customers[$i]['label'] =  $tasks;
			$customers[$i]['id'] = $res['id'];
		}
		return $customers;
	}




	public static function getUserTimesheetTasksGrid($id_timesheet, $user, $default = 0)
	{
		if ($default == 0)
		{
			return Yii::app()->db->createCommand()
					->select('user_time.*, projects_tasks.billable as task_billabillity, projects_tasks.description as task_description, projects_phases.id_project, projects.name as project_name')
									->from('user_time')
									->leftJoin('projects_tasks', 'projects_tasks.id = user_time.id_task')
									->leftJoin('projects_phases', 'projects_phases.id = projects_tasks.id_project_phase')
									->leftJoin('projects', 'projects_phases.id_project = projects.id')
									->where('id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default', array(':id_user' => $user ,':id_timesheet' => $id_timesheet, ':default' => 0))
									->order('projects.id')
									->group('user_time.id_task')
									->queryAll();
		}
		if ($default == 1)
		{	
			return Yii::app()->db->createCommand()
								->select('t.id, t.name, t.billable, m.name as parent, m.id as id_project')
								->from('user_time')
								->join('default_tasks t', 't.id = user_time.id_task')
								->join('default_tasks m', 'm.id = t.id_parent')
								->where('id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default AND t.id_maintenance IS NULL', array(':id_user' =>$user, ':id_timesheet' => $id_timesheet, ':default' => 1))
								->order('t.id_parent asc ,t.name asc')
								->group('user_time.id_task')
								->queryAll();
		
		}						
		if ($default == 2)
		{						
			return  Yii::app()->db->createCommand()
						->select('user_time.*, "Yes" as task_billabillity, support_services.service as task_description, maintenance.id_maintenance as id_project, CONCAT(customers.name," Support - ",codelkups.codelkup) as project_name')
									->from('user_time')
									->leftJoin('maintenance_services', 'maintenance_services.id = user_time.id_task')
									->leftJoin('support_services', 'support_services.id = maintenance_services.id_service')
									->leftJoin('maintenance', 'maintenance_services.id_contract = maintenance.id_maintenance')
									->leftJoin('customers', 'maintenance.customer = customers.id')
									->leftJoin('codelkups', 'codelkups.id = maintenance.support_service')
									->where('user_time.id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default', array(':id_user' =>$user, ':id_timesheet' => $id_timesheet, ':default' => 2))
									->order('maintenance.id_maintenance')
									->group('user_time.id_task')
								->queryAll();
		}
		if ($default == 3)
		{
			return Yii::app()->db->createCommand()
					->select('user_time.*, internal_tasks.billable as task_billabillity, internal_tasks.description as task_description, internal_tasks.id_internal as id_project, internal.name as project_name')
									->from('user_time')
									->leftJoin('internal_tasks', 'internal_tasks.id = user_time.id_task')
									->leftJoin('internal', 'internal_tasks.id_internal = internal.id')
									->where('id_user=:id_user AND id_timesheet=:id_timesheet AND user_time.default=:default', array(':id_user' => $user ,':id_timesheet' => $id_timesheet, ':default' => 3))
									->order('internal.id')
									->group('user_time.id_task')
									->queryAll();
		}
		
	}



	public static function getCurrentProjectTotalTimeGrid($id_timesheet, $user ,$id_project, $date = '', $default = 0)
	{
		if($default == 0)
		{
			if($date == '')
			{
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('user_time')
				->join('projects_tasks', 'user_time.id_task = projects_tasks.id')
				->join('projects_phases', 'projects_tasks.id_project_phase = projects_phases.id')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_project=:id_project AND user_time.`default`=:default',
						array(':id_user' => $user , ':id_timesheet' => $id_timesheet, ':id_project' => $id_project, ':default' => $default))
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));
				
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('user_time')
				->join('projects_tasks', 'user_time.id_task = projects_tasks.id')
				->join('projects_phases', 'projects_tasks.id_project_phase = projects_phases.id')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_project=:id_project AND date=:date AND user_time.`default`=:default',
						array(':id_user' => $user , ':id_timesheet' => $id_timesheet, ':id_project' => $id_project, ':date' => $fDate, ':default' => $default))
						->queryScalar();
			}
		}
			if($default == 2)
		{
			if($date == '')
			{
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('maintenance_services')
				->leftJoin('user_time', 'maintenance_services.id = user_time.id_task AND user_time.default = 2')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet and maintenance_services.id_contract=:id_parent',
						array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project))
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));
			
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('maintenance_services')
				->join('user_time', 'maintenance_services.id = user_time.id_task AND user_time.default = 2')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet  and maintenance_services.id_contract=:id_parent AND user_time.`date`="'.$fDate.'"',
						array(':id_user' => Yii::app()->user->id, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project))
						->queryScalar();
			}
			
		}

		if($default == 1)
		{
			if($date == '')
			{
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('default_tasks')
				->leftJoin('user_time', 'default_tasks.id = user_time.id_task AND user_time.default = 1')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_parent=:id_parent',
						array(':id_user' => $user , ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project))
						->queryScalar();
			}
			else
			{
				$fDate = date('Y-m-d', strtotime($date));
			
				$amount = Yii::app()->db->createCommand()
				->select('SUM(user_time.amount)')
				->from('default_tasks')
				->join('user_time', 'default_tasks.id = user_time.id_task AND user_time.default = 1')
				->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_parent=:id_parent AND user_time.`date`=:date',
						array(':id_user' =>  $user, ':id_timesheet' => $id_timesheet, ':id_parent' => $id_project, ':date' => $fDate))
						->queryScalar();
			}
		}

		return ($amount !== false && $amount !== NULL ? $amount : '0.00');
	}


	public static function getUserTimeOnTaskByDayGrid($id_timesheet,$user, $id_task, $date = '', $default = 0)
	{	
		if($date == '')
		{
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_task=:id_task AND user_time.`default`=:default',
					array(':id_user' => $user, ':id_timesheet' => $id_timesheet, ':id_task' => $id_task, ':default' => $default))
					->queryScalar();
			
			return ($amount !== false && $amount !== NULL ? $amount : '0.00');
		}
		else
		{
			$fDate = date('Y-m-d', strtotime($date));
			$data = Yii::app()->db->createCommand()
					->select('user_time.amount, user_time.comment')
					->from('user_time')
					->where('id_user=:id_user AND id_timesheet=:id_timesheet AND id_task=:id_task AND date=:date AND user_time.`default`=:default', 
							array(':id_user' => $user, ':id_timesheet' => $id_timesheet, ':id_task' => $id_task, ':default' => $default, ':date' => $fDate))
					->queryRow();
		
			$returnArr['amount'] = ($data['amount'] !== false && $data['amount'] !== NULL ? $data['amount'] : '0.00');
			$returnArr['comment'] = $data['comment'];
			
			return $returnArr;
		}
	}


	public static function getTotalTimesGrid($id_timesheet, $user, $date = '')
	{
		if($date == '')
		{
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet',
					array(':id_user' => $user, ':id_timesheet' => $id_timesheet))
					->queryScalar();
		}
		else
		{
			$fDate = date('Y-m-d H:i:s', strtotime($date));
				
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND date=:date',
					array(':id_user' => $user, ':id_timesheet' => $id_timesheet, ':date' => $fDate))
					->queryScalar();
		}
		
		return ($amount !== NULL ? $amount : '0.00');
	}
	
	
	public static function getTotalBillableTimesGrid($id_timesheet, $user, $date = '')
	{
		if($date == '')
		{
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->leftJoin('projects_tasks', 'user_time.id_task = projects_tasks.id')
			->leftJoin('default_tasks', 'default_tasks.id= user_time.id_task')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND ((projects_tasks.billable = "Yes" AND user_time.default = 0) OR (default_tasks.billable = "Yes" AND user_time.default = 1))',
					array(':id_user' => $user, ':id_timesheet' => $id_timesheet))
					->queryScalar();
		}
		else
		{
			$fDate = date('Y-m-d H:i:s', strtotime($date));
		
			$amount = Yii::app()->db->createCommand()
			->select('SUM(user_time.amount)')
			->from('user_time')
			->leftJoin('projects_tasks', 'user_time.id_task = projects_tasks.id')
			->leftJoin('default_tasks', 'default_tasks.id= user_time.id_task')
			->where('id_user=:id_user AND id_timesheet=:id_timesheet AND date=:date AND ((projects_tasks.billable = "Yes" AND user_time.default = 0) OR (default_tasks.billable = "Yes" AND user_time.default = 1))',
					array(':id_user' => $user, ':id_timesheet' => $id_timesheet, ':date' => $fDate))
					->queryScalar();
		}
		
		return ($amount !== NULL ? $amount : '0.00');
	}

public static function sss(){
		$id_user=Yii::app()->user->id;
		$result = Yii::app()->db->createCommand("SELECT p.customer_id as id,c.name as name FROM projects_tasks pt,user_task ut, projects_phases pp, projects p,users u, customers c
where ut.id_task=pt.id and pt.id_project_phase=pp.id and p.id=pp.id_project and ut.id_user=".$id_user." and p.status=1 and c.id=p.customer_id
GROUP BY c.name")->queryAll();

		$customers = array();
		foreach ($result as $i => $res) {
			$customers[$i]['label'] = $res['name'];
			$customers[$i]['id']=$res['id'];
		}

		return $customers;
	}
	
	public static function calculateBillabilityGrid($total, $user, $billable)
	{
		$total = (float)$total;
		$billable = (float)$billable;
 		
 		if($total != 0)
 		{
 			return number_format($billable / $total, 2) * 100 . '%';
 		}
 		else
 		{
 			return '0%';
 		}
	}

public static function getallReasons($id_timesheet){
$reason=" "		;
		$result = Yii::app()->db->createCommand("select u.id_task, u.`default` as dft, date, `comment`, reason, u.amount as amount from user_task_reasons utr, user_time u where utr.id_user_time=u.id and u.id_timesheet='".$id_timesheet."' and u.amount>0")->queryAll();
		foreach ($result as $value) {
			$pname=" ";
			$tname=" ";
			if($value['dft']==0){

					$pname=Projects::getNameById(Projects::getProjectId($value['id_task']));
					$tname=ProjectsTasks::getNameById($value['id_task']);
			}
			if($value['dft']==1){
				$pname= "Support";
				$tname= DefaultTasks::getDescription($value['id_task']);
			}
			if($value['dft']==2){
				$pname= MaintenanceServices::getMaintenanceDescriptionByTask($value['id_task']);
				$tname= MaintenanceServices::getTaskDescription($value['id_task']);
			}

			$reason.="<b>Project:</b> ".$pname." - <b>Task:</b> ".$tname." - <b>Date:</b> ".$value['date']." - <b>Task Comment:</b> ".$value['comment']." -  <b>Working Reason:</b> ".$value['reason']."  -  <b>Amount:</b> ".$value['amount']." hours. <br/>";
		}
		return $reason;
	}


	public static function getUserTimebyTimesheet($id_timesheet){

			$result = Yii::app()->db->createCommand("select u.id, u.id_user,u.date, u.`comment`,utr.reason from user_task_reasons utr, user_time u where utr.id_user_time=u.id and u.id_timesheet='".$id_timesheet."'")->queryAll();
			
			return $result;
		}
	public static function checkifPHorWeekEnd($id_timesheet){		
			$result = Yii::app()->db->createCommand("select count(1) from user_task_reasons where id_user_time in (select id from user_time where id_timesheet='".$id_timesheet."' and amount>0) ")->queryScalar();
		
			return $result;
		}

	public static function sendNotificationsEmails($id_timesheet)
	{
		 
		$notif = EmailNotifications::getNotificationByUniqueName('timesheet_ph_weekend');
		$id_user = Yii::app()->user->id;
		
		$reasons = Timesheets::getallReasons($id_timesheet);

		
		$linemanger_email=UserPersonalDetails::getLineManagerEmailById($id_user);
	
		if ($notif != NULL)
		{			
				$to_replace = array(
						'{user}', 
						'{timesheet}', 
						'{reasons}',
						'{approve_link}',
						'{reject_link}'
				);
				
				$replace = array(
						
						Users::getUsername($id_user),
						$id_timesheet,
						$reasons,												
						'<a href="'.Yii::app()->createAbsoluteUrl('timesheets/Createrequest/?id_timesheet='.$id_timesheet.'').'">Approve</a>',
						'<a href="'.Yii::app()->createAbsoluteUrl('timesheets/RejectPHWeekendTasks/?id_timesheet='.$id_timesheet.'').'">Reject</a>'
				);
				
				$subject =  $notif['name'];


				$body = str_replace($to_replace, $replace, $notif['message']);
				
					Yii::app()->mailer->Subject= $subject;
					//$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->AddAddress($linemanger_email);
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true))
					{
					
					}
			
		}
	}
	
	public  static function timesheetvalidate($id_timesheet, $id_user){
			
		//  Resource logged a Day off in lieu of a working day not requested and approved on SNSit 
		$dayoffs=Yii::app()->db->createCommand("SELECT l.date , u.id  FROM user_time u ,lieu_of l where u.`default`=1 and u.id_task='11' and u.id=l.id_user_time and u.id_timesheet='".$id_timesheet."' and u.amount>0 and u.`status`=0 and u.id_user='".$id_user."' ")->queryAll();

		foreach ($dayoffs as $value) {

			$requested=Yii::app()->db->createCommand("select count(1) from requests where user_id='".$id_user."' and '".$value['date']."' BETWEEN startDate and endDate and type='567' and `status`=1")->queryScalar();		
			if($requested==0){
			Yii::app()->db->createCommand("INSERT INTO timesheet_validations(id_user_time,id_timesheet,checked ,type) values(".$value['id'].",".$id_timesheet.",0,1) ")->execute();
			}
		}

		//clear alerts if existing
		Yii::app()->db->createCommand()
			->delete('timesheet_validations', 'id_timesheet=:id', array(':id'=>$id_timesheet));

		//More than 5 hours spent on SNS Internal tasks (excluding Travel) per resource\week  
	
		$internal=Yii::app()->db->createCommand("select id_timesheet , sum(amount) from user_time where id_task in('305','306','314','316','317') and `default`=1 and id_user='".$id_user."'  and id_timesheet='".$id_timesheet."' and status='0' group by id_timesheet having sum(amount)>5 ")->queryAll();
				foreach ($internal as $value) {
						Yii::app()->db->createCommand("INSERT INTO timesheet_validations (id_timesheet,checked,type) values(".$value['id_timesheet'].",0,2)")->execute();
						
				}

		// More than 6 hours spent on Travel per resource\day
		$travel=Yii::app()->db->createCommand("select id from user_time where id_task='318' and `default`=1 and id_user='".$id_user."' and amount>6 and id_timesheet='".$id_timesheet."' and status='0'")->queryAll();
				foreach ($travel as $value) {
						Yii::app()->db->createCommand("INSERT INTO timesheet_validations (id_user_time,id_timesheet,checked,type)  values(".$value['id'].",".$id_timesheet.",0,3)")->execute();
						
				}
		//More than 2 hours spent on Internal Meetings task per resource\day 
		$meetings=Yii::app()->db->createCommand("select id from user_time where id_task='22' and `default`=1 and id_user='".$id_user."' and amount>2 and id_timesheet='".$id_timesheet."' and status='0'")->queryAll();
				foreach ($meetings as $value) {
						
						Yii::app()->db->createCommand("INSERT INTO timesheet_validations (id_user_time,id_timesheet,checked,type)  values(".$value['id'].",".$id_timesheet.",0,4)")->execute();
				}
				
		//More than 2 hours spent on Internal Projects tasks per resource\day 
		$projects=Yii::app()->db->createCommand("select id from user_time where id_task='23' and `default`=1 and id_user='".$id_user."' and amount>2 and id_timesheet='".$id_timesheet."' and status='0'")->queryAll();
				foreach ($projects as $value) {
						
					Yii::app()->db->createCommand("INSERT INTO timesheet_validations (id_user_time,id_timesheet,checked,type) values(".$value['id'].",".$id_timesheet.",0,5)")->execute();
				}
		//Worked less than 40 hours per week			
		$ltfourty=Yii::app()->db->createCommand("select sum(u.amount) from user_time u, timesheets t where u.id_user=".$id_user." and u.id_timesheet=".$id_timesheet." and t.status='Submitted' and t.id=u.id_timesheet ")->queryScalar();		
			if($ltfourty < 40){
				Yii::app()->db->createCommand("INSERT INTO timesheet_validations (id_timesheet,checked,type)  values(".$id_timesheet.",0,6)")->execute();
			}

		//A public holiday is logged against a normal working day in Beirut or Dubai comparing with the holidays defined in SNSit calendar	
		$holiday=Yii::app()->db->createCommand("select u.id , u.date , upd.branch from user_time u , user_personal_details upd where u.id_task='14' and u.`default`=1 and u.id_user=upd.id_user and u.id_user='".$id_user."' and u.amount>0 and u.id_timesheet='".$id_timesheet."' and u.status='0'")->queryAll();
				foreach ($holiday as $value) {
						
						if($value['branch']=='31'){

							$office='Lebanon';

						}else if($value['branch']=='56') {

							$office='UAE';

						}else if($value['branch']=='689') {

							$office='USA';

						}
						else if($value['branch']=='120') {

							$office='Australia';

						}
						else{

							$office='KSA';

						}
					
					$requested=Yii::app()->db->createCommand("select count(1) from public_holidays p where p.date='".$value['date']."' and p.office like '%".$office."%' ")->queryScalar();		
				
					if($requested==0){
						Yii::app()->db->createCommand("INSERT INTO timesheet_validations (id_user_time,id_timesheet,checked,type) values(".$value['id'].",".$id_timesheet.",0,7)")->execute();
					}	
				}	

			
					
	}
 public static function getValidationDescription($type){



	$desc=Yii::app()->db->createCommand("select description from validations where id='".$type."' ")->queryScalar();		
	
	return $desc	;		

 }


 public static function getTimebyUT($ut , $timesheet=0 ,$type=0){

 		if($timesheet==0){
	$time=Yii::app()->db->createCommand("select amount from user_time where id='".$ut."' ")->queryScalar();		
	}else {
		if($type==2){
				$time=Yii::app()->db->createCommand("select sum(amount) from user_time where id_timesheet='".$timesheet."' and `default`=1 and id_task in('305','306','314','316','317') ")->queryScalar();	

		}else{
				$time=Yii::app()->db->createCommand("select sum(amount) from user_time where id_timesheet='".$timesheet."' ")->queryScalar();	
		}	
	}
	return $time	;		

 }


 public static function getCommentyUT($ut , $timesheet=0){

 		if($timesheet==0){
	$time=Yii::app()->db->createCommand("select comment from user_time where id='".$ut."' ")->queryScalar();		
	}else {
		$time=" ";		
	}
	return $time	;		

 }


 public static function getCommentyUT2($ut , $timesheet=0, $type=0){
$time=' ';
 		if($timesheet==0){
	$time=Yii::app()->db->createCommand("select comment from user_time where id='".$ut."' ")->queryScalar();		
	}else {
		if($type==2){
				$tasks=Yii::app()->db->createCommand("select comment from user_time where id_timesheet='".$timesheet."' and comment is not null and comment <> '' and `default`=1 and id_task in('305','306','314','316','317') ")->queryall();	
				foreach($tasks as $task)
				{
					$time.= '- '.$task['comment'].' ';
				}
		}else{
			$time=' ';
				//$time=Yii::app()->db->createCommand("select comment from user_time where id_timesheet='".$timesheet."' ")->queryScalar();	
		}	
	}
	return $time	;		

 }
 
  public static function getAlertyUT($ut , $timesheet=0){

 		if($timesheet==0){
	$time=Yii::app()->db->createCommand("select name from default_tasks where id in (select id_task from user_time where id='".$ut."'  )")->queryScalar();		
	}else {
		$time=" ";		
	}
	return $time	;		

 }

 
  public static function getAlertyUT2($ut , $timesheet=0, $type=0){
	$time=' ';
 		if($timesheet==0){
		$time=Yii::app()->db->createCommand("select name from default_tasks where id in (select id_task from user_time where id='".$ut."'  )")->queryScalar();	
	}else {
	
		if($type==2){
				$tasks=Yii::app()->db->createCommand("select id_task from user_time where id_timesheet='".$timesheet."' and `default`=1 and id_task in('305','306','314','316','317') ")->queryall();	
				foreach($tasks as $task)
				{
					$time.= '- '.DefaultTasks::getDescription($task['id_task']).' ';
				}
		}else{
			$time=' ';
				//$time=Yii::app()->db->createCommand("select comment from user_time where id_timesheet='".$timesheet."' ")->queryScalar();	
		}	
	}
	return $time	;		

 }
   public static function getpertask($task, $user, $date){

 		if($task!=0){
	$time=Yii::app()->db->createCommand("select SUM(amount) from user_time where id_task =".$task." and id_user=".$user." and date<='".$date."' ")->queryScalar();		
	}else {
		$time=0;		
	}
	return $time;		

 }

 public static function checkifGolive($id_timesheet, $id_user){
 	
 		$time=Yii::app()->db->createCommand("select distinct id_task from user_time where  id_timesheet='".$id_timesheet."' and amount>0 and id_user='".$id_user."' and `default`=0 and  id_task in (select id from projects_tasks where id_project_phase in (select id from projects_phases where description like '%Go-Live%')) ")->queryAll();	
 		if($time){
 				$pid=0;
 			foreach ($time as $value) {

 				if($pid<>Projects::getProjectId($value['id_task'])){


 				$pid=Projects::getProjectId($value['id_task']);	
 				$golive=Projects::getGoliveStatus($pid);
		 				if($golive<>1)
		 				{
		 				$pname=Projects::getNameByid($pid);

		 				$customer=Customers::getNameById(Projects::getCustomerByProject($pid));

		 				$notif=EmailNotifications::getNotificationByUniqueName('time_on_golive_phase');

		 				Yii::app()->mailer->ClearAddresses();

				 					if ($notif != NULL) 
				    				{
					    		
									$subject='Project entered Go-Live Phase';
									$to_replace = array(
										'{customer}',
										'{project}'
									);
									
									
													
									$replace = array(
										$customer,
										$pname
										);
									
									$body = str_replace($to_replace, $replace, $notif['message']);
									
									$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
									Yii::app()->mailer->ClearAddresses();

									foreach($emails as $email) 
									{
										if (!empty($email))
											Yii::app()->mailer->AddAddress($emails);
									}					
									Yii::app()->mailer->Subject  = $subject;
									Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
									Yii::app()->mailer->Send(true);
									
				    				
										Yii::app()->db->createCommand("UPDATE projects SET golive=1 WHERE id ='".$pid."' ")->execute();
				 					}

		 				}
						
				}
 				
 			}
 				
 			
 		}
	}
}
?>
