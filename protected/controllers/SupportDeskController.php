<?php
class SupportDeskController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){	return array(	'accessControl', 	'postOnly + delete', 	);	}
	public function accessRules(){
		return array(
			array('allow',
					'actions'=>array('sendEmailTwiceClosePending','followupReminderClose','followupReminder','monthlyAccounts','sendSms','timePerdiscrp','sendWeeklySummaryEmail','SendNextWeekSupport','SendLicensingEmail','checkunratedsr','disablesrsubmission','sendSummaryEmail','sendAfterHoursTickets','sendPotential','VacationLicenseAudit','updateCheckedUsers','sendSLAEmailNotification'),
					'users'=>array('*'),
			),
			array('allow', 
				'actions'=>array('index','view','update','assigned','checkReassign','updateHeader','upload','deleteUpload','postComment','escalateSR',
				'upload_comm','getExcel','getExcelGrid','newIssues','RateTicket'),
				'users'=>array('@'),
			),
			array('allow', 
				'actions'=>array('create','createCR','statistics'),
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
					 'stateVariable' => 'supportdesk_uploads'
				),'upload_comm'=>array(
					 'class'=>'xupload.actions.CustomXUploadAction',
					 'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
					 'publicPath' => Yii::app()->getBaseUrl() . "/uploads/tmp",
					 'stateVariable' => 'supportdesk_uploads_comm'
				),		);	}
	public function actionCreate(){	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportDesk/create' => array(
						'label'=>Yii::t('translations', 'New Incident'),
						'url' => array('supportDesk/create'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		$model = new SupportDesk;
		$customer = Customers::model()->findByPk(Yii::app()->user->customer_id);
		if (isset($_POST['SupportDesk'])){
			$model->attributes = $_POST['SupportDesk'];
			if (isset(Yii::app()->session['id_files']) && !empty(Yii::app()->session['id_files'])){
				$ids = explode(',', Yii::app()->session['id_files']);
				$count = count($ids) - 1;
			}else{		$ids = array();		$count = 0;	}		
			$model->sd_no = "00000";		$model->status = 0;		$model->id_customer = Yii::app()->user->customer_id;	$model->date = date('Y-m-d H:i:s ', strtotime('now'));		$today= date('Y-m-d', strtotime('now'));
			$dtz= new DateTimeZone('Asia/Beirut');		$time_beirut= new DateTime('now',$dtz);		$offtime= $dtz->getOffset($time_beirut)/3600;
			$model->time_offset=$offtime;		$normal_rep =$customer->cs_representative;			$dat=date('h', strtotime('now'));			
			$valid_saturday=Yii::app()->db->createCommand("select case when ((TIME_TO_SEC(NOW()) NOT BETWEEN TIME_TO_SEC('08:00:00') and TIME_TO_SEC('18:00:00') ) or (DAYNAME(NOW())='Saturday') )then TRUE else FALSE END")->queryScalar();
			$valid_sunday=Yii::app()->db->createCommand("select case when ((TIME_TO_SEC(NOW()) BETWEEN TIME_TO_SEC('08:00:00') and TIME_TO_SEC('18:00:00') ) and (DAYNAME(NOW())='Sunday') )then TRUE else FALSE END")->queryScalar();
			if ( $valid_saturday==1 && $model->system_down=="Yes"){		
			$cs_rep = Yii::app()->db->createCommand("SELECT ifnull(primary_contact,0) FROM fullsupport where '".$today."' BETWEEN from_date and to_date and type=402")->queryScalar();
				if ( is_null($cs_rep) || $cs_rep==' ' || $cs_rep==0 ){  $cs_rep= $customer->cs_representative;  }; 						
			}else if( $valid_sunday==1 && $model->system_down=="Yes") { 			
			$cs_rep = Yii::app()->db->createCommand("SELECT ifnull(primary_contact,0) FROM fullsupport where '".$today."'=from_date and type=403")->queryScalar();
				if (is_null($cs_rep) || $cs_rep==' ' || $cs_rep==0){  $cs_rep= $customer->cs_representative;  }; 				 
			}else {		$cs_rep =$customer->cs_representative;	}; 						
			$model->customer_contact_id =  Yii::app()->user->id;	$model->files = $count;		$model->assigned_to = $cs_rep ;				 
			if ($model->save())	{
				$model->sd_no = Utils::paddingCode($model->id);
				if ($model->severity != 'High')	{	$model->system_down="No";		}			 
				if ($model->save())	{				
					$comment ="";		$comment=$_POST['SupportDesk']['description'];		$customer_name=Customers::getNameById(Yii::app()->user->customer_id);		$id_user=Yii::app()->user->id;				
					if ($model->system_down == "Yes"){		SupportDesk::sendEmailNew($model, 'system_down');		self::sendSms($model->id, $customer_name );		}
					self::insertFirstComment($comment, $model->id, 'support_desk_comments',$id_user,$customer_name );
					self::updateCommId($ids, $model->id, 'support_desk_files', 'id_support_desk');
					$id_support_desk = $model->id;
					$names = Yii::app()->db->createCommand("SELECT filename FROM support_desk_files where id_support_desk = $id_support_desk")->queryAll();
					foreach ($names as $name){
						$destination = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR.$id_support_desk.DIRECTORY_SEPARATOR;
						$src = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR;
						if ( !is_dir( $destination ) ){
				            mkdir( $destination, 0777, true);
				            chmod( $destination, 0777 );       }
		                if( rename( $src.$name["filename"], $destination.$name["filename"] ) ) {
		                    chmod( $destination.$name["filename"], 0777 );
		                }	}
		                //houda
					//SupportDesk::sendEmailNew($model, 'new_sr');
					Utils::closeTab(Yii::app()->createUrl('supportDesk/create'));
					$this->redirect(array('supportDesk/update', 'id'=>$model->id));
				}	}	}
		$this->render('create',array('model'=>$model,	));
	}
	public function actionCreateCR(){	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportDesk/CreateCR' => array(
						'label'=>Yii::t('translations', 'New Incident'),
						'url' => array('supportDesk/CreateCR'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		$model = new SupportDesk;
		if(empty($model->reason)) { $model->reason=407; }
		$customer = Customers::model()->findByPk(Yii::app()->user->customer_id);
		if (isset($_POST['SupportDesk'])){
			$model->attributes = $_POST['SupportDesk'];
			$model->issue_incurred_previously= 'No';
			$model->issue= 'No';
			
			if (isset(Yii::app()->session['id_files']) && !empty(Yii::app()->session['id_files'])){
				$ids = explode(',', Yii::app()->session['id_files']);
				$count = count($ids) - 1;
			}else{		$ids = array();		$count = 0;	}		
			$model->sd_no = "00000";		$model->status = 0;		$model->id_customer = Yii::app()->user->customer_id;	$model->date = date('Y-m-d H:i:s ', strtotime('now'));		$today= date('Y-m-d', strtotime('now'));
			$dtz= new DateTimeZone('Asia/Beirut');		$time_beirut= new DateTime('now',$dtz);		$offtime= $dtz->getOffset($time_beirut)/3600;
			$model->time_offset=$offtime;	$dat=date('h', strtotime('now'));			
			$cs_rep =$customer->ca;
			$model->customer_contact_id =  Yii::app()->user->id;	$model->files = $count;		$model->assigned_to = $cs_rep ;				 
			if ($model->save())	{
				$model->sd_no = Utils::paddingCode($model->id);
				if ($model->severity != 'High')	{	$model->system_down="No";		}			 
				if ($model->save())	{				
					$comment ="";		$comment=$_POST['SupportDesk']['description'];		$customer_name=Customers::getNameById(Yii::app()->user->customer_id);		$id_user=Yii::app()->user->id;				
					if ($model->system_down == "Yes"){		SupportDesk::sendEmailNew($model, 'system_down');		self::sendSms($model->id, $customer_name );		}
					self::insertFirstComment($comment, $model->id, 'support_desk_comments',$id_user,$customer_name );
					self::updateCommId($ids, $model->id, 'support_desk_files', 'id_support_desk');
					$id_support_desk = $model->id;
					$names = Yii::app()->db->createCommand("SELECT filename FROM support_desk_files where id_support_desk = $id_support_desk")->queryAll();
					foreach ($names as $name){
						$destination = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR.$id_support_desk.DIRECTORY_SEPARATOR;
						$src = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR;
						if ( !is_dir( $destination ) ){
				            mkdir( $destination, 0777, true);
				            chmod( $destination, 0777 );       }
		                if( rename( $src.$name["filename"], $destination.$name["filename"] ) ) {
		                    chmod( $destination.$name["filename"], 0777 );
		                }	}
		                //houda
			//	SupportDesk::sendEmailNew($model, 'new_sr');
					Utils::closeTab(Yii::app()->createUrl('supportDesk/CreateCR'));
					$this->redirect(array('supportDesk/update', 'id'=>$model->id));
				}	}	}
		$this->render('create',array('model'=>$model,	));
	}
	public function actionUpdate($id){			
		if (Yii::app()->user->isAdmin && !GroupPermissions::checkPermissions('supportdesk-list','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		unset(Yii::app()->session['id']);
		unset(Yii::app()->session['id_files']);		
		$model=$this->loadModel($id);
		$customer = Customers::model()->findByPk($model->id_customer);	
		if(!Yii::app()->user->isAdmin){
			$vCustomer=  CustomersContacts::getCustomerById(Yii::app()->user->id);
			if($vCustomer!= $model->id_customer)
			{
				throw new CHttpException(403,'You don\'t have permission to access this page.');
			}
		}	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportDesk/update/'.$id => array(
						'label'=>Yii::t('translations', substr($customer->name, 0, 8).' SR#'.$model->sd_no),
						'url' => array('supportDesk/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model=$this->loadModel($id);
		if (isset($_POST['SupportDesk'])){
			$model->attributes=$_POST['SupportDesk'];
			if ($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('update',array('model'=>$model,	));
	}
public function actionStatistics(){			
		unset(Yii::app()->session['id']);
		unset(Yii::app()->session['id_files']);		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportDesk/statistics/'=> array(
						'label'=>Yii::t('translations', 'Statistics'),
						'url' => array('supportDesk/statistics'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->render('statistics');
	}	
	public function actionDelete($id){
		$this->loadModel($id)->delete();
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	public function actionIndex(){
		if (Yii::app()->user->isAdmin && !GroupPermissions::checkPermissions('supportdesk-list')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['SupportDesk']) ? $_GET['SupportDesk'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/supportDesk/index' => array(
						'label'=>Yii::t('translations', 'All Incidents'),
						'url' => array('supportDesk/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new SupportDesk('search');
		$model->unsetAttributes();  
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
			'provider' => $model->search()
		));
	}
	public function actioncheckReassign()
	{
		
		if(isset($_POST['sr']) && isset($_POST['reason']))
		{
			$sr=$_POST['sr'];
			$reason=$_POST['reason'];
			if($reason == 407)
			{
				$check = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk sp where sp.id=".$sr." and assigned_to = (select ca from customers where id= sp.id_customer)")->queryScalar();
				if($check == 0)
				{
					$ca = Yii::app()->db->createCommand("select ca from customers where id= (SELECT id_customer FROM support_desk sp where sp.id=".$sr." )")->queryScalar();
					if(isset($ca))
					{
						echo json_encode(array('status'=>'success','assign'=>'1', 'message'=> 'Kindly confirm that the incident should be reassigned to the CA.','user'=> $ca, 'uname'=>Users::getUsername($ca)));
						exit;
					}
				}
			}else if($reason == 413)
			{
				$check = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk sp where sp.id=".$sr." and assigned_to = (select ca from customers where id= sp.id_customer)")->queryScalar();
				if($check == 0)
				{
					$ca = Yii::app()->db->createCommand("select ca from customers where id= (SELECT id_customer FROM support_desk sp where sp.id=".$sr." )")->queryScalar();
					if(isset($ca))
					{
						$model = SupportDesk::model()->findByPk($sr);
						$old = $model->assigned_to;
						 $model->assigned_to= $ca;
						 $model->save();
						SupportDesk::sendEmailAssigned($model, $old);
						if($ca == Yii::app()->user->id)
						{
							echo json_encode(array('status'=>'done','assign'=>'1', 'message'=> 'Kindly note that the incident has been reassigned to the CA.','user'=> $ca,'hideout'=>'false', 'uname'=>Users::getUsername($ca))); 
						}else{
							echo json_encode(array('status'=>'done','assign'=>'1', 'message'=> 'Kindly note that the incident has been reassigned to the CA.','user'=> $ca ,'hideout'=>'true', 'uname'=>Users::getUsername($ca))); 							
						}
						exit;
					}
				}
			}else{
				$check = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk sp where sp.id=".$sr." and assigned_to = (select cs_representative from customers where id= sp.id_customer)")->queryScalar();
				if($check == 0)
				{
					$cs = Yii::app()->db->createCommand("select cs_representative from customers where id= (SELECT id_customer FROM support_desk sp where sp.id=".$sr." )")->queryScalar();
					if(isset($cs))
					{
						echo json_encode(array('status'=>'success','assign'=>'1', 'message'=> 'Kindly confirm that the incident should be reassigned to the CS Representative.','user'=> $cs, 'uname'=>Users::getUsername($cs)));
						exit;
					}
				}
			}
			echo json_encode(array('status'=>'failure'));
			exit;
		}
		echo json_encode(array('status'=>'failure'));
		exit;
	}
	
	public function actionAssigned(){
		$id = (int)$_POST['id_support_desk'];		
		$model = SupportDesk::model()->findByPk($id);
		$oldAssigned=0;
		switch ($_POST['type'])	{
			case '1':
				$model->due_date = DateTime::createFromFormat('d/m/Y', $_POST['value'])->format('Y-m-d');
				break;
			case '2':
				$oldAssigned= $model->assigned_to;
				$model->assigned_to = $_POST['value'];
				break;
			case '3':
				$model->reason = $_POST['value'];
				break;
			case '4':
				$model->responsibility = $_POST['value'];
				break;
			case '5':
				$model->repeat = $_POST['value'];
				break;
			case '6':
				$model->exclude = $_POST['value'];
				break;	
			case '7':
				$model->deployment = $_POST['value'];
				break;		
			case '8':
				$model->category= $_POST['value'];
				break;		
		}
	    if ($model->save()) {
	    	if ($_POST['type'] == 2){
	    		SupportDesk::sendEmailAssigned($model, $oldAssigned);
	    	}
			echo json_encode(array_merge(array('status'=>'success','sr'=>$model->id)));
			exit;
	    }
	    echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		exit;
	}
	public function actionDeleteUpload(){	
		if (isset($_GET['model_id'], $_GET['file'])){
			$id_support_desk = (int)$_GET['model_id'];
			$filename = $_GET['file'];			
			if (isset($_GET['comm'])){
				$id_att = Yii::app()->db->createCommand("SELECT id FROM support_desk_comm_files WHERE id_support_desk = '$id_support_desk' AND filename = '$filename' ORDER BY id DESC")->queryScalar();
				if ($id_att != null){
					$id_customer = Yii::app()->db->createCommand("SELECT id_customer from support_desk where id = '$id_support_desk'")->queryScalar();
					$filepath = SupportDesk::getDirPathComm($id_customer, $id_support_desk,true).$_GET['file'];
					$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
					if ($success){
						Yii::app()->db->createCommand("Delete from support_desk_comm_files where id = $id_att")->execute();
						$ids = explode(',', Yii::app()->session['id']);
						Yii::app()->session['id'] = "";
						foreach ($ids as $id){
							if ($id != $id_att ){
								Yii::app()->session['id'] .= $id.",";
							}		}		}		}		}
			else{	
				$id_att = Yii::app()->db->createCommand("SELECT id FROM support_desk_files WHERE ISNULL(id_support_desk) AND filename = '$filename' ORDER BY id DESC")->queryScalar();
				if ($id_att != null)
				{
					$filepath = SupportDesk::getDirPath(Yii::app()->user->customer_id, $id_support_desk).$_GET['file'];
					$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
					if ($success)
					{
						Yii::app()->db->createCommand("Delete from support_desk_files where id = $id_att")->execute();
						$ids = explode(',', Yii::app()->session['id_files']);
						Yii::app()->session['id_files'] = "";
						foreach ($ids as $id)
						{
							if ($id != $id_att)
							{
								Yii::app()->session['id_files'] .= $id.",";
							}
						}	}	}	}
		}else if($_GET['id'] != null){
				
				$id_sr = $_GET['id'];
				$model = $this->loadModel($id_sr);
				$filepath = SupportDesk::getDirPath($_GET['customer'], $id_sr).$_GET['filename'];
				$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
				if ($success)
				{
					$id_att_sr = Yii::app()->db->createCommand("SELECT id FROM support_desk_files WHERE id_support_desk = {$id_sr} AND filename ='{$_GET['filename']}' ")->queryScalar();
					if($id_att_sr != null)
					{
						$nr = Yii::app()->db->createCommand("Delete from support_desk_files where id = {$id_att_sr}")->execute();
						echo json_encode(array ('status' =>'success','html' => $this->renderPartial('_attachements', array('model' => $model), true, false)));
					}
				}	}	}	
	public function actionAdmin(){
		$model=new SupportDesk('search');
		$model->unsetAttributes();  
		if (isset($_GET['SupportDesk']))
			$model->attributes=$_GET['SupportDesk'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function actionUpdateHeader($id){
		$id = (int) $id;		$model = $this->loadModel($id);
		$extra = array();
		if (isset($_POST['SupportDesk']))
		{
			$model->attributes = $_POST['SupportDesk'];
			if (isset($_POST['repeat']) && $_POST['repeat'] != "")
				$model->repeat = $_POST['repeat'];
			else 
				$model->repeat = "No";
			if ($model->save())
			{
				echo json_encode(array_merge(array(
						'status' => 'saved',
						'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
				), $extra));
				Yii::app()->end();
			}
		}		
		Yii::app()->clientScript->scriptMap=array(
		'jquery.js'=>false,
		'jquery.min.js' => false,
		'jquery-ui.min.js' => false,
		);
		echo json_encode(array_merge(array(
				'status'=>'success',
				'html'=>$this->renderPartial('_edit_header_content', array('model'=> $model), true, true)
		), $extra));
		Yii::app()->end();
	}
	public function actionescalateSR(){
		   $sr=$_POST['sr'];	
		   $reason=$_POST['reason'];	  
		   $customerid = SupportDesk::getCustomerBySr($sr);
		   $customer = Customers::getNameById($customerid);
		   $model = SupportDesk::model()->findByPk($sr);
		   $model->severity='High';
           $model->escalate='Yes';
           $model->escalation_reason= $reason;
           $model->escalate_date= date("Y-m-d H:i:s");
           $model->save();
		   $url=Yii::app()->createAbsoluteUrl('SupportDesk/update', array('id'=>$sr));
		   $srs ="<a href=".$url.">".$sr."</a>";
		  	 $list="- CS Representative: <b>".Users::getNameById($model->assigned_to)."</b> <br /> ";
		   $list.="- CA: <b>".Users::getNameById(Customers::getCAbyCustomer($customerid))."</b> <br /> ";
		   $list.="- Account Manager: <b>".Users::getNameById(Customers::getAccountManagerbyCustomer($customerid))."</b> <br /> ";
		   $list.="- Escalation Reason: <b>".$reason."</b> <br /> ";
		   	$notif = EmailNotifications::getNotificationByUniqueName('sr_escalate');  	   				
		    			$to_replace = array(
							'{sr}',
							'{customer}',
							'{list}');
						$replace = array(
							$srs,
							$customer,
							$list	
						);
						$cs_rep=UserPersonalDetails::getEmailById($model->assigned_to);
						$CA=UserPersonalDetails::getEmailById(Customers::getCAbyCustomer($customerid));
						$AccountManager=UserPersonalDetails::getEmailById(Customers::getAccountManagerbyCustomer($customerid));
						$body = str_replace($to_replace, $replace, $notif['message']);
						$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);						
						Yii::app()->mailer->ClearAddresses();
						//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
						Yii::app()->mailer->AddAddress($cs_rep);
						//Yii::app()->mailer->AddCCs($CA);
						Yii::app()->mailer->AddAddress($CA);
						//Yii::app()->mailer->AddCCs($AccountManager);	
						Yii::app()->mailer->AddAddress($AccountManager);
							foreach($emails as $email) 	{
								if (!empty($email))
								//	Yii::app()->mailer->AddCCs($emails);
									Yii::app()->mailer->AddAddress($email);
							}
						$subject=$customer." - SR#".$sr." Escalation";		
						Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send();
					echo CJSON::encode(array(
						'status'=>'success'	));
	}
	public function actionRateTicket($id){
	if(isset($_POST['rate'])){
			 $rate=$_POST['rate'];
			 $id=$_GET['id'] ;
			 $customer=SupportDesk::getCustomerIdBySr($id);
			 $rate_reason=" ";
				if($rate<='3'){
				if( isset($_POST['rate_reason']) && !empty($_POST['rate_reason']) ){					
					 $rate_reason= $_POST['rate_reason']; 
					}else{
	   					 echo json_encode(array('status'=>'failure', 'message'=>'Review is required for 3 stars and below'));
						exit;
						}
				}
				$last_sr_rate = Yii::app()->db->createCommand("SELECT rate FROM `support_desk` WHERE STATUS = '5' and id_customer='".$customer."' order by rate_date desc limit 1 ")->queryScalar();
				if($rate<'3' && ($last_sr_rate=='0' || $last_sr_rate=='1' || $last_sr_rate=='2' ) )  {
						$sr_escalated=Yii::app()->db->createCommand("UPDATE customers SET sr_escalated=sr_escalated+1  where id='".$customer."' ")->execute();
		    		}else { 
						$sr_escalated=Yii::app()->db->createCommand("UPDATE customers SET sr_escalated=0  where id='".$customer."' ")->execute();
					}
					$sr_escalated_count = Yii::app()->db->createCommand("SELECT sr_escalated FROM `customers` WHERE id='".$customer."' ")->queryScalar();				
					if($sr_escalated_count>='3'){
						$sr_escalated=Yii::app()->db->createCommand("UPDATE customers SET sr_escalated=0  where id='".$customer."' ")->execute();
						$notif = EmailNotifications::getNotificationByUniqueName('unsatisfied_rates');
						if ($notif != NULL){
							$subject = $notif['name'];				
							$to_replace = array(		'{customer}', );
							$replace = array(	Customers::getNameById($customer),	);
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
			$rateme = Yii::app()->db->createCommand("UPDATE support_desk SET rate='".$rate."' , rate_comment='".$rate_reason."' , rate_date=NOW()  where id='".$id."' ")->execute();
				$closed = Yii::app()->db->createCommand("UPDATE support_desk SET unrated_followup='3' where id='".$id."' ")->execute();
		    	echo json_encode(array_merge(array(
					'status'=>'success'									
				)));
			}else {
				$model_support = SupportDeskController::loadModel($id);			
				echo json_encode(array_merge(array(
					'status'=>'saved',
					'div' => $this->renderPartial('_rate_ticket', array('model' => $model_support), true, true)					
				)));	}	}
	public function actionGetRate($id, $changeStatus = null){
			$model_support = SupportDeskController::loadModel($id);			
				echo json_encode(array_merge(array(
					'status'=>'saved',
					'div' => $this->renderPartial('_rate_ticket', array('model' => $model_support), true, true)					
				)));
	}
	public function actionPostComment($id, $changeStatus = null){
		$id = (int) $id;	$model = new SupportDeskComments();	$model_support = SupportDeskController::loadModel($id);
		$status = ""; 		$webroot = Yii::getPathOfAlias('webroot');
		$path =  $webroot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.php';

		if(Yii::app()->user->isAdmin)
		{
			$tdid= Yii::app()->db->createCommand("SELECT id FROM `default_tasks` where id_parent=27 and name= (select name from customers where id=".$model_support->id_customer.") and id_maintenance is NULL")->queryScalar();
			if(!empty($tdid))
			{
				$checkupd= Yii::app()->db->createCommand("SELECT id,srs FROM `user_time` where id_task=".$tdid." and date='".date('Y-m-d')."'and id_user=".Yii::app()->user->id)->queryRow();
				if(!empty($checkupd['id']))
				{	
					if(strpos($checkupd['srs'], $model_support->sd_no)  ===  false){
						if(trim($checkupd['srs']) == ''){
							$srstr= $model_support->sd_no;	
						}else{
							$srstr= $checkupd['srs'].','.$model_support->sd_no;							
						}
						Yii::app()->db->createCommand("UPDATE user_time SET srs ='".$srstr."' WHERE id=".$checkupd['id'])->execute();
					}
				}else{
					$srstr= $model_support->sd_no;	
					$idtimesh=Yii::app()->db->createCommand("SELECT id FROM `timesheets` where week_start<='".date('Y-m-d')."' and week_end>='".date('Y-m-d')."' and id_user=".Yii::app()->user->id)->queryScalar();
					if(!empty($idtimesh)){
						Yii::app()->db->createCommand("insert into user_time (id_user,id_task,id_timesheet,amount,comment,date,`default`,status,srs) values (".Yii::app()->user->id.",".$tdid.",".$idtimesh.",0,'','".date('Y-m-d')."',1,0,'".$srstr."') ")->execute();
					}
				}				
			}
		}

		if (isset($_POST['rejected_message']) && $_POST['rejected_message'] != null && !(isset($_POST['status']))){
			if ($model_support->status == SupportDesk::STATUS_PENDING_INFO && $changeStatus == null && Yii::app()->user->isAdmin){
				echo json_encode(array_merge(array(
					'status'=>'pending',
				)));
				Yii::app()->end();	
			}else if ($model_support->status == SupportDesk::STATUS_PENDING_INFO && $changeStatus == null){
				$changeStatus ="yes";
			}
			if (!empty(Yii::app()->session['id'])){
				$ids = explode(',', Yii::app()->session['id']);
				$count = count($ids) - 1;
				unset(Yii::app()->session['id']);
			}else{
				$ids = array();
				$count = 0;
			}			
			$model->id_support_desk = $id;
			$model->id_user = Yii::app()->user->id;
			$model->is_admin = (int)Yii::app()->user->isAdmin;
			$model->date =  date('Y-m-d H:i:s ', strtotime('now'));
			$model->comment = $_POST['rejected_message'];
			$model->status = $model_support->status;
			$model->sender = Yii::app()->user->isAdmin ? "SNS" : Customers::getNameById(Yii::app()->user->customer_id);	

			
			
			if ($changeStatus == "yes" )//&& Yii::app()->user->isAdmin)
			{
				$model->status = SupportDesk::STATUS_IN_PROGRESS;
			}
			$model->files = $count; 						
			if ($model->save()){

				if (($model_support->status == SupportDesk::STATUS_NEW && Yii::app()->user->isAdmin ) || ($model_support->status == SupportDesk::STATUS_REOPENED && Yii::app()->user->isAdmin ) || ($changeStatus == "yes" ))//&& Yii::app()->user->isAdmin) )
				{
					$model_support->status = SupportDesk::STATUS_IN_PROGRESS; 
					$model_support->num_of_followups = 0;
					$model_support->num_of_automatic_followups = 0;
					$model_support->last_followup_date = null;
					$newid=Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where id=".$id."")->queryScalar();
					$count2=Yii::app()->db->createCommand("SELECT count(*) FROM `users` where id=".$model->id_user."")->queryScalar();					
					if ( ($model->id_user != $newid) && ($count2>0) && ($model->sender == 'SNS')){
						$model_support->assigned_to= $model->id_user; 
					}
					$model_support->save(); 
				}
				$newid=Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where id=".$id."")->queryScalar();
				$count2=Yii::app()->db->createCommand("SELECT count(*) FROM `users` where id=".$model->id_user."")->queryScalar();				
				if ( ($model->id_user != $newid) && ($count2>0) && ($model->sender == 'SNS')){
					$model_support->assigned_to= $model->id_user; 
				}
				$model_support->users_checked='0,';
				$model_support->save(); 
				self::updateCommId($ids, $model->id,  'support_desk_comm_files', 'id_comm');
				if (!Yii::app()->user->isAdmin && $model->status!=5){
					SupportDesk::validateLastUpdate($model_support->sd_no,$model_support->assigned_to,$model_support->id_customer);
				}
				$f = fopen($path, "a+");
				if (false === $f) {
				    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, 'SR#'.$model_support->id.' Comment:'.$model->comment.','.$model->sender.','.$model->id_user.' -- ' );
				fclose($f);
//houda
				//SupportDesk::sendEmail($model_support->id, $model->id);
				
				$f = fopen($path, "a+"); 
				$bytes = fwrite($f, $model_support->id.' SENT EMAIL! --- ' );
				fclose($f);				
				echo json_encode(array_merge(array(
					'status'=>'saved',
					'html' => $this->renderPartial('update', array('model' => $model_support), true, true),
					'current_status'=>SupportDesk::getStatusLabel($model->status),
				)));
				Yii::app()->end();	
			}
		}elseif (isset($_POST['status']) && $_POST['status'] != "pending_info" ){
			if (!empty(Yii::app()->session['id'])){
				$ids = explode(',', Yii::app()->session['id']);
				$count = count($ids) - 1;
				unset(Yii::app()->session['id']);
			}	else	{
				$ids = array();
				$count = 0;
			}
			$message = "";	
			$model->id_support_desk = (int) $id;
			$model->date =  date('Y-m-d H:i:s ', strtotime('now'));
			$model->id_user = Yii::app( )->user->id;
			$model->is_admin = (int)Yii::app()->user->isAdmin;
			$model->sender = Yii::app()->user->isAdmin ? "SNS" : Customers::getNameById(Yii::app()->user->customer_id);
			$model->status = SupportDesk::getStatusIdCoded($_POST['status']);	

			if ($_POST['status'] == "close")
			{
				if ($model_support->reason == null)
				{
					$message .= "Reason can't be empty ";
				}
				if ($model_support->deployment == null || $model_support->deployment=='' )
				{
					if($model_support->reason == 407 || $model_support->reason == 413 )
					{
						$model_support->deployment= 'No';
					}else{						
						$message .= "Please Specify if a deployment was required";
					}
				}
				if ((!isset($_POST['root_cause']) || $_POST['root_cause'] == null || empty($_POST['root_cause'])) && $model_support->reason != 407 && $model_support->reason != 413){
					$message .= "Root Cause is mandatory";		
				}				
				if ($message != "")
				{
					if($message ==  "Reason can't be empty ")
					{
						echo json_encode(array_merge(array(
							'status'=>'not_complet',
							'message' => "The field Reason should not be blank"
						)));
						exit;	
					}else{
						echo json_encode(array_merge(array(
							'status'=>'not_complet',
							'message' => "The fields Reason, Deployment and Root Cause should not be blank"
						)));
						exit;	
					}
					
				}
				if ($model_support->deployment == 'Yes')
				{
					$checkDeployment = Deployments::validateSRDeployment($model->id_support_desk, $model_support->id_customer);
					if(!$checkDeployment)
					{
							echo json_encode(array_merge(array(
						'status'=>'not_complet',
						'message' => "Please create a deployment record for this SR# to close it"
						)));
						exit;
					}
				}

				if(isset($_POST['solmsg'])){
				$model->avoid = $_POST['solmsg'];	
				$newid=Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where id=".$id."")->queryScalar();
				$count2=Yii::app()->db->createCommand("SELECT count(*) FROM `users` where id=".$model->id_user."")->queryScalar();					
					if ( ($model->id_user != $newid) && ($count2>0) && ($model->sender == 'SNS'))	{
						$model_support->assigned_to= $model->id_user; 
					}	
					$model_support->users_checked='0,';
				$model_support->save();				
				}else{
						echo json_encode(array_merge(array(
							'status'=>'not_complet',
							'message' => "Please Specify how to avoid this issue"
						)));
						exit;	
				}			
			}
			elseif ($_POST['status'] ==  "reopened")
			{		
				if(isset($_POST['reopenmsg'])){
				$model->comment = $_POST['reopenmsg'];		
				$model_support->reopen = $model_support->reopen + 1;
				$newid=Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where id=".$id."")->queryScalar();
					$count2=Yii::app()->db->createCommand("SELECT count(*) FROM `users` where id=".$model->id_user."")->queryScalar();					
					if ( ($model->id_user != $newid) && ($count2>0) && ($model->sender == 'SNS')){
						$model_support->assigned_to= $model->id_user; 
					}
					$model_support->users_checked='0,';
					$model_support->save(); 
				$model_support->save();
				}else{
					echo json_encode(array_merge(array(
						'status'=>'not_complet',
						'message' => "Please Specify a reason to reopen issue"
					)));
					exit;	
				}
			}
			$model->root_cause = "";
			$model->recurring_avoidance = "";
			if (isset($_POST['rejected_message']) && $_POST['rejected_message'] != null)
				$model->comment = $_POST['rejected_message'];

			if (isset($_POST['root_cause']) && $_POST['root_cause'] != null)
				$model->root_cause = $_POST['root_cause'];
			if ($model->save())	{				
				self::updateCommId($ids, $model->id,  'support_desk_comm_files', 'id_comm');
				if (!Yii::app()->user->isAdmin  && $model->status!=5){
					SupportDesk::validateLastUpdate($model_support->sd_no,$model_support->assigned_to,$model_support->id_customer);
				}
				$f = fopen($path, "a+");
				if (false === $f) {
				    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, 'SR#'.$model_support->id.' Comment:'.$model->comment.','.$model->sender.','.$model->id_user.' -- ' );
				fclose($f);
    			//houda
    		//SupportDesk::sendEmailChangeStatus($model->id_support_desk, $model->id, Yii::app( )->user->getState('name'), SupportDesk::getStatusLabelCoded($model->status));
				$f = fopen($path, "a+");
				if (false === $f) {
				    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, $model_support->id.' SENT EMAIL! --- ' );
				fclose($f);
				$model_support->status = $model->status;
				$model_support->num_of_followups = 0;
				$model_support->num_of_automatic_followups = 0;
				$model_support->last_followup_date = null;
				$newid=Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where id=".$id."")->queryScalar();
				$count2=Yii::app()->db->createCommand("SELECT count(*) FROM `users` where id=".$model->id_user."")->queryScalar();					
				if ( ($model->id_user != $newid) && ($count2>0) && ($model->sender == 'SNS')){
					$model_support->assigned_to= $model->id_user; 
				}
				$model_support->users_checked='0,';
				$model_support->save();				
				echo json_encode(array_merge(array(
					'status'=>'success',
					'html' => $this->renderPartial('update', array('model' => $model_support), true, false),
					'current_status'=>SupportDesk::getStatusLabel($model->status),
				)));
				Yii::app()->end();	
			}
		}else if(isset($_POST['status']) && $_POST['status'] == "pending_info" && $_POST['rejected_message'] != null){			
			if (!empty(Yii::app()->session['id'])){
				$ids = explode(',', Yii::app()->session['id']);
				$count = count($ids) - 1;
				unset(Yii::app()->session['id']);
			}else{	$ids = array();
				$count = 0;
			}
			$message = "";				
			$model->id_support_desk = (int) $id;
			$model->date =  date('Y-m-d H:i:s ', strtotime('now'));
			$model->id_user = Yii::app( )->user->id;
			$model->is_admin = (int)Yii::app()->user->isAdmin;
			$model->sender = Yii::app()->user->isAdmin ? "SNS" : Customers::getNameById(Yii::app()->user->customer_id);
			$model->status = SupportDesk::getStatusIdCoded($_POST['status']);	
			$model->comment = "";
			$model->comment = $_POST['rejected_message'];			
			if ($model->save()){					
				self::updateCommId($ids, $model->id,  'support_desk_comm_files', 'id_comm');
				if (!Yii::app()->user->isAdmin  && $model->status!=5){
					SupportDesk::validateLastUpdate($model_support->sd_no,$model_support->assigned_to,$model_support->id_customer);
				}				
				$f = fopen($path, "a+");
				if (false === $f) {
				    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, 'SR#'.$model_support->id.' Comment:'.$model->comment.','.$model->sender.','.$model->id_user.' -- ' );
				fclose($f);
				//houda
				//SupportDesk::sendEmailChangeStatus($model->id_support_desk, $model->id, Yii::app( )->user->getState('name'), SupportDesk::getStatusLabelCoded($model->status));
				$f = fopen($path, "a+");
				if (false === $f) {
				    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, $model_support->id.' SENT EMAIL! --- ' );
				fclose($f);
				$model_support->status = $model->status;
				$model_support->num_of_followups = 0;
				$model_support->num_of_automatic_followups = 0;
				$model_support->last_followup_date = null;				
				$newid=Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where id=".$id."")->queryScalar();
					$count2=Yii::app()->db->createCommand("SELECT count(*) FROM `users` where id=".$model->id_user."")->queryScalar();					
					if ( ($model->id_user != $newid) && ($count2>0) && ($model->sender == 'SNS'))
					{
						$model_support->assigned_to= $model->id_user; 
					}
				$model_support->users_checked='0,';
				$model_support->save();				
				echo json_encode(array_merge(array(
					'status'=>'success',
					'html' => $this->renderPartial('update', array('model' => $model_support), true, false),
					'current_status'=>SupportDesk::getStatusLabel($model->status),
				)));
				Yii::app()->end();
			}		}
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
	public function insertFirstComment($comment, $id, $table,$id_user,$customer_name){
		$comment = str_replace("'", "\'",$comment);
		$date=date('Y-m-d H:i:s ', strtotime('now'));			
		Yii::app()->db->createCommand("Insert into `$table` (id_support_desk,comment,date,id_user,is_admin,status,files,sender) values ('".$id."' ,'".$comment."','".$date."','".$id_user."' ,'0' ,'0','0','".$customer_name."')")->execute();
	}	
	public function actionSendEmailTwiceClosePending(){
		$types = array("cron_close", "cron_pending");
		foreach ($types as $type){
			$notif[] = EmailNotifications::getNotificationByUniqueName('support_desk_'.$type);
		}		
		foreach ($notif as $key => $noti){
			if ($noti != null){	
				if ($key == 0){	}
				else{ 	$status = SupportDesk::STATUS_PENDING_INFO;		}
				$res = Yii::app()->db->createCommand("SELECT id, sd_no, id_customer, customer_contact_id, assigned_to, num_of_automatic_followups FROM `support_desk` WHERE STATUS = '$status'")->queryAll();
			$customer = array();		$srs_ids = array();
				foreach ($res as $rez){
					$srs_ids[] = $rez['id'];
					if (isset($customer[$rez['id_customer']]['user'][$rez['customer_contact_id']]['ct']))
						$customer[$rez['id_customer']]['user'][$rez['customer_contact_id']]['ct'] +=1;
					else 
						$customer[$rez['id_customer']]['user'][$rez['customer_contact_id']]['ct'] = 1;
						
					$customer[$rez['id_customer']]['user'][$rez['customer_contact_id']]['customer'] = $rez['id_customer'];
					$customer[$rez['id_customer']]['user'][$rez['customer_contact_id']]['customer_contact'] = $rez['customer_contact_id'];
					$customer[$rez['id_customer']]['user'][$rez['customer_contact_id']]['items'][$rez['id']] = $rez;
				}				
				foreach ($customer as $customers){
					$custs = $customers['user'];
					foreach ($custs as $cust){
						$cust_id = $cust['customer_contact'];
						$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = '{$cust_id}'")->queryScalar();
    					$list = ""; 
						$assigned = array();
						$to_replace = array(
							'{customer_name}',
							'{ct}',
						);
						$replace = array(
							Customers::getNameById($cust['customer']),
							$cust['ct'],
						);
						$subject = str_replace($to_replace, $replace, $noti['name']);
						$items = $cust['items'];
						foreach ($items as $item){
							$assigned[$item['assigned_to']] = $item['assigned_to'];
							$list .= '<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$item['sd_no'])).'">'.$item['sd_no'].'</a>, ';
						}
						$to_replace = array(
							'{customer_name}',
							'{ct}',
							'{list}', 
						);
						$replace = array(
							substr($name_customer, 0, strpos($name_customer, ' ')),
							$cust['ct'],
							substr($list, 0, -1),
						);
						$body = str_replace($to_replace, $replace, $noti['message']);						
						Yii::app()->mailer->ClearAddresses();
											
						$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '{$cust['customer']}' AND access = 'Yes'")->queryColumn();
						foreach ($emails_customer as $email_c){
							if (filter_var($email_c, FILTER_VALIDATE_EMAIL)){
								Yii::app()->mailer->AddAddress($email_c);
							}
						}
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");				    	
				    	Yii::app()->mailer->Send(true); 
					}	}
				if ($status==3) {
					$automatic_follow = Yii::app()->db->createCommand("UPDATE support_desk SET num_of_automatic_followups= num_of_automatic_followups + 1, last_followup_date=NOW()+ INTERVAL 10 HOUR where status=3")->execute();
		    	}	}	}  }	
	public function actionfollowupReminderClose(){
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('reminder_followUpSrsClose');
		if ($notif != NULL){
			$to_replace = array(
				'{group_description}',
				'{body}'
			);			
			$users=Yii::app()->db->createCommand("SELECT DISTINCT(s.assigned_to) as user FROM support_desk s where s.status in (3,2) and 0= (SELECT count(1)  FROM support_desk_comments sd where sd.date>= (NOW() - INTERVAL 2 DAY) and sd.id_support_desk=s.id ) and  s.assigned_to in (select id_user from user_groups where id_group= 9)")->queryAll();
			if(!empty($users)){
				foreach ($users as $user) {
					if(!EmailNotifications::isNotificationPAlertSent($user['user'], 'support', $notif['id']))
					{
						$msg = '';		$SRs= SupportDesk::getSRsPerUserClose($user['user']);
						$msg.='Dear '.Users::getfirstNameById($user['user']).',<br/><br/>This is a kind reminder to follow up on the below assigned SRs in status "Solution Proposed" or "Awaiting Customer":<br/> <br/>';
						foreach ($SRs as $SR) {
							$msg.='SR#<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$SR['sd_no'])).'">'.$SR['sd_no'].'</a> - Customer: '.Customers::getNameById($SR['id_customer']).' - Description: '.$SR['short_description'].'  - Logged Date: '.date("d/m/Y",strtotime($SR['date'])).' <br/>';
						}
						$msg.='<br/>Best Regards,<br/>SNSit';

						$subject = $notif['name'];
						$replace = array(	EmailNotificationsGroups::getGroupDescription($notif['id']),	$msg	);
	   				$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						$assigneEmail= UserPersonalDetails::getEmailById($user['user']);
						if (filter_var($assigneEmail, FILTER_VALIDATE_EMAIL))
						{
							Yii::app()->mailer->AddAddress($assigneEmail);
						} 
						$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
						foreach($emails as $email){		//Yii::app()->mailer->AddCcs($email);		
							Yii::app()->mailer->AddAddress($email);
						}
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							echo "Sent";
							EmailNotifications::saveSentNotification($user['user'], 'support', $notif['id']);
						}

					}
				}	
			}	}	}
	public function  getsev($id){	if ($id== 1){	return "Low";	}else if  ($id== 2){	return "Medium"; }else{		return "High";	}	}
	public function actionmonthlyAccounts(){
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('monthly_support');
		if ($notif != NULL)	{
			$to_replace = array(
				'{group_description}',
				'{body}'
			);	
			$accounts= SupportDesk::getallCAAccounts();
			if(!empty($accounts)){
				if(!EmailNotifications::isNotificationPAlertSent(1, 'supportCA', $notif['id']))
				{
					$msg = '';
					$month=date('M');
					$msg.='Dear All,<br/><br/>Please find below customers support accounts summary:<br/> <br/>';	
					$msg.="<table border='1'  style='font-family:Calibri;' ><tr><th colspan='3' border-collapse:collapse; style='border: 0px solid #FFFFFF;'></th><th colspan='3'>Inquiry (".$month.")</th><th colspan='3'>Change Request (".$month.")</th><th colspan='3'>Issues (".$month.")</th><th colspan='3'>RSRs (".$month.")</th> <th border-collapse:collapse; style='border: 0px solid #FFFFFF;' colspan='2'></th></tr>   <tr><th>Customer</th><th>Account Manager</th><th>CA</th><th> Closed  </th><th>Pending(<30)</th><th>Pending(>30)</th><th>Closed</th><th>Pending(<30)</th><th>Pending(>30)</th><th>Closed</th><th>Pending(<30)</th><th>Pending(>30)</th><th>Closed</th><th>Pending(<30)</th><th>Pending(>30)</th><th>Customer Satisfaction Avg.</th><th>Escalations#</th></tr>";
					foreach ($accounts as $model) {
						$msg.="<tr><td>".$model['name']."</td><td>".Users::getNameById($model['account_manager'])."</td><td>".Users::getNameById($model['ca'])."</td> <td style='text-align:center;'>".SupportDesk::getClosedPerReason($model['id'],'Inquiry')."</td><td style='text-align:center;'>".SupportDesk::getOpenPerReasonLess($model['id'],'Inquiry')."</td>			<td style='text-align:center;'>".SupportDesk::getOpenPerReasonMore($model['id'],'Inquiry')."</td> 			<td style='text-align:center;'>".SupportDesk::getClosedPerReason($model['id'],'CR')."</td>  			<td style='text-align:center;'>".SupportDesk::getOpenPerReasonLess($model['id'],'CR')." </td>			<td style='text-align:center;'>".SupportDesk::getOpenPerReasonMore($model['id'],'CR')." </td>			<td style='text-align:center;'>".SupportDesk::getClosedPerReason($model['id'],'Issue')."</td> 			<td style='text-align:center;'>".SupportDesk::getOpenPerReasonLess($model['id'],'Issue')." </td> 		<td style='text-align:center;'>".SupportDesk::getOpenPerReasonMore($model['id'],'Issue')." </td> 		<td style='text-align:center;'>".SupportRequest::getClosedLastm($model['id'],'Issue')."</td> 		<td style='text-align:center;'>".SupportRequest::getOpenLess($model['id'],'Issue')." </td> 		<td style='text-align:center;'>".SupportRequest::getOpenMore($model['id'],'Issue')." </td> 		<td style='text-align:center'>  ".SupportDesk::getAvgSat($model['id'])."</td> 		<td style='text-align:center;'>".SupportDesk::getEscalations($model['id'])." </td> </tr>";
					}
					$msg.='</table><br/>Best Regards,<br/>SNSit';
						$subject = $notif['name'];
						$replace = array(
								EmailNotificationsGroups::getGroupDescription($notif['id']),
								$msg
				
						);
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
						foreach($emails as $email)
						{
							//Yii::app()->mailer->AddCcs($email);
							Yii::app()->mailer->AddAddress($email);
						} 
					//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							echo "Sent";
							EmailNotifications::saveSentNotification(1, 'support', $notif['id']);
						}
				}
			}		
			/*$users=Yii::app()->db->createCommand("select distinct (id) as user from users where active=1 and ( id in (select cs_representative from customers where status='Active') or id in ( select ca from customers where status='Active'))")->queryAll();			
			if(!empty($users)){
				foreach ($users as $user) {
					if(!EmailNotifications::isNotificationPAlertSent($user['user'], 'support', $notif['id']))
					{
						$msg = '';
						$models= SupportDesk::getAccountsPerUser($user['user']);
						$month=date('F');
						$msg.='Dear '.Users::getfirstNameById($user['user']).',<br/><br/>Please find below customers support accounts summary:<br/> <br/>';					 
						$msg.="<table border='1'  style='font-family:Calibri;' ><tr><th>Customer</th><th># of closed Inquiries (".$month.") </th><th># of Pending Inquiries</th><th># of closed CRs (".$month.")</th><th># of Pending CRs</th><th># of closed Issues (".$month.")</th><th># of Pending Issues</th><th>Customer Satisfaction Avg.</th></tr>";
						foreach ($models as $model) {
							$msg.="<tr><td>".$model['name']."</td><td>".SupportDesk::getClosedPerReason($model['id'],'Inquiry')."</td><td>".SupportDesk::getPendingPerReason($model['id'],'Inquiry')."</td> <td>".SupportDesk::getClosedPerReason($model['id'],'CR')."</td>  <td>".SupportDesk::getPendingPerReason($model['id'],'CR')." </td> <td>".SupportDesk::getClosedPerReason($model['id'],'Issue')."</td>  <td>".SupportDesk::getPendingPerReason($model['id'],'Issue')." </td> <td style='text-align:center'>  ".SupportDesk::getAvgSat($model['id'])."</td> </tr>";
						}
						$msg.='<br/>Best Regards,<br/>SNSit';
						$subject = $notif['name'];
						$replace = array(
								EmailNotificationsGroups::getGroupDescription($notif['id']),
								$msg
				
						);
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						$assigneEmail= UserPersonalDetails::getEmailById($user['user']);
						if (filter_var($assigneEmail, FILTER_VALIDATE_EMAIL))
						{
							Yii::app()->mailer->AddAddress($assigneEmail);
						} 
						$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
						foreach($emails as $email)
						{
							//Yii::app()->mailer->AddCcs($email);
							Yii::app()->mailer->AddAddress($email);
						} 
						$email_lm = EmailNotificationsGroups::getLMNotificationUsers($user['user']);
						if (filter_var($email_lm, FILTER_VALIDATE_EMAIL)) {
						//	Yii::app()->mailer->AddCcs($email_lm);
							Yii::app()->mailer->AddAddress($email_lm);
						}
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							echo "Sent";
							EmailNotifications::saveSentNotification($user['user'], 'support', $notif['id']);
						}
					}
				}
			}*/
		} 
	}
	public function actionfollowupReminder(){
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('reminder_followUpSrs');
		if ($notif != NULL)	{
			$to_replace = array(
				'{group_description}',
				'{body}'
			);			
			$users=Yii::app()->db->createCommand("SELECT DISTINCT(assigned_to) as user FROM `support_desk` where status not in (3,5)")->queryAll();			
			if(!empty($users)){
				foreach ($users as $user) {
					if(!EmailNotifications::isNotificationPAlertSent($user['user'], 'support', $notif['id']))
					{
						$msg = '';
						$SRs= SupportDesk::getSRsPerUser($user['user']);
						$msg.='Dear '.Users::getfirstNameById($user['user']).',<br/><br/>Please find below the assigned SRs which aren\'t closed yet:<br/> <br/>';					 
						foreach ($SRs as &$SR) { 
							$followps= SupportDesk::customerFollowupsNumber($SR['sd_no']);
							$SR['followps'] = $followps; 
						} 
						array_multisort(array_column($SRs, 'severity'), SORT_DESC,
		                array_column($SRs, 'followps'),      SORT_DESC, 
		                array_column($SRs, 'date'),      SORT_DESC,
		                $SRs);

		                $msg.='<table border="1"  style="font-family:Calibri;border-collapse:collapse;" ><tr><th  style="padding-left: 5px;padding-right: 5px;">SR#</th><th style="padding-left: 5px;padding-right: 5px;">Severity</th><th style="padding-left: 5px;padding-right: 5px;">Status</th><th style="padding-left: 5px;padding-right: 5px;">Customer</th><th style="padding-left: 5px;padding-right: 5px;">Short Description</th><th style="padding-left: 5px;padding-right: 5px;">Creation Date</th><th style="padding-left: 5px;padding-right: 5px;">Followups#</th></tr>';
							
						foreach ($SRs as &$SR) {
							
							if(strpos($msg, $SR['sd_no']) == false)
							{
								$msg.='<tr><td style="padding-left: 5px;padding-right: 5px;"><a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$SR['sd_no'])).'">'.$SR['sd_no'].'</a> </td><td  style="padding-left: 5px;padding-right: 5px;"> '.self::getsev($SR['severity']).'</td><td style="padding-left: 5px;padding-right: 5px;"> '.SupportDesk::getStatusLabel($SR['status']).'</td><td style="padding-left: 5px;padding-right: 5px;"> '.Customers::getNameById($SR['id_customer']).'</td><td style="padding-left: 5px;padding-right: 5px;">'.$SR['short_description'].' </td><td style="padding-left: 5px;padding-right: 5px;">'.date("d/m/Y",strtotime($SR['date'])).' </td><td style="text-align:right;padding-left: 5px;padding-right: 5px;"> '.$SR['followps'].'</td></tr>';
							}
						}
							$msg.='</table>';
						$oldSrs= SupportDesk::getSRsPerUserPerAgeD($user['user'], 90);
						if(!empty($oldSrs))
						{
							$msg.= "<br/>Kindly note that the below SRs have pending for more than 90 days:<br/><br/>";
							$msg.='<table border="1"  style="font-family:Calibri;border-collapse:collapse;" ><tr><th  style="padding-left: 5px;padding-right: 5px;">SR#</th><th  style="padding-left: 5px;padding-right: 5px;">Severity</th><th  style="padding-left: 5px;padding-right: 5px;">Status</th><th  style="padding-left: 5px;padding-right: 5px;">Customer</th><th  style="padding-left: 5px;padding-right: 5px;">Short Description</th><th  style="padding-left: 5px;padding-right: 5px;">Creation Date</th><th  style="padding-left: 5px;padding-right: 5px;">Followups#</th></tr>';
							$oldstr='';
							foreach ($oldSrs as &$SR) {
								if(strpos($oldstr, $SR['sd_no']) == false)
								{
									$oldstr.='<tr><td  style="padding-left: 5px;padding-right: 5px;"><a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$SR['sd_no'])).'">'.$SR['sd_no'].'</a></td><td  style="padding-left: 5px;padding-right: 5px;"> '.self::getsev($SR['severity']).'</td><td style="padding-left: 5px;padding-right: 5px;"> '.SupportDesk::getStatusLabel($SR['status']).'</td><td style="padding-left: 5px;padding-right: 5px;">'.Customers::getNameById($SR['id_customer']).'</td><td style="padding-left: 5px;padding-right: 5px;"> '.$SR['short_description'].' </td><td style="padding-left: 5px;padding-right: 5px;">'.date("d/m/Y",strtotime($SR['date'])).'</td><td style="text-align:right;padding-left: 5px;padding-right: 5px;">'.$SR['followps'].'</td></tr>';
								}
							}
							$msg .= $oldstr;
							$msg .='</table>';
						}

						$msg.='<br/><br/>Best Regards,<br/>SNSit';
						$subject = $notif['name'];
						$replace = array(
								EmailNotificationsGroups::getGroupDescription($notif['id']),
								$msg
				
						);
						$body = str_replace($to_replace, $replace, $notif['message']);
						Yii::app()->mailer->ClearAddresses();
						$assigneEmail= UserPersonalDetails::getEmailById($user['user']);
						if (filter_var($assigneEmail, FILTER_VALIDATE_EMAIL))
						{
							Yii::app()->mailer->AddAddress($assigneEmail);
						} 
						$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
						foreach($emails as $email)
						{
							//Yii::app()->mailer->AddCcs($email);
							Yii::app()->mailer->AddAddress($email);
						} 
						$email_lm = EmailNotificationsGroups::getLMNotificationUsers($user['user']);
						if (filter_var($email_lm, FILTER_VALIDATE_EMAIL)) {
						//	Yii::app()->mailer->AddCcs($email_lm);
							Yii::app()->mailer->AddAddress($email_lm);
						}
						//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						if (Yii::app()->mailer->Send(true)){
							echo "Sent";
							EmailNotifications::saveSentNotification($user['user'], 'support', $notif['id']);
						}
					}
				}
			}
		}
	}
	public function actionSendNextWeekSupport(){		
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('reminder_support');
		if ($notif != NULL)
		{
			$to_replace = array(
				'{group_description}',
				'{body}'
			);
			$msg = '';			
			$model=Yii::app()->db->createCommand("select primary_contact as primary_contact , secondary_contact as secondary_contact from fullsupport where from_date= (CURRENT_DATE()+ INTERVAL 2 DAY) and type=402")->queryRow();
			$sunday=Yii::app()->db->createCommand("select primary_contact as sundaysupport from fullsupport where from_date= (CURRENT_DATE()+ INTERVAL 2 DAY) and type=403")->queryScalar();
			if(empty($model) && empty($sunday))	{
				$msg.='No Support assigned next week.';
			}else{
				$msg .="Kindly note the following Support Assignments:<br />";
				if (!empty($model))
				{
					$msg .= "<br />Primary Support: ".Users::getNameById($model['primary_contact']);
					$msg .= "<br />Secondary Support: ".Users::getNameById($model['secondary_contact']);
				}
				if(!empty($sunday))
				{
					$msg.='<br />Sunday Support: '.Users::getNameById($sunday);
				} 
			}
			$subject = $notif['name'];
			$replace = array(
					EmailNotificationsGroups::getGroupDescription($notif['id']),
					$msg
	
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email){
				Yii::app()->mailer->AddAddress($email);
			}
			//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);			
		}			
		echo $body;
	}
	public function sendSms($sr, $cust){
	/*	$mobiles = Yii::app()->db->createCommand("select up.mobile as mobile, up.id_user as user from user_personal_details up, users u where (up.id_user in (SELECT id_user from user_groups where id_group= 9) OR up.id_user =(select ca from customers where name = '".$cust."' limit 1) ) and up.id_user=u.id and u.active=1")->queryAll();
		//$mobiles = Yii::app()->db->createCommand("select '+96171811801' as mobile, '58' as user ")->queryAll();
		
		foreach ($mobiles as $mobile)
		{
			$mno =  preg_replace("/[^0-9]/","",$mobile['mobile']);
			$user = 'nadine1';
			$pass = 'n@dine1';
			$sid = 'SNSit';
			$model=Yii::app()->db->createCommand("select primary_contact as primary_contact , secondary_contact as secondary_contact from fullsupport where from_date<= (CURRENT_DATE()) and to_date >= (CURRENT_DATE()) and type=402 ")->queryRow();
			$text ="Dear ".Users::getNameById($mobile['user']).", kindly note that ".$cust." has logged a system down issue SR".$sr.". The primary support is ".Users::getNameById($model['primary_contact'])." and secondary support is ".Users::getNameById($model['secondary_contact']).". Thank You, SNSit";
			$type = '1';
			$esm = '0';
			$dcs = '0';
			$link='http://78.108.164.68:8081/websmpp/websms?user='.$user.'&pass='.$pass.'&sid='.$sid.'&mno='.$mno.'&text='.urlencode($text).'&type='.$type.'&esm='.$esm.'&dcs='.$dcs.'';	
			
			$string = file_get_contents($link);
		}*/
	}  	
	public function actiontimePerdiscrp(){
	$SRs = Yii::app()->db->createCommand("SELECT id FROM `support_desk` where status in (5,3) and year(date)=2016 and reason =490")->queryAll();
	$amount=0;
	foreach($SRs as $SR){
		$time = Yii::app()->db->createCommand("select amount from user_time where year(date)=2016 and comment like CONCAT('%',".$SR['id'].",'%') ")->queryScalar();
		$amount+=$time;
	}
	print_r('The amount of time spent over SRs due to Data Corruption reason: '.$amount.' hours');
	}
	public function actionSendWeeklySummaryEmail(){
		$monday = strtotime('Monday last week');
		$sunday = strtotime('+6 days', $monday);		
		$start_datetime = date('Y-m-d 00:00:00', $monday);
		$finish_datetime = date('Y-m-d 23:59:59', $sunday);
		$start_date = date('Y-m-d', $monday);
		$finish_date = date('Y-m-d', $sunday);		
		$list1 = SupportDesk::getCurrentSRsPerform();
		$list2 = SupportDesk::get_total_submitted_last_week() ;
		$list3 = SupportDesk::get_total_closed_last_week();
		$list4 = SupportDesk::get_total_confirmed_closed_last_week() . SupportDesk::getCurrentRSRsPerform().SupportDesk::get_total_rsrs_submitted_last_week().SupportDesk::get_total_rsrs_confirmed_closed_last_week() ;		
		$list_exception = SupportDesk::getException($start_datetime, $finish_datetime);
		$list_customer = SupportDesk::getTopCustomers();
		$list_performers = SupportDesk::getTopPerformers($start_datetime, $finish_datetime);
		$list_weekly_performers = SupportDesk::getTopWeeklyPerformers($start_datetime, $finish_datetime);
		$list_maintenance = SupportDesk::getMaintenanceSupport($start_datetime, $finish_datetime);	

		////SLA

		$dateday= Yii::app()->db->createCommand("select date_sub(now(),INTERVAL 1 WEEK)")->queryScalar();
		$dateday=explode(" ",$dateday);
		$dateday=$dateday[0];
		$sum_issues_high_system_down=0;
		$sla_high_system_down = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>1 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'high' and system_down = 'Yes' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_high_system_down as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_high_system_down++;
			}
		}
		$slanumber_high_system_down = Yii::app()->db->createCommand("SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE  c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'high' and  system_down = 'Yes' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_high_system_down as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}
    		}
			if($sum>1){
				$sum_issues_high_system_down ++;
			}
		}
		$sla1 = $sum_issues_high_system_down;		
		$sum_issues_high=0;
		$sla_high = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>2 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'high' and system_down = 'No' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_high as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_high++;
			}	}
		$slanumber_high = Yii::app()->db->createCommand(" SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'high' and  system_down = 'No' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_high as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					}else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}			} 		}
			if($sum>2){
				$sum_issues_high ++;
			}	}	
		$sla2 = $sum_issues_high;		
		$sum_issues_medium=0;
		$sla_medium = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>3 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'medium' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_medium as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_medium++;
			}	}
		$slanumber_medium = Yii::app()->db->createCommand(" SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'medium' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_medium as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					} else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}
    		}
			if($sum>3){
				$sum_issues_medium ++;
			}
		}		
		$sla3 = $sum_issues_medium;		
		$sum_issues_low=0;
		$sla_low = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>5 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'low' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_low as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_low++;
			}	}
		$slanumber_low = Yii::app()->db->createCommand(" SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'low' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_low as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					}else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}	} 		}
			if($sum>5){		$sum_issues_low ++;		}
		}		
		$sla4 = $sum_issues_low;
		$sum=($sla1+$sla2+$sla3+$sla4);
		if ($sum==0){	$highrate=0;	}else{		$highrate= (($sla1+$sla2)/($sla1+$sla2+$sla3+$sla4))*100;		}
		$sla5=number_format ($highrate,2);	

		///SLA
		$notif = EmailNotifications::getNotificationByUniqueName('support_desk_weekly_performance');
		if ($notif != NULL){
			$subject = $notif['name'];		
			$to_replace = array(
					'{start_date}',
					'{end_date}',
					'{list1}',
					'{list2}',
					'{list3}',
					'{list4}',					
					'{list_exceptions}',				
					'{list_customer}',
					'{list_performers}',
					'{list_weekly_performers}',
					'{sla1}',
					'{sla2}',
					'{sla3}',
					'{sla4}',
					'{sla5}',
			);
			$replace = array(
					date('l jS \o\f F',strtotime($start_datetime)),
					date('l jS \o\f F',strtotime($finish_datetime)),
					$list1,
					$list2,
					$list3,
					$list4,					
					$list_exception,					
					$list_customer,
					$list_performers,
					$list_weekly_performers,
					$sla1,
					$sla2,
					$sla3,
					$sla4,	
					$sla5,	
			);
			$body = str_replace($to_replace, $replace, $notif['message']);		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email){
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}			
		//	Yii::app()->mailer->AddCcs('houda.nasser@sns-emea.com');
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}		
		echo $body;
	}  	
	public function  actionsendAfterHoursTickets() {
		$list = SupportDesk::getAfterHoursTickets();	$days = 30;	$period = date('d-m-Y',strtotime(date('d-m-Y')) - (24*3600*$days));
		$notif = EmailNotifications::getNotificationByUniqueName('afterhourssupport');
		if ($notif != NULL)	{
			$subject = $notif['name'];		
			$to_replace = array('{list}',	);
			$replace = array(	$list	);
			if (!empty($list)){
			$body = str_replace($to_replace, $replace, $notif['message']);
			 }else {
			  $body="There are no customers under the Standard Support Plan who logged issues after their normal working hours."; 
			} 		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)
			{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}
			Yii::app()->mailer->Subject  = "SRs Solved After Working Hour from ".$period." until today."; 
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
		echo $body;
	}  
	public function actionsendPotential(){		
		$list = SupportDesk::getPotentialsCustomer();		
		$days = 30;
		$period = date('d-m-Y',strtotime(date('d-m-Y')) - (24*3600*$days));		
		$notif = EmailNotifications::getNotificationByUniqueName('potential_24_7_report');
		if ($notif != NULL)	{
			$subject = $notif['name'];				
			$to_replace = array(
					'{period}',
					'{list}',
					
			);
			$replace = array(
					$period,
					$list,					
			);
			if (!empty($list)){
			$body = str_replace($to_replace, $replace, $notif['message']);
			 }else {
			  $body="There are no customers under the Standard Support Plan who logged issues after their normal working hours during the period from ".$period." until today."; 
			} 		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)
			{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}		
		echo $body;
	}  
	public function actionSendSRsLoggedNotCoveredByMaintenance(){		
		$start_datetime = date('Y-m-d 00:00:00', $monday);
		$finish_datetime = date('Y-m-d 23:59:59', $sunday);
		$list_maintenance = SupportDesk::getMaintenanceSupport($start_datetime, $finish_datetime);		
		$notif = EmailNotifications::getNotificationByUniqueName('support_desk_notcovered_maintenance');
		if ($notif != NULL)	{
			$subject = $notif['name'];
		
			$to_replace = array(										
					'{maintenance}',					
			);
			$replace = array(					
					$list_maintenance,					
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)	{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}		
		echo $body;
	} 	
	public function actionSendSummaryEmail(){
		$monday = strtotime('Monday last week');
		$start_date = date('Y-m-d 00:00:00', $monday);
		$finish_date = date('Y-m-d 23:59:59', strtotime('+ 6 days', $monday));
		$status_closed = SupportDesk::STATUS_CONFIRME_CLOSED;		
		$status_new = SupportDesk::STATUS_NEW;		
		$new_total = Yii::app()->db->createCommand("SELECT count(id) FROM support_desk WHERE status = '$status_new' ")->queryScalar();
		$status_in_progress = SupportDesk::STATUS_IN_PROGRESS;
		$new_in_progress = Yii::app()->db->createCommand("SELECT count(id) FROM support_desk WHERE status = '$status_in_progress'"	)->queryScalar();	
		$new_low = 0;
		$in_progress_low = 0;
		$in_progress_low_between=0;
		$new_medium = 0;
		$in_progress_medium = 0;
		$in_progress_medium_between=0;
		$new_high = 0;
		$in_progress_high = 0;
		$in_progress_high_between=0;		
		$new_high = Yii::app()->db->createCommand("select count(1) from (
SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 FROM support_desk sd , customers c  WHERE  sd.id_customer=c.id and c.strategic='HIGH' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR)and sd.status='0' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 >2 ) as NEWHIGH48")->queryScalar();
		$in_progress_high = Yii::app()->db->createCommand("select count(1) from (SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600 FROM support_desk sd ,customers c  WHERE sd.id_customer=c.id and c.strategic='HIGH' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR) >0 and sd.status='1' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600 >2 ) as INPROGHIGH96")->queryScalar();
		$in_progress_high_between= Yii::app()->db->createCommand("select count(1) from (SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 FROM support_desk sd ,customers c  WHERE sd.id_customer=c.id and c.strategic='HIGH' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR) >0 and sd.status='1' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 >2 and MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600<1 ) as INPROGHIGH48
")->queryScalar();
			$new_medium = Yii::app()->db->createCommand("select count(1) from (
SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 FROM support_desk sd , customers c  WHERE  sd.id_customer=c.id and c.strategic='MEDIUM' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR)and sd.status='0' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 >2 ) as NEWMEDIUM48")->queryScalar();
		$in_progress_medium = Yii::app()->db->createCommand("select count(1) from (SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600 FROM support_desk sd ,customers c  WHERE sd.id_customer=c.id and c.strategic='MEDIUM' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR) >0 and sd.status='1' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600 >2  ) as INPROGHIGH96")->queryScalar();
		$in_progress_medium_between= Yii::app()->db->createCommand("select count(1) from (SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 FROM support_desk sd ,customers c  WHERE sd.id_customer=c.id and c.strategic='MEDIUM' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR) >0 and sd.status='1' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 >2 and MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600<1 ) as INPROGHIGH48
")->queryScalar();
			$new_low = Yii::app()->db->createCommand("select count(1) from (
SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 FROM support_desk sd , customers c  WHERE  sd.id_customer=c.id and c.strategic='LOW' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR)and sd.status='0' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 >2) as NEWLOW48")->queryScalar();
		$in_progress_low = Yii::app()->db->createCommand("select count(1) from (SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600 FROM support_desk sd ,customers c  WHERE sd.id_customer=c.id and c.strategic='LOW' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR) >0 and sd.status='1' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600 >2 ) as INPROGHIGH96")->queryScalar();
		$in_progress_low_between= Yii::app()->db->createCommand("select count(1) from (SELECT sd.id , MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 FROM support_desk sd ,customers c  WHERE sd.id_customer=c.id and c.strategic='LOW' and sd.date <>'0000-00-00 00:00:00' and sd.id >'7477' and  TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR) >0 and sd.status='1' GROUP BY sd.id HAVING MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/172800 >2 and MIN(TIMESTAMPDIFF(SECOND,sd.date,NOW()+ INTERVAL 10 HOUR))/345600<1 ) as INPROGHIGH48
")->queryScalar();
		$notif = EmailNotifications::getNotificationByUniqueName('support_desk_weekly_summary');
		if ($notif != NULL){
			$subject = $notif['name'];		
			$to_replace = array(
					'{ct_new}',
					'{ct_in_progress}',
					'{ct_high_new}',
					'{ct_high_in_progress}',
					'{ct_high_in_progress_between}',
					'{ct_medium_new}',
					'{ct_medium_in_progress}',
					'{ct_medium_in_progress_between}',
					'{ct_low_new}',
					'{ct_low_in_progress}',
					'{ct_low_in_progress_between}',	);
			$replace = array(
					$new_total,
					$new_in_progress,					
					$new_high,
					$in_progress_high,
					$in_progress_high_between,
					$new_medium,
					$in_progress_medium,
					$in_progress_medium_between,
					$new_low,
					$in_progress_low,
					$in_progress_low_between,	);
			$body = str_replace($to_replace, $replace, $notif['message']);				
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)	{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	} 
	public function actionGetExcel($id){
		$data = SupportDesk::model()->findAllByPk((int)$id);		
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
		->setCellValue('A1', 'Sr#')
		->setCellValue('A2', 'Product')
		->setCellValue('A3', 'Schema')
		->setCellValue('A4', 'Status')
		->setCellValue('A5', 'Due Date')
		->setCellValue('A6', 'Description')
		->setCellValue('A7', 'Sr Related To Customization')
		->setCellValue('A8', 'Repeat')
		->setCellValue('A9', 'Logged By')
		->setCellValue('F1', 'Short Description')
		->setCellValue('F2', 'Environment')
		->setCellValue('F3', 'Severity')
		->setCellValue('F4', 'System Down')
		->setCellValue('F5', 'Assigned To')
		->setCellValue('F6', 'Sr Occured Previously')
		->setCellValue('F7', 'Reason')
		->setCellValue('F8', 'Responsibility')
		;		
		$i = 1;
		foreach($data as $d => $row){
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('C1', $row->sd_no)
			->setCellValue('C2', $row->product0->codelkup)
			->setCellValue('C3', $row->schema)
			->setCellValue('C4', SupportDesk::getStatusLabel($row->status))
			->setCellValue('C5', isset($row->due_date) ? date("d/m/Y",strtotime($row->due_date)) : "") 
			->setCellValue('C6', $row->description)
			->setCellValue('C7', $row->issue)
			->setCellValue('C8', $row->repeat)
			->setCellValue('C9', CustomersContacts::getNameById($row->customer_contact_id))
			->setCellValue('H1', $row->short_description)
			->setCellValue('H2', $row->environment)
			->setCellValue('H3', $row->severity)
			->setCellValue('H4', $row->system_down)
			->setCellValue('H5', Users::getUsername($row->assigned_to))
			->setCellValue('H6', $row->issue_incurred_previously)
			->setCellValue('H7', isset($row->reason)?$row->reason0->codelkup:"")
			->setCellValue('H8', $row->responsibility)
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('SR # - '.$row->sd_no);
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="SR #'.$row->sd_no.'.xls"');
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
	public function loadModel($id){
		$model=SupportDesk::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model){
		if (isset($_POST['ajax']) && $_POST['ajax']==='supportdesk-form')
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
		->setTitle("SNS Invoices Export");
		$model =SupportDesk::model(); 
		if(isset($_POST['SupportDesk']['product']))
		{
			$model->product= $_POST['SupportDesk']['product'];		
		}
		if(isset($_POST['SupportDesk']['status']) && count($_POST['SupportDesk']['status']) == count(array_filter($_POST['SupportDesk']['status'], "strlen"))){	
			$statuss=$_POST['SupportDesk']['status'];
					$inv_status="";		
		        	foreach ($statuss as $value) {
		        	$inv_status.="".rtrim(ltrim($value," ")," ").",";
		        	}
		        	$model->status= " in (".$inv_status." -1 ) ";
		}else{		$model->status ="";		}
		$model->severity= $_POST['SupportDesk']['severity'];
		$model->id_customer= $_POST['SupportDesk']['id_customer'];
		$model->sd_no= $_POST['SupportDesk']['sd_no'];
		$model->assigned_to= $_POST['SupportDesk']['assigned_to'];	
		if(isset($_POST['SupportDesk']['responsibility']))
		{
			$model->responsibility= $_POST['SupportDesk']['responsibility'];
		}
		if(isset($_POST['SupportDesk']['submitter_name'])){
			$model->submitter_name= $_POST['SupportDesk']['submitter_name'];	
		}
		$select = "SELECT s.* FROM support_desk s";
		$where = " WHERE 1=1 ";
		//print_r($model);exit;
		if(!empty($model->product)){
			$rep= Users::getIdByNameTrim($model->product);
			if(!empty($rep))
			{
				$where .= ' AND s.id_customer in (select id from customers where cs_representative= '.$rep.')';
			}		
		}
		if(!empty($model->responsibility)){
			$ca= Users::getIdByNameTrim($model->responsibility);
			if(!empty($ca))
			{
				$where .= ' AND s.id_customer in (select id from customers where ca= '.$ca.')';
			}		
		}
		if(isset($_POST['SupportDesk']['submitter_name']) && !empty($model->submitter_name)){
			if($model->submitter_name == 'CS')
			{
				$where .= ' AND s.assigned_to in (select id_user from user_groups where id_group= 9 )';
			}else if($model->submitter_name == 'PS'){
				$where .= ' AND s.assigned_to in (select id_user from user_groups where id_group= 13 )';
			} else if($model->submitter_name == 'OPS')
			{
				$where .= ' AND s.assigned_to in (select id_user from user_groups where id_group= 12 )';
			}		
		}
		if($model->status !=""){
			$where .= " AND s.status ".$model->status." ";
		}
		if($model->severity !=""){
			$where .= " AND s.severity ='$model->severity'";
		}
		if($model->id_customer !="" || !Yii::app()->user->isAdmin){
			if(! Yii::app()->user->isAdmin)
			{
				$customer= CustomersContacts::getCustomerById(Yii::app()->user->id);
			}else{
				$customer = Customers::getIdByName($model->id_customer);
			}
			$where .= " AND s.id_customer ='$customer'";
		}
		if($model->sd_no !=""){
			$where .= " AND s.id =$model->sd_no";
		}
		if($model->assigned_to !=""){
			$last = explode("  ", $model->assigned_to);
			$id_user = Yii::app()->db->createCommand("SELECT id FROM users WHERE firstname LIKE '%".substr($model->assigned_to,0,strrpos($model->assigned_to,"  "))."%' AND lastname LIKE '%".$last[1]."%'")->queryScalar();
			$where .= " AND s.assigned_to ='$id_user'";
		}
	//print_r($select.$where);exit;
		$data = Yii::app()->db->createCommand($select.$where)->queryAll();
		$sheetId = 0;

		$nb = sizeof($data);   
		$objDrawingPType = new PHPExcel_Worksheet_Drawing();
        $objDrawingPType->setWorksheet($objPHPExcel->setActiveSheetIndex($sheetId));
        $objDrawingPType->setName("logo");
        $objDrawingPType->setPath(Yii::app()->basePath . DIRECTORY_SEPARATOR . "../images/logo_status_report.png");
        $objDrawingPType->setCoordinates('A1');
        $objDrawingPType->setOffsetX(1);
        $objDrawingPType->setOffsetY(3);    

        $objPHPExcel->getActiveSheet()->getStyle('A4:K4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A4:K4')->applyFromArray($styleArray); 


		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A4', 'Sr')
		->setCellValue('B4', (Yii::app()->user->isAdmin != true) ? "Product": "Customer")
		->setCellValue('C4', 'Severity')
		->setCellValue('D4', 'Status')
		->setCellValue('E4', 'Description')
		->setCellValue('F4', 'Beirut Date')
		->setCellValue('G4', 'Customer Date')
		->setCellValue('H4', 'Beirut Closed Date')
		->setCellValue('I4', 'Customer Closed Date')
		->setCellValue('J4', 'Requestor')
		->setCellValue('K4', 'Assigned To')
		;		
		$i = 4;
		foreach($data as $d => $row){	
			$cust_time= Yii::app()->db->createCommand("select ('".$row['date']."' - INTERVAL ".$row['time_offset']." HOUR) + INTERVAL (select t.time_offset from time_zone t where time_zone=(select c.time_zone from customers c where id ='".$row['id_customer']."')) HOUR")->queryScalar();
			$sol_date= Yii::app()->db->createCommand("select MAX(DATE) from support_desk_comments where status=3 and is_admin=1 and id_support_desk= ".$row['id']."")->queryScalar();
			if(!empty($sol_date))
			{
				$solution_time= Yii::app()->db->createCommand("select ('".$sol_date."' - INTERVAL ".$row['time_offset']." HOUR) + INTERVAL (select t.time_offset from time_zone t where time_zone=(select c.time_zone from customers c where id ='".$row['id_customer']."')) HOUR")->queryScalar();
			}else{
				$solution_time='';
			}
			
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, $row['sd_no'])
			->setCellValue('B'.$i, (Yii::app()->user->isAdmin != true)? Codelkups::getCodelkup($row['product']) : Customers::getNameById($row['id_customer']))
			->setCellValue('C'.$i, $row['severity']) 
			->setCellValue('D'.$i, SupportDesk::getStatusLabel($row['status']))
			->setCellValue('E'.$i, $row['description'])
			->setCellValue('F'.$i, (isset($row['date']) && $row['date'] != '0000-00-00') ? date('d/m/Y H:i:s', strtotime($row['date'])):"")
			->setCellValue('G'.$i, (isset($row['date']) && $row['date'] != '0000-00-00') ? date('d/m/Y H:i:s', strtotime($cust_time)):"")
			->setCellValue('H'.$i, (!empty($sol_date) && $sol_date != '0000-00-00') ? date('d/m/Y H:i:s', strtotime($sol_date)):"")
			->setCellValue('I'.$i, (isset($solution_time) && $solution_time != '') ? date('d/m/Y H:i:s', strtotime($solution_time)):"")
			->setCellValue('J'.$i, $row['submitter_name'])	
			->setCellValue('K'.$i, Users::getUsername($row['assigned_to']))				
			;
		}		
		$objPHPExcel->getActiveSheet()->setTitle('SupportDesk');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="SupportDesk.xls"');
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
	public function actionNewIssues(){
		if (isset($_GET['days'])) {
		$days = $_GET['days'];
		}else {$days=0;}
		if(!GroupPermissions::checkPermissions('alerts-issue_tickets')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/supportDesk/newIssues' => array(
								'label'=>Yii::t('translations','Support Issues'),
								'url' => array('supportDesk/newIssues'),
								'itemOptions' => array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1
						),
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$dataProvider = SupportDesk::getNewIssues($days);
		$model=new SupportDesk('getNewIssues');
		$this->render('getNewIssues', array( 'model' => $model , 'days'=>$days));
	}
	public function actionSendFollowUp() {
		if (isset($_POST['supportdesk-grid_c0']) && is_array($_POST['supportdesk-grid_c0'])){
			$notif = EmailNotifications::getNotificationByUniqueName('support_desk_follow_up');
			if ($notif != NULL) {
		    	$totalUpdated = 0;
				foreach ($_POST['supportdesk-grid_c0'] as $srId) {
					$srId = (int) $srId;
					$model = SupportDesk::model()->findByPk($srId);					
		    		$to_replace = array('{no}', '{customer_name}', '{short_description}');
		    		$replace = array($model->sd_no, $model->idCustomer->name, $model->short_description);
		    		$subject = str_replace($to_replace, $replace, $notif['name']);					
					$sr='<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>';
					$to_replace = array('{last_issue_update}', '{sr}');
					$replace = array(SupportDesk::getLastCommentMessage($srId), $sr);
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->ClearAddresses();
		    		Yii::app()->mailer->ClearCcs();
					$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '".$model->id_customer."' AND access = 'Yes'")->queryColumn();
					foreach ($emails_customer as $email_c){
						if (filter_var($email_c, FILTER_VALIDATE_EMAIL)){
							Yii::app()->mailer->AddAddress($email_c);
						}
					}					
					$id_rep=Yii::app()->db->createCommand("SELECT cs_representative from customers WHERE id ='".$model->id_customer."'")->queryScalar();
					$email_csrep = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$model->assigned_to." ")->queryScalar();
						if (filter_var($email_csrep, FILTER_VALIDATE_EMAIL)){
							Yii::app()->mailer->AddAddress($email_csrep);
						}					
					$id_assigned= Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where sd_no='".$model->sd_no."' and id_customer='".$model->id_customer."'")->queryScalar();
					if($id_rep != $id_assigned){
						$email_assi = EmailNotificationsGroups::getLMNotificationUsers($id_rep);
							if (filter_var($email_assi, FILTER_VALIDATE_EMAIL)) {
								//Yii::app()->mailer->AddCcs($email_assi);
								Yii::app()->mailer->AddAddress($email_assi);
							}
					}						
					$email_Simmon="Simon.Kosseifi@sns-emea.com";
						if (filter_var($email_Simmon, FILTER_VALIDATE_EMAIL)) {
							//Yii::app()->mailer->AddCcs($email_Simmon);
							Yii::app()->mailer->AddAddress($email_Simmon);

						} 
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    			Yii::app()->mailer->Send(true);
	    			$model->num_of_followups++;
	    			$model->last_followup_date = date('Y-m-d H:i:s ', strtotime('now'));
	    			if ($model->save()){
						$totalUpdated++;
						if (isset($_POST['update']) && count($_POST['supportdesk-grid_c0']) == 1) 
						{
							echo json_encode(array ('status' =>'success', 'totalUpdated' => $totalUpdated,
													 "num_of_followups" => $model->num_of_followups, "last_followup_date" => date("d/m/Y H:i:s", strtotime($model->last_followup_date))
							));
	    					exit;			
						}		}  		}
	    		echo json_encode(array ('status' =>'success', 'totalUpdated' => $totalUpdated));
	    		exit;
	    	}		}
		echo json_encode(array ('status' =>'error', 'totalUpdated' => 0));
		exit;
	}
	public function actionupdateCheckedUsers(){		
		if (Yii::app()->user->isAdmin){
			$sr = $_POST['id'];
			$result =  Yii::app()->db->createCommand("SELECT users_checked  FROM support_desk WHERE id = ".$sr." ")->queryScalar();
			$user=','.Yii::app()->user->id.',';
			if(strpos($result, $user) == false)	{
				$allUsers=$result;
				$allUsers.=Yii::app()->user->id.',';
				Yii::app()->db->createCommand("UPDATE support_desk SET users_checked='".$allUsers."' where id=".$sr." ")->execute();
			}	}	}
	public function actionVacationLicenseAudit(){		
		if (Yii::app()->user->isAdmin){
			$sr = $_POST['id'];
			$id_customer = Yii::app()->db->createCommand("SELECT id_customer FROM support_desk where id=".$sr."")->queryScalar();
			$last_date=Yii::app()->db->createCommand("(select IFNULL( c.date_audit ,' ') as lastaudit from customers c , eas e , eas_items ei where c.id=e.id_customer and c.id=".$id_customer." and e.id=ei.id_ea and e.category='25' and e.`status`='2'and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null ) UNION ALL (select IFNULL( c.date_audit ,' ') as lastaudit from customers c where  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
	 								and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id )and c.id=".$id_customer." ) ")->queryScalar();
			$wanted=Yii::app()->db->createCommand("(select c.id from customers c , eas e , eas_items ei where c.id=e.id_customer and c.id=".$id_customer." and e.id=ei.id_ea and e.category='25' and e.`status`='2'and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null ) UNION ALL (select c.id from customers c where  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
	 								and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id )and c.id=".$id_customer." ) ")->queryScalar();
			$connection_last_date=Yii::app()->db->createCommand("select count(1)  from connections where id_customer='".$id_customer."' ")->queryScalar(); // and last_updated > NOW()-INTERVAL 12 week ")->queryScalar();
			$licenseAudited= Yii::app()->db->createCommand("select n_licenses_audited  from customers where id='".$id_customer."'  ")->queryScalar();
			if($wanted!=false){
				$last_date_year = substr($last_date,6,5);
				$last_date_month=substr($last_date,3,3);
				$last_date_day= substr($last_date,0,3);
				$last_date=$last_date_year."-".$last_date_month."-".$last_date_day;
				$date_diff=Yii::app()->db->createCommand("Select DATEDIFF(NOW()+ INTERVAL 10 HOUR,IFNULL('".$last_date."','2013-1-1'))>122 as difference")->queryScalar();
			if($date_diff!='0' || empty($licenseAudited) ||$licenseAudited<1){
				$valid=0;	}	else{		$valid=1;	}
			}else{		$valid=1;	}
			if($connection_last_date==0){		$valid=2;	}
		}else{		$valid=1;	}
		$returnArr['valid']=$valid;
		echo json_encode($returnArr);					
		exit;
	}
	public function actionSendLicensingEmail(){
			$notif = EmailNotifications::getNotificationByUniqueName('licensing_email');
			if ($notif != NULL) {
		  			$cs_reps=Yii::app()->db->createCommand("select DISTINCT u.id as id,u.firstname as name from customers c,users u where u.id=c.cs_representative and c.cs_representative is not null ")->queryAll();
					foreach ($cs_reps as $cs_rep ) {
						$email_body="";
						$id_rep=$cs_rep['id'];
						$rep_customers=Yii::app()->db->createCommand("select c.id as id,c.name from customers c where c.cs_representative=".$id_rep." ")->queryAll();
						$rep_name=Users::getNameById($id_rep);
						$email_body="Dear ".$rep_name.",<br /><br />Please conduct a license auditing for the following customers as it has been more than 4 months. Be aware that you will not be allowed to close any issues related to these customers:<br /><br /><ul>";
						foreach ($rep_customers as $rep_customer) {
							$id_customer=$rep_customer['id'];
							$customer_name=Customers::getNameById($id_customer);
							$last_date=Yii::app()->db->createCommand("(select IFNULL( c.date_audit ,' ') as lastaudit from customers c , eas e , eas_items ei where c.id=e.id_customer and c.id=".$id_customer." and e.id=ei.id_ea and e.category='25' and e.`status`='2'and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null ) UNION ALL (select IFNULL( c.date_audit ,' ') as lastaudit from customers c where  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
 								and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id )and c.id=".$id_customer." ) ")->queryScalar();
							$wanted=Yii::app()->db->createCommand("(select c.id from customers c , eas e , eas_items ei where c.id=e.id_customer and c.id=".$id_customer." and e.id=ei.id_ea and e.category='25' and e.`status`='2'and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null ) UNION ALL (select c.id from customers c where  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
 								and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id )and c.id=".$id_customer." ) ")->queryScalar();
							if($wanted!=false){
							$last_date_year = substr($last_date,6,5);
							$last_date_month=substr($last_date,3,3);
						$last_date_day= substr($last_date,0,3);
						$last_date=$last_date_year."-".$last_date_month."-".$last_date_day;
							$date_diff=Yii::app()->db->createCommand("Select DATEDIFF(NOW()+ INTERVAL 10 HOUR,IFNULL('".$last_date."','2013-1-1'))>122 as difference")->queryScalar();
							if($date_diff!='0'){
								$url=Yii::app()->createAbsoluteUrl('customers/view', array('id'=>$id_customer));
								$email_body .="<li><a href=".$url.">".$customer_name."</a><br /></li>";
							}	}	}
						$email_body.="</ul>";
		    		$to_replace = array('{email_body}');
		    		$replace = array($email_body);
		    		$subject = "WMS License Auditing";
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->ClearAddresses();
		    		Yii::app()->mailer->ClearCcs();
					$email_csrep = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$id_rep." ")->queryScalar();
						if (filter_var($email_csrep, FILTER_VALIDATE_EMAIL)){
							Yii::app()->mailer->AddAddress($email_csrep);
						}
					$email_lm = EmailNotificationsGroups::getLMNotificationUsers($id_rep);
						if (filter_var($email_lm, FILTER_VALIDATE_EMAIL)) {
							//Yii::app()->mailer->AddCcs($email_lm);
							Yii::app()->mailer->AddAddress($email_lm);
						}					
						$email_bernz="Bernard.Khazzaka@sns-emea.com";
						if (filter_var($email_bernz, FILTER_VALIDATE_EMAIL)) {
							//Yii::app()->mailer->AddCcs($email_bernz);
							Yii::app()->mailer->AddAddress($email_bernz);

						} 
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    			Yii::app()->mailer->Send(true);
	    			}
	    	}  		exit;  }	
	public function actionsendSLAEmailNotification(){
		$dateday= Yii::app()->db->createCommand("select date_sub(now(),INTERVAL 1 WEEK)")->queryScalar();
		$dateday=explode(" ",$dateday);
		$dateday=$dateday[0];
		$sum_issues_high_system_down=0;
		$sla_high_system_down = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>1 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'high' and system_down = 'Yes' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_high_system_down as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_high_system_down++;
			}
		}
		$slanumber_high_system_down = Yii::app()->db->createCommand("SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE  c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'high' and  system_down = 'Yes' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_high_system_down as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}
    		}
			if($sum>1){
				$sum_issues_high_system_down ++;
			}
		}
		$sla1 = $sum_issues_high_system_down;		
		$sum_issues_high=0;
		$sla_high = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>2 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'high' and system_down = 'No' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_high as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_high++;
			}	}
		$slanumber_high = Yii::app()->db->createCommand(" SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'high' and  system_down = 'No' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_high as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					}else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}			} 		}
			if($sum>2){
				$sum_issues_high ++;
			}	}	
		$sla2 = $sum_issues_high;		
		$sum_issues_medium=0;
		$sla_medium = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>3 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'medium' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_medium as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_medium++;
			}	}
		$slanumber_medium = Yii::app()->db->createCommand(" SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'medium' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_medium as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					} else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}
    		}
			if($sum>3){
				$sum_issues_medium ++;
			}
		}		
		$sla3 = $sum_issues_medium;		
		$sum_issues_low=0;
		$sla_low = Yii::app()->db->createCommand(" SELECT DATEDIFF(NOW(),sd.date)>5 as TotalIssues FROM support_desk sd, customers c WHERE c.id = sd.id_customer and (c.support_weekend = 'Elite' or c.support_weekend = 'Premium') and severity = 'low' and sd.status != 5 and sd.status !=3 ")->queryAll();
		foreach($sla_low as $sla_issue){
			if($sla_issue['TotalIssues']==1){
				$sum_issues_low++;
			}	}
		$slanumber_low = Yii::app()->db->createCommand(" SELECT sd.date as submittedstartdate, NOW() as datenow, c.week_end as weekend FROM support_desk sd, customers c WHERE c.id = sd.id_customer and c.support_weekend = 'Standard' and severity = 'low' and sd.status != 5 and sd.status !=3")->queryAll();
		$sum = 0;
		foreach ($slanumber_low as $row) {
			$rowstart=$row['submittedstartdate'];
			$rowend=$row['datenow'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$weekenddays = $row ['weekend'];
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($weekenddays!="Friday/Saturday"){
  					if($date == "saturday" || $date == "sunday"){
  					}else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}	} 		}
			if($sum>5){		$sum_issues_low ++;		}
		}		
		$sla4 = $sum_issues_low;
		$sum=($sla1+$sla2+$sla3+$sla4);
		if ($sum==0){	$highrate=0;	}else{		$highrate= (($sla1+$sla2)/($sla1+$sla2+$sla3+$sla4))*100;		}
		$sla5=number_format ($highrate,2);		
		$notif = EmailNotifications::getNotificationByUniqueName('SLA_report');
		if ($notif != NULL){
			$subject = $notif['name'];		
			$to_replace = array(				
					'{sla1}',
					'{sla2}',
					'{sla3}',
					'{sla4}',
					'{sla5}',			
			);
			$replace = array(					
					$sla1,
					$sla2,
					$sla3,
					$sla4,	
					$sla5,					
			);
			$body = str_replace($to_replace, $replace, $notif['message']);		
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email)
			{
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			} 
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				if (Yii::app()->mailer->Send(true))
					{
						echo "Sent";
					} else {echo "Not Sent" ;}
		}	}
	public static function actioncheckunratedsr(){
		$unrated=Yii::app()->db->createCommand("select DISTINCT sd.id, sd.sd_no ,sd.id_customer from support_desk sd ,support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.status='3' and sdc.status='3' and DATEDIFF(CURRENT_DATE(),sdc.date) >20 and sd.unrated_followup='0' order by sdc.date desc")->queryAll();
			$notif = EmailNotifications::getNotificationByUniqueName('unrated_srs');
			if ($notif != NULL)	{
			$subject = $notif['name'];
			foreach ($unrated as $value) {
			$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '{$value['id_customer']}' AND access = 'Yes'")->queryColumn();
			$sr=$value['sd_no'];			
			Yii::app()->mailer->ClearAddresses();			
			$to_replace = array('{sr}');			
			$replace = array($sr);	
						$body = str_replace($to_replace, $replace, $notif['message']);
						foreach ($emails_customer as $email_c){
							if (filter_var($email_c, FILTER_VALIDATE_EMAIL)){
								Yii::app()->mailer->AddAddress($email_c);
							}			}
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				    	if(Yii::app()->mailer->Send(true)){
				    		echo" Sent";
				    		$sent = Yii::app()->db->createCommand("UPDATE support_desk SET unrated_followup='1' , unrated_followup_date=NOW() where id='".$value['id']."' ")->execute();
				    	}	}	}	}
	public static function actiondisablesrsubmission(){
		$unratedtwo=Yii::app()->db->createCommand("select DISTINCT sd.id from support_desk sd ,support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.status='3' and sdc.status='3' and DATEDIFF(CURRENT_DATE(),sd.unrated_followup_date) >6 and sd.unrated_followup='1' order by sdc.date desc")->queryAll();
		foreach ($unratedtwo as $value) {
			$sent = Yii::app()->db->createCommand("UPDATE support_desk SET unrated_followup='2' where id='".$value['id']."' ")->execute();
		   						echo "Disabled: ".$value['id']." /";    	}	}  }
								?>

