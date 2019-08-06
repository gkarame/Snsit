<?php
Yii::import("xupload.models.XUploadForm");
class QualityController extends Controller{
    public $layout = '//layouts/column1';
    public function filters(){
        return array(
            'accessControl',
            'postOnly + delete + deleteContact'            
        );
    }    
    public function init(){
        parent::init();
    }
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('sendQAEmailReminder', 'sendQADone'),
                'users' => array('*')
            ),            
            array(
                'allow', 
                'actions' => array('create','index','deleteQA','getExcel','update','assignQA','updateNotesDisplay','updateNotes','assignDate','GetProjectsDevTasks','sendToQA'
                ),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin '
            ),            
            array(
                'deny', 
                'users' => array('*')
            )
        );    
	}    
    public function actions(){
        return array(
            'upload' => array(
                'class' => 'xupload.actions.CustomXUploadAction',
                'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
                'publicPath' => Yii::app()->getBaseUrl() . "/uploads/tmp",
                'stateVariable' => 'customers_conn'
            )
        );
    }   
    public function actionGetExcel(){
        $model = new Quality('getAll');
		$datas = $model->getAll(null, true)->getData();	
		 
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Quality Export");		
		$sheetId = 0;

		$nb = sizeof($datas);          
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
		->setCellValue('A1', 'Project')
		->setCellValue('B1', 'Task/FBR')
		->setCellValue('C1', 'PM/OPs')
		->setCellValue('D1', 'Resource')
		->setCellValue('E1', 'complexity')
		->setCellValue('F1', 'Type')		
		->setCellValue('G1', 'Status')
		->setCellValue('H1', 'QA')
		->setCellValue('I1', 'FBR ETA')
		->setCellValue('J1', 'QA ETA')
		->setCellValue('K1', 'Result')
		->setCellValue('L1', 'Comment')
		->setCellValue('M1', 'Creation Date')
		;
		$i = 1;
		foreach($datas as $d => $data)
		{
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, Projects::getNameById($data->id_project))
			->setCellValue('B'.$i, Quality::getTaskDesc($data->id_task, $data->id_phase, $data->internal_project))
			->setCellValue('C'.$i, Projects::getPmOpsAssignedWhole($data->id_project))
			->setCellValue('D'.$i, Users::getNameById($data->id_user)) 
			->setCellValue('E'.$i, Quality::getComplexLabel($data->complexity))
			->setCellValue('F'.$i, Quality::getTypeLabel($data->type))
			->setCellValue('G'.$i, Quality::getStatusLabel($data->status))
			->setCellValue('H'.$i, Users::getNameById($data->id_resc))
			->setCellValue('I'.$i, (isset ($data->fbr_delivery) && $data->fbr_delivery!='') ? date('d/m/Y', strtotime($data->fbr_delivery)): '')
			->setCellValue('J'.$i, (isset ($data->expected_delivery_date) && $data->expected_delivery_date!='') ? date('d/m/Y', strtotime($data->expected_delivery_date)): '')
			->setCellValue('K'.$i, Quality::getScoresLabel($data->score))
			->setCellValue('L'.$i, $data->notes)
			->setCellValue('M'.$i, isset ($data->adddate) ? date('d/m/Y', strtotime($data->adddate)): '')
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Quality - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Quality_'.date("d_m_Y").'.xls"');
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
		if(!GroupPermissions::checkPermissions('general-quality','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/quality/create' => array(
					'label'=> 'New QA',
					'url' => array('quality/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;	$model = new Quality();	$model->status = 0;
		if (isset($_POST['Quality'])){			
			$model->attributes = $_POST['Quality'];				
			if(isset($_POST['Quality']['id_project'])){
				$model->id_project= Projects::getIdByName($_POST['Quality']['id_project']);
			}
			$model->id_phase=0;		
			if(isset($_POST['Quality']['expected_delivery_date']) && $_POST['Quality']['expected_delivery_date']!='1970-01-01'){
				$date = strtr($_POST['Quality']['expected_delivery_date'], '/', '-');
				$model->expected_delivery_date=date('Y-m-d', strtotime($date));
			}
			if(isset($_POST['Quality']['fbr_delivery']) && $_POST['Quality']['expected_delivery_date']!='1970-01-01'){
				$date2 = strtr($_POST['Quality']['fbr_delivery'], '/', '-');
				$model->fbr_delivery= date('Y-m-d', strtotime($date2));
			}
			$model->adddate= date('Y-m-d'); 
			if ($model->validate()){	
				if(!empty($model->id_resc))
				{
					$model->status=1;
					$model->updated=1;
				}
				$model->save();	
				$this->redirect(array('quality/index')); 
			}			 
		}			
		$this->render('create', array('model' => $model));
	} 
    public function actionIndex(){
        if (!GroupPermissions::checkPermissions('general-quality')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $searchArray                = isset($_GET['Quality']) ? $_GET['Quality'] : Utils::getSearchSession();
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/quality/index' => array(
                'label' => Yii::t('translations', 'QA Tasks'),
                'url' => array(
                    'quality/index'
                ),
                'itemOptions' => array(
                    'class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $model = new Quality('search');
        $model->unsetAttributes();     
       if (empty($searchArray['status']) && !(isset($searchArray['status']))) {
            $searchArray['status'] = "0,1,3";
        }   
        $model->attributes = $searchArray;
        $this->render('index', array(
            'model' => $model
        ));
    }    
    
    public function actiondeleteQA(){
        if (isset($_POST['qa_id'])) {
            $ids         = $_POST['qa_id'];
            $marr        = explode("checkinvoice%5B%5D", $ids);
            $filterd_ids = array();
            foreach ($marr as $valy){
                preg_match('!\d+!', $valy, $match);
                if ($match != null)
                    array_push($filterd_ids, $match[0]);
            }
            if (sizeof($filterd_ids) == 0){
                echo json_encode(array(
                    'status' => 'failed','message' => 'Please select at least one task!'
                ));
                exit;
            } else {
                $all= implode(',', $filterd_ids);                
                Yii::app()->db->createCommand("delete from `quality` WHERE id in (" . $all ." )")->execute();
                echo json_encode(array(
                    'status' => 'success'
                ));
                exit;
            }
        }
    }

    public function actionupdateNotesDisplay(){
        if (isset($_POST['qa_id'])) {
            $ids         = $_POST['qa_id'];
            $marr        = explode("checkinvoice%5B%5D", $ids);
            $filterd_ids = array();
            foreach ($marr as $valy){
                preg_match('!\d+!', $valy, $match);
                if ($match != null)
                    array_push($filterd_ids, $match[0]);
            }
            if (sizeof($filterd_ids) == 0 || sizeof($filterd_ids) > 1){
                echo json_encode(array(
                    'status' => 'failed','message' => 'Please select one task to be updated.'
                ));
                exit;
            } else {
                $model = Quality::model()->findByPk((int) $filterd_ids[0]);
                $notes = strip_tags($model->notes);
                echo json_encode(array(
                    'status' => 'success','message' => $notes
                ));
                exit;
            }
        }
    }
    public function actionupdateNotes(){
        if (isset($_POST['qa_id'])) {
            $ids         = $_POST['qa_id'];
            $marr        = explode("checkinvoice%5B%5D", $ids);
            $filterd_ids = array();
            foreach ($marr as $valy) {
                preg_match('!\d+!', $valy, $match);
                if ($match != null)
                    array_push($filterd_ids, $match[0]);
            }
            if (sizeof($filterd_ids) == 0 || sizeof($filterd_ids) > 1) {
                echo json_encode(array(
                    'status' => 'failed','message' => 'Please select one task to be updated.'
                ));
                exit;
            } else {
                $id    = (int) $filterd_ids[0];
                $notes = nl2br($_POST['notes']);
                Yii::app()->db->createCommand("UPDATE `quality` SET notes='" . addslashes($notes) . "' WHERE id=" . $id)->execute();
                echo json_encode(array(
                    'status' => 'success'
                ));
                exit;
            }
        }
    }
    public function actionUpdate(){
        $task  = $_POST['id'];
        $value = $_POST['value'];
        $field = $_POST['field'];        
        $updateStatus = 0; $message='';
        switch ($field) {
            case '1':      
                $dp = Yii::app()->db->createCommand("update  quality set status=" . $value . ", updated=1 WHERE id=" . $task . " ")->execute();
                if ($value == 4) {
                    self::sendQAEmailCancelled($task);
                }
                break;            
            case '2':
                $dp = Yii::app()->db->createCommand("update  quality set complexity=" . $value . ", updated=1 WHERE id=" . $task . " ")->execute();
                break;            
            case '3':
                $dp           = Yii::app()->db->createCommand("update  quality set score=" . $value . ", status=5, score_date= CURRENT_DATE() WHERE id=" . $task . " ")->execute();
                $updateStatus = 1;
                break;
            case '4':
                $dp = Yii::app()->db->createCommand("update  quality set id_resc=" . $value . " ,status=1,updated=1 WHERE id=" . $task . " ")->execute();
                $qadate = Yii::app()->db->createCommand("SELECT expected_delivery_date FROM quality WHERE id=" . $task . "")->queryScalar();
                if(empty($qadate))
                {
                	$message='Kindly specify the QA delivery date';
                }
                $updateStatus = 1;
                break;
            case '5':
                $dp = Yii::app()->db->createCommand("update  quality set fbr_delivery='" . $value . "', updated=1 WHERE id=" . $task . " ")->execute();
                break;
            case '6':
                $dp = Yii::app()->db->createCommand("update  quality set expected_delivery_date='" . $value . "', updated=1 WHERE id=" . $task . " ")->execute();
                break;                
        }        
        echo json_encode(array(
            "status" => "done",
            "updategrid" => $updateStatus,"message" => $message
        ));
        exit;
    }    
    public function actionGetProjectsDevTasks($id){
        $this->layout = ''; //echo json_encode(Quality::GetAllProjectsDevTasks((int) $id));
        echo json_encode(Quality::GetCustomProjectsDevTasks((int) $id));
        exit();
    }    
    public function actionsendQADone(){	
    	$selects=Quality::getQCTasksDoneCurrentMonth();
		$notif = EmailNotifications::getNotificationByUniqueName('qa_done');
		if ($notif != NULL ){
			$to_replace = array(
				'{body}',
			);			
			Yii::app()->mailer->ClearAddresses();	
			$y=0; $i=0; $table=''; $table2='';
			if(!empty($selects)){
				$message = "Dear All,<br/><br/> Kindly find below a list of completed QC tasks in the month of ".date('F Y').":";
				
				foreach ($selects as $select) {
					$project= ProjectsTasks::getProjectByTask($select['id_task']);
					$task_phase=ProjectsTasks::getTaskDescByid($select['id_task']);
					if($select['type'] == 1 ){
						$y++;
						$table.="<tr><td>".Customers::getNamebyid(Projects::getCustomerByProject($project))."</td> <td>".Projects::getNamebyid($project)."</td> <td>".Projects::getProjectManager($project)."</td> <td>".$task_phase." </td> <td> ".Quality::getComplexLabel($select['complexity'])." </td><td>".ProjectsTasks::getAllUsersTaskNormalTech($select['id_task'])."</td> <td>".((isset($select['adddate']) && $select['adddate'] !="")? date("d/m/Y", strtotime($select['adddate'])): " ")."</td> <td>".Users::getNamebyId($select['id_resc'])."</td><td>".Quality::getScoresLabel($select['score'])."</td><td>".((isset($select['score_date']) && $select['score_date'] !="")? date("d/m/Y", strtotime($select['score_date'])): " ")."</td><td>".$select['notes']."</td></tr>";
					}else{
						$i++;
						$table2.="<tr><td>".Customers::getNamebyid(Projects::getCustomerByProject($project))."</td> <td>".Projects::getNamebyid($project)."</td> <td>".Projects::getProjectManager($project)."</td> <td>".$task_phase." </td> <td> ".Quality::getComplexLabel($select['complexity'])." </td><td>".ProjectsTasks::getAllUsersTaskNormalTech($select['id_task'])."</td> <td>".((isset($select['adddate']) && $select['adddate'] !="")? date("d/m/Y", strtotime($select['adddate'])): " ")."</td> <td>".Users::getNamebyId($select['id_resc'])."</td><td>".Quality::getScoresLabel($select['score'])."</td><td>".((isset($select['score_date']) && $select['score_date'] !="")? date("d/m/Y", strtotime($select['score_date'])): " ")."</td><td>".$select['notes']."</td></tr>";
					}
				}

				if($y>0)
				{
					$message.= '<br/><br/><table  border="1"  style="font-family:Calibri;border-collapse: collapse;" > <tr><th colspan="11">QC Tech Tasks</th></tr>';
					$message .= "<tr><th width='200' >Customer</th><th width='400' >Project</th><th width='200' >PM</th><th width='400' >Task</th><th width='100' >Complexity</th><th width='300' >Assigned Resources</th><th width='100' >Creation Date</th><th width='200' >QC Resource</th><th width='100' >Score</th><th width='100' >QC Date</th><th>Comment</th></tr>";
					$message.=$table."</table>";
				}

				if($i>0)
				{
					$message.= '<br/><br/><table  border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th colspan="11">QC OPs Tasks</th></tr>';
					$message .= "<tr><th width='200' >Customer</th><th width='400' >Project</th><th width='200' >PM</th><th width='400' >Task</th><th width='100' >Complexity</th><th width='300' >Assigned Resources</th><th width='100' >Creation Date</th><th width='200' >QC Resource</th><th width='100' >Score</th><th width='100' >QC Date</th><th>Comment</th></tr>";
					$message.=$table2."</table>";
				}

				
			}else	{
				$message = "Dear All,<br/><br/> Kindly note that no QC tasks were completed in the past month.";
			}
			$message.=" <br/><br/>Best Regards,<br/>SNSit"; 
			
			$subject = $notif['name']." - ".date('F Y');
			$replace = array(
				$message,
			);
			$body = str_replace($to_replace, $replace, $notif['message']);					
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			Yii::app()->mailer->ClearAddresses();       			
			//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");			
			foreach($emails as $email){
				Yii::app()->mailer->AddAddress($email);
			}
			Yii::app()->mailer->Subject  = $subject ;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);			
		}
	}
    public function actionsendToQA(){
    	if (isset($_POST['qa_id'])) {
            $ids         = $_POST['qa_id'];
            $marr        = explode("checkinvoice%5B%5D", $ids);
            $filterd_ids = array();
            foreach ($marr as $valy){
                preg_match('!\d+!', $valy, $match);
                if ($match != null)
                    array_push($filterd_ids, $match[0]);
            }
            if (sizeof($filterd_ids) == 0){
                echo json_encode(array(
	                    "status" => "done","message" => "No QA tasks are selected."
	                ));
	                exit;
            }

            $all= implode(',', $filterd_ids);       
			$tasks = Yii::app()->db->createCommand("SELECT * FROM quality WHERE updated=1 and  id_resc is not null and id_resc!= 0 and expected_delivery_date like '%-%' and id in (".$all.")")->queryAll();
	        foreach ($tasks as $select) {            
	            $notif = EmailNotifications::getNotificationByUniqueName('qa_task');
	            if ($notif != NULL) {
	                if (!(empty($select['old_qa'])) && $select['id_resc'] != $select['old_qa']) {
	                    $to_replace = array(
	                        '{group_description}',    '{body}'
	                    );
	                    $message = "";
	                    if ($select['id_phase'] == '0') {
	                        $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
	                    } else {
	                        $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
	                        $task_phase .= '- Phase: <b>' . ProjectsPhases::getPhaseDescByPhaseId($select['id_phase']) . '</b>';
	                    }                    
	                    $message .= "Dear " . Users::getNamebyId($select['old_qa']) . ",<br/><br/> Kindly note QC task: " . $task_phase . ", Project: <b>" . Projects::getNamebyId($select['id_project']) . "</b> for resource <b>" . Users::getNamebyId($select['id_user']) . "</b> has been re-assigned to a new QA</b>. <br/><br/>Thank you,<br/>SNSit";
	                    $subject = $notif['name'];
	                    $replace = array(
	                        EmailNotificationsGroups::getGroupDescription($notif['id']),    $message
	                        
	                    );
	                    $body    = str_replace($to_replace, $replace, $notif['message']);                    
	                    $userEmail = UserPersonalDetails::getEmailById($select['old_qa']);
	                    $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);
	                    Yii::app()->mailer->ClearAddresses();                    
	                   if($select['type'] == 1){
							Yii::app()->mailer->AddAddress("Ghina.Karame@sns-emea.com");
              				Yii::app()->mailer->AddAddress("Bernard.Khazzaka@sns-emea.com");
						}else{
							Yii::app()->mailer->AddAddress("Charbel.Azzi@sns-emea.com");
		              		Yii::app()->mailer->AddAddress("Wael.Mabsout@sns-emea.com");
						}
	                    if (!empty($userEmail)) {
	                        Yii::app()->mailer->AddAddress($userEmail);
	                    }
	                    Yii::app()->mailer->Subject = "Quality Control";
	                    Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
	                    Yii::app()->mailer->Send(true);
	                }  
	                $to_replace = array(
	                    '{group_description}','{body}'
	                );                
	                $message = "";
	                if ($select['id_phase'] == '0') {
	                    $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
	                } else {
	                    $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
	                    $task_phase .= '- Phase: <b>' . ProjectsPhases::getPhaseDescByPhaseId($select['id_phase']) . '</b>';
	                }                
	                $message .= "Dear " . Users::getNamebyId($select['id_resc']) . ",<br/><br/> Kindly note that you are requested to QC the following task: <br/><br/>FBR: " . $task_phase . "<br/>Project: <b>" . Projects::getNamebyId($select['id_project']) . "</b><br/>Resource: <b>" . Users::getNamebyId($select['id_user']) . "</b><br/>Expected delivery date: <b>" . date('d/m/Y', strtotime($select['expected_delivery_date'])) . "</b>. <br/><br/>Thank you,<br/>SNSit";
	                $subject = $notif['name'];
	                $replace = array(
	                    EmailNotificationsGroups::getGroupDescription($notif['id']),$message                    
	                );
	                $body    = str_replace($to_replace, $replace, $notif['message']);                
	                $userEmail = UserPersonalDetails::getEmailById($select['id_resc']);
	                $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);
	                Yii::app()->mailer->ClearAddresses();
	               if($select['type'] == 1){
						Yii::app()->mailer->AddAddress("Ghina.Karame@sns-emea.com");
              		    Yii::app()->mailer->AddAddress("Bernard.Khazzaka@sns-emea.com");
					}else{
						Yii::app()->mailer->AddAddress("Charbel.Azzi@sns-emea.com");
	              		Yii::app()->mailer->AddAddress("Wael.Mabsout@sns-emea.com");
					}
	                if (!empty($userEmail)) {
	                    Yii::app()->mailer->AddAddress($userEmail);
	                }
	                Yii::app()->mailer->Subject = "Quality Control";
	                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
	                Yii::app()->mailer->Send(true);                
	                $dp = Yii::app()->db->createCommand("update  quality set old_qa=id_resc WHERE id=" . $select['id'] . " ")->execute(); 
	            }
	        }
	        if (!empty($tasks)) {
	            $dp = Yii::app()->db->createCommand("update  quality set updated=0 WHERE updated=1 and  id_resc is not null and id_resc!= 0 and expected_delivery_date like '%-%'  and id in (".$all.") ")->execute();
	            echo json_encode(array(
	                "status" => "done",
	                "message" => "Email(s) were sent."
	            ));
	            exit;
	        } else {
	            $count = Yii::app()->db->createCommand("SELECT count(1) FROM quality WHERE updated=1  and id in (".$all.")")->queryScalar();
	            if ($count == 0) {
	                echo json_encode(array(
	                    "status" => "done","message" => "QA task(s) were not updated."
	                ));
	                exit;
	            } else {
	                echo json_encode(array(
	                    "status" => "done","message" => "Cannot send QA without assigned QA or QA delivery date being set."
	                ));
	                exit;
	            }
	        }
	    }
    }    
    public function actionsendQAEmailReminder(){
        $tasks = Yii::app()->db->createCommand("SELECT * FROM quality WHERE expected_delivery_date =CURRENT_DATE() + INTERVAL 3 day")->queryAll();        
        $notif = EmailNotifications::getNotificationByUniqueName('qa_task');
        if ($notif != NULL && !empty($tasks)) {
            foreach ($tasks as $select) {
                $to_replace = array(
                    '{group_description}','{body}'
                );
                $message = "";
                if ($select['internal_project']>0)	{
                	$task_phase ='<b>'.InternalTasks::getNameById($select['id_task']).'</b>';
                	$pname= Internal::getNamebyId($select['id_project']).' (internal project)';
                }else if ($select['id_phase'] == '0') {
                    $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
                    $pname =Projects::getNamebyId($select['id_project']);
                } else {
                    $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
                    $task_phase .= '- Phase: <b>' . ProjectsPhases::getPhaseDescByPhaseId($select['id_phase']) . '</b>';
                    $pname =Projects::getNamebyId($select['id_project']);
                }                
                $message .= "Dear " . Users::getNamebyId($select['id_resc']) . ",<br/><br/> Kindly note that the following QA task <b>" . $task_phase . "</b> - Project: <b>" . $pname . "</b> is expected to be delivered in 3 days. <br/><br/>Thank you,<br/>SNSit";
                $subject = $notif['name'];
                $replace = array(
                    EmailNotificationsGroups::getGroupDescription($notif['id']),$message
                    
                );
                $body    = str_replace($to_replace, $replace, $notif['message']);                
                $userEmail = UserPersonalDetails::getEmailById($select['id_resc']);
                $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);
                Yii::app()->mailer->ClearAddresses();
				//Yii::app()->mailer->AddCcs("Ghina.Karame@sns-emea.com");
				if($select['type'] == 1){
					Yii::app()->mailer->AddAddress("Ghina.Karame@sns-emea.com");
              		  Yii::app()->mailer->AddAddress("Bernard.Khazzaka@sns-emea.com");
				}else{
					Yii::app()->mailer->AddAddress("Charbel.Azzi@sns-emea.com");
              		  Yii::app()->mailer->AddAddress("Wael.Mabsout@sns-emea.com");
				}
               // Yii::app()->mailer->AddCcs("Bernard.Khazzaka@sns-emea.com");
                if (!empty($userEmail)) {
                    Yii::app()->mailer->AddAddress($userEmail);
                }
                Yii::app()->mailer->Subject = "Quality Control Reminder";
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                Yii::app()->mailer->Send(true);
            }
        }
    }    
    public function sendQAEmailCancelled($task){
        $select = Yii::app()->db->createCommand("SELECT * FROM quality WHERE id=" . $task . " ")->queryRow();        
        $notif = EmailNotifications::getNotificationByUniqueName('qa_task');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $message = "";
            if ($select['id_phase'] == '0') {
                $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
            } else {
                $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
                $task_phase .= '- Phase: <b>' . ProjectsPhases::getPhaseDescByPhaseId($select['id_phase']) . '</b>';
            }            
            $message .= "Dear " . Users::getNamebyId($select['id_resc']) . ",<br/><br/> Kindly note that the following QA task <b>" . $task_phase . "</b> - Project: <b>" . Projects::getNamebyId($select['id_project']) . "</b> has been <b>Cancelled</b>. <br/><br/>Thank you,<br/>SNSit";
            $subject = $notif['name'];
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $message                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $userEmail = UserPersonalDetails::getEmailById($select['id_resc']);
            $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
        	  if($select['type'] == 1){
					Yii::app()->mailer->AddAddress("Ghina.Karame@sns-emea.com");
              		  Yii::app()->mailer->AddAddress("Bernard.Khazzaka@sns-emea.com");
				}else{
					Yii::app()->mailer->AddAddress("Charbel.Azzi@sns-emea.com");
              		  Yii::app()->mailer->AddAddress("Wael.Mabsout@sns-emea.com");
				}
            if (!empty($userEmail)) {
                Yii::app()->mailer->AddAddress($userEmail);
            }
            Yii::app()->mailer->Subject = "Quality Control - Cancellation";
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);            
        }
    }    
    public function sendQAEmail($task){
        $select = Yii::app()->db->createCommand("SELECT * FROM quality WHERE id=" . $task . " ")->queryRow();        
        $notif = EmailNotifications::getNotificationByUniqueName('qa_task');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $message = "";
            if ($select['id_phase'] == '0') {
                $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
            } else {
                $task_phase = '<b>' . ProjectsTasks::getTaskDescByid($select['id_task']) . '</b>';
                $task_phase .= '- Phase: <b>' . ProjectsPhases::getPhaseDescByPhaseId($select['id_phase']) . '</b>';
            }            
            $message .= "Dear " . Users::getNamebyId($select['id_resc']) . ",<br/><br/> Kindly note that you are requested to QC the following task: <br/><br/>FBR: " . $task_phase . "<br/>Project: <b>" . Projects::getNamebyId($select['id_project']) . "</b><br/>Resource: <b>" . Users::getNamebyId($select['id_user']) . "</b><br/>Expected delivery date: <b>" . date('d/m/Y', strtotime($select['expected_delivery_date'])) . "</b>. <br/><br/>Thank you,<br/>SNSit";
            $subject = $notif['name'];
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $message                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $userEmail = UserPersonalDetails::getEmailById($select['id_resc']);
            $emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
           if($select['type'] == 1){
					Yii::app()->mailer->AddAddress("Ghina.Karame@sns-emea.com");
              		  Yii::app()->mailer->AddAddress("Bernard.Khazzaka@sns-emea.com");
				}else{
					Yii::app()->mailer->AddAddress("Charbel.Azzi@sns-emea.com");
              		  Yii::app()->mailer->AddAddress("Wael.Mabsout@sns-emea.com");
				}
            if (!empty($userEmail)) {
                Yii::app()->mailer->AddAddress($userEmail);
            }
            Yii::app()->mailer->Subject = "Quality Control";
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);            
        }
    }    
    public function actionassignDate(){        
        if (isset($_POST['date']) && isset($_POST['taskid'])) {            
            $dp     = Yii::app()->db->createCommand("update  quality set expected_delivery_date='" . $_POST['date'] . "' WHERE id='" . $_POST['taskid'] . "' ")->execute();
            $select = Yii::app()->db->createCommand("SELECT count(1) FROM quality WHERE expected_delivery_date<>'' and id_resc is not null and id=" . $_POST['taskid'] . " ")->queryScalar();
            if ($select > 0) {
                self::sendQAEmail($_POST['taskid']);
            }            
            echo json_encode(array(
                'status' => 'success'
            ));
            exit;
        }        
    }
    public function actionassignQA(){        
        if (isset($_POST['user']) && isset($_POST['taskid'])) {            
            $dp     = Yii::app()->db->createCommand("update  quality set id_resc='" . $_POST['user'] . "' WHERE id='" . $_POST['taskid'] . "' ")->execute();
            $select = Yii::app()->db->createCommand("SELECT count(1) FROM quality WHERE expected_delivery_date<>'' and id_resc is not null and id=" . $_POST['taskid'] . " ")->queryScalar();
            if ($select > 0) {
                self::sendQAEmail($_POST['taskid']);
            }
            echo json_encode(array(
                'status' => 'success',
                'user' => $_POST['user']
            ));
            exit;
        }
    }
}?>