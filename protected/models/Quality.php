<?php
class Quality extends CActiveRecord{
	public $contact_name; public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'quality';
	}
	public function rules(){
		return array(
			array('id_project,id_user, id_task, status, type', 'required'),
			array('id_project, id_resc ,id_task,status , id_user ,id_phase,status,complexity,updated,type', 'numerical', 'integerOnly'=>true),
			array('score ', 'numerical', 'integerOnly'=>false), 
			array('id_project,id_task,id_resc, id_user ,status,complexity,type,score, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'cType'=> array(self::BELONGS_TO, 'Codelkups', 'type'),				
			'PrimaryContact' => array(self::BELONGS_TO, 'Users', 'primary_contact'),
			'SecondaryContact' => array(self::BELONGS_TO, 'Users', 'secondary_contact'),
		);
	}
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
    public function attributeLabels()	{
		return array(
			'notes' => 'Notes',
			'id_project' => 'Project',
			'id_task' => 'Task/FBR',
			'id_user' => 'Resource',
			'complexity' => 'Complexity',
			'type' => 'Type',
			'id_phase' => 'Phase',
			'status' => 'Status',
			'id_resc' => 'QA Resource',
			'score' => 'Score',
			'expected_delivery_date' => 'QA ETA',
			'fbr_delivery' => 'FBR ETA'
		);
	}	
	public function getAll($group = NULL){
		$criteria = new CDbCriteria;
		$criteria->select = array(
				"t.*"	
		);
		$criteria->mergeWith(array(
		    'join'=>'LEFT JOIN projects_phases ON projects_phases.id= t.id_phase',
		    'condition'=>'t.id_phase !=0',
		));
		$criteria->mergeWith(array(
		    'join'=>'LEFT outer JOIN projects_tasks ON projects_tasks.id= t.id_task LEFT JOIN projects_phases p2 ON p2.id= projects_tasks.id_project_phase',
		    'condition'=>' t.id_phase = 0 AND t.id_task != 0',
		));
		$criteria->condition = "projects_phases.description like '%Development%' or p2.description like '%Development%'";
		$criteria->compare('year(t.adddate)','>=2017',true);
		$dataProvider = new CActiveDataProvider('Quality', array(
				'criteria' => $criteria,
				'pagination'=>array(
					'pageSize'=>10000, // or another reasonable high value...
				),
				'sort'=>array( 
               		
               		'defaultOrder' => 'adddate ASC',
           		 ),
		));
		return $dataProvider;
	}
	public function search(){
		$criteria = new CDbCriteria;
 		$criteria->mergeWith(array(
		    'join'=>'LEFT JOIN projects_phases ON projects_phases.id= t.id_phase',
		    'condition'=>'t.id_phase !=0',
		));
		$criteria->mergeWith(array(
		    'join'=>'LEFT outer JOIN projects_tasks ON projects_tasks.id= t.id_task LEFT JOIN projects_phases p2 ON p2.id= projects_tasks.id_project_phase',
		    'condition'=>' t.id_phase = 0 AND t.id_task != 0',
		));
		$criteria->condition = "projects_phases.description like '%Development%' or p2.description like '%Development%'";
		$criteria->compare('t.id_project',Projects::getIdByName($this->id_project),true);	$criteria->compare('t.id_task',$this->id_task);
		$criteria->compare('t.id_resc',$this->id_resc); $criteria->compare('t.id_user',$this->id_user); 
		if (isset($this->status) && !empty($this->status) && strpos($this->status, ',') ) {
			 $criteria->addCondition('t.status in ('.$this->status.')');
		}else{
			$criteria->compare('t.status',$this->status);
		}
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.complexity',$this->complexity); $criteria->compare('t.score',$this->score);	$criteria->compare('year(t.adddate)','>=2017',true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),          
		));
	}
	public static function getPopupGrid($value){ 
		$arr = Utils::getShortText($value, 5);
		if ($value != null)
		return '<div class="first_it panel_container">'
				.'<div class="perf_second_item_clip perfclip" style="width:20px !important;">'.$arr['text'].'</div>'
				.'<u  style="text-decoration:none!important;" class="red">+</u>'
				.'<div class="panel" style = "left:110px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getProjectName($project, $internal){  		
  		if($internal>0){
			return Internal::getNameById($project);
		}else{
			return Projects::getNameById($project);
		}
  	}
  	public static function getPMOps($project, $internal)  
  	{
  		if($internal>0){
			return Internal::getPmOpsAssigned($project);
		}else{
  			return Projects::getPmOpsAssigned($project);
  		}
  	}
  	public static function getTaskName($task, $phase, $internal){  		
  		if($internal>0){
			return Internal::getNameById($project);
		}else{
			return Projects::getNameById($project);
		}
  	}
	public static function formatNotes($str){  		
  		$lines = explode("<br />", $str); 		$newstr='';
  		foreach ($lines as $key => $line) {		$newstr= $newstr.''.$line;		}
		return $newstr;
  	}
	public static function datedispQA($dateqa, $id){
		if(GroupPermissions::checkPermissions('general-quality','write')){
			if(!empty($dateqa)  && $dateqa!= '1970-01-01'){
				$str='<input type="date" required name="expected_delivery_date" value="'.$dateqa.'" onchange="changeInput(event,'.$id.',6);" style="font-size: 0.73rem;width:107px;">';
			}else{
				$str='<input type="date" required name="expected_delivery_date" value="" onchange="changeInput(event,'.$id.',6);" style="font-size: 0.73rem;width:107px;"> ';
			}
		}else{	$str=$dateqa;	}
		return $str;
	}
	public static function datedispFBR($dateFbr, $id){
		if(GroupPermissions::checkPermissions('general-quality','write')){
			if(!empty($dateFbr) && $dateFbr!= '1970-01-01'){
				$str='<input type="date" name="fbr_delivery" required value="'.$dateFbr.'" onchange="changeInput(event,'.$id.',5);" style="font-size: 0.73rem;width:107px;">';
			}else{
				$str='<input type="date" name="fbr_delivery"  required onchange="changeInput(event,'.$id.',5);" style="font-size: 0.73rem;width:107px;"> ';
			}
		}else{	$str=$dateFbr;	}
		return $str;
	}
	public static function getQAUsers($id, $user){
		$result =  Yii::app()->db->createCommand("SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE (active = 1  and users.id in (select id_user from user_groups where id_group in (9,12,13,19,18) and id_user <>20)) or   users.id=".$user." order by users.firstname, users.lastname")->queryAll();
		$users = array();	$users[''] = ' ';
		foreach ($result as $i => $res){	$users[$res['id']] = $res['firstname'].'  '.$res['lastname']; }
		if(GroupPermissions::checkPermissions('general-quality','write')){
			return CHtml::dropDownlist('id_resc', $user, $users, array(
		        'class'     => 'id_resc',
		    	'onchange'=>'changeInput('."value".','. $id.','."4".')',
		    	'style'=>'width:60px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('id_resc', $user, $users, array(
		        'class'     => 'id_resc',
		    	'disabled'=>true,	
		    	'style'=>'width:60px;border:none;'
		    ));
	    }
	}	
	public static function getDateFormat($datestr){
		$date = strtr($datestr, '-', '/');		$expec_date= date('d/m/Y', strtotime($date));	return $expec_date;
	} 
	public static function getTaskDesc($task, $phase, $internal){
		if($internal>0)
		{
			$str=  InternalTasks::getNameById($task);
		}else{			
			$str=ProjectsTasks::getTaskDescByid($task); 
			if (empty($str)){	$str=ProjectsPhases::getPhaseDescByPhaseId($phase);	}
		}
		return $str;
	}
	public static function GetAllProjectsDevTasks($project=null){
		$arr=array();
		if ($project){
			if($project > 554){
				$tasks =Yii::app()->db->createCommand("SELECT pt.id, pt.description FROM projects_tasks pt, projects_phases pp, projects p
				where  pp.id=pt.id_project_phase and p.id=pp.id_project and ((pp.phase_number = 6 and p.id_type=27) or (pp.phase_number = 1 and p.id_type=28))
				 and p.id=".$project."")->queryAll();
			}else{
				$tasks =Yii::app()->db->createCommand("SELECT pt.id, pt.description FROM projects_tasks pt, projects_phases pp, projects p
				where  pp.id=pt.id_project_phase and p.id=pp.id_project and ((pp.phase_number = 5 and p.id_type=27) or (pp.phase_number = 1 and p.id_type=28))
				 and p.id=".$project."")->queryAll();
			}	
			foreach ($tasks as $task) {		$arr[$task['id']]= $task['description']; }			
			return $arr;  
		}else{
			$tasks =Yii::app()->db->createCommand("SELECT pt.id, pt.description FROM projects_tasks pt, projects_phases pp, projects p
			where  pp.id=pt.id_project_phase and p.id=pp.id_project and pp.description like '%Development%' and p.id_type in (27,28)")->queryAll();	
			foreach ($tasks as $task) {	$arr[$task['id']]= $task['description'];	}			
			return $arr; 
		}	
	}
	public static function getDescriptionGrid($users){
		$arr = Utils::getShortText($users, 15);
		if ($users != null)
		return '<div class="first_it panel_container" style ="width: 50px !important;">'
				.'<div class="item_clip clip"  style ="width: 50px !important;">'.$arr['text'].'</div>'
				.'<u class="red">+</u>'
				.'<div class="panel" style = "left:0px">'
					.'<div class="phead"></div>'
					.'<div class="pcontent"><div class="cover">'.$users.'</div></div>'
					.'<div class="pftr"></div>'
				.'</div>'
			.'</div>';
	}
	public static function GetCustomProjectsDevTasks($project=null){
		$arr=array();
		if ($project){
			$tasks =Yii::app()->db->createCommand("SELECT pt.id, pt.description FROM projects_tasks pt, projects_phases pp, projects p
				where  pp.id=pt.id_project_phase and p.id=pp.id_project and  pp.description like '%Development%'  and p.id_type in (27,28)
				 and p.id=".$project." and pt.description not in (SELECT task FROM tasks)")->queryAll();	
			foreach ($tasks as $task) {		$arr[$task['id']]= $task['description']; }			
			return $arr;  
		}else{
			$tasks =Yii::app()->db->createCommand("SELECT pt.id, pt.description FROM projects_tasks pt, projects_phases pp, projects p
			where  pp.id=pt.id_project_phase and p.id=pp.id_project and pp.description like '%Development%' and p.id_type in (27,28) and pt.description not in (SELECT task FROM tasks)")->queryAll();	
			foreach ($tasks as $task) {	$arr[$task['id']]= $task['description'];	}			
			return $arr; 
		}	
	}
	public static function getScoresLabel($value){
		if(!empty($value) || $value=='0')
		{
			$list = self::getAllScores();
			return $list[$value];
		}else{
			return '';
		}
	}
	public static function getAllScores(){	return array('0'=>'Pass','1'=>'Fail');	}
	public static function gettaskscore($id, $score){ 	
		$result= array(null=>'','0'=>'Pass','1'=>'Fail');		
		if(GroupPermissions::checkPermissions('general-quality','write')){
			return CHtml::dropDownlist('score', $score, $result, array(
		        'class'     => 'score',
		    	'onchange'=>'changeInput('."value".','. $id.',3)',
		    	'style'=>'width:52px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('score', $score, $result, array(
		        'class'     => 'score',
		    	'disabled'=>true,	
		    	'style'=>'width:52px;border:none;'
		    ));
	    }
	}
	public static function getStatusLabel($value){
		if(!empty($value) || $value=='0')
		{
			$list = self::getAllStatus();
			return $list[$value];
		}else{
			return '';
		}
	}
	public static function getTypeLabel($value){
		$list = self::getAlltype();
			return $list[$value];
	}
	public static function getAlltype(){
		return array('1'=>'Tech','2'=>'Ops');		
	}
	public static function getAllStatus(){
		return array('0'=>'Pending','1'=>'Assigned','2'=>'On Hold','3'=>'Second Review','4'=>'Cancelled ','5'=>'Completed');		
	}
	public static function gettaskstatus($id, $status){ 
		$result= array('0'=>'Pending','1'=>'Assigned','2'=>'On Hold','3'=>'Second Review','4'=>'Cancelled ','5'=>'Completed');		
		if(GroupPermissions::checkPermissions('general-quality','write')){
			return CHtml::dropDownlist('status', $status, $result, array(
		        'class'     => 'status',
		    	'onchange'=>'changeInput('."value".','. $id.',1)',
		    	'style'=>'width:60px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('status', $status, $result, array(
		        'class'     => 'status',
		    	'disabled'=>true,	
		    	'style'=>'width:60px;border:none;'
		    ));
	    }
	}	
	public static function getComplexLabel($value){
		if(!empty($value) || $value=='0')
		{
			$list = self::getAllComplx();
			return $list[$value];
		}else{
			return '';
		}
	}
	public static function getAllComplx(){	return array('0'=>'Easy','1'=>'Medium','2'=>'Hard'); }
	public static function gettaskcomplexity($id,$complexity){ 	
		$result= array('0'=>'Easy','1'=>'Medium','2'=>'Hard');		
		if(GroupPermissions::checkPermissions('general-quality','write')){
			return CHtml::dropDownlist('complexity', $complexity, $result, array(
		        'class'     => 'complexity',
		    	'onchange'=>'changeInput('."value".','. $id.',2)',
		    	'style'=>'width:60px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('complexity', $complexity, $result, array(
		        'class'     => 'complexity',
		    	'disabled'=>true,	
		    	'style'=>'width:60px;border:none;'
		    ));
	    }
	}
	public static function getAllActiveUsers(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1')->queryAll();		
		$users = array();
		foreach ($result as $i => $res)	{	$users[$res['id']] = $res['firstname'].' '.$res['lastname']; }
		return $users;
	}	
	public static function getColumnsForGrid(){		
		$columns= array(	
					array(
						'name' => 'id',
						'header' => 'ID',
						'value' => '$data->id',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column70'), 
						'headerHtmlOptions' => array('class' => 'column70'),
					),					
					array(
						'name' => 'from_date',
						'header' => 'From Date',
						'value' => '$data->from_date',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),					
					array(
						'name' => 'to_date',
						'header' => 'To Date',
						'value' => '$data->to_date',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),
					array(
						'name' => 'PrimaryContact.firstname'.'PrimaryContact.lastname',
						'header' => 'Primary Contact',
						'value' => 'isset($data->PrimaryContact) ? $data->PrimaryContact->firstname." ".$data->PrimaryContact->lastname : ""',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),					
				array(
						'name' => 'SecondaryContact.firstname'.'SecondaryContact.lastname',
						'header' => 'Secondary Contact',
						'value' => 'isset($data->SecondaryContact) ? $data->SecondaryContact->firstname." ".$data->SecondaryContact->lastname : ""',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),
				);		
		$columns[] = array(
				'class'=>'CCustomButtonColumn',
				'template'=>'{update} {delete}',
				'htmlOptions'=>array('class' => 'button-column'),
				'afterDelete'=>'function(link,success,data){ 
										if (success) {
											var response = jQuery.parseJSON(data); 
											// update amounts
						  					$.each(response.amounts, function(i, item) {
						  		    			$("#"+i).html(item);
						  					});
						  					$.fn.yiiGridView.update("terms-grid");
						  				}}',
				'buttons'=>array
	            (
	            	'update' => array(
						'label' => Yii::t('translations', 'Edit'), 
						'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("eas/manageItem", array("id"=>$data->id))',
	            		'options' => array(
	            			'onclick' => 'showItemForm(this);return false;'
	            		),
					),
					'delete' => array(
						'label' => Yii::t('translations', 'Delete'),
						'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("eas/deleteItem", array("id"=>$data->id))',  
	                	'options' => array(
	                		'class' => 'delete',
						),
					),
	            ),
			); 
		return $columns;
	}	
	public static function getQCTasksDoneCurrentMonth(){
		return  Yii::app()->db->createCommand("select * from quality where score in (0,1) and MONTH(score_date)= MONTH(CURRENT_DATE()) and YEAR(score_date)= YEAR(CURRENT_DATE()) and status =5")->queryAll();
	}
	public function getPrimaryContactGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->primary_contact.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->primary_contact.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}	
	public function getSecondaryContactGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->secondary_contact.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->secondary_contact.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}	
	public function getFromDateGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->from_date.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->from_date.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}	
	public function getToDateGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->to_date.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->to_date.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}	
} ?>