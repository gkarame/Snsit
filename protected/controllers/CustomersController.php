<?php
Yii::import("xupload.models.XUploadForm");
class CustomersController extends Controller
{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array('accessControl', 'postOnly + delete + deleteContact',	);
	}
	public function init()
	{
		parent::init();
	}
	public function accessRules()
	{
		return array(
			array(
				'allow',
				'actions'=>array('SendEmailLicensingAudit','SendEmailCustomerConnections','SendEmailCustomerSupport','sendEmailCustomerInfo','sendCustomerSatisfaction','checklicensingfile'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array(
						'index','view','create','checkifksa','updatelicencesCust','getless','getAll','update', 'delete', 'manageContact','updateSensitive','updateCustSupp', 'deleteContact', 'manageConnection','uptodateConnection', 'deleteConnection', 
						'download', 'upload','accessFlag','deleteUploadConnFile','customerInfo'
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
	public function actionIndex()
	{
		if (!GroupPermissions::checkPermissions('customers-list'))
		{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$searchArray = isset($_GET['Customers']) ? $_GET['Customers'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/customers/index' => array(
						'label'=>Yii::t('translations', 'Customers'),
						'url' => array('customers/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;	$model = new Customers('search');	$model->unsetAttributes();
		$model->attributes= $searchArray;	$this->render('index',array(	'model'=>$model,	));
	}
	public function actionSendEmailCustomerSupport()	{		
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('no_issues_customers');
		if ($notif != NULL){
			$to_replace = array(
				'{body}',
			);
			$models= Yii::app()->db->createCommand("select c.name, CONCAT(u.firstname, ' ',u.lastname) as account_manager, c.ca, (select max(sdt.date) from support_desk sdt where sdt.id_customer= c.id) as last from customers c, users u
			where c.status=1  and u.id=c.account_manager 
			and ( DATEDIFF(CURRENT_DATE(),(select max(sdt.date) from support_desk sdt where sdt.id_customer= c.id))>90 or  (select max(sdt.date) from support_desk sdt where sdt.id_customer= c.id) is null)
			and c.id in (select customer from maintenance where status= 'Active' AND starting_date<CURRENT_DATE() ) 
			and ((select count(1) from projects where customer_id=c.id) = 0 or (select transition_to_support_date from projects where customer_id=c.id and transition_to_support_date is not null order by transition_to_support_date desc limit 1 ) < (CURRENT_DATE() - interval 1 MONTH))
			order by CONCAT(u.firstname, ' ',u.lastname)")->queryAll();
			$all='Dear All,<br/><br/>Kindly note that ';			
			if(empty($models))	{
				$all.='all customers are logging issues periodically.<br/><br/>';
			}else{				
				$all.='the below customers did not log any issue in the past 3 months:<br/><br/>';
				$all.='<table border="1"  style="font-family:Calibri;border-collapse:collapse;" ><tr><th>Customer</th><th>Account Manager</th><th>CA</th><th>Last Incident Date</th></tr>';
				foreach($models as $model){
					if (isset($model['last'] ) && !empty($model['last'] ))
					{	$f=date('d/m/Y',strtotime($model['last']));  }
					else
					{	$f='';   }
				 	$all.="<tr><td>".$model['name']."</td><td>".$model['account_manager']."</td> <td>".Users::getNamebyID($model['ca'])."</td> <td style='text-align:center'>  ".$f."</td> </tr>";
				}
				$all.="</table><br/>";
			}
			$all.='Best Regards, <br/>SNSit';
			$subject = $notif['name'];
			$replace = array(
					$all,	
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email)	{
				Yii::app()->mailer->AddAddress($email);
			}
			//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);		
		}			
		echo $body;
	}
	public function actionSendEmailCustomerInfo(){
		$notif = EmailNotifications::getNotificationByUniqueName('cust_info');	$subject = 'Missing Customer Info';	$txt='';
    	if ($notif != NULL) 
    	{
    		
    		$customers=	Yii::app()->db->createCommand("select c.id as id, c.name as name, case when c.bill_to_contact_person is null then 'Bill to contact person' end as field1,
	case when c.bill_to_address is null then 'Bill to Address' end as field2, case when c.bill_to_contact_email is null then 'Bill to Contact Email' end as field3,
	case when (c.erp is null and c.id in (SELECT DISTINCT(customer) from maintenance where status='Active')) then 'ERP' end as field4,
	case when (c.account_manager is null and c.id in (SELECT DISTINCT(customer) from maintenance where status='Active')) then 'Account Manager' end as field5,
	case when (c.id_assigned is null and c.id in (SELECT DISTINCT(customer) from maintenance where status='Active')) then 'Account Management' end as field6,
	case when (c.cs_representative is NULL and c.id in (SELECT DISTINCT(customer) from maintenance where status='Active')) then 'CS Representative,' end as field7,
	case when (c.ca is NULL and c.id in (SELECT DISTINCT(customer) from maintenance where status='Active')) then 'CA' end as field8
	from customers c
	where (c.bill_to_contact_person is null or c.bill_to_address is NULL or c.bill_to_contact_email is NULL)
	OR ((c.cs_representative is NULL or c.ca is NULL or c.account_manager is NULL or c.id_assigned is NULL or c.erp is NULL ) and c.id in (SELECT DISTINCT(customer) from maintenance where status='Active'))")->queryAll();
			if(empty($customers))
			{
				$txt.='<br/>None of the customers in SNS It! have missing information. <br/>';
			}else{
				$txt.='<br/>Kindly note that the below customers have missing information on their profiles:<br/><ul>';
				foreach ($customers as $cust) {
					$txt.='<li><a href="'.Yii::app()->createAbsoluteUrl('customers/view', array('id'=>$cust['id'])).'">'.$cust['name'].'</a>';
					$flag=0;
					for ($x = 1; $x <= 8; $x++) {    							
						if ($cust['field'.$x] !=null)	{
							if($flag == 0){	$flag=1; }
							if($flag == 2){ $txt.=', '; }
							if($flag == 1){ $txt.='(';	$flag=2; }	
								$txt.= $cust['field'.$x];
							}
					} 
					if($flag == 2){ $txt.=')';	}	
					$txt.='</li>';	
				}
				$txt.='</ul>';
			}
			$to_replace = array('{body}'); $replace = array($txt);
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email) 
			{
				if (!empty($email)){
			 Yii::app()->mailer->AddAddress($email);
				 }
			}
			//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
			Yii::app()->mailer->Subject  = $subject; Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);

		}
	}
	public function actionSendEmailCustomerConnections(){

		$notif=EmailNotifications::getNotificationByUniqueName('customer_no_connection');
		Yii::app()->mailer->ClearAddresses();
		$result= Yii::app()->db->createCommand("SELECT c.name from customers c where c.support_weekend!='N/A' AND c.support_weekend!='' and c.id not in (select con.id_customer from connections con)")->queryColumn();
		$customer_names="<br />";
		foreach ($result as &$value) {	$customer_names.=$value."<br />";	}
		if ($notif != NULL) {
			$subject='Customers With Support But No Connections';
			$to_replace = array('{customer_names}'); $replace = array($customer_names);
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email) 
			{
				if (!empty($email))	{ Yii::app()->mailer->AddAddress($emails); }
			}
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){	echo "sent"; }    			
    	}
	}

	public function actionchecklicensingfile(){
		$webroot = Yii::getPathOfAlias('webroot');
		$path =  $webroot . DIRECTORY_SEPARATOR . 'snifflicenses';// . DIRECTORY_SEPARATOR . 'Licensing.txt';
		$files = glob($path.'/*.{txt}', GLOB_BRACE);
		
		foreach($files as $file)
		{    
		  $str=file_get_contents($file);
		  $pieces = explode(",", $str);
		  if (!empty($pieces[0]) && !empty($pieces[1]))
		  {
		  	$last_audit=date("d/m/Y");
			Yii::app()->db->createCommand("UPDATE customers SET  n_licenses_audited= '".trim($pieces[1])."', date_audit='".$last_audit."' WHERE id = ".trim($pieces[0])."")->execute();
			Yii::app()->db->createCommand("insert into history_licenses (customer, licenses, date_audit) values (".trim($pieces[0]).",'".trim($pieces[1])."',  '".$last_audit."')")->execute();
			unlink($file);
		  }
		}
	}

	public function actionsendCustomerSatisfaction(){
		$notif=EmailNotifications::getNotificationByUniqueName('customer_satisfaction');$pssat='';
		Yii::app()->mailer->ClearAddresses();
		$result= Yii::app()->db->createCommand(" select * from (
			SELECT IFNULL(T1.count,'0') as thismonth, T2.count as year, T2.rate from 
			(select count(distinct sd.id )as count  ,sd.rate from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.`status`='3' and MONTH(sd.rate_date)=MONTH(CURDATE()) and YEAR(sd.rate_date)=YEAR(CURDATE()) group by  sd.rate order by  sd.rate   desc) AS T1
 				LEFT JOIN 
			(select count(distinct sd.id )as count  ,sd.rate from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.`status`='3' and sd.rate_date>DATE_SUB(CURDATE(),INTERVAL 12 MONTH)group by  sd.rate order by  sd.rate  desc) AS T2
				ON T1.rate=T2.rate
			UNION 
			SELECT IFNULL(T1.count,'0') , T2.count, T2.rate from 
			(select count(distinct sd.id )as count  ,sd.rate from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.`status`='3' and MONTH(sd.rate_date)=MONTH(CURDATE()) and YEAR(sd.rate_date)=YEAR(CURDATE()) group by  sd.rate order by  sd.rate   desc) AS T1
 				RIGHT JOIN 
			(select count(distinct sd.id )as count  ,sd.rate from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.`status`='3' and sd.rate_date>DATE_SUB(CURDATE(),INTERVAL 12 MONTH)group by  sd.rate order by  sd.rate  desc) AS T2
			ON T1.rate=T2.rate ) a order by a.rate desc")->queryAll();
		$totalthismonth= Yii::app()->db->createCommand("select count(distinct sd.id )as count from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.`status`='3' and MONTH(sd.rate_date)=MONTH(CURDATE()) and YEAR(sd.rate_date)=YEAR(CURDATE())")->queryScalar();
		$totallastyear= Yii::app()->db->createCommand("select count(distinct sd.id )as count from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sdc.`status`='3' and sd.rate_date>DATE_SUB(CURDATE(),INTERVAL 12 MONTH)")->queryScalar();
		$totalsatisfied=0;	$totalsatisfiedlastyear=0;	$cssat="<div style='list-style-type:none' >";	
		foreach ($result as $value) {
			if($totalthismonth==0)	{ $totalthismonth=1;}
			$average=($value['thismonth']*100)/$totalthismonth;	$lastyearaverage=($value['year']*100)/$totallastyear;
			if($value['rate']=='5' || $value['rate']=='4' ){	$totalsatisfied+=$average;	$totalsatisfiedlastyear+=$lastyearaverage;	}
			$cssat.="<br/> <img height='10' src='http://snsitnew.sns-emea.com/images/".$value['rate']."stars.png' /> &nbsp;  <b>".$value['thismonth']." &nbsp;(".Utils::formatNumber($average,2)."%) </b> - Avg. Last 12 Mo: <b>".Utils::formatNumber($lastyearaverage,2)."%</b> </li>";
		}
		$cssat.="</div>";
		$cssat.="<br/> Total satisfied: <b>".Utils::formatNumber($totalsatisfied,2)."%</b> - Avg. Last 12 Mo: <b>".Utils::formatNumber($totalsatisfiedlastyear,2)."%</b> "	;
		
		$resultps= Yii::app()->db->createCommand("select sum(a.count)as sum, a.id_project as project , a.surv_type  from (select count(rate) as count, sr.id_project , sr.rate ,ss.surv_type   from surveys_results sr , surveys_status ss where sr.rate<>0 and sr.id_project= ss.id_project and ss.surveys_submitted='1' and sr.surv_type=ss.surv_type  and MONTH(ss.surveys_submitted_date)=MONTH(CURDATE()) and YEAR(ss.surveys_submitted_date)=YEAR(CURDATE()) group by sr.rate ,sr.id_project  order by sr.id_project asc , sr.rate desc	) a where a.rate in('5','4') group by a.id_project ,a.surv_type ")->queryAll();
		if(!empty($resultps)){
			$pssat="2- Project Surveys :<br/><div style='list-style-type:none'>";
		foreach ($resultps as $val) {
			$surv_type=$val['surv_type']=='close'?'Closure':'Intermediate';
			$pssat.="<br/> ".Customers::getNameById(Projects::getCustomerByProject($val['project']))." - ".$surv_type." - <b>".Projects::getNamebyID($val['project'])."</b> - <b>".($val['sum']*10)."% </b> Satisfied </li>";
		}
		$pssat.="</div>";
		}
		if ($notif != NULL) {
			$months="<b>".date('M')."</b>";	$subject='Customers Satisfaction Snapshot for the month of '.date('M').' ';
			$to_replace = array('{cssat}',	'{month}', '{pssat}');
			$replace = array($cssat,$months	,$pssat);
			$body = str_replace($to_replace, $replace, $notif['message']);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email) {	if (!empty($email)) {	Yii::app()->mailer->AddAddress($email); 
			} }		
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){ echo "sent"; }    			
    		}
	}	
	public function actionSendEmailLicensingAudit()
	{
		$subject='Licensing Monthly Report';		
		$notif = EmailNotifications::getNotificationByUniqueName('license_audit_notification');
		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
		Yii::app()->mailer->ClearAddresses();	$body=$notif['message'];
		$emails_group = Yii::app()->db->createCommand("SELECT email from user_personal_details upd, user_groups ug , email_notifications_groups eng where upd.id_user= ug.id_user and ug.id_group=eng.id_group and eng.id_email_notification='36'")->queryColumn();
    	foreach ($emails_group as $email_c)
    	{
if (filter_var($email_c, FILTER_VALIDATE_EMAIL)){	Yii::app()->mailer->AddAddress($email_c);	}
    	}
    		//		Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
    	$licesing = Yii::app()->db->createCommand("select id , date_audit from customers c  where c.id not in ('223','37','218','217','135','169','221','149','150','237','227','165','247','208','84','177','180','273') and c.status=1 and  c.date_audit is not null and c.date_audit<>' ' and c.date_audit < DATE_SUB(NOW(), INTERVAL 4 MONTH)  and (select count(1) from maintenance m where m.customer= c.id  and m.product in ('64','394','395') and m.status='Active' )> 0  and DATEDIFF(now(),IFNULL(DATE_FORMAT(STR_TO_DATE(c.date_audit, '%d/%m/%Y'), '%Y-%m-%d'),now()))>122  order by DATE_FORMAT(STR_TO_DATE(c.date_audit, '%d/%m/%Y'), '%Y-%m-%d') asc")->queryAll();
		$customer = '<ul>';
		foreach ($licesing as $lic) {
			 	$dat=SUBSTR($lic['date_audit'],0, 10);
			 		if(count($licesing)!='0'){
						$customer_name=Customers::getNamebyID($lic['id']);
						$licensing_audited=$customer_name.' - '.$dat;
						$customer.='<li>'.$licensing_audited.' </li>';
			}
		}	
		$customer .= '</ul>';
		$select ="select  c.name , IFNULL( c.date_audit ,' ') as lastaudit, c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) as delta  from customers c  
where c.id not in ('223','37','218','217','135','169','221','149','150','237','227','165','247','208','84','177','180','273') and c.status=1 and c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <0 order by delta asc";
	$DELTA= Yii::app()->db->createCommand($select)->queryAll(); $delta_neg = '<ul>';
		foreach ($DELTA as $DELT) { 
			$dat=SUBSTR($DELT['lastaudit'],0, 10);	$customer_name=$DELT['name'];
			$delta_nega=$customer_name.' -  Last Audit Date: '.$dat.' -  Balance: '.$DELT['delta'].'';
			$delta_neg.='<li>'.$delta_nega.' </li>';
		}	
		$delta_neg .= '</ul>';	
		$noauditss = Yii::app()->db->createCommand("select c.id, c.name, c.n_licenses_allowed from customers c  where c.id not in ('223','37','218','217','135','169','221','149','150','237','227','165','247','208','84','177','180','273') and c.status=1 and  c.n_licenses_allowed>0 and (n_licenses_audited='' OR n_licenses_audited=' ' OR n_licenses_audited is NULL) order by c.name asc")->queryAll();
		$noaudit = '<ul>';
		foreach ($noauditss as $lic) {
			$noaudit.='<li>'.$lic['name'].' - Nb# of Allowed Licenses: '.$lic['n_licenses_allowed'].' </li>';
		}	
		$noaudit .= '</ul>';

	$to_replace = array(
							'{delta_neg}',
							'{customer_lic}',
							'{noaudit}'
						);
	$replace = array(
						$delta_neg,
						$customer,
						$noaudit
					);			
	$body = str_replace($to_replace, $replace, $notif['message']);	Yii::app()->mailer->Subject  = $subject;
	Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");	Yii::app()->mailer->Send(true);	
	}
	public function actionCreate()
	{
		if(!GroupPermissions::checkPermissions('customers-list','write'))
		{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/customers/create' => array(
							'label'=>Yii::t('translations', 'New Customer'),
							'url' => array('customers/create'),
							'itemOptions'=>array('class'=>'link'),
							'subtab' =>  $this->getSubTab(),
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = 0;	$model = new Customers;	$model->status = '1';						
		$this->render('manage',array(
			'model'=>$model,
		));
	}
	public function actiongetless(){	Yii::app()->session['limit'] = 3;	}
	public function actiongetAll(){ Yii::app()->session['limit'] = 0; }
	public function actionUpdate($id = null, $new = 0, $view = 0, $active = null)
	{
		if ($id == null) { $isNew = true;	$model = new Customers;	$model->status = '1';	$model->addwho = Yii::app()->user->id;
		} else {	$id = (int) $id;	$model = $this->loadModel($id);	$model->addwho = Yii::app()->user->id; $isNew = false;	}
		if (!$model->isNewRecord) 
		{
			$arr = Utils::getShortText($model->name); $subtab = $this->getSubTab(Yii::app()->createUrl('customers/update', array('id' => $id)));
			if (isset(Yii::app()->session['menu'])) {
				if ($new == 1) 
				{	
					Utils::closeTab(Yii::app()->createUrl('customers/create'));	$this->action_menu = Yii::app()->session['menu'];
				}else {
					if ($view == 1) 
					{
						Utils::closeTab(Yii::app()->createUrl('customers/view', array('id' => $id)));	$this->action_menu = Yii::app()->session['menu'];
					}
				}
			}
			$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/customers/update/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('customers/update', 'id'=>$id),
							'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->name : ''),
							'subtab' =>  $subtab,
							'order' => Utils::getMenuOrder()+1
					)
				)
			))));
			Yii::app()->session['menu'] = $this->action_menu;	$this->jsConfig->current['activeTab'] = $subtab;
		}
		$extra = array();	$contacts = array();	$connections = array();	$valid = true;	$post = false;	
		if (isset($_POST['Customers']))
		{ 
			unset($model->lpo_required);	$oldlicen= $model->n_licenses_audited;	$model->attributes = $_POST['Customers'];
			$updateinvoices = Yii::app()->db->createCommand("UPDATE `invoices` SET id_assigned='".$model['id_assigned']."' WHERE id_customer='".$model['id']."' and id_assigned is null ")->execute();
			if (isset( $_POST['Customers']['industry']))
			{
				$industries= $_POST['Customers']['industry'];
				$x= count($industries);
				$str='';
				foreach ($industries as $industry) {
					$x--;
					$str.=  $industry;
					if ($x>0)
						$str.=',';					
				}
				$model->industry=$str;
			}			
			if (isset($_POST['Customers']['n_licenses_audited']) && $_POST['Customers']['n_licenses_audited']!=0 && $_POST['Customers']['n_licenses_audited']!= $oldlicen) {
			$last_audit=date("d/m/Y");	$model->date_audit= $last_audit;	}	
			if(isset($model->lpo_required) && $model->lpo_required == 1 )
			{	$model->lpo_required = 'Yes';	}else{	$model->lpo_required = 'No'; }
			if(isset($_POST['Customers']['cs_representative'])){  $model->cs_representative = $_POST['Customers']['cs_representative'];	}
			if(isset($_POST['Customers']['ca'])){ $model->ca = $_POST['Customers']['ca'];}
			if(isset($_POST['Customers']['ca'])){ $model->account_manager = $_POST['Customers']['account_manager'];	}
			if(isset($_POST['Customers']['dolphin_aux'])){ $model->dolphin_aux = $_POST['Customers']['dolphin_aux'];	}
			$cuname='<a href="'.Yii::app()->createAbsoluteUrl('customers/update', array('id'=>$model->id)).'">'.$model->name.'</a>';
			if($isNew)
			{
				$notif = EmailNotifications::getNotificationByUniqueName('new_customer'); 
				$to_replace = array(							
								'{customer_name}',
								'{author}');
				$replace = array(
					$cuname,
					Users::getNamebyID($model->addwho));
				$body = str_replace($to_replace, $replace, $notif['message']);
				$emails=EmailNotificationsGroups::getNotificationUsers($notif['id']);	Yii::app()->mailer->ClearAddresses();
				foreach($emails as $email) {	if (!empty($email)){	Yii::app()->mailer->AddAddress($emails); }	}
				$subject= $notif['name'];		Yii::app()->mailer->Subject  = str_replace($to_replace, $replace,$subject);
				Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				Yii::app()->mailer->Send();
			}
			$post = true;
		}		
		if (isset($_POST['Connections']) && is_array($_POST['Connections']))
		{			
			foreach ($_POST['Connections'] as $i => $conn)
			{
				if (!isset($conn['id'])){	$connections[$i] = new Connections;	$connections[$i]->id_customer = 0; } 
				else {	$connections[$i] = Connections::model()->findByPk((int)$conn['id']); }
				$connections[$i]->attributes = $conn;
			}
			$extra['update_connection'] = true;
		}		
		if (isset($_POST['CustomersContacts']) && is_array($_POST['CustomersContacts']))
		{
			foreach ($_POST['CustomersContacts'] as $i => $contact)
			{
				if (!isset($contact['id']))
				{
					$contacts[$i] = new CustomersContacts;	$contacts[$i]->id_customer = 0;
				}else {
					$contacts[$i] = CustomersContacts::model()->findByPk((int)$contact['id']);
				}
				$contacts[$i]->attributes = $contact;
			}
			$extra['update_contact'] = true;
		}		
		if ($post)
		{
			if (!$model->validate()){ $valid = false;	}			
			foreach ($connections as $conn)	{	$valid = $conn->validate() && $valid; }			
			foreach ($contacts as $contact)	{	$valid = $contact->validate() && $valid; }			
			if ($valid)
			{
				if (isset($_POST['Customers']['customer_reference']) && $_POST['Customers']['customer_reference']!=0)
				{
					$model_ref=Customers::model()->findByPk($_POST['Customers']['customer_reference']);	
					$model->bill_to_contact_person=$model_ref->bill_to_contact_person;
					$model->bill_to_address=$model_ref->bill_to_address;
					$model->bill_to_contact_email=$model_ref->bill_to_contact_email;
				}
				if ($model->save())
				{
					$checkreferredto=Yii::app()->db->createCommand("SELECT id FROM customers WHERE customer_reference=".$model->id."")->queryAll();
					if(!empty($checkreferredto))
					{
						
						$allcustomers= "'".implode("','", array_column($checkreferredto, "id"))."'";
						Yii::app()->db->createCommand("UPDATE customers SET  bill_to_contact_person= '".$model->bill_to_contact_person."', bill_to_address='".$model->bill_to_address."', bill_to_contact_email='".$model->bill_to_contact_email."' WHERE id in (".$allcustomers.")")->execute();
			
					}
					if(!empty($model->dolphin_aux))
					{
						$res=Yii::app()->db->createCommand("SELECT auxiliary FROM `dolphin_fields` where id_customer=".$model->id)->queryScalar();
						if(empty($res))
						{
							Yii::app()->db->createCommand("INSERT INTO dolphin_fields (account_no, id_customer, `name`, proj, auxiliary) VALUES('41110', ".$model->id.", '".$model->name."', '".$model->name."', '".$model->dolphin_aux."')")->execute();							
						}else if ($res != $model->dolphin_aux)
						{
							Yii::app()->db->createCommand("UPDATE dolphin_fields SET auxiliary = '".$model->dolphin_aux."' WHERE id_customer = ".$model->id)->execute();
						}
					}

					Yii::app()->session['limit'] = 3;
					if (isset($isNew)) 
					{
						$structure = './uploads/customers/'.$model->id.'/';
						if(!is_dir($structure)){
							mkdir($structure, 0777);
							$folders = array('connections', 'documents', 'eas', 'invoices');
							foreach ($folders as $folder) {	mkdir($structure.$folder, 0777); }
							Yii::app()->db->createCommand("insert into default_tasks (name,id_parent,billable,id_maintenance)VALUES('".$model->name."','828','No',null)")->execute();
						}							
		 			}
					foreach ($connections as $conn)
					{
						if ($conn->isNewRecord) {	$conn->id_customer = $model->id;	}
						$conn->save();
					}
					foreach ($contacts as $contact)
					{
						if ($contact->isNewRecord){	$contact->id_customer = $model->id; }
						$contact->save();
					}					
					if (isset($isNew)) {
						echo json_encode(array('status'=>'saved', 'url'=>Yii::app()->createAbsoluteUrl('customers/update', array('id'=>$model->id, 'new'=>1))));	
					}else {	echo json_encode(array_merge(array('status'=>'saved'), $extra)); }
					Yii::app()->end();
				}
			}else	{
				$validateModels = array($model);
				if (!empty($contacts)) 
					$validateModels[] = $contacts;
				if (!empty($connections)) 
					$validateModels[] = $connections;
				$this->performAjaxValidation($validateModels);
			}
		}		
		if (!Yii::app()->request->isPostRequest) 
		{
			Yii::app()->session['limit'] = 0;	
			$this->render('manage',array(
				'model'=>$model,
				'active' => $active,
			));
		}
	}	
	public function actionView($id, $active = null)
	{
		$model = $this->loadModel($id);	$arr = Utils::getShortText($model->name);
		$subtab = $this->getSubTab(Yii::app()->createUrl('customers/view', array('id' => $id)));		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/customers/view/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('customers/view', 'id'=>$id),
							'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $model->name : ''),
							'subtab' =>  $subtab,
							'order' => Utils::getMenuOrder()+1
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;	$this->jsConfig->current['activeTab'] = $subtab;
		$this->render('view',array(
			'model'=>$model,
			'active' => $active,
		));
	}
	public function actionupdatelicencesCust()
	{
		if(isset($_POST['licenses']) && $_POST['customer']!=0 && !empty($_POST['licenses']))
		{
			$id= (int)$_POST['customer'];	$licenses= $_POST['licenses'];	$last_audit=date("d/m/Y");
			Yii::app()->db->createCommand("UPDATE customers SET  n_licenses_audited= ".$licenses.", date_audit='".$last_audit."' WHERE id = ".$id."")->execute();
			echo json_encode(array('status' => 'success'));	
			exit;
		}else{
			if( !isset($_POST['licenses']) && $_POST['customer']==0 && empty($_POST['licenses']))
			{
				echo json_encode(array('status' => 'failure','msg'=>'Customer and audited licenses number are not set.'));
				exit;
			}
			else if($_POST['customer']==0)
			{
				echo json_encode(array('status' => 'failure','msg'=>'Customer is not set.'));
				exit;
			}else
			{
				echo json_encode(array('status' => 'failure','msg'=>'Audited licenses number is not set.'));
				exit;
			}			
		}
	}
	public function actionupdateSensitive(){ $id= (int)$_POST['id_cust']; $check= $_POST['check'];	$model = $this->loadModel($id);	$model->checkb= $check;	$model->save(); }
	public function actionupdateCustSupp(){ $id= (int)$_POST['id_cust']; $check= $_POST['check'];	$model = $this->loadModel($id);	$model->custsupport= $check;	$model->save(); }
	public function actionManageContact($id = NULL)
    {
    	$unique = false;
    	if ($id == NULL){	$model = new CustomersContacts(); } 
    	else { $id = (int)$id;	$model = CustomersContacts::model()->findByPk($id); }    	
    	if (isset($_POST['update'], $_POST['CustomersContacts']) && $_POST['update'] == 1)
    	{
    		if ($id == NULL) 
    		{
    			$model->attributes = $_POST['CustomersContacts']['new'];
    			if($model->access == 1 )
    			{
    				$model->access = "Yes";	$customer_name=Customers::getNameById($model->id_customer);
					$res=Yii::app()->db->createCommand("SELECT id FROM default_tasks WHERE name='$customer_name' and id_parent='27' and id_maintenance is null")->queryScalar();
					if ($res == false){	
						Yii::app()->db->createCommand("insert into default_tasks (name,id_parent,billable,id_maintenance)VALUES('$customer_name','27','Yes',null)")->execute();
					}	
					$res2=Yii::app()->db->createCommand("SELECT id FROM default_tasks WHERE name='$customer_name' and id_parent='1324' and id_maintenance is null")->queryScalar();
					if ($res2 == false){	
						Yii::app()->db->createCommand("insert into default_tasks (name,id_parent,billable,id_maintenance)VALUES('$customer_name','1324','No',null)")->execute();
					}
    			}
    			if($model->access == "Yes")
    				$unique = Yii::app()->db->createCommand("SELECT id FROM customers_contacts WHERE username = '$model->username' LIMIT 1")->queryScalar();
			} else {
    			$model->attributes = $_POST['CustomersContacts'][$id];
	    		if($model->access == 1 )
    			{
    				$model->access = "Yes";	$customer_name=Customers::getNameById($model->id_customer);
					$res=Yii::app()->db->createCommand("SELECT id FROM default_tasks WHERE name='$customer_name' and id_parent='27' and id_maintenance is null")->queryScalar();
					if ($res == false){	
						Yii::app()->db->createCommand("insert into default_tasks (name,id_parent,billable,id_maintenance)VALUES('$customer_name','27','Yes',null)")->execute();
					}
					$res2=Yii::app()->db->createCommand("SELECT id FROM default_tasks WHERE name='$customer_name' and id_parent='1324' and id_maintenance is null")->queryScalar();
					if ($res2 == false){	
						Yii::app()->db->createCommand("insert into default_tasks (name,id_parent,billable,id_maintenance)VALUES('$customer_name','1324','No',null)")->execute();
					}
    			}else{
    				$model->access = "No"; 	$customer_name=Customers::getNameById($model->id_customer);
					$rest=Yii::app()->db->createCommand("SELECT id FROM customers_contacts WHERE id_customer='$model->id_customer' and access='Yes'")->queryScalar();
					 	if ($rest == false){ 
						Yii::app()->db->createCommand("delete from default_tasks where name='$customer_name' and id_parent='27'")->execute(); 
						Yii::app()->db->createCommand("delete from default_tasks where name='$customer_name' and id_parent='1324'")->execute();}
    			}
	    		if($model->access == "Yes")
    				$unique = Yii::app()->db->createCommand("SELECT id FROM customers_contacts WHERE username = '$model->username' AND id != $id LIMIT 1")->queryScalar();
			}
    		if($unique != false){
				echo json_encode(array('status' => 'unique'));
				exit;
			}
			if($model->password == null && $model->access == "Yes"){
				echo json_encode(array('status' => 'password'));
				exit;
			}
    		if ($model->save()) {
				echo json_encode(array('status' => 'saved'));
				exit;
			}			
    	}    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
		);		
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_contact_form', array(
            	'model'=> $model,
    			'update'=> isset($_POST['update']) && $_POST['update'] == 1,
        	), true, true)));
       exit;
    }    
	public function actionDeleteContact($id){ $id = (int) $id;	Yii::app()->db->createCommand("DELETE FROM customers_contacts WHERE id='{$id}'")->execute();	}
	public function actionuptodateConnection($id){	
    	$model = Connections::model()->findByPk($id); $now = date('Y-m-d H:i:s ', strtotime('now'));
    	Yii::app()->db->createCommand("UPDATE connections SET last_updated = '$now' WHERE id = $id")->execute(); }
	public function actionManageConnection($id = NULL)
    {
    	if ($id == NULL) {	$model = new Connections();	$x=0; } 
    	else {	$id = (int)$id;	$model = Connections::model()->findByPk($id);	$x=1; }    	
    	if (isset($_POST['Connections']) && $x==1)
    	{
    		$model->attributes = $_POST['Connections'][$id]; $now = date('Y-m-d H:i:s ', strtotime('now'));   $model->last_updated=$now;  
    	} else if (isset($_POST['Connections'])){	$now = date('Y-m-d H:i:s ', strtotime('now'));  $model->last_updated=$now;	}
    	if (isset($_POST['update'], $_POST['Connections']) && $_POST['update'] == 1)
    	{
    		if ($id == NULL) {	$model->attributes = $_POST['Connections']['new']; } 
    		else { $model->attributes = $_POST['Connections'][$id];  $now = date('Y-m-d H:i:s ', strtotime('now'));	$model->last_updated=$now; }
   			if ($model->save()) 
   			{
   				if(isset(Yii::app()->session['customers_conn'])){
   					$destination = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$model->id_customer.DIRECTORY_SEPARATOR."connections".DIRECTORY_SEPARATOR.$model->id.DIRECTORY_SEPARATOR;
					$src = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$model->id_customer.DIRECTORY_SEPARATOR."connections".DIRECTORY_SEPARATOR.$model->id.DIRECTORY_SEPARATOR;
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
   				unset(Yii::app()->session['customers_conn']);	echo json_encode(array('status' => 'saved'));
				exit;
			}
    	}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_connection_form', array(
            	'model'=> $model,
    			'update'=> isset($_POST['update']) && $_POST['update'] == 1,
    			'id_customer'=> isset($_POST['Conn'])?$_POST['Conn']['id_customer']:$model->id_customer,
        	), true, true)));
       exit;
    }    
	public function actionDeleteConnection($id)
	{
		$conn = Connections::model()->findByPk($id);
		if ($conn===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$conn->delete(); 
		$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$conn->id_customer.DIRECTORY_SEPARATOR.'connections'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
		Utils::deleteFile($dirPath.$conn->file);
		if(is_dir($dirPath))
			rmdir($dirPath);
		echo json_encode(array('status'=>'saved','form2'=> $this->renderPartial('_conn_grid', array(
	            	'model'=> $this->loadModel($conn->id_customer),	    			
        		), true,true)));	 
	}	
	public function actionDownload($id) 
	{	
		$model = Connections::model()->findByPk((int)$id);
		if (($path = $model->getFile(true)) !== null) 
		{
			$name = pathinfo($path, PATHINFO_BASENAME);	header('Content-disposition: attachment; filename='.$name);
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			if ($extension == 'pdf') {	header('Content-type: application/pdf');	}
			else {	header('Content-type: application/octet-stream');	}
			//chmod($file, 0777);
			readfile($model->getFile());
		}			
	}
	
	public function actioncheckifksa()
	{
		if(isset($_POST['idcustomer']))
		{
			$name = $_POST['idcustomer'];
			$j = Yii::app()->db->createCommand("select count(*) from  customers WHERE name ='".$name."' and country=113 ")->queryScalar();
			if ($j>0) {	echo json_encode(array('status'=>'success'));	}else	{
				echo json_encode(array('status'=>'failure'));	}
		}
	}
	public function actionAccessFlag(){
		
		if(isset($_POST['id'])){	$value = $_POST['val'];	$id = (int)$_POST['id'];
			$j = Yii::app()->db->createCommand("UPDATE customers_contacts SET access = '{$value}' WHERE id = {$id} ")->execute();
		}
	}
	public function actionDelete($id)
	{
		$id = (int) $id;
		Yii::app()->db->createCommand("DELETE FROM customers_contacts WHERE id_customer='{$id}'")->execute();
		Yii::app()->db->createCommand("DELETE FROM connections WHERE id_customer='{$id}'")->execute();
		Yii::app()->db->createCommand("DELETE FROM eas WHERE id_customer='{$id}'")->execute();
		Yii::app()->db->createCommand("DELETE FROM invoices WHERE id_customer='{$id}'")->execute();
		Yii::app()->db->createCommand("DELETE FROM documents WHERE id_model='{$id}' AND model_table='customers'")->execute();
		Yii::app()->db->createCommand("DELETE FROM customers WHERE id='{$id}'")->execute();	
		Utils::emptyDirectory(Yii::app( )->getBasePath()."/../uploads/customers/{$id}/", true);
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}
	public function loadModel($id)
	{
		$model = Customers::model()->with('cContacts')->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($array)
	{
		$errors = array();
		if (isset($_POST['ajax']) && $_POST['ajax']==='customers-form')
		{
			$result = array();
			foreach ($array as $key=> $model) {
				if (is_array($model)) {	$result = CCustomActiveForm::validateTabular($model, null, true, false);	$errors = array_merge($errors, $result);
					unset($array[$key]);	}
			}			
			$errors = array_merge(CCustomActiveForm::validate($array, null, true, false), $errors);
			if (empty($errors)) 
				echo json_encode(array('status'=>'success'));
			else 
				echo json_encode(array('status'=>'failure', 'errors' => $errors));
			Yii::app()->end();
		}
	}
	public function actionDeleteUploadConnFile()
	{
		if (isset($_GET['model_id'], $_GET['file']))
		{
			$id = (int)$_GET['model_id'];
			if (isset($_GET['id_customer']))
			{
				$customer = (int)$_GET['id_customer'];
				$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$customer.DIRECTORY_SEPARATOR.'connections'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
				Utils::deleteFile($dirPath.$_GET['file']);
				$query = "UPDATE `connections` SET file='' WHERE id='$id'";
				Yii::app()->db->createCommand($query)->execute();
			}else {	unset(Yii::app()->session['customers_conn']); }			
		}
	}

	public function actionCustomerInfo(){
	    if (isset($_GET['customer_id'])){
	        $customer = Customers::model()->findByPk((int)$_GET['customer_id']);
	        echo CJSON::encode($customer);
        }else{
	        echo null;
        }
    }
}?>