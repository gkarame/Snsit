<?php
class IncomingTransfersController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + delete', 
		);
	}
	public function init(){
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - Incoming Transfers');
	}
	public function accessRules(){
		return array(
			array(
				'allow',
				'actions'=>array('SendAssignedEmail','SendCreateEmail','SendProductdoneNoinfo','SendCompletedEmail','SendChangedEmail'),
				'users'=>array('*'),
			),

			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(
						'index','view','create','update', 'delete','deleteInvoice','manageInvoice','GetUnssignedInvoices',
						'getInvoiceDetail','UpdateHeader','createTransfer','assignInvoices','GetExcel','updateinfoheader',
						'inputInv','validateRate','getAmtIncurrency'
						
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actions(){
		 return array( );
	}	
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('financial-incomingTransfers')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['IncomingTransfers']) ?  $_GET['IncomingTransfers'] : Utils::getSearchSession();		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge( 
			$this->action_menu,
			array(
				'/incomingTransfers/index' => array(
					'label'=>Yii::t('translations','Incoming Transfers'),
					'url' => array('incomingTransfers/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model = new IncomingTransfers('search');
		$model->unsetAttributes(); 
		if (isset($_GET['IncomingTransfers'])){
			$model->attributes = $_GET['IncomingTransfers'];
		}
		if (isset($_GET['IncomingTransfers']['status'])){
			$model->status=$_GET['IncomingTransfers']['status'];
		}
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionView($id, $new =null){
		$id = (int)$id;
		$model = $this->loadModel($id);		
		$title = 'IT #'.$model->it_no.' - '.Customers::getNameById($model->id_customer);
		$arr = Utils::getShortText('IT #'.$model->it_no);		
		$subtab = $this->getSubTab(Yii::app()->createUrl('incomingTransfers/view', array('id' => $id)));		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/incomingTransfers/view/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('incomingTransfers/view', 'id'=>$id),
							'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? $title : ''),
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

	public function actionCreate(){
		$error_message = '';
		if(!GroupPermissions::checkPermissions('financial-incomingTransfers','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
			$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/incomingTransfers/create' => array(
					'label'=> 'New TR',
					'url' => array('incomingTransfers/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = 0;
		$model = new IncomingTransfers;
		$model->status = IncomingTransfers::STATUS_NEW;
		$model->it_no = "00000";
		$model->adddate = date('Y-m-d H:i:s');
  		if(isset($_POST['IncomingTransfers'])){
			try{	 	
				$model->attributes = $_POST['IncomingTransfers'];
				$model->id_user = Yii::app()->user->id;
				$model->id_customer = Customers::getIdByName($model->customer_name);
				
					if($model->save()){
					$model->it_no = Utils::paddingCode($model->id);
					$model->save();
					$this->redirect(array('incomingTransfers/update', 'id'=>$model->id));
					}
			 
			}
			catch(Exception $e){
			    throw $e;
			}
		}
		$this->render('create',array('model'=>$model));
	}

	public function actioninputInv(){
		if (isset($_POST['checkinvoice'])) {
			if(sizeof($_POST['checkinvoice']) == 0)	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Invoice is Selected.'
				)));
				exit;
			}
			$invoicestr =implode(',', $_POST['checkinvoice']);
		//	print_r($invoicestr);exit;
			$partners=Yii::app()->db->createCommand("select count(distinct partner) as c ,partner  from invoices where id in (".$invoicestr.")")->queryRow();

			if($partners['c']>1)
			{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Invoices selected must belong to the same partner!'
				)));
				exit;
			}

			$customer=Yii::app()->db->createCommand("select count(distinct id_customer)  from invoices where id in (".$invoicestr.") and partner=77 ")->queryScalar();

			if($customer>1)
			{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Invoices selected must belong to the same Customer!'
				)));
				exit;
			}

			$status=Yii::app()->db->createCommand("select count(1)  from invoices where id in (".$invoicestr.") and status='Paid' ")->queryScalar();

			if($status>0)
			{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Invoices selected must be Not Paid!'
				)));
				exit;
			}
			$status2=Yii::app()->db->createCommand("select count(1)  from invoices where id in (".$invoicestr.") and partner_status='Not Paid' and partner!=77 ")->queryScalar();

			if($status2>0)
			{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Invoices selected must have partner status Paid!'
				)));
				exit;
			}

			$old=Yii::app()->db->createCommand("select count(1)  from invoices where id in (".$invoicestr.") and old='yes' ")->queryScalar();

			if($old>0)
			{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'Invoices selected must be not be old!'
				)));
				exit;
			}

			if($partners['partner'] == 78 )
			{
				$account= Yii::app()->db->createCommand("select bank,aux from customers where id=246 ")->queryRow();
			}else if ($partners['partner'] == 79 )
			{
				$account= Yii::app()->db->createCommand("select bank,aux from customers where id=241 ")->queryRow();
			}else if ($partners['partner'] == 201 || $partners['partner'] == 554 )
			{
				$account= Yii::app()->db->createCommand("select bank,aux from customers where id=239 ")->queryRow();

			}else if ( $partners['partner'] == 1218 )
			{
				$account= Yii::app()->db->createCommand("select bank,aux from customers where id=323 ")->queryRow();

			}else{
				$customer=Yii::app()->db->createCommand("select distinct(id_customer)  from invoices where id in (".$invoicestr.") LIMIT 1")->queryScalar();
				$account= Yii::app()->db->createCommand("select bank,aux from customers where id=".$customer." ")->queryRow();
			}
			

			echo json_encode(array_merge(array(
					'status'=>'success', 'aux'=>$account['aux'] , 'bank'=>$account['bank']
				)));
				exit;
		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Invoice is selected!'
				)));
				exit;
		}
	}

	public function actionUpdate($id, $new = 0, $idprd = null){
		if(!(GroupPermissions::checkPermissions('financial-incomingTransfers','write'))){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$id = (int) $id;	$model = $this->loadModel($id);	$extra = array();
		$error = false;
		if (isset(Yii::app()->session['menu']) && $new == 1) {
				Utils::closeTab(Yii::app()->createUrl('incomingTransfers/create/'));
				$this->action_menu = Yii::app()->session['menu'];
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/incomingTransfers/update/'.$id => array(
						'label'=> 'TR #'.$model->it_no,
						'url' => array('incomingTransfers/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => '',
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$this->render('update', array('model' => $model, 'new' => $new,
		'can_modify' => $model->isEditable()));		
	}

	public function actionUpdateinfoheader($id){
		$id = (int) $id;
		$model =  IncomingTransfers::model()->findByPk($id);
		if(isset($_POST['IncomingTransfers'])){
			$model->attributes = $_POST['IncomingTransfers'];
			if($model->validate()){
				$model->save();
				echo json_encode(array_merge(array(
					'status' => 'saved',					
					'html' => $this->renderPartial('_header_content_info', array('model' => $model), true, false)
					)));
					exit();
			}
		}
		echo json_encode(array_merge(array(
						'status' => 'success',					
						'html' => $this->renderPartial('_edit_header_content_info', array('model' => $model), true, false)
				)));
		exit();
	}
	public function actionUpdateHeader($id){
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		$redirect = 0;
		$error = false;
		$create = false;
		if(isset($_POST['IncomingTransfers'])){
			$originalstate= $model->status;
			$original_curr= $model->currency;
			$model->attributes = $_POST['IncomingTransfers'];
			
			if($model->validate()){
				$model->save();
				/*if($original_curr != $model->currency)
				{
					Yii::app()->db->createCommand("UPDATE incoming_transfers_details set received_currency = '".$model->currency."' where id_it=".$model->id)->execute();
							
				}*/

				if($model->status == 2 && $originalstate !=2)
				{
					
					$invoices=Yii::app()->db->createCommand("select invoice_number, original_amount, received_amount,rate from incoming_transfers_details where id_it= ".$model->id)->queryAll();
					foreach($invoices as $invoice){
						if(round($invoice['original_amount'],1) == round(($invoice['received_amount']/ $invoice['rate']),1))
						{
							if($model->partner == 77 )					
							{
								Yii::app()->db->createCommand("UPDATE Invoices set notes = CONCAT(notes, 'Amount ".Utils::formatNumber($invoice['received_amount']/ $invoice['rate'])." of the invoice is paid per TR#".$model->it_no.". '), status = '" . Invoices::STATUS_PAID . "' , paid_date= CURRENT_DATE() WHERE id in (".$invoice['invoice_number'].") ")->execute();
							}else{
								Yii::app()->db->createCommand("UPDATE Invoices set notes = CONCAT(notes, 'Amount ".Utils::formatNumber($invoice['received_amount']/ $invoice['rate'])." of the invoice is paid per TR#".$model->it_no.". '), status = '" . Invoices::STATUS_PAID . "' , sns_paid_date= CURRENT_DATE() WHERE id in (".$invoice['invoice_number'].") ")->execute();
							}
						}else{
							$total=Yii::app()->db->createCommand("select sum(received_amount/rate) from incoming_transfers_details where invoice_number ='".$invoice['invoice_number']."' and id_it in (select id from incoming_transfers where status =2) ")->queryScalar();
							if(round($total,1) == round($invoice['original_amount'],1))
							{
								$note='';
								$note.= "Amount ".Utils::formatNumber($invoice['received_amount']/ $invoice['rate'])." of the invoice is paid per TR#".Utils::paddingCode($model->id).". ";
								if($model->partner == 77 )					
								{
									Yii::app()->db->createCommand("UPDATE Invoices set notes = CONCAT(notes, '".$note."') ,status = '" . Invoices::STATUS_PAID . "' , paid_date= CURRENT_DATE() WHERE id in (".$invoice['invoice_number'].") ")->execute();
								}else{
									Yii::app()->db->createCommand("UPDATE Invoices set notes = CONCAT(notes, '".$note."') ,status = '" . Invoices::STATUS_PAID . "' , sns_paid_date= CURRENT_DATE() WHERE id in (".$invoice['invoice_number'].") ")->execute();
								}
							}else{
								if($model->partner == 77 )					
								{
									Yii::app()->db->createCommand("UPDATE Invoices set notes = CONCAT(notes, 'Amount ".Utils::formatNumber($invoice['received_amount']/ $invoice['rate'])." of the invoice is paid per TR#".$model->it_no.". ')  WHERE id in (".$invoice['invoice_number'].") ")->execute();
								}else{
									Yii::app()->db->createCommand("UPDATE Invoices set notes = CONCAT(notes, 'Amount ".Utils::formatNumber($invoice['received_amount']/ $invoice['rate'])." of the invoice is paid per TR#".$model->it_no.". ') WHERE id in (".$invoice['invoice_number'].") ")->execute();
								}
							}
						}					
					}
				}
					echo json_encode(array_merge(array(
					'status' => 'saved',					
					'can_modify' => $model->isEditable(),
					'html' => $this->renderPartial('_header_content', array('model' => $model), true, false),
					'state'=>$model->status
					), $extra));
					exit();
			}			
		}
		echo json_encode(array_merge(array(
						'status' => 'success',					
						'can_modify' => $model->isEditable(),
						'html' => $this->renderPartial('_edit_header_content', array('model' => $model), true, false),
					'state'=>$model->status
				), $extra));
		exit();
	}
	public function loadModel($id){
		$model = IncomingTransfers::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function actiondeleteInvoice($id){
		$id = (int) $id;
		Yii::app()->db->createCommand("DELETE FROM incoming_transfers_details WHERE id='{$id}'")->execute();
	}
	public function actionmanageInvoice($id = NULL){
		$new = false;
		if (!isset($_POST['id_it']))
    		exit;
		if($id == null){
			$new = true;	$model = new IncomingTransfersDetails;
			$model->id_it = (int)$_POST['id_it'];
			$data = Yii::app()->db->createCommand("select currency,rate	from incoming_transfers  where id=".(int)$_POST['id_it'])->queryRow();
			$model->received_currency = $data['currency'];
			$model->rate = $data['rate'];
		}else{
			$id = (int)$id;
    		$model = IncomingTransfersDetails::model()->findByPk($id);
			$data = Yii::app()->db->createCommand("select rate	from incoming_transfers  where id=".(int)$_POST['id_it'])->queryScalar();
    		$model->rate = $data;
		}
		if(isset($_POST['IncomingTransfersDetails']))
		{
			if ($id == NULL) {
    			$model->attributes = $_POST['IncomingTransfersDetails']['new'];    	
    			$model->id_user=  Yii::app()->user->id;
    			if(isset($model->final_invoice_number) && !empty($model->final_invoice_number))
    			{
    				 $inv = Yii::app()->db->createCommand("select invoice_number, id_customer	from receivables  where final_invoice_number='".$model->final_invoice_number."' ")->queryRow();
    				 $model->invoice_number =$inv['invoice_number'];
    				 $model->id_customer= $inv['id_customer'];
    			}
    		} else {
				
					$model->attributes = $_POST['IncomingTransfersDetails'][$id];			
    		}
    		//print_r($model->attributes);exit;
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
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_product_form', array(
            	'model'=> $model,
        	), true, true)));			
       exit;
	}
	
	public function actionvalidateRate(){	

		if(sizeof($_POST['checkinvoice']) == 0)	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Invoice is Selected.'
				)));
				exit;
			}

		$rate= $_POST['curr'];

		$invoicestr =implode(',', $_POST['checkinvoice']);
		$count=Yii::app()->db->createCommand("select count(1)  from invoices where id in (".$invoicestr.") and currency != ".$rate)->queryScalar();
		if($count>0)
		{
			echo json_encode(array_merge(array(
					'status'=>'fail'
				)));
				exit;
		}else{
			echo json_encode(array_merge(array(
					'status'=>'success'
				)));
				exit;
		}
	}
	public function actioncreateTransfer(){	

		if(sizeof($_POST['checkinvoice']) == 0)	{
				echo json_encode(array_merge(array(
					'status'=>'fail','message'=>'No Invoice is Selected.'
				)));
				exit;
			}

		$received= $_POST['amt'];
		$currency= $_POST['curr'];
		$bank= $_POST['bank'];
		$off= $_POST['off'];
		$rate= $_POST['rate'];
		$dbank= $_POST['dbank'];
		$daux= $_POST['daux'];

		$transfer= false;
		$invoices = $_POST['checkinvoice'];

		$model = new IncomingTransfers;
		$model->status = IncomingTransfers::STATUS_NEW;
		$model->it_no = "00000";
		$model->adddate = date('Y-m-d H:i:s');
		$model->id_user = Yii::app()->user->id;
		$data= Yii::app()->db->createCommand("select partner from receivables where invoice_number = '".$invoices[0]."' LIMIT 1 ")->queryRow();
		$model->partner =$data['partner'];
		$model->rate =$rate;
		$model->received_amount =$received;
		$model->currency =$currency;
		$model->bank =$bank;
		$model->offsetting =$off;
		$model->bank_dolphin =$dbank;
		$model->aux =$daux;
		
		try{
			//print_r($model->attributes);exit;
			if($model->save()){
				$model->it_no = Utils::paddingCode($model->id);
				$model->save();
				$transfer=true;
			}			
		}
		catch(Exception $e){
		    echo json_encode(array_merge(array(
					'status'=>'fail','message'=> $e->getMessage()
				)));
				exit;
		}
					
		if($transfer)
		{
			foreach ($invoices as $invoice) {
				$id_customer= Receivables::getCustomerperInv($invoice);
				Yii::app()->db->createCommand("INSERT INTO incoming_transfers_details (id_it,id_customer, invoice_number, final_invoice_number, original_amount, original_currency, received_amount, received_currency,rate, id_user, adddate)
 					SELECT '".$model->it_no."',".$id_customer.", invoice_number, final_invoice_number, net_amount, currency,  0, ".$currency.", ".$rate.", ".Yii::app()->user->id.", '".date('Y-m-d H:i:s')."' FROM receivables where invoice_number= '".$invoice."'")->execute();
			}

		}else
		{
			echo json_encode(array_merge(array(
					'status'=>'fail','message'=> 'Transfer Cannot be Created!'
				)));
				exit;
		}		
		echo json_encode(array_merge(array(
					'status'=>'success', 'tr' => $model->it_no
				)));
				exit;
	}
public function actionGetUnssignedInvoices(){
			
		if(!isset($_POST['checkinvoice']) || sizeof($_POST['checkinvoice']) ==0 )
		{
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'Please select a transfer',
				));
				exit;
		}
		$transfer = $_POST['checkinvoice'];
		//print_r($transfer[0]);exit;
		if(sizeof($transfer)>1)
		{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'Cannot select more than one transfer!',
				));
				exit;

		}else {
			$transfer_status= Yii::app()->db->createCommand("select status from incoming_transfers where id = ".$transfer[0]." LIMIT 1 ")->queryScalar();	
		
			if($transfer_status != 1)
			{
				echo CJSON::encode(array(
						'status'=>'fail',
						'message' => 'Cannot Alter a '.IncomingTransfers::getStatusLabel($transfer_status).' Transfer',
					));
					exit;
			}

			$users = IncomingTransfers::getInvoicesPopup($transfer[0],null);
			//print_r($users);exit;
			if(empty($users))
			{
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'No Invoices Available',
				));
				exit;
			}else{
				echo CJSON::encode(array(
					'status'=>'success',
					'div'=>$this->renderPartial('_recipients_users', array('users'=>$users), true)));			
				exit;
			}
		}
	}
public function actionAssignInvoices(){

		if (empty($_POST)){
			exit;
		}
		$ids = $_POST['checked']; 
		$transfer = $_POST['checkinvoice'];
		if(sizeof($transfer)>1)
		{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'Cannot select more than one transfer!',
				));
				exit;
		}
		
		$transfer_status= Yii::app()->db->createCommand("select status, currency, rate from incoming_transfers where id = ".$transfer[0]." LIMIT 1 ")->queryRow();	
		
		if($transfer_status['status'] != 1)
		{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'Cannot Alter a '.IncomingTransfers::getStatusLabel($transfer_status).' Transfer',
				));
				exit;
		}

		if (!empty($ids)){
			foreach($ids as $id){
				$inv = Invoices::model()->findByPk($id);

				$id_user = Yii::app()->user->id;
				Yii::app()->db->createCommand("insert into incoming_transfers_details (id_it, invoice_number, final_invoice_number, original_amount, original_currency, id_user, received_currency, rate) values (".$transfer[0].", '".$inv->invoice_number."', '".$inv->final_invoice_number."', ".$inv->net_amount.",".$inv->currency.",".$id_user .",".$transfer_status['currency'].",".$transfer_status['rate'].") ")->execute();
			}
			echo CJSON::encode(array(
				'status'=>'success',
			));
			exit;
		}else{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'No invoices selected!',
				));
				exit;
		}		
	}

	public function actiongetAmtIncurrency() {
		$id = (int)$_POST['id'];	
		$amount= $_POST['amount'];
		$rate =Yii::app()->db->createCommand("select  rate from incoming_transfers where id = ".$id." LIMIT 1 ")->queryScalar();	
		echo json_encode(array_merge(array('status'=>'success', 'actnet'=> $amount, 'actrate' => $rate, 'net'=> $amount*$rate)));
			exit;
	}
	public function actiongetInvoiceDetail() {
		$id =  $_POST['id'];	
		 $select =Yii::app()->db->createCommand("select net_amount, currency from receivables where final_invoice_number = '".$id."' and old = 'No' LIMIT 1 ")->queryRow();	
		echo json_encode(array_merge(array('status'=>'success', 'net'=> $select['net_amount'],'curr'=> $select['currency'] )));
			exit;
	}

	public function actionGetExcel()
	{
		$model = new IncomingTransfers('getAll');
		$data = $model->getAll(null, true)->getData();	
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS IRs Export");		
		$sheetId = 0;
		
		$nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray); 

		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'TR #')
		->setCellValue('B1', 'Customer')
		->setCellValue('C1', 'Partner')
		->setCellValue('D1', 'Received Amount')
		->setCellValue('E1', 'currency')
		->setCellValue('F1', 'Offsetting')
		->setCellValue('G1', 'Status')
		->setCellValue('H1', 'Notes')
		->setCellValue('I1', 'Remarks')
		->setCellValue('J1', 'Created By')
		->setCellValue('K1', 'Created On')
		;
		$i = 1;
		foreach($data as $d => $row)
		{
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, ' '.$row->it_no)
			->setCellValue('B'.$i, $row->eCustomer->name)
			->setCellValue('C'.$i, Codelkups::getCodelkup($row->partner))
			->setCellValue('D'.$i, $row->received_amount) 
			->setCellValue('E'.$i, Codelkups::getCodelkup($row->currency))
			->setCellValue('F'.$i, IncomingTransfers::getOffsettingLabel($row->offsetting))
			->setCellValue('G'.$i, IncomingTransfers::getStatusLabel($row->status))
			->setCellValue('H'.$i, $row->notes)
			->setCellValue('I'.$i, $row->remarks)
			->setCellValue('J'.$i, (Users::getNameById($row->id_user)))
			->setCellValue('K'.$i, date('d/m/Y',strtotime($row->adddate)))
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Incoming Transfers - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="TRs_'.date("d_m_Y").'.xls"');
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
}
?>