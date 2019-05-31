<?php
Yii::import("xupload.models.XUploadForm");
class PerformanceController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + delete + deleteContact', 	
		);
	}
	public function init(){
		parent::init();
	}
	public function accessRules(){
		return array(			
			array(
				'allow',
				'actions'=>array('sendPerformanceReminder','sendQCReminder','createQuality', 'weeklyQualityTasks'),
				'users'=>array('*'),
			),
			array('allow', 
				'actions'=>array(
						'index','getExcel','view','getTasks','deleteTask','editScore','assignQA','updateReason','assignDate','readsurvey','checkrates','changeYear','SaveProductivity','SaveQuality','ManageAfterItem','CreateAfterItem','deleteAfterItem','CreateSundayItem','ManageSundayItem','deleteSundayItem'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin ',
			),			
			array('deny', 
				'users'=>array('*'),
			),
		);
	}	
	public function actions(){
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.CustomXUploadAction',
				 'path' =>Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'customers_conn'
			 ),
		 );
	}	
	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('general-performance'))	{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$searchArray = isset($_GET['Users']) ? $_GET['Users'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/performance/index' => array(
					'label'=>Yii::t('translations','Performance'),
					'url' => array('performance/index'),
					'itemOptions' => array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;			
		$model = new Users('search');
		$model->unsetAttributes();
		$model->active = 1;
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionView(){
		if (!GroupPermissions::checkPermissions('general-performance')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		if (isset($_GET['id'])){
			$userid=$_GET['id'];
			$fullname=Users::getNamebyId($userid);
		}		
		$searchArray = isset($_GET['performance']) ? $_GET['performance'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/performance/view/'.$userid => array(
						'label'=>Yii::t('translations',$fullname.' Perf.'),
						'url' => array('performance/view', 'id'=>$userid),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Performance('search');
		$productivity = new Productivity('search');
		$quality = new Quality('search');
		$model->unsetAttributes();		
		$year=date('Y'); 		
		$prod_list = Yii::app()->db->createCommand("SELECT id, id_project ,id_task ,id_phase , case expected_mds when '0' then '1' else expected_mds END as expected_mds , adddate , id_user, maintenancec,reason, internal_project  FROM productivity WHERE id_user='".$userid."' and YEAR(adddate)='".$year."' order by id desc")->queryAll();
		$qual_list = Yii::app()->db->createCommand("SELECT id, id_project ,id_task ,id_phase  ,id_resc , expected_delivery_date as edd, score , notes, internal_project  FROM quality WHERE id_user='".$userid."' and YEAR(adddate)='".$year."' order by id desc")->queryAll();
		$cs_list = Yii::app()->db->createCommand("select count(distinct sd.id )as count , sd.id_customer ,sd.rate ,sdc.id_user from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.id_user='".$userid."' and sdc.`status`='3' and YEAR(sd.date)='".$year."'   group by sd.id_customer , sd.rate order by  sd.id_customer asc  ,sd.rate   desc
")->queryAll();
		$ps_list = Yii::app()->db->createCommand("select count(rate) as count, sr.id_project , sent_to as last , sent_to_first_name as first, ss.surveys_submitted_date as response_date , sr.surv_type ,sr.rate   from surveys_results sr , surveys_status ss where sr.rate<>0 and sr.id_project= ss.id_project and ss.surveys_submitted='1' and sr.surv_type=ss.surv_type and sr.id_project in (SELECT distinct p.id FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$userid."' ) and YEAR(ss.surveys_sent_date)='".$year."' group by sr.rate ,sr.id_project , sent_to , sent_to_first_name , ss.surveys_submitted_date, sr.surv_type  order by sr.id_project asc , sr.rate desc
")->queryAll();
			$total= count($prod_list);
			$pages=new CPagination($total);
			$pages->pageSize=8;			
		$this->render('view',array(
			'model'=>$model,
			'productivity'=>$productivity ,
			'prod_list' => $prod_list ,
			'quality'=>$quality,
			'qual_list'=>$qual_list,
			'cs_list'=>$cs_list,
			'ps_list'=>$ps_list,
			'year'=>$year,
			 'pages'=>$pages
		));
	}
	public  function actiongetTasks(){
		if(isset($_POST['id_project'])){
			$id_user=$_POST['id_user'];
			$id_project= $_POST['id_project'];
			if (substr($id_project, -1) == 'i') {
					$id_project= substr($id_project, 0,-1);					
					$tasks = Yii::app()->db->createCommand()
					->select("internal_tasks.id, internal_tasks.id_internal as id_phase, internal.name as phase_name, internal_tasks.description as name")
					->from('internal_tasks')	
					->join('internal', 'internal_tasks.id_internal = internal.id')					
					->join('user_internal', 'internal_tasks.id = user_internal.id_task')
					->where('user_internal.id_user='.$id_user.'  and internal.id = '. $id_project.' and internal_tasks.id_internal = ' . $id_project)->queryAll();
			
			}else if ((preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id_project))){
				$id_project=preg_replace("/[^0-9]/", "", $id_project);
				$tasks = Yii::app()->db->createCommand("SELECT CONCAT('m',ms.id) as id , m.support_service as id_phase, CONCAT(cu.name,' ',c.codelkup) as phase_name, ss.service as name 
									FROM maintenance_services ms
										LEFT JOIN maintenance m on m.id_maintenance=ms.id_contract
										LEFT JOIN customers cu on m.customer=cu.id
										LEFT JOIN support_services ss on ms.id_service=ss.id
										LEFT JOIN codelkups c on m.support_service=c.id
								WHERE m.support_service in ('501','502') and m.status='Active' and ms.id_contract=".$id_project."	and ss.access='Yes'")->queryAll();
			}else{
					$tasks = Yii::app()->db->createCommand()
					->select('projects_tasks.id, projects_phases.id as id_phase, projects_phases.description as phase_name, projects_tasks.description as name')
					->from('projects_tasks')
					->join('projects_phases', 'projects_tasks.id_project_phase = projects_phases.id')					
					->join('user_task', 'projects_tasks.id = user_task.id_task')
					->where('user_task.id_user='.$id_user.' and projects_phases.id_project = ' . $id_project)->queryAll();
				
			}
			echo json_encode($tasks);
		}
	}
	public function actiondeleteTask(){
			if(isset($_POST['tasktype']) && isset($_POST['taskid']) ){
					if($_POST['tasktype']=='P'){
								$dp = Yii::app()->db->createCommand("DELETE FROM productivity WHERE id='".$_POST['taskid']."' ")->execute();
									echo json_encode(array('status'=>'success')); 
									exit;
					}else{
								$dq = Yii::app()->db->createCommand("DELETE FROM quality WHERE id='".$_POST['taskid']."' ")->execute();
									echo json_encode(array('status'=>'success')); 
									exit;
					}
			}
	}	
	public function sendQAEmail($task)	{
		$select = Yii::app()->db->createCommand("SELECT * FROM quality WHERE id=".$task." ")->queryRow();		
		$notif = EmailNotifications::getNotificationByUniqueName('qa_task');
		if ($notif != NULL)	{
			$to_replace = array(
				'{group_description}',
				'{body}',
			); 
			$message = "";
			if ($select['internal_project']>0)	{
				$task_phase='<b>'.InternalTasks::getNameById($select['id_task']).'</b>';
				$pname= '<b>'.Internal::getNamebyId($select['id_project']).'</b> (internal project)';
			}else if ($select['id_phase']=='0')	{
				$task_phase='<b>'.ProjectsTasks::getTaskDescByid($select['id_task']).'</b>';
				$pname= '<b>'.Projects::getNamebyId($select['id_project']).'</b>';
			}else	{
				$task_phase='<b>'.ProjectsTasks::getTaskDescByid($select['id_task']).'</b>';
				$task_phase.='- Phase: <b>'.ProjectsPhases::getPhaseDescByPhaseId($select['id_phase']).'</b>';
				$pname= '<b>'.Projects::getNamebyId($select['id_project']).'</b>';
			} 

			$message.= "Dear ".Users::getNamebyId($select['id_resc']).",<br/><br/> Kindly note that you are requested to QC the following task:<br/><br/>FBR: <b>".$task_phase."</b><br/>Project: ".$pname."<br/>Resource: <b>".Users::getNamebyId($select['id_user'])."</b><br/>Expected delivery date: <b>".date('d/m/Y',strtotime($select['expected_delivery_date']))."</b> <br/><br/>Thank you,<br/>SNSit"; 
			$subject = $notif['name'];
			$replace = array(
					EmailNotificationsGroups::getGroupDescription($notif['id']),
					$message,	
			);
			$body = str_replace($to_replace, $replace, $notif['message']);			 
			$userEmail=UserPersonalDetails::getEmailById($select['id_resc']);
			Yii::app()->mailer->ClearAddresses();	
			$email_lm = EmailNotificationsGroups::getLMNotificationUsers($select['id_user']);
						if (filter_var($email_lm, FILTER_VALIDATE_EMAIL)) {
							//Yii::app()->mailer->AddCcs($email_lm);
							Yii::app()->mailer->AddAddress($email_lm);
						}	
			if (!empty($userEmail)){
				Yii::app()->mailer->AddAddress($userEmail);
			}
			Yii::app()->mailer->Subject  = "Quality Control";
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);			
		}			
	}
	public function actionupdateReason(){
		if(isset($_POST['reason']) && isset($_POST['prod']) ){	
			$dp = Yii::app()->db->createCommand("update  productivity set reason='".$_POST['reason']."'  WHERE id='".$_POST['prod']."' ")->execute();
			}
	}	
	public function actionassignDate(){
		if(isset($_POST['date']) && isset($_POST['taskid']) ){
			$date = strtr($_POST['date'], '/', '-');
			$d= date('Y-m-d', strtotime($date));			 
					$dp = Yii::app()->db->createCommand("update  quality set expected_delivery_date='".$d."',updated=1 WHERE id='".$_POST['taskid']."' ")->execute();
					$select = Yii::app()->db->createCommand("SELECT count(1) FROM quality WHERE expected_delivery_date<>'' and id_resc is not null and id=".$_POST['taskid']." ")->queryScalar();
					if($select >0)	{
						self::sendQAEmail($_POST['taskid']);
					}	
					
			echo json_encode(array('status'=>'success')); 
			exit;
		}

	}	
	public function actionassignQA(){
			if(isset($_POST['user']) && isset($_POST['taskid']) ){	
								$dp = Yii::app()->db->createCommand("update  quality set id_resc='".$_POST['user']."',updated=1 WHERE id='".$_POST['taskid']."' ")->execute();
								$select = Yii::app()->db->createCommand("SELECT count(1) FROM quality WHERE expected_delivery_date<>'' and id_resc is not null and id=".$_POST['taskid']." ")->queryScalar();
								if($select >0){
									self::sendQAEmail($_POST['taskid']);
								}	
									echo json_encode(array('status'=>'success' ,'user'=>$_POST['user'])); 
									exit;
			}
	}	
	public function actionSaveProductivity(){
			$error="";$id_project='';
			$projectname="";			
			if(isset($_POST['estimate']) && !empty($_POST['estimate']) ){
			 		$estimate=$_POST['estimate'];
			}else{
			 	$error="* Please put your estimation";
			}
			 if(isset($_POST['year'])){
			 	$year=$_POST['year'];
			}
			if(isset($_POST['id_project']) && !empty($_POST['id_project'])){
					$id_project=$_POST['id_project'];
			}else {
					$error="* Please choose a project";
			}

 			if(isset($_POST['id_task_phase']) && !empty($_POST['id_task_phase']) ) {
					$id_task_phase=$_POST['id_task_phase'];
					$id_task= "";
					$id_phase= "";
				if (strpos($id_task_phase,'phase_') !== false) { 
					$id_phase=substr($id_task_phase, 6) ;
					if($id_project!='' && substr($id_project, -1) == 'i')
					{
						$error="* Please choose a task";  
					}
				}else{ 
					 $id_task = substr($id_task_phase, 5);
				}			
			}else {
				$error="* Please choose Task\Phase ";
			}
				

			if(isset($_POST['id_user']) && !empty($_POST['id_user']) ){
			  	$id_user=$_POST['id_user'];
				  }else{
				  	 	$error="* Please choose a user";
				  }			
			if($error=="" || $error==" " || empty($error)){
				$m=0;	$internal=0;
				if($id_project!='' && substr($id_project, -1) == 'i')
				{
					$id_phase='';
					$internal=1;
				}else if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id_task)){
					$m=1;
				}
				$id_project=preg_replace("/[^0-9]/", "", $id_project);
				$id_task=preg_replace("/[^0-9]/", "", $id_task);		
				$save_prod = Yii::app()->db->createCommand("insert into productivity (id_project,id_user,id_phase , id_task ,expected_mds, adddate, maintenancec, internal_project) values ('".$id_project."','".$id_user."','".$id_phase."','".$id_task."','".$estimate."' ,NOW(), ".$m.", ".$internal.")")->execute();
			$user= Users::getNameById($id_user);
			if ($m>0){
				$project= Maintenance::getMaintenanceDescription($id_project);
			}else if ($internal>0){
				$project= Internal::getNameById($id_project);
			}else{
				$project= Projects::getNameById($id_project);
			}		
			if ($internal>0){				
				$task= InternalTasks::getNameById($id_task);
				$customer= 'SNS';
			}else if($id_phase=='0'  ||  $id_phase==' ' || $id_phase=='' || empty($id_phase))
			{	
				$task=ProjectsTasks::getTaskDescByid($id_task); 
				$customer= Customers::getNamebyId(Projects::getCustomerByProject($id_project));
			}else{  
				$task=ProjectsPhases::getPhaseDescByPhaseId($id_phase); 
				$customer= Customers::getNamebyId(Projects::getCustomerByProject($id_project));
			}			
			$prod_list = Yii::app()->db->createCommand("SELECT id, id_project ,id_task ,id_phase , case expected_mds when '0' then '1' else expected_mds END as expected_mds , adddate , id_user, maintenancec, internal_project FROM productivity WHERE id_user='".$id_user."' and YEAR(adddate)=YEAR(CURDATE()) order by id desc limit 1")->queryRow();
			if($prod_list['internal_project']=='1')
			{
				$actuals= Utils::formatNumber(InternalTasks::getTimeSpentperTask($prod_list['id_task'] , $prod_list['id_user']) );
			}
			else if($prod_list['id_phase']=='0')
			{
				$actuals= Utils::formatNumber(ProjectsTasks::getTimeSpentperTask($prod_list['id_task'] , $prod_list['id_user']) );
			}else{
				$actuals= Utils::formatNumber( ProjectsPhases::getTimeSpentperPhase($prod_list['id_phase'], $prod_list['id_user']) );
			}
			$prodnewdiv=" ";
			if(strlen(Projects::getNameById2($prod_list['id_project'], $prod_list['maintenancec'], $prod_list['internal_project']))<15){$projectname=Projects::getNameById2($prod_list['id_project'],$prod_list['maintenancec'], $prod_list['internal_project']) ; }else{ $projectname=Performance::getPopupGrid(Projects::getNameById2($prod_list['id_project'],$prod_list['maintenancec'], $prod_list['internal_project'])) ; } 
			if($prod_list['internal_project']>0)
			{
				$task_phase= InternalTasks::getNameById($prod_list['id_task']);
			}
			else if($prod_list['id_phase']=='0'){
				$task_phase=ProjectsTasks::getTaskDescByid2($prod_list['id_task'],  $prod_list['maintenancec']);
			}else{ 
				$task_phase=ProjectsPhases::getPhaseDescByPhaseId($prod_list['id_phase']);  
			}
			if(strlen($task_phase)<15){ $descr=$task_phase; }else{ $descr=Performance::getPopupTaskGridProd($task_phase);} 
				$prodnewdiv="<div class='perfrow'  id='P".$prod_list['id']."'>
						<div class='qualperfhorizontalLine' ></div> 
						<div class='item first prodproj' id='prodproj_".$prod_list['id']."' project='".$prod_list['id']."'>".$projectname."</div>
						<div class='item second prodtask' style=\"width:100px !important;\" id='prodtask_".$prod_list['id']."' project='".$prod_list['id']."'>".$descr."</div>
						<div class='item third' style=\"margin-left:-20px;\">".$prod_list['expected_mds'] ."</div>
						<div class='item third'>".$actuals."</div>
						<div class='item third'>".$saverage =(Utils::formatNumber((($prod_list['expected_mds']-$actuals)*100 )/ $prod_list['expected_mds'])) ."</div>
						<div class='item third'>".Utils::formatDate($prod_list['adddate'])."</div>
						<div class='item third' >  <div style=\"margin-left:50px !important;\">&emsp;  </div></div>
						<div class='item delete_P".$prod_list['id']."' style='  cursor:pointer' onclick='' ><img src='../../images/DeletePhase.png' height='15' width='15'></div>			
					</div> ";
				echo json_encode(array('status'=>'success' ,'div'=>$prodnewdiv ,'taskid'=>$prod_list['id'])); 
			exit;
			}else{
			echo json_encode(array('status'=>'failure' , 'message'=>$error));
			exit;
			}
	}
public function actioneditScore(){
			if(isset($_POST['score']) && isset($_POST['taskid']) ){	
				$dp = Yii::app()->db->createCommand("update  quality set score='".$_POST['score']."', status=5, score_date= CURRENT_DATE() WHERE id='".$_POST['taskid']."' ")->execute();
									echo json_encode(array('status'=>'success' ,'score'=>$_POST['score'])); 
									exit;
			}
	}
	public function actionSaveQuality(){
			$error="";	
			$notes=" "; $id_project='';
			if(isset($_POST['id_project']) && !empty($_POST['id_project'])){
					$id_project=$_POST['id_project'];
			}else {
					$error="* Please choose a project";
			}
			if(isset($_POST['notes']) && !empty($_POST['notes']) ){
			  	$notes=$_POST['notes'];
			}
			if(isset($_POST['id_user']) && !empty($_POST['id_user']) ){
			 		$id_user=$_POST['id_user'];
			}
			if(isset($_POST['expec_date']) && !empty($_POST['expec_date']) ){
			 		$date = strtr($_POST['expec_date'], '/', '-');
					$expec_date= date('Y-m-d', strtotime($date));
			}else{
			 	$error="* Please choose Delivery Date";
			}
			if(isset($_POST['resc']) && !empty($_POST['resc']) ){
			 		$resc=$_POST['resc'];
			}else{
			 	$error="* Please choose QC";
			}
			if(isset($_POST['id_task_phase']) && !empty($_POST['id_task_phase']) ) {
					$id_task_phase=$_POST['id_task_phase'];
					$id_task= "";
					$id_phase= "";
				if (strpos($id_task_phase,'phase_') !== false) { 
						$id_phase=substr($id_task_phase, 6) ; 
						if($id_project!='' && substr($id_project, -1) == 'i')
						{
							$error="* Please choose a task";  
						} 
					}else{ 
							 $id_task = substr($id_task_phase, 5);
							 }			
			}else {
				$error="* Please choose Task\Phase ";
			}
				
			 if(isset($_POST['year'])){
			 		$year=$_POST['year'];
			 }  else {
			 	$year='2016';
			 }			
			if($error=="" || $error==" " || empty($error)){	
				$internal=0;
				if($id_project!='' && substr($id_project, -1) == 'i')
				{
					$id_project=preg_replace("/[^0-9]/", "", $id_project);
					$internal=1;
				}	
			$save_qual = Yii::app()->db->createCommand("insert into quality (id_project,id_user,id_phase , id_task , notes,  id_resc , expected_delivery_date ,adddate, status, internal_project) values ('".$id_project."','".$id_user."','".$id_phase."','".$id_task."','".$notes."','".$resc."','".$expec_date."' ,NOW(), 1,".$internal.")")->execute();
			$user=Users::getNameById($id_user);
			$rescname=Users::getNameById($resc);
			if($internal>0){
				$task= InternalTasks::getNameById($id_task);
				$project=  Internal::getNameById($id_project);
				$customer='SNS';
			}else if($id_phase=='0'  ||  $id_phase==' ' || $id_phase=='' || empty($id_phase)){
				$task= ProjectsTasks::getTaskDescByid($id_task);
				$project=Projects::getNameById($id_project);
				$customer=Customers::getNamebyId(Projects::getCustomerByProject($id_project));
			}else{ 
				$task= ProjectsPhases::getPhaseDescByPhaseId($id_phase);
				$project=Projects::getNameById($id_project);
				$customer=Customers::getNamebyId(Projects::getCustomerByProject($id_project));
			}

						
			$qual_id = Yii::app()->db->createCommand("select id from quality WHERE id_project='".$id_project."' and id_user ='".$id_user."' and id_phase ='".$id_phase."' and  id_task ='".$id_task."' and  id_resc='".$resc."'  order by adddate desc limit 1 ")->queryScalar();
			self::sendQAEmail($qual_id);						
			$qual_list = Yii::app()->db->createCommand("select id, id_project , id_phase ,id_task ,id_resc , expected_delivery_date as edd, score , notes, internal_project from quality WHERE YEAR(adddate)=YEAR(CURDATE())  and id_user='".$id_user."'  order by id desc limit 1")->queryRow();
			if($qual_list['internal_project']>0){
				$descr= InternalTasks::getNameById($qual_list['id_task']);
				$pname= Internal::getNameById($qual_list['id_project']);
			}else if($qual_list['id_phase']=='0')
			{
				$descr= ProjectsTasks::getTaskDescByid($qual_list['id_task']);
				$pname=Projects::getNameById($qual_list['id_project']);
			}else{
				$descr= ProjectsPhases::getPhaseDescByPhaseId($qual_list['id_phase']) ;
				$pname=Projects::getNameById($qual_list['id_project']);
			} 

			if(strlen($pname)<15){ $projectname=$pname; }else{ $projectname=Performance::getPopupGrid($pname) ; } 
			if($qual_list['internal_project']>0){
				$task_phase= $descr;
			}else if($qual_list['id_phase']=='0')
			{
				$task_phase=ProjectsTasks::getTaskDescByid($qual_list['id_task']); 
			}else{
				$task_phase=ProjectsPhases::getPhaseDescByPhaseId($qual_list['id_phase']); 
			} 
			if(strlen($task_phase)<26){ $descr=$task_phase; }else{ $descr=Performance::getPopupTaskGrid($task_phase);} 
			$notess=" ";
			//if(strlen($qual_list['notes'])>18){$notess=Performance::getPopupNoteGrid($qual_list['notes']);} else { $notess=$qual_list['notes']; }
			$notess=$qual_list['notes'];
			if(empty(trim($notess))){ $style= 'padding-right:98px;';} else { $style='';}
					$qualnewdiv="
						<div class='perfrow ' id='Q".$qual_list['id']."'>
						<div class='qualperfhorizontalLine' ></div> 
						<div class='item first prodproj' id='prodproj_".$qual_list['id']."' project='".$qual_list['id']."'>".$projectname."</div>
						<div class='item second  prodtask' id='prodtask_".$qual_list['id']."' project='".$qual_list['id']."'>".$descr."</div>
						<div class='item fourth'>".Users::getNameById($qual_list['id_resc'])."</div>
						<div class='item fifth' style='".$style."'>".date('d/m/Y', strtotime(strtr($qual_list['edd'], '-', '/')))."</div>	
						<div class='item notes' >".Performance::getnotes($notess)."</div>
						<div  class='item' > &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;</div>						
						<div class='item six score' id='score_".$qual_list['id']."'>
							<select id='qual_score_".$qual_list['id']."' onchange='editScore(".$qual_list['id'].")' name='Quality[score]'>
								<option value=''></option>
								<option value='0'>Yes</option>
								<option value='1'>No</option>
							</select>
						</div>
						 	<div  class='item' >&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;</div>						
						
						<div class='item delete_Q".$qual_list['id']."' style='margin-left:12px; cursor:pointer' onclick='' ><img src='../../images/DeletePhase.png' height='15' width='15'></div>			


</div> "	;
			echo json_encode(array('status'=>'success' ,'div'=>$qualnewdiv,'taskid'=>$qual_list['id'])); 
			exit;
			}else{
			echo json_encode(array('status'=>'failure' , 'message'=>$error));
			exit;
			}
	}
	public function actionchangeYear(){
		if(isset($_POST['year']) && isset($_POST['userid']) ){
			$userid=$_POST['userid'];
			$year=$_POST['year'];
			$prod_list = Yii::app()->db->createCommand("SELECT id, id_project ,id_task ,id_phase , case expected_mds when '0' then '1' else expected_mds END as expected_mds , adddate , id_user FROM productivity WHERE id_user='".$userid."'  and YEAR(adddate)='".$year."' order by id desc")->queryAll();
			if(count($prod_list)>0){
			  	$taverage=0;    
    		   	$count=0;
    		   	$projectname="";
				$prod_div=" <div class='table '  >
			 					<div class='perfrow titleRow'>
									<div class='item first'>Project</div>
									<div class='item second'>Phase/Task</div>
									<div class='item third'>Expected MDs</div>
									<div class='item third'>Actual MDs</div>
									<div class='item third'>OverRun %</div>
									<div class='item third'>Logged Date</div>
									</div>  ";	
						$prod_div.=	"<div class='newdatacontainer'>";
				foreach ($prod_list as $key => $value) {
						$value['id_phase']=='0'? $actuals=Utils::formatNumber(ProjectsTasks::getTimeSpentperTask($value['id_task'] , $value['id_user']) ): $actuals= Utils::formatNumber( ProjectsPhases::getTimeSpentperPhase($value['id_phase'], $value['id_user']) );  
   						$saverage =(($value['expected_mds']-$actuals)*100 )/ $value['expected_mds'];
    					$taverage+=$saverage; 
    					$count++;		
						$actuals=$value['id_phase']=='0'? Utils::formatNumber(ProjectsTasks::getTimeSpentperTask($value['id_task'] , $value['id_user']) ): Utils::formatNumber( ProjectsPhases::getTimeSpentperPhase($value['id_phase'], $value['id_user']) );
						
						if(strlen(Projects::getNameById($value['id_project']))<15){$projectname=Projects::getNameById($value['id_project']) ; }else{ $projectname=Performance::getPopupGrid(Projects::getNameById($value['id_project'])) ; } 
					 $value['id_phase']=='0'? $task_phase=ProjectsTasks::getTaskDescByid($value['id_task']): $task_phase=ProjectsPhases::getPhaseDescByPhaseId($value['id_phase']);  
						if(strlen($task_phase)<26){ $descr=$task_phase; }else{ $descr=Performance::getPopupTaskGrid($task_phase);} 
						$prod_div.="<div class='perfrow'  id='P".$value['id']."'>
									<div class='qualperfhorizontalLine' ></div> 
									<div class='item first prodproj' id='prodproj_".$value['id']."' project='".$value['id']."'>".$projectname."</div>
									<div class='item second prodtask' id='prodtask_".$value['id']."' project='".$value['id']."'>".$descr."</div>
									<div class='item third'>".$value['expected_mds'] ."</div>
									<div class='item third'>".$actuals."</div>
									<div class='item third'>".Utils::formatNumber((($value['expected_mds']-$actuals)*100 )/$value['expected_mds'])."</div>
									<div class='item third'>".Utils::formatDate($value['adddate'])."</div>
										</div> ";
				}
				$prod_div.="  </div> </div>";
					if($count==0){ $count=1;}
  					$total_productivity= ($taverage/$count);
    				$tot_prod=Utils::formatNumber($total_productivity,2); 
    		}else {
    			$prod_div=" <div class='table '  >
			 					<div class='perfrow titleRow'>
									<div class='item first'>Project</div>
									<div class='item second'>Phase/Task</div>
									<div class='item third'>Expected MDs</div>
									<div class='item fourth'>Actual MDs</div>
									<div class='item fifth'>OverRun %</div>
									<div class='item six'>Logged Date</div>
									</div> </div>  ";	
								$tot_prod="";
								$prod_sign="";
    		}
			$qual_list = Yii::app()->db->createCommand("select id, id_project ,id_task , id_phase ,id_resc , expected_delivery_date as edd, score , notes FROM quality WHERE id_user='".$userid."' and YEAR(DATE_FORMAT(STR_TO_DATE(expected_delivery_date, '%d/%m/%Y'), '%Y-%m-%d'))='".$year."'  order by id desc")->queryAll();
			if(count($qual_list)>0){				
				$qaverage=0;    
     			$count=0;
     			$total_quality=0;
     			$score=" &nbsp; &nbsp; ";
				$qual_div="<div class='table '  >
						    <div class='perfrow titleRow'>
							<div class='item first'>Project</div>
							<div class='item second'>Phase/Task</div>
							<div class='item fourth'>QC</div>
							<div class='item fifth'>Exp. Delivery Date</div>							
							<div class='item notes'>Notes</div>
							<div class='item six' style='padding-left:7px;' >Passed</div>
							</div>  ";	
					$qual_div.=	"<div class='newdatacontainer'>";
					foreach ($qual_list as $key => $value) {
						  if(isset($value['score'])){      
     						 if($value['score']=='0'){
        					 $qaverage++;
        					 $score="Yes";
      						}else{
      							$score="No";
      						}       							
      					}
    					$count++;						
						$user=Users::getNameById($value['id_resc']);						
						if(strlen(Projects::getNameById($value['id_project']))<15){$projectname=Projects::getNameById($value['id_project']) ; }else{ $projectname=Performance::getPopupGrid(Projects::getNameById($value['id_project'])) ; } 
					$value['id_phase']=='0'? $task_phase=ProjectsTasks::getTaskDescByid($value['id_task']): $task_phase=ProjectsPhases::getPhaseDescByPhaseId($value['id_phase']);  
						if(strlen($task_phase)<26){ $descr=$task_phase; }else{ $descr=Performance::getPopupTaskGrid($task_phase);} 
						$notess=" ";
						if(strlen($value['notes'])>18){$notess=Performance::getPopupNoteGrid($value['notes']);} else { $notess=$value['notes']; };
				$qual_div.="
						<div class='perfrow ' id='Q".$value['id']."'>
						 <div class='qualperfhorizontalLine' ></div> 
						<div class='item first prodproj' id='prodproj_".$value['id']."' project='".$value['id']."'>".$projectname."</div>
						<div class='item second prodtask' id='prodtask_".$value['id']."' project='".$value['id']."'>".$descr."</div>
						<div class='item fourth'>".$user."</div>
						<div class='item fifth'>".$value['edd']."</div>						
						<div class='item notes'  onmouseenter='showToolNoteTip(this)' onmouseleave='hideToolNoteTip(this)' >".$notess."</div>
						<div class='item six'>".$score."</div>
						</div> "	;
					}				
				$qual_div.=" </div> </div>";
				if($count==0){ $count=1;}
      			$total_quality= ($qaverage*100)/$count;
      			$tot_qual=Utils::formatNumber($total_quality,2);
      		} else {
      					$qual_div="<div class='table '  >
						    <div class='perfrow titleRow'>
							<div class='item first'>Project</div>
							<div class='item second'>Task</div>
							<div class='item third'>QC</div>
							<div class='item fourth'>Expected Delivery Date</div>						
							<div class='item six'>Notes</div>
							<div class='item fifth'>Passed</div>
							</div>  </div> ";
							$tot_qual="";
							$qual_sign="";
      		}
      		$cs_list = Yii::app()->db->createCommand("select count(distinct sd.id )as count , sd.id_customer ,sd.rate ,sdc.id_user from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.id_user='".$userid."' and sdc.`status`='3' and YEAR(sd.date)='".$year."'  group by sd.id_customer , sd.rate order by  sd.id_customer  asc ,sd.rate desc
")->queryAll();
      		if(count($cs_list)>0){
      			$csaverage=0;
     			$count=0;	
     			$cs_div="<div class='table table2' id='currentcsyear'>
							<div class='titret'>Support Ratings</div>
								<div class='perfrow titleRow'>
								<div class='item customer'>Customer Name</div>
								<div class='item closed'>Closed SRs</div>
								<div class='item rating'>SRs Rating</div>
								<div class='item satisf'>Satis.%</div>
							</div>";
						$cs_div.=	"<div class='newcspsdatacontainer'>";	
				$customer=" ";		
				foreach ($cs_list as $key => $value) {
					 if($value['rate']>2){
         				 $csaverage+=$value['count'];
       					}
          				$count+=$value['count'];				
					$customername=Customers::getNamebyId($value['id_customer']);
	 				$SRCustomerResc= Utils::formatNumber(CustomerSatisfaction::getCountSRCustomerResource($value['id_user'],$value['id_customer'] , $year),2);
	 				$totalSRclosedSatisf= Utils::formatNumber((CustomerSatisfaction::getTotalSRClosedCustomerSatis($value['id_user'],$value['id_customer'], $year)*100)/$SRCustomerResc,1);
	 				if($SRCustomerResc==0){$SRCustomerResc=1;}
	 				$count=Utils::formatNumber(($value['count']*100)/$SRCustomerResc,1);
					if($value['id_customer']!=$customer){
						$cs_div.="<div class='perfrowsmall'> <div class='perfhorizontalLine' ></div> 
									<div class='item  customer ' >". $customername ." </div>
									<div class='item closed'>". $SRCustomerResc ."</div>
									<div class='item rating fcs'  id='checkrates_".$value['id_customer']."' customer='".$value['id_customer']."' user='".$userid."' year='".$year."' > <span style='width:48px;'>".Utils::formatNumber(($value['count']*100)/$SRCustomerResc,1)."%</span> <img height='10' src='../../images/".$value['rate']."stars.png' /> </div>
									<div class='item satisf '   >".$totalSRclosedSatisf."% </div>   <br/>  <br/>  <br/>  <br/> 
									</div>  ";  
					} else { 
						$cs_div.=" <div class='fcs' id='checkrates_".$value['id_customer']."' customer='".$value['id_customer']."' user='".$userid."' year='".$year."'  style='padding-left:650px;' >".Utils::formatNumber(($value['count']*100)/$SRCustomerResc,1)."% <img height='10' src='../../images/".$value['rate']."stars.png' /> </div>
									  <br/>"; 
					} 
					$customer=$value['id_customer'];
				 }
				 $cs_div.="</div> </div>";
				 $total_cs= (($csaverage*10)/$count)*10;
				 $tot_cs=Utils::formatNumber($total_cs,1);
			}else{
				 			$cs_div="<div class='table table2' id='currentcsyear'>
										<div class='titret'>Support Ratings</div>
										<div class='perfrow titleRow'>
										<div class='item customer'>Customer Name</div>
										<div class='item closed'>Closed SRs</div>
										<div class='item rating'>SRs Rating</div>
										<div class='item satisf'>Satis.%/div>
									</div>
									</div>";
									$tot_cs="";
									$cs_sign="";
			}
			$ps_list = Yii::app()->db->createCommand("select count(rate) as count, sr.id_project , sent_to as last , sent_to_first_name as first, ss.surveys_submitted_date as response_date , sr.surv_type ,sr.rate   from surveys_results sr , surveys_status ss where sr.rate<>0 and sr.id_project= ss.id_project and ss.surveys_submitted='1' and sr.surv_type=ss.surv_type and sr.id_project in (SELECT distinct p.id FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$userid."' ) and YEAR(ss.surveys_sent_date)='".$year."'  group by sr.rate ,sr.id_project , sent_to , sent_to_first_name , ss.surveys_submitted_date, sr.surv_type  order by sr.id_project asc , sr.rate desc
")->queryAll();
			if(count($ps_list)>0){
				$ps_div="<div class='table' id='currentpsyear'>
							<div class='titret'>Projects Surveys</div>
							<div class='perfrow titleRow'>
								<div class='item first'>Customer Name</div>
								<div class='item second'>Project Name</div>
								<div class='item surveyee'>Surveyee</div>
								<div class='item response'>Response Date</div>
								<div class='item type'>Type</div>
								<div class='item third'>Rates</div>
								<div class='item satisf' >Satis.%</div>
							</div>";
				$ps_div.=	"<div class='newcspsdatacontainer'>";	
						$psaverage=0;
    					$count=0;		
						$customer=" ";
				foreach ($ps_list as $key => $value) {
					$cname= Customers::getNameById(Projects::getCustomerByProject($value['id_project']));
					$pname=Projects::getNamebyId($value['id_project']);
					$response_date= substr($value['response_date'], 0,10) ;
					$surv_type = $value['surv_type']=='close'?'Closure':'Intermediate';
					$tot_rate=(CustomerSatisfaction::getTotalSurveyProjectType($value['id_project'],$value['surv_type']) *10)."%" ;
					if(Projects::getCustomerByProject($value['id_project'])!=$customer){
					 	$count++;
   					   	$psaverage=  $psaverage+ CustomerSatisfaction::getTotalSurveyProjectType($value['id_project'],$value['surv_type']);
						$ps_div.="<div class='perfrowsmall'>
						<div class='perfhorizontalLine'></div> 
									<div class='item  first ' >".$cname."</div>
									<div class='item second'>".$pname." </div>
									<div class='item surveyee'>".$value['first'].' '.$value['last'] ." </div>
									<div class='item response'> ".Utils::formatDate($response_date)." </div>
									<div class='item type'> ".$surv_type." </div>
									<div class='item third fq' project='".$value['id_project']."' type='".$value['surv_type']."'> ".($value['count']*10)."% <img height='10' src='../../images/".$value['rate']."stars.png' /> </div>
									<div class='item satisf '  >".$tot_rate." </div> <br/> <br/><br/> <br/> 
								</div>";
					}else {
						$ps_div.="
				 					<div class='fq' project='".$value['id_project']."' type='".$value['surv_type']."' style='margin-left:657px; text-align:left;'>".($value['count']*10)."% <img height='10' src='../../images/".$value['rate']."stars.png' /> </div> <br/>
								";
					}
					$customer=Projects::getCustomerByProject($value['id_project']); 
				 }
				$ps_div.=" </div> </div>";
   				$total_ps= ($psaverage/$count)*10;
   				$tot_ps=Utils::formatNumber($total_ps,2);  
			}else{					
					$ps_div="<div class='table' id='currentpsyear'>
								<div class='titret'>Projects Surveys</div>
								<div class='perfrow titleRow'>
								<div class='item first'>Customer Name</div>
								<div class='item second'>Project Name</div>
								<div class='item surveyee'>Surveyee</div>
								<div class='item response'>Response Date</div>
								<div class='item type'>Type</div>
								<div class='item third'>Rates</div>
								<div class='item satisf' >Satis.%</div>
							</div>
								</div>";
								$tot_ps="";
								$ps_sign="";
			}
			 $summary="<div class='item'>
                       <div class='label'>Productivity</div>
                       <div class='number'>".$tot_prod."%</div>
                      
                   </div>
                    <div class='item'>
                       <div class='label'>Quality</div>
                       <div class='number'>".$tot_qual." %</div>
                      
                   </div>
                    <div class='item'> 
                      <div class='label' style='padding-right:50px;' >Customer Satisfaction</div>
                        <div class='smallitem'>
                        <div class='number'>".$tot_ps."%</div>
                        <div class='perf_note'>Projects Surveys</div>
                        </div>
                         <div class='smallitem'>
                           <div class='number'>".$tot_cs."%</div>
                        <div class='perf_note'>Support Ratings</div>
                           </div>
                     
                   </div>
                   
				  "; 			 
				  $performer=0;
		   						 if ($tot_prod>=0) { $performer++;  } else{$performer-- ;}
		   						 if ($tot_qual>=50 ) { $performer++;  } else{$performer-- ;}
		   						 if ($tot_ps>=50) { $performer++; } else{$performer-- ;}
		   						 if ($tot_cs>=50){ $performer++; } else{$performer-- ;}
		   						      if($performer<0){  $performance="<div class='caption bad'>Bad Performer</div>"; } 
               						  if($performer==0 ){   $performance="<div class='caption good'>Good Performer</div>"; } 
                  					  if($performer>0 ){$performance="<div class='caption super'>Super Performer </div>";  }
			echo json_encode(array('status'=>'success' ,'prod_div'=>$prod_div,'qual_div'=>$qual_div , 'cs_div'=>$cs_div , 'ps_div'=>$ps_div , 'sum'=>$summary , 'perf'=>$performance)); 
			exit;
		}
	}	
	public function actionCreateAfterItem()	{
		$model = new FullSupport();		
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
					$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
					$model->to_date = date('Y/m/d',strtotime($_POST['FullSupport']['to_date']));
					$model->type='402';					
   			if ($model->save()){	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_after', array(
	            	'model'=> $model  	
					), true, true)));
	        	exit;	
			}
   		}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_after', array(
            	'model'=> $model 
				), true, true)));
        exit;
	}
	public function actionCreateSundayItem(){
		$model = new FullSupport();
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
					$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
					$model->type='403';					
   			if ($model->save()){	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_sunday', array(
	            	'model'=> $model  	
					), true, true)));
	        	exit;	
			}
   		}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_sunday', array(
            	'model'=> $model 
				), true, true)));
        exit;
	}	
	public function actionManageAfterItem($id){
		$model = FullSupport::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
   			$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
					$model->to_date = date('Y/m/d',strtotime($_POST['FullSupport']['to_date']));
					$model->type='402';
   			if ($model->save()){
					$model->save();					
					echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_after', array(
		            	'model'=> $model
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_after', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}	
	public function actionManageSundayItem($id){
		$model = FullSupport::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['FullSupport'])){
   			$model->attributes = $_POST['FullSupport'];
   			$model->from_date = date('Y/m/d',strtotime($_POST['FullSupport']['from_date']));
					$model->type='403';
   			if ($model->save()){
					$model->save();					
					echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_item_form_sunday', array(
		            	'model'=> $model
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form_sunday', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}	
public function actionDeleteAfterItem($id){
		$model = FullSupport::model()->findByPk($id);
		if ($model===null)
			echo json_encode(array('status'=>'error', 'error' => '404'));	
		if ($model->delete()) {
						echo json_encode(array('status'=>'saved'));	
		} 
	}
public function actionDeleteSundayItem($id){
		$model = FullSupport::model()->findByPk($id);
		if ($model===null)
			echo json_encode(array('status'=>'error', 'error' => '404'));	
		if ($model->delete()) {
						echo json_encode(array('status'=>'saved'));	
		} 
	}	
public function actionreadsurvey(){
		if(isset($_POST['project'])){
			$project=$_POST['project'];
		}
		if(isset($_POST['surv_type'])){
			$surv_type=$_POST['surv_type'];
		}
$readsurvey = Yii::app()->db->createCommand("select sr.id,  sq.id as question_id , sq.question,sr.rate,sr.surv_type ,sr.comments from surveys_results sr, surveys_questions sq where  sr.id_question=sq.id and sr.id_project='".$project."' and sr.surv_type='".$surv_type."'
")->queryAll();
		$read="<table border='0' class='readsurv'>";
		$i=0;
		foreach ($readsurvey as $key => $value) {
			$i++;
				$img="";
		if($value['rate']=='1'){$img="<img height='25' src='../../images/1star.png'>";}
			if($value['rate']=='2'){$img="<img height='25' src='../../images/2star.png'>";}
				if($value['rate']=='3'){$img="<img height='25' src='../../images/3star.png'>";}
					if($value['rate']=='4'){$img="<img height='25'  src='../../images/4star.png'>";}
						if($value['rate']=='5'){$img="<img height='23' src='../../images/5star.png'>";}

		$read.="<tr> ";	
		if($value['question_id']=='11' || $value['question_id']=='22'){
						}else{
		$read.="<td>".$i.". </td>		
						<td>".$value['question']."</td>";
					
							$read.="<td>".$img."</td>";
						}
						$read.="</tr>";
		}
	$read.="</table>";
	$pname = Projects::getNamebyId($project);
echo json_encode(array('status'=>'success','readsurvey'=>$read , 'pname'=>$pname));
}	
public function actioncheckrates(){
		if(isset($_POST['customer'])){
			$customer=$_POST['customer'];
		}
		if(isset($_POST['user'])){
			$user=$_POST['user'];
		}
		if(isset($_POST['year'])){
			$year=$_POST['year'];
		}		
$checkrates = Yii::app()->db->createCommand("select sd.id ,sd.id_customer ,sd.rate ,sdc.id_user from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.id_user='".$user."' and sdc.`status`='3' and sd.id_customer='".$customer."' and YEAR(sd.date)='".$year."'   group by sd.id_customer , sd.rate  ,sd.id , sdc.id_user order by  sd.id_customer  asc , sd.rate desc")->queryAll();
	$read="	<table border='0' class='readsurv'>";
		foreach ($checkrates as $key => $value) {
				$img="";
		if($value['rate']=='1'){$img="<img height='25' src='../../images/1star.png'>";}
			if($value['rate']=='2'){$img="<img height='25'  src='../../images/2star.png'>";}
				if($value['rate']=='3'){$img="<img height='25'  src='../../images/3star.png'>";}
					if($value['rate']=='4'){$img="<img  height='25'  src='../../images/4star.png'>";}
						if($value['rate']=='5'){$img="<img height='23'  src='../../images/5star.png'>";}
		$read.="<tr> ";	
						$read.="<td> SR# ".$value['id']." </td>";				
						$read.="<td>".$img."</td>";						
						$read.="</tr>";
		}
	$read.="</table>";
	$pname = Customers::getNamebyId($customer);
echo json_encode(array('status'=>'success','checkrates'=>$read , 'pname'=>$pname));
}
public function actionGetExcel(){		
		$data = Performance::getAllData();
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");		
		$sheetId = 0;
		$nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray); 

		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Resource')
		->setCellValue('B1', 'Team')
		->setCellValue('C1', 'Project')
		->setCellValue('D1', 'Phase/Task')
		->setCellValue('E1', 'QCed User')
		->setCellValue('F1', 'Type')
		->setCellValue('G1', 'Status')
		;
		$i = 1;
		foreach($data as $d => $row)
		{
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, (Users::getUsername($row['user'])))
			->setCellValue('B'.$i, (Users::getUnit($row['user'])))
			->setCellValue('C'.$i, Projects::getNameById2($row['id_project'], $row['m'], $row['internal_project'])) 
			->setCellValue('D'.$i, $row['id_phase']=='0'? $task_phase=ProjectsTasks::getTaskDescByid2($row['id_task'], $row['m']): $task_phase=ProjectsPhases::getPhaseDescByPhaseId($row['id_phase']))
			->setCellValue('E'.$i, (Users::getUsername($row['QCed'])))
			->setCellValue('F'.$i, $row['type'] =='q' ? 'Quality' : 'Productivity')
			->setCellValue('G'.$i, Performance::getstatus($row['id'], $row['type'],$row['user'], $row['id_phase'], $row['id_task']))
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Performance Tasks - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="TASKS'.date("d_m_Y").'.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	public function actioncreateQuality(){
		$tasks=Performance::getQCTasks();
		foreach ($tasks as $task) {
			$save_q = Yii::app()->db->createCommand("insert into quality (id_project, id_phase, id_task, id_user, adddate) values ('".$task['id_project']."','".$task['id_project_phase']."','".$task['id_task']."','".$task['id_user']."', CURDATE())")->execute();
			$updateFlaf = Yii::app()->db->createCommand("update projects_tasks set flag=1 where id= ".$task['id_task']." ")->execute();					
		}		
	}	
	public function actionweeklyQualityTasks(){		
		$selects=Performance::getQCTasksFromProj();
		$notif = EmailNotifications::getNotificationByUniqueName('biweekly_qa');
		if ($notif != NULL ){
			$to_replace = array(
				'{group_description}',
				'{body}',
			);			
			Yii::app()->mailer->ClearAddresses();			 
			$message = "Dear All,<br/><br/> Kindly find below a list of ";
			$content='';
			if(!empty($selects)){
				foreach ($selects as $select) {
					$project= ProjectsTasks::getProjectByTask($select['id']);
					$task_phase=ProjectsTasks::getTaskDescByid($select['id']);
						$content.="<tr><td>".Customers::getNamebyid(Projects::getCustomerByProject($project))."</td> <td>".Projects::getNamebyid($project)."</td> <td>".Projects::getProjectManager($project)."</td> <td>".$task_phase." </td> <td>".$select['notes']." </td> <td  style='text-align:center;'> ".ProjectsTasks::getComplexityLabel($select['complexity'])." </td><td>".ProjectsTasks::getAllUsersTaskNormalOps($select['id'])."</td><td>".ProjectsTasks::getAllUsersTaskNormalTech($select['id'])."</td> <td>".((isset($select['adddate']) && $select['adddate'] !="")? date("d/m/Y", strtotime($select['adddate'])): " ")."</td></tr>";
				}
			}			
			if (!empty($content)){
					$message.='FBRs/interfaces to be QCed:<br/><br/><table border="1"  style="font-family:Calibri;border-collapse: collapse;"><tr><th width="100">Customer</th><th width="300">Project</th><th width="100">PM</th><th width="150">FBR Title</th><th  width="400">Description</th><th>Complexity</th><th  width="100">Assigned Ops</th><th  width="100">Assigned Tech</th><th  width="100">Creation Date</th></tr>';
					$message.=$content;
					$message.="</table> ";  
					
			}else{
				$content= 'empty';
				$message= "Dear All,<br/><br/> Kindly note that no tasks were created this week for QA validation."; 
			}
			if(!empty($content)){
				$message.=" <br/><br/>Best Regards,<br/>SNSit"; 
					$subject = $notif['name'];
					$replace = array(
							EmailNotificationsGroups::getGroupDescription($notif['id']),
							$message,
			
					);
					$body = str_replace($to_replace, $replace, $notif['message']);					
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);		
					//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");			
					foreach($emails as $email){
						Yii::app()->mailer->AddAddress($email);
					}
					Yii::app()->mailer->Subject  = $subject ;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);
			}			
		}
	}
	public function actionsendQCReminder(){
		$users=Performance::getQCUsersDeadline();
		$notif = EmailNotifications::getNotificationByUniqueName('quality_reminder');
		$subject = 'QC Reminder';
		$str='';
		if ($notif != NULL && !(empty($users))) {
    		foreach ($users as $user) {
    			$str='';
    			$tasks=Performance::getQCtasksByUser($user['id']);
    			$usr='<b>'.Users::getNameById($user['id']).'</b>';
				 foreach ($tasks as $task){
				 	$id_phase= $task['id_phase'];
				 	$id_task=$task['id_task'];
				 	$id_project=$task['id_project'];

				 	$doneby=Users::getNameById($task['id_user']);
				 	if ($task['internal_project']>0)	{
				 		$task=InternalTasks::getNameById($id_task);
				 		$project=Internal::getNameById($id_project);
						$customer='SNS ';
				 	}else if($id_phase=='0'  ||  $id_phase==' ' || $id_phase=='' || empty($id_phase)){
				 		$task=ProjectsTasks::getTaskDescByid($id_task) ;
				 		$project=Projects::getNameById($id_project);
						$customer=Customers::getNamebyId(Projects::getCustomerByProject($id_project));
				 	} else{
				 		$task=ProjectsPhases::getPhaseDescByPhaseId($id_phase);
				 		$project=Projects::getNameById($id_project);
						$customer=Customers::getNamebyId(Projects::getCustomerByProject($id_project));
				 	}
					$str.='<br/>- <b>Project:</b> '.$project.', <b>Task:</b> '.$task.', <b>Customer:</b> '.$customer.', <b>User:</b> '.$doneby.'.';
				 }
    			$to_replace = array(
					'{user}',
					'{task}'					
					);
	    		$replace = array(						
					$usr,
					$str
					);
				$body = str_replace($to_replace, $replace, $notif['message']);							
				$email=Users::getEmailById($user['id']);							
				Yii::app()->mailer->ClearAddresses();
				Yii::app()->mailer->ClearCcs();
				//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
				if (!empty($email)){
					Yii::app()->mailer->AddAddress($email);
				}
				$email_lm = EmailNotificationsGroups::getLMNotificationUsers($user['id']);
						if (filter_var($email_lm, FILTER_VALIDATE_EMAIL)) {
							//Yii::app()->mailer->AddCcs($email_lm);
							Yii::app()->mailer->AddAddress($email_lm);
						}									
				Yii::app()->mailer->Subject  = $subject;
				Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				Yii::app()->mailer->Send(true);
			}
		}
					
	}
	public function actionsendPerformanceReminder(){
					$notif = EmailNotifications::getNotificationByUniqueName('performance_reminder');
					$subject = 'Performance Reminder';
					$history=Performance::getUsersPerformanceHistory();
    				if ($notif != NULL) {
    					$to_replace = array(
						'{history}'						
							);
    					$replace = array(						
							$history
							);
					$body = str_replace($to_replace, $replace, $notif['message']);					
					$emails=UserPersonalDetails::getAllLineManagers();				
					$emailscc = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();				
					foreach($emails as $email) {
						if (!empty($email))
							Yii::app()->mailer->AddAddress(Users::getEmailById($email['linem']));
					}	
					foreach($emailscc as $emailc) {
						if (!empty($emailc))
						{	//Yii::app()->mailer->AddCCs($emailc);
							Yii::app()->mailer->AddAddress($emailc);
						}
						
					}					
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);

				}					
	}
}?>