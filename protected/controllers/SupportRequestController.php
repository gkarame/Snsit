<?php
class SupportRequestController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){	return array(	'accessControl', 	'postOnly + delete', 	);	}
	public function accessRules(){
		return array(
			array('allow',
					'actions'=>array('sendRSRsSummary'),
					'users'=>array('*'),
			),
			array('allow', 
				'actions'=>array('index','getHistory','delete','cancel','view','update','assigned','updateHeader','createFromSr','linkFromSr','upload','deleteUpload','postComment','escalateSR',
				'upload_comm','getExcel','getExcelGrid','newIssues','RateTicket'),
				'users'=>array('@'),
			),
			array('allow', 
				'actions'=>array('create','statistics'),
				'expression'=>'!$user->isGuest AND !$user->isAdmin',
			),
			array('allow', 
				'actions'=>array('sendFollowUp'),
				 'expression'=>'$user->isAdmin',
			),
			array('deny', 
				'users'=>array('*'),
			),		);	}
	public function actionView($id){		$this->render('view',array(		'model'=>$this->loadModel($id),	));	}
	public function actions(){
		return array(
				'upload'=>array(
					 'class'=>'xupload.actions.CustomXUploadAction',
					 'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
					 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
					 'stateVariable' => 'rsr_uploads'
				),'upload_comm'=>array(
					 'class'=>'xupload.actions.CustomXUploadAction',
					 'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
					 'publicPath' => Yii::app()->getBaseUrl() . "/uploads/tmp",
					 'stateVariable' => 'rsr_uploads_comm'
				),		);	
	}
	
	public function actionCancel($id){
		$model = $this->loadModel($id);
		$model->status= 7;
		if ($model->save()) {			
			if(!isset($_GET['ajax']))
  			   $this->redirect(Yii::app()->request->urlReferrer);
		} else{
		echo json_encode(array('status'=>'error'));	

		}
	}
	public function actionDelete($id){
		$model = $this->loadModel($id);
		if ($model->delete()) {			
			echo json_encode(array('status'=>'saved'));	
			exit();
		} 
		echo json_encode(array('status'=>'error'));	
	}
	public function actionDeleteUpload(){	
		if (isset($_GET['model_id'], $_GET['file'])){
			$id_rsr = (int)$_GET['model_id'];
			$filename = $_GET['file'];			
			if (isset($_GET['comm'])){
				$id_att = Yii::app()->db->createCommand("SELECT id FROM rsr_comm_files WHERE id_rsr = '$id_rsr' AND filename = '$filename' ORDER BY id DESC")->queryScalar();
				if ($id_att != null){
					$id_customer = Yii::app()->db->createCommand("SELECT id_customer from rsr where id = '$id_rsr'")->queryScalar();
					$filepath = SupportRequest::getDirPathComm($id_customer, $id_rsr,true).$_GET['file'];
					$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
					if ($success){
						Yii::app()->db->createCommand("Delete from rsr_comm_files where id = $id_att")->execute();
						$ids = explode(',', Yii::app()->session['id']);
						Yii::app()->session['id'] = "";
						foreach ($ids as $id){
							if ($id != $id_att ){
								Yii::app()->session['id'] .= $id.",";
							}		
						}
					}
				}		
			}
			/*else{	
				$id_att = Yii::app()->db->createCommand("SELECT id FROM rsr_files WHERE ISNULL(id_rsr) AND filename = '$filename' ORDER BY id DESC")->queryScalar();
				if ($id_att != null)
				{
					$filepath = SupportRequest::getDirPath(Yii::app()->user->customer_id, $id_rsr).$_GET['file'];
					$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
					if ($success)
					{
						Yii::app()->db->createCommand("Delete from rsr_files where id = $id_att")->execute();
						$ids = explode(',', Yii::app()->session['id_files']);
						Yii::app()->session['id_files'] = "";
						foreach ($ids as $id)
						{
							if ($id != $id_att)
							{
								Yii::app()->session['id_files'] .= $id.",";
							}
						}	}	}	
			}*/
		}else if($_GET['id'] != null){
				
			$id_rsr = $_GET['id'];
			$model = $this->loadModel($id_rsr);
			$filepath = SupportRequest::getDirPath($_GET['customer'], $id_rsr).$_GET['filename'];
			$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
			if ($success){
				$id_att_sr = Yii::app()->db->createCommand("SELECT id FROM rsr_files WHERE id_rsr = {$id_rsr} AND filename ='{$_GET['filename']}' ")->queryScalar();
				if($id_att_sr != null)
				{
					$nr = Yii::app()->db->createCommand("Delete from rsr_files where id = {$id_att_sr}")->execute();
					echo json_encode(array ('status' =>'success','html' => $this->renderPartial('_attachements', array('model' => $model), true, false)));
				}
			}	
		}
	}	
	public function actionAdmin(){
		$model=new SupportRequest('search');
		$model->unsetAttributes();  
		if (isset($_GET['SupportRequest']))
			$model->attributes=$_GET['SupportRequest'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('general-rsr')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['SupportRequest']) ? $_GET['SupportRequest'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportRequest/index' => array(
						'label'=>Yii::t('translations', 'RSRs'),
						'url' => array('supportRequest/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new SupportRequest('search');
		$model->unsetAttributes();  
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
			'provider' => $model->search()
		));
	}

	public  function actionSendRSRsSummary(){
		$notif = EmailNotifications::getNotificationByUniqueName('rsr_summary');
		$rsrs= Yii::app()->db->createCommand("SELECT * from rsr WHERE status != 6 and  status!=4 ")->queryAll();
		$resolvedrsr= Yii::app()->db->createCommand("SELECT * from rsr WHERE status =4 ")->queryAll();
		$closedrsrs= Yii::app()->db->createCommand("SELECT * from rsr WHERE status =6 and YEAR(closedate)=Year(current_date())")->queryAll();
		if ($notif != NULL && (!empty($rsrs) || !empty($closedrsrs) || !empty($resolvedrsr)))
		{
			$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/>Please find below a summary of RSRs:<br/><br/>";

            if(!empty($rsrs))
            {
            	$message .= "<b>Pending RSRs:</b><br/><br/>";
            	$message .= '<table  border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th>RSR#</th><th width="200">Customer</th><th  width="400">Description</th><th>Status</th><th>Severity</th><th>Version</th><th>DB Type</th><th width="200">Assigned To</th><th  width="50">Linked SRs#</th><th>Logged On</th></tr>';
		        foreach($rsrs as $rsr)	{
					$message .="<tr><td><a href='".Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$rsr['rsr_no']))."'>".$rsr['rsr_no']."</a> </td><td>".Customers::getNameById($rsr['id_customer'])."</td><td>".$rsr['short_description']."</td><td>".SupportRequest::getStatusLabel($rsr['status'])."</td><td>".$rsr['severity']."</td><td>".Codelkups::getCodelkup($rsr['version'])."</td><td>".Codelkups::getCodelkup($rsr['dbms'])."</td><td>".Users::getNameById($rsr['assigned_to'])."</td><td>".str_replace(',', ', ', $rsr['sr'])."</td><td>".date('d/m/Y', strtotime($rsr['adddate']))."</td></tr>";
				}
				$message.= "</table><br/>";
            }

            if(!empty($resolvedrsr))
            {
            	$message .= "<b>Resolved RSRs:</b><br/><br/>";
            	$message .= '<table  border="1"  style="font-family:Calibri;border-collapse: collapse;" > <tr><th>RSR#</th><th width="200">Customer</th><th width="400">Description</th><th>Status</th><th>Severity</th><th>Version</th><th>DB Type</th><th width="200">Assigned To</th><th width="50">Linked SRs#</th><th>Logged On</th></tr>';
		        foreach($resolvedrsr as $rsr)	{
					$message .="<tr><td><a href='".Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$rsr['rsr_no']))."'>".$rsr['rsr_no']."</a> </td><td>".Customers::getNameById($rsr['id_customer'])."</td><td>".$rsr['short_description']."</td><td>".SupportRequest::getStatusLabel($rsr['status'])."</td><td>".$rsr['severity']."</td><td>".Codelkups::getCodelkup($rsr['version'])."</td><td>".Codelkups::getCodelkup($rsr['dbms'])."</td><td>".Users::getNameById($rsr['assigned_to'])."</td><td>".str_replace(',', ', ', $rsr['sr'])."</td><td>".date('d/m/Y', strtotime($rsr['adddate']))."</td></tr>";
				}
				$message.= "</table><br/>";
            }

			if(!empty($closedrsrs))
            {
            	$message .= "<b>Closed RSRs:</b><br/><br/>";
            	$message .= '<table border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th>RSR#</th><th width="200">Customer</th><th width="200">Description</th><th>Severity</th><th>Version</th><th>DB Type</th><th width="200">Assigned To</th><th width="50">Linked SRs#</th><th>Logged On</th><th>Close Date</th></tr>';
		        foreach($closedrsrs as $rsr)	{
					$message .="<tr><td><a href='".Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$rsr['rsr_no']))."'>".$rsr['rsr_no']."</a></td><td>".Customers::getNameById($rsr['id_customer'])."</td><td>".$rsr['short_description']."</td><td>".$rsr['severity']."</td><td>".Codelkups::getCodelkup($rsr['version'])."</td><td>".Codelkups::getCodelkup($rsr['dbms'])."</td><td>".Users::getNameById($rsr['assigned_to'])."</td><td>".str_replace(',', ', ', $rsr['sr'])."</td><td>".date('d/m/Y', strtotime($rsr['adddate']))."</td><td>".date('d/m/Y', strtotime($rsr['closedate']))."</td></tr>";
				}
				$message.= "</table><br/>";
            }            

			$message.= "Best Regards,<br/>SNSit";

	        $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
            
			$emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
            foreach($emails as $email)	{
				Yii::app()->mailer->AddAddress($email);
			}
			//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
            Yii::app()->mailer->Subject =  "RSRs Summary";
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);     
    	}	
    }
   	public function actiongetHistory()
	{
		$ids = $_POST['id'];
		 $comments= Yii::app()->db->createCommand("select id_support_desk,comment,date, id_user, is_admin, status,sender from support_desk_comments where id_support_desk in (".$ids.") order by id_support_desk, date ASC")->queryAll();
   		$sr=''; $printsr= false; 
   		$table='<table style=" border: 1px solid grey;border-collapse: collapse;"><tr style=" border: 1px solid grey;border-collapse: collapse;"><th style="border: 1px solid grey;border-collapse: collapse;">SR#</th><th style="border: 1px solid grey;border-collapse: collapse;">Comment</th><th style="border: 1px solid grey;border-collapse: collapse;">Date</th><th style="border: 1px solid grey;border-collapse: collapse;">User</th><th style="border: 1px solid grey;border-collapse: collapse;">Status</th></tr>';

   		foreach ($comments as $comment) {
   			if($sr != $comment['id_support_desk'])
   			{
	   			$sr= $comment['id_support_desk'];
	   			$printsr=true;
   			}else{
   			//	$printsr=false;
   			}
   			$table .= "<tr style='border: 1px solid grey;border-collapse: collapse;'>";
   			if($printsr)
   			{
   				$table .= "<td style='border: 1px solid grey;border-collapse: collapse;'>SR#".$comment['id_support_desk']."</td>";
   			}else{
   				$table .= "<td style='border: 1px solid grey;border-collapse: collapse;'></td>";
   			}
   			if($comment['is_admin'] == 1)
   			{
   				$user=  Users::getUsername($comment['id_user']);
   			}else{
   				$user= CustomersContacts::getNameById($comment['id_user']);
   			}
   			$user= 
   			$table .= "<td style='border: 1px solid grey;border-collapse: collapse;'>".$comment['comment']."</td><td style='border: 1px solid grey;border-collapse: collapse;'>".date("d/m/Y H:i:s",strtotime($comment['date']))."</td><td style='border: 1px solid grey;border-collapse: collapse;'>".$user."</td><td style='border: 1px solid grey;border-collapse: collapse;'>".SupportDesk::getStatusLabel($comment['status'])."</td></tr>";
   		}

   		$table.='</table>';
   		echo json_encode(array('status'=>'success', 'table'=>$table));
						exit;
   	}
	public function actionAssigned(){
		$id = (int)$_POST['id_rsr'];
		$model = SupportRequest::model()->findByPk($id);
		$oldAssigned=0; $close=false; $oldstat=$model->status;
		switch ($_POST['type'])	{
			case '1':
				$model->eta = DateTime::createFromFormat('d/m/Y', $_POST['value'])->format('Y-m-d');
				break;
			case '2':
				$oldAssigned= $model->assigned_to;
				$model->assigned_to = $_POST['value'];
				break;
			case '3':
				$model->root = $_POST['value'];
				break;
			case '4':
				$model->status = $_POST['value'];
				if($model->status == 6){
					if(SupportRequest::validateCloseSRs($model->sr) == 0 )
					{
						$model->closedate= date('Y-m-d H:i:s');
						$close=true;
					}else{
						echo json_encode(array('status'=>'failure', 'errormessage'=>'RSR Cannot be Closed when linked SR(s) are not Closed'));
						exit;
					}
					
				}
				break;	
			case '5':
				$model->category = $_POST['value'];
				break;	
					
		}
	    if ($model->save()) {
	    	if($oldstat != $model->status && $oldstat ==8 && $model->status != 6)
	    	{
	    		$comment= Yii::app()->db->createCommand("SELECT comment FROM rsr_comments WHERE id_rsr = ".$model->id." order by date asc limit 1")->queryScalar();
	    		SupportRequest::sendEmailNew($model,$comment);
	    	}
	    	if ($_POST['type'] == 2){
	    		SupportRequest::sendEmailAssigned($model, $oldAssigned);
	    	}
	    	if ($_POST['type'] == 4){
	    		Yii::app()->db->createCommand("insert into rsr_comments (id_rsr, id_user, comment, status, date, files) values (".$model->id.", ".Yii::app()->user->id.", '".SupportRequest::getStatusLabel($model->status)."', ".$model->status.", CURRENT_TIMESTAMP(),0)")->execute();
	    		echo json_encode(array_merge(array(
					'status'=>'saved',
					'html' => $this->renderPartial('_posts_table', array('model' => $model), true, true),
					'html2' => $this->renderPartial('_comment', array('model' => $model), true, true),
				)));
				exit;
	    	}
	    	if($close){
	    		SupportRequest::sendEmailClosed($model);
	    	}
			echo json_encode(array_merge(array('status'=>'saved')));
			exit;
	    }
	    echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		exit;
	}

	public function loadModel($id){
		$model=SupportRequest::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model){
		if (isset($_POST['ajax']) && $_POST['ajax']==='supportrequest-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionGetExcelGrid(){		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS RSRs Export");
		$model =SupportRequest::model(); 
		$model->product= $_POST['SupportRequest']['product'];		
		if(isset($_POST['SupportRequest']['status']) && count($_POST['SupportRequest']['status']) == count(array_filter($_POST['SupportRequest']['status'], "strlen"))){	
			$statuss=$_POST['SupportRequest']['status'];
					$inv_status="";		
		        	foreach ($statuss as $value) {
		        	$inv_status.="".rtrim(ltrim($value," ")," ").",";
		        	}
		        	$mstatus= " in (".$inv_status." -1 ) ";
		}else{		$mstatus ="";		}
		$model->severity= $_POST['SupportRequest']['severity'];
		$model->id_customer= $_POST['SupportRequest']['id_customer'];
		$model->id= $_POST['SupportRequest']['rsr_no'];
		$model->assigned_to= $_POST['SupportRequest']['assigned_to'];
		
		$select = "SELECT s.* FROM rsr s";
		$where = " WHERE 1=1 ";
		if($model->product !=""){
			$where .= " AND s.product ='$model->product'";
		}
		if($mstatus !=""){
			$where .= " AND s.status ".$mstatus." ";
		}
		if($model->severity !=""){
			$where .= " AND s.severity ='$model->severity'";
		}
		if($model->id_customer !=""){
			$customer = Customers::getIdByName($model->id_customer);
			$where .= " AND s.id_customer ='$customer'";
		}
		if($model->id !=""){
			$where .= " AND s.id =$model->id";
		}
		if($model->assigned_to !=""){
			$first_name = substr($model->assigned_to,0,strrpos($model->assigned_to," "));
			$last_name = substr(strstr($model->assigned_to, ' '),1);
			$id_user = Yii::app()->db->createCommand("SELECT id FROM users WHERE firstname = '$first_name' AND lastname = '$last_name'")->queryScalar();
			$where .= " AND s.assigned_to ='$id_user'";
		}
		$data = Yii::app()->db->createCommand($select.$where)->queryAll();
		$sheetId = 0;

		$nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:R1')->applyFromArray($styleArray); 

		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'RSR#')
		->setCellValue('B1', 'Description')
		->setCellValue('C1', 'Customer')
		->setCellValue('D1', 'Submitter')
		->setCellValue('E1', 'Severity')
		->setCellValue('F1', 'Schema')
		->setCellValue('G1', 'Status')
		->setCellValue('H1', 'Category')		
		->setCellValue('I1', 'Product')
		->setCellValue('J1', 'DB Type')
		->setCellValue('K1', 'WMS Version')
		->setCellValue('L1', 'RSR Root')
		->setCellValue('M1', 'Linked SRs#')
		->setCellValue('N1', 'Expected Delivery Date')
		->setCellValue('O1', 'Assigned To')
		->setCellValue('P1', 'Logged By')
		->setCellValue('Q1', 'Creation Date')
		->setCellValue('R1', 'Hours Spent')
		;		
		$i = 1;

		foreach($data as $d => $row){	 
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValueExplicit('A'.$i, $row['rsr_no'],PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i, $row['short_description'])
			->setCellValue('C'.$i, Customers::getNameById($row['id_customer']))
			->setCellValue('D'.$i, $row['submitter_name'])
			->setCellValue('E'.$i, $row['severity'])
			->setCellValue('F'.$i, $row['schema'])
			->setCellValue('G'.$i, SupportRequest::getStatusLabel($row['status']))
			->setCellValue('H'.$i, SupportRequest::getCategoryLabel($row['category']))
			->setCellValue('I'.$i, Codelkups::getCodelkup($row['product']))
			->setCellValue('J'.$i, Codelkups::getCodelkup($row['dbms']))
			->setCellValue('K'.$i, Codelkups::getCodelkup($row['version']))
			->setCellValue('L'.$i, SupportRequest::getRootLabel($row['root']))
			->setCellValue('M'.$i, $row['sr'])
			->setCellValue('N'.$i, !empty($row['eta']) ? date('d/m/Y', strtotime($row['eta'])) : '')	
			->setCellValue('O'.$i, Users::getNameById($row['assigned_to']))
			->setCellValue('P'.$i, Users::getNameById($row['logged_by']))				
			->setCellValue('Q'.$i,  date('d/m/Y', strtotime($row['adddate'])))				
			->setCellValue('R'.$i,  SupportRequest::countTime($row['id'], $row['id_customer'], $row['sr']))	
			;
		}		
		$objPHPExcel->getActiveSheet()->setTitle('RSRs');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="RSRs.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/excel/export.xls";
		$objWriter->save($path);
		echo json_encode(array ('success' =>'success'));
		exit;
	} 
	public function actionGetExcel($id){
		$data = SupportDesk::model()->findAllByPk((int)$id);		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS RSRs Export");
		
		$select = "SELECT s.* FROM rsr s";
		$where = " WHERE id=".$id." ";
		
		$data = Yii::app()->db->createCommand($select.$where)->queryAll();
		$sheetId = 0;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'RSR#')
		->setCellValue('B1', 'Description')
		->setCellValue('C1', 'Customer')
		->setCellValue('D1', 'Submitter')
		->setCellValue('E1', 'Severity')
		->setCellValue('F1', 'Schema')
		->setCellValue('G1', 'Status')
		->setCellValue('H1', 'Category')		
		->setCellValue('I1', 'Product')
		->setCellValue('J1', 'DB Type')
		->setCellValue('K1', 'WMS Version')
		->setCellValue('L1', 'RSR Root')
		->setCellValue('M1', 'Linked SRs#')
		->setCellValue('N1', 'Expected Delivery Date')
		->setCellValue('O1', 'Assigned To')
		->setCellValue('P1', 'Logged By')
		->setCellValue('Q1', 'Creation Date')
		;		
		$i = 1;

		foreach($data as $d => $row){	 
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValueExplicit('A'.$i, $row['rsr_no'],PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i, $row['short_description'])
			->setCellValue('C'.$i, Customers::getNameById($row['id_customer']))
			->setCellValue('D'.$i, $row['submitter_name'])
			->setCellValue('E'.$i, $row['severity'])
			->setCellValue('F'.$i, $row['schema'])
			->setCellValue('G'.$i, SupportRequest::getStatusLabel($row['status']))
			->setCellValue('H'.$i, SupportRequest::getCategoryLabel($row['category']))
			->setCellValue('I'.$i, Codelkups::getCodelkup($row['product']))
			->setCellValue('J'.$i, Codelkups::getCodelkup($row['dbms']))
			->setCellValue('K'.$i, Codelkups::getCodelkup($row['version']))
			->setCellValue('L'.$i, SupportRequest::getRootLabel($row['root']))
			->setCellValue('M'.$i, $row['sr'])
			->setCellValue('N'.$i, $row['eta'])	
			->setCellValue('O'.$i, Users::getNameById($row['assigned_to']))
			->setCellValue('P'.$i, Users::getNameById($row['logged_by']))				
			->setCellValue('Q'.$i,  date('m/d/Y', strtotime($row['adddate'])))	
			;
		}		
		$objPHPExcel->getActiveSheet()->setTitle('RSRs');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="RSRs.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/excel/export.xls";
		$objWriter->save($path);
		echo json_encode(array ('success' =>'success'));
		exit;
	}
	public function actionUpdate($id){
			$model=$this->loadModel($id);			
		if (!GroupPermissions::checkPermissions('general-rsr','write') || ($model->status == 8 && Users::checkCSManagers(Yii::app()->user->id)== 0) ){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		unset(Yii::app()->session['id']);
		unset(Yii::app()->session['id_files']);		
		$model=$this->loadModel($id);
		$customer = Customers::model()->findByPk($model->id_customer);	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportRequest/update/'.$id => array(
						'label'=>Yii::t('translations', substr($customer->name, 0, 8).' RSR#'.$model->rsr_no),
						'url' => array('supportRequest/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model=$this->loadModel($id);
		if (isset($_POST['SupportRequest'])){
			$model->attributes=$_POST['SupportRequest'];
			if ($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('update',array('model'=>$model,	));
	}
	public function actionPostComment($id, $changeStatus = null){
		$id = (int) $id;	$model = new SupportRequestComments();	$model_support = SupportRequest::model()->findByPk($id);;
		$status = ""; 		
		if (isset($_POST['rejected_message']) && $_POST['rejected_message'] != null ){
			if (!empty(Yii::app()->session['id'])){
				$ids = explode(',', Yii::app()->session['id']);
				$count = count($ids) - 1;
				unset(Yii::app()->session['id']);
			}else{
				$ids = array();
				$count = 0;
			}		

			$model->id_rsr = $id;
			$model->id_user = Yii::app()->user->id;
			$model->date =  date('Y-m-d H:i:s ', strtotime('now'));
			$model->comment = $_POST['rejected_message'];
			$model->status = $model_support->status;			
			$model->files = $count; 						
			if ($model->save()){				
				self::updateCommId($ids, $model->id,  'rsr_comm_files', 'id_comm');
				SupportRequest::sendEmail($model_support->id, $model->id);
				
				echo json_encode(array_merge(array(
					'status'=>'saved',
					'html' => $this->renderPartial('_posts_table', array('model' => $model_support), true, true),
					'html2' => $this->renderPartial('_comment', array('model' => $model_support), true, true),
					'current_status'=>SupportRequest::getStatusLabel($model->status),
				)));
				Yii::app()->end();	
			}
		}
		Yii::app()->clientScript->scriptMap=array(
			'jquery.js'=>false,
			'jquery.min.js' => false,
			'jquery-ui.min.js' => false,
		);
		echo json_encode(array_merge(array(
			'status'=>'no_message',
		)));
		exit;
	}

	public function updateCommId($ids, $id_comm, $table, $column){
		if (!empty($ids)){
			$new_ids = array();
			foreach ($ids as $id)
			{
				if (!empty($id)){
					$new_ids[] = $id;
				}
			}
			$ids_str = implode(',', $new_ids);
			if(!empty($ids_str))
				Yii::app()->db->createCommand("UPDATE `$table` SET `$column` ='".(int)$id_comm."' WHERE id IN (".$ids_str.")")->execute();
		}	
	}
	public function actionlinkFromSr(){
		if (Yii::app()->user->isAdmin && isset($_POST['id']))
		{
			$sr = $_POST['id'];
			$rsr= $_POST['rsr'];

			$model_rsr = SupportRequest::model()->findByPk($rsr);
			$model_rsr->sr= $model_rsr->sr.','.$sr;

			if ($model_rsr->save())	{
				$returnArr['valid']=1;
				$returnArr['num']= '<a href="'.Yii::app()->createAbsoluteUrl("SupportRequest/update/".$model_rsr->rsr_no).'">'.$model_rsr->rsr_no.'</a>';
				
				echo json_encode($returnArr);					
				exit;
			}else{
				$returnArr['valid']=0;
				echo json_encode($returnArr);					
				exit;
			}
		}
	}
	public function actionCreateFromSr(){
		if (Yii::app()->user->isAdmin && isset($_POST['id']) && isset($_POST['description']) && isset($_POST['category'] ))
		{
			$sr = $_POST['id'];
			if (empty(SupportRequest::getRSR($sr)))
			{
				$model_support = SupportDesk::model()->findByPk($sr);
				$model_rsr= new SupportRequest();

				$model_rsr->sr= $model_support->sd_no;
				$model_rsr->category= $_POST['category'];
				$model_rsr->id_customer= $model_support->id_customer;
				$model_rsr->short_description= $_POST['description'];
				$model_rsr->product= $model_support->product;
				$model_rsr->schema= $model_support->schema;
				$model_rsr->severity= $model_support->severity;
				$model_rsr->submitter_name= $model_support->submitter_name;
				$model_rsr->customer_contact_id= $model_support->customer_contact_id;
				$model_rsr->status= 0;
				$model_rsr->assigned_to=Yii::app()->db->createCommand("SELECT cs_representative from customers WHERE id ='".$model_support->id_customer."'")->queryScalar();
				$model_rsr->logged_by = Yii::app()->user->id;
				$model_rsr->adddate= date('Y-m-d H:i:s');
				$model_rsr->dbms= Maintenance::getDBMSPerProduct($model_support->id_customer, $model_support->product);
				$model_rsr->version= Maintenance::getVersionPerProduct($model_support->id_customer, $model_support->product);
				if((Users::checkCSManagers(Yii::app()->user->id)) == 0)
				{
					$model_rsr->status=8;
				}
				$model_rsr->rsr_no = "00000";
				if ($model_rsr->save())	{
					$comment = $_POST['comm'];
					$comm = new SupportRequestComments();
					$comm->id_rsr = $model_rsr->id;
					$comm->id_user = Yii::app()->user->id;
					$comm->date =  date('Y-m-d H:i:s ', strtotime('now'));
					$comm->comment = $comment;
					if((Users::checkCSManagers(Yii::app()->user->id)) == 0)
				{
					$comm->status=8;
				}else{
					$comm->status =0;	
				}
					
					$comm->save();


					$model_rsr->rsr_no = Utils::paddingCode($model_rsr->id);
					$model_rsr->save();
					if($model_rsr->status != 8 )
					{
						SupportRequest::sendEmailNew($model_rsr, $comment);
					}else{
						SupportRequest::sendEmailPending($model_rsr, $comment);
					}
					$returnArr['valid']=1;
					$returnArr['approved']=$model_rsr->status ;
					$returnArr['num']= '<a href="'.Yii::app()->createAbsoluteUrl("SupportRequest/update/".$model_rsr->rsr_no).'">'.$model_rsr->rsr_no.'</a>';

					;
					
					echo json_encode($returnArr);					
					exit;
				}else{
					$returnArr['valid']=0;
					echo json_encode($returnArr);					
					exit;
				}
			}else{
					$returnArr['valid']=0;
					echo json_encode($returnArr);					
					exit;
			}
		}
	}
	
}	?>

