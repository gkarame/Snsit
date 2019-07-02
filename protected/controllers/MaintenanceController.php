<?php
class MaintenanceController extends Controller{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
		);
	}
	public function accessRules(){
		return array(
			array(
				'allow',
				'actions'=>array('sendEndofContractReminder','sendContractswithoutstart','sendServicesSummary','checkAnniversary'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('generate'),
				'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(
						'upload','getServicesGrid','index','view','create','accessFlag','updatefields','updatefieldsItem','update', 'delete','updateHeader','manageItem','newItem',
						'deleteItem','inputDate','inputInv','inputInvItems','deactivateitems','barChart','DeleteService', 'lineChart','manageService','changeInput',
						'changeInput2','deleteUpload','createInvMaintItem','createInvMaint','GetExcel2','getExcel'
				),
				'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actions(){
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.CustomXUploadAction',
				 'path' => Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'maintenance'
			 ),
		 );
	}
	public function actionView($id){
		if(!GroupPermissions::checkPermissions('financial-maintenance'))	{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$id = (int) $id;
		$model = $this->loadModel($id);
		$subtab = $this->getSubTab(Yii::app()->createUrl('maintenance/view', array('id' => $id)));
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/maintenance/view/'.$id => array(
					'label'=> 'Contract #'.$model->id_maintenance,
					'url' => array('maintenance/view', 'id' => $model->id_maintenance),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1,
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		Yii::app()->clientScript
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/globalize.min.js',CClientScript::POS_HEAD)
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/dx.chartjs.js',CClientScript::POS_HEAD)
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-core.js',CClientScript::POS_HEAD) //<!-- required -->
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-viz-core.js',CClientScript::POS_HEAD) //<!-- required -->
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-viz-charts.js',CClientScript::POS_HEAD);//<!-- dxChart -->
		$this->jsConfig->current['activeTab'] = $subtab;
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'data'=>"19914"
		));		
	}
	public function actionCreate(){
		if (!GroupPermissions::checkPermissions('financial-maintenance','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/maintenance/create' => array(
					'label'=> 'Add Contract',
					'url' => array('maintenance/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model=new Maintenance();
		if (isset($_POST['Maintenance']))	{
			$model->attributes=$_POST['Maintenance'];
			$model->end_customer=$model->customer;
			if($model->starting_date != null && trim($model->starting_date) != '' && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['Maintenance']['starting_date']))
			{
				$model->starting_date = DateTime::createFromFormat('d/m/yy', $_POST['Maintenance']['starting_date'])->format('Y-m-d');
			}else if ($model->starting_date != null && $model->starting_date != '' && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['Maintenance']['starting_date'])) {
				$model->starting_date=$_POST['Maintenance']['starting_date'];
			}else{
				$model->starting_date=null;
			}
			if (!empty($model->sns_share))
			{
				$model->sns_share= str_replace("%","",$model->sns_share);
			}else{
				$model->sns_share = 100;
			}
			if (!empty($model->net_share))
			{
				$model->net_share= str_replace("%","",$model->net_share);
			}else{
				$model->net_share = 100;
			}
			if (!empty($model->escalation_factor))
			{
				$model->escalation_factor= str_replace("%","",$model->escalation_factor);
				$model->real_esc = $model->escalation_factor;
			}
			$rate = CurrencyRate::getCurrencyRate((int)$model->currency);
			if(!empty($_POST['Maintenance']['currency'])){
				$model->amount = $rate['rate'] * $model->original_amount;
				$model->currency_rate_id = $rate['id'];
			}
			if ($model->save())	{	
				if ($model->status == "Active") {
					Yii::app()->db->createCommand("INSERT INTO default_tasks (name, id_parent, billable,id_maintenance) VALUES ('".$model->customer0->name."',".Maintenance::SUPPORT_TASK.",'"."YES"."',".$model->id_maintenance.")" )->execute();
				}
			if($model->support_service=='501'||$model->support_service=='502'){
						$services=Yii::app()->db->createCommand("select id , field_type, access, default_value from support_services where type='".$model->support_service."' ")->queryAll();
						foreach ($services as  $value) {
							Yii::app()->db->createCommand("INSERT INTO maintenance_services (id_contract, id_service, `limit`, access, field_type ) VALUES ('".$model->id_maintenance."' , '".$value['id']."' , '".$value['default_value']."' ,'".$value['access']."','".$value['field_type']."') ")->execute();
						}				
					}
				Utils::closeTab(Yii::app()->createUrl('maintenance/create'));
				$this->action_menu = Yii::app()->session['menu'];
				$this->redirect(array('maintenance/view', 'id'=>$model->id_maintenance, 'new' => 1));
			}
		}
		$this->render('create',array(
			'model'=>$model,
			'starting_date'=>isset($_POST['Maintenance']['starting_date'])?$_POST['Maintenance']['starting_date']:"",
		));
	}
	/*
	 * Author: Mike
	 * Date: 17.06.19
	 * Fix Date picker on edit header display 1970 in case the starting date is empty
	 */
	public function actionUpdateHeader($id){
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		$model->customer_name = $model->customer0->name;
		if (isset($_POST['Maintenance']))
		{
			if (empty($_POST['Maintenance']['sma_recipients'])  && $_POST['Maintenance']['support_service'] == '501' && $model->customer0->id !=101)	{
				echo json_encode(array('status' => 'fail','errormsg'=>'Contract plan is Elite, must set SMA recipients'));
						exit; 
			}
			if (empty($_POST['Maintenance']['sma_instances'])  && $_POST['Maintenance']['support_service'] == '501' && $model->customer0->id !=101)	{
				echo json_encode(array('status' => 'fail','errormsg'=>'Contract plan is Elite, must set SMA instances'));
						exit; 
			}
			if ($_POST['Maintenance']['support_service'] == '501'  && $model->customer0->id !=101){
				if(isset($_POST['Maintenance']['sma_recipients'])){
					$emails = explode(",", $_POST['Maintenance']['sma_recipients']);
					foreach ($emails as $key => $email) {
						if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	{
		        			echo json_encode(array('status' => 'fail','errormsg'=>'Email '.$email.' is not valid'));
							exit; 
					    }					    
					}
				}
			}			
			$model->attributes = $_POST['Maintenance'];
			$rate = CurrencyRate::getCurrencyRate((int)$model->currency);
			if(isset( $_POST['Maintenance']['original_amount'])){
				if(!empty($rate)){
						$model->amount = $rate['rate'] * $model->original_amount;
						$model->currency_rate_id = $rate['id'];
					}
				}
				//print_r($_POST['Maintenance']['starting_date']);exit;
				if ($_POST['Maintenance']['starting_date']!=null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['Maintenance']['starting_date'])){

			$model->starting_date = DateTime::createFromFormat('d/m/Y', $_POST['Maintenance']['starting_date'])->format('Y-m-d');
		}else if ($_POST['Maintenance']['starting_date']!=null  && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['Maintenance']['starting_date']))
		{
			$model->starting_date =$_POST['Maintenance']['starting_date'];
		}else{
			$model->starting_date =null;
		}
			if($model->starting_date !=null && $model->starting_date == '1970-01-01' )
		{
			$model->starting_date =null;
		}
		if($_POST['Maintenance']['support_service'] != '501' && $_POST['Maintenance']['support_service'] != '502')
		{
			$model->sma_instances = null; $model->sma_recipients = null; 
		}
		 if (!empty($model->sns_share))
			{
				$model->sns_share= str_replace("%","",$model->sns_share);
			}
			if (!empty($model->escalation_factor))
			{
				$model->escalation_factor= str_replace("%","",$model->escalation_factor);
			}
			if (!empty($model->real_esc))
			{
				$model->real_esc= str_replace("%","",$model->real_esc);
			}
			if (!empty($model->net_share))
			{
				$model->net_share= str_replace("%","",$model->net_share);
			}else{
				$model->net_share = 100;
			}
			if(isset($model->support_service)){
				$maintenance_services=Yii::app()->db->createCommand("select count(1) from maintenance_services where id_contract='".$model->id_maintenance."' ")->queryScalar();
				if($maintenance_services==0){
					if($model->support_service=='502'||$model->support_service=='501'){						
						$services=Yii::app()->db->createCommand("select id ,default_value, field_type, access from support_services where type='".$model->support_service."' ")->queryAll();
						foreach ($services as  $value) {
							Yii::app()->db->createCommand("INSERT INTO maintenance_services (id_contract, id_service, `limit`,  access, field_type) VALUES ('".$model->id_maintenance."' , '".$value['id']."' ,'".$value['default_value']."' ,'".$value['access']."' ,'".$value['field_type']."') ")->execute();
						}				
					}
				}				
			}
			if(!empty($model->ea) && !empty($model->customer0->id))
			{
				if(!Eas::validateMaintenanceEa($model->ea,$model->customer0->id ))
				{
					echo json_encode(array('status' => 'fail','errormsg'=>'EA# is not valid'));
							exit; 
				}
			}else if (empty($model->ea) && $model->id_maintenance> 322 && $model->status == 'Active')
			{
				echo json_encode(array('status' => 'fail','errormsg'=>'EA# must be specified'));
							exit; 
			}

			if ($model->save()){
					$total['total_periodic_amount'] = Utils::formatNumber($model->getTotalPeriodicAmountUsd());
   					$total['total_gross_amount'] = Utils::formatNumber($model->getTotalGrossAmountUsd());
					$total['total_net_amount'] = Utils::formatNumber($model->getTotalNetAmountUsd());
					$total['total_partent_amount'] = Utils::formatNumber($model->getTotalPartnerAmountUsd());
					if ($model->status == "Inactive") {
						Yii::app()->db->createCommand("DELETE FROM default_tasks WHERE id_maintenance='".$model->id_maintenance."' AND id_parent='".Maintenance::SUPPORT_TASK."'")->execute();
					} else {
						Yii::app()->db->createCommand("INSERT INTO default_tasks (name, id_parent, billable,id_maintenance) VALUES ('".$model->customer0->name."',".Maintenance::SUPPORT_TASK.",'"."YES"."',".$model->id_maintenance.")" )->execute();
					}
						echo json_encode(array_merge(array(
							'status' => 'saved',
							'total' => $total,
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
				'starting_date'=>isset($model->starting_date)?date('d/m/Y', strtotime($model->starting_date)):null,
				'html'=>$this->renderPartial('_edit_header_content', array('model'=> $model), true, true)
		), $extra));
		Yii::app()->end();
	}
	public function actionUpdate($id, $new = 0){
		$model=$this->loadModel($id);
		if (isset(Yii::app()->session['menu']) && $new == 1) {
				Utils::closeTab(Yii::app()->createUrl('maintenance/create'));
				$this->action_menu = Yii::app()->session['menu'];
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/maintenance/update/'.$id => array(
						'label'=> 'CONTRACT #'.$model->id_maintenance,
						'url' => array('maintenance/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => $subtab,
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		if (isset($_POST['Maintenance'])){
			$model->attributes=$_POST['Maintenance'];
			if ($model->save())
				$this->redirect(array('view','id'=>$model->id_maintenance));
		}
		$this->render('update',array(
			'model'=>$model,
		)); 
	}
	public function actionManageItem($id = NULL)  {
    	if (!isset($_POST['id_maintenance']))
    		exit;

    	$new= false;
    	if ($id == NULL)	{
    		$model = new MaintenanceItems();
    		$new=true;
  			$model->id_contract = (int)$_POST['id_maintenance'];
  			if(isset($_POST['currency']))
  				$model->currency = (int)$_POST['currency'];
    	} else 	{
    		$id = (int)$id;
    		$model = MaintenanceItems::model()->findByPk($id);
    	}
    	$j = $model->maintenance0;
   		if (isset($_POST['MaintenanceItems']))	{
   			$orgstate= $model->status;
    		$model->attributes = $_POST['MaintenanceItems'];
    		$model->date = date('Y-m-d');
			$model->sns_share = $model->idContract->sns_share;
    		$rate = CurrencyRate::getCurrencyRate((int)$model->currency);
			if(!empty($rate)){
				$model->amount_usd = $rate['rate'] * $model->amount;
				$model->currency_rate_id = $rate['id'];
			}
			if($model->status == 2 && $orgstate == 1)
			{
				$model->deactivate_date = date('Y-m-d');
			}else if ($model->status == 1 && $orgstate == 2)
			{
				$model->deactivate_date = '';
			}

			if(!empty( $_POST['MaintenanceItems']['from_inv_date'])&& !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",  $_POST['MaintenanceItems']['from_inv_date'])){
					$model->from_inv_date = DateTime::createFromFormat('d/m/Y', $_POST['MaintenanceItems']['from_inv_date'])->format('Y-m-d');
			}
			if(!empty( $_POST['MaintenanceItems']['to_inv_date'])&& !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",  $_POST['MaintenanceItems']['to_inv_date'])){
			
				$model->to_inv_date = DateTime::createFromFormat('d/m/Y', $_POST['MaintenanceItems']['to_inv_date'])->format('Y-m-d');
			}
			if((!empty( $_POST['MaintenanceItems']['from_inv_date']) && !empty($model->from_inv_date)) || (!empty( $_POST['MaintenanceItems']['to_inv_date']) && !empty($model->to_inv_date)) ){
				 if ( !empty($model->from_inv_date) && (empty($model->to_inv_date)  || trim($model->to_inv_date) == '')){
				 	echo json_encode(array('status' => 'errorinv','errormsg' => 'To Inv. Date must be specified'));
						exit;
				 }
				 if ( !empty($model->to_inv_date) &&  (empty($model->from_inv_date) || trim($model->from_inv_date) == '') ){
				 	echo json_encode(array('status' => 'errorinv','errormsg' => 'From Inv. Date must be specified'));
						exit;
				 }
				if($model->to_inv_date < $model->from_inv_date)
				{
					echo json_encode(array('status' => 'errorinv','errormsg' => 'From Inv. Date cannot be after To Inv. Date'));
						exit;
				}
			}

   			if ($model->save()) {
	   			if($new && !empty($model->from_inv_date) && !empty($model->to_inv_date))
	   			{
					self::insertInv($model->from_inv_date, $model->to_inv_date, $model->id);
	   			}	
   				$total['total_periodic_amount'] = Utils::formatNumber($j->getTotalPeriodicAmountUsd());
   				$total['total_gross_amount'] = Utils::formatNumber($j->getTotalGrossAmountUsd());
				$total['total_net_amount'] = Utils::formatNumber($j->getTotalNetAmountUsd());
				$total['total_partent_amount'] = Utils::formatNumber($j->getTotalPartnerAmountUsd());				
				echo json_encode(array('status' => 'saved','total'=>$total));
				exit;
			}
			if(!empty( $_POST['MaintenanceItems']['from_inv_date']))
			{
				$model->from_inv_date= $_POST['MaintenanceItems']['from_inv_date'];
			}
			if(!empty( $_POST['MaintenanceItems']['to_inv_date']))
			{
				$model->to_inv_date=   $_POST['MaintenanceItems']['to_inv_date'];
			}
    		
    	}
    	
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form', array(
            	'model'=> $model,
    			//'labels' => Eas::getItemsLabelsByCategory($ea->category),
        	), true, true)));
       exit;
    }
    public function actionDeleteItem($id){		
		$item = MaintenanceItems::model()->findByPk($id);
		if ($item===null)
			throw new CHttpException(404,'The requested page does not exist.');
		if ($item->delete()) {
				$model = $item->maintenance0;
				$total['total_periodic_amount'] = Utils::formatNumber($model->getTotalPeriodicAmountUsd());
   					$total['total_gross_amount'] = Utils::formatNumber($model->getTotalGrossAmountUsd());
					$total['total_net_amount'] = Utils::formatNumber($model->getTotalNetAmountUsd());
					$total['total_partent_amount'] = Utils::formatNumber($model->getTotalPartnerAmountUsd());
				echo json_encode(array('status' => 'success','total'=>$total));
		}
	}
	public function actionGenerate(){
		$number = 0;
		$to_period ;
		$allContracts = Yii::app()->db->createCommand()
			->select('*')
			->from('maintenance')
			->where(' sns_share>0 and status =:status', array(':status'=>'Active'))
    		->queryAll();    	
    	foreach ($allContracts as $contract){
    		$inv=0;
    		$model = $this->loadModel($contract['id_maintenance']);
    		$result = MaintenanceInvoices::getLastInvoicebyDate($contract['id_maintenance']);
	    	if ($result !== false){
	    		$aux =$result['to_period'];
	    		$to_period = date('d-m-Y', strtotime( "$aux + 1 day" )); 
	    	}else{
	    	 	if (!(empty($contract['starting_date']))){
		    		$to_period = $contract['starting_date'];
		    		$no_privious_invoices = 1;
	    		}else {
	    			$inv=1; 
	    		}
	    	}
	    	if ($inv == 0){
		    	$month = MaintenanceInvoices::getMonth($to_period);
	    		$escalation_factor=$contract['real_esc'];
		    	if ($month)	{
		    		if (isset($no_privious_invoices)){
		    			$from_date = $to_period;
		    		}else{
		    			$from_date = date('d-m-Y', strtotime( "$to_period" ));
		    		}
		    		$totgrossamount=$model->getTotalGrossAmountFromlastINV();
		    		$frequency = Maintenance::getFrequency($contract['frequency']);
		    		$to_date = date('Y-m-d', strtotime( "$from_date + $frequency month - 1 day" ));
		    		$periodfreq = Maintenance::getPeriodFreq($contract['frequency']);	    		
		    		$additional_desc = MaintenanceItems::getDescription($contract['id_maintenance']);
		    		$ea= "NULL";
		    		if(!empty($contract['ea']) && $contract['ea']!=0 )
		    		{
		    			$ea= "'".$contract['ea']."'";
		    		}

		    		if (!empty($escalation_factor) && $escalation_factor!='0'){
		    			$totgrossamount = $totgrossamount + ($totgrossamount*$escalation_factor/100);
		    		//	$invoice_description = Customers::getNameById($contract['customer'])." - ".$contract['contract_description']." -".$additional_desc." + ".$escalation_factor."% Escalation - From period ".$from_date." To ".$to_date;
			    			    			

		    		}/*	else	{*/
		    			if(!empty($additional_desc))
		    			{
		    				$additional_desc= substr($additional_desc, 0, -1);
		    				$invoice_description = Customers::getNameById($contract['customer'])." - ".$contract['contract_description']." - ".$additional_desc." - From period ".$from_date." To ".$to_date;
		    			}else{
		    				$invoice_description = Customers::getNameById($contract['customer'])." - ".$contract['contract_description']." - From period ".$from_date." To ".$to_date;
		    			}
			    			
			    	//}

			    	if(MaintenanceInvoices::getLastInvoice($contract['id_maintenance']) !== false)
			    	{
						$net_amount =($totgrossamount*$contract['sns_share']/100);
						$partner_amount = ($totgrossamount*(1-$contract['sns_share']/100));
						$gross_amount = ($totgrossamount);	
			    	}else{
			    		$net_amount =($totgrossamount*$contract['sns_share']/100)/$periodfreq;
						$partner_amount = ($totgrossamount*(1-$contract['sns_share']/100))/$periodfreq;
						$gross_amount = ($totgrossamount)/$periodfreq;	
			    	}

		    		$amount = $totgrossamount;
		    		$invoice_number = "00000";
		    		$assigned= Customers::getIdAssigned($contract['customer']);
		    		if(empty($assigned)){

		    			Yii::app()->db->createCommand("INSERT INTO invoices(escalation, id_ea ,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
							partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date) 
							VALUES (".$escalation_factor.",".$ea.",'".$contract['customer']."','".$invoice_number."','".$invoice_description."',".$contract['currency'].",
							'1/1', 100,".$contract['owner'].",".$contract['sns_share'].",".$net_amount.",".$gross_amount.",".$partner_amount.",
							".$amount.",".date('m',strtotime("-1 month")).",".date('Y',strtotime("-1 month")).",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00' )")->execute();
		    		}else{
		    			Yii::app()->db->createCommand("INSERT INTO invoices(escalation,id_ea ,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
							partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date, id_assigned) 
							VALUES (".$escalation_factor.",".$ea.",'".$contract['customer']."','".$invoice_number."','".$invoice_description."',".$contract['currency'].",
							'1/1', 100,".$contract['owner'].",".$contract['sns_share'].",".$net_amount.",".$gross_amount.",".$partner_amount.",
							".$amount.",".date('m',strtotime("-1 month")).",".date('Y',strtotime("-1 month")).",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00',".$assigned." )")->execute();
		    		}
		    		$id_inv = Yii::app()->db->getLastInsertId();
		    		$invoice_number = Utils::paddingCode($id_inv);
					if($invoice_number == "99999")
						$invoice_number = "00000";
					Yii::app()->db->createCommand("UPDATE invoices SET invoice_number ='$invoice_number' WHERE id = '$id_inv'")->execute();
					$from_date = date('Y-m-d', strtotime($from_date));
		    		Yii::app()->db->createCommand("INSERT INTO maintenance_invoices(id_contract, id_invoice, amount,from_period, to_period, escalation_factor) VALUES ('".$contract['id_maintenance']."',".$id_inv.",".$totgrossamount.",'".$from_date."','".$to_date."','".$escalation_factor."')")->execute();
		    	//	print_r("INSERT INTO maintenance_invoices(id_contract, id_invoice, amount,from_period, to_period, escalation_factor) VALUES ('".$contract['id_maintenance']."',".$id_inv.",".$totgrossamount.",'".$from_date."','".$to_date."','".$escalation_factor."')");exit;
		    	}
	   		}
    	}
	}
    public function actionBarChart($id){
    	$model = Maintenance::model()->findByPk($id);
    	if($model->support_service == '503'){
	    	$values = Maintenance::getAllSupportDesk($model);
	    	$dataset = array();
	    	foreach ($values as $k => $value) {
	    		if ($k>2015){
		    		$data = array();
		    		$support = $value['support'];
					$issues = count($support);
					$hours = Maintenance::getHoursByYear($model->id_maintenance, $k);
					if($hours == null)
						$hours = 0;
					if($issues == null)
						$issues = 0;
				    $data['label'] = ''.$k.'';
				    $data['issues'] =  (double) $issues;
				    $data['hours'] = (double)$hours;
				    array_push($dataset,$data);
				}
	    	}
	    }else{
	    	$values = Maintenance::getYears($model->starting_date);
	    	$dataset = array();
	    	foreach ($values as $value) {
	    		if ($value>2015){
		    		$data = array();
					$hours = Maintenance::getHoursByYearNotStandard($model->id_maintenance, $value);
					if($hours == null)
						$hours = 0;
					$issues = 0;
				    $data['label'] = ''.$value.'';
				    $data['hours'] = (double)$hours;
				    array_push($dataset,$data);
				}
	    	}
    	}
    	echo json_encode($dataset);    	
    }
	public function actionLineChart($id) {    	
    	$model = Maintenance::model()->findByPk($id);
    	if($model->support_service == '503'){
    		$values = Maintenance::getAllSupportDesk($model);
	    	$dataset = array();
	    	if (empty($values)){
	    		$k=date('Y');
	    		$data = array();
			    $data['label']=''.$k.'';
				$rev = $model->getTotalNetAmountYear($k);
				$cos = Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost();
				$pro  = $rev - $cos;
				$data['revenues']= (double)$rev;
				$data['cost'] = (double) $cos;
				$data['profit'] =  (double)$pro;
				array_push($dataset,$data);
	    	}else{
		    	foreach ($values as $k => $value) {
		    		if ($k>2015){
			    		$data = array();
			    		$data['label']=''.$k.'';
			    		$support = $value['support'];
						$rev = $model->getTotalNetAmountYear($k);
						$cos = Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost();
						$pro  = $rev - $cos;
					    $data['revenues']= (double)$rev;
					    $data['cost'] = (double) $cos;
					    $data['profit'] =  (double)$pro;
					    array_push($dataset,$data);
					}
		    	}
	    	}
	    }else{
	    	$values = Maintenance::getYears($model->starting_date);
	    	$dataset = array();
	    	if (empty($values)){
	    		$k=date('Y');
	    		$data = array();
			    $data['label']=''.$k.'';
				$rev = $model->getTotalNetAmountYear($k);
				$cos = Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost();
				$pro  = $rev - $cos;
				$data['revenues']= (double)$rev;
				$data['cost'] = (double) $cos;
				$data['profit'] =  (double)$pro;
				array_push($dataset,$data);
	    	}else{
		    	foreach ($values as  $value) {
		    		if ($value>2015){
			    		$data = array();
			    		$data['label']=''.$value.''; 
						$rev = $model->getTotalNetAmountYear($value);
						$cos = Maintenance::getHoursByYearNotStandard($model->id_maintenance, $value)*SystemParameters::getCost();
						$pro  = $rev - $cos;
					    $data['revenues']= (double)$rev;
					    $data['cost'] = (double) $cos;
					    $data['profit'] =  (double)$pro;
					    array_push($dataset,$data);
					}
		    	}
	    	}
	    }
    	echo json_encode($dataset);    	
    }
	public function actionDelete($id){
		$this->loadModel($id)->delete();
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	public function actionsendContractswithoutstart(){
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('contracts_without_starting_date');
		if ($notif != NULL)	{
			$to_replace = array(
				'{group_description}',
				'{list}',
			);	
			$contracts = '';
			$models=Yii::app()->db->createCommand("select id_maintenance , contract_description, customer FROM `maintenance` where status='Active' and customer!='177' and starting_date is NULL")->queryAll();
			if(empty($models)){
				$contracts.='No contracts need to be updated.<br>';
			}	else{
				$str='(';
				$contracts.='Kindly find below all maintenance contracts without a starting date: <br><br>';
				$contracts.='<table border="1"  style="font-family:Calibri;border-collapse: collapse;">';
	    		$contracts.="<tr><th width='200' >Customer</th><th width='200'>Contract</th><th width='100'>Amount</th> </tr>";	
	    	
	    			$tot=0;
				foreach($models as $model)
				{
					$customer= Customers::getNameById($model['customer']);
					$model = $this->loadModel($model['id_maintenance']);
					$initialamt= $model->getTotalGrossAmountUsd();
					$amount = Utils::formatNumber($initialamt,2);
					$contracts.= '<tr><td width="200">'.$customer.'</td><td width="300">'.$model['contract_description'].'</td><td width="200" style="text-align:right;">'.$amount.' $</td></tr>';	
					$tot= $tot+ 	$initialamt;			
				}
				$contracts.= '<tr>  <td style="text-align:right;" colspan="2" ><b>Total:</b></td><td style="text-align:right;"><b>'.Utils::formatNumber($tot).' $</b></td></tr>';	 
				$contracts.= '</table>';	
			}
			$subject = $notif['name'];
			$replace = array(
					EmailNotificationsGroups::getGroupDescription($notif['id']),
					$contracts,
	
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
	public function actiongetServicesGrid(){
		$contract=$_POST['id'];
		$services= MaintenanceServices::getSupportServicesPerMaint($contract);
		$list='<div class="closepopupwidget" style=\' margin-top:15px;\' onclick="parentNode.classList.add(\'hidden\');"> </div>';		
		$customer=Customers::getNameById(Maintenance::getCustByMaint($contract));
		$plan=Codelkups::getCodelkup(Maintenance::getPlanByMaint($contract));
		$list.="<div style='font-family:Calibri;font-size: 17px;padding-bottom:40px;margin-top:-35px;'><b>".$customer." - ".$plan."</b><br/></div>";
		if (empty($services))	{
			$list.= '<div style=\'font-family:Calibri;font-size: 15px; margin-left:-5px;\'>No services eligibile for Quota/Actuals entry.</div>';
		}	else	{
			$list.="<table style='font-family:Calibri;font-size: 13.5px;margin-top:-30px;margin-left:-5px;'>";
	    	$list.="<tr style='text-align:center; vertical-align:bottom; font-weight:bold'><td width='600' style='height: 16px; ;border-left:1px solid ; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Service</td><td width='12' style='height: 16px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Quota</td><td width='12' style='height: 16px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;''>Actuals</td> </tr>";	
	    	foreach ($services as $service) {
	    		if ($service['id_service'] == 7 || $service['id_service'] == 19) {
	    			$quota= 'Unlimited';	
	    		}else{
	    			$quota= Utils::formatNumber($service['quota']);
	    		}
		    	$list.="<tr style='text-align:center; vertical-align:bottom;'><td width='600' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-left:1px solid  ;border-right:1px solid  ; text-align:left;'>".MaintenanceServices::getNameById($service['id_service'])."</td><td width='12' style='height: 16px; border-top:1.5px solid;border-bottom:1.5px solid ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>".$quota."</td><td width='12' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid  ;border-right:1px solid ;padding-left:10px;padding-right:10px;'>".Utils::formatNumber(MaintenanceServices::getActualExcel($contract,$service['id_service'],$service['field_type']))."</td> </tr>";	
	    	}
	    	$list.='</table></div> ';
		}
    	echo json_encode(array ('servs' =>$list));
	}
	public function actionsendServicesSummary(){
		$notif=EmailNotifications::getNotificationByUniqueName('contract_services');		
		$results= Yii::app()->db->createCommand("select distinct c.id, c.name,m.id_maintenance, m.contract_description, m.support_service from customers c left outer join maintenance m on c.id=m.end_customer left outer join users u on c.ca=u.id left outer join users u2 on c.account_manager=u2.id where  m.status='active'  and c.status='1' and m.starting_date is not null and m.support_service in (501,502) order by name" )->queryAll();
	  	$list="";
		if ($notif != NULL && (!empty($results)) ) {    		
    		$list.='Kindly find below a summary of the active Maintenance contracts and their services:<br />';
    		$list.='<br /><br />';
    		foreach ($results as $result){
    			$list.="<table style='font-family:Calibri' ><tr> <td colspan='4' style='text-align:left;border-top:1.5px solid;padding-left:10px;padding-right:10px'> Customer: ".$result['name']." <br />Contract Description: ".$result['contract_description']." <br />Support Service: ".Codelkups::getCodelkup($result['support_service'])."</td> </tr>";
    			$list.="<tr style='text-align:center; vertical-align:bottom; font-weight:bold'><td width='17' style='height: 20px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-right:1px solid ;border-left:1px solid ;padding-left:10px;padding-right:10px'> Item# </td><td width='350' style='height: 20px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Service</td><td width='20' style='height: 20px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Quota</td><td width='20' style='height: 20px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;''>Actuals</td> </tr>";	
    			$services= MaintenanceServices::getSupportServicesPerMaint($result['id_maintenance']);
				$items=1;
    			foreach ($services as $service) {
    				if ($service['id_service'] == 7 || $service['id_service'] == 19) {
    					$quota= 'Unlimited';	
    				}else{
    					$quota= Utils::formatNumber($service['quota']);
    				}
	    			$list.="<tr style='text-align:center; vertical-align:bottom;'><td width='17' style='height: 20px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-right:1px solid ;border-left:1px solid ;padding-left:10px;padding-right:10px'> ".$items." </td><td width='350' style='height: 20px; border-top:1.5px solid ;border-bottom:1.5px solid  ;border-right:1px solid  ; text-align:left;'>".MaintenanceServices::getNameById($service['id_service'])."</td><td width='20' style='height: 20px; border-top:1.5px solid;border-bottom:1.5px solid ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>".$quota."</td><td width='20' style='height: 20px; border-top:1.5px solid ;border-bottom:1.5px solid  ;border-right:1px solid ;padding-left:10px;padding-right:10px;'>".Utils::formatNumber(MaintenanceServices::getActualExcel($result['id_maintenance'],$service['id_service'],$service['field_type']))."</td> </tr>";	
    				$items++;
    			}    			
    			$list.='</table>';
    			$list.='<br /><br /><br />';
	    	}
	    	$to_replace = array(
					'{body}',
					);			
				$replace = array(
					$list
					);
			$body = str_replace($to_replace, $replace, $notif['message']);
			print_r($body);
			$subject = $notif['name'];
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email) 	{
					if (!empty($email))
						Yii::app()->mailer->AddAddress($emails);
			}					
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){
					echo "sent";				
			}   		
		}
	}
	public function actionsendEndofContractReminder(){
		$notif=EmailNotifications::getNotificationByUniqueName('contract_end_reminder');		
		$results= Yii::app()->db->createCommand("SELECT contract_description, customer, contract_duration,starting_date FROM `maintenance` where contract_duration is not null and contract_duration <> '' and contract_duration <> 'Open Ended' order by starting_date" )->queryAll();
	  	$list="";
	  	$rec=false;
		if ($notif != NULL && (!empty($results)) ) {    		
    		$list.='Kindly note that the Maintenance contracts below will be ending in 3 months:<br />';
    		$list.='<ul>';
    		foreach ($results as $result)	{
    			if ($result['contract_duration'] == "1 Year"){
					$days=Yii::app()->db->createCommand(" select DATEDIFF(('".$result['starting_date']."' + INTERVAL 1 Year),CURRENT_DATE()+ INTERVAL 90 Day) as diff")->queryScalar(); 
	  				if ($days==0)	{
						$list.='<li>Contract:<b> '.$result['contract_description'].'</b> for Customer: <b>'.Customers::getNameById($result['customer']).'</b> - Contract duration: <b>1 Year</b></li>';
	  					$rec=true;
					} 
				}else if ($result['contract_duration'] == "2 Years"){
				  	$days=Yii::app()->db->createCommand(" select DATEDIFF(('".$result['starting_date']."' + INTERVAL 2 Year),CURRENT_DATE()+ INTERVAL 90 Day) as diff")->queryScalar(); 
	  				if ($days==0)	{
						$list.='<li>Contract:<b> '.$result['contract_description'].'</b> for Customer: <b>'.Customers::getNameById($result['customer']).'</b> - Contract duration: <b>2 Years</b></li>';
	  					$rec=true;
					}
				}else if ($result['contract_duration'] == "3 Years"){
				  	$days=Yii::app()->db->createCommand(" select DATEDIFF(('".$result['starting_date']."' + INTERVAL 3 Year),CURRENT_DATE()+ INTERVAL 90 Day) as diff")->queryScalar(); 
	  				if ($days==0)
	  				{
						$list.='<li>Contract:<b> '.$result['contract_description'].'</b> for Customer: <b>'.Customers::getNameById($result['customer']).'</b> - Contract duration: <b>3 Years</b></li>';
	  					$rec=true;
					}
				}else if ($result['contract_duration'] == "5 Years")	{
				  	$days=Yii::app()->db->createCommand(" select DATEDIFF(('".$result['starting_date']."' + INTERVAL 5 Year),CURRENT_DATE()+ INTERVAL 90 Day) as diff")->queryScalar(); 
	  				if ($days==0)	{
						$list.='<li>Contract:<b> '.$result['contract_description'].'</b> for Customer: <b>'.Customers::getNameById($result['customer']).'</b> - Contract duration: <b>5 Years</b></li>';
	  					$rec=true;
					}
				}
	    	}
    		$list.='</ul>';	    	
    		if ($rec){
    			$to_replace = array(
					'{body}',
					);			
				$replace = array(
					$list
					);						
				$body = str_replace($to_replace, $replace, $notif['message']);
				$subject = $notif['name'];
				$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
				Yii::app()->mailer->ClearAddresses();
					foreach($emails as $email) {
					if (!empty($email))
						Yii::app()->mailer->AddAddress($emails);
				}					
				Yii::app()->mailer->Subject  = $subject;
				Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				if (Yii::app()->mailer->Send(true)){
					echo "sent";
				
				}
    		}
		}
	}
	public function actioncheckAnniversary(){
		//yearly
	    $results= Yii::app()->db->createCommand("SELECT id_maintenance, starting_date FROM  `maintenance` where DAY(starting_date)= DAY(CURRENT_DATE()) and MONTH(starting_date)= MONTH(CURRENT_DATE()) and (year(starting_date)+1)=year(CURRENT_DATE()) and frequency=83" )->queryAll();
	    $limits= yii::app()->db->createCommand("SELECT id, default_value, type FROM support_services")->queryAll();
	    if(!empty($results)) {
		    foreach($results as $result){
				foreach ($limits as $limit) {
					Yii::app()->db->createCommand("UPDATE maintenance_services set `limit`='".$limit['default_value']."' WHERE id_contract='".$result['id_maintenance']."' and id_service='".$limit['id']."' " )->execute();
				}
			}
		}
		//biyearly
		$results= Yii::app()->db->createCommand("SELECT id_maintenance, starting_date FROM  `maintenance`  WHERE DATEDIFF(CURDATE(),starting_date) = 180 and frequency=82" )->queryAll();
	    if(!empty($results)){
		    foreach($results as $result){
				foreach ($limits as $limit) 	{
					Yii::app()->db->createCommand("UPDATE maintenance_services set `limit`='".$limit['default_value']."' WHERE id_contract='".$result['id_maintenance']."' and id_service='".$limit['id']."' " )->execute();
				}
			}
		}
		//quarterly
		$results= Yii::app()->db->createCommand("SELECT id_maintenance, starting_date FROM  `maintenance`  WHERE DATEDIFF(CURDATE(),starting_date) = 91 and frequency=81" )->queryAll();
	     if(!empty($results)) {
		    foreach($results as $result){
				foreach ($limits as $limit) {
					Yii::app()->db->createCommand("UPDATE maintenance_services set `limit`='".$limit['default_value']."' WHERE id_contract='".$result['id_maintenance']."' and id_service='".$limit['id']."' " )->execute();
				}
			}
		}
		//monthly
		$results= Yii::app()->db->createCommand("SELECT id_maintenance, starting_date FROM  `maintenance`  WHERE DATEDIFF(CURDATE(),starting_date) = 30 and frequency=80" )->queryAll();
	    if(!empty($results))  {
	    	foreach($results as $result){
				foreach ($limits as $limit) 	{
					Yii::app()->db->createCommand("UPDATE maintenance_services set `limit`='".$limit['default_value']."' WHERE id_contract='".$result['id_maintenance']."' and id_service='".$limit['id']."' " )->execute();
				}
			}
		}
		echo "done";
	}
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('financial-maintenance')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['Maintenance']) ? $_GET['Maintenance'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/maintenance/index' => array(
						'label'=>Yii::t('translations', 'Contract'),
						'url' => array('maintenance/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Maintenance('search');
		$model->unsetAttributes(); 
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionAdmin(){
		$model=new Maintenance('search');
		$model->unsetAttributes();
		if (isset($_GET['Maintenance']))
			$model->attributes=$_GET['Maintenance'];
		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function loadModel($id){
		$model=Maintenance::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model){
		if (isset($_POST['ajax']) && $_POST['ajax']==='maintenance-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionDeleteUpload()	{
		if (isset($_GET['model_id'], $_GET['file'])){
			$id = (int)$_GET['model_id'];			
			$filepath = Maintenance::getDirPath($id).$_GET['file'];
			$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
			echo $filepath." ".$success ;
			if ($success){
				$query = "UPDATE `maintenance` SET file='' WHERE id_maintenance='$id'";
				Yii::app()->db->createCommand($query)->execute();
			}
		}
	}
	public function actionGetExcel(){
		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");
		$model =Maintenance::model(); 
		$model->product= $_POST['Maintenance']['product'];
		$model->support_service= $_POST['Maintenance']['support_service'];
		$month= $_POST['Maintenance']['starting_date'];
		$model->product= $_POST['Maintenance']['product'];
		$model->status= $_POST['Maintenance']['status'];
		$model->owner= $_POST['Maintenance']['owner'];
		$model->customer= $_POST['Maintenance']['customer'];	
		$model->end_customer= $_POST['Maintenance']['end_customer'];	
		$select = "SELECT * FROM maintenance ";
		$where = " WHERE 1=1 ";
		if($model->product !=""){
			$code = Yii::app()->db->createCommand("select id from codelkups where codelkup='".$model->product."' ")->queryScalar();
			$where .= " AND product =".$code." ";
		}
		if($model->status !=""){
			$where .= " AND status ='$model->status'";
		}
		if($model->owner !=""){
			$where .= " AND owner ='$model->owner'";
		}
		if($model->support_service !=""){
			$where .= " AND support_service ='$model->support_service'";
		}
		if($month !=""){
			$where .= " AND MONTH(starting_date) ='$month'";
		}
		if($model->customer !=""){
			$customer = Customers::getIdByName($model->customer);
			$where .= " AND customer ='$customer'";
		}
		if($model->end_customer !=""){
			$customer = Customers::getIdByName($model->end_customer);
			$where .= " AND end_customer ='$customer'";
		}
		$data = Yii::app()->db->createCommand($select.$where)->queryAll();	

		$nb = sizeof($data);         
        $objPHPExcel->getActiveSheet()->getStyle('A1:T1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:T1')->applyFromArray($styleArray); 

        $curryear= date("Y");
		$sheetId = 0;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Customer')
		->setCellValue('B1', 'Contract')
		->setCellValue('C1', 'Owner')
		->setCellValue('D1', 'Frequency')
		->setCellValue('E1', 'Amount USD')
		->setCellValue('F1', 'Net USD')
		->setCellValue('G1', 'Starting Date')
		->setCellValue('H1', 'Status')
		->setCellValue('I1', 'Support Service')
		->setCellValue('J1', 'Product')
		->setCellValue('K1', 'Esc% (as per agreement)')
		->setCellValue('L1', 'Esc% (for this year)')
		->setCellValue('M1', $curryear.' Amount ($)')
		->setCellValue('N1', $curryear.' ESC%')		
		->setCellValue('O1', ($curryear-1).' Amount ($)')
		->setCellValue('P1', ($curryear-1).' ESC%')		
		->setCellValue('Q1', ($curryear-2).' Amount ($)')
		->setCellValue('R1', ($curryear-2).' ESC%')
		->setCellValue('S1', ($curryear-3).' Amount ($)')
		->setCellValue('T1', ($curryear-3).' ESC%')
		;		
		$sup='';
		$i = 1;
		foreach($data as $d => $row){
			$sup= Yii::app()->db->createCommand("SELECT codelkup FROM `codelkups` where id ='".$row['support_service']."' ")->queryScalar();
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, Customers::getNameById($row['customer']))
			->setCellValue('B'.$i, $row['contract_description'])
			->setCellValue('C'.$i, Codelkups::getCodelkup($row['owner']))
			->setCellValue('D'.$i, Codelkups::getCodelkup($row['frequency']))
			->setCellValue('E'.$i, Utils::formatNumber($row['amount'])) 
			->setCellValue('F'.$i, Utils::formatNumber($row['amount']*$row['sns_share']/100))
			->setCellValue('G'.$i, (isset($row['starting_date']) && $row['starting_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['starting_date'])):"")
			->setCellValue('H'.$i, $row['status'])
			->setCellValue('I'.$i, $sup)	
			->setCellValue('J'.$i, Codelkups::getCodelkup($row['product']))	
			->setCellValue('K'.$i, $row['escalation_factor'])	
			->setCellValue('L'.$i, $row['real_esc'])	
			->setCellValue('M'.$i, round(MaintenanceInvoices::getTotalPerYear($row['id_maintenance'], $curryear),2))
			->setCellValue('N'.$i, MaintenanceInvoices::getESCPerYear($row['id_maintenance'],  $curryear))	
			->setCellValue('O'.$i, round(MaintenanceInvoices::getTotalPerYear($row['id_maintenance'], ($curryear-1)), 2)	)
			->setCellValue('P'.$i, MaintenanceInvoices::getESCPerYear($row['id_maintenance'], ($curryear-1)))	
			->setCellValue('Q'.$i, round(MaintenanceInvoices::getTotalPerYear($row['id_maintenance'], ($curryear-2)) ,2)	)
			->setCellValue('R'.$i, MaintenanceInvoices::getESCPerYear($row['id_maintenance'], ($curryear-2)))	
			->setCellValue('S'.$i, round(MaintenanceInvoices::getTotalPerYear($row['id_maintenance'], ($curryear-3)),2))	
			->setCellValue('T'.$i, MaintenanceInvoices::getESCPerYear($row['id_maintenance'], ($curryear-3)))		
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Maintenance');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Maintenance.xls"');
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
	public function actiondeactivateitems(){
			if (isset($_POST['checkinvoice'])) {
			if(sizeof($_POST['checkinvoice']) == 0)	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No item(s) are selected.'
				)));
				exit;
			}
			$ids=implode(',',$_POST['checkinvoice']);
			$d= Yii::app()->db->createCommand("update maintenance_items set status=2 where id in (".$ids.")")->execute();
			
			echo json_encode(array_merge(array(
					'status'=>'success'
				)));
				exit;
		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No item(s) are selected.'
				)));
				exit;
		}
	}
	public function actioninputInvItems(){
		if (isset($_POST['checkinvoice'])) {
			if(sizeof($_POST['checkinvoice']) == 0)	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No item is selected.'
				)));
				exit;
			}
			if(sizeof($_POST['checkinvoice']) >1){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Please mark one item only.'
				)));
				exit;
			}
			$id=implode('',$_POST['checkinvoice']);


			$active= Yii::app()->db->createCommand("SELECT count(1) from maintenance_items where id=".$id." and status =1")->queryScalar();
			if($active == 0){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Item selected is Inactive.'
				)));
				exit;
			}


			$d= Yii::app()->db->createCommand("SELECT starting_date - interval 1 day as enddate FROM maintenance m, maintenance_items mi where mi.id=".$id." and mi.id_contract=id_maintenance")->queryScalar();
			if(empty($d)){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Starting date of the contract is not defined.'
				)));
				exit;
			}
			$enddate=  DateTime::createFromFormat("Y-m-d", $d);
			$current= new DateTime();
		//	print_r($current->format("d/m/Y"));exit;//.$current->format("d/m/Y"));exit;
			while ($enddate<$current) {
				$enddate= $enddate->add(new DateInterval('P1Y'));
			}
			$d = $enddate->format("d/m/Y");
			echo json_encode(array_merge(array(
					'status'=>'success','dateend'=>$d
				)));
				exit;
		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No item is selected.'
				)));
				exit;
		}
	}
	public function actioninputInv(){
		if (isset($_POST['checkinvoice'])) {
			if(sizeof($_POST['checkinvoice']) == 0)	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Contract is selected.'
				)));
				exit;
			}
			if(sizeof($_POST['checkinvoice']) >1){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Please mark one contract only.'
				)));
				exit;
			}
			$id=implode('',$_POST['checkinvoice']);

			$active= Yii::app()->db->createCommand("SELECT count(1) from maintenance where status='Active' and  id_maintenance= ".$id."")->queryScalar();
			if($active == 0){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Contract selected is Inactive.'
				)));
				exit;
			}


			$d= Yii::app()->db->createCommand("SELECT starting_date - interval 1 day as enddate FROM maintenance  where id_maintenance=".$id)->queryScalar();
			if(empty($d)){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Starting date of the contract is not defined.'
				)));
				exit;
			}
			$enddate=  DateTime::createFromFormat("Y-m-d", $d);
			$current= new DateTime();
		//	print_r($current->format("d/m/Y"));exit;//.$current->format("d/m/Y"));exit;
			while ($enddate<$current) {
				$enddate= $enddate->add(new DateInterval('P1Y'));
			}
			$d = $enddate->format("d/m/Y");
			echo json_encode(array_merge(array(
					'status'=>'success','dateend'=>$d
				)));
				exit;

		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Contract is selected.'
				)));
				exit;
		}
	}
	public function insertInv($startdate, $enddate, $id)
	{
			$contract=Maintenance::getContractByItem($id);
			$model = $this->loadModel($contract);
			if($model->status=='Inactive'){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Contract is not Active.'
				)));
				exit;
			}
			$date = DateTime::createFromFormat("Y-m-d", $startdate);
			$enddate=  DateTime::createFromFormat("Y-m-d", $enddate);
			$from_date = $date->format("d-m-Y");
			$from_date2 = $date->format("Y-m-d");
			$year = $date->format("Y");
			$month = $date->format("m");
 			$data= Yii::app()->db->createCommand("select * from maintenance_items where id=".$id." ")->queryRow();
			$totgrossamount=$data['amount'];
		    $frequency = Maintenance::getFrequency($model->frequency);
		    $to_date = $enddate->format("Y-m-d");
		    $periodfreq = Maintenance::getPeriodFreqDays($model->frequency);
		    $daysinvtot=	$enddate->diff($date)->format("%a"); 		
		    $additional_desc = $data['contract_description'];
		    $escalation_factor=0;
		    $ea= "NULL";
		    		if(!empty($model->ea) && $model->ea !=0 )
		    		{
		    			$ea= "'".$model->ea."'";
		    		}
		    /*if (!empty($escalation_factor) && $escalation_factor!='0') {
		    	$invoice_description = Customers::getNameById($model->customer)." - ".$additional_desc." + ".$escalation_factor."% Escalation - From period ".$from_date." To ".$to_date."";
		    }else{*/
		    	if(!empty($additional_desc))
		    	{
		    		$additional_desc= substr($additional_desc, 0, -1);
		    		$invoice_description = Customers::getNameById($model->customer)." - ".$additional_desc." - From period ".$from_date." To ".$to_date."";
		    	}else{
		    		$invoice_description = Customers::getNameById($model->customer)." - From period ".$from_date." To ".$to_date."";
		    	}				
		//	}
		    $net_amount =(($totgrossamount*$data['sns_share']/100)/$periodfreq)*$daysinvtot;
		    $partner_amount = (($totgrossamount*(1-$data['sns_share']/100))/$periodfreq)*$daysinvtot;  		
		    $gross_amount = (($totgrossamount)/$periodfreq)*$daysinvtot;
		    $amount = ($totgrossamount/$periodfreq)*$daysinvtot;
		    $invoice_number = "00000";
		    $assigned= Customers::getIdAssigned($model->customer);
		    if(empty($assigned))  {
 				Yii::app()->db->createCommand("INSERT INTO invoices(escalation,id_ea ,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
		  					partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date) 
		  					VALUES (".$escalation_factor.",".$ea.",'".$model->customer."','".$invoice_number."','".$invoice_description."',".$data['currency'].",
		  					'1/1', 100,".$model->owner.",".$data['sns_share'].",".$net_amount.",".$gross_amount.",".$partner_amount.",
		  					".$amount.",".$month.",".$year.",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00')")->execute();
		  		    
		    }else{  
		 		Yii::app()->db->createCommand("INSERT INTO invoices(escalation,id_ea ,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
		  					partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date, id_assigned) 
		  					VALUES (".$escalation_factor.",".$ea.",'".$model->customer."','".$invoice_number."','".$invoice_description."',".$data['currency'].",
		  					'1/1', 100,".$model->owner.",".$data['sns_share'].",".$net_amount.",".$gross_amount.",".$partner_amount.",
		  					".$amount.",".$month.",".$year.",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00',".$assigned." )")->execute();
		  	}
		  	$id_inv = Yii::app()->db->getLastInsertId();
		    $invoice_number = Utils::paddingCode($id_inv);
					if($invoice_number == "99999")
						$invoice_number = "00000";
					Yii::app()->db->createCommand("UPDATE invoices SET invoice_number ='$invoice_number' WHERE id = '$id_inv'")->execute();
		    		Yii::app()->db->createCommand("INSERT INTO maintenance_invoices(id_contract, id_invoice, amount,from_period, to_period, escalation_factor) VALUES ('".$model->id_maintenance."',".$id_inv.",".$gross_amount.",'".$from_date2."','".$to_date."','".$escalation_factor."')")->execute();
	    		
	}
	public function actioninputDate(){ }	
	public function actioncreateInvMaintItem(){		
		$startdate= $_POST['start'];
		$enddate= $_POST['end'];
		if(empty($startdate) || empty($enddate)){
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'From and To Period should be set.'
				)));
				exit;
		}
		if (isset($_POST['checkinvoice']) && sizeof($_POST['checkinvoice']) == 1) {
			$id=implode('',$_POST['checkinvoice']);
			$contract=Maintenance::getContractByItem($id);
			$model = $this->loadModel($contract);
			if($model->status=='Inactive'){
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Contract is not Active.'
				)));
				exit;
			}
			$date = DateTime::createFromFormat("d/m/Y", $startdate);
			$enddate=  DateTime::createFromFormat("d/m/Y", $enddate);
			$from_date = $date->format("d-m-Y");
			$from_date2 = $date->format("Y-m-d");
			$year = $date->format("Y");
			$month = $date->format("m");
 			$data= Yii::app()->db->createCommand("select * from maintenance_items where id=".$id." ")->queryRow();
			$totgrossamount=$data['amount'];
		    $frequency = Maintenance::getFrequency($model->frequency);
		    $to_date = $enddate->format("Y-m-d");
		    $periodfreq = Maintenance::getPeriodFreqDays(83);
		    $daysinvtot=	$enddate->diff($date)->format("%a"); 		
		    $additional_desc = $data['contract_description'];
		    $escalation_factor=0;
		    $ea= "NULL";
		    		if(!empty($data['ea']) && $data['ea'] !=0 )
		    		{
		    			$ea= "'".$data['ea']."'";
		    		}
		    /*if (!empty($escalation_factor) && $escalation_factor!='0') {
		    	$invoice_description = Customers::getNameById($model->customer)." - ".$additional_desc." + ".$escalation_factor."% Escalation - From period ".$from_date." To ".$to_date."";
		    }else{*/
		    	if(!empty($additional_desc))
		    	{
		    		$additional_desc= substr($additional_desc, 0, -1);
		    		$invoice_description = Customers::getNameById($model->customer)." - ".$additional_desc." - From period ".$from_date." To ".$to_date."";
		    	}else{
		    		$invoice_description = Customers::getNameById($model->customer)." - From period ".$from_date." To ".$to_date."";
		    	}				
		//	}
		    $net_amount =(($totgrossamount*$data['sns_share']/100)/$periodfreq)*$daysinvtot;
		    $partner_amount = (($totgrossamount*(1-$data['sns_share']/100))/$periodfreq)*$daysinvtot;  		
		    $gross_amount = (($totgrossamount)/$periodfreq)*$daysinvtot;
		    $amount = ($totgrossamount/$periodfreq)*$daysinvtot;
		    $invoice_number = "00000";
		    $assigned= Customers::getIdAssigned($model->customer);
		    if(empty($assigned))  {
 				Yii::app()->db->createCommand("INSERT INTO invoices(escalation,id_ea ,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
		  					partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date) 
		  					VALUES (".$escalation_factor.",".$ea.",'".$model->customer."','".$invoice_number."','".$invoice_description."',".$data['currency'].",
		  					'1/1', 100,".$model->owner.",".$data['sns_share'].",".$net_amount.",".$gross_amount.",".$partner_amount.",
		  					".$amount.",".$month.",".$year.",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00')")->execute();
		  		    
		    }else{  
		 		Yii::app()->db->createCommand("INSERT INTO invoices(escalation,id_ea ,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
		  					partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date, id_assigned) 
		  					VALUES (".$escalation_factor.",".$ea.",'".$model->customer."','".$invoice_number."','".$invoice_description."',".$data['currency'].",
		  					'1/1', 100,".$model->owner.",".$data['sns_share'].",".$net_amount.",".$gross_amount.",".$partner_amount.",
		  					".$amount.",".$month.",".$year.",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00',".$assigned." )")->execute();
		  	}
		  	$id_inv = Yii::app()->db->getLastInsertId();
		    $invoice_number = Utils::paddingCode($id_inv);
					if($invoice_number == "99999")
						$invoice_number = "00000";
					Yii::app()->db->createCommand("UPDATE invoices SET invoice_number ='$invoice_number' WHERE id = '$id_inv'")->execute();
		    		Yii::app()->db->createCommand("INSERT INTO maintenance_invoices(id_contract, id_invoice, amount,from_period, to_period, escalation_factor) VALUES ('".$model->id_maintenance."',".$id_inv.",".$gross_amount.",'".$from_date2."','".$to_date."','".$escalation_factor."')")->execute();
		    		

			echo json_encode(array_merge(array(
					'status'=>'success'
				)));
				exit;
		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Contract is selected.'
				)));
				exit;
		}
	}
	public function actioncreateInvMaint(){		
		$startdate= $_POST['start'];
		$enddate= $_POST['end'];
		if(empty($startdate) || empty($enddate)){
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'From and To Period should be set.'
				)));
				exit;
		}
		if (isset($_POST['checkinvoice']) && sizeof($_POST['checkinvoice']) == 1) {
			$id=implode('',$_POST['checkinvoice']);
			$model = $this->loadModel($id);
			if($model->status=='Inactive')	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Contract is not Active.'
				)));
				exit;
			}
			$date = DateTime::createFromFormat("d/m/Y", $startdate);
			$enddate=  DateTime::createFromFormat("d/m/Y", $enddate);
			$from_date = $date->format("d-m-Y");
			$from_date2 = $date->format("Y-m-d");
			$year = $date->format("Y");
			$month = $date->format("m");
			$escalation_factor=$model->real_esc;
			$totgrossamount=$model->getTotalGrossAmountFromlastINVWithoutFreq();
		    $frequency = Maintenance::getFrequency($model->frequency);
		    $to_date = $enddate->format("Y-m-d");
		    $periodfreq = Maintenance::getPeriodFreqDays(83);
		    $daysinvtot=	$enddate->diff($date)->format("%a"); 		
		    $additional_desc = MaintenanceItems::getDescription($id);

		    if (!empty($escalation_factor) && $escalation_factor!='0'){
		    			$totgrossamount = $totgrossamount + ($totgrossamount*$escalation_factor/100);
		    }

		   /* if (!empty($escalation_factor) && $escalation_factor!='0')  {
		    	$invoice_description = Customers::getNameById($model->customer)." - ".$model->contract_description." - ".$additional_desc." + ".$escalation_factor."% Escalation - From period ".$from_date." To ".$to_date."";
		    }else{*/
		    	if(!empty($additional_desc))
		    	{
		    		$additional_desc= substr($additional_desc, 0, -1);
		    		$invoice_description = Customers::getNameById($model->customer)." - ".$model->contract_description." - ".$additional_desc." - From period ".$from_date." To ".$to_date."";
				}
				else{
					$invoice_description = Customers::getNameById($model->customer)." - ".$model->contract_description." - From period ".$from_date." To ".$to_date."";
				}
//}
			$ea= "NULL";
		    		if(!empty($model->ea) && $model->ea !=0 )
		    		{
		    			$ea= "'".$model->ea."'";
		    		}
		    $net_amount =(($totgrossamount*$model->sns_share/100)/$periodfreq)*$daysinvtot;
		    $partner_amount = (($totgrossamount*(1-$model->sns_share/100))/$periodfreq)*$daysinvtot;  		
		    $gross_amount = (($totgrossamount)/$periodfreq)*$daysinvtot;
		    $amount = ($totgrossamount/$periodfreq)*$daysinvtot;
		    $invoice_number = "00000";
		    $assigned= Customers::getIdAssigned($model->customer);
		    if(empty($assigned)){
 				Yii::app()->db->createCommand("INSERT INTO invoices(escalation, id_ea,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
		  					partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date) 
		  					VALUES (".$escalation_factor.",".$ea.",'".$model->customer."','".$invoice_number."','".$invoice_description."',".$model->currency.",
		  					'1/1', 100,".$model->owner.",".$model->sns_share.",".$net_amount.",".$gross_amount.",".$partner_amount.",
		  					".$amount.",".$month.",".$year.",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00')")->execute();
		  		    
		    }else{  
		 		Yii::app()->db->createCommand("INSERT INTO invoices(escalation, id_ea,id_customer,invoice_number,invoice_title, currency,payment,payment_procente, partner, sns_share,net_amount,gross_amount,
		  					partner_amount,amount,invoice_date_month,invoice_date_year,status,type, partner_status, paid_date, id_assigned) 
		  					VALUES (".$escalation_factor.",".$ea.",'".$model->customer."','".$invoice_number."','".$invoice_description."',".$model->currency.",
		  					'1/1', 100,".$model->owner.",".$model->sns_share.",".$net_amount.",".$gross_amount.",".$partner_amount.",
		  					".$amount.",".$month.",".$year.",'"."To Print"."' ,'Maintenance','Not Paid','0000-00-00',".$assigned." )")->execute();
		  	}
		  	$id_inv = Yii::app()->db->getLastInsertId();
		    $invoice_number = Utils::paddingCode($id_inv);
					if($invoice_number == "99999")
						$invoice_number = "00000";
					Yii::app()->db->createCommand("UPDATE invoices SET invoice_number ='$invoice_number' WHERE id = '$id_inv'")->execute();
		    		Yii::app()->db->createCommand("INSERT INTO maintenance_invoices(id_contract, id_invoice, amount,from_period, to_period, escalation_factor) VALUES ('".$model->id_maintenance."',".$id_inv.",".$gross_amount.",'".$from_date2."','".$to_date."','".$escalation_factor."')")->execute();
		    echo json_encode(array_merge(array(
					'status'=>'success'
				)));
				exit;
		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Contract is selected.'
				)));
				exit;
		}
	}
	public function actionGetExcel2(){		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();		
		$startdate= $_POST['start'];
		$enddate= $_POST['end'];
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");	
		$sheetId = 0;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Customer')
		->setCellValue('B1', 'Revenues')
		->setCellValue('C1', 'Cost')
		->setCellValue('D1', 'Profit')
		;



		$customers= Yii::app()->db->createCommand("select distinct(m.customer) as id, c.name from maintenance m, customers c where c.id=m.customer and m.status ='Active' order by c.name")->queryAll();
		$i = 1;	$largerev=0;	$largecost=0;
		foreach ($customers as $customer) {
			$data= Yii::app()->db->createCommand("select * from maintenance where customer=".$customer['id']." and status ='Active' ")->queryAll();
			$totalrev=0;
			$totalcost=0;
			foreach($data as $d => $row){
				$totalrev+=$row['amount']*($row['sns_share']/100);
				$totalcost+=(Maintenance::getHoursbydate($row['id_maintenance'],$startdate,$enddate))*SystemParameters::getCost();				
			}
			$profit=$totalrev-$totalcost;
			$largerev+= $totalrev;
			$largecost+= $totalcost;
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, Customers::getNameById($customer['id']))
			->setCellValue('B'.$i, Utils::formatNumber($totalrev))
			->setCellValue('C'.$i, Utils::formatNumber($totalcost)) 
			->setCellValue('D'.$i, Utils::formatNumber($profit)) 
			;
		}
		$largeprof=$largerev-$largecost;
			$i++;
				$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i,  "Total")
			->setCellValue('B'.$i, Utils::formatNumber($largerev))
			->setCellValue('C'.$i, Utils::formatNumber($largecost)) 
			->setCellValue('D'.$i, Utils::formatNumber($largeprof)) 
			;

        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray); 

		$objPHPExcel->getActiveSheet()->setTitle('Maintenance');		
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Maintenance.xls"');
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
	public function actionChangeInput(){	
		if(isset($_POST['id'])){
			$value = $_POST['value'];
			$id = (int)$_POST['id'];
			$j = Yii::app()->db->createCommand("UPDATE maintenance_services SET `limit` = '{$value}' WHERE id = {$id} ")->execute();
		}
	}
	public function actionChangeInput2(){	
		if(isset($_POST['id'])){
			$value = $_POST['value'];
			if($value=='Yes')	{ $prompt='0'; }else{ $prompt='1';}
			$id = (int)$_POST['id'];
			$j = Yii::app()->db->createCommand("UPDATE maintenance_services SET `limit` = '$prompt' WHERE id = '$id' ")->execute();
		}
	}
	public function actiondeleteService($id){
		if(isset($id)) {	
			Yii::app()->db->createCommand("DELETE from maintenance_services WHERE id = '$id'" )->execute();
		}
	}
	public function actionupdatefieldsItem()
	{
		if (isset($_POST['val']))
		{
			$ea =$_POST['val'];
			if(is_numeric($ea))
			{

				$value = Yii::app()->db->createCommand("select sum(amount)  from eas_items where id_ea=  ".$ea." ")->queryScalar();
				if(!empty($value))
				{
					echo json_encode(array('status' => 'success', 'amount' => $value));
				}else{
					echo json_encode(array('status' => 'failure'));
				}

			}else{
				echo json_encode(array('status' => 'failure'));
			}
		}
	}

	public function actionupdatefields(){
		if (isset($_POST['val']))
		{
			$ea =$_POST['val'];
			if(is_numeric($ea))
			{

				$values = Yii::app()->db->createCommand("SELECT c.name, c.id, e.currency, (select case when settings_codelkup= 456 then 501   WHEN  settings_codelkup= 458  THEN 502  WHEN  settings_codelkup= 457  THEN 503  WHEN  settings_codelkup= 1385  THEN 1257 ELSE null  END settings_codelkup from eas_items where id_ea= e.id limit 1) as plan, (select man_day_rate_n from eas_items where id_ea= e.id limit 1) as freq, (select sum(amount) from eas_items where id_ea= e.id limit 1) as amount from eas e, customers c where e.id= ".$ea."  and e.id_customer= c.id")->queryRow();
				if(!empty($values))
				{
					echo json_encode(array('status' => 'success', 'cname' => $values['name'],'customer' => $values['id'], 'currency' => $values['currency'], 'amount' => $values['amount'], 'plan' => $values['plan'], 'freq' => $values['freq']));
				}else{
					echo json_encode(array('status' => 'failure'));
				}

			}else{
				echo json_encode(array('status' => 'failure'));
			}
		}
	}
	public function actionManageService($id = NULL)  {
    	if(isset($_POST['MaintenanceServices']))	{
    		$contract= $_POST['MaintenanceServices']['id_contract'];
		    $service= $_POST['MaintenanceServices']['id_service'];
		    $diffplan=($service-11);    		
    		$j = Yii::app()->db->createCommand("SELECT count(1) FROM maintenance_services WHERE id_contract='".$contract."' AND id_service='".$service."' ")->queryScalar();
    		//$k = Yii::app()->db->createCommand("SELECT count(1) FROM maintenance_services WHERE id_contract='".$contract."' AND id_service='".$diffplan."' ")->queryScalar();
			if ($j == 0) {
   		   		$field_type = MaintenanceServices::getfieldtypeByService($service);
   		   		$access= MaintenanceServices::getaccesstypeByService($service);
   		   		$default_value= MaintenanceServices::getvaluetypeByService($service);
		   		Yii::app()->db->createCommand("INSERT into maintenance_services (id_service, id_contract ,`limit`, field_type , access) values ('".$service."', '".$contract."', '".$default_value."', '".$field_type."' , '".$access."' )" )->execute();
				echo json_encode(array('status' => 'saved'));
				exit;
    		}else{
    			echo json_encode(array('status' => 'bad'));
				exit;
    		}
    	}else{
	       	$model = new MaintenanceServices();	
	    	Yii::app()->clientScript->scriptMap=array(
	        	'jquery.js'=>false,
	    		'jquery.min.js' => false,
	    		'jquery-ui.min.js' => false,
			);			
	    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_service_form', array(
	            	'model'=> $model,
	    		
	        	), true, true)));
				
	      	 exit;
   		}
    }
}?>


