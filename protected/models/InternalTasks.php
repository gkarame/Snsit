<?php
class InternalTasks extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'internal_tasks';
	}
	public function rules(){
		return array(
			array('priority, estimated_effort, description,status', 'required'),
			array('id_internal, status, priority', 'numerical', 'integerOnly'=>true),
			array('estimated_effort', 'numerical','integerOnly'=>false),
			array('eta', 'length', 'max'=>11),
			array('notes', 'length', 'max'=>5000),
			array('priority, description, status, close_date', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'users' => array(self::HAS_MANY, 'UserInternal', 'id_task')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_internal' => 'Project',
			'status' => 'Status',
			'priority' => 'Priority',
			'description' => 'Description',
			'adddate' => 'Creation Date',
			'eta' => 'ETD',
			'estimated_effort'=> 'EMDs',
			'close_date' =>'Closed Date',
			'notes' => 'Remarks'
		);
	}

	public function renderNumber()
	{
		echo '<a class="show_link" href="'.Yii::app()->createUrl("internal/task", array("id" => $this->id)).'">'.$this->description.'</a>';
	}

	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }

	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_internal',$this->id_internal);
		$criteria->compare('estimated_effort',$this->estimated_effort);	$criteria->compare('priority',$this->priority,true);
		$criteria->compare('status',$this->status,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getNameById($id){
		return Yii::app()->db->createCommand()
    		->select('description')
    		->from('internal_tasks')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
	}	
	

	public static function getTimeSpentperTask($id_task,$id_user) {
		$result =  Yii::app()->db->createCommand("select sum(amount)/8 from user_time where id_user='".$id_user."' and `default`='3' and id_task='".$id_task."' ")->queryScalar();
		return Utils::formatNumber($result);
	}
	public static function getTimeSpent($id_task) {
		$result =  Yii::app()->db->createCommand("select IFNULL(sum(amount),0)/8 from user_time where `default`='3' and id_task='".$id_task."' ")->queryScalar();
		return Utils::formatNumber($result);
	} 
	public static function getAsignUsers($id_task){
		return 	Yii::app()->db->createCommand('SELECT Distinct u.id, u.username, u.firstname, u.lastname FROM users u left join user_internal on user_internal.id_user=u.id where id_task in ('.$id_task.')')->queryAll();
	}
	public static function closedTaskslastweekorAll($id, $val=true){
		if($val)
		{
			return 	Yii::app()->db->createCommand("SELECT count(1) from internal_tasks where status=3 and id_internal=".$id." and date(close_date) >= (current_date()- interval 1 WEEK)")->queryScalar();
		}else{
			return 	Yii::app()->db->createCommand("SELECT count(1) from internal_tasks where status=3 and id_internal=".$id." ")->queryScalar();
		}		
	}
	public static function closedTaskslastTWOweekorAll($id, $val=true){
		if($val)
		{
			return 	Yii::app()->db->createCommand("SELECT count(1) from internal_tasks where status=3 and id_internal=".$id." and date(close_date) >= (current_date()- interval 2 WEEK)")->queryScalar();
		}else{
			return 	Yii::app()->db->createCommand("SELECT count(1) from internal_tasks where status=3 and id_internal=".$id." ")->queryScalar();
		}		
	}
	public static function getCountTasks($id){
		return 	Yii::app()->db->createCommand("SELECT count(1) from internal_tasks where id_internal=".$id." ")->queryScalar();	
	}
	public static function closedTaskslastweekorAllInfo($id, $val=true){
		if($val)
		{
			return 	Yii::app()->db->createCommand("SELECT * from internal_tasks where status=3 and id_internal=".$id." and date(close_date) >= (current_date()- interval 1 WEEK)")->queryAll();
		}else{
			return 	Yii::app()->db->createCommand("SELECT * from internal_tasks where status=3 and id_internal=".$id." ")->queryAll();
		}		
	}
	public static function closedTaskslastTWOweekorAllInfo($id, $val=true){
		if($val)
		{
			return 	Yii::app()->db->createCommand("SELECT * from internal_tasks where status=3 and id_internal=".$id." and date(close_date) >= (current_date()- interval 2 WEEK)")->queryAll();
		}else{
			return 	Yii::app()->db->createCommand("SELECT * from internal_tasks where status=3 and id_internal=".$id." ")->queryAll();
		}		
	}

	public static function getProjectByTask($id){
		$project = Yii::app()->db->createCommand("select id_internal from internal_tasks where id=".$id." ")->queryScalar();
    	return $project;
	}	
	public static function getPriority($code)
	{
		switch ($code) {
			case 0:
				return 'Low';
				break;
			case 1:
				return 'Medium';
				break;
			case 2:
				return 'High';
				break;
			default:
				return '';
				break;
		}
	}
	public static function getPriorityList(){		
		return array(			
			0 => 'Low',
			1 => 'Medium',
			2 => 'High'
		); 
	}
	public static function getTotalTasks($project){
		$result = Yii::app()->db->createCommand("Select count(1) from internal_tasks where id_internal= '$project' ")->queryScalar();
		return $result;	
	}
	public static function getStatusContor($project){
		$status = array();
		for ($i=0; $i<5; $i++)	{	$status[$i] = self::getTotalStatus($i, $project);	}
		return $status;
	}
	public static function getTotalStatus($value, $project){
		$result = Yii::app()->db->createCommand("Select count(1) from internal_tasks where status = '$value' and id_internal= '$project' ")->queryScalar();
		return $result;	
	}
	public static function getOpenTasks($project){
		$result = Yii::app()->db->createCommand("Select count(1) from internal_tasks where id_internal= '$project' and status in (0,1,2,4) ")->queryScalar();
		return $result;	
	}
	public static function getStatusList(){		
		return array(			
			0 => 'New',
			1 => 'In Progress',
			2 => 'In Testing',
			4 => 'On Hold',
			3 => 'Closed'
		); 
	}
	public static function getStatus($status)
	{
		switch ($status) {
			case 0:
				return 'New';
				break;
			case 1:
				return 'In Progress';
				break;
			case 2:
				return 'In Testing';
				break;
			case 3:
				return 'Closed';
				break;
			case 4:
				return 'On Hold';
				break;
			default:
				return '';
				break;
		}
	}

	public static function getnotes($str)
	{
		return chunk_split($str, 12, ' ');
	}
	public static function getRecipPerId($id){
		return 	Yii::app()->db->createCommand('SELECT recipients FROM internal  where id='.$id.'')->queryScalar();
	}
	public static function getRecip($id){
		$current= InternalTasks::getRecipPerId($id);
		if(!empty($current)){
			return 	Yii::app()->db->createCommand('SELECT u.id, u.username, u.firstname, u.lastname FROM users u where active=1 and id not in ('.$current.') order by u.firstname')->queryAll();
		}else{
			return 	Yii::app()->db->createCommand('SELECT u.id, u.username, u.firstname, u.lastname FROM users u where active=1 order by u.firstname')->queryAll();
		}			
	}
	public function getAll($group = NULL){
		$criteria = new CDbCriteria;
		$criteria->select = array(
				"t.*"	
		);
		$dataProvider = new CActiveDataProvider('InternalTasks', array(
				'criteria' => $criteria,
				'pagination'=>array(
					'pageSize'=>10000, // or another reasonable high value...
				),
				'sort'=>array( 
               		
               		'defaultOrder' => 'description ASC',
           		 ),
		));
		return $dataProvider;
	}
	public static function getAllUsers(){
		return 	Yii::app()->db->createCommand('SELECT u.id, u.username, u.firstname, u.lastname FROM users u where active=1 order by u.firstname')->queryAll();
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
							.'<div class="item_clip" style="float:left;" onmouseover="showToolTip(this);" onmouseout="hideToolTip(this);">'.$short_sir['text'].'</div>'
							.'<u class="red block" onmouseover="showToolTip(this);" onmouseout="hideToolTip(this);">+</u>'
							.'<div class="panel" style="left:80px;">'
								.'<div class="phead"></div>'
								.'<div class="pcontent"><div class="cover">'.substr(trim($sir),0,-1).'</div></div>'
								.'<div class="pftr"></div>'
							.'</div>'
						.'</div>';
		}
		return "";
	}

	public static function getAllUsersTask($id){
		$result =  Yii::app()->db->createCommand('SELECT id_user from user_internal where id_task='.$id)->queryAll();	$name = "";
		foreach($result as $res){
			$result_name =  Yii::app()->db->createCommand('SELECT firstname,lastname from users where id='.$res['id_user'])->queryAll();
				foreach($result_name as $rname){	$fname =  strtoupper(substr($rname['lastname'], 0, 1));	$name.=ucwords($rname['firstname']).'.'.$fname.' | '; }
		}
		if($name != '')
		{
			$name= substr($name, 0, (strlen($name)-2));
		}
		return $name;
	}

}?>