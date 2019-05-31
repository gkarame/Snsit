<?php
class TimesheetStatuses extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'timesheet_statuses';
	}
	public function rules(){
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>250),
			array('id, name', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'timesheets' => array(self::HAS_MANY, 'Timesheets', 'id_status'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Name',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('name',$this->name,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getLastTimesheets($status = 'Submitted'){
		$timesheets = array();	$user = Yii::app()->user->id;
		switch($status){
			case 'Submitted':			
				$timesheets1 =  Yii::app()->db->createCommand("
					SELECT u.id as userid,	t.id_user as id_user_timesheet, t.id as id,	t.timesheet_cod as code,
					t.status as timesheet_status, u.firstname,	u.lastname,	p.project_manager,	p.business_manager
				 FROM projects p 
				 LEFT JOIN projects_phases pp ON pp.id_project=p.id 
				 LEFT JOIN projects_tasks pt ON pt.id_project_phase=pp.id
				 LEFT JOIN user_time ut ON ut.id_task=pt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user WHERE (t.status = 'Submitted' OR t.status = 'In Approval') AND ut.status = 0  AND ut.amount > 0  ORDER BY u.firstname ASC")->queryAll();	
				array_push($timesheets, $timesheets1);
				$timesheets3 =  Yii::app()->db->createCommand("
					SELECT t.id as id,	t.id_user as id_user_timesheet,	dt.id as id_default_task,	dt.name as name,	dt.id_parent as id_parent,
					m.name as parent_phase, t.timesheet_cod as code, u.firstname,	u.lastname,	upd.line_manager
				 FROM default_tasks dt
				 LEFT JOIN default_tasks m ON m.id = dt.id_parent
				 LEFT JOIN user_time ut ON ut.id_task=dt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON t.id_user = u.id
				 LEFT JOIN user_personal_details upd ON u.id = upd.id_user 
				WHERE (t.status = 'Submitted' OR t.status = 'In Approval')AND ut.status = 0 AND ut.amount > 0 and dt.id_parent is not null ORDER BY u.firstname ASC")->queryAll();
				array_push($timesheets, $timesheets3);				
			break;
			default:
				$week=date('W');
				$timesheets =  Yii::app()->db->createCommand("SELECT users.id as userid, users.firstname, users.lastname,  min(timesheets.week_start) as week_start, count(timesheets.id_user) as total
FROM timesheets LEFT JOIN users ON (timesheets.id_user = users.id) WHERE timesheets.status = 'new' and users.active=1
and week < ".$week."  GROUP BY id_user  ORDER BY users.firstname ASC")->queryAll();
			break;			
		}
		if($status == "New"){
			$new = array();
			foreach($timesheets as $v)
				if($v["total"]>=1)
					array_push($new,$v);
			return $new;
		}
		return $timesheets;
	}
	public static function getCountTimesheets($status = 'Submitted'){
			$user = Yii::app()->user->id;		
		$timesheets =  Yii::app()->db->createCommand("select u.firstname , u.lastname ,t.id from timesheets t , users u  where t.id_user=u.id and t.`status`='NEW' and t.id_user='".$user."'")->queryAll();
		return $timesheets;
	}
public static function getUserLastTimesheets($status = 'Submitted'){
		$timesheets = array();	$user = Yii::app()->user->id;
				$timesheets1 =  Yii::app()->db->createCommand("SELECT u.id as userid,t.id_user as id_user_timesheet, 
					t.id as id, t.timesheet_cod as code,t.status as timesheet_status, 
					u.firstname, u.lastname,p.project_manager,   p.business_manager
				 FROM projects p 
				 LEFT JOIN projects_phases pp ON pp.id_project=p.id 
				 LEFT JOIN projects_tasks pt ON pt.id_project_phase=pp.id
				 LEFT JOIN user_time ut ON ut.id_task=pt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user
						WHERE (t.status = 'Submitted' OR t.status = 'In Approval') AND ut.status = 0  AND ut.amount > 0 AND (p.project_manager=".$user." OR p.business_manager=".$user." ) ORDER BY u.firstname ASC ")->queryAll();	
				array_push($timesheets, $timesheets1);
				$timesheets3 =  Yii::app()->db->createCommand("SELECT t.id as id,t.id_user as id_user_timesheet, dt.id as id_default_task,dt.name as name, dt.id_parent as id_parent,m.name as parent_phase,
				 t.timesheet_cod as code,u.firstname,u.lastname,upd.line_manager
				 FROM default_tasks dt
				 LEFT JOIN default_tasks m ON m.id = dt.id_parent
				 LEFT JOIN user_time ut ON ut.id_task=dt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON t.id_user = u.id
				 LEFT JOIN user_personal_details upd ON u.id = upd.id_user 
						WHERE (t.status = 'Submitted' OR t.status = 'In Approval')AND ut.status = 0 AND ut.amount > 0 and dt.id_parent is not null AND upd.line_manager=".$user."  ORDER BY u.firstname ASC ")->queryAll();
				array_push($timesheets, $timesheets3);			
		if($status == "New"){
			$new = array();
			foreach($timesheets as $v)
				if($v["total"]>1)
					array_push($new,$v);
			return $new;
		}
		return $timesheets;
	}
} ?>