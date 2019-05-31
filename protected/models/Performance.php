<?php
class Performance extends CActiveRecord{
	public $contact_name;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'performance';
	}
	public function rules(){
		return array(			
			array('id_user,year', 'numerical', 'integerOnly'=>true),
			array('id_user', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'productivity' => array(self::HAS_ONE, 'productivity', 'id_user'),			
		);
	}	
	public function searchAfterHours(){
		$criteria = new CDbCriteria;	$criteria->with = array('PrimaryContact', 'SecondaryContact','cType');	$criteria->together = true;
		$criteria->compare('t.primary_contact', $this->primary_contact);	$criteria->compare('t.secondary_contact', $this->secondary_contact);	$criteria->compare('t.type','402');
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),          
		));
	}	
	public function searchSunday(){
		$criteria = new CDbCriteria;	$criteria->with = array('PrimaryContact', 'SecondaryContact','cType');	$criteria->together = true;
		$criteria->compare('t.primary_contact', $this->primary_contact);	$criteria->compare('t.secondary_contact', $this->secondary_contact);	$criteria->compare('t.type','403');
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),          
		));
	}	

	public static function getAllProjectsAssignedtoUserProd($user_id){	
		if ($user_id){	
			$projectlist = Yii::app()->db->createCommand("SELECT distinct p.id ,p.name FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$user_id."' and p.status='1' order by p.name")->queryAll();
			$internalp= Yii::app()->db->createCommand("SELECT distinct p.id ,p.name FROM internal p , internal_tasks pt , user_internal uta WHERE uta.id_task=pt.id and pt.id_internal=p.id and uta.id_user='".$user_id."' and p.status='0' order by p.name")->queryAll();
			foreach ($projectlist as $id => $res){	$projects[$res['id']] = $res['name'];	}
			foreach ($internalp as $id => $res)	{	$projects[$res['id'].'i'] = $res['name'];	}			
			return $projects;  
		}		
	}	
	public static function getAllActiveUsers(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1')->queryAll();		
		$users = array();
		foreach ($result as $i => $res){	$users[$res['id']] = $res['firstname'].' '.$res['lastname'];}
		return $users;
	}	
	public static function getAllData(){
		$ct=Yii::app()->db->createCommand("SELECT id, id_project ,id_task ,id_phase , id_user as user, id_resc as QCed, 'q' as type, 0 as m, internal_project FROM quality UNION ALL SELECT id, id_project ,id_task ,id_phase ,id_user as user, '' as QCed,'p' as type, maintenancec as m, internal_project  FROM productivity  ORDER BY  user ")->queryAll();
		return $ct;
	}
	public static function getstatus($id, $type, $user, $phase, $task){
		if ($type == 'q'){	
			$ct=Yii::app()->db->createCommand("select CASE  WHEN score=0  THEN 'Pass' WHEN score=1  THEN 'Fail' ELSE '' END as score from quality where id='".$id."' ")->queryScalar();
			return $ct;
		}else{
			if($phase ==0){
				$actuals=ProjectsTasks::getTimeSpentperTask($task, $user);
			}else{
				$actuals=ProjectsPhases::getTimeSpentperPhase($phase, $user);  
			}
			if ($actuals == 0)
				return ' ';
			$ct=Yii::app()->db->createCommand("SELECT expected_mds FROM productivity where id='".$id."' ")->queryScalar();
			$pass= $ct - $actuals;
			if ($pass >= 0 )
				return 'Pass';
			else
				return 'Fail';
		}
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
	public static function getYears(){
		$start_year = date('Y',strtotime('now - 3 year'));
		$end_year = date('Y',strtotime('now '));
		for ($i=$end_year;$i>=$start_year;$i--){   	$years["{$i}"]="{$i}";	}		
		return $years;                   
	}
	public static function getPopupGrid($value){ 
		$arr = Utils::getShortText($value, 15);
		if ($value != null)
		return '<div class="first_it panel_container">'
				.'<div class="perf_second_item_clip perfclip">'.$arr['text'].'</div>'
				.'<u  style="text-decoration:none!important;" class="red">+</u>'
				.'<div class="panel" style = "left:110px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getnotes($str)
	{
		return chunk_split($str, 12, ' ');
	}
	public static function getPopupTaskGridnote($value){ 
		$arr = Utils::getShortText($value, 5);
		if ($value != null)
		return '<div class="first_it panel_container" style="margin-left:-10px;">'
				.'<div class="perf_first_item_clip perfclip">'.$arr['text'].'</div>'
				.'<u style="text-decoration:none!important;float:right;" class="red">+</u>'
				.'<div class="panel_expenses" style = "left:100px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getPopupTaskGridProd($value){ 
		$arr = Utils::getShortText($value, 15);
		if ($value != null)
		return '<div class="first_it panel_container" style="margin-left:-10px;">'
				.'<div class="perf_first_item_clip perfclip">'.$arr['text'].'</div>'
				.'<u style="text-decoration:none!important;float:right;" class="red">+</u>'
				.'<div class="panel_expenses" style = "left:100px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getPopupTaskGridProd2($value){ 
		$arr = Utils::getShortText($value, 15);
		if ($value != null)
		return '<div class="first_it panel_container" style="margin-left:-35px;">'
				.'<div class="perf_first_item_clip perfclip">'.$arr['text'].'</div>'
				.'<u style="text-decoration:none!important;" class="red">+</u>'
				.'<div class="panel_expenses" style = "left:100px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getPopupTaskGrid($value){ 
		$arr = Utils::getShortText($value, 26);
		if ($value != null)
		return '<div class="first_it panel_container">'
				.'<div class="perf_first_item_clip perfclip">'.$arr['text'].'</div>'
				.'<u style="text-decoration:none!important;" class="red">+</u>'
				.'<div class="panel_expenses" style = "left:160px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getPopupNoteGrid($value){ 
		$arr = Utils::getShortText($value, 20);
		if ($value != null)
		return '<div class="first_it panel_container">'
				.'<div class="perf_note_item_clip perfclip">'.$arr['text'].'</div>'
				.'<u style="text-decoration:none!important;" class="red">+</u>'
				.'<div class="panelNotes" style = "left:10px">'
					.'<div class="perfhead"></div>'
					.'<div class="perfcontent"><div class="perfcover">'.$value.'</div></div>'
					.'<div class="perfooter"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getUsersPerformanceHistory(){
			$list_users="";
				$results = Yii::app()->db->createCommand("select DISTINCT ut.id_user , up.line_manager from user_task ut , users u, projects_tasks pt , projects_phases pp , user_personal_details up , projects p
																																	where ut.id_task=pt.id 
																																	and ut.id_user=u.id
																																	and u.active='1'
																																	and pt.id_project_phase=pp.id 
																																	and pp.id_project=p.id
																																	and ut.id_user=up.id_user
																																	and  (up.performance <>'1'or up.performance is null)
																																	and p.`status`='1'
																																	order by up.line_manager asc")->queryAll();
				$prj="";
				foreach ($results as $top){	
					if($prj<>$top['line_manager']){
							$list_users .= "<br/><br/>";
							$list_users .= "<u>Line Manager:</u> <b>".Users::getNameById($top['line_manager'])."</b> <br/><br/>";
							$prj=$top['line_manager'];

					}
					$list_users .= "<b>".Users::getNameById($top['id_user'])."</b> - Productivity: <b>".Performance::getProductivitybyResc($top['id_user'])."</b> - Quality: <b>".Performance::getQualitybyResc($top['id_user'])."</b> <br/>";		
				}
		return $list_users;	
	}
	public static function getProductivitybyResc($id_user){
				return $results = Yii::app()->db->createCommand("select count(1) from productivity where id_user='".$id_user."' and YEAR(adddate)=YEAR(NOW()) ")->queryScalar();
	}
	public static function getQualitybyResc($id_user){
				return $results = Yii::app()->db->createCommand("select count(1) from quality where id_user='".$id_user."'  and YEAR(adddate)=YEAR(NOW()) ")->queryScalar();
	}
	public static function getQCUsersDeadline(){
		return $results = Yii::app()->db->createCommand("select distinct(id_resc) as id from quality where (date(expected_delivery_date) - INTERVAL 3 DAY)= CURDATE()")->queryAll();
	}
	public static function getQCtasksByUser($id){
		return $results = Yii::app()->db->createCommand("select * from quality where id_resc=".$id." and (date(expected_delivery_date) - INTERVAL 3 DAY)= CURDATE()")->queryAll();
	}	
	public static function getQCTasks(){
		return Yii::app()->db->createCommand("SELECT ut.id_user, ut.id_task, pt.id_project_phase, pp.id_project,p.ADDDATE FROM user_task ut, projects_tasks pt, projects_phases pp, projects p
			where  pt.id=ut.id_task and  not exists (select 1 from tasks ts where pt.description = ts.task) and (pt.description like '%FBR%' or pt.description like '%INT%') 
			and pp.id=pt.id_project_phase and p.id=pp.id_project and p.ADDDATE >= '2017-08-01' -INTERVAL 1 MONTH 
			and exists (select 1 from user_groups ug where ug.id_group in (9,13) and ut.id_user = ug.id_user) 	and pt.flag=0  and pp.description like '%Development%' and p.id_type=27
			and 0=(select count(1) from quality q where q.id_project =pp.id_project and q.id_phase=pt.id_project_phase and q.id_task =ut.id_task and q.id_user= ut.id_user)")->queryAll();
	}
	public static function getQCTasksFromProj(){
		return  Yii::app()->db->createCommand("select pt.* from projects_tasks pt, projects_phases pp where pt.adddate >= CURDATE() - INTERVAL 7 DAY and (pt.description like '%FBR%' or pt.description like  '%INT%' or pt.description like  '%CR%'  or pt.type=1 or pt.type=3) and pt.description not in (select task from tasks ) and pp.id= pt.id_project_phase order by pp.id_project")->queryAll();
	}
	public static function getNoTaskSWProj(){
		$sws=  Yii::app()->db->createCommand("select p.id as id from projects p where p.id_type=27 and p.qa='Yes' and p.status=1 and 0<(select count(1) from projects_phases pp where pp.id_project=p.id and pp.description like 'Development' )")->queryAll();
		$str='';
		foreach ($sws as $sw) {
			$gettasks=Yii::app()->db->createCommand("SELECT COUNT(1) FROM projects_tasks pt, projects_phases pp, projects p where p.id=".$sw['id']." and pp.id_project=p.id and pp.description like 'Development' AND pp.id= pt.id_project_phase and  pt.description not in (select task from tasks)")->queryScalar();
			if ($gettasks == 0){
				$str.='- '.Projects::getNameById($sw['id']).'<br/>';
			}			
		}
		return $str;
	}
}?>