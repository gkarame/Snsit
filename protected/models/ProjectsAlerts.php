<?php
class ProjectsAlerts extends CActiveRecord{
		public $dataprovider;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_alerts';
	}
	public function rules(){
		return array(
			array('id_project, alerts', 'required'),
		);
	}
	public function relations(){
		return array(
			'project' => array(self::BELONGS_TO, 'Projects', 'id_project')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Project',
			'alerts' => 'Alerts',
		);
	}
	public static function getAll(){
		$alerts =   Yii::app()->db->createCommand()
			->select('*')
			->from('projects_alerts')
			->queryAll();
		return $alerts;
	}
	public static function getAlertsCount($projectId, $alerts = false , $type='alert'){ 		
		$project_status=(int)Yii::app()->db->createCommand("SELECT status from projects where id=".$projectId." ")->queryScalar();
		if($project_status!=2){
		if ($alerts === false){
			$alerts =   Yii::app()->db->createCommand()
				->select('alerts')
    			->from('projects_alerts')
    			->where('id_project = :id', array(':id'=>$projectId))
    			->queryScalar();    		
		}
			if($alerts !=false){
			$alerts_array = explode(',', $alerts);	$alertss = array();	$warnings = array();
			foreach ($alerts_array as $value) {
				$arr = explode("#", $value, 2);	$first = $arr[0];
					if(self::checktype($first)=='1'){
					array_push($alertss, $value)  ;
				}else{
					array_push($warnings, $value)  ;
				} 
			}
			if($type=='alert'){	return count($alertss);	}else{	return count($warnings); }	
		}else{
				return 0;
			}
		}else{
			return 0;
		}		 
	}
	public static function build_sorter($key, $key2 = null) {
	    return function ($a, $b) use ($key, $key2) {
	    	$result = strnatcmp($a[$key], $b[$key]);
	    	if ($key2 != null && $result == 0)	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
	public static function getProjectAlerts($projectId , $type='alerts'){
		$alerts =   Yii::app()->db->createCommand()
			->select('alerts')
			->from('projects_alerts')
			->where('alerts<>" " AND id_project = :id', array(':id'=>$projectId))
			->queryScalar();
		$alertsData = array();	$alertss = array();		$warnings = array();
		if (!empty($alerts)){
			$values = array();
			if (strpos($alerts, '#') !== false)	{
				$alerIds = array();	$a = explode(',', $alerts);
				foreach ($a as $alert){
					if(strpos($alert,'#') !== false){
						$params = explode('#', $alert);	$alerIds[] = reset($params);	$values[$params[0]] = $params[1];
					}else{
						$alerIds[] = $alert;
					}
				}
				$ids = implode(',', $alerIds);
			}else{
				$ids = $alerts; 
			}
			if( $type=='alerts' ){
				$alertsData = Yii::app()->db->createCommand("SELECT * from alerts WHERE `id` IN (". $ids .") and user_control='1'   ORDER BY  case id  when 39 then 100  when 6 then 1  when 17 then 1  when 7 then 0  when 18 then 0  when 41 then 100   else id  end, severity ASC")->queryAll();
			}else{
				$alertsData = Yii::app()->db->createCommand("SELECT * from alerts WHERE `id` IN (". $ids .") and user_control='0' ORDER BY  case id  when 39 then 100  when 6 then 1  when 17 then 1  when 7 then 0  when 18 then 0  when 41 then 100   else id  end, severity ASC")->queryAll();
			}
			foreach ($alertsData as &$data){
				if (isset($values[$data['id']])){
						$data['description'] = 	str_replace('{percent}', $values[$data['id']], $data['description']);
						if ($values[$data['id']] > '100'){
							$data['severity'] = 1;
						}else{
							switch ($values[$data['id']]){
								case '0';
								case '25';
								case '50':
								case '75':
									$data['severity'] = 2;
									break;
								case '90':
								case '100':
									$data['severity'] = 1;
									break;
							}
						}					
				}
			}
			}
			//uasort($alertsData, ProjectsAlerts::build_sorter('severity'));
			return $alertsData;		
	}	
	public static function parseAlerts($id_project, $alerts){
		foreach ($alerts as $al){
			if ($al['id_project'] == $id_project)
				return $al['alerts'];
		}
		return false;
	}
	public static function test1($project){
		$alertId = 1;	$check= ProjectsMilestones::CheckApplicable($project['id'], 7);
		if ($project['id'] > 554){
			if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 6) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 6) != 'Closed'){	return $alertId; }
		}else if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 5) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 5) != 'Closed'){	return 9; }		
		return '';
	}
	public static function test2($project){
		/* $alertId = 2;	$check= ProjectsMilestones::CheckApplicable($project['id'], 7);
		if ($project['id'] > 550){
			if ($check == 'Yes' && Timesheets::checkFirstPhaseTimeEnteredByPeriodTech($project['id'], 6) && 
			!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 18) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0)){
				return $alertId;
			}
		}else{
			if ($check == 'Yes' && Timesheets::checkFirstPhaseTimeEnteredByPeriodTech($project['id'], 5) && 
			!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 18) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0)){
				return $alertId;
			}
		}		*/
		return '';
	}
	public static function test3($project){
		/* $alertId = 3;	$check= ProjectsMilestones::CheckApplicable($project['id'], 7);
		if ($project['id'] > 550){
			if ($check == 'Yes' && (ProjectsMilestones::getStatusByMilestoneNUmber($project['id'],7) != 'Closed') && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 6) &&
			Documents::checkLastModifiedByPeriod($project['id'], 'projects', 18) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0)){
				return $alertId;
			}
		}else{
			if ($check == 'Yes' && (ProjectsMilestones::getStatusByMilestoneNUmber($project['id'],7) != 'Closed') && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 5) &&
			Documents::checkLastModifiedByPeriod($project['id'], 'projects', 18) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0)){
				return $alertId;
			}
		}		*/
		return '';
	}
	public static function test4($project){
		$alertId = 4;
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && Timesheets::checkProjectHasTimeByPeriod($project['id'], '+ 4 week') &&
				!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 17) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
			return $alertId;
		return '';
	}
	public static function test5($project){
		$alertId = 5;
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && Timesheets::checkProjectHasTimeByPeriod($project['id']) &&
				Documents::checkLastModifiedByPeriod($project['id'], 'projects', 17) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
		{	
			$date=Documents::checkIfAnyDocByCategory($project['id'], 'projects', 17);
			return $alertId.'#'.date("F jS Y", strtotime($date));
		}
		return '';
	}
	public static function test6($project, $alertId = 6){
		$alert_val = Yii::app()->db->createCommand('SELECT `values` FROM alerts WHERE id = 6')->queryScalar();
		$values = explode(',', $alert_val);	rsort($values);	$eas = Projects::getSEas($project['id']);
		$budget = Projects::getSumBudgetUSD($eas); 	
		if( $budget == null || $budget==0 ){	return '';	}
		foreach ($values as $value){
		 	 if (is_numeric($budget) && Projects::getExpensesAmountUSD($project['id']) > $budget){
				$value= round((Projects::getExpensesAmountUSD($project['id'])*100)/$budget,2);
				return $alertId.'#'.$value;
			}else{
				if (is_numeric($budget) && Projects::getExpensesAmountUSD($project['id']) >= ($value/100*$budget)){
					return $alertId.'#'.$value;
				}
			}
		}
		return '';
	}
	public static function test7($project, $alertId = 7){
		if(Projects::checktandm($project['id']) == 'Yes')
		{
			return '';
		}
		$alert_val = Yii::app()->db->createCommand('SELECT `values` FROM alerts WHERE id = 7')->queryScalar();
		$alert_val = Yii::app()->db->createCommand('SELECT `values` FROM alerts WHERE id = 7')->queryScalar();
		$values = explode(',', $alert_val);		rsort($values);
		$eas = Projects::getSEas($project['id']);		$actualMD = Projects::getProjectActualManDays($project['id'], $eas);
		if ($actualMD > 0){
			foreach ($values as $value){	
					if(Projects::getProjectTotalManDays($eas)==0 || Projects::getProjectTotalManDays($eas) == null )
						{ $totalmandays=1;	} else { $totalmandays=  Projects::getProjectTotalManDays($eas) ; }					
					$value= round(($actualMD*100)/$totalmandays,2);		return $alertId.'#'.$value;
				}
		}
		return '';
	}	
	public static function test8($project, $alertId = 8){
		$alertCheck = Yii::app()->db->createCommand("SELECT count(1) FROM projects_milestones 
				WHERE (((status = 'Pending' or status='In Progress' ) AND DATEDIFF(CURDATE(), last_updated) < 28 ) OR status ='closed' ) AND id_project = {$project['id']}   and applicable='Yes' ")->queryScalar();
		if ($alertCheck > 0){
			return '';
		}else{
			$alertCheck2 = Yii::app()->db->createCommand("SELECT count(1) FROM projects_milestones where  id_project = {$project['id']} AND applicable='Yes' ")->queryScalar();
			if ($alertCheck2 > 0){	return $alertId; }else{	return '';	}				
		}
	}
	public static function test9($project){	return ''; //used in another alert(1) 
	}
	public static function test10($project){	$alertId = 10;	return ''; }
	public static function test11($project){	$alertId = 11;	return ''; }
	public static function test12($project){	$alertId = 12;	return ''; }
	public static function test13($project){
		$alertId = 13;	$check= ProjectsMilestones::CheckApplicable($project['id'], 12);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5)
		{
			if ($project['id'] > 550){
			if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 10) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 11) != 'Closed'){
				return $alertId;
			}
			}else{
				if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 7) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 11) != 'Closed'){
					return $alertId;
				}
			}		
		}
		return '';
	}
	public static function test14($project){
		$alertId = 14;	$milestone = ProjectsMilestones::getMilestoneByNumber($project['id'], 12);
		$tfirst = strtotime($milestone['last_updated'].' + 4 week');	$now = strtotime("now");
		$check= ProjectsMilestones::CheckApplicable($project['id'], 12);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && $check == 'Yes' && $tfirst <= $now	&& $milestone['status'] == 'Closed'  && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 13) == 'Pending'){
			return $alertId;
		}
		return '';
	}
	public static function test15($project){
		$alertId = 15;
		if (Timesheets::checkProjectHasTimeByPeriod($project['id'], ' + 2 week') &&
			!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 17) &&	!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 24) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
			return $alertId;
		return '';
	}
	public static function test16($project){
		$alertId = 16;		
		if (Timesheets::checkProjectHasTimeByPeriod($project['id']) &&
				(Documents::checkLastModifiedByPeriod($project['id'], 'projects', 17, '+ 3 week') && Documents::checkLastModifiedByPeriod($project['id'], 'projects', 24, '+ 3 week')) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
			{
				$date=Documents::checkIfAnyDocByCategory($project['id'], 'projects', 24);
				if(empty($date))
				{
					$date=Documents::checkIfAnyDocByCategory($project['id'], 'projects', 17);					
				}
				return $alertId.'#'.date("F jS Y", strtotime($date));
			}
		return '';
	}	
	public static function test17($project){	return ProjectsAlerts::test6($project, 17); }	
	public static function test18($project){	
		if(Projects::checktandm($project['id']) == 'Yes'){
			return ProjectsAlerts::test29($project, 29);	
		}else{
			return ProjectsAlerts::test7($project, 18);	
		}
	}
	public static function test19($project) {	return ProjectsAlerts::test8($project, 19);	}
	public static function test20($project){
		$alertId = 20;	$check= ProjectsMilestones::CheckApplicable($project['id'], 10);
		if ($check == 'Yes' && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 10) == 'Closed' && 
		!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 23)){
			return $alertId;
		}			
		return '';
	}
	public static function test21($project){
		$alertId = 21;	$milestone = ProjectsMilestones::getMilestoneByNumber($project['id'], 5);
		$tfirst = strtotime($milestone['last_updated'].' + 4 week');	$now = strtotime("now");
		$check= ProjectsMilestones::CheckApplicable($project['id'], 5);
		if ($check == 'Yes' && $tfirst <= $now	&& $milestone['status'] == 'Closed' && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 6) == 'Pending'){
			return $alertId;
		}
		return '';
	}
	public static function test22($project){
		$alertId=22;	$lastlogged=ProjectsAlerts::getlastloggedhours($project['id']);
		if($lastlogged){
			return $alertId;
		}
		return '';
	}
	public static function test23($project){
		$alertId=23;	$customer_id = Projects::getId('customer_id', $project['id']);
		$connections = Yii::app()->db->createCommand("SELECT COUNT(*) FROM connections WHERE id_customer={$customer_id}")->queryScalar();
		$check= ProjectsMilestones::CheckApplicable($project['id'], 10);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5)
		{
			if ($project['id'] > 550){
				if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 9) && !$connections && (ProjectsMilestones::checkPhasesStatus($project['id'])>0)){return $alertId;}
			}else{
				if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 6) && !$connections && (ProjectsMilestones::checkPhasesStatus($project['id'])>0)){return $alertId;}
			}	
		}	
		return '';
	} 
  	public static function test25($project){ $alertId = 25;	return ''; }
	public static function test26($project){
		$alertId = 26;	
		return '';
	}	
	public static function test27($project){
		$alertId = 27;
		if (ProjectsMilestones::checkPhasesStatusdate($project['id'])>0)
			return $alertId;
		return '';
	}
	public static function test28($project){
		$alertId = 28;
		if (ProjectsMilestones::checkPhasesStatusdate($project['id'])>0)
			return $alertId;
		return '';
	}
	public static function test39($project){
		$alertId = 39;	$value=ProjectsMilestones::checkPhaseChecklistdate($project['id']);	$checkphase= ProjectsMilestones::checkProjectMilestone($project['id']);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && $value>0 && $checkphase){
			return $alertId.'#'.$value;
		}else{	
			return '';
		}
	}
	 public static function test30($project){	$alertId = 30;	return ''; }
	public static function test31($project){
		$alertId = 31;	$check= ProjectsMilestones::CheckApplicable($project['id'], 8);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && $check == 'Yes' && $project['id'] > 550 && Timesheets::checkPhaseStartedByPhasenumber($project['id'],9) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 9) != 'Closed'){
			return $alertId;
		}else{
			return '';
		}
	}
	public static function test32($project){
		$alertId = 32;	$check= ProjectsMilestones::CheckApplicable($project['id'], 9);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && $check == 'Yes' && $project['id'] > 550 && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 9) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 8) != 'Closed'){
	 		return $alertId;
		}else{
			return '';
		}
	}	
	public static function test33($project){
		$alertId = 33;
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && Timesheets::checkProjectHasTimeByPeriod($project['id'], '+ 4 week') &&
				!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 28) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
			return $alertId;
		return '';
	}
	public static function test34($project){
		$alertId = 34;
		if (Timesheets::checkProjectHasTimeByPeriod($project['id'], '+ 4 week') &&
				!Documents::checkIfAnyDocByCategory($project['id'], 'projects', 27) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
			return $alertId;
		return '';
	}
	public static function test35($project){
		$alertId = 35;
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && Timesheets::checkProjectHasTimeByPeriod($project['id']) &&
				Documents::checkLastModifiedByPeriod($project['id'], 'projects', 28,"+ 4 week") && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
		{
			$date=Documents::checkIfAnyDocByCategory($project['id'], 'projects', 28);
			return $alertId.'#'.date("F jS Y", strtotime($date));
		}
		return '';
	}
	public static function test36($project){
		$alertId = 36;
		if (Timesheets::checkProjectHasTimeByPeriod($project['id']) &&
				Documents::checkLastModifiedByPeriod($project['id'], 'projects', 27,"+ 4 week") && (ProjectsMilestones::checkPhasesStatus($project['id'])>0))
		{
			$date=Documents::checkIfAnyDocByCategory($project['id'], 'projects', 27);
			return $alertId.'#'.date("F jS Y", strtotime($date));
		}
		return '';
	}
	public static function test37($project, $alertId = 37){
		$value = ProjectsIssues::checkOpenIssues($project['id']);
		if($value==0 ){
			return '';
		}else{
			return $alertId.'#'.$value;
		}
	}
	public static function test38($project, $alertId = 38){
		$check= ProjectsMilestones::CheckApplicable($project['id'], 7);
		$template= Projects::getTemplatePerProject($project['id']);
		if ($template!= 5 && $template!= 4 && $project['id'] > 554){
			if ($check == 'Yes' && Timesheets::checkPhaseStartedByPhasenumber($project['id'], 6) && (ProjectsMilestones::checkPhasesStatus($project['id'])>0) && ProjectsMilestones::getStatusByMilestoneNUmber($project['id'], 5) != 'Closed'){
				if($template == 2)
				{
					return 40; 
				}
				else{
					return $alertId; 
				}				
			}
		}
		return '';
	}
	public static function test29($project, $alertId = 29){
		if(Projects::checktandm($project['id']) == 'Yes')
		{
			$eas = Projects::getSEas($project['id']);		
			$actualMD = Projects::getProjectActualManDays($project['id'], $eas);
			return $alertId.'#'.round($actualMD, 3);
		}else{

			return '';
		}
	}
	public static function test40($project, $alertId = 40){ return ''; }
	public static function test41($project){
		$alertId = 41;	
		if( ProjectsTasks::getFBRsTot($project['id']) > 0)
		{
			$notchecked= ProjectsTasks::checkFBRExistsStat($project['id'], 1);
			return $alertId.'#'.$notchecked;
		}
		else{
			return '';
		}
	}
	public static function getlastloggedhours($project_id){
		$result = Yii::app()->db->createCommand("select DATEDIFF(NOW(),(SELECT IFNULL((SELECT MAX(utime.date) FROM user_time utime, projects p, projects_tasks pt,projects_phases pp,user_task utask
			Where p.id=pp.id_project and p.id=".$project_id." and p.status=1 and pp.id=pt.id_project_phase and utask.id_task=pt.id and utask.id_user=utime.id_user and pt.id=utime.id_task 
group by p.id),NOW())))>60")->queryScalar();
		if($result[0]!='0'){
			return true;
		}
		return false;
	}
 public static function checktype($alert){
 		$result = Yii::app()->db->createCommand("select user_control from alerts where id ='".$alert."' ")->queryScalar();
 		return $result;
 }
} ?>