<?php
class ProjectsTasks extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_tasks';
	}
	public function rules(){
		return array(
			array('id_project_phase, billable, type', 'required'),
			array('id_project_phase, complexity,module,type', 'numerical', 'integerOnly'=>true),
			array('man_days_budgeted', 'numerical','integerOnly'=>false),
			array('billable,complexity,fbr', 'length', 'max'=>3),
			array('title', 'length', 'max'=>200),
			array('keywords,notes', 'length', 'max'=>3000),
			array('type','validatetype'),
			
			array('id, id_project_phase, man_days_budgeted, billable, description,type, fbr, title, module, keywords,notes, existsfbr, redundant_send', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'users' => array(self::HAS_MANY, 'UserTask', 'id_task'),
			'phase' => array(self::HAS_MANY, 'ProjectPhases', 'id_project_phase'),
		);
	}
    /*
    * Author: Mike
    * Date: 03.07.19
    * Change notes to short description and make the field wider + mandatory for FBR type tasks + put it on second row
    */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project_phase' => 'Id Project Phase',
			'man_days_budgeted' => 'Man Days Budgeted',
			'days_spent' => 'Days Spent',
			'billable' => 'Billable',
			'description' => 'Description',
			'fbr' => 'FBR#',
			'title' => 'Title',
			'module' => 'Module',
			'keywords' => 'Keywords',
			'existsfbr' => 'Previously Done?',
			'notes' => 'Short description',
			'parent_fbr' => 'Parent FBR',
			'type' => 'Type'
		);
	}
	
	public function validatetype(){
	 	if ( $this->type == 1 || $this->type == 4 || $this->type == 5){
	        if(empty($this->fbr)){
	        	 $this->addError('fbr','Cannot be empty.');
	        }else if (!is_numeric($this->fbr)){
	        	$this->addError('fbr','Invalid value.');
	        }else if (strlen($this->fbr)<3){
	        	$this->addError('fbr','3 digits mandatory.');
	        }else if (ProjectsTasks::ensureUnique($this->id_project_phase, $this->fbr, $this->id,$this->type)){
	        	$this->addError('fbr','FBR# must be unique.');
	        }
	        if(empty($this->title)){
	        	 $this->addError('title','Title cannot be empty.');
	        }	
	        if(empty($this->module)){
	        	 $this->addError('module','Module cannot be empty.');
	        }	 	    
	        if(empty($this->keywords)){
	        	 $this->addError('keywords','Keywords cannot be empty.');
	        }	
	        if(!empty($this->existsfbr)){
	        	if( $this->existsfbr == 2 && empty($this->parent_fbr))
	        	{ $this->addError('parent_fbr','Parent FBR must be valid.'); }
	        	else if ( $this->existsfbr ==2 )
				{
				 	if( (ProjectsTasks::getTaskIdByPTName($this->parent_fbr) ==null  ||  ProjectsTasks::getTaskIdByPTName($this->parent_fbr) =='') && trim($this->parent_fbr)!='' && !is_numeric($this->parent_fbr))
				 	{
				 		$this->addError('parent_fbr','Parent FBR is not valid.');
					}
				}
	        }    	
	    }else if ( $this->type == 3){
	        if(empty($this->fbr)){
	        	 $this->addError('fbr','Cannot be empty.');
	        }else if (!is_numeric($this->fbr)){
	        	$this->addError('fbr','Invalid value.');
	        }else if (strlen($this->fbr)<3){
	        	$this->addError('fbr','3 digits mandatory.');
	        }else if (ProjectsTasks::ensureUnique($this->id_project_phase, $this->fbr, $this->id,$this->type )){
	        	$this->addError('fbr','INT# must be unique.');
	        }
	        if(empty($this->title)){
	        	 $this->addError('title','Title cannot be empty.');
	        }	
	        if(empty($this->module)){
	        	 $this->addError('module','Module cannot be empty.');
	        }	 	    
	        if(empty($this->keywords)){
	        	 $this->addError('keywords','Keywords cannot be empty.');
	        }
	            	
	    }else{
	    	 if(empty($this->description)){
	        	 $this->addError('description','Description cannot be empty.');
	        }
	    }		
	}

	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project_phase',$this->id_project_phase);
		$criteria->compare('man_days_budgeted',$this->man_days_budgeted);	$criteria->compare('billable',$this->billable,true);
		$criteria->compare('description',$this->description,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getNameById($id){
		$name = Yii::app()->db->createCommand()
    		->select('description')
    		->from('projects_tasks')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	return ($name != false ? $name : '');
	}	
	
	public static function getNondocumentedTasksPerProject($project, $timesheet){
		$tasks = Yii::app()->db->createCommand("select ut.amount,ut.comment, IFNULL(ut.fbr,'') as fbr , pt.id from projects_tasks pt, projects_phases pp, user_time ut where pt.id=ut.id_task and pt.id_project_phase = pp.id and ut.`default`=0 and documented='no' and pp.id_project=".$project." and ut.id_timesheet= ".$timesheet."")->queryAll();
    	return $tasks;
	}
	public static function getComplexityLabel($value){
		$list = self::getComplexityList();
		return $list[$value];
	}
	public static function getTPName($task)
	{
		if(empty($task)){
			return '';
		}else{
			return Yii::app()->db->createCommand("select concat(p.name, ': ', pt.description) from projects_tasks pt, projects_phases pp, projects p where pt.id=".$task." and pt.id_project_phase=pp.id and pp.id_project=p.id  ")->queryScalar();
		}
	}
	public static function ensureUnique($phase, $fbr, $task, $type){
		if(empty($task))
		{
			$count=Yii::app()->db->createCommand("select count(1)  from projects_tasks where  id_project_phase = ".$phase." and fbr=".$fbr." and type=".$type." ")->queryScalar();
		}else{
			$count=Yii::app()->db->createCommand("select count(1)  from projects_tasks where  id_project_phase = ".$phase." and fbr=".$fbr." and id !=".$task." and type=".$type." ")->queryScalar();
		}
    	if($count>0){
    		return true;
    	}else{
    		return false;
    	}
	}	
	public static function getComplexityList(){		
		return array(
			1 => 'Low',
			2 => 'Medium',
			3 => 'High',
		); 
	}
	public static function getTaskIdByPTName($str){
		if (strpos($str,': ') !== false) {
			$pieces = explode(": ", $str);
		//	print_r($pieces);exit;
			return Yii::app()->db->createCommand("select pt.id from projects_tasks pt, projects_phases pp, projects p where pt.id_project_phase = pp.id and pp.id_project=p.id and p.name like '%".$pieces[0]."%' and pt.description like '%".$pieces[1]."%' limit 1")->queryScalar();
	   	}else{
	   		return '';
	   	}
   	}
	public static function getAllTasksAutocomplete(){
		$result= Yii::app()->db->createCommand("select pt.id, pt.description, p.name from projects_tasks pt, projects_phases pp, projects p where pt.id_project_phase = pp.id and pp.id_project=p.id and pt.description not in (select name from default_tasks) and pt.description not in ('FBRXXX','INTXXX') and (pt.type=1 or pt.type is null) order by p.name")->queryAll();
   		$tasks = array();	$j = 0;
		foreach ($result as $i=>$res){
			
				$tasks[$j]['label'] = $res['name'].': '.$res['description'];
				$tasks[$j]['id'] = $res['id'];
				$j++;
			
		}
		return $tasks;
	}
	public static function getTypeLabel($value){
		$list = self::getTypeList();
		return $list[$value];
	}	
	public static function getTypeList(){		
		return array(
			1 => 'FBR',
			2 => 'Task',
			3 => 'Interface',
			4 => 'Report',
			5 => 'Label'
		); 
	}
	public static function getExistsList(){		
		return array(
			1 => 'Not Checked',
			2 => 'Yes',
			3 => 'No'
		); 
	}
	public static function getExistsLabel($value){
		$list = self::getExistsList();
		return $list[$value];
	}	
	public static function getProjectsPerTimesheet($timesheet){
		$projects = Yii::app()->db->createCommand("select distinct(pp.id_project) from projects_tasks pt, projects_phases pp, user_time ut where pt.id=ut.id_task and pt.id_project_phase = pp.id and ut.`default`=0  and documented='no' and ut.id_timesheet=".$timesheet."")->queryAll();
    	return $projects;
	}
	public static function getProjectByTask($id){
		$project = Yii::app()->db->createCommand("select pp.id_project from projects_tasks pt, projects_phases pp where pt.id=".$id." and pt.id_project_phase = pp.id")->queryScalar();
    	return $project;
	}
	public function getdays_spent(){
		$sum = Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE id_task = {$this->id} and `default`='0'")->queryScalar();
		return $sum;
	}
	public static function getStatusBillableTask($billable,$id_task,$id_phase){		
	    $stats = array(
	        'Yes' => 'Yes',
	        'No' => 'No',
	    );
	    if(GroupPermissions::checkPermissions('projects-tasks','write')){
		    return CHtml::dropDownlist('billable', $billable, $stats, array(
		        'class'     => 'status',
		        'data-id'   => $id_task,
		    	'onchange'=>'changeInput('."value".','. $id_task.','.$id_phase.',2)',
		    	'id' => 'billable_'.$id_task,
		    ));
		}else{
			return CHtml::dropDownlist('billable', $billable, $stats, array(
		        'class'     => 'status',
		        'data-id'   => $id_task,
		    	'disabled'  => true,
		    	'id' => 'billable_'.$id_task,
		    ));
		}
	}
	public static function getAllUsersTaskNormalTech($id){
		$result =  Yii::app()->db->createCommand('SELECT id_user from user_task where id_task='.$id.' and id_user in (select id_user from user_groups where id_group in (9,13,18,19,25,32,34))  and id_user not in (select id_user from user_groups where id_group in (12,22,27))')->queryAll();	$name = "";
		foreach($result as $res){
			$result_name =  Yii::app()->db->createCommand('SELECT firstname,lastname from users where id='.$res['id_user'])->queryAll();
				foreach($result_name as $rname){	$fname =  strtoupper(substr($rname['lastname'], 0, 1));	$name.=ucwords($rname['firstname']).'.'.$fname.' ,'; }
		}
		$name= substr($name,0, (strlen($name)-1));
		return $name;
	}
	public static function getAllUsersTaskNormalOps($id){
		$result =  Yii::app()->db->createCommand('SELECT id_user from user_task where id_task='.$id.' and id_user in (select id_user from user_groups where id_group in (12,22,27))')->queryAll();	$name = "";
		foreach($result as $res){
			$result_name =  Yii::app()->db->createCommand('SELECT firstname,lastname from users where id='.$res['id_user'])->queryAll();
				foreach($result_name as $rname){	$fname =  strtoupper(substr($rname['lastname'], 0, 1));	$name.=ucwords($rname['firstname']).'.'.$fname.' ,'; }
		}
		$name= substr($name,0, (strlen($name)-1));
		return $name;
	}
	public static function getAllUsersTask($id){
		$result =  Yii::app()->db->createCommand('SELECT id_user from user_task where id_task='.$id)->queryAll();	$name = "";
		foreach($result as $res){
			$result_name =  Yii::app()->db->createCommand('SELECT firstname,lastname from users where id='.$res['id_user'])->queryAll();
				foreach($result_name as $rname){	$fname =  strtoupper(substr($rname['lastname'], 0, 1));	$name.=ucwords($rname['firstname']).'.'.$fname.' | '; }
		}
		return $name;
	}
	public static function getAllUsersTaskReport($id){
		$result =  Yii::app()->db->createCommand('SELECT id_user from user_task where id_task='.$id)->queryAll();	$name = "";
		foreach($result as $res){
			$result_name =  Yii::app()->db->createCommand('SELECT firstname,lastname from users where id='.$res['id_user'])->queryAll();
				foreach($result_name as $rname){	
					$fname =  strtoupper(substr($rname['firstname'], 0, 1));	
					$lname =  strtoupper(substr($rname['lastname'], 0, 1));	
					$name.=$fname.$lname.', '; }
		}
		$name= substr($name,0,strlen($name)-2);
		return $name;
	}
	public static function getUsersGrid($sir){
		if ($sir != '')	{
			$pos1 = strpos($sir, '|');	$pos2 = strpos($sir, '|', $pos1 + strlen('|'));			
			if($pos2 == null || $pos2 == strlen($sir)-2)
				return
					'<div class="first_it panel_container">'
							.'<div class="item_clip">'.substr(trim($sir),0,-1).'</div>'
					.'</div>';
			else
				$short_sir = Utils::getShortText($sir,$pos2);				
			return '<div class="first_it panel_container">'
							.'<div class="item_clip" style="float:left" onmouseover="showToolTip(this);" onmouseout="hideToolTip(this);">'.$short_sir['text'].'</div>'
							.'<u class="red" onmouseover="showToolTip(this);" onmouseout="hideToolTip(this);">+</u>'
							.'<div class="panel" style="left:80px;">'
								.'<div class="phead"></div>'
								.'<div class="pcontent"><div class="cover">'.substr(trim($sir),0,-1).'</div></div>'
								.'<div class="pftr"></div>'
							.'</div>'
						.'</div>';
		}
		return "";
	}
	public static function getAllTasks($id_phase) {
		$result =  Yii::app()->db->createCommand('SELECT id from projects_tasks where id_project_phase='.$id_phase)->queryAll();		
		return $result;
	}
	public static function getTaskDescByid2($id_task, $m) {
		if((preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id_task)) || $m>0){
			$id_task=preg_replace("/[^0-9]/", "", $id_task);	$idservice =  Yii::app()->db->createCommand('SELECT id_service from maintenance_services where id='.$id_task)->queryScalar();
			$result = MaintenanceServices::getDescriptionName($idservice);
		}else{
			$result =  Yii::app()->db->createCommand('SELECT description from projects_tasks where id='.$id_task)->queryScalar();
		}
		return $result;
	}
	public static function getTaskDescByid($id_task){
		if($id_task){
		if((preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id_task))){
			$id_task=preg_replace("/[^0-9]/", "", $id_task);	$idservice =  Yii::app()->db->createCommand('SELECT id_service from maintenance_services where id='.$id_task)->queryScalar();
			$result = MaintenanceServices::getDescriptionName($idservice);
		}else{
			$result =  Yii::app()->db->createCommand('SELECT description from projects_tasks where id='.$id_task)->queryScalar();
		}} else { return '';}
		return $result;
	}
	public static function getPhaseDescByid($id_task) {
		$result =  Yii::app()->db->createCommand('SELECT pp.description from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and  pt.id='.$id_task)->queryScalar();
		return $result;
	}
	public static function getTimeSpentperTask($id_task,$id_user) {
		$result =  Yii::app()->db->createCommand("select sum(amount)/8 from user_time where id_user='".$id_user."' and `default`='0' and id_task='".$id_task."' ")->queryScalar();
		return $result;
	}	
	public static function getTimeSpentperTaskID($id_task) {
		$result =  Yii::app()->db->createCommand("select sum(amount)/8 from user_time where `default`='0' and id_task='".$id_task."' ")->queryScalar();
		return $result;
	}	
	public static function getAllUsers(){
		return 	Yii::app()->db->createCommand('SELECT u.id, u.username, u.firstname, u.lastname FROM users u where active=1 order by u.firstname')->queryAll();
	}
	public static function getAsignUsers($id_task){
		return 	Yii::app()->db->createCommand('SELECT Distinct u.id, u.username, u.firstname, u.lastname FROM users u left join user_task on user_task.id_user=u.id where id_task in ('.$id_task.')')->queryAll();
	}
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
    public static function getPhasesIdByTasks($tasks){
		$allTasks = '('. implode(',',$tasks).')';
		$phases= Yii::app()->db->createCommand("SELECT distinct(pt.id_project_phase),pp.description  FROM projects_tasks  pt, projects_phases pp  where pt.id in ".$allTasks." and pt.id_project_phase=pp.id")->queryAll();
		foreach ($phases as $phase) {	$allphases[$phase['id_project_phase']]=$phase['description']; }
		return $allphases;
	}
	public static function getSign(){
		return "<select class=\"assigned_to\" style=\"width:40px;border:none;\" name=\"assigned_to\" id=\"assigned_to\">
		<option value=\"\"></option>
		<option value=\"Minus\">-</option>
		<option value=\"Plus\">+</option>
		</select>";
	}
    public static function getDay($id_task)  {
    	return Yii::app()->db->createCommand()
    		->select('man_days_budgeted')
    		->from('projects_tasks')
    		->where('id =:id', array(':id'=>$id_task))
    		->queryScalar();
    }
    public static function checkFBRExistsStat($project,$status){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT COUNT(*) from projects_tasks pt,projects_phases pp where  pt.id_project_phase= pp.id and pp.id_project =".$project." and type =1 and existsfbr =".$status)->queryScalar();
	}
	public static function getFBRsTot($project){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT COUNT(*) from projects_tasks pt, projects_phases pp where  pt.id_project_phase= pp.id and pp.id_project  =".$project." AND type =1 ")->queryScalar();		
	}
	public static function checkFBRLatest($project){
		$project = (int) $project;
		$date=  Yii::app()->db->createCommand("SELECT MAX(adddate) from projects_tasks pt, projects_phases pp where  pt.id_project_phase= pp.id and pp.id_project  =".$project." AND type =1 LIMIT 1")->queryScalar();		
		if(!empty($date))
		{
			return date("d/m/Y", strtotime($date));
		}else{
			return '';
		}

	}
	public static function checkFBRLatestUnchecked($project){
		$project = (int) $project;
		$date=  Yii::app()->db->createCommand("SELECT MIN(adddate) from projects_tasks pt, projects_phases pp where  pt.id_project_phase= pp.id and pp.id_project  =".$project." AND type =1 AND existsfbr=1 LIMIT 1")->queryScalar();		
		if(!empty($date))
		{
			return date("d/m/Y", strtotime($date));
		}else{
			return '';
		}

	}
	public static function getTitleTask($type, $descr)
	{
		if($type == 1){
			return "FBR".$descr;
		}else if($type==3){
			return "INT".$descr;
		}else if($type==4){
			return "RPT".$descr;
		}else if($type==5){
			return "LABEL".$descr;
		}else{
			return $descr;
		}
	}
}?>