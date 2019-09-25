<?php
class EasController extends Controller
{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array(
			'accessControl', 
			'postOnly + delete', 
		);
	}
	public function init()
	{
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - EAs');
	}
	public function accessRules()
	{
		return array(
			array(
				'allow',
				'actions'=>array('SendTMEmailNotification','generalUpdate','sendOpp','sendEaEmail', 'insertChecklist', 'checkconn'),
				'users'=>array('*'),
			),	
			array('allow',
				'actions'=>array(
						'index','view','create','update', 'delete',
						'GetTrainings', 'ChangeSortRangeNote',
						'manageItem', 'getTrainingDesc','getRegion','manageTerm','manageTermSecond','manageTermSandUSec', 'manageTermSandU', 'manageNote' , 'saveDiscount','saveNetAmount',
						'deleteItem', 'deleteTerm', 'view', 'print', 
						'upload','testPdf', 'updateHeader', 'deleteUpload','DeleteUploadSheet' ,'checkDuration','updateDuration'
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
				 'path' => Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'eas'
			 ),
		 );
	}
    /*
    * Author: Mike
    * Date: 18.06.19
    * Add a sorting input
    */
	public function actionChangeSortRangeNote($id){
	    Yii::app()->db->createCommand("UPDATE codelkups SET sort_rang=".(int)trim(Yii::app()->getRequest()->getQuery('range'))." WHERE id=".(int)$id)->execute();
        echo json_encode(array('successes'));
        exit();
    }

	public function actiontestPdf(){	$model = $this->loadModel('23');	$this->render('_export_pdf', array('model' => $model)); }
    /*
     * Author: Mike
     * Date: 19.06.19
     * Add estimated MDs per t&m EA
     */
	public function actionCreate()
	{
		if(!GroupPermissions::checkPermissions('eas-list','write'))
		{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/eas/create' => array(
					'label'=> 'New EA',
					'url' => array('eas/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;	$trainingEamodel = new TrainingEas();	$model = new Eas();
		$model->ea_number = "00000";	$model->author = Yii::app()->user->id;	$model->status = Eas::STATUS_NEW;
		$model->expense = '';	$model->TM =0;
		if (isset($_POST['Eas'])){
			if (isset($_POST['Eas']['crmOpp']) && (!empty($_POST['Eas']['crmOpp'] && $_POST['Eas']['crmOpp']!='')) && !isset($_POST['Eas']['TM']) ){
				$exist=self::validateOpp($_POST['Eas']['crmOpp']);
				if ($exist){	$model->crmOpp =$_POST['Eas']['crmOpp']; }else{	$model->addCustomError('crmOpp', 'Opportunity# is not valid');	}
			}else if (!isset($_POST['Eas']['TM'])) {	$model->addCustomError('crmOpp', 'Opportunity# cannot be blank');	}
			else if (isset($_POST['Eas']['crmOpp']) && (!empty($_POST['Eas']['crmOpp'] && $_POST['Eas']['crmOpp']!='')) ){
				$model->crmOpp =$_POST['Eas']['crmOpp'];
			}
			if (isset($_POST['Eas']['category'])){
				switch ($_POST['Eas']['category']){
				case '24':
					if (empty($_POST['TrainingEas']['id_training']))
					{
						$trainingEamodel->addCustomError('id_training', 'Training Course Cannot be blank');
					} 
						break;
					case '454':
					case '25':
						unset($_POST['Eas']['project_name']);
						unset($_POST['Eas']['id_parent_project']);
						break;
					case '26':
					case '27':
						if (empty($_POST['Eas']['project_name']))
						{
							$model->addCustomError('id_project', 'Project cannot be blank');
						}
						if (!empty($_POST['Eas']['project_name']) && !empty($_POST['Eas']['customer_name']))
						{
							$id_cust = Customers::getIdByName($_POST['Eas']['customer_name']);							
							$ver = Eas::singleProject($id_cust,$_POST['Eas']['project_name']);
							if(!$ver){
								$model->addCustomError('id_project', 'Duplicate Project Name');
							}
						}
						unset($_POST['Eas']['id_parent_project']);
						break;
					case '28':
						if (empty($_POST['Eas']['project_name']))
						{
							$model->addCustomError('id_project', 'Project cannot be blank');
						} 
						if (empty($_POST['Eas']['id_parent_project']))
						{
							$model->addCustomError('id_parent_project', 'Parent Project cannot be blank');
						}
						if (!empty($_POST['Eas']['project_name']) && !empty($_POST['Eas']['customer_name']))
						{
							$id_cust = Customers::getIdByName($_POST['Eas']['customer_name']);							
							$ver = Eas::singleProject($id_cust,$_POST['Eas']['project_name']);
							if(!$ver){
								$model->addCustomError('id_project', 'Duplicate Project Name');
							}
						}
						break;					
				}
				if (isset($_POST['Eas']['TM'])) 
				{
					if($_POST['Eas']['TM']==1 )				
					{
						if($_POST['Eas']['category'] == '28')
						{	$model->addCustomError('id_parent_project', 'Cannot create a T&M Change Request EA');
						}else	{	$model->TM = 'Yes';	}						
					}else{
						if($_POST['Eas']['category'] == '28')
						{
							if (!empty($_POST['Eas']['id_parent_project']))
							{
								$checkparent=Eas::checktandmFlag(Projects::getEAid($_POST['Eas']['id_parent_project']));
								if($checkparent == 1){	$model->addCustomError('id_parent_project', 'Parent Porject cannot be T&M');
								}else	{	$model->TM = 'No';	}
							}
						}else	{	$model->TM = 'No';	}
					}
				}else{
					if($_POST['Eas']['category'] == '28')
						{
							if (!empty($_POST['Eas']['id_parent_project']))
							{
								$checkparent=Eas::checktandmFlag(Projects::getEAid($_POST['Eas']['id_parent_project']));
								if($checkparent == 1){
									$model->addCustomError('id_parent_project', 'Parent Porject cannot be T&M');
								}else{	$model->TM = 'No';	}
							}
						}else{	$model->TM = 'No'; }
				}
				if(isset($_POST['Eas']['description'])){	$model->description = $_POST['Eas']['description'];	}
				else{	$model->addCustomError('description', 'Description Can\'t be empty'); }
			}
			$model->attributes = $_POST['Eas'];
			if ($model->validate()) 
			{
				$model->currency = $model->customer->default_currency;
				switch ($model->category)
				{
					case '26':
					case '27':
						$resp = Projects::validatname($_POST['Eas']['project_name']);
						if ($resp<1 && isset($_POST['Eas']['project_name']))
						{
							$model->project_n = $_POST['Eas']['project_name'];
						}
						else
						{
							$model->addCustomError('id_project', 'Project name is invalid');
						}
						break;
					case '24':
							$id_training = $_POST['TrainingEas']['id_training'];
							$trainingEamodel->id_training = $id_training;					
					break;
					case '28':
						if(isset($_POST['Eas']['id_parent_project'])){
								
								$id_parent_project=$_POST['Eas']['id_parent_project'];
								$status_parent_project=Projects::getProjectStatus($id_parent_project);

								if($status_parent_project=='2'){
									$resp = Projects::validatname($_POST['Eas']['project_name']);
									if ($resp<1 && isset($_POST['Eas']['project_name'])){
											$model->project_n = $_POST['Eas']['project_name'];
										}else{	$model->addCustomError('id_project', 'Project name is invalid');	}
								}
							}
						break;
				}

				if (isset($_POST['Eas']['mds']) && !empty($_POST['Eas']['mds'])){
                    $model->mds = trim($_POST['Eas']['mds']);
                }

				if ($model->save()) 
				{	
					$model->ea_number = Utils::paddingCode($model->id);
					Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES ('.$model->id.',658),('.$model->id.',1087)')->execute(); 
					if (empty($model->currency)){	$model->currency = $model->customer->default_currency;	}	
					$reasnote = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='228'")->queryScalar();
					if ($reasnote ==0) {	Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',228)')->execute(); }
					if($model->category=='496'){
						$reasnot = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='550'")->queryScalar();
						if ($reasnot ==0) {	Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',550)')->execute(); 	}
						$reasnot2 = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='690'")->queryScalar();
						if ($reasnot2 ==0) { Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',690)')->execute();	}
						$reasnot3 = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='691'")->queryScalar();
						if ($reasnot3 ==0) { Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',691)')->execute(); }
					}
					if(Customers::getCountryById($model->id_customer) == 113)
					{
						$reasnot = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='1401'")->queryScalar();
						if ($reasnot ==0) {	Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',1401)')->execute(); 	}
					}
					if($model->category =='24'){
						Yii::app()->db->createCommand('INSERT INTO training_eas (id_training, id_ea) VALUES('.$id_training.','.$model->id.')')->execute();
						Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',676),('.$model->id.',677),('.$model->id.',678)')->execute();
					}
					if($model->category=='25' || $model->category=='454' ){
						$reasnote1 = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='489'")->queryScalar();
					if ($reasnote1 ==0) {		Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',489)')->execute(); }
					$reasnote2 = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='492'")->queryScalar();
					if ($reasnote2 ==0) {		Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',492)')->execute(); }
					}
					$model->save();	$this->redirect(array('eas/update', 'id'=>$model->id, 'new' => 1));
				}
			}
		}			
		$this->render('create', array('model' => $model));
	}
public function actionGetRegion($id){
		$flag = Yii::app()->db->createCommand("select region, custsupport from customers where id= ".$id)->queryRow();
		echo json_encode(array('region'=> $flag['region'],'custsupport'=> $flag['custsupport']));
		exit();
	}
	public function actiongetTrainingDesc($id){
		$query ='select t.idTrainings, t.training_number, t.country, city, t.start_date, t.end_date,t.status, cl.codelkup as cname
												from trainings_new_module t join codelkups cl on t.course_name = cl.id											
												join users u on t.instructor = u.id';
		
		$where =' Where t.idTrainings ='.$id;	$result = Yii::app()->db->createCommand($query.$where)->queryAll();
		foreach($result as $i=>$res)
			$desc = $res['cname'].' '.$res['city'].' '.$res['start_date'].' '.$res['end_date'];		
		echo json_encode($desc) ;
	}
	public function actionGetTrainings()
	{
		$this->layout='';
		echo json_encode(TrainingsNewModule::getTrainingSelectDDL());
		exit();
	}
	public function actionsendOpp()
	{		
		$part = array();	$notif = EmailNotifications::getNotificationByUniqueName('crmopp');
		if ($notif != NULL)
		{
			$to_replace = array('{list}', ); $contracts = '';
			$models= Yii::app()->db->createCommand("SELECT ea_number FROM `eas` where status=1 and crmOpp is not null and crmOpp in (select opp_id from crm_opportunities where status=1)")->queryAll();
			if(empty($models)){	$contracts.='<br/> All closed opportunities have their EAs approved<br>';			}
			else{
				$str='(';	$contracts.='Kindly find below all EAs that are not approved and their opportunities are closed:<br><br>';
				foreach($models as $model){	$contracts .= '- <b>EA# '.$model['ea_number'];	$contracts .= '</b><br>';	}			
			}
			$models2= Yii::app()->db->createCommand("SELECT opp_id FROM `crm_opportunities` where status=1 and opp_id not in (select DISTINCT(crmOpp) from eas where crmOpp is not null) 
				and opp_id not in ( 10093,10101,10102,10106,10107,10108,10110,10112,10116,10117,10118,10119,10120,10124,10129,10130,10133,10134,10139,10140,10144,10145,10147,10150,10153,10154,10167,10173,10174,10175,10176,10178,10179,10180,10181,10182,10188,10190,10197,10198,10206,10207,10208,10209,10210,10211,10212,10213,10214,10215,10217,10218,10220,10275,10525,10671,10736,10836)")->queryAll();
			if(!empty($models2)){
				$str='(';	$contracts.='<br>Kindly find below all closed opportunities that are not associated with an EA on SNSit:<br><br>';
				foreach($models2 as $model){	$contracts .= '- <b>Opportunity# '.$model['opp_id'];	$contracts .= '</b><br>';	}			
			}
			$subject = $notif['name'];
			$replace = array(
					$contracts, );
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email)	{	Yii::app()->mailer->AddAddress($email);	}
			Yii::app()->mailer->Subject  = $subject;	Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);			
		}			
		echo $body;
	}
	public function actioninsertChecklist(){}
	public function actionsendEaEmail(){
		$models= Yii::app()->db->createCommand("
			SELECT e.id as id
							FROM eas e
							where ((e.status=1 
							and (e.send_email is NULL or (select id_model from email_notifications_sent where id_model=e.id and table_model='eas' and id_notification in (1,38) limit 1) is NULL)) 
							OR (e.status>1 
							and (e.send_email=1 or (select id_model from email_notifications_sent where id_model=e.id and table_model='eas' and id_notification in (2,39) limit 1) is NULL))
							OR (e.status=0 
							and (e.send_email=2 or (select id_model from email_notifications_sent where id_model=e.id and table_model='eas' and id_notification in (3,40) limit 1) is NULL)))
 ")->queryAll();
		foreach ($models as $m)
		{
			$model = $this->loadModel($m['id']);
			if ($model->status == 1) 
			{
				if($model->category == 25 || ($model->category == 28 && $model->customization ==1 ) || ( $model->category ==27 && ($model->template ==2 || ($model->template ==6 && Customers::getRegion($model->id_customer) != 59)))) 
				{
					if ($model->getTermsSum() == 200) 
					{
						$model->send_email=1;	$model->save();
						self::sendNotificationsEmails($model);
					}
				}
				else {
					if ($model->getTermsSum() == 100) 
					{
						$model->send_email=1;	$model->save();
						self::sendNotificationsEmails($model);
					}

				}
			}else{
				if($model->status >= 2)
				{
					$model->send_email=2;	$model->save();
					if($model->category == 25 || ($model->category == 28 && $model->customization ==1 ) || ( $model->category ==27 && ($model->template ==2 || ($model->template ==6 && Customers::getRegion($model->id_customer) != 59))) ) {	self::sendEAwithSandU($model);	}
					else if($model->category=='27' || $model->category=='28') 	{
						self::sendEACustomization($model); }
					self::sendNotificationsEmails($model);
				}
				else if($model->status == 0){
					$model->send_email=3; $model->save();	self::sendNotificationsEmails($model);
				}
			}			
		}
	}
	public function actionUpdateHeader($id)
	{
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		$error = false;
		$create = false;
		if($model->category == 25 )
			$model->expense = "N/A";
		$model->customer_name = $model->customer->name;
		$model->parent_project = Projects::getNameById($model->id_parent_project);
		$model->project_name = Projects::getNameById($model->id_project);
		$old_expense = $model->expense;
		if ($model->expense != 'N/A' && $model->expense != 'Actuals')
		{
			$model->lump_sum = $model->expense;
		}
		$stat= $model->status;	$initialStat=$model->status;	$app=0;
		if (isset($_POST['Eas']))
		{
			unset($_POST['Eas']['category']);	unset($_POST['Eas']['project_name']);	unset($_POST['Eas']['id_parent_project']);	unset($_POST['Eas']['id_customer']);
			if (empty($_POST['Eas']['country_perdiem']) || $_POST['Eas']['expense'] == 'N/A' ||  (int)$_POST['country_perdiem_checbox'] === 0){
                $model->country_perdiem = null;
            }else if((int)$_POST['country_perdiem_checbox'] === 1 && !empty($_POST['Eas']['country_perdiem'])){
                $model->country_perdiem = $_POST['Eas']['country_perdiem'];
            }
			if (isset($_POST['Eas']['billto_contact_person']) && $_POST['Eas']['billto_contact_person'] != $model->billto_contact_person){		
					$model->billto_contact_person= $_POST['Eas']['billto_contact_person']; } 
			if (isset($_POST['Eas']['billto_address']) && $_POST['Eas']['billto_address'] != $model->billto_address){		
					$model->billto_address= $_POST['Eas']['billto_address']; } 
			if (!$model->isEditable())
			{
				if (isset($_POST['Eas']['description']) && $_POST['Eas']['description'] != $model->description)	{		
					$model->description= $_POST['Eas']['description']; } 
				if (isset($_POST['Eas']['expense']) && $_POST['Eas']['expense'] != $model->expense)	{		
					$model->expense= $_POST['Eas']['expense']; } 
				if (isset($_POST['Eas']['status']))
				{
					$app=$_POST['Eas']['status'];
					if ($_POST['Eas']['status'] != $stat)
					{
						$model->status = $_POST['Eas']['status'];
						if($_POST['Eas']['status'] == Eas::STATUS_CANCELLED)
						{		
							if ($model->category != 24 && $model->category != 25){
									$pid=$model->id_project;
									Projects::setStatusinactive($model->id_project, Projects::STATUS_INACTIVE);
							} 							
						}
					}
				}
				if (isset($_POST['Eas']['customer_lpo']))
				{
					$mandatory = Customers::getLpo((int)$model->id_customer);
					if($_POST['Eas']['customer_lpo'] == "" && $mandatory == 'Yes' && $_POST['Eas']['status'] == Eas::STATUS_APPROVED )
						$model->addCustomError('customer_lpo', 'Customer LPO cannot be blank');
						$model->customer_lpo = $_POST['Eas']['customer_lpo'];
				}				
			}else{ 
				if($model->status == Eas::STATUS_CANCELLED)
					{	if($model->category != 24){	
						$pid=$model->id_project;
						Projects::setStatusinactive($model->id_project, Projects::STATUS_INACTIVE);
						}
					}
				if (isset($_POST['Eas']['crmOpp']))
				{
					if (preg_match('#[^0-9]#',$_POST['Eas']['crmOpp'])){
						$model->addCustomError('crmOpp', 'CRM# should only be numeric.');
					}else if (!empty($_POST['Eas']['crmOpp']) && $model->TM!=1){
						$Oppex =Yii::app()->db->createCommand("select count(1) from  crm_opportunities where opp_id=".$_POST['Eas']['crmOpp']."   ")->queryScalar();
						if($Oppex==0){
							$model->addCustomError('crmOpp', 'CRM# specified does not exist');
						}else{	$model->crmOpp=$_POST['Eas']['crmOpp'];	}
					}
				}					
				if (isset($_POST['Eas']['expense'])){
					switch ($_POST['Eas']['expense']){
						case 'N/A':
						case 'Actuals':
                        if(empty($_POST['Eas']['country_perdiem']) && $_POST['country_perdiem_checbox'] == 1){
                            $model->addCustomError('country_perdiem', 'Country cannot be blank');
                        }
							break;
						case '':
							$model->addCustomError('expense', 'Expense cannot be blank');
							break;
						default:
							if (empty($_POST['Eas']['lump_sum']))
							{
								$model->addCustomError('lump_sum', 'Lump Sum cannot be blank');
							}
                            if(empty($_POST['Eas']['country_perdiem']) && !empty($_POST['Eas']['lump_sum'])){
                                $model->addCustomError('country_perdiem', 'Country cannot be blank');
                            }
							break;
					}
				}
				if (isset($_POST['Eas']['customer_lpo'])){
					$mandatory = Customers::getLpo((int)$model->id_customer);
					if($_POST['Eas']['customer_lpo'] == "" && $mandatory == 'Yes'  && $_POST['Eas']['status'] == Eas::STATUS_APPROVED)
						$model->addCustomError('customer_lpo', 'Customer LPO cannot be blank');
				}
				if (isset($_POST['Eas']['status'])
						&& $_POST['Eas']['status'] == Eas::STATUS_APPROVED
						&& empty($model->approved)
						&& $model->status !=2){
					$error_message = '';
					if (count($model->eItems) == 0)	{
						$error_message = 'You can not change this EA\'s status to approved until You add EA Items';	$error = true;
					}
				if($model->getTotalAmount() !=0 || $model->TM==1 ){	
					if($model->category == 25 || $model->customization ==1  ) {
						if ($model->getTermsWithoutSumSandU($model->id, 0) != 100) {
							if ($error) {	$error_message .= ' and The sum of the Payment Terms is 100%';} 
							else {	$error_message = 'You can not change this EA\'s status to approved until The sum of the Payment Terms is 100%';
							}
							$error = true;
						}
						if ($model->getTermsSumSandU($model->id, 0) != 100) {
							if($model->category == 25){
									if ($error) {	$error_message .= ' and The sum of the S&U Terms is 100%';} 
									else {	$error_message = 'You can not change this EA\'s status to approved until The sum of the S&U Terms is 100%';
									}
									$error = true;
							}else{
								$yearly_support_flag = Yii::app()->db->createCommand("SELECT sum(payment_term) FROM ea_payment_terms WHERE id_ea=".$model->id." and term_type='sandu'")->queryScalar(); 
									if($yearly_support_flag!=100){
										if ($error) {	$error_message .= ' and The sum of the Support Terms is 100%';} 
										else {	$error_message = 'You can not change this EA\'s status to approved until The sum of the Support Terms is 100%';
										}

										$error = true;
									}	
									
							}
							
						}
					}else {

							if ($model->getTermsSum() != 100) 
							{
								if ($error) {	$error_message .= ' and The sum of the Payment Terms is 100%'; } 
								else {	$error_message = 'You can not change this EA\'s status to approved until The sum of the Payment Terms is 100%';
								}
								$error = true;
							}
					}
				}
					if ($model->getFile(true, true) == null && ($model->getTotalAmount() !=0 || $model->TM==1)){
						if ($error) {	$error_message .= ' and You upload a signed copy of the EA';	}
						else {	$error_message = 'You can not change this EA\'s status to approved until: <br /> You upload a signed copy.';}
						$error = true;	
					}
					if (!empty($model->crmOpp) && $model->crmOpp != null && $model->TM!=1)	{
							$updateOpp = Yii::app()->db->createCommand("select count(*) from  crm_opportunities where opp_id=".$model->crmOpp." and status=1  ")->queryScalar();
							if ($updateOpp<1){
								if ($error) {	$error_message .= ' and the Opportunity of the EA is not closed yet in CRM';		}
								else{	$error_message = 'You can not change this EA\'s status to approved until: <br /> Close its Opportunity in CRM.';							
								}
								$error = true;	
							}
						}else if($model->TM!=1) {
							if ($error) { $error_message .= ' and the CRM Opportunity of the EA is not specified';	}
							else{ $error_message = 'You can not change this EA\'s status to approved until: <br /> The CRM Opportunity of the EA is specified.';							
								}
								$error = true;	
						}
					if($model->category == 24){
								$train_id = Yii::app()->db->createCommand('select id_training from training_eas where id_ea='.$model->id)->queryScalar();
								if($model->getTotalAmount() == 0){
									$customer_exist = Yii::app()->db->createCommand("select id from trainings_free_candidates where id_customer = ".$model->id_customer." and id_training =".$train_id)->queryScalar();
									if($customer_exist ==null){
										$insert_customer = Yii::app()->db->createCommand("insert into trainings_free_candidates (id_customer,id_training) values (".$model->id_customer.",".$train_id.")")->execute();
										if($insert_customer == 0){
											if($error){	$error_message .= ' And Customer is not added as a free candidate to the training';
											}else{	$error_message .= 'Customer is not added as a free candidate to the training';	}
											$error = true;		
										}
									}
								}
							}
					if (!$error){					
						if($model->category == 25) {	$model->AddLicences($model->id, $model->id_customer);	}
						$model->approved = date('Y-m-d H:i:s');	$model->approver= Yii::app()->user->id;
						if(isset($model->project_n) && $model->project_n!='' && $model->project_n!=' '){
							$exist = Yii::app()->db->createCommand("select id from projects where name='".$model->project_n."' ")->queryScalar();
							if (empty($exist)){
								$idp=Projects::createProjectActive($model->id, $model->project_n, Projects::STATUS_ACTIVE, $model->id_customer,$model->category,$model->getTotalManDays(), $model->id_parent_project, $model->TM, $model->template);
								$model->id_project=$idp;
								if ($model->expense == 'Actuals' )	{	Projects::setBillable($idp);	}
						$category_phases = array(26, 27, 28);
						if (in_array($model->category, $category_phases) && !empty($model->id_project)) {
									$phases = Phases::getAllByCategoryTemplate($model->category, $model->template);
									foreach ($phases as $phase)
									{
										$values_phases ='('.$model->id_project.','.$phase['phase_number'].',"'.$phase['phase'].'",'.$phase['id'].')';
										Yii::app()->db->createCommand('INSERT IGNORE INTO projects_phases (id_project,phase_number,description,id_phase) VALUES '.$values_phases)->execute();
										$id_phase = Yii::app()->db->getLastInsertID();
										if ($id_phase == 0) {	$id_phase = ProjectsPhases::getPhaseByProjectId($model->id_project, $phase['id']);	}
										$tasks = Tasks::getAllByPhaseTemplate($phase['id'], $model->template);
										$values_tasks = array();					
										if (!empty($tasks)){
											foreach($tasks as $task)	{
												$values_tasks[] = '('.$id_phase.',"'.$task['billable'].'","'.$task['task'].'")';
											}							
											Yii::app()->db->createCommand('INSERT IGNORE INTO projects_tasks (id_project_phase,billable,description) VALUES '.implode(',',$values_tasks))->execute();
										}
									}
									if ($model->category != 28 && $model->category != 27){
										$milestones = Milestones::getAllMilestonesByCtegoryId($model->category);
										foreach ($milestones as $milestone){
											$values[] ="('".$model->id_project."','".$milestone['id']."','Yes', NOW())";
										}
										Yii::app()->db->createCommand('INSERT IGNORE INTO projects_milestones (id_project, id_milestone, applicable,last_updated) VALUES '.implode(',',$values))->execute();
									}else if($model->category == 27){
										$milestones = Milestones::getAllMilestonesByCtegoryIdTemplate($model->category,$model->template);
										if(!empty($milestones)){
											foreach ($milestones as $milestone){
												$values[] ="('".$model->id_project."','".$milestone['id']."', NOW())";
											}
												Yii::app()->db->createCommand('INSERT IGNORE INTO projects_milestones (id_project, id_milestone, last_updated) VALUES '.implode(',',$values))->execute();
										}
									}
									if ($model->category == 27 && $model->template != 5 && $model->template != 6  && $model->template != 7){
										$checkitems = Checklist::getAllPerTemplate($model->template);
										foreach ($checkitems as $checkitem){
											$stat="Open";
											$values2[] ="(".$model->id_project.",".$checkitem['id'].", ".$checkitem['id_phase'].", '".$stat."' )";
										}
										Yii::app()->db->createCommand('INSERT IGNORE INTO projects_checklist (id_project, id_checklist, id_phase,status) VALUES '.implode(',',$values2))->execute();
									}
								}
								$create = true;
							} else if ($model->id_project!=$exist)
							{
								$model->id_project=$exist;
							}
						}
					}else{
						$extra['error'][] = $error_message; unset($_POST['Eas']['status']);	
					}
				}				
				$model->attributes = $_POST['Eas'];
			}
			if ($model->validate()){
				if ($model->expense == 'Actuals' && $old_expense != $model->expense){		Projects::setBillable($model->id_project);	$model->modifyTerms();}
				if ($model->expense == 'N/A' && $old_expense != $model->expense){		$model->modifyTerms();}
				if ($model->expense != 'N/A' && $model->expense != 'Actuals')	{	$model->expense = $_POST['Eas']['lump_sum'];
					$model->modifyTerms();	}
			}
			if ($model->save()){
				if (!$error){
					$country=Yii::app()->db->createCommand("SELECT country from customers where id=".$model->id_customer." ")->queryScalar(); 
					if($country=='398' ){
						$net_amount = $model->getNetAmountWithExpOffshore("Yes"); 
					}else
					{
						$net_amount = $model->getNetAmountWithExp(); 
					}
					if($net_amount >0 &&$model->status == 2 && $model->category != 454  && $model->TM != 1 && $app!=2 && $initialStat!=2){	self::CreateInvoice($model);	}
					else if ($net_amount == 0 && $model->category != 454 && $model->status == 2  && $model->status != 5 && $model->TM != 1)
					{
						$model->status=5; $model->save();
					}
				}
				if($model->category == '24'){
					if($model->status == 2 && $initialStat!=2){
						Trainings::createTrainingNew($model->description,$model->id_customer,$model->id);
					}
					$train_id = Yii::app()->db->createCommand('select id_training from training_eas where id_ea='.$model->id)->queryScalar();
					$reven = Yii::app()->db->createCommand("select sum(ea_amount) from
															(
															select case 
															WHEN e.currency='9'
															THEN (sum(ei.amount) * ((100 - e.discount)/100)) 
															ELSE (sum((ei.amount)*(select c.rate FROM currency_rate c where c.currency=e.currency order by date limit 1)) *  ((100 - e.discount)/100))
															END
															as ea_amount
															from eas_items ei join eas e on ei.id_ea = e.id
															where e.status=2 and EXISTS (select 1 from training_eas te where id_training = ".$train_id." AND e.id = te.id_ea) group by e.id) as qu")->queryScalar();
							$current_part_num = Yii::app()->db->createCommand("
								select sum(man_days) from eas_items join eas on eas_items.id_ea = eas.id
								where  eas.id in (select id_ea from training_eas where id_training = ".$train_id." and status not in (0,1))")->queryScalar();
								$training_mod = TrainingsNewModule::model()->findByPk($train_id);
								$training_mod->confirmed_participants = $current_part_num ;	$training_mod->revenues = $reven;
								if($training_mod->validate()){	$training_mod->save();	}
				}
				if($model->status == 0 )
				{
					Yii::app()->db->createCommand("Update invoices set status='Cancelled' where id_ea=".$model->id." and status!= 'Paid' ")->execute();
				}
				$f=false;
				$amounts=array();
				if($model->customization ==1 ){
					$f='true';
					$amounts['sandu_amount'] = Utils::formatNumber($model->getTotalSandU());
					$amounts['net_sandu_amount'] = Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU());
					if($model->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($model->getNetAmount()+$model->getTotalSandU())*$model->rate);
						$amounts['total_net_amount_usd'] = Utils::formatNumber($model->getNetAmountWithExp()*$model->rate);		
					}
				}
				echo json_encode(array_merge(array(
						'status' => 'saved',					
						'can_modify' => $model->isEditable(),
						'flag'=> $f,
						'amounts'=> $amounts,
						'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
				), $extra));
				Yii::app()->end();
			}
		}
		$customer_country = Yii::app()->db->createCommand()
            ->select('ck.codelkup')
            ->from('codelkups ck')
            ->join('customers c', 'c.country=ck.id')
            ->join('eas e', 'e.id_customer=c.id')
            ->where('e.id=:id', array(':id'=>$id))
            ->queryRow();
		echo json_encode(array_merge(array(
                        'country_perdiem_id' => $model->country_perdiem,
						'status' => 'success',					
						'can_modify' => $model->isEditable(),
						'html' => $this->renderPartial('_edit_header_content', array('model' => $model,'country_choose' => $customer_country), true, true)
				), $extra));
		Yii::app()->end();
	}	
	public function actionUpdate($id, $new = 0)
	{
		if(!GroupPermissions::checkPermissions('eas-list','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		$error = false;
		if (isset(Yii::app()->session['menu']) && $new == 1) {	Utils::closeTab(Yii::app()->createUrl('eas/create'));	$this->action_menu = Yii::app()->session['menu'];	}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/eas/update/'.$id => array(
						'label'=> 'EA #'.$model->ea_number,
						'url' => array('eas/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => '',
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		if($model->status ==1 && $model->currency!=9)
		{
			$rate= CurrencyRate::getCurrencyRate($model->currency);
			if (isset($rate['rate'])){
				$model->rate = $rate['rate'];
				$model->save();
			}
		}
		
//houda
		Yii::app()->session['menu'] = $this->action_menu;	$model->customer_name = $model->customer->name;
		$model->parent_project = Projects::getNameById($model->id_parent_project); 	$model->project_name = Projects::getNameById($model->id_project);
		if ($model->expense != 'N/A' && $model->expense != 'Actuals'){	$model->lump_sum = $model->expense; }		
		if (count($model->eItems) == 0)	{	$extra['error'][] = 'No EA Items added.';	$error = true; }

		if($model->getTotalAmount() !=0 || $model->TM==1 ){	
					if($model->category == 25 || $model->customization==1 ) {
						if ($model->getTermsWithoutSumSandU($model->id, 0) != 100) {
							$extra['error'][] = 'The sum of the Payment Terms is not 100%';	$error = true;
						}
						if ($model->getTermsSumSandU($model->id, 0) != 100) {
							if($model->category == 25){
								$extra['error'][] = 'The sum of S&U Payment Terms is not 100%';	$error = true;
							}else{
								$yearly_support_flag = Yii::app()->db->createCommand("SELECT sum(payment_term) FROM ea_payment_terms WHERE id_ea=".$model->id." and term_type='sandu'")->queryScalar(); 
									if($yearly_support_flag!=100){
										$extra['error'][] = 'The sum of Support Payment Terms is not 100%';	$error = true;
									}	
									
							}
							
						}
					}else {

							if ($model->getTermsSum() != 100) 
							{
								$extra['error'][] = 'The sum of the Payment Terms is not 100%';	$error = true;
							}
					}
		}
	if (isset($_POST['submitted'])){
			if ($model->isEditable()){
				$model->deleteAllNotes();
				if (isset($_POST['EasNotes']) && is_array($_POST['EasNotes'])){
					foreach ($_POST['EasNotes'] as $id_note){	$notes[] = '('.$model->id.','.(int)$id_note.')';	}
					Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES '.implode(',',$notes))->execute();
				}
				$reasnote = Yii::app()->db->createCommand("SELECT count(1) FROM eas_notes WHERE id_ea=".$model->id." and id_note='228'")->queryScalar();
				if ($reasnote ==0) { Yii::app()->db->createCommand('INSERT INTO eas_notes (id_ea, id_note) VALUES('.$model->id.',228)')->execute(); }
			}
			if (!$error){
				$this->sendNotificationsEmails($model);				
			}
			echo json_encode(array_merge(array('status'=>'saved', 'can_modify' => $model->isEditable()), $extra));
			Yii::app()->end();
		}						
		$this->render('update', array('model' => $model, 'new' => $new, 'can_modify' => $model->isEditable()));
	}
	public function actionView($id)
	{
		$id = (int)$id;	$model = $this->loadModel($id);
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/eas/view/'.$id => array(
						'label'=> 'EA #'.$model->ea_number,
						'url' => array('eas/view', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => '',
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$this->render('view', array('model' => $model));
	}
	private function validateOpp($opp)
	{
		$checkExist = Yii::app()->db->createCommand("select count(1) from crm_opportunities where opp_id=".$opp."  ")->queryScalar();		
		if($checkExist>0){	return true;	}	else	{	return false;	}	 					
	}
	public function actiongeneralUpdate()
	{
		$eas= Yii::app()->db->createCommand("select id from eas where status>1  ")->queryAll();	
		foreach ($eas as $ea) {
				$model= $this->loadModel($ea);		
				$netamountusd= $model->getTotalAmount() * $model->rate;
				$model->netamountusd= $netamountusd;
				$model->save();
			}	
	}
	public function actioncheckconn(){
		$servername = "77.42.154.243:5555";	$username = "sa";	$password = "sql@2008";	$databaseName = "SNS_MSCRM";
		$connect = new mysqli($servername,$username,$password,$databaseName,true);
		if ($connect->connect_error) {
		    die("Connection failed: " . $connect->connect_error);
		} 
		echo "Connected successfully";
	}
	private function sendNotificationsEmails($model)
	{
		if($model->status > 2 )
		{
			$status=2;
		}else{
			$status =$model->status;
		}
		if ($model->TM==1) {				
				$notif = EmailNotifications::getNotificationByUniqueName('ea_'.strtolower(Eas::getStatusLabel($status)).'_tm');
			} else if ($model->category==24) {
				$notif = EmailNotifications::getNotificationByUniqueName('ea_'.strtolower(Eas::getStatusLabel($status)).'_training');
			} else {
				$notif = EmailNotifications::getNotificationByUniqueName('ea_'.strtolower(Eas::getStatusLabel($status)));
			}
    		if ($notif != NULL) {
	    		if 	(EmailNotifications::isNotificationSent($model->id, 'eas', $notif['id']) == false) {
					$category=Eas::getCategory($model->category);	
					$subject = 'EA '.$model->ea_number. ' '.Eas::getStatusLabel($status);
					$to_replace = array(
						'{url}', '{ea_number}','{category}' ,'{description}', 
						'{customer_name}', '{project_name}', '{author}',	
						'{total_amount}', '{discount}', '{total_net_amount}', '{Expense}', '{currency}', '{total_man_days_by_category}',
						'{net_man_day_rate_by_category}'
					);
					
					
					if ($model->TM==1) {
						$terms = Eas::getTermsPaymentwithoutSandU($model->id);
						if(!empty($terms)){
							$milestones='<br /><br />Payment Terms:';
						}else{
							$milestones='';
						}
						foreach ($terms as $term) {
							if($term['term_type'] ==''){
								$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).'';
							}else if(  $model->getTotalSandU()>0){
								$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).' (Yearly Support)';
							}
						}						
						if ($model->category == '28')	{
							$total='';
							if($model->customization == 1 && $model->getTotalSandU()>0)
							{
								$total.= Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU());
								$total.= '<br /><br />Total Yearly Support: '.Utils::formatNumber($model->getTotalSandU());
							}else{
								$total= Utils::formatNumber($model->getTotalAmount());
							}
							$replace = array(
							Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
							$model->ea_number,$category,$model->description, $model->customer->name, 
									$model->project_n ? '<br />Project: '.$model->project_n.'<br /><br /> Parent Project: '.Projects::getNameById($model->id_parent_project).'<br />' : '<br /> Parent Project :'.Projects::getNameById($model->id_parent_project).'<br />',
							$model->eAuthor->fullname, 
							$total,
							Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()),$model->getFormatExpense() ,$model->eCurrency->codelkup,
							$model->getManDaysByCategory(),
							'<b>'.Utils::formatNumber($model->getTMManDayRate()). ' '. $model->eCurrency->codelkup.'</b> '.$milestones);
						}else{

							$total='';
							if($model->customization==1  && $model->getTotalSandU()>0){
								$total.= Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU());
								$total.= '<br /><br />Total Yearly Support: '.Utils::formatNumber($model->getTotalSandU());
							}else{
								$total.=Utils::formatNumber($model->getTotalAmount());
							}

							if($model->category == '27')
							{
								$category.= '<br/><br/>Subtype: '.Eas::getTemplateLabel($model->template);
							}	
							$replace = array(
								Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
								$model->ea_number,$category,$model->description, $model->customer->name, 
								$model->project_n ? '<br />Project: '.$model->project_n.'<br />' : '', 
								$model->eAuthor->fullname, 
								$total,
								Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()),$model->getFormatExpense() ,$model->eCurrency->codelkup,
								$model->getManDaysByCategory(),
								'<b>'.Utils::formatNumber(($model->getTMManDayRate()  )). ' '. $model->eCurrency->codelkup. '</b> '.$milestones);
						}
					 } else { 
					  	if($model->category=='25'){
					  		$terms = Eas::getTermsPaymentwithoutSandU($model->id);
							if(!empty($terms)){
								$milestones='Payment Terms:';
							}else{
								$milestones='';
							}
							foreach ($terms as $term) {
								if($term['term_type'] ==''){
									$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).'';
								}else{
									$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).' (S&U)';
								}
							}

	 					$products = Yii::app()->db->createCommand("select c.codelkup as product from codelkups c, eas_items e where e.settings_codelkup=c.id and e.id_ea='".$model->id."'  ")->queryAll();
	 					$pro='';
	 					foreach ($products as $product) {	$pro.=$product['product'].'/ ';	}
	 					$replace = array(
						Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
						$model->ea_number,$category.'<br/><br/> Product: '.substr($pro, 0, -2).' ' ,
						$model->description, $model->customer->name, 
						$model->project_n ? '<br />Project: '.$model->project_n.'<br />' : '<br />', 
						$model->eAuthor->fullname, 
						'Licenses Qty: '.Utils::formatNumber($model->getTotalManDays()).'<br/><br/> Licenses Amount: '.Utils::formatNumber($model->getTotalAmount()).' '.$model->eCurrency->codelkup.'<br/> <br/> S&U Amount: '.Utils::formatNumber($model->getTotalSandU()).' ' ,
						Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU()),$model->getFormatExpense() ,$model->eCurrency->codelkup,
						$milestones,					 
					 $model->getNetManDayRateByCategory());
					 	} else{
					 		if ($model->category == '28'){
					 			$terms = Eas::getTermsPaymentwithoutSandU($model->id);
								if(!empty($terms)){
									$milestones='<br /><br />Payment Terms:';
								}else{
									$milestones='';
								}
								foreach ($terms as $term) {
									if($term['term_type'] ==''){
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).'';
									}else if(  $model->getTotalSandU()>0){
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).' (Yearly Support)';
									}
								}
								$total='';
								if($model->customization == 1 ){
									$total.= 'Total Amount: '.Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU()).' ';
									if( $model->getTotalSandU()>0)
									{	$total.= '<br /><br />Total Yearly Support: '.Utils::formatNumber($model->getTotalSandU());	}
								}else{
									$total.='Total Amount: '.Utils::formatNumber($model->getTotalAmount()).' ';
								}
					 			 $replace = array(
									Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
									$model->ea_number,$category,$model->description, $model->customer->name, 
									$model->project_n ? '<br />Project: '.$model->project_n.'<br /><br /> Parent Project: '.Projects::getNameById($model->id_parent_project).'<br /><br />' : '<br /> Parent Project :'.Projects::getNameById($model->id_parent_project).'<br /><br />',
									$model->eAuthor->fullname, 
									$total,
									Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()),$model->getFormatExpense() , $model->eCurrency->codelkup,
									$model->getManDaysByCategory(),					 
								 $model->getNetManDayRateByCategory(). ' '. $model->eCurrency->codelkup.' '.$milestones );
					 		}else if ($model->category == '24'){
					 			$terms = Eas::getTermsPaymentwithoutSandU($model->id);
								if(!empty($terms)){
									$milestones='<br /><br />Payment Terms:';
								}else{
									$milestones='';
								}
								foreach ($terms as $term) {
									if($term['term_type'] ==''){
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).'';
									}else{
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).' (S&U)';
									}
								}

								$fields= Yii::app()->db->createCommand("select man_days, md_rate from trainings_new_module where idTrainings=(SELECT id_training FROM training_eas where id_ea=".$model->id.") and type= 640")->queryRow();
								if (!empty($fields))
								{
									$mds='<br/><br/> Man Days: '.$fields['man_days'];
									$mds.='<br/><br/> MD Rate: '.$fields['md_rate'].'$';
								}else{
									$mds='';
								}
								


					 			 $replace = array(
									Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
									$model->ea_number,$category,$model->description, $model->customer->name, 
									$model->project_n ? '<br />Project: '.$model->project_n.'<br /><br />' : '<br /> Parent Project :'.Projects::getNameById($model->id_parent_project).'<br /><br />',
									$model->eAuthor->fullname, 
									'Total Amount: '.Utils::formatNumber($model->getTotalAmount()).' ' ,
									Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()),$model->getFormatExpense() , $model->eCurrency->codelkup,
									$model->getTrainers().' '.$mds.' '.$milestones,					 
								);
					 		}else if($model->category == '454' || $model->category == '496'  || $model->category == '623'){
					 			$terms = Eas::getTermsPaymentwithoutSandU($model->id);
								if(!empty($terms)){
									$milestones='Payment Terms:';
								}else{
									$milestones='';
								}
								foreach ($terms as $term) {
									if($term['term_type'] ==''){
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).'';
									}else{
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).' (S&U)';
									}
								}

								 $replace = array(
									Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
									$model->ea_number,$category,$model->description, $model->customer->name, 
									$model->project_n ? '<br />Project: '.$model->project_n.'<br /><br />' : '<br />', 
									$model->eAuthor->fullname, 
									'Total Amount: '.Utils::formatNumber($model->getTotalAmount()).' ' ,
									Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()),$model->getFormatExpense() , $model->eCurrency->codelkup,
									$milestones,					 
								 $model->getNetManDayRateByCategory());
							}else{
								$total='';
								if( $model->customization==1 && $model->getTotalSandU()>0) {
									$total.= 'Total Amount: '.Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU()).' ';
									$total.= '<br /><br />Total Yearly Support: '.Utils::formatNumber($model->getTotalSandU());
								}else{
									$total.='Total Amount: '.Utils::formatNumber($model->getTotalAmount()).' ';
								}
								$terms = Eas::getTermsPaymentwithoutSandU($model->id);
								if(!empty($terms)){
									$milestones='<br /><br />Payment Terms:';
								}else{
									$milestones='';
								}
								foreach ($terms as $term) {
									if($term['term_type'] ==''){
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).'';
									}else if ( $model->getTotalSandU()>0){
										$milestones.='<br />- '.$term['payment_term'].'% '.Codelkups::getCodelkup($term['milestone']).' (Yearly Support)';
									}
								}
								if($model->category == '27')
								{
									$category.= '<br/><br/>Subtype: '.Eas::getTemplateLabel($model->template);
								}	
								$replace = array(
									Yii::app()->createAbsoluteUrl('eas/update', array('id'=>$model->id)), 
									$model->ea_number,$category,$model->description, $model->customer->name, 
									$model->project_n ? '<br />Project: '.$model->project_n.'<br /><br />' : '<br />', 
									$model->eAuthor->fullname, 
									$total,
									Utils::formatNumber($model->discount).' %', Utils::formatNumber($model->getNetAmount()),$model->getFormatExpense() , $model->eCurrency->codelkup,
									$model->getManDaysByCategory(),					 
								 $model->getNetManDayRateByCategory(). ' '. $model->eCurrency->codelkup.' '.$milestones);
							}
						}
					}
					$body = str_replace($to_replace, $replace, $notif['message']);
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					foreach($emails as $email) 
					{
						if (!empty($email))
							Yii::app()->mailer->AddAddress($email);
					}	
						//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
					 Yii::app()->mailer->Subject  = $subject;	Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true))	{
						EmailNotifications::saveSentNotification($model->id, 'eas', $notif['id']);
					}
    			} 
    		}
	}	
	public function sendEAwithSandU($model){
					$notif = EmailNotifications::getNotificationByUniqueName('ea_approved_sandu');
					$body='';
					if($model->category == 25)
					{
						$subject = 'EA '.$model->ea_number. ' Includes S&U Fees';	
						$body='Dear Senior Admin Team,<br/> <br/>Please note that EA# <b>'.$model->ea_number.'</b> is approved and includes S&U fees.<br/> <br/>Best Regards,<br/>SNSit';
					}else{
						$subject = 'EA '.$model->ea_number. ' Includes Yearly Support Fees';	
						$body='Dear Senior Admin Team,<br/> <br/>Please note that EA# <b>'.$model->ea_number.'</b> is approved and includes Yearly Support fees.<br/> <br/>Best Regards,<br/>SNSit';
					}
					
    				if ($notif != NULL) 
    				{
    					$to_replace = array(
						'{ea}'
							);
    						$replace = array(	$body
							);
						$body = str_replace($to_replace, $replace, $notif['message']);					
						$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
						Yii::app()->mailer->ClearAddresses();
						foreach($emails as $email) 
						{
							if (!empty($email))
								Yii::app()->mailer->AddAddress($email);
						}					
						Yii::app()->mailer->Subject  = $subject;
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
						Yii::app()->mailer->Send(true);
				}
	}
	public function sendEACustomization($model){
			$maintenance_customization = Yii::app()->db->createCommand("select count(1) from maintenance where customer='".$model->id_customer."' and `status`='active' and support_service='545'")->queryScalar();
			if($maintenance_customization>0){
					$notif = EmailNotifications::getNotificationByUniqueName('eas_customization');
					$subject = 'EA '.$model->ea_number. ' Includes Customization';
	   				if ($notif != NULL) 
    				{	$to_replace = array(
						'{ea}',
						'{author}');
    					$replace = array(						
							$model->ea_number,
							Users::getNameById($model->author)	);
					$body = str_replace($to_replace, $replace, $notif['message']);	$author=UserPersonalDetails::getEmailById($model->author);
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	Yii::app()->mailer->ClearAddresses();
					Yii::app()->mailer->AddAddress($author);
					foreach($emails as $email) {
						if (!empty($email))
						{//	Yii::app()->mailer->AddCcs($email);
							Yii::app()->mailer->AddAddress($email);
						}
					}					
					Yii::app()->mailer->Subject  = $subject;	Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);
						}
					}
	}
	public function actionManageItem($id = NULL)
    {
    	$new =false;
    	if (!isset($_POST['id_ea']))
    		exit;

    	$ea = $this->loadModel($_POST['id_ea']);	

    	if ($id == NULL) {	
    		$new=true;	$model = new EasItems();	$model->id_ea = (int)$_POST['id_ea'];
    		if($ea->category ==25 )
    		{
    			$model->sandu= SupportRate::getRate(1337);
    		}
    	} else {
    		$id = (int)$id;	$model = EasItems::model()->findByPk($id);
    	}    	
    	switch ($ea->category)
    	{
    		case 24:
    			$model->settings_codelist = 'training_course';
    			break;
			case 25:
				$model->settings_codelist = 'product';
				break;
			case 26:
				$model->settings_codelist = 'consultancy';
				break;
			case 27:
				$model->settings_codelist = 'product';
				break;
			case 28:
				$model->settings_codelist = '';
				break;
			case 454:
				$model->settings_codelist = 'service_support';
				break;
			case 496:
				$model->settings_codelist = 'labels';
				break;
			case 623:
				$model->settings_codelist = 'Recruitment';
				break;
    	}    	
    	if (isset($_POST['EasItems']))	{
			if (isset($_POST['EasItems']['man_day_rate_n'])){ 
				$model->man_day_rate_n=$_POST['EasItems']['man_day_rate_n'] ; 
			}			
    		if ($id == NULL){
    			$model->attributes = $_POST['EasItems']['new'];	$model->amount=preg_replace('/[^0-9.]/', '', $model->amount); 
    		} else {
    			$model->attributes = $_POST['EasItems'][$id];	$model->amount=preg_replace('/[^0-9.]/', '', $model->amount);				 
    		}			
    		$model->description = nl2br($model->description);
    		if ($ea->category != 28 && $ea->category != 623 && $ea->category != 24  ){
				if (empty($model->settings_codelkup)){
					$model->addCustomError('settings_codelkup', $model->settingsCodelist->label.' cannot be blank');
				}
			}
			if($ea->category == 24){
				$model->settings_codelkup = $_POST['training_course'];
				if(empty($model->man_days) || $model->man_days==0){
					$model->addCustomError('man_days',' Number of Participants cannot be blank or 0');
				}
			}
			if($ea->category == 454)
			{	if(isset($model->man_day_rate_n) && $model->man_day_rate_n>0)	{ $model->man_days = $model->man_day_rate_n;}else{ $model->man_days =1; }	}
			if($ea->category == 496 || $ea->category == 623  ){
				$model->man_days =1;
			}
			if($model->ea->TM == 1 && empty($model->man_days) || $model->man_days==0){
					$model->addCustomError('man_days',' Estimated Man Days cannot be blank or 0');
			}
   			if ($model->save()) {	
				
   				$ea->modifyTerms();
   				if ($model->man_day_rate_n==0){
	   				$amounts['total_amount'] = Utils::formatNumber($ea->getTotalAmount());
					$amounts['net_amount'] = Utils::formatNumber($ea->getNetAmount());
					$amounts['net_man_day_rate'] = Utils::formatNumber($ea->getNetManDayRate());
					$amounts['man_day_rate'] = Utils::formatNumber($ea->getManDayRate());
					$amounts['total_man_days'] = Utils::formatNumber($ea->getTotalManDays());
					$amounts['total_net_amount'] = Utils::formatNumber($ea->getNetAmountWithExp());
					if($ea->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($ea->getNetAmount()+$ea->getTotalSandU())*$ea->rate);
						$amounts['net_man_day_rate_usd'] = Utils::formatNumber($ea->getNetManDayRate()*$ea->rate);	
						$amounts['total_net_amount_usd'] = Utils::formatNumber($ea->getNetAmountWithExp()*$ea->rate);		
					}
				} 				
				else if($ea->category!=454 && $ea->category!=496 && $ea->category!=623 && $ea->category!=24){
					$amounts['total_amount'] = Utils::formatNumber($ea->getTotalAmount());
					$amounts['net_amount'] = Utils::formatNumber($ea->getNetAmount());
					$amounts['net_man_day_rate'] = Utils::formatNumber($ea->getNetTMManDayRate());
					$amounts['man_day_rate'] = Utils::formatNumber($ea->getTMManDayRate());
					$amounts['total_man_days'] = Utils::formatNumber($ea->getTotalManDays());
					$amounts['total_net_amount'] = Utils::formatNumber($ea->getNetAmountWithExp());

					if($ea->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($ea->getNetAmount()+$ea->getTotalSandU())*$ea->rate);
						$amounts['net_man_day_rate_usd'] = Utils::formatNumber($ea->getNetManDayRate()*$ea->rate);	
						$amounts['total_net_amount_usd'] = Utils::formatNumber($ea->getNetAmountWithExp()*$ea->rate);		
					}
				}else {
				$amounts['total_amount'] = Utils::formatNumber($ea->getTotalAmount());
				$amounts['net_amount'] = Utils::formatNumber($ea->getNetAmount());
				$amounts['total_net_amount'] = Utils::formatNumber($ea->getNetAmountWithExp());

					if($ea->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($ea->getNetAmount()+$ea->getTotalSandU())*$ea->rate);
						$amounts['total_net_amount_usd'] = Utils::formatNumber($ea->getNetAmountWithExp()*$ea->rate);		
					}
				}
				if($ea->category==25 ||  $ea->customization==1){
					$amounts['sandu_amount'] = Utils::formatNumber($ea->getTotalSandU());
					$amounts['net_sandu_amount'] = Utils::formatNumber($ea->getNetAmount()+$ea->getTotalSandU());
				}
				$currency=$ea->eCurrency->id;
				$rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$currency'")->queryScalar();
				$ea->netamountusd = ($ea->getNetAmount()/$rate);
                $ea->netmandayrateusd = ($ea->getNetManDayRate()/$rate);
                $ea->save();	
				echo json_encode(array('status' => 'saved', 'amounts' => $amounts));
				exit;
			}else{
				print_r($model->getErrors());exit;
			}
    	}    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form', array(
            	'model'=> $model,
    			'labels' => Eas::getItemsLabelsByCategory($ea->category),
        	), true, true)));
       exit;
    }
	public function actionDeleteItem($id)
	{
		$item = EasItems::model()->findByPk($id);
		if ($item===null)
			throw new CHttpException(404,'The requested page does not exist.');
		if ($item->delete()) {
			$ea = $item->ea;	$ea->modifyTerms();
			$amounts['total_amount'] = Utils::formatNumber($ea->getTotalAmount());
			$amounts['net_amount'] = Utils::formatNumber($ea->getNetAmount());
			$amounts['net_man_day_rate'] = Utils::formatNumber($ea->getNetManDayRate());
			$amounts['man_day_rate'] = Utils::formatNumber($ea->getManDayRate());
			$amounts['total_man_days'] = Utils::formatNumber($ea->getTotalManDays());
			$amounts['total_net_amount'] = Utils::formatNumber($ea->getNetAmountWithExp());		

			if($ea->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($ea->getNetAmount()+$ea->getTotalSandU())*$ea->rate);
						$amounts['net_man_day_rate_usd'] = Utils::formatNumber($ea->getNetManDayRate()*$ea->rate);	
						$amounts['total_net_amount_usd'] = Utils::formatNumber($ea->getNetAmountWithExp()*$ea->rate);		
			}

			if($ea->category==25 || $ea->customization==1){
				$amounts['sandu_amount'] = Utils::formatNumber($ea->getTotalSandU());
				$amounts['net_sandu_amount'] = Utils::formatNumber($ea->getNetAmount()+$ea->getTotalSandU());
			}

			echo json_encode(array('status' => 'success', 'amounts' => $amounts));
		} 
	}	
	public function actionmanageTermSandUSec($id = NULL)
    {
    	if ($id == NULL) {	$model = new EaPaymentTerms();	} 
    	
    	else {
    		$id = (int)$id;	$model = EaPaymentTerms::model()->findByPk($id);	$model->milestone = Codelkups::getCodelkup($model->milestone);
    	}   
    	$m=''; 	
    	if (isset($_POST['EaPaymentTerms']))
    	{
    		if ($id == NULL) {
    			$model->attributes = $_POST['EaPaymentTerms']['new'];	$milestone = $_POST['EaPaymentTerms']['new']['milestone'];
    		} else {
    			$model->attributes = $_POST['EaPaymentTerms'][$id];	$milestone = $_POST['EaPaymentTerms'][$id]['milestone'];
    		}
    		if($milestone != null){
    			$m= $milestone;
	    		$id_codelist = Codelists::getIdByCodelist('ea_milestone');
	    		$rez = Yii::app()->db->createCommand("SELECT id FROM codelkups WHERE id_codelist = '$id_codelist' AND codelkup = '$milestone' LIMIT 1")->queryScalar();
	    		if($rez == false){
	    			$id_milestone = Codelkups::insertValueCustom($id_codelist,$milestone);
	    			$model->milestone = $id_milestone;
	    		}else{
	    			$model->milestone = $rez;
	    		}	    		
    		}
    		$model->payment_term = (float) $model->payment_term;	$sum = $model->ea->getTermsSumSandU($model->id_ea, $model->id);				 
			if (($sum + $model->payment_term) > 100){
    			$model->addCustomError('payment_term', 'S&U terms (%) bigger than 100%');
    		}    		    		
   			if ($model->validate()) {
   				$model->amount = ($model->payment_term / 100) * $model->ea->getTotalSandU();	$model->term_type="sandu";
   				if ($model->save())
   				{
					echo json_encode(array('status' => 'saved'));
					exit;
   				}
			}
    	}  
    	if($m != '')
    	{
    		$model->milestone= $m;  
    	}  	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_term_sandu_formSecond', array(
            	'model'=> $model,
        	), true, true)));
       exit;
    }
	public function actionmanageTermSandU($id = NULL)
    {
    	if ($id == NULL){	$model = new EaPaymentTerms();} 
    	else {	$id = (int)$id;	$model = EaPaymentTerms::model()->findByPk($id); $model->milestone = Codelkups::getCodelkup($model->milestone);	}
    	$m='';
    	if (isset($_POST['EaPaymentTerms'])){
    		if ($id == NULL){	$model->attributes = $_POST['EaPaymentTerms']['new'];	$milestone = $_POST['EaPaymentTerms']['new']['milestone'];	} 
    		else {	$model->attributes = $_POST['EaPaymentTerms'][$id];	$milestone = $_POST['EaPaymentTerms'][$id]['milestone'];	}
    		if($milestone != null){
    			$m= $milestone;
	    		$id_codelist = Codelists::getIdByCodelist('ea_milestone');
	    		$rez = Yii::app()->db->createCommand("SELECT id FROM codelkups WHERE id_codelist = '$id_codelist' AND codelkup = '$milestone' LIMIT 1")->queryScalar();
	    		if($rez == false){
	    			$id_milestone = Codelkups::insertValueCustom($id_codelist,$milestone);	$model->milestone = $id_milestone;
	    		}else{	$model->milestone = $rez;	}	    		
    		}
    		$model->payment_term = (float) $model->payment_term;	$sum = $model->ea->getTermsSumSandU($model->id_ea, $model->id);	
			if (($sum + $model->payment_term) > 100){	$model->addCustomError('payment_term', 'S&U terms (%) bigger than 100%');}
   			if ($model->validate()) {
   				$model->amount = ($model->payment_term / 100) * $model->ea->getTotalSandU();	$model->term_type="sandu";
   				if ($model->save()){
					echo json_encode(array('status' => 'saved'));
					exit;
   				}
			}
    	}    	
    	if($m != '')
    	{
    		$model->milestone= $m;  
    	} 
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_term_sandu_form', array(
            	'model'=> $model,
        	), true, true)));
       exit;
    }
    public function actionmanageTermSecond($id = NULL)
    {
    	if ($id == NULL) {	$model = new EaPaymentTerms();	} 
    	else	{	$id = (int)$id;	$model = EaPaymentTerms::model()->findByPk($id);	$model->milestone = Codelkups::getCodelkup($model->milestone);
    	}  
    	$m='';  	
    	if (isset($_POST['EaPaymentTerms'])){
    		if ($id == NULL) {
    			$model->attributes = $_POST['EaPaymentTerms']['new'];	$milestone = $_POST['EaPaymentTerms']['new']['milestone'];} 
    		else {	$model->attributes = $_POST['EaPaymentTerms'][$id];	$milestone = $_POST['EaPaymentTerms'][$id]['milestone']; }
    		if($milestone != null){
    			$m= $milestone;
	    		$id_codelist = Codelists::getIdByCodelist('ea_milestone');
	    		$rez = Yii::app()->db->createCommand("SELECT id FROM codelkups WHERE id_codelist = '$id_codelist' AND codelkup = '$milestone' LIMIT 1")->queryScalar();
	    		if($rez == false){
	    			$id_milestone = Codelkups::insertValueCustom($id_codelist,$milestone);
	    			$model->milestone = $id_milestone;
	    		}else{	$model->milestone = $rez;	}	    		
    		}
    		$model->payment_term = (float) $model->payment_term;	$sum = $model->ea->getTermsWithoutSumSandU($model->id_ea,$model->id);			 
			if (($sum + $model->payment_term) > 100){	$model->addCustomError('payment_term', 'The total sum of payment terms is bigger than 100%');	}    		    		
   			if ($model->validate()) {
   				$model->amount = ($model->payment_term / 100) * $model->ea->getNetAmountWithExp();
   				if ($model->save()){
					echo json_encode(array('status' => 'saved'));
					exit;
   				}
			}
    	}    
    	if($m != '')
    	{
    		$model->milestone= $m;  
    	}	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_term_formSecond', array(
            	'model'=> $model,
        	), true, true)));
       exit;
    }
	public function actionManageTerm($id = NULL)
    {
    	if ($id == NULL) {	$model = new EaPaymentTerms();	} 	else	{	$id = (int)$id;
    		$model = EaPaymentTerms::model()->findByPk($id);
    		$model->milestone = Codelkups::getCodelkup($model->milestone);	}    	
    		$m='';
    	if (isset($_POST['EaPaymentTerms'])){
    		if ($id == NULL) {
    			$model->attributes = $_POST['EaPaymentTerms']['new'];	$milestone = $_POST['EaPaymentTerms']['new']['milestone'];
    		} else 	{
    			$model->attributes = $_POST['EaPaymentTerms'][$id];	$milestone = $_POST['EaPaymentTerms'][$id]['milestone'];
    		}
    		if($milestone != null)	{
    			$m=$milestone;
	    		$id_codelist = Codelists::getIdByCodelist('ea_milestone');
	    		$rez = Yii::app()->db->createCommand("SELECT id FROM codelkups WHERE id_codelist = '$id_codelist' AND codelkup = '$milestone' LIMIT 1")->queryScalar();
	    		if($rez == false)	{
	    			$id_milestone = Codelkups::insertValueCustom($id_codelist,$milestone);	$model->milestone = $id_milestone;
	    		}else{	$model->milestone = $rez;	}	    		
    		}
    		$model->payment_term = (float) $model->payment_term;	$sum = $model->ea->getTermsWithoutSumSandU($model->id_ea,$model->id);				 
			if (($sum + $model->payment_term) > 100){
    			$model->addCustomError('payment_term', 'The total sum of payment terms is bigger than 100%');
    		}    		    		
   			if ($model->validate()) {
   				$model->amount = ($model->payment_term / 100) * $model->ea->getNetAmountWithExp();
   				if ($model->save()){
					echo json_encode(array('status' => 'saved'));
					exit;
   				}
			}
    	} 
    	if($m != '')
    	{
    		$model->milestone= $m;  
    	}
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_term_form', array(
            	'model'=> $model,
        	), true, true)));
       exit;
    }    
    public function actionManageNote($id = NULL){	
    	if(isset($_POST['id_ea'])){
    		$id_ea=$_POST['id_ea'];
    	}
    	if ($id == NULL) {
    		$model = new EasNotes();
    	} else {	$id = (int)$id;	$model = EasNotes::model()->findByPk($id);	}    	
    	if (isset($_POST['EasNotes'])){	
    		$note=''; $id_ea='';
    		if(isset($_POST['EasNotes']['new']['id_note']) && isset($_POST['EasNotes']['new']['id_ea']))
    		{	$note=$_POST['EasNotes']['new']['id_note'];	$id_ea=$_POST['EasNotes']['new']['id_ea']; }
    		 if(!empty($note) && !empty($id_ea)){
    		 	Yii::app()->db->createCommand('INSERT INTO codelkups (id_codelist, codelkup, custom) VALUES(9,"'.$note.'" ,1)')->execute(); 
    		 	$getCode= Yii::app()->db->createCommand("SELECT id FROM codelkups WHERE id_codelist=9 and codelkup='".$note."' limit 1")->queryScalar();
    		 	Yii::app()->db->createCommand("INSERT INTO eas_specific_notes (codelkup, id_ea) VALUES(".$getCode.",".$id_ea.")")->execute(); 
    		}
    		echo json_encode(array('status'=>'saved', 'noteform'=>$this->renderPartial('_note_form', array(
            	'model'=> $model,'id_ea'=>$id_ea,	), true, true)));
     		exit;
    	}    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,);		
    	echo json_encode(array('status'=>'success', 'noteform'=>$this->renderPartial('_note_form', array(
            	'model'=> $model,'id_ea'=>$id_ea,
        	), true, true)));
       exit;
    }
	public function actionDeleteTerm($id){
		$item = EaPaymentTerms::model()->findByPk($id);
		if ($item===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$item->delete(); 
	}
	public function actionDelete($id){
		if(!GroupPermissions::checkPermissions('eas-list','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$model = $this->loadModel($id);		
		if (in_array($model->status, array(Eas::STATUS_APPROVED, Eas::STATUS_INVOICED))){
			echo json_encode(array('status' => 'error', 'Can\'t delete an EA with status Approved or Invoiced'));
			exit;
		}
		$dirPath = dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$model->id_customer.'/eas/';
		Utils::deleteSearchFile($dirPath, 'EA_'.$model->ea_number);
		if ($model->file){
			Utils::deleteSearchFile($dirPath, $model->file);
		}
		if ($model->category != 24 && $model->category != 25){
		$pid=$model->id_project;		
		Yii::app()->db->createCommand("DELETE from projects where id='".$pid."' ")->execute(); }
		$model->delete();		
		exit;
	}	
	public function actionSaveDiscount($id){
		$model = $this->loadModel($id);		
		if (isset($_POST['Eas']['discount']) && (!in_array($model->status, array(Eas::STATUS_APPROVED, Eas::STATUS_INVOICED)))){
			$model->discount = (float)$_POST['Eas']['discount'];
			$currency=$model->eCurrency->id;
			$rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$currency'")->queryScalar();		
			$model->netamountusd = ($model->getNetAmount()*$rate);
            $model->netmandayrateusd = ($model->getNetManDayRate()*$rate);
			if ($model->save()){
				$model->modifyTerms();				
				$amounts['net_amount'] = Utils::formatNumber($model->getNetAmount());
				if ($model->TM !=1){
				$amounts['net_man_day_rate'] = Utils::formatNumber($model->getNetManDayRate());
				}else {
				$amounts['net_man_day_rate'] = Utils::formatNumber($model->getNetTMManDayRate());
				}				
				$amounts['total_net_amount'] = Utils::formatNumber($model->getNetAmountWithExp());

				if($model->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($model->getNetAmount()+$model->getTotalSandU())*$model->rate);
						$amounts['net_man_day_rate_usd'] = Utils::formatNumber($model->getNetManDayRate()*$model->rate);	
						$amounts['total_net_amount_usd'] = Utils::formatNumber($model->getNetAmountWithExp()*$model->rate);		
					}

				if($model->category==25 || $model->customization==1){
				$amounts['sandu_amount'] = Utils::formatNumber($model->getTotalSandU());
				$amounts['net_sandu_amount'] = Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU());
				}


				echo json_encode(array('status' => 'saved', 'amounts' => $amounts));
			}
		}
		exit;
	}
	public function actionSaveNetAmount($id){
		$model = $this->loadModel($id);
		if (isset($_POST['val']) && (!in_array($model->status, array(Eas::STATUS_APPROVED, Eas::STATUS_INVOICED))))
		{
			$value = $_POST['val'];
			//$model->discount = round(((float)((($model->getTotalAmount()- $value) * 100)/ $model->getTotalAmount())),2); 
			$model->discount = (((float)((($model->getTotalAmount()- $value) * 100)/ $model->getTotalAmount()))); 
			$currency=$model->eCurrency->id;
			$rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$currency'")->queryScalar();
            $model->netamountusd = ($model->getNetAmount()*$rate);
            $model->netmandayrateusd = ($model->getNetManDayRate()*$rate);
			if ($model->save()){
				$model->modifyTerms();				
				$amounts['discountinput'] = $model->discount;
				if ($model->TM !=1){
				$amounts['net_man_day_rate'] = Utils::formatNumber($model->getNetManDayRate());
				}else {
				$amounts['net_man_day_rate'] = Utils::formatNumber($model->getNetTMManDayRate());
				}				
				$amounts['total_net_amount'] = Utils::formatNumber($model->getNetAmountWithExp());
				if($model->currency !=9 ){
						$amounts['net_amount_usd'] = Utils::formatNumber(($model->getNetAmount()+$model->getTotalSandU())*$model->rate);
						$amounts['net_man_day_rate_usd'] = Utils::formatNumber($model->getNetManDayRate()*$model->rate);	
						$amounts['total_net_amount_usd'] = Utils::formatNumber($model->getNetAmountWithExp()*$model->rate);		
					}
				if($model->category==25 ||  $model->customization==1){
				$amounts['sandu_amount'] = Utils::formatNumber($model->getTotalSandU());
				$amounts['net_sandu_amount'] = Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU());
				}

				echo json_encode(array('status' => 'saved', 'amounts' => $amounts));
			}
		}
		exit;	 	
	}
	public function actionPrint($id) {
		$model = $this->loadModel((int) $id);
		$this->generatePdf('eas', $id); 
		$file = $model->getFile();	
		if ($file !== null) 
		{
			header('Content-disposition: attachment; filename=EA_'.$model->ea_number.'.pdf');
			header('Content-type: application/pdf');
			//chmod($file, 0777);
			readfile(str_ireplace('\\','/',$file));
			Yii::app()->end();
		} else{
			$this->redirect(array(Utils::getMenuOrder(true)));
		}
	}	
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('eas-list')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['Eas']) ? $_GET['Eas'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge( 
			$this->action_menu,
			array(
				'/eas/index' => array(
					'label'=>Yii::t('translations','EAs'),
					'url' => array('eas/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;			
		$model = new Eas('search');
		$model->unsetAttributes();  
		if (isset($_GET['Eas'])){
			$model->attributes = $_GET['Eas'];
		}
		if (isset($_GET['Eas']['category'])){
			$model->category=$_GET['Eas']['category'];
		}
		if (isset($_GET['Eas']['status'])){
			$model->status=$_GET['Eas']['status'];
		}		
		$model->attributes= $searchArray;		
		$this->render('index',array(
			'model'=>$model,));
	}
	public function loadModel($id){
		$model = Eas::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($array){
		$errors = array();
		if (isset($_POST['ajax']) && $_POST['ajax']==='eas-form')
		{
			$result = array();
			foreach ($array as $key=> $model) {
				if (is_array($model)) {
					$result = CCustomActiveForm::validateTabular($model, null, true, false);
					$errors = array_merge($errors, $result);
					unset($array[$key]);
				}
			}			
			$errors = array_merge(CCustomActiveForm::validate($array, null, true, false), $errors);
			if (empty($errors)) 
				echo json_encode(array('status'=>'success'));
			else 
				echo json_encode(array('status'=>'failure', 'errors' => $errors));
			Yii::app()->end();
		}
	}
	public function actionDeleteUpload(){
		if (isset($_GET['model_id'], $_GET['file']))
		{
			$id = (int)$_GET['model_id'];
			if (isset($_GET['id_customer'])){
				$customer = (int)$_GET['id_customer'];
			}else{
				$customer = (int) Yii::app()->db->createCommand("SELECT id_customer FROM eas WHERE id = $id")->queryScalar();	
			}
			$filepath = Eas::getDirPath($customer, $id).$_GET['file'];
			$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
			if ($success){
				$query = "UPDATE `eas` SET file='' WHERE id='$id'";
				Yii::app()->db->createCommand($query)->execute();
			}
		}
	}
	public function actionDeleteUploadSheet(){
		if (isset($_GET['model_id'], $_GET['file']))
		{
			$id = (int)$_GET['model_id'];
			if (isset($_GET['id_customer'])){
				$customer = (int)$_GET['id_customer'];
			}else {
				$customer = (int) Yii::app()->db->createCommand("SELECT id_customer FROM eas WHERE id = $id")->queryScalar();	
			}
			$filepath = Eas::getDirPathSheet($customer, $id).$_GET['file'];
			$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
			if ($success){
				$query = "UPDATE `eas` SET file='' WHERE id='$id'";
				Yii::app()->db->createCommand($query)->execute();
			}
		}
	}
	public function actioncheckDuration(){
		if(isset($_POST['course'])){
			 $course=$_POST['course']; 
			 $id=$_POST['id'];			
			$id_codelist = Codelists::getIdByCodelist('training_course');
	    	$dur = Yii::app()->db->createCommand("SELECT comments FROM codelkups WHERE id_codelist = '$id_codelist' AND id = '$course' LIMIT 1")->queryScalar();
	    	$duration="  Duration:".$dur." Days";
	    	echo $duration; exit;
	    	$query="update eas_items set description= CONCAT(description,'".$duration."') where id ='".$id."' "; 
	    	Yii::app()->db->createCommand($query)->execute();			
			echo json_encode(array(
						'status' => 'success',					
						'duration' => $duration,	));
		}
	}
	public function createInvoice($eas){ 	 
		$payment_terms = $eas->getTermsPaymentwithoutSU();
		$payment_number = count($payment_terms);	$i = 1;	$title_name = ""; $titleaust="";
		foreach ($payment_terms as $payment ){
			$title_name = "";	$model = new Invoices();	$model->invoice_number = "00000";
			$model->id_customer = $eas->id_customer;	$model->project_name = $eas->description;
			if($eas->id_project == null)
				$model->id_project = 0;
			else
				$model->id_project = $eas->id_project;
			$cardinal_number = Eas::cardinalNumber($i);			
			if($payment_number == 1){
				$title = $eas->description." - ". $payment['payment_term'] ."% Payment - ".Codelkups::getCodelkup($payment['milestone']);
			}else{
				$title = $eas->description." - ".$cardinal_number." ". $payment['payment_term'] ."% Payment - ".Codelkups::getCodelkup($payment['milestone']);
			}	
			$region=Yii::app()->db->createCommand("SELECT region from customers where id=".$eas->id_customer." ")->queryScalar(); 
			$country=Yii::app()->db->createCommand("SELECT country from customers where id=".$eas->id_customer." ")->queryScalar(); 
			$model->sns_share = 100;
			if($country=='398' ){
				$net_amount = $eas->getNetAmountWithExpOffshore("Yes"); 
				$titleaust= $title;
				$title.= ' - Offshore = Yes '; 
			}else
			{
				$net_amount = $eas->getNetAmountWithExp(); 
			}

			$assigneduser= Yii::app()->db->createCommand("SELECT id_assigned from customers where id= ".$model->id_customer." ")->queryScalar();
			if ( !empty($assigneduser) && $assigneduser!= 0){
				$model->id_assigned=$assigneduser;
				$updateinvoices = Yii::app()->db->createCommand("UPDATE `invoices` SET id_assigned='".$assigneduser."' WHERE id_customer='".$model->id_customer."' and (id_assigned is null or id_assigned=0) ")->execute();
			}

			$model->invoice_title = $title;
			$model->id_ea = $eas->id;
			$model->payment = $i++.'/'.$payment_number;
			
			if($region=='59'){
				if($eas->category=='27' || $eas->category=='28'){
						if($country=='113' || $country=='115'){
								$model->partner= Maintenance::PARTNER_SNS;
						}else {
								 $model->partner= '79' ; 
								 $model->sns_share = 80;
								 $model->partner_status='Not Paid';
								  }
			 }	else{
				 	$model->partner= Maintenance::PARTNER_SNS;
				 }
			}elseif($region=='63') {
				{
					$model->partner= '201'; 
				}
				$model->sns_share = 80; 
				$model->partner_status='Not Paid';
			}else{
				$model->partner= Maintenance::PARTNER_SNSI;
				$model->sns_share = 80;
				$model->partner_status='Not Paid';
			}	
			if($eas->category == '24'){
				$model->status = Invoices::STATUS_TO_PRINT;
			}
			$model->payment_procente = $payment['payment_term'];				
			$model->amount = $net_amount;
			$model->net_amount = $net_amount*($model->sns_share/100)*($payment['payment_term']/100);
			$model->gross_amount = $net_amount*($payment['payment_term']/100);
			$model->partner_amount = $model->gross_amount - $model->net_amount;
			$model->sold_by = "";
			$model->type = "Standard";
			$model->currency = $eas->currency;
			if($model->save()){
				$model->invoice_number = Utils::paddingCode($model->id);
				if($model->invoice_number == "99999")
					$model->invoice_number = "00000";
				$model->save();
				if($country=='398' ){
					$net_amountAust = $eas->getNetAmountWithExpOffshore("No"); 
					if($net_amountAust>0)
					{
						self::createInvoiceAUST($eas->id_customer,$eas->description, $titleaust , $eas->id_project, $eas->id,$model->payment, $eas->currency,$payment['payment_term'],$net_amountAust);				
					}
				}
			}
		}
		return true;
	}
	public function createInvoiceAUST($customer, $descr, $title , $project, $ea, $payment, $currency,$procente,$net_amount){
    	$model = new Invoices();	$model->invoice_number = "00000";
    	$title.= ' - Offshore = No '; 
    	$model->invoice_title = $title;
		$model->id_ea = $ea;
		$model->payment = $payment;
		$model->id_customer = $customer;	$model->project_name = $descr;
			if($project == null)
			{	$model->id_project = 0;	}
			else
			{	$model->id_project = $project;	}
		$model->partner= '1218' ; 
		$model->sns_share = 40;
		$model->partner_status='Not Paid';
		$model->payment_procente= $procente;	
		$model->amount = $net_amount;
		$model->net_amount = $net_amount*($model->sns_share/100)*($procente/100);
		$model->gross_amount = $net_amount*($procente/100);
		$model->partner_amount = $model->gross_amount - $model->net_amount;
		$model->sold_by = "";
		$model->type = "Standard";
		$model->currency = $currency;

	
		if($model->save()){
			$model->invoice_number = Utils::paddingCode($model->id);
			if($model->invoice_number == "99999")
			{	$model->invoice_number = "00000";	}
			$model->save();
		}
    } 
	public function actionSendTMEmailNotification(){
		$notif = EmailNotifications::getNotificationByUniqueName('ea_tm_alert');
		if ($notif != NULL) 
    	{
    		$all_TM=Yii::app()->db->createCommand("select distinct e.ea_number as number,p.`name` as pname,p.id as pid,c.`name` as cname ,p.project_manager, MONTH(CURRENT_DATE()-INTERVAL 1 MONTH) as mon,YEAR(CURRENT_DATE()-INTERVAL 1 MONTH) as yearr, CURRENT_DATE()-INTERVAL 1 MONTH  from projects_phases pp ,eas e,projects_tasks pt,user_task ut ,user_time utime,projects p,customers c where pt.id_project_phase=pp.id and pp.id_project=e.id_project and e.TM=1 and e.`status`>=2 and ut.id_task=pt.id and utime.id_task=pt.id and utime.amount>0 and p.id=e.id_project and c.id=p.customer_id and DATEDIFF(NOW(),utime.date)<30 order by p.name")->queryAll();
    		$month=Yii::app()->db->createCommand("SELECT MONTHNAME(DATE_ADD(CURDATE(), INTERVAL -1 MONTH))")->queryScalar();
    		$email_body="Dear Admin Team, <br/> <br/> Below is a list of T&M EAs which have at least one time entry during the month of ".$month.":<br /><br />";
			foreach($all_TM as $TM){
    			$ea_number=$TM['number'];
    			$pname = $TM['pname'];
    			$pid= $TM['pid'];
    			$mon= $TM['mon'];
    			$year= $TM['yearr'];
    			$cname = $TM['cname'];
    			$email_body.="     ".$cname."<br />".$pname." <br/>  EA# ".$ea_number."<br /> PM: ".Users::getNameById($TM['project_manager'])."<br /> Man Hours: ".TandM::getAmounttimeByProject($pid,$mon,$year)."<br /><br />";
			}    		
    		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
    		$subject="EA T&M User Time - ".$month;
			$to_replace = array(
				'{email_body}' 
			);
			$replace = array(
				$email_body				
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
    		foreach ($emails as $user_email) 
			{
				Yii::app()->mailer->AddAddress($user_email);
			}		
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true))
			{
				return true; 
			}
    	}
    	return false;	
	}
	public function actionupdateDuration(){		
			if(isset($_POST['training'])){
					$train=	$_POST['training'];
			}
			$dv=Yii::app()->db->createCommand("SELECT `values` from codelkups where id='".$train."' ")->queryScalar();				
			echo json_encode(array(
						'status' => 'success',					
						'duration' => $dv,						
				));
	}
}?>