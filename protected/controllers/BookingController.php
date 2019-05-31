<?php
class BookingController extends Controller{
	public function filters(){
		return array(
			'accessControl', 
		);
	}
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
					'actions'=>array(
							'index', 'view','getBranch','getSupplier','updateAlerts', 'create', 'update', 'delete','upload','deleteUpload'
					),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  
					'users'=>array('*'),
			),
		);
	}
	
	public function init()
	{
		parent::init();
	}
	
	public function actions()
	{
		 return array(	 );
	}
	public function loadModel($id)
	{
		$model = Booking::model()->findByPk($id);;
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('booking-list'))
		{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		
		$searchArray = isset($_GET['Booking']) ? $_GET['Booking'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/booking/index' => array(
					'label'=>Yii::t('translations', 'Travel'),
					'url' => array('booking/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1+1,
					'search' => $searchArray,
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new Booking('search');
		$model->unsetAttributes(); 
		$model->attributes= $searchArray;
		
		$this->render('index',array(
			'model'=>$model,
		));
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$arr = Utils::getShortText(Yii::t('translations', 'TR#'.str_pad($model->id, 5, '0', STR_PAD_LEFT)));
		$subtab = $this->getSubTab(Yii::app()->createUrl('booking/view', array('id' => $id)));
	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/booking/view/'.$id => array(
					'label'=>$arr['text'],
					'url' => array('booking/view', 'id'=>$id),
					'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? Yii::t('translations', 'TR#'.str_pad($model->id, 5, '0', STR_PAD_LEFT)) : ''),
					'subtab' =>  $subtab,
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = $subtab;
	
		$this->render('view',array(
			'model'=>$model,
		));
	}
	public function actionUpdate($id=null)
	{
		if($id!=null){
			$model = $this->loadModel($id);
			$arr = Utils::getShortText(Yii::t('translations', 'TR#'.str_pad($model->id, 5, '0', STR_PAD_LEFT)));
			$subtab = $this->getSubTab(Yii::app()->createUrl('booking/update', array('id' => $id)));
		
			$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/booking/update/'.$id => array(
						'label'=>$arr['text'],
						'url' => array('booking/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? Yii::t('translations', 'TR#'.str_pad($model->id, 5, '0', STR_PAD_LEFT)) : ''),
						'subtab' =>  $subtab,
						'order' => Utils::getMenuOrder()+1
					)
				)
			))));
			Yii::app()->session['menu'] = $this->action_menu;
			$this->jsConfig->current['activeTab'] = $subtab;
				
			if (isset($_POST['Booking']))
			{
				$oldcarbook= $model->car_booking;
				$olddatebookpick=$model->pickup;
				$olddatebookreturn=$model->return;				
				$oldHotelbook= $model->hotel_booking;
				$olddatebookcheckin=$model->checkin;
				$olddatebookcheckout=$model->checkout;
				$oldhotelsupp= $model->hotel_supplier;
				$oldcarsupp= $model->car_supplier;
				$oldhotelname= $model->hotelname;
				$oldreason=  $model->reason;
				$oldcarname= $model->carname;
				$oldcarreason=  $model->carreason;
				$oldbillable= $model->billable;
				$oldSupplier = $model->travel_supplier;
				$oldtraveler = $model->traveler;
				$oldbranch= $model->origin;
				$olddepdate = $model->departure_date;
				$olddeptime= $model->departure_time;
				$oldredate = $model->return_date;
				$oldretime= $model->return_time;
				$oldorigin= $model->origin;
				$olddestination=  $model->destination;
				$oldpurpose=  $model->purpose;
				$oldcustomer = $model->id_customer;
				$oldproject= $model->id_project;
				

				$model->attributes = $_POST['Booking'];	
				/*if(isset($_POST['Booking']['origin']))
				{
					$origin=Codelkups::getCodelkup($_POST['Booking']['origin']);
					$model->travel_supplier=Suppliers::getTravelSupp($origin);
				}				*/
				if (!empty($_POST['Booking']['id_project']) && !empty($_POST['Booking']['purpose'])  && $_POST['Booking']['purpose']== 'Project')
				{
					//$model->id_project = implode(",", $_POST['Booking']['id_project']);
					$model->id_project = $_POST['Booking']['id_project'];
				}else
				{
					$model->id_project=null;
				}
				if (!empty($_POST['Booking']['departure_date']))
				{
					$model->departure_date= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['departure_date'])->format('d/m/Y');
				}
				if (!empty($_POST['Booking']['return_date']))
				{
					$model->return_date= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['return_date'])->format('d/m/Y');
				}
				if (!empty($_POST['Booking']['checkin']) && $_POST['Booking']['hotel_booking'] == 'Yes')
				{
					$model->checkin= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['checkin'])->format('d/m/Y');
				}
				else
				{
					$model->checkin=null;
				}
				if (!empty($_POST['Booking']['checkout']) && $_POST['Booking']['hotel_booking'] == 'Yes')
				{
					$model->checkout= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['checkout'])->format('d/m/Y');
				}
				else
				{
					$model->checkout=null;
				}
				if (!empty($_POST['Booking']['pickup']) && $_POST['Booking']['car_booking'] == 'Yes')
				{
					$model->pickup= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['pickup'])->format('d/m/Y');
				}
				else
				{
					$model->pickup=null;
				}				
				if (!empty($_POST['Booking']['return']) && $_POST['Booking']['car_booking'] == 'Yes')
				{
					$model->return= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['return'])->format('d/m/Y');
				}
				else
				{
					$model->return=null;
				}
				if (!empty($_POST['Booking']['destination']) && $_POST['Booking']['destination']!='')
				{
					$model->destination=Booking::getIdByCityName($_POST['Booking']['destination']);
				}  				
				if ($model->save())
				{
					if( $olddepdate  !=  $model->departure_date || $oldredate  !=  $model->return_date){
						if ($model->departure_date ==  $model->return_date)
						{
							self::sendOneDayAlert($model->traveler, $model->id, $model->destination, $model->departure_date, $model->return_date, $model->id_project, $model->billable, $model->origin, $model->id_customer);
						}
					}
					if($oldbillable != $model->billable || $oldSupplier  !=  $model->travel_supplier || $oldtraveler  !=  $model->traveler || 	$oldbranch !=  $model->origin || $olddepdate  !=  $model->departure_date || $olddeptime !=  $model->departure_time || $oldredate  !=  $model->return_date || $oldretime !=  $model->return_time || $oldorigin !=  $model->origin || $olddestination !=   $model->destination || $oldcustomer  !=  $model->id_customer || $oldproject !=  $model->id_project)
					{
						self::sendUpdatesBooking($model->travel_supplier, $model->traveler, $model->id, $model->destination, $model->departure_date, $model->departure_time, $model->return_date, $model->return_time, $model->id_project, $model->billable, $model->origin, $model->id_customer);
					}
					if($model->car_booking == 'No')
					{ $model->car_supplier=null; }
					if($model->hotel_booking == 'No')
					{ $model->hotel_supplier=null; }	
					$model->save();			
					if(($model->hotel_booking == 'Yes' && $oldHotelbook =='No' ) || ($model->car_booking == 'Yes' && $oldcarbook=='No'))
					{
						self::sendBookingsEmail($model->hotel_booking, $model->car_booking, $model->destination, $model->traveler, $model->hotel_supplier, $model->hotelname, $model->reason, $model->checkin, $model->checkout, $model->car_supplier, $model->carname, $model->carreason,$model->pickup, $model->return, $model->id_project);
					}else if(((($model->hotel_booking == 'Yes' && $oldHotelbook =='Yes') || ($model->hotel_booking == 'No' && $oldHotelbook =='Yes' && $model->car_booking =='Yes')) && ($olddatebookcheckin != $model->checkin || $olddatebookcheckout != $model->checkout || $oldhotelsupp != $model->hotel_supplier || $oldhotelname != $model->hotelname || $oldreason != $model->reason ) ) || ((($model->car_booking == 'Yes' && $oldcarbook=='Yes') || ($model->car_booking == 'No' && $oldcarbook=='Yes' && $model->hotel_booking =='Yes')) && ($olddatebookpick != $model->pickup || $olddatebookreturn != $model->return || $oldcarsupp != $model->car_supplier || $oldcarname != $model->carname || $oldcarreason != $model->carreason )))
					{
						self::sendBookingsEmailUpdated($model->hotel_booking, $model->car_booking, $model->destination, $model->traveler, $model->hotel_supplier, $model->hotelname, $model->reason, $model->checkin, $model->checkout, $model->car_supplier, $model->carname, $model->carreason,$model->pickup, $model->return, $model->id_project);
					}
					Utils::closeTab(Yii::app()->request->url);
					$this->redirect(array('booking/view','id' => $model->id)); 
				}
			}
			$model->destination=Codelkups::getCodelkup($model->destination);
			$this->render('update',array(
				'model'=>$model,
			));
		}				
	}
	public function sendBookingsEmailUpdated($hotelbook, $carbook, $destination, $traveler, $hotel_supplier, $hotelname, $reason, $checkin, $checkout, $car_supplier, $carname, $carreason,$pickup, $return, $projects)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('bookings_notification');
		$subject = 'Updated Hotel & Car Booking for '.Users::getNameById($traveler);	
    	if ($notif != NULL){
    		if($carbook=="Yes" && $hotelbook == "Yes"){
    			$bname= "Hotel/Car bookings";
    		}else if($carbook=="Yes") {
    			$bname= "Car booking";
    		}else if($hotelbook=="Yes") {
    			$bname= "Hotel booking";
    		}
    		$txt="Dear SNS Admins, <br/><br/>Please proceed with the following ".$bname.": <br/><br/><b>Destination:</b> ".Codelkups::getCodelkup($destination)." ";
    		if($hotelbook=="Yes"){
    			if($hotel_supplier== 'Choose another hotel')
    			{
    				$supplier= $hotelname."<br/> Reason: ".$reason; 

    			}else{
    				$supplier=Suppliers::getNameById($hotel_supplier);
    			}
    			$txt.="<br/><br/><b>Hotel Booking Details:</b><br />Check-In Date: ".$checkin."<br/>Check-Out Date: ".$checkout."<br/>Supplier: ".$supplier."";
    		}
    		if($carbook=="Yes"){
    			if($car_supplier== 'Choose another supplier')
    			{
    				$carsupp= $carname."<br/> Reason: ".$carreason; 

    			}else{
    				$carsupp=Suppliers::getNameById($car_supplier);
    			}
    			$txt.="<br/><br/><b>Car Booking Details:</b><br />Pick Up Date: ".$pickup."<br/>Return Date: ".$return."<br/>Supplier: ".$carsupp."";
    		}
    		$txt.="<br/><br/>Best Regards,<br/>SNSit";
    		$to_replace = array('{body}');
    		$replace = array($txt);

			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			foreach($emails as $email) {
			if (!empty($email))
				{	Yii::app()->mailer->AddAddress($email);	}
			}
			$traveleremail=Users::getEmailbyID($traveler);
			//Yii::app()->mailer->AddCcs($traveleremail);
			Yii::app()->mailer->AddAddress($traveleremail);
			 Yii::app()->mailer->AddAddress("sns-travel@sns-emea.com");
			//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");		
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
	public function sendBookingsEmail($hotelbook, $carbook, $destination, $traveler, $hotel_supplier, $hotelname,  $reason, $checkin, $checkout, $car_supplier, $carname, $carreason,$pickup, $return, $projects)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('bookings_notification');
		$subject = 'Hotel & Car Booking for '.Users::getNameById($traveler);	
    	if ($notif != NULL){
    		if($carbook=="Yes" && $hotelbook == "Yes"){
    			$bname= "Hotel/Car bookings";
    		}else if($carbook=="Yes") {
    			$bname= "Car booking";
    		}else if($hotelbook=="Yes") {
    			$bname= "Hotel booking";
    		}
    		$txt="Dear SNS Admins, <br/><br/>Please proceed with the following ".$bname.": <br/><br/><b>Destination:</b> ".Codelkups::getCodelkup($destination)." ";
    		if($hotelbook=="Yes"){
    			if($hotel_supplier== 'Choose another hotel')
    			{
    				$supplier= $hotelname."<br/> Reason: ".$reason; 

    			}else{
    				$supplier=Suppliers::getNameById($hotel_supplier);
    			}
    			$txt.="<br/><br/><b>Hotel Booking Details:</b><br />Check-In Date: ".$checkin."<br/>Check-Out Date: ".$checkout."<br/>Supplier: ".$supplier."";
    		}
    		if($carbook=="Yes"){
    			if($car_supplier== 'Choose another supplier')
    			{
    				$carsupp= $carname."<br/> Reason: ".$carreason; 

    			}else{
    				$carsupp=Suppliers::getNameById($car_supplier);
    			}
    			$txt.="<br/><br/><b>Car Booking Details:</b><br />Pick Up Date: ".$pickup."<br/>Return Date: ".$return."<br/>Supplier: ".$carsupp."";
    		}
    		$txt.="<br/><br/>Best Regards,<br/>SNSit";
    		$to_replace = array('{body}');
    		$replace = array($txt);

			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			foreach($emails as $email) {
				if (!empty($email))
				{	Yii::app()->mailer->AddAddress($email);	
				}
			}
			Yii::app()->mailer->AddAddress("sns-travel@sns-emea.com");
		//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");	
			$traveleremail=Users::getEmailbyID($traveler);
		//Yii::app()->mailer->AddCcs($traveleremail);
			Yii::app()->mailer->AddAddress($traveleremail);	
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
	
	public function actiongetBranch(){		
			if(isset(	$_POST['traveler']) && !empty($_POST['traveler'] )){
			$user= 	$_POST['traveler'];	
			$branch= Users::getBranchByUser($user);
			echo json_encode(array(
						'status' => 'success',					
						'branch' => $branch,						
				));
		}else{echo json_encode(array(	'status' => 'failure'	));}
	}
	public function actiongetSupplier(){		
			if(isset(	$_POST['origin']) && !empty($_POST['origin']) && !empty($_POST['type'])){
			$origin= 	$_POST['origin'];	
			$type= $_POST['type'];
			if($type == 'travel')
			{
				$supplier= Suppliers::getTravelSuppPerOriginDD($origin);
			}else if ($type =='hotel')
			{
				$supplier= Suppliers::getHotelSuppPerCityDD($origin);
			}
			else if ($type =='car')
			{
				$supplier= Suppliers::getCarSuppPerCityDD($origin);
			}
			if (!empty($supplier))
			{	echo json_encode($supplier);

			}	else{echo json_encode(array(	'' => ''	));}
		}else{echo json_encode(array(	'' => ''	));}
	}
	
	public function actionupdateAlerts(){		
			if(isset($_POST['departuredate']) && isset(	$_POST['traveler']) && !empty($_POST['traveler'] && !empty($_POST['departuredate']))){
			$departuredate=	$_POST['departuredate'];
			$user= 	$_POST['traveler'];	
			$alert="";
			if ((!ctype_digit($user)))
			{
				$user=Users::getIdByName($user);
			}
			$expiry=Yii::app()->db->createCommand("SELECT max(expiry_date) FROM `user_visas` where type='passport' and id_user=".$user." ")->queryScalar();				
			if(!empty($expiry))
			{	$limit= DateTime::createFromFormat('d/m/Y', $departuredate)->format('Y-m-d');
						$effectiveDate = date('Y-m-d', strtotime($limit . "+6 months") );
						if($expiry<$effectiveDate)
						{	$alert="Passport is not valid, as per the record passport expiry date is on ".date('d-m-Y', strtotime($expiry) )."";	}
						else { $alert=""; } }
						else
							{ $alert="Passport is not added for traveler ".Users::getNameById($user); }
			echo json_encode(array(
						'status' => 'success',					
						'alerts' => $alert,						
				));
		}else{echo json_encode(array(	'status' => 'failure'	));}
	}
	public function sendBookingCreated($travel_supplier, $traveler, $id, $destination, $departure_date, $departure_time, $return_date, $return_time, $projects, $billable, $origin, $customer)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('new_booking');
		$date = date_format(DateTime::createFromFormat('d/m/Y', $departure_date), 'M j');
		$subject = 'Flight Booking - '.Users::getNameById($traveler).' - '.Codelkups::getCodelkup($destination).' - '.$date;	
    	if ($notif != NULL){
    		
    		$txt="Dear ".Suppliers::getNameById($travel_supplier).", <br/><br/>Please send us booking options for the following itinerary:<br/><br/><b><a class='show_link' href='".Yii::app()->createAbsoluteUrl("booking/update", array("id" => $id))."'>TR#".str_pad($id, 5, '0', STR_PAD_LEFT)."</a></b>";
    		$txt.="<br />Passenger Name: ".Users::getNameById($traveler)."<br />Flying To: ".Codelkups::getCodelkup($destination)."<br />Flying From: ".Codelkups::getCodelkup($origin);
    		$txt.="<br />Departure Date: ".$departure_date.", Preferred Time: ".$departure_time."<br />Return Date: ".$return_date.", Preferred Time: ".$return_time."";
    		
    		
			if(!empty($projects)){	$txt.="<br /><br />Project(s): ".Booking::getProjectByIds($projects);	}
			else{ $txt.="<br />";} 
			if(!empty($customer))
			{
				$txt.="<br />Customer: ".Booking::getName($customer);
			}
    		$txt.="<br />Billable: ".$billable."<br/><br/>Best Regards,<br/>SNSit";

			Yii::app()->mailer->ClearAddresses();
    		$supp_email= Suppliers::getEmailById($travel_supplier);
    		if(!empty($supp_email)){
				$pieces = explode(";", $supp_email);
				foreach($pieces as $email) {
					if (!empty($email))
					{	Yii::app()->mailer->AddAddress($email);	}
				}
			}
    		$to_replace = array('{body}');
    		$replace = array($txt);

			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->From="sns-travel@sns-emea.com";
			//Yii::app()->mailer->AddCcs("sns-travel@sns-emea.com");
			Yii::app()->mailer->AddAddress("sns-travel@sns-emea.com");
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			foreach($emails as $email) {
			if (!empty($email))
				{	//Yii::app()->mailer->AddCcs($email);	
					Yii::app()->mailer->AddAddress($email);
				}
			}
	//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");	
			if(!empty($projects))
			{
				$pids=explode(',', $projects);
				foreach ($pids as $key => $id) {
					$pm= Projects::getProjectManagerEmail($id);
					if(!empty($pm))
					{
						Yii::app()->mailer->AddAddress($pm);
					}
				}	
			}
				
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}

	public function sendOneDayAlert($traveler, $id, $destination, $departure_date, $return_date, $projects, $billable, $origin, $customer)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('same_day_booking');
		$date = date_format(DateTime::createFromFormat('d/m/Y', $departure_date), 'M j');
		$subject = ' Same Day Booking Alert - '.Users::getNameById($traveler).' - '.Codelkups::getCodelkup($destination).' - '.$date;	
    	if ($notif != NULL){
    		
    		$txt="Dears,  <br/><br/>Please find below information on booking <b><a class='show_link' href='".Yii::app()->createAbsoluteUrl("booking/update", array("id" => $id))."'>TR#".str_pad($id, 5, '0', STR_PAD_LEFT)."</a></b> made for one day:" ;
    		$txt.="<br/><br /><b>- Passenger Name:</b> ".Users::getNameById($traveler)."<br /><b>- Flying To:</b> ".Codelkups::getCodelkup($destination)."<br /><b>- Flying From:</b> ".Codelkups::getCodelkup($origin);
    		$txt.="<br /><b>- Departure Date:</b> ".$departure_date."<br /><b>- Return Date:</b> ".$return_date." ";
    		
    		
			if(!empty($projects)){	$txt.="<br /><b>- Project(s):</b> ".Booking::getProjectByIds($projects);	}
			if(!empty($customer)){	$txt.="<br /><b>- Customer:</b> ".Booking::getName($customer);		}
    		$txt.="<br /><b>- Billable:</b> ".$billable."<br/><br/>Best Regards,<br/>SNSit";

			Yii::app()->mailer->ClearAddresses();
    		
    		$to_replace = array('{body}');
    		$replace = array($txt);

			$body = str_replace($to_replace, $replace, $notif['message']);
				
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			foreach($emails as $email) {
			if (!empty($email))
				{		
					Yii::app()->mailer->AddAddress($email);
				}
			}
				
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
	public function sendUpdatesBooking($travel_supplier, $traveler, $id, $destination, $departure_date, $departure_time, $return_date, $return_time, $projects, $billable, $origin, $customer)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('new_booking');
		$date = date_format(DateTime::createFromFormat('d/m/Y', $departure_date), 'M j');
		$subject = ' Updated Flight Booking - '.Users::getNameById($traveler).' - '.Codelkups::getCodelkup($destination).' - '.$date;	
    	if ($notif != NULL){
    		
    		$txt="Dear ".Suppliers::getNameById($travel_supplier).", <br/><br/>Please send us booking options for the following itinerary:<br/><br/><b><a class='show_link' href='".Yii::app()->createAbsoluteUrl("booking/update", array("id" => $id))."'>TR#".str_pad($id, 5, '0', STR_PAD_LEFT)."</a></b><br />Passenger Name: ".Users::getNameById($traveler)."<br />Flying To: ".Codelkups::getCodelkup($destination)."<br />Flying From: ".Codelkups::getCodelkup($origin);
    		$txt.="<br />Departure Date: ".$departure_date.", Preferred Time: ".$departure_time."<br />Return Date: ".$return_date.", Preferred Time: ".$return_time." ";
    		
    		
			if(!empty($projects)){	$txt.="<br /><br />Project(s): ".Booking::getProjectByIds($projects);	}
			else{ $txt.="<br />";} 
			if(!empty($customer))
			{
				$txt.="<br />Customer: ".Booking::getName($customer);
			}
    		$txt.="<br />Billable: ".$billable."<br/><br/>Best Regards,<br/>SNSit";

			Yii::app()->mailer->ClearAddresses();
    		$supp_email= Suppliers::getEmailById($travel_supplier);
    		if(!empty($supp_email)){
				$pieces = explode(";", $supp_email);
				foreach($pieces as $email) {
					if (!empty($email))
					{	Yii::app()->mailer->AddAddress($email);	}
				}
			}
    		$to_replace = array('{body}');
    		$replace = array($txt);

			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->From="sns-travel@sns-emea.com";
			//Yii::app()->mailer->AddCcs("sns-travel@sns-emea.com");
			Yii::app()->mailer->AddAddress("sns-travel@sns-emea.com");
		//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");	
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			foreach($emails as $email) {
			if (!empty($email))
				{	//Yii::app()->mailer->AddCcs($email);	
					Yii::app()->mailer->AddAddress($email);
				}
			}
			if(!empty($projects))
			{
				$pids=explode(',', $projects);
				foreach ($pids as $key => $id) {
					$pm= Projects::getProjectManagerEmail($id);
					if(!empty($pm))
					{
						Yii::app()->mailer->AddAddress($pm);
					}
				}	
			}
				
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'update' page.
	 * @throws CHttpException
	 * @author Romeo Onisim
	 */
	public function actionCreate()
	{
		if(!GroupPermissions::checkPermissions('booking-list','write'))
		{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/booking/create' => array(
					'label'=>Yii::t('translations', 'New Trip'),
					'url' => array('booking/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' =>  $this->getSubTab(),
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = 0;//$this->getSubTab();
	
		$model = new Booking();
		if (isset($_POST['Booking']))
		{
			$model->attributes = $_POST['Booking'];		
			/*if(isset($_POST['Booking']['origin']))
			{
				$origin=Codelkups::getCodelkup($_POST['Booking']['origin']);
				$model->travel_supplier=Suppliers::getTravelSupp($origin);
			}*/
			if (!empty($_POST['Booking']['destination']) && $_POST['Booking']['destination']!='')
			{
				$model->destination=Booking::getIdByCityName($_POST['Booking']['destination']);
			}

			if (!empty($_POST['Booking']['id_project']) && !empty($_POST['Booking']['purpose'])  && $_POST['Booking']['purpose']== 'Project' )
				{
					//$model->id_project = implode(",", $_POST['Booking']['id_project']);
					$model->id_project = $_POST['Booking']['id_project'];
				}else
				{
					$model->id_project=null;
				}

			if (!empty($_POST['Booking']['departure_date']))
			{
				$model->departure_date= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['departure_date'])->format('d/m/Y');
			}
			if (!empty($_POST['Booking']['return_date']))
			{
				$model->return_date= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['return_date'])->format('d/m/Y');
			}	
				
			if (!empty($_POST['Booking']['checkin']) && $_POST['Booking']['hotel_booking'] == 'Yes')
			{
				$model->checkin= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['checkin'])->format('d/m/Y');
			//	$model->hotel_supplier=Suppliers::getHotelSuppPerCity($_POST['Booking']['destination']);
			}
			else
			{
				$model->hotel_supplier=null;
				$model->checkin=null;
			}
			if (!empty($_POST['Booking']['checkout']) && $_POST['Booking']['hotel_booking'] == 'Yes')
			{
				$model->checkout= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['checkout'])->format('d/m/Y');
			}
			else
			{
				$model->checkout=null;
			}
			if (!empty($_POST['Booking']['pickup']) && $_POST['Booking']['car_booking'] == 'Yes')
			{
				$model->pickup= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['pickup'])->format('d/m/Y');
			}
			else
			{
				$model->pickup=null;
			}			
			if (!empty($_POST['Booking']['return']) && $_POST['Booking']['car_booking'] == 'Yes')
			{
				$model->return= DateTime::createFromFormat('d/m/Y', $_POST['Booking']['return'])->format('d/m/Y');
			//	$model->car_supplier=Suppliers::getCarSuppPerCity($_POST['Booking']['destination']);
			}
			else
			{
				$model->return=null;
				$model->car_supplier=null;
			}
			$model->addwho=Yii::app()->user->id;
			if ($model->save())
			{	
				if ($model->departure_date ==  $model->return_date)
				{
					self::sendOneDayAlert($model->traveler, $model->id, $model->destination, $model->departure_date, $model->return_date, $model->id_project, $model->billable, $model->origin, $model->id_customer);
				}

				if($model->car_booking == 'No')
				{ $model->car_supplier=null; }
				if($model->hotel_booking == 'No')
				{ $model->hotel_supplier=null; }	

				if(!empty($model->purpose)  && $model->purpose== 'Exhibition')
				{
					$model->id_customer=null;
					$model->id_project=null;
				}
				$model->save();		
				self::sendBookingCreated($model->travel_supplier, $model->traveler, $model->id, $model->destination, $model->departure_date, $model->departure_time, $model->return_date, $model->return_time, $model->id_project, $model->billable, $model->origin, $model->id_customer);
				if(($model->hotel_booking == 'Yes' ) || ($model->car_booking == 'Yes'))
					{
						self::sendBookingsEmail($model->hotel_booking, $model->car_booking, $model->destination, $model->traveler, $model->hotel_supplier, $model->hotelname, $model->reason, $model->checkin, $model->checkout, $model->car_supplier, $model->carname, $model->carreason,$model->pickup, $model->return, $model->id_project);
					}
				Utils::closeTab(Yii::app()->request->url);
				$this->redirect(array('Booking/view', 'id'=>$model->id));
			}else
			{
				if (!empty($_POST['Booking']['destination']))
				{
					$model->destination=$_POST['Booking']['destination'];
				}
				if (!empty($_POST['Booking']['id_project']))
				{	$model->id_project= $_POST['Booking']['id_project'];}
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Deletes the current booking
	 * @param int $id
	 * @author Romeo Onisim
	 */
	public function actionDelete($id)
	{
		$id = (int) $id;
		Yii::app()->db->createCommand("DELETE FROM booking WHERE id='{$id}'")->execute();
	}
}