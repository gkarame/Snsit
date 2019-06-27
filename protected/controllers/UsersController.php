<?php
class UsersController extends Controller{	
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + delete + deleteVisa', 
		);
	}
	public function accessRules(){
		return array(
		
			array('allow',
				'actions'=>array('SendVisaToExpire','SendDailytest','sendBillability','changeCountry', 'checkperf', 'checkUserContract', 'checkProbationEnd'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','view','create', 'update','delete', 'changeStatus', 'upload', 'manageVisa', 
					'deleteVisa', 'deleteAttachment','visas', 'passport'),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),		
		);
	}
	public function init(){
		parent::init();
	}
	public function actionView($id, $active = null){
		$model = $this->loadModel($id);
		$arr = Utils::getShortText($model->fullname);
		$subtab = $this->getSubTab(Yii::app()->createUrl('users/view', array('id' => $id)));		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/users/view/'.$id => array(
					'label' => $arr['text'],
					'url' => array('users/view', 'id'=>$id),
					'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->fullname : ''),
					'subtab' =>  $subtab,
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = $subtab;
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'active' => $active,
		));
	}
	public function actionCreate(){
		if(!GroupPermissions::checkPermissions('users-list','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/users/create' => array(
							'label'=>Yii::t('translations','New User'),
							'url' => array('users/create'),
							'itemOptions'=>array('class'=>'link'),
							'subtab' =>  $this->getSubTab(),
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = 0;
		$model = new Users('create');
		$model->active = '1';
		$model->userPersonalDetails = new UserPersonalDetails();
		$model->userPersonalDetails->id_user = 0;
		$model->userPersonalDetails->sns_admin = 0;
		$model->userPersonalDetails->pqa = 0;		
		$model->userHrDetails = new UserHrDetails();
		$model->userHrDetails->id_user = 0;
		$model->userHrDetails->contract_signed = 'n';
		$model->userHrDetails->hr_manual_signed = 'n';		
		Yii::import("xupload.models.XUploadForm");
		$upload_attachments = new XUploadForm;		
		$this->render('manage',array(
				'model'=>$model,
				'upload_attachments' => $upload_attachments,
				'active' => null,
		));			
	}
	public function actionUpdate($id = null, $new = 0, $active = null){
		if(!GroupPermissions::checkPermissions('general-users','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		if ($id == null) {
			$isNew = true;			
			$model = new Users('create');
			$model->active = '1';		
			$model->userPersonalDetails = new UserPersonalDetails();
			$model->userPersonalDetails->id_user = 0;
			$model->userPersonalDetails->sns_admin = 0;
			$model->userPersonalDetails->pqa = 0;
			$model->userHrDetails = new UserHrDetails();
			$model->userHrDetails->id_user = 0;
			$model->userHrDetails->contract_signed = 'n';
			$model->userHrDetails->hr_manual_signed = 'n';
		} else {
			$id = (int) $id;
			$model = $this->loadModel($id);			
			if (isset($model->userPersonalDetails)){			
				if (!empty($model->userPersonalDetails->birthdate)){	
					$model->userPersonalDetails->birthdate = date('d/m/Y', strtotime($model->userPersonalDetails->birthdate));
				}
			}else {
				$model->userPersonalDetails = new UserPersonalDetails();
				$model->userPersonalDetails->id_user = $model->id;
			}			
			if (isset($model->userHrDetails)){
				if (!empty($model->userHrDetails->employment_date)){
					$model->userHrDetails->employment_date = date('d/m/Y', strtotime($model->userHrDetails->employment_date));
				}
				if (!empty($model->userHrDetails->evaluation_date)){
					$model->userHrDetails->evaluation_date = date('d/m/Y', strtotime($model->userHrDetails->evaluation_date));
				}			
				if (!empty($model->userHrDetails->contract_expiry_date)){
					$model->userHrDetails->contract_expiry_date = date('d/m/Y', strtotime($model->userHrDetails->contract_expiry_date));
				}
			}else{
				$model->userHrDetails = new UserHrDetails();
				$model->userHrDetails->id_user = $model->id;
				$model->userHrDetails->contract_signed = 'n';
				$model->userHrDetails->hr_manual_signed = 'n';
			}
		}		
		Yii::import("xupload.models.XUploadForm");
		$upload_attachments = new XUploadForm;		
		if (!$model->isNewRecord) {
			$arr = Utils::getShortText($model->fullname);
			$subtab = $this->getSubTab(($new == 1 ? Yii::app()->createUrl('users/create') :  Yii::app()->createUrl('users/update', array('id' => $id))));
			if (isset(Yii::app()->session['menu'])) {
				if ($new == 1) {
					Utils::closeTab(Yii::app()->createUrl('users/create'));
					$this->action_menu = Yii::app()->session['menu'];
				}
			}
			$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
					$this->action_menu,
					array( 
						'/users/update/'.$id => array(
								'label'=>$arr['text'],
								'url' => array('users/update', 'id'=>$model->id),
								'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->fullname : ''),
								'subtab' => $subtab,
								'order' => Utils::getMenuOrder()+1
						)
					)
			))));
			Yii::app()->session['menu'] = $this->action_menu;
			$this->jsConfig->current['activeTab'] = $subtab;
		}
		$extra = array();
		$visas = array();		
		$valid = true;
		$post = false;
		if (isset($_POST['Users'])){
			$model->attributes = $_POST['Users'];
			$post = true;
		}
		if (isset($_POST['UserPersonalDetails'])){
			if(!isset($_POST['UserPersonalDetails']['sns_admin'])){
				$model->userPersonalDetails->sns_admin=0;
			}
			if(!isset($_POST['UserPersonalDetails']['pqa'])){
				$model->userPersonalDetails->pqa=0;
			}
			$model->userPersonalDetails->attributes = $_POST['UserPersonalDetails'];
		}
		if (isset($_POST['UserHrDetails'])){
			$model->userHrDetails->attributes = $_POST['UserHrDetails'];
		}	
		if (isset($_POST['UserVisas']) && is_array($_POST['UserVisas'])){
			foreach ($_POST['UserVisas'] as $i => $visa){
				if (!isset($visa['id'])){
					$visas[$i] = new UserVisas;
					$visas[$i]->id_user = 0;
				} else {
					$visas[$i] = UserVisas::model()->findByPk((int)$visa['id']);
				}
				$visas[$i]->attributes = $visa;
			}
			$extra['update_visa'] = true;
		}			
		if ($post){
			if (!$model->validate()){
				$valid = false;
			}
			if (!$model->userPersonalDetails->validate()){
				$valid = false;
			}
			if (!$model->userHrDetails->validate()){
				$valid = false;
			}
			foreach ($visas as $visa){
				$valid = $visa->validate() && $valid;
			}
			if ($valid){
				if ($model->save()){
					if (isset($isNew)) {
						$model->userPersonalDetails->id_user = $model->id;
						$model->userHrDetails->id_user = $model->id;
					}
					$model->userPersonalDetails->save();
					$model->userHrDetails->save();
					foreach ($visas as $visa){
						if ($visa->isNewRecord) {
							$visa->id_user = $model->id;
						}
						$visa->save();
					}					
					if (isset($isNew)) {
						echo json_encode(array('status'=>'saved', 'url'=>Yii::app()->createAbsoluteUrl('users/update', array('id'=>$model->id, 'new'=>1))));	
					}else {
						echo json_encode(array_merge(array('status'=>'saved'), $extra));
					}
					Yii::app()->end();
				}
			}else{
				$validateModels = array($model, $model->userPersonalDetails, $model->userHrDetails);
				if (!empty($visas)) 
					$validateModels[] = $visas;
				$this->performAjaxValidation($validateModels);
			}
		}
		if (!Yii::app()->request->isPostRequest) {
			$this->render('manage',array(
				'model'=>$model,
				'active' => $active,
			));
		}
	}	
	public function actionManageVisa($id = NULL) {
    	$is_update = false;    	
    	if ($id == NULL) {
    		$model = new UserVisas();
	    	if ((isset($_POST['id_user']) && $_POST['id_user'] > 0)) {
	    		$is_update = true;
	    		$model->id_user = $_POST['id_user']; 
	    	}
    	}else {
    		$id = (int)$id;
    		$model = UserVisas::model()->findByPk($id);
    		$is_update = true;
    	}    	
    	if (isset($_POST['UserVisas'])){
    		if ($id == NULL) {
    			$model->attributes = $_POST['UserVisas']['new'];
    		} else {
    			$model->attributes = $_POST['UserVisas'][$id];
    		}
   			if ($model->save()) {
				echo json_encode(array('status' => 'saved'));
				exit;
			}
    	}    	
    	if (!empty($model->expiry_date)){	
			$model->expiry_date = date('d/m/Y', strtotime($model->expiry_date));
		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_visa_new', array(
            	'visa'=> $model,
    			'update'=> $is_update,
        	), true, false)));
       exit;
    }    
	public function actionDeleteVisa($id){
		$visa = UserVisas::model()->findByPk($id);
		if ($visa===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$visa->delete(); 
	}
	public function actionDeleteAttachment(){
		$res = false;
		if (Yii::app()->request->isAjaxRequest){
			if (isset($_POST['attachment']) && !empty($_POST['attachment'])){
				$res = Utils::deleteFile($_POST['attachment']);
			}
		}
		echo $res;
		Yii::app()->end();		
	}	
	public function actionChangeStatus($id){
		$user = $this->loadModel($id);
		$user->active = $user->active == 0 ? $user->active = 1 : $user->active = 0;
		$user->save();
		Yii::app()->end();
	}
	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('users-list')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$searchArray = isset($_GET['Users']) ? $_GET['Users'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/users/index' => array(
					'label'=>Yii::t('translations','Users'),
					'url' => array('users/index'),
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
	public function loadModel($id){
		$model=Users::model()->findByPk($id);
		if($model===null)
			$this->redirect(array('index'));
		return $model;
	}
	protected function performAjaxValidation($array){
		if (isset($_POST['ajax']) && $_POST['ajax']==='users-form'){
			$errors = array();
			if (isset($_POST['ajax']) && $_POST['ajax']==='users-form'){
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
	}
	public function actionVisas(){		
		if(!GroupPermissions::checkPermissions('alerts-visas_alerts')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/users/visas' => array(
								'label'=>Yii::t('translations','Visas to Expire'),
								'url' => array('users/visas'),
								'itemOptions' => array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1
						),
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$dataProvider = UserVisas::getVisasExpires();
		$model=new UserVisas('getVisasExpires');
		$this->render('visasexpires', array( 'model' => $model));
	}
	public function actionPassport(){
		if(!GroupPermissions::checkPermissions('alerts-passports_alerts')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/users/visas' => array(
								'label'=>Yii::t('translations','Passports to Expire'),
								'url' => array('users/passport'),
								'itemOptions' => array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1
						),
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$dataProvider = UserVisas::getVisasExpires();
		$model=new UserVisas('getPassportExpires');
		$this->render('passportexpires', array( 'model' => $model));
	}	
	public function actioncheckUserContract(){ 
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('user_contract');
		if ($notif != NULL){
			$to_replace = array(
				'{user}',
			);
			$users=Yii::app()->db->createCommand("SELECT id_user  FROM `user_hr_details` where employment_date<CURRENT_DATE() and contract_signed='n' and DATEDIFF(CURRENT_DATE(),employment_date)>90 and id_user in (select id from users where active=1) ; ")->queryAll();
			if (!empty($users)) {
				$usern='<ul>';
				foreach ($users as $user) {
					$usern.='<li>'.Users::getNameById($user['id_user']).'</li>';
				}
				$usern.='</ul>';
					$subject = $notif['name'];
					$replace = array(
						$usern,
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					Yii::app()->mailer->ClearAddresses();
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					foreach ($emails as $email){
						if (filter_var($email, FILTER_VALIDATE_EMAIL))
							Yii::app()->mailer->AddAddress($email);
					}
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);
			}
		}
	}
	public function actioncheckProbationEnd(){ 
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('user_prob');
		if ($notif != NULL){
			$to_replace = array(
				'{user}',
			);
			$users=Yii::app()->db->createCommand("SELECT id_user  FROM `user_hr_details` where id_user in (select id from users where active=1) and CURRENT_DATE() =  employment_date + INTERVAL 90 DAY; ")->queryAll();
			if (!empty($users)) {
				foreach ($users as $user) {	
					$usern= Users::getNameById($user['id_user']);
					$subject = $notif['name'];
					$replace = array(
						$usern,
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					$lm=EmailNotificationsGroups::getLMNotificationUsers($user['id_user']);
					Yii::app()->mailer->ClearAddresses();
					Yii::app()->mailer->AddAddress($lm);
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					foreach ($emails as $email){
						if (filter_var($email, FILTER_VALIDATE_EMAIL))
							Yii::app()->mailer->AddAddress($email);
					}
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);					
				}
			}
		}
	}
	public function actionsendBillability(){ 
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('monthly_billability');
		if ($notif != NULL){
			$now = new \DateTime('-31 days');
			$month = date('Y-m',strtotime('-31 days'));
			$monthname = $now->format('M');
  			$year = $now->format('Y');	
  			$year2= $year-1;		 
			$to_replace = array(
				'{body}',
			);
			$ops="and uts.id_user in ("; 			
			foreach(UserPersonalDetails::getOpsAll() as $o){
				$ops.=" '".$o['id']."',";
			}
			$ops.=" 0 ) ";			
			$techPS=" and uts.id_user in ("; 			
			foreach(UserPersonalDetails::getPSAll() as $t){
				$techPS.=" '".$t['id']."',";
			}
			$techPS.=" 0 ) ";
			$techCS=" and uts.id_user in ("; 			
			foreach(UserPersonalDetails::getCSAll() as $t){
				$techCS.=" '".$t['id']."',";
			}
			$techCS.=" 0 ) ";			
			$str="Dears,<br/><br/>Kindly find below the billability of all teams for the month of <b>".$monthname."</b>:<br/><br/>";
			$PS=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and uts.date like '".$month."%'  ".$techPS." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and uts.date like '".$month."%'   ".$techPS.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and uts.date like '".$month."%'  ".$techPS." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and  uts.date like '".$month."%'   ".$techPS."
					union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  uts.date like '".$month."%'   ".$techPS.") as r 
					) as total ")->queryScalar();			
			$CS=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and uts.date like '".$month."%'  ".$techCS." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and uts.date like '".$month."%'   ".$techCS.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and uts.date like '".$month."%'  ".$techCS." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and  uts.date like '".$month."%'   ".$techCS." 
						union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  uts.date like '".$month."%'   ".$techCS.") as r 
					) as total ")->queryScalar();
			$OPS=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and uts.date like '".$month."%'  ".$ops." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and uts.date like '".$month."%'   ".$ops.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and uts.date like '".$month."%'  ".$ops." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and  uts.date like '".$month."%'   ".$ops."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  uts.date like '".$month."%'   ".$ops.") as r 
					) as total ")->queryScalar();			
			$str.='<li>OPS Team: '.(double)$OPS.'%</li><li>Tech-PS Team: '.(double)$PS.'%</li><li>Tech-CS Team: '.(double)$CS.'%</li>';	
			$str.='<br/><br/>Teams billability for <b>'.$year.':</b><br/><br/>';
			$PSYTD=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and YEAR(uts.date)=".$year."   ".$techPS." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and YEAR(uts.date)=".$year."   ".$techPS.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and YEAR(uts.date)=".$year."   ".$techPS." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and YEAR(uts.date)=".$year."    ".$techPS."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  YEAR(uts.date)=".$year."    ".$techPS.") as r 
					) as total ")->queryScalar();			
			$CSYTD=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and YEAR(uts.date)=".$year."   ".$techCS." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and YEAR(uts.date)=".$year."    ".$techCS.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and YEAR(uts.date)=".$year."  ".$techCS." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and YEAR(uts.date)=".$year."    ".$techCS."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  YEAR(uts.date)=".$year."   ".$techCS.") as r 
					) as total ")->queryScalar();
			$OPSYTD=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and YEAR(uts.date)=".$year."   ".$ops." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and YEAR(uts.date)=".$year."    ".$ops.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and YEAR(uts.date)=".$year."   ".$ops." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and YEAR(uts.date)=".$year."    ".$ops."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  YEAR(uts.date)=".$year."    ".$ops.") as r 
					) as total ")->queryScalar();
			$str.='<li>OPS Team: '.(double)$OPSYTD.'% YTD</li><li>Tech-PS Team: '.(double)$PSYTD.'% YTD</li><li>Tech-CS Team: '.(double)$CSYTD.'% YTD</li>';
			$str.='<br/><br/>Teams billability for <b>'.$year2.':</b><br/><br/>';
			$PSYTD2=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and YEAR(uts.date)=".$year2."   ".$techPS." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and YEAR(uts.date)=".$year2."   ".$techPS.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and YEAR(uts.date)=".$year2."   ".$techPS." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and YEAR(uts.date)=".$year2."    ".$techPS."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  YEAR(uts.date)=".$year2."    ".$techPS.") as r 
					) as total ")->queryScalar();			
			$CSYTD2=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and YEAR(uts.date)=".$year2."   ".$techCS." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and YEAR(uts.date)=".$year2."    ".$techCS.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and YEAR(uts.date)=".$year2."  ".$techCS." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and YEAR(uts.date)=".$year2."    ".$techCS."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  YEAR(uts.date)=".$year2."   ".$techCS.") as r 
					) as total ")->queryScalar();
			$OPSYTD2=Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
				 from (
				 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and YEAR(uts.date)=".$year2."   ".$ops." 
				 		union all
				 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and YEAR(uts.date)=".$year2."    ".$ops.") as r
					) as billable ,
				 	 (
				 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and YEAR(uts.date)=".$year2."   ".$ops." 
				 	 	union all
						select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user  and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and YEAR(uts.date)=".$year2."    ".$ops."
							union all 
				 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  YEAR(uts.date)=".$year2."    ".$ops.") as r 
					) as total ")->queryScalar();
			$str.='<li>OPS Team: '.(double)$OPSYTD2.'% YTD</li><li>Tech-PS Team: '.(double)$PSYTD2.'% YTD</li><li>Tech-CS Team: '.(double)$CSYTD2.'% YTD</li>';
			
			$str.='<br/><br/>Top 5 non-billable resources for <b>'.$year.':</b><br/><br/>';
			$nonbs= Yii::app()->db->createCommand("
select t1.id_user , TRUNCATE((nonbillable) ,2) as nbperc from 
(
select r.id_user , sum(r.amount) as nonbillable from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='No' and YEAR(uts.date)=".$year." GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and dti.billable='No' and YEAR(uts.date)=".$year." GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='No' and YEAR(uts.date)=".$year." GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user ) t1
,
(
select r.id_user , sum(r.amount) as total from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable in ('No','Yes') and uts.status='1' and YEAR(uts.date)=".$year."  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('No','Yes') and uts.status='1' and YEAR(uts.date)=".$year."  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable in ('No','Yes') and YEAR(uts.date)=".$year." GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user
order by nbperc desc limit 5")->queryAll();
		foreach ($nonbs as $nonb) {
			$str.='<li>'.Users::getNameByid($nonb['id_user']).'</li>';
		}
		$str.='<br/><br/>Top 5 billable resources for <b>'.$year.':</b><br/><br/>';
		$bs= Yii::app()->db->createCommand("
select t1.id_user , TRUNCATE((billable) ,2) as nbperc from 
(
select r.id_user , sum(r.amount) as billable from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and YEAR(uts.date)=".$year." GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and YEAR(uts.date)=".$year." GROUP BY uts.id_user 
) as r  
GROUP BY r.id_user ) t1,
(
select r.id_user , sum(r.amount) as total from (
select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and uts.status='1' and YEAR(uts.date)=".$year."  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable IN ('Yes','No') and uts.status='1' and YEAR(uts.date)=".$year."  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable IN ('Yes','No') and YEAR(uts.date)=".$year." GROUP BY uts.id_user 
) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user
order by nbperc desc limit 5")->queryAll();
		foreach ($bs as $b) {
			$str.='<li>'.Users::getNameByid($b['id_user']).'</li>';
		}
				$str.='<br/><br/>Best Regards,<br/>SNSit';	 
					$subject = $notif['name'].' - '.$monthname;
					$replace = array(
						$str,
					);
					$body = str_replace($to_replace, $replace, $notif['message']);					 
					Yii::app()->mailer->ClearAddresses();
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					foreach ($emails as $email){
						Yii::app()->mailer->AddAddress($email);
					}
					//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
					print_r($str);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);
		}else{
			echo "no";
		}			
	}	
	public function actioncheckperf(){ 
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('user_perf');
		if ($notif != NULL){
			$to_replace = array(
				'{body}',
			);
			$users=Yii::app()->db->createCommand("SELECT distinct(id_user) as usr FROM productivity  where  sent_email is NULL;")->queryAll();
			foreach ($users as $user) {
				$up='(';
				$flag=0;
				$values=Yii::app()->db->createCommand(" 
				select p.id, p.id_user, p.id_task, p.id_phase,p.id_project, p.expected_mds, case when p.id_phase =0 then
				 p.expected_mds- (select sum(amount)/8 from user_time where id_user=p.id_user and `default`<>'1' and id_task=p.id_task) 
				ELSE
				p.expected_mds- (select sum(amount)/8 from user_time where id_user=p.id_user and `default`<>'1' and id_task in (select pt.id from projects_tasks pt  where pt.id_project_phase=p.id_phase ) 
				)
				end diff, case when p.id_phase =0 then
				(select sum(amount)/8 from user_time where id_user=p.id_user and `default`<>'1' and id_task=p.id_task) 
				ELSE
				(select sum(amount)/8 from user_time where id_user=p.id_user and `default`<>'1' and id_task in (select pt.id from projects_tasks pt  where pt.id_project_phase=p.id_phase ) 
				)
				end actuals
				FROM productivity p where sent_email is null and id_user='".$user['usr']."' ")->queryAll();
				$list='<br><table border="1" style="width:100%;">   <tr>
    <th style="height:10">Project</th>
    <th style="height:10">Task</th> 
    <th style="height:10">Expected MDs</th>
    <th style="height:10">Actuals</th></tr>';
				if(!(empty($values))){
					foreach ($values as $value){
						if($value['diff']<0){
							$flag=1;
							$up.=$value['id'].',';
							if($value['id_phase']=='0') 
							{
									$task_phase=ProjectsTasks::getTaskDescByid($value['id_task']);
							}
							else
							{
									$task_phase=ProjectsPhases::getPhaseDescByPhaseId($value['id_phase']);
							}
							$list.='<tr><td style="width:100px;text-align: center;">'.Projects::getNameById($value['id_project']).'</td><td style="width:100px;text-align: center;">'.$task_phase.'</td><td style="width:60px;text-align: center;">'.$value['expected_mds'].'</td><td style="width:60px;text-align: center;">'.number_format($value['actuals'],2).'</td></tr>';
						}						
					}
					$list.='</table>';	
					if ($flag == 1){
						$up.='0)';
						$nrm = Yii::app()->db->createCommand("UPDATE productivity SET sent_email = 1 WHERE id in ".$up." ")->execute();	
						$body='<br/>Kindly note that <b><a href="'.Yii::app()->createAbsoluteUrl('performance/view', array('id'=>$value['id_user'])).'">'.Users::getNameById($value['id_user']).'</a></b> has exceeded the expected mandays on the below task(s), please fill the Reason field on these tasks: <br/>'.$list;
						$subject = $notif['name'];
							$replace = array(
									$body,
							);
							$body = str_replace($to_replace, $replace, $notif['message']);
    						$lm=EmailNotificationsGroups::getLMNotificationUsers($value['id_user']);
							Yii::app()->mailer->ClearAddresses();
							Yii::app()->mailer->AddAddress($lm);							
							Yii::app()->mailer->Subject  = $subject;
							Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
							Yii::app()->mailer->Send(true);
					}
					$body='';
				}
			}
		}			
	}
	public function actionSendVisaToExpire(){ 		
		$subject='Visas to Expire';		
		$notif = EmailNotifications::getNotificationByUniqueName('visa_to_expire');
		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
		Yii::app()->mailer->ClearAddresses();
		$body=$notif['message'];
		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach ($emails as $email){
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					Yii::app()->mailer->AddAddress($email);
			}
		$visa = UserVisas::getVisasExpires()->getData(); 
		$visa_to_exp=' ';
		$visa_to_exp.='<ul>';
	 	foreach($visa as $row){						
						$visa_to_exp.=' <li>'.$row->user->fullname.' '.$row->notes.' Expires in '.Utils::formatDate($row->expiry_date).' '.$row->diff.' days left . </li>';
		}
		$visa_to_exp.='</ul>';
		$expired = UserVisas::getVisasExpired()->getData(); 
		$expired_visa=' ';
		$expired_visa.='<ul>';
	 	foreach($expired as $row){						
						$expired_visa.=' <li>'.$row->user->fullname.', '.$row->notes.'. Expired in '.Utils::formatDate($row->expiry_date).' </li>';
		}
		$expired_visa.='</ul>';
		 $passports = UserVisas::getPassportExpires()->getData(); 
		$pass_to_exp=' ';
		$pass_to_exp.='<ul>';
		foreach($passports as $row){						
						$pass_to_exp.=' <li>'.$row->user->fullname.' '.$row->notes.' Expires in '.Utils::formatDate($row->expiry_date).' '.$row->diff.' days left . </li>';
		}
		$pass_to_exp.='</ul>';
		if (count($visa)<1){
			$visa_to_exp="There are no visas about to expire." ;
		}
		if (count($expired)<1){
			$expired_visa="There are no visas that expired." ;
		}
		if (count($passports)<1){
			$pass_to_exp="There are no passports about to expire" ;
		}
	$to_replace = array(
						'{visas}','{expvisas}',
						'{passports}'							
						);
	$replace = array(
						$visa_to_exp, $expired_visa,
						$pass_to_exp		
									);
if(count($visa)>0 || count($passports)>0 || count($expired)>0 ){
$body = str_replace($to_replace, $replace, $notif['message']); }
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    if (Yii::app()->mailer->Send(true)){
						echo "Sent";
	 					echo $body; }
	}
	public function actionSendDailytest(){ 
		$subject='Daily Test';			
		Yii::app()->mailer->ClearAddresses();
		
		
		$body="TEST please  <a href=\"mailto:houda.nasser@sns-emea.com?Subject=SR#321&body=%20\" target=\"_top\">Send Mail</a>";

		/*$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '106' AND access = 'Yes'")->queryColumn();
			$assgined=Users::getEmailbyID(58);			
	    		

			array_push($emails_customer, $assgined);
			foreach ($emails_customer as $email_c)
			{
				if (filter_var($email_c, FILTER_VALIDATE_EMAIL))
				{
					Yii::app()->mailer->AddCcs($email_c);
				}
			}	*/		

    	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");    		
			Yii::app()->mailer->Subject  = $subject." ".date('Y-m-d H:i:s');
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)." ".date('Y-m-d H:i:s')."</div>");
	    	Yii::app()->mailer->Send(true); 	
	 	echo $body;
	}
	public static function actionChangeCountry(){ 	
		if(isset($_POST['country']) && isset($_POST['id_visa']) ){
			 $country=$_POST['country'];
			 $visa=$_POST['id_visa'];			
			if(empty($country)){
					echo json_encode(array('status'=>'failure'));
			}else{
				$nrm = Yii::app()->db->createCommand("UPDATE user_visas SET country = '$country' WHERE id ='$visa' ")->execute();	
					echo json_encode(array('status'=>'success'));
			}					
		}
	}
}?>
