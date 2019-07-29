<?php
class ProjectsController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl',
			'postOnly + delete', 
		);
	}
	public function init(){
		parent::init();
	}
	public function accessRules(){
		return array(
			array(
				'allow',
				'actions'=>array('SendRedundantTaskFBR','GetAlerts','sendIssuesSSummary','deleteEmptyIssues','sendIssuesSSummarySolved','goLiveRemider','fbrsSummary','undocumentsTasksReminder','sendDailyIssues','sendWeeklyIssuesSSummary','sendContractOverload','sendProjectCRStatus','sendProjectSWStatus','sendProjectConStatus','sendProjectClosedNotTransitedSupport','sendNotificationsEmailsAlertsSecond','sendNotificationsEmailsAlerts','sendNotificationsEmailsAlertsperPM','sendSurvey','sendProjectsSurveyReminders'),
				'users'=>array('*'),
			),
			array(
				'allow',
					'actions'=>array('AddChecklist','index','create','GetExcelInternal','view','update','CreateTasks','CreateRiskItem','createIssue','CreateScenarioItem','UpdateTasks','manageScenarioItem','manageIssueItem','ManageRiskItem','DeleteScenarioItem','DeleteRiskItem','ManageTasks','ChangeTask','ChangePhase','ValidateMilestones','ValidateAlerts','ValidateTimesheet',
					'CreatePhase','getProjectsByClientsMulti','GetCityByClientsMulti','UpdatePhase','ChangeOffset' ,'ManagePhase','invoicesFlag','DeleteTask','DeletePhase','ManageMilestone','ManageChecklist','GetUnssignedUsersIssues','GetUnssignedUsers','assignUsersIssues','AssignUsers','GetAlertsPerProject',
					'CreatePhase','issue','DeleteUploadIssueFile','UpdatePhase','Updatelessons','ChangeOffset' ,'ManagePhase','invoicesFlag','checklistFlag','timesheetFlag','DeleteTask','DeletePhase','ManageMilestone','ManageChecklist','GetUnssignedUsers','AssignUsers','GetAlertsPerProject',
					'GetAssignedUsers','GetAssignedUsersIssues','ValidateOpenChecklist','ValidateOpenIssues','ValidateTimesheetGetNames','unassignUsersIssues', 'UnassignUsers','ValidateClosedonupdate','getProjectsByClient','GetProjectsByClientName','GetPhasesByProjectTimesheetReport' ,'GetProjectsByClientTimesheetReport','GetProjectsAndInternalByClient','GetProjectsTrainingsByClientName','GetParentProjectsMaintByClient','GetParentProjectsByClient','getProjectsByClientUser','GetCurrencyRate','GetProjectsTrainingsByClient',
					'createStatusReport','deleteIssueItem','manageMilestonen','manageHighlight','deleteHighlight','deleteMilestone','UpdateStatusReport',
					'sendNotif','updateDocument', 'upload', 'alerts','getexcel','getExcelIssues','getExcelReport','getBillablity', 'readsurvey','testProjectActualExpenses','showMilestoneAlert','updateFooter','validateTransitionSupport','deleteOffsets','getestimated','closeOffsets','reasonOffset','valueOffset','signOffset','sendNotificationsEmailsAlerts','sendProjectTransitionSupport','SetOffsetReason','setreason','getProjectInfo'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',
			'users'=>array('*'),
			),
		);
	}
	public function actions()
	{
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.CustomXUploadAction',
				 'path' =>Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'customers_conn'
			 ),
		 );
	}
	public function actionissue($id){		
		
		$model=ProjectsIssues::model()->findByPk($id);		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/projects/issue/'.$id => array(
						'label'=>Yii::t('translations', ' Issue#'.str_pad($model->id_issue, 3, "0", STR_PAD_LEFT)),
						'url' => array('projects/issue', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->render('issue',array('model'=>$model,	));
	}

    /*
    * Author: Mike
    * Date: 03.07.19
    * Add the option of adding new check list items
    */
	public function actionAddChecklist(){
	    $checklist= new Checklist;
	    $last = Yii::app()->db->createCommand("SELECT MAX(id) AS id from checklist")->queryAll();
        $checklist->id = $last[0]['id'] + 1;
        $checklist->descr = $_POST['Checklist']['descr'];
	    $checklist->category = $_POST['Checklist']['category'];
        $checklist->checklist_number = $last[0]['id'] + 1;
        $checklist->responsibility = $_POST['Checklist']['responsibility'];
        $checklist->id_phase = (int)$_POST['Checklist']['id_phase'];
        if ($checklist->save()){
            $project_checklist = new ProjectsChecklist;
            $project_checklist->id_project = (int)$_POST['project_id'];
            $project_checklist->id_checklist = $checklist->id;
            $project_checklist->status = $_POST['responsibility'];
            $project_checklist->id_phase = $checklist->id_phase;
            if ($project_checklist->save()){
                echo true;
                die();
            }
        }
        echo false;
        die();
    }

	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('general-projects')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['Projects']) ? $_GET['Projects'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/projects/index' => array(
						'label'=>Yii::t('translations', 'Projects'),
						'url' => array('projects/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Projects('search');
		$model->unsetAttributes();  
		$model->attributes= $searchArray;
		$alerts = ProjectsAlerts::getAll();
		$this->render('index',array(
			'model'=>$model,
			'alerts' => $alerts,
		));
	}
	public function actionupdateDocument(){
		if (isset($_GET['id'])){
			$id = (int) $_GET['id'];
			$ps= Yii::app()->db->createCommand("SELECT project_manager, business_manager FROM `projects` where id= (select id_project from undocumented_tasks where id= ".$id.")")->queryRow();
			if($ps['project_manager'] != Yii::app()->user->id  && $ps['business_manager'] != Yii::app()->user->id ){
				throw new CHttpException(404,'You have to be the designated PM or BM.');
			}else{
				Yii::app()->db->createCommand("update undocumented_tasks set flag='1' where id=".$id."")->execute();
				$this->redirect(Yii::app()->createUrl('projects/index'));	
			}
		}
	}
	
	public function actionGetExcelInternal($id){
        $data = Internal::gettaskitems($id);
        $proj= Internal::getNamebyId($id);
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");		
		$sheetId = 0;

		$nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray); 


		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Task')
		->setCellValue('B1', 'Status')
		->setCellValue('C1', 'Priority')
		->setCellValue('D1', 'ETA')
		->setCellValue('E1', 'EMDs')
		->setCellValue('F1', 'Assigned Resources')
		->setCellValue('G1', 'Close Date')
		->setCellValue('H1', 'Remarks')
		;
		$i = 1;
		foreach($data as $d => $row){
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, $row['description'])
			->setCellValue('B'.$i, InternalTasks::getStatus($row['status']))
			->setCellValue('C'.$i, InternalTasks::getPriority($row['priority'])) 
			->setCellValue('D'.$i, (isset($row['eta']) && $row['eta'] !="")? $row['eta']: " ")
			->setCellValue('E'.$i, $row['estimated_effort'])
			->setCellValue('F'.$i, InternalTasks::getAllUsersTask($row['id']))
			->setCellValue('G'.$i,  (isset($row['close_date']) && $row['close_date'] !="")? date("d/m/Y", strtotime($row['close_date'])): " ")
			->setCellValue('H'.$i, $row['notes'])
			;			
		}
		$objPHPExcel->getActiveSheet()->setTitle('Tasks - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$proj.'_Tasks_'.date("d_m_Y").'.xls"');
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
	public function actionCreate(){
		if (!GroupPermissions::checkPermissions('general-projects','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/projects/create' => array(
							'label'=>Yii::t('translations', 'New Projects'),
							'url' => array('projects/create'),
							'itemOptions'=>array('class'=>'link'),
							'subtab' =>  $this->getSubTab(),
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = 0;
		$model = new Projects;
		$this->render('manage',array(
			'model'=>$model,
		));
	}
	public function actionView($id, $active = null){
		$model = $this->loadModel($id);
		$arr = Utils::getShortText($model->name);
		$subtab = $this->getSubTab(Yii::app()->createUrl('projects/view', array('id' => $id)));
		$searchArray = isset($_GET['Projects']) ? $_GET['Projects'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/projects/view/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('projects/view', 'id'=>$id),
							'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->name : ''),
							'subtab' =>  $subtab,
							'order' => Utils::getMenuOrder()+1,
							'getChecklist' => $searchArray,
							'getIssues' => $searchArray			
					)
				)
		))));
		$year=date('Y');
		$ps_list = Yii::app()->db->createCommand("select count(rate) as count, sr.id_project , sent_to as last , sent_to_first_name as first, ss.surveys_submitted_date as response_date , sr.surv_type ,sr.rate   
from surveys_results sr , surveys_status ss where sr.rate<>0 and sr.id_project= ss.id_project and ss.surveys_submitted='1' and sr.surv_type=ss.surv_type and sr.id_project =".$id." and YEAR(ss.surveys_sent_date)='".$year."' group by sr.rate ,sr.id_project , sent_to , sent_to_first_name , ss.surveys_submitted_date, sr.surv_type  order by sr.rate desc")->queryAll();
		if($ps_list == null){
			$ps_list = 'empty';
		}
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = $subtab;
		$this->render('view',array(
			'model'=>$model,
			'active' => $active,
			'ps_list'=>$ps_list
		));
	}
	public function actionsendProjectsSurveyReminders(){
		$notif = EmailNotifications::getNotificationByUniqueName('projects_surveys_reminder');
		$closenotsent=Projects::getProjectsProjectSurveyNotSent('close');
		$intermediatenotsent=Projects::getProjectsProjectSurveyNotSent('intermediate');
		$closenotsubmitted=Projects::getProjectsProjectSurveyNotSubmitted('close');
		$intermediatenotubmitted=Projects::getProjectsProjectSurveyNotSubmitted('intermediate');
		if ($notif != NULL){
			$subject = $notif['name'];				
			$to_replace = array(
					'{closenotsent}',
					'{intermediatenotsent}',
					'{closenotsubmitted}',
					'{intermediatenotubmitted}'					
			);
			$replace = array(
					$closenotsent,
					$intermediatenotsent,
					$closenotsubmitted,
					$intermediatenotubmitted					
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email){
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
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
	public function actionsendSurvey(){	
	if(isset($_POST['id_project']))	{$id=$_POST['id_project'];	}			
		$project_name=Projects::getNameById($id);
		$checkprojectstatus = Projects::getProjectStatus($id);
			if($checkprojectstatus=="2"){
				$checksurvstatus = Projects::checkSurveyStatus($id ,'close');
				$surv_type='close';
			}else{
				$checksurvstatus = Projects::checkSurveyStatus($id ,'intermediate');
				$surv_type='intermediate';
			}
		if(isset($_POST['send']) && $_POST['send']=='1' && $surv_type!='intermediate'){
				if(!isset($_POST['to']) || empty($_POST['to'])){
							echo CJSON::encode(array(
						'status'=>'failed',
						'message'=>"Please enter an Email Address."));	
						exit;
				}
				if(!isset($_POST['name'])|| empty($_POST['name'])){
						echo CJSON::encode(array(
						'status'=>'failed',
						'message'=>"Please enter a Last Name."));	
						exit;
				}
				if(!isset($_POST['fname'])|| empty($_POST['fname'])){
						echo CJSON::encode(array(
						'status'=>'failed',
						'message'=>"Please enter a First Name."));	
						exit;
				}
		 				$emailc=$_POST['to'];
	 					$firstname=$_POST['fname'];
	 					$lastname=$_POST['name'];
						$checkstatus = Yii::app()->db->createCommand("SELECT count(1) FROM surveys_status where id_project=".$id." and surveys_sent='1' and surv_type='".$surv_type."' ")->queryScalar();
						$p=Surveys::encrypt_url($id);						
						$url=Yii::app()->getBaseUrl();
						$link="<a href='http:://snsitnew.sns-emea.com/site/Surveys?p=".$p."'>Click Here</a> ";
						$notif = EmailNotifications::getNotificationByUniqueName('send_survey'); 
		    			$to_replace = array(
		    				'{firstname}',
		    				'{lastname}',
							'{project_name}'	,							
		    				'{link}'
						);
						$replace = array(
							$firstname ,
							$lastname ,
							$project_name	,
							$link
						);	
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->ClearBccs();
						Yii::app()->mailer->AddAddress($emailc);
						Yii::app()->mailer->AddBccs('claudine.daaboul@sns-emea.com');					
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if(Yii::app()->mailer->Send(true)){
							$updatesurverystatus= Yii::app()->db->createCommand("update projects set surveystatus='Sent' where id=".$id."")->execute();
							$survey_sent = Yii::app()->db->createCommand("insert into surveys_status (id_project,surveys_sent,surveys_sent_date , sent_to ,sent_to_first_name , sent_to_email ,surv_type) values (".$id.",'1',NOW(),'".$lastname."','".$firstname."','".$emailc."','".$surv_type."')")->execute();
									echo CJSON::encode(array(
										'status'=>'success'
										));
								}else{
										echo CJSON::encode(array(
										'status'=>'failed',
										'message'=>"For an unknown reason email has not been sent"));	
										exit;
								}
		}elseif (isset($_POST['send']) && $_POST['send']=='2') {
			if($checksurvstatus<'1'){					 
			$type=Projects::getProjectType($id);					
			$result =  Yii::app()->db->createCommand("SELECT id , question  from surveys_questions where type='".$type."' and surv_type='".$surv_type."' order by id asc ")->queryAll(); 
			$error=0;
				 foreach($result as $res){ 
					$q=$res['id'];
					 if(isset($_POST['comment-'.$q])){
					 	$comment=$_POST['comment-'.$q];
					 	$surveys_comment = Yii::app()->db->createCommand("insert into surveys_results (id_question, comments, id_project,surv_type) values (".$q.",'".$comment."',".$id.",'".$surv_type."')")->execute();
					}else{						
						if(isset($_POST['surv-rate-'.$q]) && !empty($_POST['surv-rate-'.$q])){
							$rate= $_POST['surv-rate-'.$q];						
							$surveys_results = Yii::app()->db->createCommand("insert into surveys_results (id_question, rate, id_project,surv_type) values (".$q.",".$rate.",".$id.",'".$surv_type."')")->execute();
							}else{
								$error=1;								
								}
						}
					}
					if($error==1){					
					 $delete_results = Yii::app()->db->createCommand("delete from surveys_results where id_project=".$id." and surv_type='".$surv_type."' ")->execute();
						echo CJSON::encode(array(
											'status'=>'failed',
											'message'=>"All the survey should be filled"));	
											exit;
					}
				}else {
						echo CJSON::encode(array(
										'status'=>'failed',
										'message'=>"Survey is already submitted"));	
										exit;
				}
						$surveys_submitted = Yii::app()->db->createCommand("update surveys_status set surveys_submitted='1' ,surveys_submitted_date=NOW() where id_project=".$id." ")->execute();
						$notif = EmailNotifications::getNotificationByUniqueName('submit_survey');  				
    					$pm= Projects::getProjectManagerEmail($id);
    					$bm= Projects::getBusinessManagerEmail($id);
    					$customer=Customers::getNameById(Projects::getCustomerByProject($id));
    					$SS=Surveys::getTotalRatebyProject($surv_type,$id,'5');
    					$S=Surveys::getTotalRatebyProject($surv_type,$id,'4');
    					$N=Surveys::getTotalRatebyProject($surv_type,$id,'3');
    					$U=Surveys::getTotalRatebyProject($surv_type,$id,'2');
    					$SU=Surveys::getTotalRatebyProject($surv_type,$id,'1');
    					$results="<div style='list-style-type:none'><br /><br /><img height='10' src='http://snsitnew.sns-emea.com/images/5stars.png' />&nbsp;&nbsp;".$SS."/10 - ".($SS*10)."% <br /><br /><img height='10' src='http://snsitnew.sns-emea.com/images/4stars.png' />&nbsp;&nbsp;".$S."/10 - ".($S*10)."% <br /><br /><img height='10' src='http://snsitnew.sns-emea.com/images/3stars.png' />&nbsp;&nbsp;".$N."/10 - ".($N*10)."% <br /><br /><img height='10' src='http://snsitnew.sns-emea.com/images/2stars.png' />&nbsp;&nbsp;".$U."/10 - ".($U*10)."% <br /><br /><img height='10' src='http://snsitnew.sns-emea.com/images/1stars.png' />&nbsp;&nbsp;".$SU."/10 - ".($SU*10)."% </div>";
		    			$to_replace = array(		    				
							'{project_name}',	
							'{customer}',
							'{surv_type}' ,
							'{results}'
						);
						$replace = array(						
							$project_name	,	
							$customer	,
							$surv_type ,
							$results
						);	
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->AddAddress($pm);
						Yii::app()->mailer->AddAddress($bm);						
							foreach($emails as $email){
								if (!empty($email))
								{	//Yii::app()->mailer->AddCCs($email);
									Yii::app()->mailer->AddAddress($email);
								}
							} 
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if(Yii::app()->mailer->Send(true)){
							$updatesurverystatus= Yii::app()->db->createCommand("update projects set surveystatus='Completed' where id=".$id."")->execute();
							echo CJSON::encode(array(
							'status'=>'success'));	
						}							
		}else{ 	
				$model = new ShareByForm;
				echo CJSON::encode(array(
						'status'=>'success',
						'div'=>$this->renderPartial('share', array('id_project'=>$id , 'model'=>$model), true)));
			}	
	}
	public function actionUpdate($id = null, $view = 0, $active = null){
		$id = (int) $id;
		$model = $this->loadModel($id);		
		if (!Yii::app()->request->isAjaxRequest) {		
			$arr = Utils::getShortText($model->name);
			$subtab = $this->getSubTab(Yii::app()->createUrl('projects/update', array('id' => $id)));
			if (isset(Yii::app()->session['menu']) && $view == 1) {
				Utils::closeTab(Yii::app()->createUrl('projects/view', array('id' => $id)));
				$this->action_menu = Yii::app()->session['menu'];
			}
			$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/projects/update/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('projects/update', 'id'=>$id),
							'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->name : ''),
							'subtab' =>  $subtab,
							'order' => Utils::getMenuOrder()+1
					)
				)
			))));
			Yii::app()->session['menu'] = $this->action_menu;
			$this->jsConfig->current['activeTab'] = $subtab;
		}		
		if (isset($_POST['Projects'])){
			$originalstatus=$model->status;
			$model->attributes = $_POST['Projects'];
			if ($model->id_type != '27'){
				$model->complexmodule = 'No';
			} 			
			Yii::app()->createUrl('projects/GetAlerts');
			if ($model->status=='2' && $originalstatus!='2') {
				$count=self::ValidateClosedonupdate($model->id, $model->id_type);				
				if ($count != 0  ){
					$this->sendNotificationsEmailsClosed('project_closed',$model->id);
					$this->sendLessonsProject($model->id);
					$model->closed_date= date('Y-m-d');
					$model->deactivate_alerts="Yes";
				}else{
					$model->status=='1';
				}
			}		
			if (isset($_POST['under_support']) && $_POST['under_support']=="Yes" && $model->under_support !="Yes"){ 
				$model->under_support = $_POST['under_support'];
				$model->transition_to_support_date = date('Y-m-d');
				$this->sendProjectTransitionSupport($model->id);
			}else if (isset($_POST['under_support']) && $_POST['under_support']=="No"){
				$model->under_support = $_POST['under_support'];
			}
			if  (isset($_POST['qa'])){
				$model->qa = $_POST['qa'];
			}
			if  (isset($_POST['deactivate_alerts'])){
				$model->deactivate_alerts = $_POST['deactivate_alerts'];
			}else if($model->status!='2'){
				$model->deactivate_alerts = "No";
			}
			if (isset($_POST['Projects']['complexnotes'])){ 
				$arr=$_POST['Projects']['complexnotes'];				
				$str='';
				foreach ($arr as $key => $ar) {
					$str.=$ar;
					if ($key<(sizeof($arr)-1)){
						$str.=',';
					}
				}
				$model->complexnotes =$str;
				$str='Complex modules "'.$str;
				$str.='" are included in this project';
				$countRisks = Yii::app()->db->createCommand("SELECT count(1) FROM projects_risks where risk='".$str."' and id_project=".$model->id." ")->queryScalar();
				if($countRisks == 0){
					$deleteRisk = Yii::app()->db->createCommand("delete from  projects_risks where id_project=".$model->id." and risk like 'Complex modules%are included in this project' ")->execute();
					$insert_offset = Yii::app()->db->createCommand("INSERT INTO projects_risks (id_project,risk,priority,planned_actions, responsibility, `status`, privacy) VALUES (".$model->id.",'".$str."',1,' ','SNS','Pending','Internal')")->execute();
				}				
			}
			if (isset($_POST['complexmodule'])){ 
				$model->complexmodule = $_POST['complexmodule'];
			}
			if ($model->complexmodule  == 'No' && $model->id_type == '27'){
					$deleteRisk = Yii::app()->db->createCommand("delete from  projects_risks where id_project=".$model->id." and risk like 'Complex modules%are included in this project' ")->execute();
			}
			if ($model->save()){ 
				if($model->id_type == '27' && (!empty($model->project_manager))  && (!empty($model->business_manager)) ){
					self::checksendMilestonesEmail($model->id);
				}
				echo json_encode(array_merge(array('status'=>'saved')));
				exit;
			}else{
				echo json_encode(array_merge(array('status'=>'failure', 'errors' => $model->getErrors())));
				exit;
			}
		}		
		if (!Yii::app()->request->isPostRequest) {
			$this->render('manage',array(
				'model'=>$model,
				'active' => $active,
			));
		}	
	}	
	public function actionCreateTasks(){
    	$model = new ProjectsTasks();
    	$model->attributes = $_POST['ProjectsTasks'];
    //	print_r($_POST['ProjectsTasks']);exit;
    	$model->existsfbr= $_POST['ProjectsTasks']['existsfbr'];
    	$model->parent_fbr= $_POST['ProjectsTasks']['parent_fbr'];
    	$model->description= $_POST['ProjectsTasks']['description'];
  		$model->id_project_phase = (int)$_POST['id_phase'];
        /*
        * Author: Mike
        * Date: 03.07.19
        * Change notes to short description and make the field wider + mandatory for FBR type tasks + put it on second row
        */

  		if ((int)$model->type === 1){
  		    $model->validatorList->add(CValidator::createValidator('required', $model, 'notes'));
        }
  		if($model->existsfbr !=2 || $model->type != 1 ){
  			$model->parent_fbr='';
  		}else if(!empty($model->parent_fbr)){
  			$model->parent_fbr= ProjectsTasks::getTaskIdByPTName($model->parent_fbr);
  		}
  		if($model->type == 1 || $model->type == 3 || $model->type == 4 || $model->type == 5)
  		{
  			$model->description = $model->fbr.'-'.$model->title;
  		}
        if(!empty($model->parent_fbr)){
            $progect = Yii::app()
                ->db->createCommand("SELECT p.name AS project_name,c.name AS castomer_name,c.primary_contact_email,
                                 (SELECT CONCAT(u.firstname,' ',u.username,' ',u.lastname) FROM users AS u WHERE u.id=p.project_manager) AS project_m,
                                 (SELECT CONCAT(u.firstname,' ',u.username,' ',u.lastname) FROM users AS u WHERE u.id=p.business_manager) AS business_m FROM projects AS p 
                                 JOIN customers AS c ON c.id=p.customer_id
                                WHERE p.id={$_POST['id_project']};")
                ->queryAll();
            self::sendNotificationsEmailsProjectSTask($model,$progect[0]);
        }
    	if ($model->save()) {


	  		echo json_encode(array('status'=>'saved',
	  				'totalDays'=>Projects::getDaysOf($_POST['id_project'],$_POST['id_phase']),
	  		));
	    } else {
	  		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors(),'filers' => $model->rules()));
	  	}    	
	  	exit;
    }

    /*
    * Author: Mike
    * Date: 19.07.19
    * Add email to be triggered and sent to the PM and Bernard when a task FBR is flagged as redundant
    */
    private static function sendNotificationsEmailsProjectSTask($data,$project){
        $body = <<<XER
            <p style="background: yellow;">Dear All,<br>Please find below a list of FBRs which were created as redundant for project</p>
            <table>
                  <thead>
                    <tr>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Project</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Customer</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">PM</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">BM</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">FBR #</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Title</span></th>                      
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Module</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Keywords</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Complexity</span></th>
                    </tr>
                  </thead>
                  <tbody>
            
XER;
        $complexity = [1 => 'Low',2 => 'Medium', 3 => 'High'];

        $body .= <<<XER

             <tr>
                <td style="padding: 5px;"><span style="background: yellow;">{$project['project_name']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$project['castomer_name']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$project['project_m']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$project['business_m']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$data->fbr}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$data->title}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;"></span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$data->keywords}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$complexity[(int)$data->complexity]}</span></td>
             <tr>

XER;



        $body .= '</tbody></table>';
        Yii::app()->mailer->ClearAddresses();
        Yii::app()->mailer->AddAddress('Bernard.Khazzaka@sns-emea.com');
        Yii::app()->mailer->Subject  = 'task FBR is flagged as redundant';
        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
        try{
            Yii::app()->mailer->Send(true);
            if(isset($project['primary_contact_email']) && !empty($project['primary_contact_email'])){
                Yii::app()->mailer->AddAddress($project['primary_contact_email']);
                Yii::app()->mailer->Send(true);
            }
        }catch (Exception $exception){
            CVarDumper::dump($exception,10,true);
        }
    }

	public function actionUpdateTasks($id=null){
    	if (isset($_POST['save'])){	
    	}else{
	    	$id = (int)$id;
	    	$model = ProjectsTasks::model()->findByPk($id);
	    	$model->attributes = $_POST['ProjectsTasks'];	  	
	    	$model->existsfbr= $_POST['ProjectsTasks']['existsfbr'];
	    	$model->parent_fbr= $_POST['ProjectsTasks']['parent_fbr'];
	    	$model->description= $_POST['ProjectsTasks']['description'];
	  		if($model->existsfbr !=2 || $model->type != 1){
	  			$model->parent_fbr='';
	  		}else if(!empty($model->parent_fbr)){
	  			$model->parent_fbr= ProjectsTasks::getTaskIdByPTName($model->parent_fbr);
	  		}
	  		if($model->type == 1 || $model->type == 3 || $model->type == 4 || $model->type == 5)
	  		{
	  			$model->description = $model->fbr.'-'.$model->title;
	  		}
	    	if (empty($model->description)){
				$model->addCustomError('description', $model->description.' cannot be blank');
			}
    		if (empty($model->billable)){
				$model->addCustomError('billable', $model->billable.' cannot be blank');
			}
		    if ($model->save())
		  		echo json_encode(array('status'=>'saved' ));
		  	else {		  		
		  		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		  	}
    	}
	  	exit;
    }
	public function actionManageTasks($id = NULL){
    	if (!isset($_POST['id_project']))
    		exit;    	
    	if ($id == NULL) {
    		$model = new ProjectsTasks();
  			$model->id_project_phase = (int)$_POST['id_phase'];
    	} else {
    		$id = (int)$id;
    		$model = ProjectsTasks::model()->findByPk($id);
    	}    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
		if($model->existsfbr ==2){	
			$model->parent_fbr= ProjectsTasks::getTPName($model->parent_fbr);
		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_task_form', array(
            	'model'=> $model,
    			'id_phase'=>$model->id_project_phase,
    			'id_project'=>$_POST['id_project'],
        	), true, true)));
       exit;
    }	
	public function actionUpdatelessons(){
		$id= $_POST['project'];
		$lessons= nl2br($_POST['lessons']);		
		if(!empty($lessons)){
			$count=  Yii::app()->db->createCommand("SELECT count(1) FROM projects_lessons where id_project=".$id." ")->queryScalar();
			if($count == 0){
				Yii::app()->db->createCommand( "insert into projects_lessons (id_project, lessons) values (".$id.", '".addslashes($lessons)."') ")->execute();
			}else{
				Yii::app()->db->createCommand( "update projects_lessons set lessons='".addslashes($lessons)."'  where id_project=".$id." ")->execute();			
			}
		}	
		echo json_encode(array('status'=>'success'));
		exit;
	}
	 public function actionChangePhase(){    	
		$id = (int)$_POST['id_project_phase'];
		$model = ProjectsPhases::model()->findByPk($id);
		$model->id = $_POST['id_project_phase'];	
		if(isset($_POST['value'])){
			 $value=$_POST['value'];
		}
		$id_project = Yii::app()->db->createCommand("SELECT id_project FROM projects_phases where id=".$id." ")->queryScalar();
		$validate_mandayphase = Yii::app()->db->createCommand("select ROUND(sum(man_days_budgeted),4) from projects_phases where id<>".$id." and id_project=".$id_project." ")->queryScalar();
		$totalbudgeted= Projects::getProjectTotalManDaysByProject($id_project); 		
		if( $validate_mandayphase+$value > $totalbudgeted && Projects::getTMstatus($id_project)<>'1'){
		echo json_encode(array('status'=>'failure', 
			'error'=>'The total MDs you specified on this page is greater than the budgeted Man Days for this project ('.$totalbudgeted .')',
			'form'=>$this->renderPartial('_footer_content', array(
	            	'model'=> $model,
	        	), true, true)));
		exit;
		}
		if ($_POST['field']==1) {
	    	$offsets= ProjectsPhases::getOffsetsByPhase($id);
	    	$model->man_days_budgeted = $value;
	    	$model->total_estimated = $value+$offsets;
	    }	    
	    if ($model->save()) {
	    	$estimatedMDs=Projects::getEstimatedMD($id_project);
	    	$inculdingOffsetMDS=Projects::getIncludingOffsetMD($id_project);

			echo json_encode(array_merge(array('status'=>'success' , 
				'total_estimated'=>$model->total_estimated ,'estimatedMDs'=>$estimatedMDs,'inculdingOffsetMDS'=>$inculdingOffsetMDS,
				'form'=>$this->renderPartial('_footer_content', array(
	            	'model'=> $model,
	        	), true, true)
				)));
			exit;
	    }
		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		exit;
    }
	public function actionChangeOffset(){    	
		$id = (int)$_POST['id_project_phase'];
		$model = ProjectsPhases::model()->findByPk($id);
		$model->id = $_POST['id_project_phase'];
		if ($_POST['field']==1 || $_POST['field']==3){
				if (empty($model->offset_sign)){
					echo json_encode(array('status'=>'failure', 'error'=>'You need to specify the offset sign first.'));
				exit;		
			}
	    } 
	     if($_POST['field']==3){
	     	$model->offset =$_POST['value'];
	     }
	      	if ($_POST['field']==2) {
	    	$model->offset_sign =$_POST['value'];
	    } 
	    if ($model->save()) {	    	
			echo json_encode(array_merge(array('status'=>'success')));
			exit;
	    }
		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		exit;
    }
    public function actionChangeTask()  {
    	$extra = array();
		$id = (int)$_POST['id_task'];
		$model = ProjectsTasks::model()->findByPk($id);
		$model->id_project_phase = $_POST['id_phase'];
		if ($_POST['field']==1)  {
	    	$model->man_days_budgeted = $_POST['value'];
	    } else {
	    	$model->billable = $_POST['value'];
	    }
	    if ($model->save())  {
	    	if ($_POST['field'] == 1){
	    		$extra['totalDays'] = Projects::getDaysOf($_POST['id_phase']);
	    	}
			echo json_encode(array_merge(array('status'=>'success'), $extra));
			exit;
	    }
		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		exit;
    }
	public function actionCreatePhase() {
    	if (!isset($_POST['ProjectsPhases'], $_POST['id_project']))
    		exit;
    	$model = new ProjectsPhases();
    	$model->attributes = $_POST['ProjectsPhases'];
  		$model->id_project = (int)$_POST['id_project'];
    	$model->total_estimated= $model->man_days_budgeted;    	
	    if (Yii::app()->request->isAjaxRequest){
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.yiigridview.js'] = false;
		}		
	    if ($model->save()){
	  		echo json_encode(array(
	  			'status'=>'saved',
	  			'id_phase' => $model->id,
        		'form2'=>$this->renderPartial('_body_phase_form', array(
	            	'model'=> $model,	    			
        		), true,true)
        	));
	    }else {
	  		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
	  	}		
	  	exit;
    }
	public function actionUpdatePhase($id = 0){
	    if (isset($_POST['cancel'])){
	    	$id = (int)$id;
    		$model = ProjectsPhases::model()->findByPk($id);
	    	echo json_encode(array(
	    		'status'=>'saved',
	    		'form'=>$this->renderPartial('_head_phase_form', array(
	            	'model'=> $model,
	    			'id_project'=>$_POST['id_project']
	        	), true, true))
	        );
	        exit;
		}
    	$id = (int)$id;
    	$model = ProjectsPhases::model()->findByPk($id);
		$validate_mandayphase = Yii::app()->db->createCommand("select sum(man_days_budgeted) from projects_phases where id<>".$model->id." and id_project=".$model->id_project." ")->queryScalar();
		$totalbudgeted= Projects::getBudgetedMD($model->id_project);
		if( ($validate_mandayphase+$_POST['ProjectsPhases']['man_days_budgeted']) > $totalbudgeted && Projects::getTMstatus($model->id_project)<>'1'){
		echo json_encode(array('status'=>'failure',
		 'message'=>'The total MDs you specified on this page is greater than the budgeted Man Days for this project ('.$totalbudgeted .')',
		 'form2'=>$this->renderPartial('_footer_content', array(
	            	'model'=> $model,
	        	), true, true))
		);
		exit;
		}
		$diff=0;
		if($_POST['ProjectsPhases']['man_days_budgeted']!=$model->man_days_budgeted){
			$diff=$_POST['ProjectsPhases']['man_days_budgeted'] - $model->man_days_budgeted;
		}
    	$model->attributes = $_POST['ProjectsPhases'];
    	$model->total_estimated=$model->total_estimated + $diff;
    	if (empty($model->description)){
			$model->addCustomError('description', $model->description.' cannot be blank');
		}
		if ($model->save())
	  		echo json_encode(array('status'=>'saved','form'=>$this->renderPartial('_head_phase_form', array(
            	'model'=> $model,
    			'id_project'=>$_POST['id_project']
        	), true, true)
	  	));
	  	else {
	  		echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
	  	}    	
	  	exit;
    }
	public function actionManagePhase($id = NULL){
    	if (!isset($_POST['id_project']))
    		exit;    	
    	if ($id == NULL) {
    		$model = new ProjectsPhases();
  			$model->id_project = (int)$_POST['id_project'];
    	} else {
    		$id = (int)$id;
    		$model = ProjectsPhases::model()->findByPk($id);
    	}    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_phase_form', array(
            	'model'=> $model,
    			'id_project'=>$model->id_project,
        	), true, true)));
       exit;
    } 
    public function actionManageIssueItem($id){		
		$model = ProjectsIssues::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['ProjectsIssues'])){
			$oldstatus= $model->status;
   			$model->attributes = $_POST['ProjectsIssues'];
   			if($model->status ==1 && $oldstatus !=1)
   			{$model->fixed_date=date('Y-m-d'); $model->fixed_by =Yii::app()->user->id; }
   			if($model->status ==2 && $oldstatus !=2 )
   			{$model->close_date=date('Y-m-d');}
   			$model->lastupdateby=Yii::app()->user->id;
   			$model->lastupdateddate=date('Y-m-d');
            $model->etd_date=date('Y-m-d',strtotime($_POST['ProjectsIssues']['etd_date']));
   			if ($model->save()){	

   				if(isset(Yii::app()->session['customers_conn'])){
   					$destination = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."projects".DIRECTORY_SEPARATOR.$model->id_project.DIRECTORY_SEPARATOR."issues".DIRECTORY_SEPARATOR.$model->id_issue.DIRECTORY_SEPARATOR;
					$src = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."projects".DIRECTORY_SEPARATOR.$model->id_project.DIRECTORY_SEPARATOR."issues".DIRECTORY_SEPARATOR.$model->id_issue.DIRECTORY_SEPARATOR;
					if ( !is_dir( $destination ) ) 
				 	{
			            mkdir( $destination, 0777, true);
			            chmod( $destination, 0777 );
			        }else
			        {
			        	if($model->file != null)
			        	unlink( $destination.$model->file );
			        }
					$model->file = Yii::app()->session['customers_conn'];
	                if( rename( $src.Yii::app()->session['customers_conn'], $destination.Yii::app()->session['customers_conn']) ) {
	                    chmod( $destination.Yii::app()->session['customers_conn'], 0777 );
	               }
	               $model->save();
   				}
   				unset(Yii::app()->session['customers_conn']);
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_issues_form', array(
		           	'model'=> $model
		        ), true, true)));
		        exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_issues_form', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}
	public function actionManageRiskItem($id){		
		$model = ProjectsRisks::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['ProjectsRisks'])){
   			$model->attributes = $_POST['ProjectsRisks'];   		
   			if ($model->save()){
					$model->save();					
					echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_risk_form', array(
		            	'model'=> $model
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_risk_form', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}	
	public function actionDeleteUploadIssueFile()
	{
		if (isset($_GET['model_id'], $_GET['file']))
		{
			$issue = (int)$_GET['model_id'];
			if (isset($_GET['id_project']))
			{
				$project = (int)$_GET['id_project'];
				$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.$project.DIRECTORY_SEPARATOR.'issues'.DIRECTORY_SEPARATOR.$issue.DIRECTORY_SEPARATOR;
				Utils::deleteFile($dirPath.$_GET['file']);
				$query = "UPDATE `projects_issues` SET file='' WHERE id_project= '$project' and id_issue='$issue'";
				Yii::app()->db->createCommand($query)->execute();
			}else {	unset(Yii::app()->session['customers_conn']); }			
		}
	}
	public function actionmanageScenarioItem($id){		
		$model = ProjectsScenarios::model()->findByPk((int)$id);		
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>false,
						'jquery.min.js' => false,
						'jquery-ui.min.js' => false,
					);		
		if (isset($_POST['ProjectsScenarios'])){
   			$model->attributes = $_POST['ProjectsScenarios'];   		
   			if ($model->save()){
					$model->save();			
					echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_scenario_form', array(
		            	'model'=> $model
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_scenario_form', array(
            	'model'=> $model
        	), true, true)));
        Yii::app()->end();
	}	
	public function actionvalueOffset(){
		$id_project = $_POST['id_project'];
		$id_phase = $_POST['phase'];
		$offset_number = $_POST['offset'];
		$exists = Yii::app()->db->createCommand("SELECT count(1) FROM log_offsets WHERE id_project = ".$id_project." AND id_phase = ".$id_phase." ")->queryScalar();
		if($exists>0){
			$comm=Yii::app()->db->createCommand(" UPDATE log_offsets SET offset_number = ".$offset_number." WHERE  id_project = ".$id_project." AND id_phase = ".$id_phase." ")->execute();
		}else{
			$comm=Yii::app()->db->createCommand("INSERT INTO log_offsets (id_project,id_phase,offset_number) VALUES (".$id_project.",".$id_phase.",".$offset_number." ) ")->execute();
		}
	}
	public function actiondeleteOffsets(){
		$id_project = $_POST['id_project']; 
		$comm=Yii::app()->db->createCommand("delete from log_offsets where id_project= ".$id_project." ")->execute();
		echo CJSON::encode(array(
	  				'status'=>'success'	  				
	 			));
	 			exit;
	}
	public function actioncloseOffsets(){
		$id_project = $_POST['id_project']; 
		$offsetsAlerts = Yii::app()->db->createCommand("SELECT count(1) FROM log_offsets WHERE id_project = ".$id_project." AND (id_phase is null or reason is null or offset_number is null) ")->queryScalar();
		if ($offsetsAlerts>0){
			$error='';
			$offsetsErrors = Yii::app()->db->createCommand("SELECT distinct(id_phase) FROM log_offsets WHERE id_project = ".$id_project." AND ( reason is null or reason=' ' or reason='') ")->queryAll();
			$error.='Offset Reason cannot be empty for phase(s):<br/>';
			foreach ($offsetsErrors as $offsetsError) {
				$error.='- '.ProjectsPhases::getPhaseDescByPhaseId($offsetsError['id_phase']).'<br/>';
			}
			echo CJSON::encode(array(
	  				'status'=>'fail',
	  				'message' => $error,
	 			));
	 			exit;
		}	
		$offsets = Yii::app()->db->createCommand("SELECT * FROM log_offsets WHERE id_project = ".$id_project." AND id_phase is not null and reason is not null and offset_number is not null ")->queryAll();
		$returnstr='';
		foreach ($offsets as $offset) {
			$returnstr.=self::setreason($id_project, $offset['id_phase'],$offset['reason'] ,$offset['offset_number'],$offset['sign']);
		}
		$id_phases = Yii::app()->db->createCommand("SELECT DISTINCT (id_phase) FROM log_offsets WHERE id_project = ".$id_project."")->queryColumn();
		$comm=Yii::app()->db->createCommand("delete from log_offsets where id_project= ".$id_project." ")->execute();
		echo CJSON::encode(array(
	  				'status'=>'success',
	  				'id_phases' => $id_phases,
	 			));
	 			exit;
	}
	public function actionupdateFooter(){
		if (isset($_POST['id_project'])){ 
			$proj=$_POST['id_project']; 
			$tot= Projects::getIncludingOffsetMD($proj);                       
			$offs= Projects::getTotalOffsetMD($proj); 
			$reqs=  Projects::getTotalOffset($proj);
			echo CJSON::encode(array( 
	  				'inculdingOffsetMDS'=> $tot,
	  				'offsetMDS' => $offs,
	  				'requestsoffs' => $reqs,
	 			));
		}else{
			echo CJSON::encode(array(
	  				'inculdingOffsetMDS'=> 0,
	  				'offsetMDS' => 0,
	  				'requestsoffs' => 0,
	 			));
		}
	}
	public function actiongetestimated(){
		if (isset($_POST['phase'])){
			$phase = $_POST['phase']; 
			$value = ProjectsPhases::getIncludingOffsetMDByPhase($phase);			  
			echo CJSON::encode(array(
	  				'estimatedMDs'=>$value,
	 			));			 
		}else{
			echo CJSON::encode(array(
	  				'estimatedMDs' => 0,	  				
	 			));
		}		
	}
	public function actionreasonOffset(){
		$id_project = $_POST['id_project'];
		$id_phase = $_POST['phase'];
		$reason = $_POST['reason']; 
		$exists = Yii::app()->db->createCommand("SELECT count(1) FROM log_offsets WHERE id_project = ".$id_project." AND id_phase = ".$id_phase." ")->queryScalar();
		if($exists>0){
			$comm=Yii::app()->db->createCommand(" UPDATE log_offsets SET reason = '".$reason."' WHERE  id_project = ".$id_project." AND id_phase = ".$id_phase." ")->execute();
		}else	{
			$comm=Yii::app()->db->createCommand("INSERT INTO log_offsets (id_project,id_phase,reason) VALUES (".$id_project.",".$id_phase.",'".$reason."' ) ")->execute();
		}
	}
	public function actionsignOffset(){
		$id_project = $_POST['id_project'];
		$id_phase = $_POST['phase'];
		$sign = $_POST['sign'];
		$exists = Yii::app()->db->createCommand("SELECT count(1) FROM log_offsets WHERE id_project = ".$id_project." AND id_phase = ".$id_phase." ")->queryScalar();
		if($exists>0){
			$comm=Yii::app()->db->createCommand(" UPDATE log_offsets SET sign = '".$sign."' WHERE  id_project = ".$id_project." AND id_phase = ".$id_phase." ")->execute();
		}else{
			$comm=Yii::app()->db->createCommand("INSERT INTO log_offsets (id_project,id_phase,sign) VALUES (".$id_project.",".$id_phase.",'".$sign."' ) ")->execute();
		}
	}
	public function actionSetOffsetReason(){		
		if (isset($_POST['id_project'])){
			$phases = ProjectsTasks::getPhasesIdByTasks($_POST['checktask']); 
			$id_project = $_POST['id_project'];
			$rate_table="<table id=\"inputratetable\"> <tr><th>Phase</th><th>Sign*</th><th style=\"width:50px !important;\">Offset*</th><th style=\"width:220px !important;\">Reason*</th></tr>";	
			foreach ($phases as $key => $phase) {				 
				$rate_table.="<tr><td>".$phase."</td><td id=\"sign_".$key."\" onchange=\"updateOffsetSign(this.id,event)\">".ProjectsTasks::getSign()."</td><td><input type=text style=\"width:50px !important;\" id=\"offset_".$key."\" onKeyUp=\"updateOffset(this.id,event)\" pattern=\"[0-9]+([,\.][0-9]+)?\"></td><td><input type=text style=\"width:220px !important;\" id=\"reason_".$key."\" onKeyUp=\"updateReason(this.id,event)\" pattern=\"[0-9]+([,\.][0-9]+)?\"></td></tr>";
			}			
			$rate_table.="<tr><td><label id='warn_label'></label></td></tr></table>";				
			echo json_encode(array(
											"status"=>"success",
											'rate_table'=>$rate_table,
											'id_project'=>$id_project,
										));
									exit;	
			}else{
 						
					echo json_encode(array(	"status"=>"failure"));
										exit;	

			}
	}
	public function actiongetProjectInfo(){		
		$id_project = $_POST['id_project'];	
		$name=Projects::getNameById($id_project);	 	
				echo CJSON::encode(array(
						'status'=>'success', 'div'=>$name));
	}
	public function setreason($id_project, $id_phase, $reason,$offset, $offsetsign){
		$project_name=Projects::getNameById($id_project);
		$phase_name=ProjectsPhases::getPhaseDescByPhaseId($id_phase);
 		$model = ProjectsPhases::model()->findByPk($id_phase);
		$model->id = $id_phase;			
		$insert_offset = Yii::app()->db->createCommand("INSERT INTO projects_phases_offsets (id_project_phase, offset,offset_sign,offset_reason) VALUES ('".$id_phase."','".$offset."','".$offsetsign."','".$reason."')")->execute();
		if($offsetsign=='Minus'){
					$offsetsign='-';
			        $model->total_estimated =$model->total_estimated-$offset;
			}else{
					 $offsetsign='+';
					 $model->total_estimated =$model->total_estimated+$offset;
					}
		$offset=$offsetsign.''.$offset;	  	
	  	$model->save();
		$eas = Projects::getSEas($id_project);
	   	$newrate=Utils::formatNumber(Projects::getProjectNetAmountWithoutExpenses($eas)/Projects::getIncludingOffsetMD($id_project),2);
	   	$budgetedrate= Utils::formatNumber(Projects::getProjectNetAmountWithoutExpenses($eas)/Projects::getBudgetedMD($id_project),2);
		$pm_email=Projects::getProjectManagerEmail($id_project);
		$bm_email=Projects::getBusinessManagerEmail($id_project);
		$customer_name= Customers::getNameById(Projects::getCustomerByProject($id_project));
		$budgetedbyphase=Projects::getEstimatedMDbyPhase($id_phase);
		if($budgetedbyphase==0){$budgetedbyphase=1;}
		$actualmdbyphase=Projects::getActualDaysOf($id_phase);
		$overrun= (($budgetedbyphase-$actualmdbyphase)/ $budgetedbyphase)*100;
		$requested=Projects::getTotalOffset($id_project);
		$offindays=Projects::getTotalOffsetMD($id_project);
		$budgetPro=Projects::getBudgetedMD($id_project);
		$userid=Yii::app()->user->id;
		$actualmds= round(Projects::getActualMD($id_project), 2);
		$actualmdsrate=Utils::formatNumber(Projects::getProjectActualRateUSD($id_project,$eas)).'$';
		$username= Users::getNamebyId($userid);
		$notif = EmailNotifications::getNotificationByUniqueName('offset_reason');	
		    			$to_replace = array(
							'{project_name}',
							'{phase_name}',
							'{reason}',
							'{createdby}',
							'{offset}',	
							'{newrate}',
							'{budgetedrate}',
							'{customer_name}',
							'{budgetedbyphase}',
							'{actualmdbyphase}',
							'{overrun}',
							'{requested}',
							'{offindays}',
							'{budgetPro}',
							'{actualmds}',
							'{actualmdsrate}'
						);
						$replace = array(
							$project_name	,
							$phase_name,
							$reason,
							$username,
							$offset,
							$newrate ,
							$budgetedrate,
							$customer_name,
							$budgetedbyphase,
							$actualmdbyphase,
							$overrun,
							$requested,
							$offindays,
							$budgetPro,
							$actualmds,
							$actualmdsrate
						);	
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->AddAddress($pm_email);
						//Yii::app()->mailer->AddCCs($bm_email);
						Yii::app()->mailer->AddAddress($bm_email);
							foreach($emails as $email) {
								if (!empty($email))
								{
									//	Yii::app()->mailer->AddCCs($email);
									Yii::app()->mailer->AddAddress($email);
								}
							}
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send();
					$inculdingOffsetMDS=Projects::getIncludingOffsetMD($id_project);
					return $id_phase.'-'.$model->total_estimated.'-'.$inculdingOffsetMDS; 					
	}	
	public function actionGetUnssignedUsersIssues(){
		//print_r($_POST['checkissue']);exit;
		if (isset($_POST['checkissue'])){
			$users = ProjectsTasks::getAllUsers();	
				echo CJSON::encode(array(
						'status'=>'success',
						'div'=>$this->renderPartial('_unassigned_users_issues', array('users'=>$users), true)));
		}else{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one issue!',
			));
		}
		exit;
	}
	public function actionGetUnssignedUsers(){
		$message = "Please ensure that the following info is entered:<br />";
		$i=1;
		$error = 0;
		$id_project = $_POST['id_project'];
		if (isset($_POST['checktask'])){
			$complex = Yii::app()->db->createCommand("SELECT count(1) FROM projects where (complexmodule is null or complexmodule='') and id ='$id_project' and id_type=27 ")->queryScalar();
			if ($complex > 0){
					$message .=$i.'- Complex Module is defined<br />';
					$error = 1; $i++;
			}
			$complex2 = Yii::app()->db->createCommand("SELECT count(1) FROM projects where complexmodule='Yes' and complexnotes is null and id ='$id_project' and id_type=27 ")->queryScalar();
			if ($complex2>0){
					$message .=$i.'- Complex Module Values are defined<br />';
					$error = 1;$i++;
			}
			$uid = Yii::app()->db->createCommand("SELECT id FROM projects_milestones WHERE (estimated_date_of_completion = '0000-00-00' OR estimated_date_of_start = '0000-00-00') and id_project ='$id_project' and  applicable='Yes' LIMIT 1")->queryScalar();
				if ($uid != null){
					$message .=$i.'- Milestones Start/End Dates are defined<br />';
					$error = 1;$i++;
				}
				$applic = Yii::app()->db->createCommand("SELECT id FROM projects_milestones WHERE (applicable is null or applicable='') and id_project ='$id_project' LIMIT 1")->queryScalar();
				if ($applic != null){
					$message .=$i.'- All Milestones Applicable field is defined<br />';
					$error = 1;$i++;
				}
			$project_man = Yii::app()->db->createCommand("SELECT project_manager FROM projects WHERE id = '$id_project' LIMIT 1")->queryScalar();
				if ($project_man == null){
					$message .=$i.'- Project Manager is defined<br />';
					$error = 1;$i++;
				}
			$business_manager = Yii::app()->db->createCommand("SELECT business_manager FROM projects WHERE id = '$id_project' LIMIT 1")->queryScalar();
				if ($business_manager == null){
					$message .=$i.'- Business Manager is defined<br />';
					$error = 1;$i++;
				}
			$fields = Yii::app()->db->createCommand("SELECT  count(1) FROM projects WHERE id = '$id_project' and id_type=27 and template in (1,4,6) and (product is null or product='' or version is null or version='') LIMIT 1")->queryScalar();
				if  ($fields > 0){
					$message .=$i.'- Version and Product are defined<br />';
					$error = 1;$i++;
				}

				$tasks = array();
				$tasks=$_POST['checktask'];
				$stasks = implode(',', $tasks);
				if (!empty($tasks)){
				$id_phases = Yii::app()->db->createCommand("SELECT DISTINCT id_project_phase FROM projects_tasks WHERE id IN ( $stasks )")->queryColumn();
				}			
			$TM=Projects::getTMstatus($id_project);
			$customer_id=Projects::getCustomerByProject($id_project);
			$oneerror=true;
			foreach ($id_phases as $key => $value) {
		 	 	while ( $oneerror) {		 		
			 	$man_days_budgeted = Yii::app()->db->createCommand("SELECT description, man_days_budgeted , total_estimated from projects_phases where id=$value")->queryRow();
				if($man_days_budgeted['man_days_budgeted']==0  && $TM<>'1' && $customer_id<>'177' && $man_days_budgeted['total_estimated']==0 ){
			 			$message .=$i.'- Estimated MDs per phase is updated<br />';
			 			$oneerror=false;$i++;
						$error = 1;						
			 		}else{
			 			$oneerror=false;
			 		}
			 	}
		 	}
		 	$total_estimated=Yii::app()->db->createCommand("SELECT sum(man_days_budgeted) from projects_phases where id_project=".$id_project." ")->queryScalar();
		 	$total_estimated=round($total_estimated, 2);
		 	$budgeted_p=round(Projects::getProjectTotalManDaysByProject($id_project), 2);
		 	if(	$total_estimated <> $budgeted_p  && $TM<>'1'  && $customer_id<>'177'){
					$message .=$i.'- Total Estimated MDs is equal to Total Budgeted MDs<br />';
					$error = 1;$i++;
			}
			if ($error == 1){
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => $message,
				));
			}else{
				$users = ProjectsTasks::getAllUsers();	
				echo CJSON::encode(array(
						'status'=>'success',
						'div'=>$this->renderPartial('_unassigned_users', array('users'=>$users), true)));
			}
		}else{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one task!',
			));
		}
		exit;
	}
	
	public function actionGetAssignedUsersIssues(){
		if (isset($_POST['checkissue'])){
			$bring_user = $_POST['checkissue'];
			$nr = "";
			foreach ($bring_user as $bu){
				$nr = $nr.','.$bu;
			}
			$users = ProjectsIssues::getAsignUsers(substr($nr,1));			
			echo CJSON::encode(array(
					'status'=>'success',
					'div'=>$this->renderPartial('_assign_users_issues', array('users'=>$users), true)));
		}else{
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one task!',
			));
		}
		exit;
	}
	public function actionGetAssignedUsers(){
		if (isset($_POST['checktask'])){
			$bring_user = $_POST['checktask'];
			$nr = "";
			foreach ($bring_user as $bu){
				$nr = $nr.','.$bu;
			}
			$users = ProjectsTasks::getAsignUsers(substr($nr,1));			
			echo CJSON::encode(array(
					'status'=>'success',
					'div'=>$this->renderPartial('_assign_users', array('users'=>$users), true)));
		}else{
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one task!',
			));
		}
		exit;
	}
	public function actionValidateOpenIssues(){
		$id_project = $_POST['selected'];
		$valid=1;
		$type=Projects::getProjectType($id_project);
		if ($type == 27) {
				$Issuesnotcompleted= ProjectsIssues::checkOpenIssues($id_project);
				if ($Issuesnotcompleted>0){
						$valid=0; //error
				}
		}	
		$returnArr['valid'] = $valid;		
		echo json_encode($returnArr);
	}
	public function actionValidateOpenChecklist(){	
		$id_project = $_POST['selected'];
		$valid=1;
		$type=Projects::getProjectType($id_project);
		if ($type == 27) {
				$Checklistnotcompleted= ProjectsChecklist::checkPhasesStatus($id_project);
				if ($Checklistnotcompleted>0){
						$valid=0; 
				}
		}		
		$returnArr['valid'] = $valid;		
		echo json_encode($returnArr);
	}
	public static function ValidateClosedonupdate($id_project, $type){ 
			$status_milestones = Yii::app()->db->createCommand("SELECT id, status FROM projects_milestones WHERE id_project=".$id_project." and applicable = 'Yes' ")->queryAll();
			$valid=1;			
			foreach ($status_milestones as $key => $ts){	
				if ($ts['status']!="Closed") {
				$valid=0;
				}				
			}
				$result=Yii::app()->db->createCommand("SELECT pa.alerts as alerts FROM projects_alerts pa 
				where pa.id_project=".$id_project."")->queryScalar();
				if($result!=false){
					$alerts= array();
					$alerts= explode(',',$result);					
					foreach ($alerts as &$value) {
						$value=((int)$value);
						$useralerts= Yii::app()->db->createCommand("SELECT count(1) FROM alerts a 
								where a.id=".$value." AND a.user_control=1")->queryScalar();
						if($useralerts[0]=="1"){
							$valid=0;
						}
					}
				}
		$getnotapproved = Yii::app()->db->createCommand("select count(1) from user_time where 
											`default`=0 
											and amount>0 
											and `status`<>'1' 
											and id_task in (
												select pt.id from projects_tasks pt , projects_phases pp where 
														pt.id_project_phase=pp.id
														and pp.id_project='".$id_project."'
											) ")->queryScalar();

		if ($type == 27) {
				$Checklistnotcompleted= ProjectsChecklist::checkPhasesStatus($id_project);
				if ($Checklistnotcompleted>0){
						$valid=0; 
				}
		}
		if ($getnotapproved>0) {
				$valid=0; 
		}
		return $valid;
	}
	public function actionValidateMilestones(){ 
		$id_project = $_POST['selected'];
			$status_milestones = Yii::app()->db->createCommand("SELECT id, status FROM projects_milestones WHERE id_project=".$id_project." and (applicable is null or applicable='Yes')")->queryAll();
			$valid=1;			
			foreach ($status_milestones as $key => $ts){	
				if ($ts['status']!="Closed") {
				$valid=0;
				}				
			}			
			$returnArr['valid'] = $valid;
			echo json_encode($returnArr);
	}
	public function actionValidateTimesheet(){	
		$id_project = $_POST['selected'];
		$valid=1;
		$getnotapproved = Yii::app()->db->createCommand("select count(1) from user_time where 
											`default`=0 
											and amount>0 
											and `status`<>'1' 
											and id_task in (
												select pt.id from projects_tasks pt , projects_phases pp where 
														pt.id_project_phase=pp.id
														and pp.id_project='".$id_project."'
											) ")->queryScalar();
		if ($getnotapproved>0) {	$valid=0; 	}		
		$returnArr['valid'] = $valid;		
		echo json_encode($returnArr);
	}	
	public function actionValidateTimesheetGetNames(){
		$id_project = $_POST['selected'];
		$valid=1;
		$getnotapproved = Yii::app()->db->createCommand("select  distinct(id_timesheet),id_user,status from user_time where 
											`default`=0 
											and amount>0 
											and `status`<>'1' 
											and id_task in (
												select pt.id from projects_tasks pt , projects_phases pp where 
														pt.id_project_phase=pp.id
														and pp.id_project='".$id_project."'
											) ")->queryAll();

		$message='';
		if (!empty($getnotapproved)){
				$valid=0; 
				$message='Cannot close project with pending timesheet tasks:';
				foreach ($getnotapproved as $key ) {
					$name=Users::getNameById($key['id_user']);
					$alertm="";
					$timesheetStat= Timesheets::getStatusFunc($key['id_timesheet']);
					if(($key['status']== 0 || $key['status']== -1) && ($timesheetStat =='New' || $timesheetStat =='Rejected')){
						$alertm=" needs to be Submitted.";
					}else{
						$alertm=" needs to be Approved.";
					}
					$message.='<br/>'.$name.' Timesheet#'.$key['id_timesheet'].' '.$alertm;	
				}
		}		
		$returnArr['valid'] = $valid;
		$returnArr['message'] = $message; 	
		echo json_encode($returnArr);
	}
public static function sendProjectTransitionSupport($id_project){
					$notif = EmailNotifications::getNotificationByUniqueName('Project_Transition_Support');
					$id_project = (int) $id_project;
    				$project_name=Projects::getNameById($id_project);
		    			$to_replace = array(
							'{project_name}'
						);
						$replace = array(
							$project_name							
						);					
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
						foreach($emails as $email) {
								if (!empty($email))
									Yii::app()->mailer->AddAddress($emails);
							}
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send();	
}
public static function actionundocumentsTasksReminder(){
	$part = array();
	$notif = EmailNotifications::getNotificationByUniqueName('undocumented_summary');
	if ($notif != NULL && !EmailNotifications::isNotificationPAlertSent(1 , 'UndocumentedTasksMonthly', $notif['id'])) 
	{
		$to_replace = array('{body}',);
		
		$projects= Yii::app()->db->createCommand("SELECT p.name,p.id,p.project_manager, p.business_manager, p.customer_id,u.id_task from undocumented_tasks u, projects p where u.flag=0 and u.id_project= p.id")->queryAll();
 		if(!empty($projects)){ 
			$emailstr="Dear All, <br /><br />Kindly find below a list of projects with undocumented tasks:<br/>";
			$emailstr.="<br /><table border='1'  style='font-family:Calibri;' ><tr><th>Project</th><th>Customer</th><th>PM</th><th>BM</th><th>Phase</th><th>Task</th></tr>";
			foreach($projects as $project){					
				$emailstr.="<tr><td>".$project['name']."</td> <td>".Customers::getNamebyId($project['customer_id'])."</td> <td>".Users::getNameById($project['project_manager'])."</td> <td>".Users::getNameById($project['business_manager'])."</td> <td>".ProjectsTasks::getPhaseDescByid($project['id_task'])."</td>   <td>".ProjectsTasks::getTaskDescByid($project['id_task'])."</td> </tr>";
			}
			$emailstr.="</table><br /> Best Regards,<br />SNSit";
			$replace = array(
				$emailstr,	
			);
			$subject= $notif['name'];
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email)	{
					Yii::app()->mailer->AddAddress($email);
			}
		//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
			Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){
						EmailNotifications::saveSentNotification(1 , 'UndocumentedTasksMonthly', $notif['id']);
			}
		}
	}
}
public static function actiondeleteEmptyIssues()
{
	Yii::app()->db->createCommand("delete from projects_issues where trim(description)='' or trim(description)='Descr:' or trim(description)='Issue:' ")
								->execute();
}
public static function actionfbrsSummary()
{
	$part = array();
	$notif = EmailNotifications::getNotificationByUniqueName('fbrs_summary');
	if ($notif != NULL && !EmailNotifications::isNotificationPAlertSent(1 , 'ProjectFBRs', $notif['id'])) 
	{
		$to_replace = array('{body}',);
		
		$projects= Yii::app()->db->createCommand("SELECT p.name,p.id,p.project_manager, p.business_manager, p.customer_id from projects_tasks pt,projects_phases pp, projects p where  pt.id_project_phase= pp.id and pp.id_project = p.id  and p.status=1 and type =1 and existsfbr =1 group by p.id")->queryAll();
 		if(!empty($projects)){ 
			$emailstr="Dear All, <br /><br />Kindly find below a list of active project with <b>Not Checked</b> FBRs:<br/>";
			$emailstr.='<br /><table  border="1"  style="font-family:Calibri;border-collapse: collapse;" >';
			 $emailstr.="<tr><th colspan='4' border-collapse:collapse; style='border: 0px solid #FFFFFF;'></th><th colspan='4'>FBR Status</th><th border-collapse:collapse; style='border: 0px solid #FFFFFF;'></th></tr>    <tr><th>Project</th><th>Customer</th><th>PM</th><th>BM</th><th>FBRs</th><th>Not Checked</th><th>Redundant</th><th>New</th><th>Oldest Not Checked FBR Date</th></tr>";
			
			Yii::app()->mailer->ClearAddresses();

			foreach($projects as $project){					
				$emailstr.="<tr><td>".$project['name']."</td> <td>".Customers::getNamebyId($project['customer_id'])."</td> <td>".Users::getNameById($project['project_manager'])."</td> <td>".Users::getNameById($project['business_manager'])."</td> <td style='text-align:center;'>".ProjectsTasks::getFBRsTot($project['id'])."</td>   <td  style='text-align:center;'>".ProjectsTasks::checkFBRExistsStat($project['id'],1)."</td>   <td style='text-align:center;'> ".ProjectsTasks::checkFBRExistsStat($project['id'],2)."</td>  <td style='text-align:center;'>".ProjectsTasks::checkFBRExistsStat($project['id'],3)."</td>  <td style='text-align:center;'>".ProjectsTasks::checkFBRLatestUnchecked($project['id'])."</td>  </tr>";
				$pmemail= Users::getEmailbyID($project['project_manager']);
				
				if(!empty($pmemail))
				{
					Yii::app()->mailer->AddAddress($pmemail);
				}
			}
			$emailstr.="</table><br /> Best Regards,<br />SNSit";
			$replace = array(
				$emailstr,	
			);
			$subject= $notif['name'];
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			foreach($emails as $email)	{
					Yii::app()->mailer->AddAddress($email);
			}
		//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
			Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){
						EmailNotifications::saveSentNotification(1 , 'ProjectFBRs', $notif['id']);
			}
		}	
	}	
}
public static function actiongoLiveRemider()
{
	$part = array();
	$notif = EmailNotifications::getNotificationByUniqueName('golive_reminder');
	if ($notif != NULL){
		$to_replace = array('{body}',);
		
		$projects= Yii::app()->db->createCommand("SELECT id_project, DATE_FORMAT(estimated_date_of_start,'%d/%m/%Y') as estimated_date_of_start FROM `projects_milestones` where id_milestone=8 and estimated_date_of_start between current_date() and current_date()+interval 1 MONTH and id_project in (select id from projects where status=1)")->queryAll();
 		if(!empty($projects)){ 
			Yii::app()->mailer->ClearAddresses();
			$emailstr="Dears, <br /><br />Kindly find below the list of SW projects with a Go-Live scheduled in the coming month:<br/>";
			$emailstr.='<br /><table  border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th width="150">Customer</th><th width="300">Project</th><th>Scheduled Go-Live Date</th><th width="100">BM</th><th width="100">PM</th><th>Risks</th><th>Pending Amount($)</th></tr>';
			foreach($projects as $project){	
				$pm=Projects::getProjectManager($project['id_project']);
				$bm=Projects::getBusinessManager($project['id_project']);
				$risks=ProjectsRisks::getRisksStr($project['id_project']);
				$customer=Projects::getCustomerByProject($project['id_project']);
				$amt= Receivables::gettotalnotpaidandAll($customer);
				if(empty($amt) || $amt<0 || $amt==null || !isset($amt)) { $amt='';	}else {	$amt=Utils::formatNumber($amt,2); }
				$emailstr.="<tr><td>".Customers::getNamebyId($customer)."</td> <td>".Projects::getNameById($project['id_project'])."</td> <td>".$project['estimated_date_of_start']."</td><td>".$bm."</td> <td>".$pm."</td> <td>".$risks."</td>   <td style= 'text-align:right;'>".$amt."</td> </tr>";
				
				$emailPM= Projects::getProjectManagerEmail($project['id_project']);
					if (!empty($emailPM)){
						Yii::app()->mailer->AddAddress($emailPM);
					}
					$emailBM= Projects::getBusinessManagerEmail($project['id_project']);
					if (!empty($emailBM)){
						Yii::app()->mailer->AddAddress($emailBM);
					}

			}
			$emailstr.="</table><br /> Best Regards,<br />SNSit";
			$replace = array(
				$emailstr,	
			);
			$subject= $notif['name'];
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			foreach($emails as $email)	{
					Yii::app()->mailer->AddAddress($email);
			}
		//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
			Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);	
		}	
	}	
}
public static function actionsendIssuesSSummary()
{
	$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('issues_weekly');
		if ($notif != NULL){
			$to_replace = array(
				
				'{body}',
			);
			$issue = '';	$i = 1;	$total='';	$total2='';	$mon=date('m');
			$projects= Yii::app()->db->createCommand("SELECT p.id,p.name FROM projects p where p.status=1 and (select count(1) from projects_issues pi where pi.id_project=p.id and pi.status=0) >0")->queryAll();
 		
			if(!empty($projects)){ 
				foreach($projects as $project){		
					$emailstr="Dears, <br /><br />Below is a weekly summary of ".$project['name']." issues:<br/><br/>";
					
					$models= ProjectsIssues::getIssuesPendingorFixed($project['id']);
 					$emailstr.="<table border='1'  style='font-family:Calibri;' ><tr><th>Issue#</th><th>Description</th><th>Priority</th><th>Status</th><th>Type</th><th>FBR</th><th>Status</th><th>Assigned To</th><th>Logged By</th><th>Logged Date</th></tr>";
					foreach($models as $model){	
						$descr =str_replace('Descr:', '', $model['description']);
						$descr =str_replace('Issue:', '', $descr);
					 	$emailstr.="<tr><td>".$model['id_issue']."</td> <td>".$descr."</td>  <td>".ProjectsIssues::getprio($model['priority'])." </td> <td> ".ProjectsIssues::getStatus($model['status'])." </td> <td style='text-align:center'>  ".$model['type']."</td> <td style='text-align:center'>  ".ProjectsTasks::getTaskDescByid($model['fbr'])."</td> <td style='text-align:center'>".ProjectsIssues::getStatus($model['status'])." </td> <td style='text-align:center'> ".ProjectsIssues::getUsersGrid(ProjectsIssues::getAssignedto($model['id']))." </td> <td style='text-align:center'>  ".Users::getCredentialsbyId($model['logged_by'])."</td><td style='text-align:center'>  ".date("d/m/Y", strtotime($model['logged_date']))."</td></tr>";
					}
					$emailstr.="</table> <br /><br />Best Regards,<br />SNSit";
				

					$subject = $project['name'] .' Issue List - '.date('Y-m-d');
					$replace = array(
							$emailstr,	
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					foreach($emails as $email)	{
						Yii::app()->mailer->AddAddress($email);
					}
					$emailpro = ProjectsIssues::getUsersProj($project['id']);
					foreach($emailpro as $emailp)	{
						Yii::app()->mailer->AddAddress($emailp['email']);
					}
					$emailPM= Projects::getProjectManagerEmail($project['id']);
					if (!empty($emailPM)){
						Yii::app()->mailer->AddAddress($emailPM);
					}
					$emailBM= Projects::getBusinessManagerEmail($project['id']);
					if (!empty($emailBM)){
						Yii::app()->mailer->AddAddress($emailBM);
					}
					$replace = array(
							$emailstr,	
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);	
				}
			}	
		}	
}
public static function actionsendIssuesSSummarySolved()
{
	$part = array();
	$notif = EmailNotifications::getNotificationByUniqueName('issues_solved');
	if ($notif != NULL){
		if(!EmailNotifications::isNotificationPAlertSent(1 , 'base bugs', $notif['id'])){
			$to_replace = array(
				
				'{body}',
			);
			$issue = '';	$i = 1;	$total='';	$total2='';	$mon=date('m');
			$issues= Yii::app()->db->createCommand("SELECT p.name as pname, c.name as cname, c.id, pi.description, pi.priority, pi.fixed_date, p.customer_id, pi.logged_by, pi.fixed_by, pi.notes, p.version, pi.module FROM projects_issues pi, projects p, customers c where pi.fixed_date>= (CURRENT_DATE()- INTERVAL 7 DAY) and pi.type='Base Bug' and ( pi.status =1 or  pi.status =2 ) and p.id= pi.id_project and c.id=  p.customer_id order by  p.name, pi.fixed_date")->queryAll();
			
			if(!empty($issues)){ 
						
					$emailstr="Dear All, <br /><br />Kindly find below a list of base bug issue(s) solved in the past week:<br/><br/>";
					$emailstr.="<table border='1'  style='font-family:Calibri;' ><tr><th>Project</th><th>Customer</th><th>Version</th><th>Issue</th><th>Priority</th><th>Module</th><th>Notes</th><th>Fix Date</th><th>Logged By</th><th>Fixed By</th></tr>";
					foreach($issues as $issue){	
						$descr =str_replace('Descr:', '', $issue['description']);
						$descr =str_replace('Issue:', '', $descr);
						if(!empty($issue['version']))
						{
							$version =Codelkups::getCodelkup($issue['version']);
						}else{
							$version =Codelkups::getCodelkup(Maintenance::getVersionPerProduct($issue['id'], 64));
						}
						
						$emailstr.="<tr><td>".$issue['pname']."</td>  <td>".$issue['cname']."</td> <td>".$version."</td>   <td>".$descr."</td>  <td>".ProjectsIssues::getprio($issue['priority'])."</td><td>".Codelkups::getCodelkup($issue['module'])."</td>  <td>".$issue['notes']."</td> <td>".$issue['fixed_date']."</td> <td>".Users::getNamebyId($issue['logged_by'])."</td><td>".Users::getNamebyId($issue['fixed_by'])."</td></tr>";
					}
					$emailstr.="</table> <br /><br />Best Regards,<br />SNSit";			
						$subject = 'Base Bugs Fixes - '.date('d/m/Y');
					$replace = array(
							$emailstr,	
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					foreach($emails as $email)	{
						Yii::app()->mailer->AddAddress($email);
					}	
//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");					
					$replace = array(
							$emailstr,	
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true)){
						EmailNotifications::saveSentNotification(1 , 'base bugs', $notif['id']);
					}    
				
			}	
		}
	}	
}
public static function actionsendDailyIssues()
{
	$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('issues_weekly');
		if ($notif != NULL){
			$to_replace = array(				
				'{body}',
			);
			$issue = '';	$i = 1;	$total='';	$total2='';	$mon=date('m');
			$projects= Yii::app()->db->createCommand("SELECT p.id,p.name FROM projects p where p.status=1 and (select count(1) from projects_issues pi where pi.id_project=p.id and pi.lastupdateddate >(CURRENT_DATE() - INTERVAL 1 DAY) ) >0")->queryAll();
 			if(!empty($projects)){ 
				foreach($projects as $project){		
					$emailstr="Dears, <br /><br />Kindly find below the issues updated today on ".$project['name'].":<br/><br/>";

					$created= ProjectsIssues::getIssuesCreated($project['id']);
					if(!empty($created)){
						$emailstr.="<b>New Issues:</b><br/><br/>";
						$emailstr.="<table border='1'  style='font-family:Calibri;' ><tr><th>Issue#</th><th>Description</th><th>Priority</th><th>Status</th><th>Module</th><th>Type</th><th>FBR</th><th>Assigned To</th><th>Logged By</th><th>Logged Date</th></tr>";
						foreach($created as $model){	
							$descr =str_replace('Descr:', '', $model['description']);
							$descr =str_replace('Issue:', '', $descr);
						 	$emailstr.="<tr><td>".sprintf("%03d", $model['id_issue'])."</td> <td>".$descr."</td>  <td>".ProjectsIssues::getprio($model['priority'])." </td> <td> ".ProjectsIssues::getStatus($model['status'])." </td>  <td> ".Codelkups::getCodelkup($model['module'])." </td>  <td style='text-align:center'>  ".$model['type']."</td> <td style='text-align:center'>  ".ProjectsTasks::getTaskDescByid($model['fbr'])."</td> <td style='text-align:center'> ".ProjectsIssues::getUsersGrid(ProjectsIssues::getAssignedto($model['id']))." </td><td style='text-align:center'>  ".Users::getCredentialsbyId($model['logged_by'])."</td><td>".date("d/m/Y", strtotime($model['logged_date']))."</td></tr>";
						}	
						$emailstr.="</table><br />";					
					}
					
					$models= ProjectsIssues::getIssuesupdated($project['id']);
					if(!empty($models)){
						$emailstr.="<b>Updated Issues:</b><br/><br/>";
						$emailstr.="<table border='1'  style='font-family:Calibri;' ><tr><th>Issue#</th><th>Description</th><th>Priority</th><th>Status</th><th>Module</th><th>Type</th><th>FBR</th><th>Assigned To</th><th>Logged By</th><th>Logged Date</th></tr>";
						foreach($models as $model){	
							$descr =str_replace('Descr:', '', $model['description']);
							$descr =str_replace('Issue:', '', $descr);
						 	$emailstr.="<tr><td>".sprintf("%03d", $model['id_issue'])."</td> <td>".$descr."</td>  <td>".ProjectsIssues::getprio($model['priority'])." </td> <td> ".ProjectsIssues::getStatus($model['status'])." </td> <td> ".Codelkups::getCodelkup($model['module'])." </td> <td style='text-align:center'>  ".$model['type']."</td> <td style='text-align:center'>  ".ProjectsTasks::getTaskDescByid($model['fbr'])."</td> <td style='text-align:center'> ".ProjectsIssues::getUsersGrid(ProjectsIssues::getAssignedto($model['id']))." </td><td style='text-align:center'>  ".Users::getCredentialsbyId($model['logged_by'])."</td><td>".date("d/m/Y", strtotime($model['logged_date']))."</td></tr>";
						}
						$emailstr.="</table> <br />";
					}

					$emailstr.="<br />Best Regards,<br />SNSit";

					$subject = $project['name'] .' Issues Update';
					$replace = array(
							$emailstr,	
					);
					Yii::app()->mailer->ClearAddresses();					
					$emailpro = ProjectsIssues::getUsersProj($project['id']);
					foreach($emailpro as $emailp)	{
						 Yii::app()->mailer->AddAddress($emailp['email']);
					}
					$emailPM= Projects::getProjectManagerEmail($project['id']);
					if (!empty($emailPM)){
						 Yii::app()->mailer->AddAddress($emailPM);
					} 
					$emailBM= Projects::getBusinessManagerEmail($project['id']);
					if (!empty($emailBM)){
						Yii::app()->mailer->AddAddress($emailBM);
					}
				//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);	
				}
			}	
		}
}
public static function actionsendWeeklyIssuesSSummary()
{
	$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('issues_weekly');
		if ($notif != NULL){
			$to_replace = array(
				
				'{body}',
			);
			$issue = '';	$i = 1;	$total='';	$total2='';	$mon=date('m');
			$projects= Yii::app()->db->createCommand("SELECT p.id,p.name FROM projects p where p.status=1 and  (select count(1) from projects_issues pi where pi.id_project=p.id and pi.status in (0,1)) >0")->queryAll();
 		
			if(!empty($projects)){ 
				foreach($projects as $project){	
					if 	(!EmailNotifications::isNotificationPAlertSent($project['id'], 'projects', $notif['id']))	
					{
						$modelsPending= ProjectsIssues::getIssuesperStatus($project['id'],0);
						$modelsFixed= ProjectsIssues::getIssuesperStatus($project['id'],1);

						if(!empty($modelsPending) || !empty($modelsFixed) )
						{
							$emailstr="Dears, <br /><br />Kindly find below a weekly summary of ".$project['name']." issues on ".date('Y-m-d').":<br/><br/>";
							$emailstr.='<table  border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th># Issues Created Last Week</th><th># Issues Fixed Last Week</th><th># Issues Closed Last Week</th></tr>';
							$emailstr.="<tr><td style='text-align:center;'>".ProjectsIssues::loggedlastWeek($project['id'])."</td> <td style='text-align:center;'>".ProjectsIssues::fixedlastWeek($project['id'])."</td>  <td style='text-align:center;'>".ProjectsIssues::closedlastWeek($project['id'])."</td>   </tr>";
							$emailstr.="</table> <br />";
							if(!empty($modelsPending))
							{
								$emailstr.='<b>Pending Issues:</b><br/><br/><table border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th>Issue#</th><th>Description</th><th>Priority</th><th>Status</th><th>Module</th><th>Type</th><th>FBR</th><th>Assigned To</th><th>Logged By</th><th>Logged Date</th></tr>';
								foreach($modelsPending as $model){	
									$descr =str_replace('Descr:', '', $model['description']);
									$descr =str_replace('Issue:', '', $descr);
								 	$emailstr.="<tr><td>".sprintf("%03d", $model['id_issue'])."</td> <td>".$descr."</td>  <td>".ProjectsIssues::getprio($model['priority'])." </td> <td> ".ProjectsIssues::getStatus($model['status'])." </td> <td> ".Codelkups::getCodelkup($model['module'])." </td><td style='text-align:center'>  ".$model['type']."</td> <td style='text-align:center'>  ".ProjectsTasks::getTaskDescByid($model['fbr'])."</td> <td style='text-align:center'>  ".ProjectsIssues::getUsersGrid(ProjectsIssues::getAssignedto($model['id']))."</td> <td style='text-align:center'>  ".Users::getCredentialsbyId($model['logged_by'])."</td><td>".date("d/m/Y", strtotime($model['logged_date']))."</td></tr>";
								}
								$emailstr.="</table>  <br />";
							}

							if(!empty($modelsFixed))
							{
								$emailstr.='<b>Fixed Issues:</b><br/><br/><table  border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th>Issue#</th><th>Description</th><th>Priority</th><th>Status</th><th>Module</th><th>Type</th><th>FBR</th><th>Assigned To</th><th>Logged By</th><th>Logged Date</th></tr>';
								foreach($modelsFixed as $model){	
									$descr =str_replace('Descr:', '', $model['description']);
									$descr =str_replace('Issue:', '', $descr);
								 	$emailstr.="<tr><td>".sprintf("%03d", $model['id_issue'])."</td> <td>".$descr."</td>  <td>".ProjectsIssues::getprio($model['priority'])." </td> <td> ".ProjectsIssues::getStatus($model['status'])." </td> <td> ".Codelkups::getCodelkup($model['module'])." </td> <td style='text-align:center'>  ".$model['type']."</td> <td style='text-align:center'>  ".ProjectsTasks::getTaskDescByid($model['fbr'])."</td> <td style='text-align:center'>  ".ProjectsIssues::getUsersGrid(ProjectsIssues::getAssignedto($model['id']))."</td><td style='text-align:center'>  ".Users::getCredentialsbyId($model['logged_by'])."</td><td>".date("d/m/Y", strtotime($model['logged_date']))."</td></tr>";
								}
								$emailstr.="</table> ";
							}
							$emailstr.=" <br /><br />Best Regards,<br />SNSit";
						

							$subject = $project['name'] .' Issues Summary - '.date('Y-m-d');
							$replace = array(
									$emailstr,	
							);
							$body = str_replace($to_replace, $replace, $notif['message']);
							$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
							Yii::app()->mailer->ClearAddresses();
							foreach($emails as $email)	{
								Yii::app()->mailer->AddAddress($email);
							}
							$emailpro = ProjectsIssues::getUsersProjLM($project['id']);

							foreach($emailpro as $emailp)	{
								Yii::app()->mailer->AddAddress($emailp);
							}
							$emailPM= Projects::getProjectManagerEmail($project['id']);
							if (!empty($emailPM)){
								Yii::app()->mailer->AddAddress($emailPM);
							}
							$emailBM= Projects::getBusinessManagerEmail($project['id']);
							if (!empty($emailBM)){
								Yii::app()->mailer->AddAddress($emailBM);
							}
							//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
							$replace = array(
									$emailstr,	
							);
							$body = str_replace($to_replace, $replace, $notif['message']);
							Yii::app()->mailer->Subject  = $subject;
							Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
							Yii::app()->mailer->Send(true);	
							EmailNotifications::saveSentNotification($project['id'], 'projects', $notif['id']);	
						}
					}
				}
			}	
		}			
		//echo $body;
}
public static function actionsendContractOverload(){
	$notif = EmailNotifications::getNotificationByUniqueName('contract_overload');
	$list='Kindly find below the maintenance contracts crossed services limit: <br /> <ul>';
	$count=0;
	$services= Yii::app()->db->createCommand("SELECT id ,id_contract, id_service, `limit` FROM `maintenance_services` where field_type=1 ")->queryAll();		
			foreach ($services as $service) {
				$actuald= MaintenanceServices::getActual($service['id'], $service['id_service'], 1);
				$li= $service['limit'] - $actuald; 						
				if($li<0 ){ 
					$details=Yii::app()->db->createCommand("SELECT customer, contract_description FROM maintenance where id_maintenance=".$service['id_contract']." ")->queryRow();
					$cname=Customers::getNameById($details['customer']);
					$url=Yii::app()->createAbsoluteUrl('maintenance/view', array('id'=>$service['id_contract']));
					$list.='<li><b>Customer</b>: <a href='.$url.'>'.$cname.'</a>- <b>Project</b>: '.$details['contract_description'].'- <b>Renewal Date</b>: '.date("d/m/Y", strtotime(MaintenanceServices::getNextRenovationDate($service['id']))).'- <b>Service</b>: '.MaintenanceServices::getDescriptionName($service['id_service']).'- <b>Budget</b>: '.$service['limit'].' - <b>Actuals</b>: '.$actuald.'</li>';
					$checking=0;
					$count++;
				}
			} 	
	$list.='</ul>';
	if($count==0){
		$list='Kindly note that none of the maintenance contracts have crossed their specified limits. <br />';
	}
	$to_replace = array(
		'{list}'			
	);
	$replace = array(
		$list						
	);
	$body = str_replace($to_replace, $replace, $notif['message']);
	$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
	Yii::app()->mailer->ClearAddresses();
	foreach($emails as $email) {
		if (!empty($email)){
			Yii::app()->mailer->AddAddress($emails);
		}
	}
	$subject= $notif['name'];		
	Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
	Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	Yii::app()->mailer->Send();
}	
public static function actionsendProjectCRStatus(){
					$notif = EmailNotifications::getNotificationByUniqueName('CR_Project_Status');
					$all_active_cr= Yii::app()->db->createCommand("SELECT p.id , p.customer_id , p.name, date(p.adddate) as adddate FROM projects p,customers c , eas e WHERE p.id=e.id_project and (e.TM<>'1' or e.TM is null )and p.customer_id=c.id and p.status='1' and (p.id_type='28') order by c.name asc")->queryAll();
				 	$all_active_tandm= Yii::app()->db->createCommand("SELECT p.id , p.customer_id , p.name,  date(p.adddate) as adddate FROM projects p,customers c , eas e WHERE p.id=e.id_project and e.TM='1' and p.customer_id=c.id and p.status='1'  order by c.name asc")->queryAll();
				 	$all_deactivated= Yii::app()->db->createCommand("SELECT p.id , p.customer_id , p.name, date(p.adddate) as adddate, p.id_type FROM projects p,customers c , eas e WHERE p.id=e.id_project and (e.TM<>'1' or e.TM is null )and p.customer_id=c.id and p.status='1' and p.id_type!='28' and (deactivate_alerts ='Yes') order by c.name asc")->queryAll();
					$list="<table border='1'  style='font-family:Calibri;' ><tr><th>Customer Name</th><th>Project Name</th><th>Type</th><th>Project Manager</th><th>Business Manager</th><th>Budgeted MDs</th><th>Actuals MDs</th><th>Project Overrun %</th><th>Created On</th></tr>";
					foreach ($all_active_cr as $value) {
							$budgeted=Projects::getBudgetedMD($value['id']);
							if($budgeted==0 || empty($budgeted)){ $budgeted=1;}
							 $list.="<tr><td>".Customers::getNameById($value['customer_id'])."</td> <td>".$value['name']."</td>  <td> CR </td>  <td>".Projects::getProjectManager($value['id'])." </td> <td> ".Projects::getBusinessManager($value['id'])." </td> <td style='text-align:center'>  ".Utils::formatNumber($budgeted)."</td> <td style='text-align:center'>  ".Utils::formatNumber(Projects::getProjectActualManDays($value['id']))."</td> <td style='text-align:center'>  ".Utils::formatNumber((($budgeted-Projects::getProjectActualManDays($value['id']))/$budgeted )*100)."</td> <td style='text-align:center'>  ".$value['adddate']."</td> </tr>";
					}

					$list.="</table><br />Kindly check if any of the below projects with deativated alerts can be closed:<br /><br />";
						$list.="<table border='1'  style='font-family:Calibri;' ><tr><th>Customer Name</th><th>Project Name</th><th>Type</th><th>Project Manager</th><th>Business Manager</th><th>Budgeted MDs</th><th>Actuals MDs</th><th>Project Overrun %</th><th>Created On</th></tr>";
					foreach ($all_deactivated as $value) {
								$budgeted=Projects::getBudgetedMD($value['id']);
							if($budgeted==0 || empty($budgeted)){ $budgeted=1;}
							$list.="<tr><td>".Customers::getNameById($value['customer_id'])."</td> <td>".$value['name']."</td> <td>".Codelkups::getCodelkup($value['id_type'])."</td>  <td>".Projects::getProjectManager($value['id'])." </td> <td> ".Projects::getBusinessManager($value['id'])." </td>  <td style='text-align:center'>  ".Utils::formatNumber($budgeted)."</td> <td style='text-align:center'>  ".Utils::formatNumber(Projects::getProjectActualManDays($value['id']))."</td> <td style='text-align:center'>  ".Utils::formatNumber((($budgeted-Projects::getProjectActualManDays($value['id']))/$budgeted )*100)."</td>  <td style='text-align:center'>  ".$value['adddate']."</td> </tr>";
						}
					 $list.="</table>";

					$list.="</table><br />Kindly find below all active T&M Projects:<br /><br />";
						$list.="<table border='1'  style='font-family:Calibri;' ><tr><th>Customer Name</th><th>Project Name</th><th>Project Manager</th><th>Business Manager</th><th>Actuals MDs</th><th>Created On</th></tr>";
					foreach ($all_active_tandm as $value) {
							$list.="<tr><td>".Customers::getNameById($value['customer_id'])."</td> <td>".$value['name']."</td>  <td>".Projects::getProjectManager($value['id'])." </td> <td> ".Projects::getBusinessManager($value['id'])." </td> <td style='text-align:center'>  ".Utils::formatNumber(Projects::getProjectActualManDays($value['id']))."</td> <td style='text-align:center'>  ".$value['adddate']."</td> </tr>";
						}
					 $list.="</table>";
		    			$to_replace = array(
							'{list}'			
						);
						$replace = array(
							$list						
						);					
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
						//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
						foreach($emails as $email) {
								if (!empty($email))
									Yii::app()->mailer->AddAddress($emails);
							}
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send();
}
public static function actionsendProjectSWStatus(){
					$notif = EmailNotifications::getNotificationByUniqueName('SW_Project_Status');	
					$all_active_cr= Yii::app()->db->createCommand("SELECT p.id , p.customer_id , p.name FROM projects p,customers c , eas e WHERE p.id=e.id_project and (e.TM<>'1' or e.TM is null )and p.customer_id=c.id and p.status='1' and p.id_type='27' order by c.name asc")->queryAll();
				 	$list="<table border='1'  style='font-family:Calibri;' ><tr><th>Customer Name</th><th>Project Name</th><th>Project Manager</th><th>Business Manager</th><th>Budgeted MDs</th><th>Actuals MDs</th><th>Project Overrun %</th></tr>";
					foreach ($all_active_cr as $value) {
							$budgeted=Projects::getBudgetedMD($value['id']);
							if($budgeted==0 || empty($budgeted)){ $budgeted=1;}
							 $list.="<tr><td>".Customers::getNameById($value['customer_id'])."</td> <td>".$value['name']."</td>  <td>".Projects::getProjectManager($value['id'])." </td> <td> ".Projects::getBusinessManager($value['id'])." </td> <td style='text-align:center'>  ".Utils::formatNumber($budgeted)."</td> <td style='text-align:center'>  ".Utils::formatNumber(Projects::getProjectActualManDays($value['id']))."</td> <td style='text-align:center'>  ".Utils::formatNumber((($budgeted-Projects::getProjectActualManDays($value['id']))/$budgeted )*100)."</td> </tr>";
					}
					$list.="</table>";    				
		    		$to_replace = array(
						'{list}'			
					);
					$replace = array(
						$list						
					);					
					$body = str_replace($to_replace, $replace, $notif['message']);
					$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					foreach($emails as $email) {
						if (!empty($email))
							Yii::app()->mailer->AddAddress($emails);
					}
					$subject= $notif['name'];		
					Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send();
}
public static function actionsendProjectConStatus(){
					$notif = EmailNotifications::getNotificationByUniqueName('Con_Project_Status');			
					$all_active_cr= Yii::app()->db->createCommand("SELECT p.id , p.customer_id , p.name FROM projects p,customers c , eas e WHERE p.id=e.id_project and (e.TM<>'1' or e.TM is null )and p.customer_id=c.id and p.status='1' and p.id_type='26' order by c.name asc")->queryAll();
				 	$list="<table border='1'  style='font-family:Calibri;' ><tr><th>Customer Name</th><th>Project Name</th><th>Project Manager</th><th>Business Manager</th><th>Budgeted MDs</th><th>Actuals MDs</th><th>Project Overrun %</th></tr>";
					foreach ($all_active_cr as $value) {
							$budgeted=Projects::getBudgetedMD($value['id']);
							if($budgeted==0 || empty($budgeted)){ $budgeted=1;}
							$list.="<tr><td>".Customers::getNameById($value['customer_id'])."</td> <td>".$value['name']."</td>  <td>".Projects::getProjectManager($value['id'])." </td> <td> ".Projects::getBusinessManager($value['id'])." </td> <td style='text-align:center'>  ".Utils::formatNumber($budgeted)."</td> <td style='text-align:center'>  ".Utils::formatNumber(Projects::getProjectActualManDays($value['id']))."</td> <td style='text-align:center'>  ".Utils::formatNumber((($budgeted-Projects::getProjectActualManDays($value['id']))/$budgeted )*100)."</td> </tr>";
					}
					 $list.="</table>";    				
		    			$to_replace = array(
							'{list}'			
						);
						$replace = array(
							$list						
						);					
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
							foreach($emails as $email) {
								if (!empty($email))
									Yii::app()->mailer->AddAddress($emails);
							}
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send();
}
public static function actionsendProjectClosedNotTransitedSupport(){
					$notif = EmailNotifications::getNotificationByUniqueName('Projects_Closed_Not_Support');
					$all_active_cr= Yii::app()->db->createCommand("SELECT id, name, closed_date FROM projects WHERE  status='2' and id_type='27' and under_support='No' and customer_id IN (select customer from maintenance where status='Active')  and id not in (498, 522, 539, 541, 557, 599, 625, 663 )")->queryAll();
				 	$list="<table border='1' style='font-family:Calibri;' ><tr><th>Project Name</th><th>Project Manager</th><th>Business Manager</th><th>Closed Date</th></tr>";
					foreach ($all_active_cr as $value) {
							 $list.="<tr><td>".$value['name']."</td>  <td>".Projects::getProjectManager($value['id'])." </td> <td> ".Projects::getBusinessManager($value['id'])." </td><td> ".date("d/m/Y", strtotime($value['closed_date']))."  </td> </tr>";
					}
					$list.="</table>";    				
		    			$to_replace = array(
							'{list}'			
						);
						$replace = array(
							$list						
						);					
						$body = str_replace($to_replace, $replace, $notif['message']);
							$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
							foreach($emails as $email) {
								if (!empty($email))
									Yii::app()->mailer->AddAddress($emails);
							}
						$subject= $notif['name'];		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send();	
}
	public function actionshowMilestoneAlert($id){
		$validate=Yii::app()->db
		->createCommand("SELECT 
			case when (estimated_date_of_completion='0000-00-00' and estimated_date_of_start='0000-00-00')  then 'Start/End Date has to be defined' end as field,
			case when (estimated_date_of_start='0000-00-00')  then 'Start Date has to be defined' end as fields,
			case when (estimated_date_of_completion='0000-00-00')  then 'End Date has to be defined' end as fieldc,
			case when (status not in ('Closed') and estimated_date_of_completion < CURRENT_DATE())  then 'Closed Date has passed and milestone is not closed yet.' end as field1,
			case when (status ='Pending' and estimated_date_of_start<CURRENT_DATE()) then 'Start Date has passed and milestone is not in progress yet.' end as field2
			FROM projects_milestones 
			WHERE id= ".$id." and  applicable='Yes'  
			and (
			(status not in ('Closed') and estimated_date_of_completion < CURRENT_DATE()) 
			OR (status ='Pending' and estimated_date_of_start<CURRENT_DATE())
			OR (estimated_date_of_completion='0000-00-00' or estimated_date_of_start='0000-00-00')
			)")->queryAll();	
			$txt="";
			if ($validate[0]['field'] !=null){
				$txt.= $validate[0]['field'];
			}else if ($validate[0]['fields'] !=null){
				$txt.= $validate[0]['fields'];
				if ($validate[0]['field1'] !=null){
					$txt.='<br />'. $validate[0]['field1'].'<br/>';
				}
			}else if ($validate[0]['fieldc'] !=null){
				$txt.= $validate[0]['fieldc'];
				if ($validate[0]['field2'] !=null){
					$txt.= '<br />'.$validate[0]['field2'];
				}
			}else{
				if ($validate[0]['field1'] !=null){
					$txt.= $validate[0]['field1'].'<br/>';
				}
				if ($validate[0]['field2'] !=null){
					$txt.= $validate[0]['field2'];
				}
			}
		echo CJSON::encode(array(
				'message' => $txt,
			));
			exit;
	}	
	public function actionvalidateTransitionSupport(){ 
		$id_customer = $_POST['selected'];
		$id_project = $_POST['id_project'];			
			$valid=0;	
			$validate_maintenance = Yii::app()->db->createCommand("SELECT count(1) FROM maintenance WHERE customer=".$id_customer." and status='Active' ")->queryScalar();
			$validate_sd_acess=  Yii::app()->db->createCommand("SELECT count(1) FROM customers_contacts where access='Yes' and  id_customer=".$id_customer." ")->queryScalar();
			$validate_cs=  Yii::app()->db->createCommand("SELECT count(1) FROM customers where  id=".$id_customer." and cs_representative<>'' and cs_representative<>' ' and cs_representative<>'0' and cs_representative is not null and cs_representative in (select id from users where active='1') ")->queryScalar();
			$validate_checklist=  Yii::app()->db->createCommand("SELECT count(1) FROM `projects_checklist` where id_project=".$id_project." and status='Open'")->queryScalar();
			if($validate_maintenance==0){$valid=1;}
			if($validate_sd_acess==0){$valid=2;}
			if($validate_cs==0){$valid=3;}
			if($validate_checklist>0){$valid=4;}
			$returnArr['valid'] = $valid;
			echo json_encode($returnArr);
	}
	public function actionValidateAlerts(){
		$valid= 1;
		$id_project=$_POST['selected'];
		$result=Yii::app()->db->createCommand("SELECT pa.alerts as alerts FROM projects_alerts pa 
		where pa.id_project=".$id_project."")->queryScalar();
		if($result!=false){
			$alerts= array();
			$alerts= explode(',',$result);			
			foreach ($alerts as &$value) {
				$value=((int)$value);
				$useralerts= Yii::app()->db->createCommand("SELECT count(1) FROM alerts a 
						where a.id=".$value." AND a.user_control=1")->queryScalar();
				if($useralerts[0]=="1"){
					$valid=0;
				}
			}
		}else{
			echo "no alerts ";
		}
		$returnArr['valid']=$valid;
		echo json_encode($returnArr);
	}	
	public function actionassignUsersIssues($id)
	{
		if (empty($_POST)){
			exit;
		}
		
		$ids = $_POST['checked']; 
		$tasks = $_POST['checkissue'];
	
		$susers = implode(',', $ids);

		foreach ($ids as $uid) {
			foreach ($tasks as $task) {
				$taskss[] = '('.$uid.','.$task.')';
			}
		}

		if (!empty($taskss)){
			$nr = Yii::app()->db->createCommand('INSERT IGNORE INTO user_issues (id_user, id_issue) VALUES '.implode(',',$taskss))
								->execute();
		}

		$this->sendNotificationsEmailsIssue('issue_assigned_act',$id, $ids, implode(',', $tasks));
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been assigned!',				
			));
		exit;
	}
	public function actionAssignUsers($id){
		if (empty($_POST)){
			exit;
		}
		$ids = $_POST['checked']; 
		$tasks = $_POST['checktask'];
		$stasks = implode(',', $tasks);
		$susers = implode(',', $ids);
		$nr = 0;
		$id_phases = array();		
		$taskss = array();
		$user_times = array();		
		if (!empty($tasks)) {
			$id_phases = Yii::app()->db->createCommand("SELECT DISTINCT id_project_phase FROM projects_tasks WHERE id IN ( $stasks )")->queryColumn();
		}
		$id_project=Projects::getProjectId($tasks['0']);
		$sphases = implode(',', $id_phases);
		$current_user=Yii::app()->user->id;
		$update_assigned = Yii::app()->db->createCommand("update projects_phases set assigned=1 , assigned_date=NOW(), assigned_by=".$current_user." where id_project =$id_project ")->execute();
		if (!empty($ids)){
			$all_timesheets = Yii::app()->db->createCommand("SELECT id, id_user, week_start FROM timesheets WHERE id_user IN ($susers) AND status='New'")->queryAll();
			$timesheets = array();
			foreach ($all_timesheets as $key => $ts){
				$timesheets[$ts['id_user']][$key]['id'] = $ts['id'];
				$timesheets[$ts['id_user']][$key]['day'] = $ts['week_start'];
			}
		}		
		foreach ($ids as $uid) {
			foreach ($tasks as $task) {
				$taskss[] = '('.$uid.','.$task.')';
				if (isset($timesheets[$uid])){
					foreach ($timesheets[$uid] as $ts){
						if(ProjectsTasks::getTaskDescByid($task) != 'FBRXXX' && ProjectsTasks::getTaskDescByid($task) != 'INTXXX')
						{
							$user_times[] = '('.$uid.','.$task.','.$ts['id'].',"0.00","'.$ts['day'].'",0,0'.')';
						}
					}
				}
			}
		}		
		if (!empty($taskss)){
			$nr = Yii::app()->db->createCommand('INSERT IGNORE INTO user_task (id_user, id_task) VALUES '.implode(',',$taskss))
								->execute();
		}		
		if (!empty($user_times)){
			Yii::app()->db->createCommand('INSERT IGNORE INTO user_time (id_user, id_task, id_timesheet, amount, date, `default`, status)
			 					VALUES '.implode(',',$user_times))
						  ->execute();
		}		
		$expenses = Projects::getExpensesType(Projects::getId('customer_id',$id), $id);
		if ($expenses == 'Actuals'){
			$this->sendNotificationsEmailsTask('project_assigned_act',$id, $ids);
		}else{ 
			$this->sendNotificationsEmailsTask('project_assigned_not',$id, $ids);
		}			
		$allphases=Projects::getAllPhasesByProject($id_project);
		$tandmrecord=TandM::checkandInsert($id_project);
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been assigned!',
				'id_phases' => $id_phases,
				'allphases' => $allphases
			));
		exit;
	}	
	public function actionunassignUsersIssues($id){
		if (!isset($_POST['checkissue'], $_POST['checked'])){
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one issue and one user!',
			));
			exit;
		}
		$nr = 0;
		
		$issues = implode(',', $_POST['checkissue']);
		$users = implode(',', $_POST['checked']);		
		
		$nr = Yii::app()->db->createCommand("DELETE FROM user_issues WHERE id_issue IN ($issues) and id_user in ($users)")->execute();
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been unassigned!'
			));
		exit;	
	}
	public function actionUnassignUsers($id){
		if (!isset($_POST['checktask'], $_POST['checked'])){
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one task and one user!',
			));
			exit;
		}
		$nr = 0;
		$id_phases = array();
		$tasks = implode(',', $_POST['checktask']);
		$users = implode(',', $_POST['checked']);		
		$id_phases = Yii::app()->db->createCommand("SELECT DISTINCT id_project_phase FROM projects_tasks WHERE id IN ( $tasks )")->queryColumn();		
		$nr = Yii::app()->db->createCommand("DELETE FROM user_task WHERE id_task IN ($tasks) and id_user in ($users)")->execute();
		echo CJSON::encode(array(
				'status'=>'success',
				'message' => $nr.' users have been unassigned!',
				'id_phases' => $id_phases,
			));
		exit;	
	}
	public function actionDeleteTask($id  = null){	
		if ($id != null){
			$item = ProjectsTasks::model()->findByPk($id);
			if ($item===null)
				exit;
			$days = Projects::getDaysOf(Projects::getProjectId($id),Projects::getPhaseId($id));
			$id_phase = (int)Projects::getPhaseId($id);
			$task_day = ProjectsTasks::getDay($id);
			$time= Projects::getActualMDPerTask($item->id);
			if ($item!==null && $time== 0){
				$item->delete();
			}else if ( $time> 0){
						echo CJSON::encode(array(
						'status'=>'fail',
						'message' => ' Cannot delete a task with time spent!',
					)); exit;
					}			
			echo CJSON::encode(array(
				'status'=>'success',
				'id_phase'=>$id_phase,
				'totalDays'=>$days - $task_day,
			));
		}else{
			if (isset($_POST['checktask'])){
				$id_phase = array();
				$r = array();
				$id_task = $_POST['checktask'];
				foreach ($id_task as $idt){
					$item = ProjectsTasks::model()->findByPk($idt);
					$time= Projects::getActualMDPerTask($item->id);
					if ($item!==null && $time== 0){
						$id_phase[] = $item->id_project_phase;
						$days = Projects::getDaysOf(Projects::getProjectId($idt), $item->id_project_phase);
						$task_day = ProjectsTasks::getDay($idt);
						$r[] = $days - $task_day;
						$item->delete();
					}else if ( $time> 0){
						echo CJSON::encode(array(
						'status'=>'fail',
						'message' => ' Cannot delete a task with time spent!',
					));exit;
					}
				}
				echo CJSON::encode(array(
					'status'=>'success',
					'id_phase'=>$id_phase,
					'totalDays'=>$r,
				));
			}else{
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one task!',
				));
			}
		} 
	}
	public function actionDeletePhase($id){
		$phase = ProjectsPhases::model()->findByPk($id);
		if ($phase===null)
			exit;
		$id_task = ProjectsTasks::getAllTasks((int)$id);
		foreach ($id_task as $idt) {

			$item = ProjectsTasks::model()->findByPk($idt['id']);
			$time= Projects::getActualMDPerTask($item->id);
			if ($item!==null && $time== 0){
			$item->delete();
			}else if ( $time> 0){
						echo CJSON::encode(array(
						'status'=>'fail',
						'message' => ' Cannot delete a task with time spent!',
					));
						exit;
					}
		}		
		$phase->delete();		
		echo CJSON::encode(array(
			'status'=>'success',
		));
		exit;
	}
	public function actionDeleteScenarioItem($id){
		$scenario = ProjectsScenarios::model()->findByPk($id);
		if ($scenario==null)
			exit;
		$scenario->delete();		
		echo CJSON::encode(array(
			'status'=>'success',
		));
		exit;
	}
	public function actionDeleteRiskItem($id){
		$risk = ProjectsRisks::model()->findByPk($id);
		if ($risk==null)
			exit;
		$risk->delete();		
		echo CJSON::encode(array(
			'status'=>'success',
		));
		exit;
	}
	public function actiondeleteIssueItem($id){
		$issue = ProjectsIssues::model()->findByPk($id);
		if ($issue==null)
			exit;
		$issue->delete();		
		echo CJSON::encode(array(
			'status'=>'success',
		));
		exit;
	}
	public function actionManageChecklist(){
		$id = (int)$_POST['id_checklist'];
		$value = $_POST['value'];
		$model = ProjectsChecklist::model()->findByPk($id);
		if (empty($value)){
			$value='Open';
		}
		$model->status = $value;
		$model->save();				
				echo json_encode(array('status' => 'saved'));
				exit;	    	
		Yii::app()->clientScript->scriptMap = array(
		    'jquery.js'=>false,
			'jquery.min.js'=>false,
			'jquery-ui.js'=>false,
			'jquery-ui.min.js'=>false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_checklist_form', array(
            	'model'=> $model,
    			'id_project'=>$_POST['id_project'],
        	), true, true)));
       exit;
	}
	public function actionManageMilestone(){
		$id = (int)$_POST['id_milestone'];
		$value = $_POST['value'];
		$type = $_POST['type'];		
		$model = ProjectsMilestones::model()->findByPk($id);		
    	$model->last_updated = date('Y-m-d', strtotime('now'));
    	$pType=Projects::getProjectType($model->id_project);
    	if( $model->estimated_date_of_completion == '0000-00-00')
    		$model->estimated_date_of_completion = "";
    	if( $model->estimated_date_of_start == '0000-00-00')
    		$model->estimated_date_of_start = "";    	
		switch ($type){
			case '1':
				$model->status = $value;
				if ($model->status == 'Closed' && ($pType ==26 || $pType == 27)) {
					if(($model->id_milestone == 9  || $model->id_milestone == 15 ) && $model->applicable != 'No')
					{
						//Golive
						$nb= Documents::validateMilestoneDoc($model->id_project, 'Project Sign Off');
						if($nb<1)
						{
							echo json_encode(array('status' => 'error', 'message' => '<br/>Kindly upload the Project Sign-Off document to close this milestone'));
							exit;
						}else{
							$model->save();
							if ($model->status == 'Closed'){
								$milestone_number = Milestones::getMilestoneNumber($model->id_milestone);
								$type = Codelkups::getCodelkup(Projects::getId('id_type',$_POST['id_project']));
								$sw = array('4','7','9');
								$con = array('4','6');
								if ((in_array($milestone_number,$sw )) && $type == 'SW Service'){
									$this->sendNotificationsEmailsMilestone('milestone',$_POST['id_project'], $milestone_number);
								}
								elseif ((in_array($milestone_number,$con )) && $type == 'Consulting'){	
									$this->sendNotificationsEmailsMilestone('milestone', $_POST['id_project'], $milestone_number);
								}
			   				}
							echo json_encode(array('status' => 'saved'));
							exit;
						}
					}else if($model->id_milestone == 4  && $model->applicable  != 'No'){
						//sop 
						$template= Projects::getTemplatePerProject($model->id_project);
						if($template == 2)
						{
							$nb= Documents::validateMilestoneDoc($model->id_project, 'Integration');
						}else{
							$nb= Documents::validateMilestoneDoc($model->id_project, 'SOP');							
						}
						if($nb<1)
						{
							if($template == 2)
							{
								echo json_encode(array('status' => 'error', 'message' => '<br/>Kindly upload the Sign-off/Integration document to close this milestone'));
								exit;
							}else{
								echo json_encode(array('status' => 'error', 'message' => '<br/>Kindly upload the Sign-off/SOP document to close this milestone'));
								exit;
							}
						}else{
							$model->save();
							if ($model->status == 'Closed'){
								$milestone_number = Milestones::getMilestoneNumber($model->id_milestone);
								$type = Codelkups::getCodelkup(Projects::getId('id_type',$_POST['id_project']));
								$sw = array('4','7','9');
								$con = array('4','6');
								if ((in_array($milestone_number,$sw )) && $type == 'SW Service'){
									$this->sendNotificationsEmailsMilestone('milestone',$_POST['id_project'], $milestone_number);
								}
								elseif ((in_array($milestone_number,$con )) && $type == 'Consulting'){	
									$this->sendNotificationsEmailsMilestone('milestone', $_POST['id_project'], $milestone_number);
								}
			   				}
							echo json_encode(array('status' => 'saved'));
							exit;
						}
					}
					else if($model->id_milestone == 7 && $model->applicable  != 'No'){
						//uat
						$nb= Documents::validateMilestoneDoc($model->id_project, 'UAT');
						if($nb<1)
						{
							echo json_encode(array('status' => 'error', 'message' => '<br/>Kindly upload the Sign-off/UAT document to close this milestone'));
							exit;
						}else{
							$model->save();
							if ($model->status == 'Closed'){
								$milestone_number = Milestones::getMilestoneNumber($model->id_milestone);
								$type = Codelkups::getCodelkup(Projects::getId('id_type',$_POST['id_project']));
								$sw = array('4','7','9');
								$con = array('4','6');
								if ((in_array($milestone_number,$sw )) && $type == 'SW Service'){
									$this->sendNotificationsEmailsMilestone('milestone',$_POST['id_project'], $milestone_number);
								}
								elseif ((in_array($milestone_number,$con )) && $type == 'Consulting'){	
									$this->sendNotificationsEmailsMilestone('milestone', $_POST['id_project'], $milestone_number);
								}
			   				}
							echo json_encode(array('status' => 'saved'));
							exit;
						}
					}else{
						$model->save();
						if ($model->status == 'Closed'){
							$milestone_number = Milestones::getMilestoneNumber($model->id_milestone);
							$type = Codelkups::getCodelkup(Projects::getId('id_type',$_POST['id_project']));
							$sw = array('4','7','9');
							$con = array('4','6');
							if ((in_array($milestone_number,$sw )) && $type == 'SW Service'){
								$this->sendNotificationsEmailsMilestone('milestone',$_POST['id_project'], $milestone_number);
							}
							elseif ((in_array($milestone_number,$con )) && $type == 'Consulting'){	
								$this->sendNotificationsEmailsMilestone('milestone', $_POST['id_project'], $milestone_number);
							}
		   				}
						echo json_encode(array('status' => 'saved'));
						exit;
					}
				}else{
					$model->save();
					if ($model->status == 'Closed'){
						$milestone_number = Milestones::getMilestoneNumber($model->id_milestone);
						$type = Codelkups::getCodelkup(Projects::getId('id_type',$_POST['id_project']));
						$sw = array('4','7','9');
						$con = array('4','6');
						if ((in_array($milestone_number,$sw )) && $type == 'SW Service'){
							$this->sendNotificationsEmailsMilestone('milestone',$_POST['id_project'], $milestone_number);
						}
						elseif ((in_array($milestone_number,$con )) && $type == 'Consulting'){	
							$this->sendNotificationsEmailsMilestone('milestone', $_POST['id_project'], $milestone_number);
						}
	   				}
					echo json_encode(array('status' => 'saved'));
					exit;
				}
				break;				
			case '2':
				$d=DateTime::createFromFormat('d/m/Y', $value)->format('Y-m-d');
				if ( isset($model->estimated_date_of_start) && $model->estimated_date_of_start > $d && $model->estimated_date_of_start!= "0000-00-00" && !empty($model->estimated_date_of_start)){
					echo json_encode(array('status' => 'error', 'message' => '<br/>End Date Cannot be earlier than Start Date'));
						exit;
				}
				$model->estimated_date_of_completion = $value;
				$model->estimated_date_of_completion = DateTime::createFromFormat('d/m/Y', $model->estimated_date_of_completion)->format('Y-m-d');
				$model->save();
				break;				
			case '4':
				$d=DateTime::createFromFormat('d/m/Y', $value)->format('Y-m-d');				
				if ( isset($model->estimated_date_of_completion) && $model->estimated_date_of_completion < $d && $model->estimated_date_of_completion != "0000-00-00" && !empty($model->estimated_date_of_completion) ){
					echo json_encode(array('status' => 'error', 'message' => '<br/>Start Date Cannot be later than End Date'));
						exit;
				}
				$model->estimated_date_of_start = $value;
				$model->estimated_date_of_start = DateTime::createFromFormat('d/m/Y', $model->estimated_date_of_start)->format('Y-m-d');
				//print_r($model->estimated_date_of_completion);exit;
				if(empty($model->estimated_date_of_completion) || $model->estimated_date_of_completion ==  "0000-00-00"  ){
					$model->estimated_date_of_completion = $model->estimated_date_of_start;
				}
				$model->save();
				break;
			case '3':
		//	print_r($model);exit;
				$model->applicable = $value;
				if($model->applicable == 'Yes')
				{
					Yii::app()->db->createCommand("update projects_checklist set status ='Open' WHERE status ='N/A' and id_project=".$model->id_project." and id_phase=".$model->id_milestone)->execute();
				}else{
					Yii::app()->db->createCommand("update projects_checklist set status ='N/A' WHERE status ='Open' and id_project=".$model->id_project." and id_phase=".$model->id_milestone)->execute();
				}		
				$model->save();								
			}
			
			if($pType == '27' && !( Projects::getBusinessManager($model->id_project)==" ")  && !( Projects::getProjectManager($model->id_project)==" ")){
				self::checksendMilestonesEmail($model->id_project);
			}		 
		Yii::app()->clientScript->scriptMap = array(
		    'jquery.js'=>false,
			'jquery.min.js'=>false,
			'jquery-ui.js'=>false,
			'jquery-ui.min.js'=>false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_milestone_form', array(
            	'model'=> $model,
    			'id_project'=>$_POST['id_project'],
        	), true, true)));
       exit;
	}
	public function loadModel($id){
		$model = Projects::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function checksendMilestonesEmail($project){
		$notif = EmailNotifications::getNotificationByUniqueName('milestone_proj_new');
		if ($notif != NULL) {
    		if 	(!EmailNotifications::isNotificationSent($project, 'projects', $notif['id'])){
		    	$to_replace = array(
				'{body}');
				$count=Yii::app()->db->createCommand("select (total = finished) from(
				(select count(*) as total from projects_milestones where id_project = ".$project.") as q1,
				(select count(*) as finished from projects_milestones where id_project =  ".$project." and estimated_date_of_completion<>'0000-00-00' and  estimated_date_of_start <>'0000-00-00' and  applicable is not null and applicable<>'' ) as q2)")->queryScalar();
			if($count>0){
    				$pinfo= Yii::app()->db->createCommand("select project_manager, business_manager, template from projects  where id  = ".$project." ")->queryAll();
					$milestones= Yii::app()->db->createCommand("SELECT description,applicable,DATE_FORMAT(estimated_date_of_start,'%d/%m/%Y') as estimated_date_of_start,DATE_FORMAT(estimated_date_of_completion,'%d/%m/%Y') as estimated_date_of_completion FROM `projects_milestones` left JOIN milestones on id_milestone=milestones.id where id_project=".$project." order by milestones.milestone_number")->queryAll();
    				$body1="Dear All,<br/><br/>Kindly note that project <b>".Projects::getNameById($project)."</b> has been created with the below information:<br/><br/>";
		    		$body1.="- <b>PM</b>: ".Users::getNameById($pinfo[0]['project_manager'])."<br/>- <b>BM</b>: ".Users::getNameById($pinfo[0]['business_manager'])."<br/><br/>";
	    			$body1.="<table border='1'  style='font-family:Calibri;'><b><tr><td>Milestone Name</td><td >Applicable</td><td>Start Date</td><td>End Date</td></tr></b>";
	    			foreach ($milestones as $milestone) {
	    				if($pinfo[0]['template'] == 2 && strpos($milestone['description'], 'SOP') !== false )
	    				{
	    					$descr = str_replace("SOP","Integration",$milestone['description']);
	    				}else{
	    					$descr= $milestone['description'];
	    				}
	    				$body1.="<tr><td>".$descr."</td><td align=center>".$milestone['applicable']."</td><td style='padding-left:2px;padding-right:2px;'>".$milestone['estimated_date_of_start']."</td><td style='padding-left:2px;padding-right:2px;'>".$milestone['estimated_date_of_completion']."</td></tr>";
	    			}
	    			$body1.="</table><br/><br/>Best Regards,<br/>SNSit";
	    			$replace = array(
					$body1);
					$body = str_replace($to_replace, $replace, $notif['message']);
					$subject = $notif['name'];
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					$emailPM= Projects::getProjectManagerEmail($project);
					if (!empty($emailPM)){
						Yii::app()->mailer->AddAddress($emailPM);
					}
					$emailBM= Projects::getBusinessManagerEmail($project);
					if (!empty($emailBM)){
						Yii::app()->mailer->AddAddress($emailBM);
					}
					foreach($emails as $email){
						Yii::app()->mailer->AddAddress($email);
					} 
					Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if(Yii::app()->mailer->Send(true)){
						EmailNotifications::saveSentNotification($project, 'projects', $notif['id']);	
						return true; 
					}
	   			}		    			
			}
    	}
	}
	public function actionGetParentProjectsMaintByClient($id){
		$this->layout='';
		echo json_encode(Projects::getAllParentProjectsMaintSelect((int)$id));
		exit();
	}
	public function actionGetParentProjectsByClient($id){
		$this->layout='';
		echo json_encode(Projects::getAllParentProjectsSelect((int)$id));
		exit();
	}		
	public function actionGetCityByClientsMulti(){
		$this->layout='';
		$customers=$_GET['id'];

		echo json_encode(array('result'=>Customers::getCityPernames($customers)));
		exit();
	}
	public function actionGetProjectsByClientsMulti(){
		$this->layout='';
		$customers=$_GET['id'];
		echo json_encode(Projects::getAllProjectsSelect2($customers));
		exit();
	}
	public function actionGetProjectsAndInternalByClient($id){
		$this->layout='';
		echo json_encode(Projects::getAllProjectsAndInternalSelect((int)$id));
		exit();
	}

	public function actionGetProjectsByClient($id){
		$this->layout='';
		echo json_encode(Projects::getAllProjectsSelect((int)$id));
		exit();
	}	
	public function actionGetPhasesByProjectTimesheetReport($id){
		$this->layout='';
		echo json_encode(ProjectsPhases::getAllPhasesTimesheetReport((int)$id));
		exit();
	}
	public function actionGetProjectsByClientTimesheetReport($id){
		$this->layout='';
		echo json_encode(Projects::getAllProjectsTimesheetReport((int)$id));
		exit();
	}
	public function actionGetProjectsByClientName(){
		$name=$_GET['id'];
		$this->layout='';
		$id=yii::app()->db->createCommand("Select id from customers where name='".$name."'")->queryScalar();
		echo json_encode(Projects::getAllProjectsSelect((int)$id));
		exit();
	}
	public function actionGetProjectsByClientUser($id){
		$this->layout='';
		echo json_encode(Projects::getUserCustomerProjects($id));
		exit();
	}
	public function actionGetProjectsTrainingsByClient($id,$exp = false){
		$this->layout='';
		echo json_encode(Projects::getAllProjectsTrainingsSelect((int)$id,$exp));
		exit();
	}
	public function actionGetProjectsTrainingsByClientName(){	
		$exp = false;	
		$name=$_GET['id'];
		$this->layout='';
		$id=yii::app()->db->createCommand("Select id from customers where name='".$name."'")->queryScalar();
		echo json_encode(Projects::getAllProjectsTrainingsSelect((int)$id,$exp));
		exit();
	}
	public function actionGetCurrencyRate($id){
		$y =   Yii::app()->db->createCommand()
				->select('rate')
    			->from('currency_rate')
    			->where('currency = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryScalar();
    	if($y)
    		echo json_encode(array('status' => 'success', 'rate'=>$y));
    	else{
    		$y =   Yii::app()->db->createCommand()
				->select('rate')
    			->from('support_rate')
    			->where('plan = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryScalar();
    		if($y)
    		{	
    			echo json_encode(array('status' => 'success', 'supportrate'=>$y));
			}
			else{
				$y =   Yii::app()->db->createCommand()
				->select('template')
				->from('receivables_template_emails')
				->where('id_codelkup = :id', array(':id'=>$id))
				->order('id DESC')
				->limit('1')
				->queryScalar();
				if($y)
					echo json_encode(array('status' => 'success', 'template'=>$y));
				else{
					// should I add a model ?
				$y =   Yii::app()->db->createCommand()
				->select('Amount')
				->from('yearly_sales')
				->where('id_codelkup = :id', array(':id'=>$id))
				->order('id DESC')
				->limit('1')
				->queryScalar();
				if($y)
					echo json_encode(array('status' => 'success', 'yearlyamount'=>$y));
				else
					echo json_encode(array('status' => 'success'));
				}
			}
    	}
		exit();
	}	
	private function returnProjectId($i) {
	    return $i['id'];
	}
	public function actionGetAlerts(){
		Yii::app()->db->createCommand("DELETE FROM projects_alerts WHERE 1")->execute();
		$projects =   Yii::app()->db->createCommand()
			->select('*')
			->from('projects')
			->where('(id_type = 26 || id_type = 27) && status='.Projects::STATUS_ACTIVE.' && (deactivate_alerts IS NULL OR deactivate_alerts <> "Yes")')
			->queryAll();
		echo "project number: ".count($projects);
		$alerts =   Yii::app()->db->createCommand()
			->select('*')
			->from('alerts')
			->queryAll();
		$alertsData = array();
		foreach ($projects as $key => $project){
			$count=Yii::app()->db->createCommand("SELECT count(1) FROM projects_milestones pm WHERE pm.id_milestone IN ('15','9') AND (pm.`status`='Pending' or pm.`status`='In Progress' )AND pm.id_project='".$project['id']."'
			AND NOT EXISTS (SELECT 1 FROM projects_milestones pm2 WHERE pm2.id_milestone NOT IN ('15','9') AND (pm2.`status`='Pending' or  pm2.`status`='In Progress')AND pm2.id_project =pm.id_project) 
			ORDER BY pm.id_project 	")->queryScalar();
			$projectAlerts = array();
			if($count=='0'){					
					foreach ($alerts as $alert){
						if ($alert['id_category'] == $project['id_type'])
						{
					$name = 'test'.$alert['id'];
					echo "\n<br/>Project ".$project['id']." Alert ".$alert['id'];
					if (method_exists('ProjectsAlerts', $name)){
						$projectAlerts[] = ProjectsAlerts::$name($project);
					}
						}
					}
			}else{	
				if($project['id_type']== '27'){
						$name = 'test'.'7';
					}else{
						$name = 'test'.'18';
					} 				
					if (method_exists('ProjectsAlerts', $name)){
						$projectAlerts[] = ProjectsAlerts::$name($project);
					}
					$alert=$project['id_type']-1;
					echo "\n<br/>Project ".$project['id']." Alert ".$alert."";
					$projectAlerts[] .= (int) ($alert) ;
				}
				$projectAlerts = array_filter($projectAlerts);
				$projectAlerts = implode(',', $projectAlerts);
				$alertsData[] = "(".$project['id'].',"'.$projectAlerts."\", NOW())";
			
		}
		echo "\n<br/>";
		print_r($alertsData);
		if (!empty($alertsData)){
			Yii::app()->db->createCommand("INSERT INTO  projects_alerts(`id_project`, `alerts`, `date`) VALUES ".implode(',', $alertsData))->execute();
		}
	}
		public function actionGetAlertsPerProject(){
		$id=$_POST['selected'];
		Yii::app()->db->createCommand("DELETE FROM projects_alerts WHERE id_project=".$id."")->execute();
		$projects =   Yii::app()->db->createCommand()
			->select('*')
			->from('projects')
			->where('id='.$id.' and (id_type = 26 || id_type = 27) && status='.Projects::STATUS_ACTIVE.' && (deactivate_alerts IS NULL OR deactivate_alerts <> "Yes")')
			->queryAll();
		$alerts =   Yii::app()->db->createCommand()
			->select('*')
			->from('alerts')
			->queryAll();
		$alertsData = array();
		foreach ($projects as $key => $project){
			$projectAlerts = array();
			foreach ($alerts as $alert){
				if ($alert['id_category'] == $project['id_type']){
					$name = 'test'.$alert['id'];				
					if (method_exists('ProjectsAlerts', $name)){
						$projectAlerts[] = ProjectsAlerts::$name($project);
					}
				}
			}
			$projectAlerts = array_filter($projectAlerts);
			$projectAlerts = implode(',', $projectAlerts);
			$alertsData[] = "(".$project['id'].',"'.$projectAlerts."\", NOW())";
		}	
		if (!empty($alertsData)){
			Yii::app()->db->createCommand("INSERT INTO  projects_alerts(`id_project`, `alerts`, `date`) VALUES ".implode(',', $alertsData))->execute();
		}
	}
	private function sendNotificationsEmails($name, $id_project,$ids_users){
		$notif = EmailNotifications::getNotificationByUniqueName($name);
		$id_project = (int) $id_project;
		if ($notif != NULL && !empty($ids_users) && $id_project > 0) {
    		$project_data = Yii::app()->db->createCommand("SELECT c.name as customer, p.name as projectname , cd.codelkup as country, cdk.codelkup as category  
    			FROM projects p LEFT JOIN customers c ON c.id=p.customer_id LEFT JOIN codelkups cd ON cd.id=c.country 
    			LEFT JOIN codelkups cdk ON cdk.id=p.id_type WHERE p.id = {$id_project}")->queryRow();
    		$users_ids = implode(',', $ids_users);
    		$emails = Yii::app()->db->createCommand("SELECT u.id, u.firstname, u.lastname, d.email FROM users u LEFT JOIN user_personal_details d
    			ON d.id_user=u.id WHERE u.id IN ($users_ids) ")->queryAll();
    		foreach ($emails as $user_email){
    			if (!empty($user_email['email'])){
		    		if (ProjectsEmails::isNotificationSent($id_project, $user_email['id']) == false){		    			
		    			$category = $project_data['category'];
		    			$customer = $project_data['customer'];
		    			$country = $project_data['country'];
						$project = $project_data['projectname'];
		    			$subject = $notif['name'];
		    			$to_replace_sub = array(							
							'{project}' ,
						);
						$replace_sub = array(							
							$project							
						);
		    			$to_replace = array(
							'{firstname}',
							'{eaCategory}', 
							'{customer}', 
							'{country}', 
							'{project}'
						);
						$replace = array(
							$user_email['firstname'],
							$category,
							$customer,
							$country,
							$project							
						);
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->AddAddress($user_email['email']);
						Yii::app()->mailer->Subject  = str_replace($to_replace_sub, $replace_sub,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true))
						{
							EmailNotifications::saveSentProjectNotification($id_project, $user_email['id']);	
						}
					}
	    		}
	    	}
    	}
	}
	
	private function sendNotificationsEmailsIssue($name, $id_project,$ids_users, $tasks){
		$notif = EmailNotifications::getNotificationByUniqueName($name);
		$id_project = (int) $id_project;
		if ($notif != NULL && !empty($ids_users) && $id_project > 0) {
			$project_data = Yii::app()->db->createCommand("SELECT c.name as customer, p.name as projectname FROM projects p LEFT JOIN customers c ON c.id=p.customer_id WHERE p.id = {$id_project}")->queryRow();
    		$users_ids = implode(',', $ids_users);
    		$emails = Yii::app()->db->createCommand("SELECT u.id, u.firstname, u.lastname, d.email FROM users u LEFT JOIN user_personal_details d
    			ON d.id_user=u.id WHERE u.id IN ($users_ids) ")->queryAll();
    		foreach ($emails as $user_email){
    			if (!empty($user_email['email'])){

    					$issues= Yii::app()->db->createCommand("SELECT description FROM `projects_issues` where id in (".$tasks.")")->queryAll();
    				//	print_r($issues);exit;
    					$issuestr= "";
    					foreach ($issues as $issue) {
    						$issuestr .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ".$issue['description'];
    					}
		    		    			
		    			$customer = $project_data['customer'];
						$project = $project_data['projectname'];
		    			$subject = $notif['name'].' - '.$customer;
		    			$to_replace_sub = array(							
							'{project}' ,
						);
						$replace_sub = array(							
							$project							
						);
		    			$to_replace = array(
							'{firstname}',							
							'{customer}', 
							'{project}',
							'{issue}'
						);
						$replace = array(
							$user_email['firstname'],
							$customer,
							$project,
							$issuestr
						);
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->AddAddress($user_email['email']);
						Yii::app()->mailer->Subject  = str_replace($to_replace_sub, $replace_sub,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send(true);
					
				}
			}
		}
	}
	private function sendNotificationsEmailsTask($name, $id_project,$ids_users){
		$notif = EmailNotifications::getNotificationByUniqueName($name);
		$id_project = (int) $id_project;
		if ($notif != NULL && !empty($ids_users) && $id_project > 0) {
    		$project_data = Yii::app()->db->createCommand("SELECT c.name as customer, p.name as projectname , cd.codelkup as country, cdk.codelkup as category , p.business_manager as bm, p.project_manager as pm 
    			FROM projects p LEFT JOIN customers c ON c.id=p.customer_id LEFT JOIN codelkups cd ON cd.id=c.country 
    			LEFT JOIN codelkups cdk ON cdk.id=p.id_type WHERE p.id = {$id_project}")->queryRow();
    		$users_ids = implode(',', $ids_users);
    		$emails = Yii::app()->db->createCommand("SELECT u.id, u.firstname, u.lastname, d.email FROM users u LEFT JOIN user_personal_details d
    			ON d.id_user=u.id WHERE u.id IN ($users_ids) ")->queryAll();
    		foreach ($emails as $user_email){
    			if (!empty($user_email['email'])){
		    		if (ProjectsEmails::isNotificationSent($id_project, $user_email['id']) == false){		    			
		    			$category = $project_data['category'];
		    			$customer = $project_data['customer'];
		    			$country = $project_data['country'];
						$project = $project_data['projectname'];
						$bm = Users::getNamebyId($project_data['bm']);
						$pm = Users::getNamebyId($project_data['pm']);
		    			$subject = $notif['name'];
		    			$to_replace_sub = array(							
							'{project}' ,
						);
						$replace_sub = array(							
							$project							
						);
		    			$to_replace = array(
							'{firstname}',
							'{eaCategory}', 
							'{customer}', 
							'{country}', 
							'{project}',
							'{bm}',
							'{pm}'
						);
						$replace = array(
							$user_email['firstname'],
							$category,
							$customer,
							$country,
							$project,
							$bm,
							$pm
						);
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->AddAddress($user_email['email']);
						Yii::app()->mailer->Subject  = str_replace($to_replace_sub, $replace_sub,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							EmailNotifications::saveSentProjectNotification($id_project, $user_email['id']);	
						}
					}
	    		}
	    	}
    	}
	}
	private function sendLessonsProject($id_project){
		$notif = EmailNotifications::getNotificationByUniqueName('project_lessons');
		$id_project = (int) $id_project;
		$lessons= Yii::app()->db->createCommand("SELECT lessons FROM projects_lessons WHERE id_project = ".$id_project." ")->queryScalar();
		if ($notif != NULL && $id_project > 0 && !empty($lessons) && EmailNotifications::isNotificationSent($id_project, 'projects_lessone', $notif['id']) == false) {
			$to_replace = array(
				'{group_description}',
				'{body}',
			);	
			$lessonText="Dear All,<br/><br/>Please find below the lessons learned on project <b>".Projects::getNamebyId($id_project)."</b>:<br/><br/>".$lessons."<br/><br/>Best Regards,<br/>SNSit";
			$subject = $notif['name'];
			$replace = array(
					EmailNotificationsGroups::getGroupDescription($notif['id']),
					$lessonText,
			);
			$body = str_replace($to_replace, $replace, $notif['message']);			
			Yii::app()->mailer->ClearAddresses();
			Yii::app()->mailer->ClearCcs();	
			$emailPM= Projects::getProjectManagerEmail($id_project);
			$emailLM= Projects::getBusinessManagerEmail($id_project);
			if (!empty($emailPM)){
				Yii::app()->mailer->AddAddress($emailPM);
			}
			if (!empty($emailLM)){
				Yii::app()->mailer->AddAddress($emailLM);
			}						
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			if (!empty($emails)){
				foreach($emails as $email){
					//Yii::app()->mailer->AddCcs($email);
					Yii::app()->mailer->AddAddress($email);
				}
			}
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){
				EmailNotifications::saveSentNotification($id_project, 'projects_lessone', $notif['id']);	
			}			
    	}
	}
	private function sendNotificationsEmailsClosed($name, $id_project){
		$notif = EmailNotifications::getNotificationByUniqueName($name);
		$id_project = (int) $id_project;
		if ($notif != NULL ) {
    		if 	(EmailNotifications::isNotificationSent($id_project, 'projects', $notif['id']) == false) 
    		{
    				$project_data = Yii::app()->db->createCommand("SELECT c.name as customer, p.id_type as type ,  p.name as projectname , cd.codelkup as country, cdk.codelkup as category, p.id as id , p.template
    					FROM projects p LEFT JOIN customers c ON c.id=p.customer_id LEFT JOIN codelkups cd ON cd.id=c.country 
    					LEFT JOIN codelkups cdk ON cdk.id=p.id_type WHERE p.id = {$id_project}")->queryRow();
    				$checkTM=Yii::app()->db->createCommand("SELECT TM FROM eas WHERE id_project = '".$project_data['id']."' ")->queryScalar();
  					$subtype= Eas::getTemplateLabel($project_data['template']);
  					if ($checkTM == '1'){
  						$notif = EmailNotifications::getNotificationByUniqueName('project_closed_TM');
  						$project = $project_data['projectname'];
  						$projectmanager= Projects::getProjectManager($id_project);
  						$category = $project_data['category'];
  						$ActualMDs=Projects::getActualMD($id_project);
  						$ActualMD=($ActualMDs=='0')?'1':Utils::formatNumber($ActualMDs);						
		    			$customer = $project_data['customer'];		    			
		    			$country = $project_data['country'];
						$project = $project_data['projectname'];
						$type = $project_data['type'];
						$checkinv= Invoices::checkinvPending($id_project);
		    			$subject = $notif['name'];
  						$to_replace_sub = array(							
							'{project}' ,
							'{customer}' 
						);
						$replace_sub = array(							
							$project,
							$customer							
						);
		    			$to_replace = array(						
							'{project}' ,
							'{projectmanager}',
							'{category}',
							'{subtype}',
							'{ActualMD}',
							' {checkinv} ',	
						);
						$replace = array(							
							$project,
							$projectmanager,
							$category,
							$subtype,
							$ActualMDs,
							$checkinv
						);
  					}else{
    					$OverRun=0;    		
		    			$ActualMDs=Projects::getActualMD($id_project);
			 			$BudgetedMDs=Projects::getBudgetedMD($id_project);
			 			$BudgetedMD=($BudgetedMDs=='0')?'1':$BudgetedMDs;
			 			if($BudgetedMDs==0){
			 				$OverRun=0;
			 			}else{
			 				$OverRun=Utils::formatNumber((($BudgetedMDs-$ActualMDs)*100)/$BudgetedMDs);
			 			}
			 			$ActualMD=($ActualMDs=='0')?'1':Utils::formatNumber($ActualMDs);
			 			$ActualRate=Utils::formatNumber(Projects::getNetAmount($id_project)/$ActualMD);
			 			if($BudgetedMDs==0){
			 				$BudgetedMDRate=Utils::formatNumber(Projects::getNetAmount($id_project)/1);
			 			}else{
			 				$BudgetedMDRate=Utils::formatNumber(Projects::getNetAmount($id_project)/$BudgetedMD);
						}
			 			$offset=Projects::getTotalOffsetMD($id_project) ;
			 			$offsetcount= Projects::getTotalOffset($id_project) ; 
			 			$projectmanager= Projects::getProjectManager($id_project);
		    			$category = $project_data['category'];
		    			$customer = $project_data['customer'];	
		    			$projecttype=Codelkups::getCodelkup($project_data['type']);   			
		    			$country = $project_data['country'];
						$project = $project_data['projectname'];
						$type = $project_data['type'];
						$checkinv= Invoices::checkinvPending($id_project);
		    			$subject = $notif['name'];
		    			$to_replace_sub = array(							
							'{project}' ,
							'{customer}' 
						);
						$replace_sub = array(							
							$project,
							$customer							
						);
		    			$to_replace = array(						
							'{project}' ,
							'{projectmanager}',
							'{projecttype}',
							'{category}',
							'{subtype}',
							'{Budgeted}',
							'{ActualMD}',
							'{OverRun}',
							'{ActualMDRate}',
							'{BudgetedMDRate}',
							'{offset}',
							'{offsetcount}',
							'{checkinv}',			
						);
						$replace = array(							
							$project,
							$projectmanager,
							$projecttype,
							$category,
							$subtype,
							$BudgetedMDs ,
							$ActualMDs,
							$OverRun,
							$ActualRate ,
							$BudgetedMDRate ,
							$offset ,
							$offsetcount,
							$checkinv,							
						);
					}
					$body = str_replace($to_replace, $replace, $notif['message']);
						$emails = Yii::app()->db->createCommand("
							SELECT d.email FROM users u LEFT JOIN user_personal_details d
		    				ON d.id_user=u.id WHERE u.id IN (
		    				select id_user from user_groups ug ,email_notifications_groups en where ug.id_group=en.id_group and en.id_email_notification=41
							union 
							select project_manager from projects where id=".$id_project."
							union 
							select business_manager from projects where id=".$id_project." ) ")->queryColumn();
						Yii::app()->mailer->ClearAddresses();
						foreach ($emails as $user_email) {
							Yii::app()->mailer->AddAddress($user_email);
    					}	
    					//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
    					if($type =='26' || $type='27'){
    						Yii::app()->mailer->AddAddress('claudia.daaboul@sns-emea.com');		
    					}						
						Yii::app()->mailer->Subject  = str_replace($to_replace_sub, $replace_sub,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
								EmailNotifications::saveSentNotification($id_project, 'projects', $notif['id']);	
								return true; 
							}
				}								
    		}
	}
	public function actionSendNotif(){
		$this->sendNotificationsEmailsMilestone('milestone', 8, 1);
	}
	public function actionsendNotificationsEmailsAlerts(){
		$dear=true;
		$pms=Yii::app()->db->createCommand("select DISTINCT project_manager from projects where status=1 and deactivate_alerts='No' and project_manager is not null and (exists (select 1 from projects_alerts pa where pa.id_project=projects.id and pa.alerts<>'' and pa.alerts<>' ') or exists (select 1 from projects_risks pr where pr.id_project=projects.id))")->queryAll();
		foreach($pms as $pm_value) {
			$pm=$pm_value['project_manager'];
			$dear=true;
		if($pm!=null){
			$notif = EmailNotifications::getNotificationByUniqueName('project_alerts');
			if(!EmailNotifications::isNotificationPAlertSent($pm, 'project alerts', $notif['id']))
			{
				$projects = Yii::app()->db->createCommand("select id from projects where project_manager=".$pm." and status=1 and deactivate_alerts='No' ")->queryAll();
				$email_body=" ";
				foreach ($projects as $project) {
					$id_project=$project['id'];
					$alerts = Projects::getProjectAlerts($id_project,'alerts');
					$warnings = Projects::getProjectAlerts($id_project,'warnings');
					$risks= Projects::getProjectRisks($id_project);			
					$customerID= Projects::getCustomerByProject($id_project);	
					$pendingamnt= Receivables::gettotalnotpaidandAll($customerID);
					if(!empty($pendingamnt) && $pendingamnt>0 && $pendingamnt!=null && isset($pendingamnt)){
						$avgpendingage= Receivables::getavgageNotPaidAll($customerID);
						$maxpendingage= Receivables::getmaxAgePendingAll($customerID);				
						$rep= Users::getNameById(Customers::getIdAssigned($customerID));
					}
					$name=Projects::getNameById($id_project);
					$project_m=Projects::getProjectManager($id_project);
					$business_m=Projects::getBusinessManager($id_project);
					$phase= Projects::getPhase($id_project);
					if($dear){
						$email_body ="Dear ".$project_m.",<br /><br />Kindly find below the following Projects' Alerts:<br /><br />";
						$dear=false;
					}
					$url=Yii::app()->createAbsoluteUrl('projects/view', array('id'=>$id_project));
					$email_body .="<a href=".$url.">".$name."</a><br /><br /> BM: ".$business_m."<br />Current Phase: ".$phase."<br /><br />";
					if($alerts!='' && $alerts!=' ' && !empty($alerts)){ 
						$email_body .="<b>Alerts:</b> <br/>".$alerts."<br />";
					}
					if($risks!='' && $risks!=' ' && !empty($risks)){ 
						$email_body .="<b>Risks:</b> <br/>".$risks."<br />";
					}else{
						$email_body .="<b>Risks:</b> <br/>None Specified.<br /><br />";

					}
					if($warnings!='' && $warnings!=' ' && !empty($warnings)){ 
						$email_body .="<b>Warnings:</b> <br/>".$warnings."<br />";
					} 
					if(!empty($pendingamnt) && $pendingamnt>0 && $pendingamnt!=null && isset($pendingamnt)){ 
						$email_body .="<b>Pending Invoices:</b> <br/>Total Amount: ".Utils::formatNumber($pendingamnt,2)." $<br />Average Age: ".Utils::formatNumber($avgpendingage,2)." days<br />Max Age: ".Utils::formatNumber($maxpendingage,2)." days<br />Receivables Rep: ".$rep."<br /><br />";
					} 
				}
				if ($notif != NULL) {	    		
						$subject = 'Project Alerts & Risks- '.$project_m;
						$to_replace = array('{email_body}');
						$replace = array($email_body);
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails =EmailNotificationsGroups::getPMNotificationUsers($pm);
						$email_cc=EmailNotificationsGroups::getLMNotificationUsers($pm);
						$email_ccprime=EmailNotificationsGroups::getBMNotificationUsers($pm,$id_project);
						$email_directors=EmailNotificationsGroups::getDirectorsEmails();
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->ClearCcs();					
							if (!empty($emails))
								Yii::app()->mailer->AddAddress($emails);
							if(!empty($email_cc))
							{	//Yii::app()->mailer->AddCcs($email_cc);
							//	Yii::app()->mailer->AddAddress($email_cc);
							}
							if(!empty($email_ccprime)){
								foreach ($email_ccprime as $ccemail) {
							//	Yii::app()->mailer->AddCcs($ccemail['email']);
								//Yii::app()->mailer->AddAddress($ccemail['email']);
								}
							}	if(!empty($email_directors)){
								foreach ($email_directors as $demail) {
									//Yii::app()->mailer->AddCcs($demail['email']);
									//Yii::app()->mailer->AddAddress($demail['email']);
								}
							}
						//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");	
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							echo "Sent";
							EmailNotifications::saveSentNotification($pm, 'project alerts', $notif['id']);
						}    			
	    		} 
    		}
		}
		}
	}
	public function actionsendNotificationsEmailsAlertsSecond(){
		$dear=true;
		$pms=Yii::app()->db->createCommand("select DISTINCT project_manager from projects where status=1 and deactivate_alerts='No' and project_manager is not null and (exists (select 1 from projects_alerts pa where pa.id_project=projects.id and pa.alerts<>'' and pa.alerts<>' ') or exists (select 1 from projects_risks pr where pr.id_project=projects.id))")->queryAll();
		foreach($pms as $pm_value) {
			$pm=$pm_value['project_manager'];
			$dear=true;
		if($pm!=null){
			$notif = EmailNotifications::getNotificationByUniqueName('project_alerts');
			if(!EmailNotifications::isNotificationPAlertSent($pm, 'project alerts', $notif['id']))
			{
				$projects = Yii::app()->db->createCommand("select id from projects where project_manager=".$pm." and status=1 and deactivate_alerts='No' ")->queryAll();
				$email_body=" ";
				foreach ($projects as $project) {
					$id_project=$project['id'];
					$alerts = Projects::getProjectAlerts($id_project,'alerts');
					$warnings = Projects::getProjectAlerts($id_project,'warnings');
					$risks= Projects::getProjectRisks($id_project);			
					$customerID= Projects::getCustomerByProject($id_project);	
					$pendingamnt= Receivables::gettotalnotpaidandAll($customerID);
					if(!empty($pendingamnt) && $pendingamnt>0 && $pendingamnt!=null && isset($pendingamnt)){
						$avgpendingage= Receivables::getavgageNotPaidAll($customerID);
						$maxpendingage= Receivables::getmaxAgePendingAll($customerID);				
						$rep= Users::getNameById(Customers::getIdAssigned($customerID));
					}
					$name=Projects::getNameById($id_project);
					$project_m=Projects::getProjectManager($id_project);
					$business_m=Projects::getBusinessManager($id_project);
					$phase= Projects::getPhase($id_project);
					if($dear){
						$email_body ="Dear ".$project_m.",<br /><br />Kindly find below the following Projects' Alerts:<br /><br />";
						$dear=false;
					}
					$url=Yii::app()->createAbsoluteUrl('projects/view', array('id'=>$id_project));
					$email_body .="<a href=".$url.">".$name."</a><br /><br /> BM: ".$business_m."<br />Current Phase: ".$phase."<br /><br />";
					if($alerts!='' && $alerts!=' ' && !empty($alerts)){ 
						$email_body .="<b>Alerts:</b> <br/>".$alerts."<br />";
					}
					if($risks!='' && $risks!=' ' && !empty($risks)){ 
						$email_body .="<b>Risks:</b> <br/>".$risks."<br />";
					}else{
						$email_body .="<b>Risks:</b> <br/>None Specified.<br /><br />";

					}
					if($warnings!='' && $warnings!=' ' && !empty($warnings)){ 
						$email_body .="<b>Warnings:</b> <br/>".$warnings."<br />";
					} 
					if(!empty($pendingamnt) && $pendingamnt>0 && $pendingamnt!=null && isset($pendingamnt)){ 
						$email_body .="<b>Pending Invoices:</b> <br/>Total Amount: ".Utils::formatNumber($pendingamnt,2)." $<br />Average Age: ".Utils::formatNumber($avgpendingage,2)." days<br />Max Age: ".Utils::formatNumber($maxpendingage,2)." days<br />Receivables Rep: ".$rep."<br /><br />";
					} 
				}
				if ($notif != NULL) {	    		
						$subject = 'Project Alerts & Risks- '.$project_m;
						$to_replace = array('{email_body}');
						$replace = array($email_body);
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails =EmailNotificationsGroups::getPMNotificationUsers($pm);
						$email_cc=EmailNotificationsGroups::getLMNotificationUsers($pm);
						$email_ccprime=EmailNotificationsGroups::getBMNotificationUsers($pm,$id_project);
						$email_directors=EmailNotificationsGroups::getDirectorsEmails();
						Yii::app()->mailer->ClearAddresses();
						Yii::app()->mailer->ClearCcs();					
							if (!empty($emails))
								Yii::app()->mailer->AddAddress($emails);
							if(!empty($email_cc))
							{	//Yii::app()->mailer->AddCcs($email_cc);
							 	Yii::app()->mailer->AddAddress($email_cc);
							}
							if(!empty($email_ccprime)){
								foreach ($email_ccprime as $ccemail) {
							//	Yii::app()->mailer->AddCcs($ccemail['email']);
								 Yii::app()->mailer->AddAddress($ccemail['email']);
								}
							}	if(!empty($email_directors)){
								foreach ($email_directors as $demail) {
									//Yii::app()->mailer->AddCcs($demail['email']);
									 Yii::app()->mailer->AddAddress($demail['email']);
								}
							}
						//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");	
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							echo "Sent";
							EmailNotifications::saveSentNotification($pm, 'project alerts', $notif['id']);
						}    			
	    		} 
    		}
		}
		}
	}
public function actionsendNotificationsEmailsAlertsperPM(){
		$dear=true;		
		if(isset($_GET['id'])){
				$id_pm=$_GET['id'];
		}else {
			$pm=null;
			echo " no Project Manager has been set";
		}
		$pm=$id_pm;
		if($pm!=null){
			$notif = EmailNotifications::getNotificationByUniqueName('project_alerts');
			$projects = Yii::app()->db->createCommand("select id from projects where project_manager=".$pm." and status=1 and deactivate_alerts='No' ")->queryAll();
			$email_body=" ";
			foreach ($projects as $project) {
			$id_project=$project['id'];
			$alerts = Projects::getProjectAlerts($id_project,'alerts');
			$warnings = Projects::getProjectAlerts($id_project,'warnings');		
			$name=Projects::getNameById($id_project);
			$project_m=Projects::getProjectManager($id_project);
			$business_m=Projects::getBusinessManager($id_project);
			if($dear){
				$email_body ="Dear ".$project_m.",<br /><br />Kindly find below the following Projects' Alerts:<br /><br />";
				$dear=false;
			}
			if($alerts!='' && $alerts!=' ' && !empty($alerts) && $warnings!='' && $warnings!=' ' && !empty($warnings)){ 
				$url=Yii::app()->createAbsoluteUrl('projects/view', array('id'=>$id_project));
				$email_body .="<a href=".$url.">".$name."</a><br /><br /> BM: ".$business_m."<br /><br /> <b>Alerts:</b> <br/>".$alerts."<br /><br /> <b>Warnings:</b> <br/>".$warnings."<br /><br />";
			} else if($alerts!='' && $alerts!=' ' && !empty($alerts) ){ 
				$url=Yii::app()->createAbsoluteUrl('projects/view', array('id'=>$id_project));
				$email_body .="<a href=".$url.">".$name."</a><br /><br /> BM: ".$business_m."<br /><br /> <b>Alerts:</b> <br/>".$alerts."<br /><br />";
			} else if($warnings!='' && $warnings!=' ' && !empty($warnings)){ 
				$url=Yii::app()->createAbsoluteUrl('projects/view', array('id'=>$id_project));
				$email_body .="<a href=".$url.">".$name."</a><br /><br /> BM: ".$business_m."<br /><br /> <b>Warnings:</b> <br/>".$warnings."<br /><br />";
			}
			}
			if ($notif != NULL) 
    		{	    		
					$subject = 'Project Alerts - '.$project_m;
					$to_replace = array('{email_body}');
					$replace = array($email_body);					
					$body = str_replace($to_replace, $replace, $notif['message']);
					$emails =EmailNotificationsGroups::getPMNotificationUsers($pm);
					$email_cc=EmailNotificationsGroups::getLMNotificationUsers($pm);
					$email_ccprime=EmailNotificationsGroups::getBMNotificationUsers($pm,$id_project);
					$email_directors=EmailNotificationsGroups::getDirectorsEmails();
					Yii::app()->mailer->ClearAddresses();
					Yii::app()->mailer->ClearCcs();
						if (!empty($emails))
							Yii::app()->mailer->AddAddress($emails);
						if(!empty($email_cc))
						{	//Yii::app()->mailer->AddCcs($email_cc);
							Yii::app()->mailer->AddAddress($email_cc);
						}
						
						if(!empty($email_ccprime)){
							foreach ($email_ccprime as $ccemail) {
							//Yii::app()->mailer->AddCcs($ccemail['email']);
							Yii::app()->mailer->AddAddress($ccemail['email']);
							}
						}	if(!empty($email_directors)){
							foreach ($email_directors as $demail) {
							//Yii::app()->mailer->AddCcs($demail['email']);
							Yii::app()->mailer->AddAddress($demail['email']);
							}
						}	
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true)){
						echo "Sent";
						EmailNotifications::saveSentNotification($pm, 'project alerts', $notif['id']);
					}    			
    		}    	
		}
	}
	private function sendNotificationsEmailsMilestone($name, $id_project, $milestone_number){
		$notif = EmailNotifications::getNotificationByUniqueName($name);
		if ($notif != NULL) {
    		$milestone_number = (int) $milestone_number;
    		$pm_email=Yii::app()->db->createCommand("select up.email from user_personal_details up, projects p where up.id_user=p.project_manager and p.id=".$id_project." ")->queryScalar();
    		$pm_id=Yii::app()->db->createCommand("select up.id_user from user_personal_details up, projects p where up.id_user=p.project_manager and p.id=".$id_project." ")->queryScalar();
    		$bm_email=Yii::app()->db->createCommand("select up.email from user_personal_details up, projects p where up.id_user=p.business_manager and p.id=".$id_project." ")->queryScalar();
    		$pm_linemanager_email=Yii::app()->db->createCommand("select up.email from user_personal_details up where up.id_user=(SELECT up1.line_manager from user_personal_details up1 where up1.id=".$pm_id." )")->queryScalar();
			$projectname = Projects::getId('name',$id_project);
			$milestone_name=ProjectsMilestones::getMilestoneNameByNumber($id_project,$milestone_number);
			$to_replace = array(
				'{projectname}',
				'{number}', 
			);
			$replace = array(
				$projectname,
				$milestone_name,
				
			);
			$body = "";
			$subject = str_replace($to_replace, $replace, $notif['name']);
			Yii::app()->mailer->ClearAddresses();
			Yii::app()->mailer->AddAddress($pm_email);
			Yii::app()->mailer->AddAddress($bm_email);
			Yii::app()->mailer->AddAddress($pm_linemanager_email);			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){
				return true; 
			}
    	}
    	return false;
	}
	public function actionAlerts(){
		if(!GroupPermissions::checkPermissions('alerts-project_alerts')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/projects/alerts' => array(
								'label'=>Yii::t('translations','Project Alerts'),
								'url' => array('projects/alerts'),
								'itemOptions' => array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1
						),
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model = new ProjectsAlerts('getAllProjectAlerts');
		$this->render('alerts_list', array( 'model' => $model));
	}
	public function actionTestProjectActualExpenses(){
		if (count($_POST) > 0){
			$id_project = (int)$_POST['id_project'];			
			$type = Yii::app()->db->createCommand()
				->select('expense')
				->from('eas')
				->where('id_project = :id_project', array(':id_project' => $id_project))
				->queryScalar();			
			if ($type == 'Actuals'){
				echo json_encode(array('code' => 200, 'type' => 'true'));
			}else{
				echo json_encode(array('code' => 200, 'type' => 'false'));
			}			
			return;
		}
	}
	public function actiongetBillablity(){
		if (count($_POST) > 0){
			$id_project = $_POST['id_project'];	
			$projects=$id_project;//implode(',', $id_project);			
			$type = Yii::app()->db->createCommand("select expense from eas where id_project in (".$projects.") group by expense")->queryAll();
			$str = implode(',',array_map('implode',$type));
			if(strpos($str, 'Actuals') !== false){
				echo json_encode(array('code' => 200, 'type' => 'true'));
			}else{
				echo json_encode(array('code' => 200, 'type' => 'false'));
			}			
			return;
		}
	}	
	public function actiontimesheetFlag(){
		if (count($_POST) > 0){
				$id= $_POST['id'];
				$val=$_POST['val'];
				$update_assigned = Yii::app()->db->createCommand("update projects_status_reports set incluetimesheet=".$val." where id =".$id." ")->execute();
				echo json_encode(array('status' =>'success'));
		}
	}
	public function actionchecklistFlag(){
		if (count($_POST) > 0){
				$id= $_POST['id'];
				$val=$_POST['val'];
				$update_assigned = Yii::app()->db->createCommand("update projects_status_reports set includechecklist=".$val." where id =".$id." ")->execute();
				echo json_encode(array('status' =>'success'));
		}
	}
	public function actioninvoicesFlag(){
		if (count($_POST) > 0){
				$id= $_POST['id'];
				$val=$_POST['val'];
				$update_assigned = Yii::app()->db->createCommand("update projects_status_reports set invoicesflag=".$val." where id =".$id." ")->execute();
				echo json_encode(array('status' =>'success'));
		}
	}
	public function actionCreateScenarioItem(){
		$model = new ProjectsScenarios();		
		if (isset($_POST['ProjectsScenarios'])){
   			$model->attributes = $_POST['ProjectsScenarios'];
   			$model->id_project= isset($_GET['id_project']) ? $_GET['id_project'] : Utils::getSearchSession();				
   			if ($model->save()){	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_scenario_form', array(
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
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_scenario_form', array(
            	'model'=> $model,'id_project'=>isset($_GET['id_project']) ? $_GET['id_project'] : Utils::getSearchSession() 
				), true, true)));
        exit;
	}
public function actioncreateIssue(){
		$model = new ProjectsIssues();
		$model->id_project= isset($_GET['id_project']) ? $_GET['id_project'] : Utils::getSearchSession();
		$model->logged_by=Yii::app()->user->id;
   			$model->logged_date=date('Y-m-d');
   			$model->lastupdateby=Yii::app()->user->id;
   			$model->lastupdateddate=date('Y-m-d');
   			$model->id_issue= ProjectsIssues::getlastissue($model->id_project);
   			$model->type= "FBR";
   			$model->description= "Descr: ";
   			$model->priority= 1;
   			$model->status= 0;
			$model->save();	
		if (isset($_POST['ProjectsIssues'])){
   			$model->attributes = $_POST['ProjectsIssues'];	
   			$model->lastupdateby=Yii::app()->user->id;
   			$model->lastupdateddate=date('Y-m-d');
   			if ($model->save()){
   				if($model->priority == 2){
					self::sendHighIssue($model->id_project, $model->id_issue, $model->description);
   				}	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_issues_form', array(
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
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_issues_form', array(
            	'model'=> $model,'id_project'=>isset($_GET['id_project']) ? $_GET['id_project'] : Utils::getSearchSession() 
				), true, true)));
        exit;
	}
	public function sendHighIssue($project, $issue, $description){

		$emailstr="Dears, <br /><br /> Please note that a high priority issue has been logged on project ".Projects::getNameById($project).", Issue# ".$issue.":".$description." <br/><br/>Best Regards,<br />SNSit";
		
		$notif = EmailNotifications::getNotificationByUniqueName('issues_weekly');
			$to_replace = array(
				
				'{body}',
			);
					$subject = Projects::getNameById($project).' - High Priority Issue';
					$replace = array(
							$emailstr,	
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->ClearAddresses();
					
					$emailpro = ProjectsIssues::getUsersProj($project);
					foreach($emailpro as $emailp)	{
						Yii::app()->mailer->AddAddress($emailp['email']);
					}
					$emailPM= Projects::getProjectManagerEmail($project);
					if (!empty($emailPM)){
						Yii::app()->mailer->AddAddress($emailPM);
					}
					$emailBM= Projects::getBusinessManagerEmail($project['id']);
					if (!empty($emailBM)){
						Yii::app()->mailer->AddAddress($emailBM);
					}
					$replace = array(
							$emailstr,	
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);	

	}
	public function actionCreateRiskItem(){
		$model = new ProjectsRisks();		
		if (isset($_POST['ProjectsRisks'])){
   			$model->attributes = $_POST['ProjectsRisks'];
   			$model->id_project= isset($_GET['id_project']) ? $_GET['id_project'] : Utils::getSearchSession();				
   			if ($model->save()){	
				echo json_encode(array('status'=>'saved',  'form'=>$this->renderPartial('_risk_form', array(
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
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_risk_form', array(
            	'model'=> $model,'id_project'=>isset($_GET['id_project']) ? $_GET['id_project'] : Utils::getSearchSession() 
				), true, true)));
        exit;
	}
	public function actioncreateStatusReport($id){		
		$model = new ProjectsStatusReports;
		$model->project = $id;
		$model->date = date('Y-m-d',strtotime('now'));
		$model->report_num = Utils::paddingCodeSmall(Yii::app()->db->createCommand("select count(*)+1 from projects_status_reports where project =".$id)->queryScalar());
		$model->title =  date('d/m/Y',strtotime('now')).' Report #'.$model->report_num;
		if($model->validate()){
					if($model->save()){
					$model->save();
					$this->redirect(array('projects/updatestatusreport', 'id'=>$model->id, 'new' => 1));
					}
				}
	}
	public function actionUpdateStatusReport($id,$new =0){
		if (!GroupPermissions::checkPermissions('general-projects','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/projects/updatestatusreport/'.$id=> array(
							'label'=>Yii::t('translations', 'New Status Report'),
							'url' => array('projects/updatestatusreport', 'id' =>$id),
							'itemOptions'=>array('class'=>'link'),
							'subtab' =>  '',
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$id = (int) $id;
		$model = ProjectsStatusReports::model()->findByPk($id);
		if($new == 1){
			$model_health_indicators = new StatusReportHealthIndicators;
			$model_health_indicators->project_scope=1;
			$model_health_indicators->resources=1;
			$model_health_indicators->timeline=1;
			$model_health_indicators->project_finance=1;
			$model_health_indicators->risks_issues=1;
			$model_health_indicators->overall_project_health=1;
			$model_health_indicators->indicators_date=1; 
			$model_health_indicators->format=1; 
			$new = false;
		}
		if(isset($_POST['StatusReportHealthIndicators'])){		
			$date=date('Y-m-d',strtotime("now"));
			$x=  ProjectsStatusReports::checksameDayReport($model->project, $date);			
			$model_health_indicators->attributes = $_POST['StatusReportHealthIndicators'];
			$model_health_indicators->indicators_date  = $date;
			$model_health_indicators->status_report = $model->id;
			if($model_health_indicators->validate()){
				$model_health_indicators->save();
				if ($model_health_indicators->format==1)
				{
					if($this->generatePdf('project_status_report', $model->project)){
						echo json_encode(array('status' => 'sent'));
						Yii::app()->end(); 
					}	
				}else{
					self::getExcelReport($model->project);
				}
			}
			echo json_encode(array('status' => 'sent'));
					Yii::app()->end(); 
		}else{
			$this->render('updatestatusreport', array('model' => $model,'modelhealth'=> $model_health_indicators,'new' => $new));
		}
	}
	public function actiondeleteMilestone($id){
		$id = (int) $id;
		Yii::app()->db->createCommand("DELETE FROM status_report_milestones WHERE id='{$id}'")->execute();	
	}
	public function actiondeleteHighlight($id){
		$id = (int) $id;
		Yii::app()->db->createCommand("DELETE FROM status_report_highlights WHERE id='{$id}'")->execute();	
	}
	public function actionmanageHighlight($id = NULL ){
		$new = false;
		if (!isset($_POST['status_report']))
    		exit;
    	if($id == NULL){
    		$new = true;
    		$model = new StatusReportHighlights;
    		$model->status_report = $_POST['status_report'];
    	}else{
			$id = (int)$id;
    		$model = StatusReportHighlights::model()->findByPk($id);
		}
		if(isset($_POST['StatusReportHighlights'])){
			if ($id == NULL) {
    			$model->attributes = $_POST['StatusReportHighlights']['new'];
    		}else{
    			$model->attributes = $_POST['StatusReportHighlights'][$id];
    		}
    		if($model->validate()){
    			$model->save();
    		echo json_encode(array('status' => 'saved'));
			exit;
		}
	}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_highlight_form', array(
            	'modelhighlight'=> $model,
        	), true, true)));
			
       exit;
	}
	public function actionmanageMilestonen($id = NULL){
		$new = false;
		if (!isset($_POST['status_report']))
    		exit;
    	if($id == NULL){
    		$new = true;
    		$model = new StatusReportMilestones;
    		$model->status_report = $_POST['status_report'];
    	}else{
			$id = (int)$id;
    		$model = StatusReportMilestones::model()->findByPk($id);
		}
		if(isset($_POST['StatusReportMilestones'])){
			if ($id == NULL) {
    			$model->attributes = $_POST['StatusReportMilestones']['new'];
    		}else{
    			$model->attributes = $_POST['StatusReportMilestones'][$id];
    		}
    		if($model->validate()){
    		$model->save();
    		echo json_encode(array('status' => 'saved'));
			exit;
		}
	}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_milestone_formn', array(
            	'model'=> $model,
        	), true, true)));
			
       exit;
	}	
	public static function getExcelReport($id){		
		$uat_open_checklist = array();
			$uat_golive_pending_checklist = array();
				$model = Projects::model()->findByPk($id);
				$cust_name= Customers::GetCustByProject($id);
				$project_phases = Yii::app()->db->createCommand("select  m.description, pm.status , estimated_date_of_start, estimated_date_of_completion from projects_milestones pm join milestones m on pm.id_milestone = m.id
				where pm.applicable='Yes' and  pm.id_project = ".$id." order by m.milestone_number")->queryAll();
				$project_highlights = Yii::app()->db->createCommand("select description from status_report_highlights where status_report in (select id from projects_status_reports where project = ".$id.")")->queryAll();
			//	$project_health_indicators = Yii::app()->db->createCommand("select project_scope, resources, timeline, project_finance, risks_issues, overall_project_health, indicators_date
			//	from status_report_health_indicators where status_report in (select id from projects_status_reports where project = ".$id.") order by indicators_date ASC limit 25")->queryAll();
				$project_health_indicators = Yii::app()->db->createCommand("SELECT * FROM ( select project_scope, resources, timeline, project_finance, risks_issues, overall_project_health, indicators_date from status_report_health_indicators where status_report in (select id from projects_status_reports where project = ".$id.") order by indicators_date DESC limit 45) sub ORDER BY indicators_date ASC")->queryAll();
				$sizeindic= sizeof($project_health_indicators);
				if ($sizeindic<45)
				{
					$x=45-$sizeindic;
					$countArr=$sizeindic;
					$start_date = $project_health_indicators[$sizeindic-1]['indicators_date']; 
					for ($i = 0; $i < $x; $i++) {
						$date = strtotime($start_date);		$date = strtotime("+7 day", $date);						
					    $project_health_indicators[$countArr]['project_scope'] = 0;
			            $project_health_indicators[$countArr]['resources'] = 0;
			            $project_health_indicators[$countArr]['timeline'] = 0;
			            $project_health_indicators[$countArr]['project_finance'] = 0;
			            $project_health_indicators[$countArr]['risks_issues'] = 0;
			            $project_health_indicators[$countArr]['overall_project_health'] = 0;
			            $project_health_indicators[$countArr]['indicators_date'] = date('Y-m-d', $date);			           
			           	$start_date = $project_health_indicators[$countArr]['indicators_date'];
			            $countArr++;
					} 
				}
				$project_milestones = Yii::app()->db->createCommand("select milestone from status_report_milestones where status_report in (select id from projects_status_reports where project = ".$id.")")->queryAll();
				$project_risks = Yii::app()->db->createCommand("select risk,priority, responsibility, planned_actions,status from projects_risks where status <> 'Closed' and privacy='External' and id_project = ".$id)->queryAll();
				$project_risks_closed = Yii::app()->db->createCommand("select risk from projects_risks where status = 'Closed' and privacy='External' and id_project = ".$id)->queryAll();
				$financialflag=Yii::app()->db->createCommand("select invoicesFlag, incluetimesheet,includechecklist from projects_status_reports where project = ".$id." order by id DESC,date DESC limit 1")->queryRow();
				if ($financialflag['invoicesFlag'] == 1)
				{
				$project_invoices = Yii::app()->db->createCommand("select CASE 
				    when partner='201' then snsapj_partner_inv
				    when partner='78'  then partner_inv
                    when partner='79'  and old='Yes' then old_sns_inv
				    when partner='79'  and old='No' then final_invoice_number
					when partner='77'  then final_invoice_number
					END as final_invoice_number, gross_amount , default_currency,DATE_ADD(printed_date, INTERVAL 1 MONTH) as due_date, DATEDIFF(CURDATE(), LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age from invoices join customers on customers.id = invoices.id_customer where invoices.status <> 'Paid' and DATE_ADD(printed_date, INTERVAL 1 MONTH) < NOW()  and id_project = ".$id)->queryAll();
				}	else	{			$project_invoices = null;		}
				if ($financialflag['incluetimesheet'] == 1)
				{
				$project_time = Yii::app()->db->createCommand("select p.id, p.description, (select sum(amount) from user_time where `default`=0 and amount>0 and id_task in (select id from projects_tasks where id_project_phase = p.id))/8 as time from 
projects_phases p where p.id_project=  ".$id." and  ((select sum(amount) from user_time where `default`=0 and amount>0 and id_task in (select id from projects_tasks where id_project_phase = p.id))/8)>0")->queryAll();
				}	else	{			$project_time = null;		}
				
				$project_members = Yii::app()->db->createCommand("select id_user from user_task ut join projects_tasks pt on ut.id_task = pt.id
				join projects_phases pp on pt.id_project_phase = pp.id where pp.id_project = ".$id." group by id_user")->queryAll();
				$sop_signoff_stat = Yii::app()->db->createCommand("
				select count(id) > 0 from projects_milestones where id_milestone = 4 and status = 'Closed' and applicable = 'Yes' and id_project = ".$id)->queryScalar();
				if($sop_signoff_stat != null && $sop_signoff_stat !=0 && $financialflag['includechecklist'] == 1){
					$uat_open_checklist = Yii::app()->db->createCommand("select c.descr,c.category from  checklist  c join projects_checklist pc on c.id = pc.id_checklist where c.id_phase = 6 and c.responsibility = 'Client' and pc.status = 'Open' and  pc.id_project = ".$id)->queryAll();
					if($uat_open_checklist == null){
						$uat_open_checklist = Yii::app()->db->createCommand("select c.descr,c.category from  checklist  c join projects_checklist pc on c.id = pc.id_checklist where (( c.id_phase = 6 and c.responsibility = 'Client' and pc.status = 'Pending' ) or (c.id_phase = 7 and c.responsibility = 'Client' and pc.status = 'Open')) and  pc.id_project = ".$id)->queryAll();
					} 
				}else{
					$uat_open_checklist = null;
				}

 		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");		
		$sheetId = 0;
		$objPHPExcel->getDefaultStyle()->applyFromArray(
		    array(
		        'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => 'FFFFFF')
		        )
		    )
		);
		 $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);    
   		 $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);    

		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
		$objPHPExcel->getDefaultStyle()->getAlignment()->applyFromArray(
				    array('vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,)
				);
		for ($i = 'A';  $i !=  'AMK'; $i++){
		    $objPHPExcel->getActiveSheet()->getColumnDimension($i)
		        ->setAutoSize(false);
			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth("3");
		}
 		
	
 		$objPHPExcel->getActiveSheet()->getStyle('AA1:AB1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
 		$objPHPExcel->getActiveSheet()->getStyle('AA2:AB2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
		$objDrawingPType = new PHPExcel_Worksheet_Drawing();
        $objDrawingPType->setWorksheet($objPHPExcel->setActiveSheetIndex($sheetId));
        $objDrawingPType->setName("logo");
        $objDrawingPType->setPath(Yii::app()->basePath . DIRECTORY_SEPARATOR . "../images/logo_status_report.png");
		$objDrawingPType->setWidthAndHeight(110,35);        
        $objDrawingPType->setCoordinates('B2');
        $objDrawingPType->setOffsetX(5);
        $objDrawingPType->setOffsetY(5);  

        $objPHPExcel->getActiveSheet()
	    ->getPageSetup()
	    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()
	    ->getPageSetup()
	    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4); 
		
	$file=Customers::getLogo($model->customer_id);
		if( $file != null){ 	
			$actp= "../uploads/customers/".$model->customer_id."/documents/".$file."/".Customers::getFName($file);	
			$objDrawingPTypeCust = new PHPExcel_Worksheet_Drawing();
	        $objDrawingPTypeCust->setWorksheet($objPHPExcel->setActiveSheetIndex($sheetId));
	        $objDrawingPTypeCust->setResizeProportional(true);
	        $objDrawingPTypeCust->setName("logoCustomer");
	        $objDrawingPTypeCust->setPath( Yii::app()->basePath . DIRECTORY_SEPARATOR .	$actp );
	        $objDrawingPTypeCust->setWidthAndHeight(110,35);
			$objDrawingPTypeCust->setResizeProportional(true);
	        $objDrawingPTypeCust->setCoordinates('AY2');
	        $objDrawingPTypeCust->setOffsetX(5);
	        $objDrawingPTypeCust->setOffsetY(5);   
	    }

			$sheetId = 0;

		 $bold = array(
            'font' => array(
                'bold' => true
            )
        );        
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            )
        );        
        $styleborder = array(
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            )
        );


        $objPHPExcel->getActiveSheet()->getStyle("H2:AX2")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("H3:AX3")->getFont()->setSize(10);
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('H2:S2')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('U2:AF2')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('U3:AF3')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AH2:AX2')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AH3:AX3')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B5:AA5')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B6:AA6')->applyFromArray($styleborder);		

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H2:J2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K2:S2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K3:S3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('U2:Z2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('U3:Z3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AA2:AF2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AA3:AF3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AH2:AK2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AH3:AK3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AL2:AX2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AL3:AX3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:AA5');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:M6');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N6:Q6');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('R6:U6');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('V6:AA6');

	 /*	$objPHPExcel->getActiveSheet()->getStyle('H2:BC2')->applyFromArray($styleborder);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A9:M9');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A10:G10');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H10:I10');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J10:K10');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L10:M10');*/

       $objPHPExcel->getActiveSheet()->getStyle('H2:J2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');       
        $objPHPExcel->getActiveSheet()->getStyle('U2:Z2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
        $objPHPExcel->getActiveSheet()->getStyle('U3:Z3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->getActiveSheet()->getStyle('AH2:AK2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->getActiveSheet()->getStyle('AH3:AK3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');  
        $objPHPExcel->getActiveSheet()->getStyle('B5:AA5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');   
      //  $objPHPExcel->getActiveSheet()->getStyle('B6:AA6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');     

        $objPHPExcel->getActiveSheet()->getStyle('H2:J2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('U2:Z2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('U3:Z3')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('AH2:AK2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('AH3:AK3')->applyFromArray($styleArray);  
        $objPHPExcel->getActiveSheet()->getStyle('B5:AA5')->applyFromArray($styleArray);   

	//	$nb = sizeof($data);          
         
		/*$str='';
		foreach ($project_members as $key => $member) {
			$str .= Users::getNameById($member['id_user']).",  ";
		}
		$str2=substr_replace($str ,"",-3);*/

		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('H2', 'Customer')
		->setCellValue('K2', Customers::getNameByID(($model->customer_id)))
		->setCellValue('U2', 'Project Manager')
		->setCellValue('U3', 'Business Manager')		
		->setCellValue('AA2', Users::getNameById($model->project_manager))
		->setCellValue('AA3', Users::getNameById($model->business_manager))
		->setCellValue('AH2', 'Project Name')
		->setCellValue('AH3', 'Report Date')		
		->setCellValue('AL2', $model->name)
		->setCellValue('AL3',  date('d/m/Y',strtotime("now")))		
		->setCellValue('B5', 'Project Milestones')
		->setCellValue('B6', 'Milestone')
		->setCellValue('N6', 'Status')
		->setCellValue('R6', 'Start Date')
		->setCellValue('V6', 'End Date')
		;
 
	 	$i = 7;
		$rcount=1;
		foreach($project_phases as $d => $row){

			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':AA'.$i.'')->applyFromArray($styleborder);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$i.':M'.$i.'');
	        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$i.':Q'.$i.'');
	        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('R'.$i.':U'.$i.'');
	        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('V'.$i.':AA'.$i.'');
			
			if ($row['status'] == "Closed")
	        {
	        	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':AA'.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');  
	        }else if ($row['status'] == "In Progress"){ 
	    	    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':AA'.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFC000');
	    	}

	    	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
	    	$objPHPExcel->getActiveSheet()->getStyle('R'.$i.':V'.$i)->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('B'.$i, $rcount)
			->setCellValue('C'.$i, $row['description'])
			->setCellValue('N'.$i, $row['status']) 
			->setCellValue('R'.$i, date('d/m/Y', strtotime($row['estimated_date_of_start'])))
			->setCellValue('V'.$i, date('d/m/Y', strtotime($row['estimated_date_of_completion'])))
			;	
			$i++;		
			$rcount++;	
		}

		if(!empty($project_highlights ))
		{ 
       		$objPHPExcel->getActiveSheet()->getStyle('AD5:BC5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle('AD5:BC5')->applyFromArray($styleArray);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AD5:BC5');
			$k=6;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('AD5', 'Highlight(s)')
			;

			foreach($project_highlights as $d => $row){
				$objPHPExcel->getActiveSheet()->getStyle('AD'.$k.':BC'.$k.'')->applyFromArray($styleborder);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AD'.$k.':BC'.$k.'');

				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('AD'.$k, $row['description'])
				;
				$k++;
			}	
			if($k>$i)
			{
				$i=$k;
			}
		}

		$i += 1;

		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
		$objPHPExcel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(20);
		$objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(20);
		$objPHPExcel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(20);

		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.$i.'')->applyFromArray($styleArray); 
		
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.($i+9).'')->applyFromArray($styleborder); 
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i+4).':J'.($i+9))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2DCDB');
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1).':I'.($i+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2DCDB');
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+2).':I'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2DCDB');
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+3).':I'.($i+3))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2DCDB');
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i+3))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
		
		$objPHPExcel->getActiveSheet()->getStyle('K'.($i+1).':BC'.($i+1).'')->getFont()->setBold( true );
		$objPHPExcel->getActiveSheet()->getStyle('K'.($i+2).':BC'.($i+2).'')->getFont()->setBold( true );
		$objPHPExcel->getActiveSheet()->getStyle('K'.($i+3).':BC'.($i+3).'')->getFont()->setBold( true );

		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$i.':J'.$i.'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+4).':I'.($i+4).'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+5).':I'.($i+5).'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+6).':I'.($i+6).'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+7).':I'.($i+7).'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+8).':I'.($i+8).'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+9).':I'.($i+9).'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('K'.$i.':BC'.$i.'');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+1).':I'.($i+1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+2).':I'.($i+2));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+3).':I'.($i+3));


		$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('B'.$i, 'Project Health Indicators')
			->setCellValue('K'.$i, 'Period Ending')
			->setCellValue('B'.($i+1), 'On Track')
			->setCellValue('J'.($i+1), 'G')
			->setCellValue('B'.($i+2), 'Manageable Issues / Risks')
			->setCellValue('J'.($i+2), 'Y')
			->setCellValue('B'.($i+3), 'Major Problems')
			->setCellValue('J'.($i+3), 'R')
			->setCellValue('B'.($i+4), 'Project Scope')
			->setCellValue('B'.($i+5), 'Resources')
			->setCellValue('B'.($i+6), 'Timeline')
			->setCellValue('B'.($i+7), 'Project Finance')
			->setCellValue('B'.($i+8), 'Risks & Issues')
			->setCellValue('B'.($i+9), 'Overalll Project Health')
			;

		$column= 'K';
		$cr=2;
		foreach($project_health_indicators as $indicator){	

				
			if ($cr % 2 == 0) {
				$objPHPExcel->getActiveSheet()->getStyle(''.$column.($i+1).':'.$column.($i+3).'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2DCDB');
			}else{
				$objPHPExcel->getActiveSheet()->getStyle(''.$column.($i+1).':'.$column.($i+3).'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f8eded');
			}

			if($indicator['project_scope'] == 1)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
			}else if ($indicator['project_scope'] == 2){
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
			}
			else if($indicator['project_scope'] == 3)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
			}

			if($indicator['resources'] == 1)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
			}else if ($indicator['resources'] == 2){
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
			}
			else if($indicator['resources'] == 3)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
			}

			if($indicator['timeline'] == 1)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+6))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
			}else if ($indicator['timeline'] == 2){
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+6))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
			}
			else if($indicator['timeline'] == 3)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+6))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
			}

			if($indicator['project_finance'] == 1)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+7))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
			}else if ($indicator['project_finance'] == 2){
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+7))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
			}
			else if($indicator['project_finance'] == 3)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+7))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
			}


			if($indicator['risks_issues'] == 1)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+8))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
			}else if ($indicator['risks_issues'] == 2){
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+8))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
			}
			else if($indicator['risks_issues'] == 3)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+8))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
			}

			if($indicator['overall_project_health'] == 1)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+9))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
			}else if ($indicator['overall_project_health'] == 2){
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+9))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
			}
			else if($indicator['overall_project_health'] == 3)
			{
				$objPHPExcel->getActiveSheet()->getStyle($column.($i+9))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
			}

			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column.($i+1).':'.$column.($i+3).'');
			$objPHPExcel->getActiveSheet()->getStyle($column.($i+1))->getAlignment()->setTextRotation(90);
			$objPHPExcel->getActiveSheet()->getStyle($column.($i+1))->getAlignment()->applyFromArray(
				    array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,)
				);

			$objPHPExcel->getActiveSheet()->getStyle($column.($i+4))->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
   			 $objPHPExcel->getActiveSheet()->getStyle($column.($i+5))->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
   			 $objPHPExcel->getActiveSheet()->getStyle($column.($i+6))->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
   			 $objPHPExcel->getActiveSheet()->getStyle($column.($i+7))->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
   			 $objPHPExcel->getActiveSheet()->getStyle($column.($i+8))->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
   			 $objPHPExcel->getActiveSheet()->getStyle($column.($i+9))->getAlignment()->applyFromArray(
				    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue($column.($i+1), date('j',strtotime($indicator['indicators_date'])).'-'.date('M',strtotime($indicator['indicators_date'])).'-'.date('y',strtotime($indicator['indicators_date'])))
			->setCellValue($column.($i+4), StatusReportHealthIndicators::getIndicatorLabel($indicator['project_scope']))
			->setCellValue($column.($i+5), StatusReportHealthIndicators::getIndicatorLabel($indicator['resources']))
			->setCellValue($column.($i+6), StatusReportHealthIndicators::getIndicatorLabel($indicator['timeline']))
			->setCellValue($column.($i+7), StatusReportHealthIndicators::getIndicatorLabel($indicator['project_finance']))
			->setCellValue($column.($i+8), StatusReportHealthIndicators::getIndicatorLabel($indicator['risks_issues']))
			->setCellValue($column.($i+9), StatusReportHealthIndicators::getIndicatorLabel($indicator['overall_project_health']))	
			;	
			$cr++;
			$column++;
		}
		$i +=11;

		if (!empty($project_risks)){
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.$i)->applyFromArray($styleborder); 
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1).':BC'.($i+1))->applyFromArray($styleborder); 
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1).':BC'.($i+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCBB'); 
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.$i)->applyFromArray($styleArray);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$i.':BC'.$i);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+1).':T'.($i+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('U'.($i+1).':W'.($i+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('X'.($i+1).':AB'.($i+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AC'.($i+1).':BC'.($i+1));

			$k=11;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('B'.$i, 'Open Risk(s)')
			->setCellValue('B'.($i+1), 'Description')
			->setCellValue('U'.($i+1), 'Priority')			
			->setCellValue('X'.($i+1), 'Responsibility')			
			->setCellValue('AC'.($i+1), 'Planned Action(s)')
			;
			$i+=2;
		 	foreach($project_risks as $d => $row){
				if ($row['priority'] == 3)
		        {
		        	$objPHPExcel->getActiveSheet()->getStyle('U'.$i.':W'.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');  
		        }else if ($row['priority'] == 2){ 
		    	    $objPHPExcel->getActiveSheet()->getStyle('U'.$i.':W'.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
		    	}else if ($row['priority'] == 1){ 
		    	    $objPHPExcel->getActiveSheet()->getStyle('U'.$i.':W'.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
		    	}
		    	$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':BC'.$i)->applyFromArray($styleborder); 
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$i.':T'.$i.'');
		        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('U'.$i.':W'.$i.'');
		        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('X'.$i.':AB'.$i.'');
		        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AC'.$i.':BC'.$i.'');
					
				if($row['responsibility'] == 'CLIENT')
				{
					$resp= Customers::getNameByID(Projects::getCustomerByProjectName($model->name));
				}
				else if($row['responsibility'] == 'SNS/CLIENT'){
					$resp= 'SNS/'.Customers::getNameByID(Projects::getCustomerByProjectName($model->name));
				}else{
					$resp=$row['responsibility'];
				}

				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('B'.$i, $row['risk'])
				->setCellValue('U'.$i, ucfirst(strtolower(Projects::getPriorityLabel($row['priority']))))
				->setCellValue('X'.$i, $resp) 
				->setCellValue('AC'.$i, $row['planned_actions'])
				;	
				$i++;	
			}
			$i ++;
		}	


		$left=true;
		$original=$i;

		if (!empty($project_risks_closed)){ 
			$left= false;
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':AA'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1).':AA'.($i+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCBB');  
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':AA'.$i)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1).':AA'.($i+1))->applyFromArray($styleborder);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$i.':AA'.$i);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.($i+1).':AA'.($i+1));

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('B'.$i, 'Closed Risks')
			->setCellValue('B'.($i+1), 'Description')
			;
			$i += 2;
			foreach($project_risks_closed as $d => $row){
		    	$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':AA'.$i)->applyFromArray($styleborder);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$i.':AA'.$i);
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('B'.$i, $row['risk'])
				;	
				$i++;
			} 
			$i +=1;
		}

		if (!empty($project_milestones)){

			if($left)
			{
				$columnf= 'B';
				$columnl= 'AA';
				$left = false; 
				$k= $i ;
			}else{
				$columnf= 'AD';
				$columnl= 'BC';
				$k= $original;
				$left=true;
			}

			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCBB');  
			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->applyFromArray($styleborder);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnl.$k);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.($k+1).':'.$columnl.($k+1));

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue($columnf.$k, 'Milestone(s) For The Next 6 Months')
			->setCellValue($columnf.($k+1), 'Description')
			;
			$k += 2;
			foreach($project_milestones as $d => $row){
				$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleborder);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnl.$k);
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue($columnf.$k, $row['milestone'])
				;	
				$k++;
			}
			if($left)
			{
				$original= $k+1;
			}else{
				$i=$k+1;
			}
		}
 

		if (!empty($project_invoices)){
			if($left)
			{
				$columnf= 'B';
				$columnf2= 'G';
				$columnsec= 'H';
				$columnsec2= 'L';
				$columnthird= 'M';
				$columnthird2= 'Q';
				$columnfourth= 'R';
				$columnfourth2= 'W';
				$columnfifth= 'X';
				$columnl= 'AA';
				$left = false;
				$k= $i  ; 
			}else{
				$columnf= 'AD';
				$columnf2= 'AI';
				$columnsec= 'AJ';
				$columnsec2= 'AN';
				$columnthird= 'AO';
				$columnthird2= 'AS';
				$columnfourth= 'AT';
				$columnfourth2='AY';
				$columnfifth= 'AZ';
				$columnl= 'BC';
				$k= $original ;
				$left=true;
			}

			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCBB');  
			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->applyFromArray($styleborder);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnl.$k);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.($k+1).':'.$columnf2.($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnsec.($k+1).':'.$columnsec2.($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnthird.($k+1).':'.$columnthird2.($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnfourth.($k+1).':'.$columnfourth2.($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnfifth.($k+1).':'.$columnl.($k+1));

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue(''.$columnf.$k, 'Invoice(s) Due')
			->setCellValue(''.$columnf.($k+1), 'Invoice#')
			->setCellValue(''.$columnsec.($k+1), 'Amount')
			->setCellValue(''.$columnthird.($k+1), 'Currency')
			->setCellValue(''.$columnfourth.($k+1), 'Due Date')
			->setCellValue(''.$columnfifth.($k+1), 'Age')
			;
			$k += 2;
			foreach($project_invoices as $d => $row){

				$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleborder);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnf2.$k);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnsec.$k.':'.$columnsec2.$k);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnthird.$k.':'.$columnthird2.$k);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnfourth.$k.':'.$columnfourth2.$k);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnfifth.$k.':'.$columnl.$k);

				$objPHPExcel->getActiveSheet()
			    ->getStyle($columnsec.$k.':'.$columnsec2.$k)
			    ->getAlignment()
			    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue(''.$columnf.$k, $row['final_invoice_number'])
				->setCellValue(''.$columnsec.$k, Utils::formatNumber($row['gross_amount']))
				->setCellValue(''.$columnthird.$k,  Codelkups::getCodelkup($row['default_currency']))
				->setCellValue(''.$columnfourth.$k, date('d/m/Y',strtotime($row['due_date'])))
				->setCellValue(''.$columnfifth.$k, $row['age'])
				;	
				$k++;
			}
			if($left)
			{
				$original= $k+1;
			}else{
				$i=$k+1;
			}
		}
 

		if (!empty($uat_open_checklist)){

			if($left)
			{
				$columnf= 'B';
				$columnf2= 'U';
				$columnsec= 'V';
				$columnl= 'AA';
				$left = false;
				$k= $i  ; 
			}else{
				$columnf= 'AD';
				$columnf2= 'AW';
				$columnsec= 'AX';
				$columnl= 'BC';
				$k= $original ;
				$left=true;
			}

			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCBB');  
			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->applyFromArray($styleborder);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnl.$k);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.($k+1).':'.$columnf2.($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnsec.($k+1).':'.$columnl.($k+1));

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue($columnf.$k, 'Pending Checklist Item(s)')
			->setCellValue($columnf.($k+1), 'Description')
			->setCellValue($columnsec.($k+1), 'Category')
			;
			$k += 2;
			foreach($uat_open_checklist as $d => $row){

				$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleborder);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnf2.$k);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnsec.$k.':'.$columnl.$k);

				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue($columnf.$k, $row['descr'])
				->setCellValue($columnsec.$k, $row['category'])
				;	
				$k++;
			}
			if($left)
			{
				$original= $k+1;
			}else{
				$i=$k+1;
			}
		}
 

		if (!empty($project_time)){

			if($left)
			{
				$columnf= 'B';
				$columnf2= 'U';
				$columnsec= 'V';  
				$columnl= 'AA';
				$left = false;
				$k= $i  ; 
			}else{
				$columnf= 'AD';
				$columnf2= 'AW';
				$columnsec= 'AX'; 
				$columnl= 'BC';
				$k= $original ;
				$left=true;
			}

			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532'); 
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCBB');  
			$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle($columnf.($k+1).':'.$columnl.($k+1))->applyFromArray($styleborder);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnl.$k);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.($k+1).':'.$columnf2.($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnsec.($k+1).':'.$columnl.($k+1));

			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue($columnf.$k, 'Time Spent Per Phase')
			->setCellValue($columnf.($k+1), 'Phase')
			->setCellValue($columnsec.($k+1), 'Time in  MDs')
			;
			$k += 2;
			foreach($project_time as $d => $row){

				$objPHPExcel->getActiveSheet()->getStyle($columnf.$k.':'.$columnl.$k)->applyFromArray($styleborder);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnf.$k.':'.$columnf2.$k);
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($columnsec.$k.':'.$columnl.$k);

				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue($columnf.$k, $row['description'])
				->setCellValue($columnsec.$k, Utils::formatNumber($row['time'],2))
				;	
				$k++;
			}
			if($left)
			{
				$original= $k+1;
			}else{
				$i=$k+1;
			}
		} 

		 

		$objPHPExcel->getActiveSheet()->setTitle('Status Report - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$cust_name.'_SR_'.date("dMy",strtotime("now")).'.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$doc_model = new Documents; $doc_model->id_model = $id; $doc_model->model_table = 'projects';
			 		$doc_model->id_category = 17; 	$cust_name= Customers::GetCustByProject($id);
			 		 $cust_name= str_replace("/","_",$cust_name); $doc_model->document_title = $cust_name."_SR_".date("dMy");
			 		$doc_model->uploaded_by = 1; $doc_model->file = $cust_name."_SR_".date("dMy",strtotime("now")).'.xls';
			 		 if($doc_model->validate()){ $doc_model->save(); }
			 		$objWriter->save(Projects::getDirPath($model->id, $doc_model->id).$cust_name.'_SR_'.date("dMy",strtotime("now")).'.xls', 'F');		
		 
	}
	public function actiongetExcelIssues($id){		
		$data = Projects::getIssuesitems($id);
		$type= Projects::getProjectType($id);
		$proj= Projects::getNameById($id);
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");		
		$sheetId = 0;

		$nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray); 


		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Issue#')
		->setCellValue('B1', 'Description')
		->setCellValue('C1', 'Priority')
		->setCellValue('D1', 'Status')
		->setCellValue('E1', 'Type')
		->setCellValue('F1', 'FBR')
		->setCellValue('G1', 'Fix')
		->setCellValue('H1', 'Notes')
		->setCellValue('I1', 'Logged by')
		->setCellValue('J1', 'Logged Date')
		->setCellValue('K1', 'Last Updated By')
		->setCellValue('L1', 'Last Updated On')
		->setCellValue('M1', 'Closure Date')
        ->setCellValue('N1', 'ETD')
		;
		$i = 1;
		foreach($data as $d => $row){
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, " ".str_pad($row['id_issue'], 3, "0", STR_PAD_LEFT))
			->setCellValue('B'.$i, $row['description'])
			->setCellValue('C'.$i, ($row['priority'])==0 ? "Low" :(($row['priority'])==1? "Medium": "High")) 
			->setCellValue('D'.$i, ProjectsIssues::getStatus($row['status']))
			->setCellValue('E'.$i, ($row['type']))
			->setCellValue('F'.$i, ProjectsTasks::getTaskDescByid($row['fbr']))
			->setCellValue('G'.$i, ($row['fix']))
			->setCellValue('H'.$i, ($row['notes']))
			->setCellValue('I'.$i, Users::getCredentialsbyId($row['logged_by']))
			->setCellValue('J'.$i, date("d/m/Y", strtotime($row['logged_date'])))
			->setCellValue('K'.$i, Users::getCredentialsbyId($row['lastupdateby']))
			->setCellValue('L'.$i, date("d/m/Y", strtotime($row['lastupdateddate'])))
			->setCellValue('M'.$i, ($row['close_date'] !="0000-00-00")? date("d/m/Y", strtotime($row['close_date'])): " ")
            ->setCellValue('M'.$i, ($row['etd_date'] !="0000-00-00")? date("d/m/Y", strtotime($row['etd_date'])): " ")
			;			
		}
		$objPHPExcel->getActiveSheet()->setTitle('Issues List - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$proj.'_Issues_'.date("d_m_Y").'.xls"');
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
	public function actionGetExcel($id){		
		$data = Projects::getChecklistitems($id);
		$type= Projects::getProjectType($id);
		$proj= Projects::getNameById($id);
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");		
		$sheetId = 0;
		$nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray); 

		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Item#')
		->setCellValue('B1', 'Description')
		->setCellValue('C1', 'Category')
		->setCellValue('D1', 'Responsibility')
		->setCellValue('E1', 'Phase')
		->setCellValue('F1', 'Status')
		;
		$i = 1;
		foreach($data as $d => $row){
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, $i-1)
			->setCellValue('B'.$i, Checklist::getMilestoneDescription($row['id_checklist']))
			->setCellValue('C'.$i, Checklist::getCategory($row['id_checklist'])) 
			->setCellValue('D'.$i,  Checklist::getresponsibility($row['id_checklist']))
			->setCellValue('E'.$i, Milestones::getMilestoneDescriptionShort($row['id_phase']))
			->setCellValue('F'.$i, $row['status'])
			;			
		}
		$objPHPExcel->getActiveSheet()->setTitle('Checklist Items - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$proj.'_Checklist_'.date("d_m_Y").'.xls"');
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
}?>