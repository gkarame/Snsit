<?php
class InvoicesController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + delete',
		);
	}
	public function init()	{
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - Invoices');
	}
	public function accessRules(){
		return array(
						array('allow',
					'actions'=>array('sendInvoices','createSNSAPJ','sendUnassignedInvoices','createSNSAUST'),
					'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('index','view','getExcel','create','update', 'delete','sendToCustomer', 'download','changeStatus','updateHeader','changeInvoiceDate','InvoicesDates',
						'printOne','print','ChangeInvoiceDatePoPUp','PrintTransfer','getTransferInv','checkPrint','checkTransfer','CheckEmail','checkSend','changeStatuspopup' ,'checkStatus','shareAll','updateAmtEscalation', 'changeInput', 'printReceivables'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}	
	public function actionView($id, $active = null)	{
		if (!GroupPermissions::checkPermissions('financial-invoices'))	{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$model = $this->loadModel($id);		
		$title = 'Invoice #'.$model->invoice_number.' - '.Customers::getNameById($model->id_customer);
		$arr = Utils::getShortText('Invoice #'.$model->invoice_number);		
		$subtab = $this->getSubTab(Yii::app()->createUrl('invoices/view', array('id' => $id)));		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array( 
					'/invoices/view/'.$id => array(
							'label'=>$arr['text'],
							'url' => array('invoices/view', 'id'=>$id),
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
			'active' => $active,
		));
	}	
	public function actionCreate(){
		if (!GroupPermissions::checkPermissions('financial-invoices', 'write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/invoices/create' => array(
					'label'=> 'New Invoice',
					'url' => array('invoices/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Invoices();		$extra = array();	$model->sns_share=100;	
		if (isset($_POST['Invoices'])){
			$error = true;		$model->attributes = $_POST['Invoices'];	$model->project_name = $_POST['Invoices']['invoice_description'];
			$model->invoice_title = "";		$model->invoice_number = "00000";
			if ($model->partner == Maintenance::PARTNER_SNS){
				$model->partner_status = null;
			}
			else if ($model->partner == null){
				$model->partner_status = null;
			}				
			$model->status = Invoices::STATUS_NEW;
			$model->type=$_POST['Invoices']['type'];
			if(($_POST['Invoices']['type']=='Standard' || $_POST['Invoices']['type']=='T&M') && isset($_POST['Invoices']['id_ea']) && !empty($_POST['Invoices']['id_ea'])){				
				$model->id_ea= $_POST['Invoices']['id_ea'];
				$model->id_project=Eas::getIdProjByEa($model->id_ea);
			}
			$assigneduser= Yii::app()->db->createCommand("SELECT id_assigned from customers where id= ".$_POST['Invoices']['id_customer']." ")->queryScalar();
			if ( !empty($assigneduser) && $assigneduser!= 0){
				$model->id_assigned=$assigneduser;
				$updateinvoices = Yii::app()->db->createCommand("UPDATE `invoices` SET id_assigned='".$assigneduser."' WHERE id_customer='".$model['id_customer']."' and (id_assigned is null or id_assigned=0) ")->execute();
			}
			$model->net_amount = $model->amount * ($model->sns_share/100)*($model->payment_procente/100);
			$model->gross_amount = $model->amount *($model->payment_procente/100);
			$model->partner_amount = $model->amount * (1 - $model->sns_share/100)*($model->payment_procente/100);
			if($model->invoice_date_month == null)	{
				$error = false;	
				$extra['invoice_date_month'] = "Invoice month cannot be blank.";
			}
			if($model->invoice_date_year == null)	{
				$error = false;	
				$extra['invoice_date_year'] = "Invoice year cannot be blank.";
			}
			if ($model->save() && $error){				
				if (isset($model->project_name)){
					$model->project_name = $_POST['Invoices']['invoice_description'];
				}
				if ($model->project_name != null){
					$cardinal_number = Eas::cardinalNumber(1);
					$title = $model->project_name;//." - ".$cardinal_number." ". $model->payment_procente ."% Payment - ".Codelkups::getCodelkup($model->payment);
				}else{
					$title = "";
				}				
				$model->invoice_number = Utils::paddingCode($model->id);
				if ($model->invoice_number == "99999") {
					$model->invoice_number = "00000";
				}
				$model->invoice_title = $title;
				if($model->save()){
					Utils::closeTab(Yii::app()->createUrl('invoices/create'));
					$this->action_menu = Yii::app()->session['menu'];
					$this->redirect(array('invoices/view', 'id'=>$model->id, 'new' => 1));
				}
			}
		}
		$this->render('create', array('model' => $model,'extra'=>$extra));
	}
	 public function actionGetExcel(){


	 	Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS Invoices Export");
		$model =Invoices::model(); 
		//$model->customer_name= $_POST['Invoices']['customer_name'];	

		//print_r($_POST['Invoices']);exit;

		$criteria = new CDbCriteria;	
		$criteria->with = array('customer','idEa','project');
		$criteria->select = array(
				"t.*"
		);	

		if(!empty($_POST['Invoices']['project_name']) && $_POST['Invoices']['project_name']!='' &&  $_POST['Invoices']['project_name']!=' ')
		{
			$criteria->compare('project_name', $_POST['Invoices']['project_name'], true);
		}	
		if(!empty($_POST['Invoices']['id_customer']) && $_POST['Invoices']['id_customer']!='' &&  $_POST['Invoices']['id_customer']!=' ')
		{
			 $criteria->compare('customer.name',$_POST['Invoices']['id_customer'], true);  
		}	
		if(!empty($_POST['Invoices']['final_invoice_number']) && $_POST['Invoices']['final_invoice_number']!='' &&  $_POST['Invoices']['final_invoice_number']!=' ')
		{
			 $criteria->compare('final_invoice_number',$_POST['Invoices']['final_invoice_number'], true);  
		}	
		if(!empty($_POST['Invoices']['status']) && $_POST['Invoices']['status']!='' &&  $_POST['Invoices']['status']!=' ')
		{
			 $criteria->compare('t.status',$_POST['Invoices']['status'], true);  
		}	
		if(!empty($_POST['Invoices']['invoice_date_month']) && $_POST['Invoices']['invoice_date_month']!='' &&  $_POST['Invoices']['invoice_date_month']!=' ')
		{
			 $criteria->compare('invoice_date_month',$_POST['Invoices']['invoice_date_month']);  
		}	
		if(!empty($_POST['Invoices']['invoice_date_year']) && $_POST['Invoices']['invoice_date_year']!='' &&  $_POST['Invoices']['invoice_date_year']!=' ')
		{
			 $criteria->compare('invoice_date_year',$_POST['Invoices']['invoice_date_year']);  
		}	
		if(!empty($_POST['Invoices']['partner'])  && $_POST['Invoices']['partner']!='' &&  $_POST['Invoices']['partner']!=' ')
		{
			 $criteria->compare('partner',$_POST['Invoices']['partner'],true);  
		}

		if (!empty($_POST['Invoices']['type']) && $_POST['Invoices']['type']!='' &&  $_POST['Invoices']['type']!=' '){         	
			$types=$_POST['Invoices']['type'];	$inv_type="";
        	foreach ($types as $value) {
        	$inv_type.="'".rtrim(ltrim($value," ")," ")."',";
        	}       	
        	$criteria->addCondition("t.type in (".$inv_type." '-1' ) ");  
        } 

        if(isset($_POST['Invoices']['id_ea']) && $_POST['Invoices']['id_ea']!='' && $_POST['Invoices']['id_ea']!=' '){      		
      	 	$eassubmitted=" ";		$eassubmitted=str_replace(","," ",$_POST['Invoices']['id_ea']); $eassubmitted=str_replace(" ",",",$eassubmitted);		$eanos = array();	$easnos=explode(",", $eassubmitted);	$eanos="";       		
       		if (count($easnos)==1){
       		$criteria->compare('idEa.id', ltrim($_POST['Invoices']['id_ea'],"0"), true);
       		}else{
      	 	foreach ($easnos as $ea) { $eanos.=" '".rtrim(ltrim(ltrim($ea,"0")," ")," ")."' ,";}
       			 $criteria->addCondition("idEa.id IN (".$eanos." 0) ");
    		}
       	} 
       	if(isset($_POST['Invoices']['payment']) && $_POST['Invoices']['payment'] == 1)
       	{
       		 $criteria->addCondition("t.id = (select MIN(i.id) from invoices i where i.id_ea = t.id_ea and (i.status ='New' or i.status= 'To Print')) ");

       	}

$group = NULL; $export = false;
		$dataProvider = new CActiveDataProvider('Invoices', array(
				'criteria' => $criteria,
				'pagination'=>($group != null || $export) ? false : array(
						'pageSize' => 500000,
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => $group ? $group : ($export ? 'customer.name ASC' : 't.final_invoice_number ASC'),
           		 ),
		));



        //$model = new Invoices('getAll');
        $data  = $dataProvider->getData();
        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('Error PHP Excel extension');
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")->setLastModifiedBy("http://www.sns-emea.com")->setTitle("SNS Invoices Export");
        $sheetId = 0;

        $nb = sizeof($data);  
        //print_r($dataProvider);exit;       
        $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

       $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->applyFromArray($styleArray); 


        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A1', 'Invoice#')->setCellValue('B1', 'Customer')->setCellValue('C1', 'EA #')->setCellValue('D1', 'EA Type')
        ->setCellValue('E1', 'Invoice Title')->setCellValue('F1', 'Partner')->setCellValue('G1', 'Partner Inv')->setCellValue('H1', 'Currency')->setCellValue('I1', 'Gross Amount')
        ->setCellValue('J1', 'Net Amount')->setCellValue('K1', 'Partner Status')->setCellValue('L1', 'Status')->setCellValue('M1', 'Invoice Date')->setCellValue('N1', 'Paid Date')
        ->setCellValue('O1', 'Old')->setCellValue('P1', 'Payment')->setCellValue('Q1', 'Percent')->setCellValue('R1', 'SNS Share')->setCellValue('S1', 'ESC%')->setCellValue('T1', 'Remarks')
        ->setCellValue('U1', 'Notes')->setCellValue('V1', 'Assigned To');
        $i = 1;
        foreach ($data as $d => $row) {
            $net   = $row->net_amount;
            $gross = $row->gross_amount;
            /*$final = $row->final_invoice_number;
            if ($row->partner == '554') {
                $final = $row->snsapj_partner_inv;
            }*/
            if ($row->partner == '79' && isset($row->span_partner_inv)) {
                $pinv = $row->span_partner_inv;
            } else if($row->partner == '201' && !isset($row->partner_inv)) {
                $pinv = $row->snsapj_partner_inv;
            }else{
            	$pinv = $row->partner_inv;
            }
            $i++;
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . $i, $row->invoice_number)
                     ->setCellValue('B' . $i, isset($row->customer->name) ? $row->customer->name : '')->setCellValue('C' . $i, isset($row->id_ea) ? $row->id_ea : "")->setCellValue('D' . $i, isset($row->id_ea) ? Codelkups::getCodelkup(Eas::getCategoryById($row->id_ea)) : "")
                     ->setCellValue('E' . $i, $row->invoice_title)->setCellValue('F' . $i, $row->partner ?  Codelkups::getCodelkup($row->partner) : '')->setCellValue('G' . $i, $pinv)
					->setCellValue('H' . $i, Codelkups::getCodelkup($row->currency))->setCellValue('I' . $i, $gross)->setCellValue('J' . $i, $net)
					->setCellValue('K' . $i, $row->partner != '77' ? $row->partner_status : '')->setCellValue('L' . $i, $row->status)->setCellValue('M' . $i, ($row->getinvdate()))
					->setCellValue('N' . $i, ($row->paid_date != '0000-00-00') ? date("d/m/Y", strtotime($row->paid_date)) : '')->setCellValue('O' . $i, $row->old)->setCellValue('P' . $i, $row->payment)
					->setCellValue('Q' . $i, $row->payment_procente . "%")->setCellValue('R' . $i, $row->sns_share . ' %')->setCellValue('S' . $i, ($row->type == 'Maintenance') ? $row->escalation.'%' : '')->setCellValue('T' . $i, $row->remarks)->setCellValue('U' . $i, $row->notes)
					->setCellValue('V' . $i, (Users::getUsername($row->id_assigned)));
        }
		$objPHPExcel->getActiveSheet()->setTitle('Invoices # - ' . date("d m Y"));
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
	public function actionDelete($id=null){
		if (!GroupPermissions::checkPermissions('financial-invoices','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$msg=''; $i=0;
		if (isset($_POST['checkinvoice'])) 	{
			$ids_invoices = $_POST['checkinvoice'];
			//print_r($ids_invoices);exit;
			foreach ($ids_invoices as $id) {
				$model = $this->loadModel($id);
				if ($model != null && ($model->status == Invoices::STATUS_NEW || $model->status == Invoices::STATUS_TO_PRINT || $model->status == Invoices::STATUS_CANCELLED)){
						//$dirPath = dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$model->id_customer.'/invoices/';
						//Utils::deleteSearchFile($dirPath, 'INVOICE_'.$model->invoice_number); 
						$model->delete();
						$i++;
				}else if ($model->status != Invoices::STATUS_NEW && $model->status != Invoices::STATUS_TO_PRINT && $model->status != Invoices::STATUS_CANCELLED){
					$msg='no';
				}
			}
		}else{
			echo json_encode(array_merge(array(
				'status'=>'fail',
				'message' => 'Please select an invoice'
			)));
			exit;
		}

		if ($msg != '' && $i>0)
		{
			echo json_encode(array_merge(array(
				'status'=>'halffail',
				'message' => 'Not all invoices were deleted'
			)));
			exit;
		}else if ($msg !='')
		{
			echo json_encode(array_merge(array(
				'status'=>'fail',
				'message' => 'Selected invoice(s) cannot be deleted'
			)));
			exit;
		}

		echo json_encode(array_merge(array(
				'status'=>'success',
		)));
		exit;
	}
	public function actionEdit(){
		if(!GroupPermissions::checkPermissions('financial-invoices','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->render('edit');
	}
	public function actionDownload($id) {	
		$model = $this->loadModel((int) $id);		
		if (($path = $model->getFile(true)) !== null) {			
			$name = pathinfo($path, PATHINFO_BASENAME);
			header('Content-disposition: attachment; filename='.$name);
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			if ($extension == 'pdf') 	{
				header('Content-type: application/pdf');
			}else {
				header('Content-type: application/octet-stream');
			}
			$file= $model->getFile(true);
			chmod($file, 0777);
			readfile($file);
		}			
	}	
	public function actionIndex(){
		if (!GroupPermissions::checkPermissions('financial-invoices')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['Invoices']) ? $_GET['Invoices'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge( 
			$this->action_menu,
			array(
				'/invoices/index' => array(
					'label'=>Yii::t('translations','Invoices'),
					'url' => array('invoices/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/scripts/jquery.blockUI.js', CClientScript::POS_END)
								->registerScriptFile(Yii::app()->baseUrl.'/scripts/jquery.cookie.js', CClientScript::POS_END);		
		$model = new Invoices('search');
		$model->unsetAttributes();
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionInvoicesDates(){
		if (isset($_POST['checkinvoice'])){
			$id_invoices=$_POST['checkinvoice'];
			$invoice_table="<table id=\"invoicestable\"> <tr><th>Invoice #</th><th>Customer Name</th><th>Title</th><th>Date</th></tr>";	
				foreach ($id_invoices as $inv) {
					    $distinct_inv = Yii::app()->db->createCommand("SELECT id,invoice_number, id_customer , invoice_title ,invoice_date_month ,invoice_date_year from invoices where id = '$inv' and (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' OR status='".Invoices::STATUS_TO_PRINT."')")->queryAll();
						if(!isset($distinct_inv)){
								echo json_encode(array(	"status"=>"failure" ,'message'=>"Dates has been already set for all the resources."));
								exit;	
						}
						foreach ($distinct_inv as $valueu) {
						$invoice_table.="<tr><td>".$valueu['invoice_number']."</td><td>".Customers::getNameById($valueu['id_customer'])."</td><td>
						<div class=\"inline-block\" onmouseenter=\"showToolTipM(this);\" onmouseleave=\"hideToolTipM(this);\">
						<div class=\"first_it panel_container\">
						<span class=\"clip\">".substr($valueu['invoice_title'],0 ,5)."</span><u class=\"red\">+</u>
							 <div class=\"panelM\" style = \"left:80px\">
								 <div style=\"  background-image: url('/images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;\"></div>
								<div style=\"  background-image: url('/images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;\">
								 	<div class=\"coverM\" style=\"background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;\">".$valueu['invoice_title']."</div>
								 </div>
								 <div  style=\"background-image: url('/images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;\"></div>			
							 </div>
						 </div>
			 			</div>
					</td><td>".Invoices::getInvoiceDate($valueu['invoice_date_month'],$valueu['invoice_date_year'],$valueu['id'])."</td></tr>";
						}				
				}		
			$invoice_table.="</table>"	;
			echo json_encode(array(
											"status"=>"success",
											'invoice_table'=> $invoice_table
									));
									exit;	
			}else{ 						
					echo json_encode(array(	"status"=>"failure"));
										exit;	

			}
	}
	public function actionChangeStatus($id = null)	{
		$models_to_print = array();
		if ($id != null){
			$nr = Yii::app()->db->createCommand("UPDATE `invoices` SET status='".Invoices::STATUS_TO_PRINT."' WHERE (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' or status='".Invoices::STATUS_TO_PRINT."' or status='".Invoices::STATUS_PAID."') AND id='".(int)$id."' AND invoice_date_month != 0 AND invoice_date_month != 0 AND not isnull(invoice_date_month) AND not isnull(invoice_date_month) ")->execute();
			if ($nr != 0)	{
				$model =  $this->loadModel($id);
				array_push($models_to_print,$model);
				self::sendNotificationsEmails($models_to_print, 'to_print');				
				echo CJSON::encode(array(
					'status'=>'success',
					'status_invoice' =>Invoices::STATUS_TO_PRINT
				));
				exit;
			}else{
				echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'No Invoice was updated.',
				));
				exit;
			}			
		}
		elseif (!isset($_POST['checkinvoice']))	{
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one invoice!',
			));
			exit;
		}		
		$ok = true;		$ids_invoices = $_POST['checkinvoice'];		 $tot=0;
		foreach($ids_invoices as $id_invoice)	{
			$nr = Yii::app()->db->createCommand("UPDATE `invoices` SET status='".Invoices::STATUS_TO_PRINT."' WHERE id='".(int)$id_invoice."' AND (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' or status='".Invoices::STATUS_TO_PRINT."' or status='".Invoices::STATUS_PAID."') AND invoice_date_month != 0 AND invoice_date_month != 0 AND not isnull(invoice_date_month) AND not isnull(invoice_date_month)")->execute();
			if ($nr !=0){
				$tot++;
				$model =  $this->loadModel($id_invoice);
				array_push($models_to_print,$model);
			}
		}
		$all= implode(',', $ids_invoices);
		//print_r("select GROUP_CONCAT(name,', ') from customers where id in (select id_customer from invoices  WHERE id in (".$all.") and partner= 77) and (dolphin_aux is null or dolphin_aux='')");exit;
		/*$aux= Yii::app()->db->createCommand("select name from customers where id in (select id_customer from invoices  WHERE id in (".$all.") and partner= 77) and (dolphin_aux is null or TRIM(dolphin_aux)='')")->queryAll();
						
		if($tot>0 && !empty($aux)){
			 
			$straux=implode(', ', array_column($aux,'name'));
			//$aux= substr($aux, 0, (strlen($aux)-2));
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => "Not all invoices were successfully updated to status 'To Print', Kindly specify the dolphin auxiliary for direct invoice customer(s): ".$straux,
				));
				exit;
				$ok = false;

		}else if(!empty($aux))	{
			$straux=implode(', ', array_column($aux,'name'));

			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => 'Kindly specify the dolphin auxiliary for direct invoice customer(s): '.$straux,
				));
				exit;
				$ok = false;
		}	*/
		if ($models_to_print != null){
			self::sendNotificationsEmails($models_to_print, 'to_print');
		}
		echo CJSON::encode(array(
			'status'=>'success',
			'ok' => $ok,
		));
		exit;	
	}
	public function actionUpdateHeader($id){
		$id = (int)$id;		$error = 0;		$model = $this->loadModel($id);		$models_to_print = array();		
		if (isset($_POST['Invoices']))	{
			if($model->partner_status ='Not Paid' && $_POST['Invoices']['status'] == 'Paid'){
				if ($_POST['Invoices']['partner_status'] == 'Not Paid'){
					echo json_encode(array(
					'status'=>'failure',				
					));
					Yii::app()->end();
				}
			}
			$old_status = $model->status;
			$old_esc= $model->escalation;
			$old_gross = $model->gross_amount;
			$model->attributes = $_POST['Invoices'];
			if(isset($_POST['months'])){
				$model->invoice_date_month = $_POST['months'];
			}
			if($old_status=='Printed' && $model->status=='To Print'){
				 $model->printed_date= null; 
			}
			if(isset($_POST['years']))	{
				$model->invoice_date_year = $_POST['years'];
			}
			if(isset($_POST['Invoices']['old']) && $_POST['Invoices']['old']=='Yes')	{	
				if($model->status=='Printed'){ $model->status='To Print'; $model->printed_date= null; }
				$model->final_invoice_number = null;
			}
			if(isset($_POST['Invoices']['partner_inv']))	{
				$model->partner_inv = $_POST['Invoices']['partner_inv'];
			}
			$model->gross_amount=(float)$_POST['Invoices']['gross_amount'];	
			$model->invoice_title = $_POST['Invoices']['invoice_title'];
			/*if($model->partner == Maintenance::PARTNER_SNS)	{
				$model->sns_share = 100;
				$model->partner_status = null;
			}*/
			if($model->payment_procente != 0){		
				$model->partner_amount =(1-$model->sns_share/100)*$model->gross_amount;
				$model->net_amount = ($model->sns_share/100)*$model->gross_amount;					
			}else{
				$model->net_amount = ($model->gross_amount)*($model->sns_share/100); 
				$model->partner_amount = ($model->gross_amount)*(1-$model->sns_share/100); 	
			}
			if(!empty($_POST['Invoices']['escalation']))
			{
				$model->escalation= str_replace('%', '',$_POST['Invoices']['escalation']);
			}
 
			if($error==0){	
				if ($model->save()){
					if(($old_esc != $model->escalation || $old_gross != $model->gross_amount) && $model->type == 'Maintenance')
					{
						Yii::app()->db->createCommand("UPDATE maintenance_invoices SET escalation_factor=".$model->escalation." ,amount=".$model->gross_amount." WHERE id_invoice =".$model->id." ")->execute();
					}
					if($model->status == 'Cancelled' and !empty($model->id_ea))
					{
						/*$getremaininginv= Yii::app()->db->createCommand("SELECT count(1) from invoices where id_ea =".$model->id_ea." and status!= 'Cancelled' ")->queryScalar();
						if($getremaininginv == 0)
						{
							 Yii::app()->db->createCommand("UPDATE eas SET status=0  WHERE id =".$model->id_ea." ")->execute();
						}else*/
						{							
							$getremaininginv2= Yii::app()->db->createCommand("SELECT count(1) from invoices where id_ea =".$model->id_ea." and status!= 'Cancelled' and status!= 'Printed' ")->queryScalar();
							if($getremaininginv2 == 0)
							{
								 Yii::app()->db->createCommand("UPDATE eas SET status=5  WHERE id =".$model->id_ea." ")->execute();
							}
						}
						$training= Yii::app()->db->createCommand("SELECT id_training from training_eas where id_ea =".$model->id_ea." ")->queryScalar();
						if(!empty($training))
						{
							 $easstat= Yii::app()->db->createCommand("SELECT count(1) from eas where id in (select id_ea from training_eas where id_training= ".$training.") and status != '0' and status!='5'")->queryScalar();
								$inv=  Yii::app()->db->createCommand("SELECT count(1) from invoices where id_ea in  (select id_ea from training_eas where id_training= ".$training.") and status != 'Cancelled' ")->queryScalar();
								if($easstat == 0 && $inv ==0) {
									Yii::app()->db->createCommand("UPDATE trainings_new_module SET status = 0 WHERE idTrainings =".$training." ")->execute();
								}
						}

					}
					if($model->status == "To Print" && $old_status != $model->status){
						array_push($models_to_print,$model);
						self::sendNotificationsEmails($models_to_print,'to_print');
					}
					echo json_encode(array(
							'status'=>'saved', 
							'html'=>$this->renderPartial('_header_content', array('model'=> $model), true, true)
						));
					Yii::app()->end();
				}		
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
		)));
		Yii::app()->end();
	}
	
	public function actioncreateSNSAUST()	{
		$tasks=Invoices::getInvoicesDataAUST();
		$getinsertID= Yii::app()->db->createCommand("SELECT max(id)+1 from invoices")->queryScalar();
		$getinsertID= Utils::paddingCode($getinsertID); 
		$year= date('Y/m', strtotime("-1 month"));  $inv= array(); $i=0;
		$assigneduser= Yii::app()->db->createCommand("SELECT id_assigned from customers where id=323 ")->queryScalar();		
		if(!empty($tasks))	{
			foreach ($tasks as $task) {
					$amount=$task['amount']; 
				//$title= Invoices::getTitleAUSTInv($task['month'],$task['year'],$task['currency'] );
				$title='Being the Expenses incurred on your behalf for related to Management, Marketing, Finance, Logistics, Accounting, Client Management & Administration';
				$insertRecord = Yii::app()->db->createCommand("insert into invoices (final_invoice_number, id_customer, invoice_number, invoice_title, id_project,project_name, type,payment, payment_procente, status,currency, partner,sns_share, invoice_date_month, invoice_date_year, sold_by, old,partner_status,net_amount,gross_amount,partner_amount,amount, paid_date,id_assigned, printed_date)
				values
				('".$year."',323, '".$getinsertID."', '".$title."',0,'".$title."','Standard',1,100,'To Print', ".$task['currency'].",77, 100,".$task['month'].", ".$task['year'].",0,'No','Not Paid',".$amount.", ".$amount.", 0,".$amount.",'0000-00-00',".$assigneduser.",'".date('Y-m-d')."')")->execute();
				$inv[$i]= $getinsertID; $i++;
				$getinsertID=Utils::paddingCode($getinsertID+1);
			}
			//self::sendcreateaustinvoice(implode(',',  $inv));
		}
	}
	public function sendcreateaustinvoice($ids_invoices ){        
        $notif = EmailNotifications::getNotificationByUniqueName('aust_inv');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $smas = "Dear Team,<br/><br/>Kindly find attached the SNS AUST invoice of " . Sma::getMonthName(date('m', strtotime("-1 month"))) . " " . date('Y', strtotime("-1 month")) . ".<br/><br/>Thank you,<br/>SNSit";
            $subject = $notif['name'];
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $smas
                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                Yii::app()->mailer->AddAddress($email);
            }    



		$qqq = array();		$q = array();
		$ids_invoices = '('.$ids_invoices.')';
		print_r($ids_invoices)	;exit;	
		$id_dis = Yii::app()->db->createCommand("SELECT DISTINCT i.id_project, i.id_customer,i.partner , i.old FROM invoices i WHERE i.status = 'To Print' AND id IN $ids_invoices ")->queryAll();
		if ($id_dis != null){
			foreach ($id_dis as $id){
				array_push($qqq, $id);
			}
			$this->generatePdf('invoices', $qqq, null, null, null, $ids_invoices); 
			$file = Invoices::getFileMore();	
			if ($file !== null) 
			{
				header('Content-disposition: attachment; filename=INVOICE_'.date('Y-m-d').'.pdf');
				header('Content-type: application/pdf');
				chmod($file, 0777);
				readfile($file);
				$file = Invoices::getFileMore();	
				Yii::app()->mailer->AddFile($file);
				Yii::app()->end();
			}
		}                     
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true); 
        }
    }
	public function actioncreateSNSAPJ()	{
		$tasks=Invoices::getInvoicesData();
		$getinsertID= Yii::app()->db->createCommand("SELECT max(id)+1 from invoices")->queryScalar();
		$getinsertID= Utils::paddingCode($getinsertID);
		$assigneduser= Yii::app()->db->createCommand("SELECT id_assigned from customers where id=246 ")->queryScalar();		
		if(empty($tasks))	{
			$insertRecord = Yii::app()->db->createCommand("insert into invoices (id_customer, invoice_number, invoice_title, id_project,project_name, type,payment, payment_procente, status,currency, partner,sns_share, invoice_date_month, invoice_date_year, sold_by, old,partner_status,net_amount,gross_amount,partner_amount,amount, paid_date,id_assigned)
			values
			(246, '".$getinsertID."', 'SNSI-APJ invoice',0,'SNSI-APJ invoice','Standard',1,100,'To Print', 9,554, 0, MONTH((CURRENT_DATE - INTERVAL 1 MONTH)), YEAR((CURRENT_DATE - INTERVAL 1 MONTH)),0,'No','Not Paid',0, 0,0,0,'0000-00-00',".$assigneduser.")")->execute();
		}else{
			foreach ($tasks as $task) {
				$amount=$task['amount']+($task['amount']/100); 
				$title= Invoices::getTitleAPJInv($task['month'],$task['year'],$task['currency'] );
				$insertRecord = Yii::app()->db->createCommand("insert into invoices (id_customer, invoice_number, invoice_title, id_project,project_name, type,payment, payment_procente, status,currency, partner,sns_share, invoice_date_month, invoice_date_year, sold_by, old,partner_status,net_amount,gross_amount,partner_amount,amount, paid_date,id_assigned)
				values
				(246, '".$getinsertID."', '".$title."',0,'".$title."','Standard',1,100,'To Print', ".$task['currency'].",554, 0,".$task['month'].", ".$task['year'].",0,'No','Not Paid',0, ".$amount.", ".$amount.",".$amount.",'0000-00-00',".$assigneduser.")")->execute();
				$getinsertID=Utils::paddingCode($getinsertID+1);
			}
		}
	} 
	public function actionsendUnassignedInvoices()
	{	
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('invoices_unassigned');
		$models=Yii::app()->db->createCommand("select i.invoice_number, i.final_invoice_number, i.id_ea, i.gross_amount, i.net_amount, i.currency, i.partner, i.sns_share, i.old, c.name  from invoices i, customers c where c.id=i.id_customer and ( i.id_assigned is null or i.id_assigned =0) and i.status='Printed' ")->queryAll();
		if ($notif != NULL && $models!=null){
			$to_replace = array(
				'{body}',
			);

			$text='Dear All,<br /><br />Please find below all invoices that are not assigned to a resource:<br/><br/>';
			$text.="<table border='1'  style='font-family:Calibri;' ><tr><th>Invoice#</th><th>Final Invoice#</th><th>EA#</th><th>Customer</th><th>Gross Amount</th><th>Net Amount</th><th>Currency</th><th>Partner</th><th>SNS Share</th><th>Old</th></tr>";
			foreach($models as $model){
					
				 	$text.="<tr><td>".$model['invoice_number']."</td><td>".$model['final_invoice_number']."</td><td>".$model['id_ea']."</td> <td>".$model['name']."</td>  <td>".Utils::formatNumber($model['gross_amount'])." </td> <td> ".Utils::formatNumber($model['net_amount'])." </td> <td style='text-align:center'>  ".Codelkups::getCodelkup($model['currency'])."</td> <td style='text-align:center'>  ".Codelkups::getCodelkup($model['partner'])."</td> <td style='text-align:center'>  ".$model['sns_share']."%</td> <td>".$model['old']."</td></tr>";
			}
			$text.="</table>";	
			$subject = $notif['name'];
			$replace = array(
					$text,	
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
	public function actionsendInvoices()	{		
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('invoices_printed_lastm');
		if ($notif != NULL){
			$to_replace = array(
				'{group_description}',
				'{body}',
			);
			$invoices = '';	$i = 1;	$total='';	$total2='';	$mon=date('m');			
			if ($mon=='01')	{
				$models=Yii::app()->db->createCommand("select id_ea, case when i.final_invoice_number is null then (case when (i.old_sns_inv is null or i.old_sns_inv='') then (case when (i.snsapj_partner_inv is null or i.snsapj_partner_inv='') then i.partner_inv else i.snsapj_partner_inv end )  else i.old_sns_inv end ) else i.final_invoice_number 	end final_invoice_number, i.printed_date,c.name,i.partner,i.sns_share, sum(i.gross_amount) as gross_amount,sum(i.net_amount) as net_amount,i.currency 
					, case when i.id_ea is not null then (SELECT TM from eas where id=i.id_ea) else 0 end tandm, i.old as old
					from invoices i, customers c
					where i.invoice_date_month= 12
					and i.invoice_date_year=(YEAR(CURRENT_DATE()) -1)
					and i.type not in ('Maintenance','Expenses','Travel Expenses') 
					and i.status in ('Printed','Paid') 
					and i.id_customer=c.id
					group by i.final_invoice_number, i.old_sns_inv, i.partner_inv
					ORDER BY c.name ")->queryAll();
			}else{
				$models=Yii::app()->db->createCommand("select id_ea, case when i.final_invoice_number is null then (case when (i.old_sns_inv is null or i.old_sns_inv='') then (case when (i.snsapj_partner_inv is null or i.snsapj_partner_inv='') then i.partner_inv else i.snsapj_partner_inv end )  else i.old_sns_inv end ) else i.final_invoice_number 	end final_invoice_number, i.printed_date,c.name,i.partner,i.sns_share, sum(i.gross_amount) as gross_amount,sum(i.net_amount) as net_amount,i.currency 
				, case when i.id_ea is not null then (SELECT TM from eas where id=i.id_ea) else 0 end tandm, i.old as old
				from invoices i, customers c
				where i.invoice_date_month=(MONTH(CURRENT_DATE())-1) 
				and i.invoice_date_year=YEAR(CURRENT_DATE()) 
				and i.type not in ('Maintenance','Expenses','Travel Expenses') 
				and i.status in ('Printed','Paid') 
				and i.id_customer=c.id
				group by i.final_invoice_number, i.old_sns_inv, i.partner_inv
				ORDER BY c.name
				")->queryAll();
			}			
			if(empty($models))	{
				$invoices.='No invoices were printed last month.<br>';
			}else{				
				$invoices.='Kindly find below all invoices printed in the past month:<br/><br/>';
				$invoices.="<table border='1'  style='font-family:Calibri;' ><tr><th>Invoice#</th><th>EA#</th><th>Customer</th><th>Gross Amount</th><th>Net Amount</th><th>Currency</th><th>Partner</th><th>SNS Share</th><th>Printed Date</th><th>T&M</th><th>Old</th></tr>";
				foreach($models as $model){
					if ($model['tandm'] == 0)
						$f='No';
					else
						$f='Yes';
				 	$invoices.="<tr><td>".$model['final_invoice_number']."</td><td>".$model['id_ea']."</td> <td>".$model['name']."</td>  <td>".Utils::formatNumber($model['gross_amount'])." </td> <td> ".Utils::formatNumber($model['net_amount'])." </td> <td style='text-align:center'>  ".Codelkups::getCodelkup($model['currency'])."</td> <td style='text-align:center'>  ".Codelkups::getCodelkup($model['partner'])."</td> <td style='text-align:center'>  ".$model['sns_share']."%</td><td style='text-align:center'>  ".date('d-m-y',strtotime($model['printed_date']))."</td> <td style='text-align:center'>  ".$f."</td><td>".$model['old']."</td></tr>";
				}
				$invoices.="</table>";
			}
			$subject = $notif['name'];
			$replace = array(
					EmailNotificationsGroups::getGroupDescription($notif['id']),
					$invoices,	
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
	public function actionChangeInvoiceDate(){
		$value = $_POST['value'];
		$table = $_POST['type'];
		$id = $_POST['id'];
		if($table == 'month'){
			$table_name = 'invoice_date_month';
			$currentyear=date('Y');	
			$nrm = Yii::app()->db->createCommand("UPDATE invoices SET invoice_date_year = '$currentyear' WHERE id ='$id' ")->execute();
		}else {
			$table_name = 'invoice_date_year';
		}
		$nr = Yii::app()->db->createCommand("UPDATE invoices SET {$table_name} = '$value' WHERE id ='$id' ")->execute();
		if($nr != 0)
			echo json_encode(array_merge(array(
				'status'=>'success',
				)));
		else 
			echo json_encode(array_merge(array(
				'status'=>'fail',
				'message'=>'Error'
			)));
	}
	public function getStatus(){	}
	public function actionChangeInvoiceDatePoPUp(){
		$value = $_POST['value'];
		$table = $_POST['type'];
		$ids = $_POST['ids'];
		$marr = explode("checkinvoice%5B%5D", $ids);
		$filterd_ids = array();
		foreach($marr as $valy){
			preg_match('!\d+!', $valy, $match);
			if($match != null)
				array_push($filterd_ids, $match[0]);
		}
if (!isset($filterd_ids)){
			echo CJSON::encode(array(
				'status'=>'fail',
				'message' => ' You have to select at least one invoice!',
			));
			exit;
		}else{
			$error = 1;
			if($table == 'month'){
				$table_name = 'invoice_date_month';
			foreach($filterd_ids as $idinv){
				$currmonth = Yii::app()->db->createCommand("SELECT {$table_name} FROM invoices WHERE id ='".$idinv."' and (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' OR status='".Invoices::STATUS_TO_PRINT."') ")->queryScalar();
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET {$table_name} = '$value' WHERE id ='".$idinv."' and (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' OR status='".Invoices::STATUS_TO_PRINT."') ")->execute();
			if ($nr == 0 && $currmonth != $value){
				$error = 0;
				break;
				}
			}
		}else {
			$table_name = 'invoice_date_year';
		foreach($filterd_ids as $idinv){
			$curryear = Yii::app()->db->createCommand("SELECT {$table_name} FROM invoices WHERE id ='".$idinv."' and (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' OR status='".Invoices::STATUS_TO_PRINT."') ")->queryScalar();
			//print_r($curryear);print_r($value);exit();
			$nr = Yii::app()->db->createCommand("UPDATE invoices SET {$table_name} = '$value' WHERE id ='".$idinv."' and (status='".Invoices::STATUS_NEW."' OR status='".Invoices::STATUS_PRINTED."' OR status='".Invoices::STATUS_TO_PRINT."')")->execute();
			if ($nr == 0 && $curryear != $value){
				$error = 0;
				break;
				}
			}
		}
		if($error != 0)
			echo json_encode(array_merge(array(
				'status'=>'success',
				)));
		else 
			echo json_encode(array_merge(array(
				'status'=>'fail',
				'message'=>'Please Make Sure that ALL selected Invoices are not PAID'
			)));
		}
	}
	public function actionUpdateAmtEscalation()
	{
		$id = (int)$_POST['id_invoice'];
		$esc = $_POST['value'];
		if(!empty($esc) || $esc ==0)
		{
			$contract= Yii::app()->db->createCommand("select id_contract from maintenance_invoices where id_invoice=".$id)->queryScalar();
				
			$model = Maintenance::model()->findByPk($contract);
			$totgrossamount=$model->getTotalGrossAmountFromlastINVBefore($id);
			$totgrossamount = $totgrossamount + ($totgrossamount*$esc/100);
			$periodfreq = Maintenance::getPeriodFreq($model->frequency);	
			if(MaintenanceInvoices::getLastInvoice($contract) !== false)
			{
				$gross_amount = ($totgrossamount);	
			}else{
				$gross_amount = ($totgrossamount)/$periodfreq;	
		   	}
	//net_amount,gross_amount,	partner_amount,amount
			echo json_encode(array_merge(array(
							'status'=>'success',
							'gross_amount' => $gross_amount,
							)));
		
		}	else{

		echo json_encode(array_merge(array(
					'status'=>'fail',
					)));
		}
		exit;
	}
	public function actionChangeInput()	{
		$id = (int)$_POST['id_invoice'];
		$value = $_POST['value'];
		$type = $_POST['type'];			
		switch ($type)
		{
			case '1':
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET type = '$value' WHERE id ='$id' ")->execute();
				break;
			case '2':				
				$model = $this->loadModel($id);
				$model->partner = $value; 
				if ($value == Maintenance::PARTNER_SNS){
					$model->partner_status=null;
					$model->sns_share = 100;
					if ($model->payment_procente != 0)	{
						$model->net_amount = ((float)$model->amount)*((float)$model->sns_share/100)*((float)$model->payment_procente/100); 
						$model->gross_amount = ((float)$model->amount)*((float)$model->payment_procente/100); 
						$model->partner_amount = ((float)$model->amount)*(1-(float)$model->sns_share/100)*((float)$model->payment_procente/100); 
					} else 	{
						$model->net_amount = ((float)$model->amount)*((float)$model->sns_share/100); 
						$model->gross_amount = ((float)$model->amount); 
						$model->partner_amount = ((float)$model->amount)*(1-(float)$model->sns_share/100); 	
					}
				}else{
					if (empty($model->partner_status))	{
						$model->partner_status='Not Paid';
					}					
					if($model->id_expenses != null){
						$model->sns_share = 100;
					}else if($value == Maintenance::PARTNER_APJ){
						$model->sns_share = 0;
					}else if($value == Maintenance::PARTNER_AUST){
						$model->sns_share = 40;
					}else{
						$model->sns_share = 80;
					}					
					if ($model->payment_procente != 0)	{
						$model->net_amount = ((float)$model->amount)*((float)$model->sns_share/100)*((float)$model->payment_procente/100); 
						$model->gross_amount = ((float)$model->amount)*((float)$model->payment_procente/100); 
						$model->partner_amount = ((float)$model->amount)*(1-(float)$model->sns_share/100)*((float)$model->payment_procente/100); 
					} else 	{
						$model->net_amount = ((float)$model->amount)*((float)$model->sns_share/100); 
						$model->gross_amount = ((float)$model->amount); 
						$model->partner_amount = ((float)$model->amount)*(1-(float)$model->sns_share/100); 	
					}
				}				
				if ($model->save()){	
					$nr = 1;
				}else{
					$nr = 0;
				}
				break;
			case '3':
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET sold_by = '$value' WHERE id ='$id' ")->execute();
				break;
			case '4':
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET old = '$value' WHERE id ='$id' ")->execute();
				break;
			case '5':
				$model = $this->loadModel($id);
				$model->sns_share = $value;
				if($model->payment_procente != 0)
				{
					$model->net_amount = ((float)$model->amount)*((float)$model->sns_share/100)*((float)$model->payment_procente/100); 
					$model->gross_amount = ((float)$model->amount)*((float)$model->payment_procente/100); 
					$model->partner_amount = ((float)$model->amount)*(1-(float)$model->sns_share/100)*((float)$model->payment_procente/100); 	
				}else{
					$model->net_amount = ((float)$model->amount)*((float)$model->sns_share/100); 
					$model->gross_amount = ((float)$model->amount); 
					$model->partner_amount = ((float)$model->amount)*(1-(float)$model->sns_share/100); 	
				}
				if ($model->save())
				{
					$nr = 1;
				}
				else
				{
					$nr = 0;
				}
				break;
		}
		if ($nr != 0)
			echo json_encode(array_merge(array(
				'status'=>'success',
			)));
		else 
			echo json_encode(array_merge(array(
				'status'=>'fail',
				'message'=>'Error'
			)));
		exit;
	}
	public function actionPrintOne($id) {
		$email = false;
		$model = $this->loadModel((int) $id);
		if($model->type == 'Maintenance' && empty($model->po) && Customers::getLpo((int)$model->id_customer) == 'Yes')
		{
			$this->redirect(array(Utils::getMenuOrder(true)));
			exit;
		}
		if ($model->status == "To Print")	{
			$email = true;
			$this->generatePdf('invoicesOne', $model->id);
			$model = $this->loadModel((int) $id);
		} 
		$file = $model->getFilePrinted(true);
		if ($file !== null) 
		{	
			chmod($file, 0777);	
			if (Codelkups::getCodelkup($model->partner) == 'SPAN' && $model->old == "Yes")
			{
				header('Content-disposition: attachment; filename=INVOICE_'.str_replace('/','_',$model->invoice_number).'.pdf');
			}else{
				header('Content-disposition: attachment; filename=INVOICE_'.str_replace('/','_',$model->final_invoice_number).'.pdf');
			}
			header('Content-type: application/pdf');
			readfile($file);
			Yii::app()->end();
		} else{	
			$this->redirect(array(Utils::getMenuOrder(true)));
		}
	} 		
	public function actiongetTransferInv() {
		if (isset($_POST['checkinvoice'])){ 
			//print_r();exit;
			echo json_encode(array(
				'inv' => "",
				'TR_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
				'count' =>count($_POST['checkinvoice']),
			));
		}
	}	
	public function actionPrintTransfer() {
		/*if (isset($_GET['token'])){
			setcookie("fileDownloadToken", $_GET['token']);
		}*/
		$qqq = array();
		$q = array();	
		$transfers = '('.$_GET['checkinvoice'].')';
		$allinv=  Yii::app()->db->createCommand("SELECT invoice_number FROM incoming_transfers_details  WHERE id_it in ".$transfers." ")->queryAll();
		$ids_invoices=  '('.	implode(',', array_column($allinv, 'invoice_number')).')';
		//print_r($_GET['template']);exit;
		//print_r("SELECT DISTINCT invoice_number FROM incoming_transfers_details  WHERE id_it in ".$transfers." ");exit;
		$template= $_GET['template'];
		$transferid= $_GET['checkinvoice'];
		$id_dis = Yii::app()->db->createCommand("SELECT i.id, i.id_customer, i.final_invoice_number, i.type,i.net_amount,case when i.currency=9 
					THEN i.net_amount 
					else i.net_amount*(select c.rate from currency_rate c where c.currency=i.currency  order by c.date DESC  limit 1) end as usd_amount
					,
					case when i.currency=9 
					THEN i.gross_amount 
					else i.gross_amount*(select c.rate from currency_rate c where c.currency=i.currency  order by c.date DESC  limit 1) end as gross_amount
					,i.currency,i.partner,i.partner_inv, i.snsapj_partner_inv, i.invoice_title as description FROM invoices i WHERE i.id IN $ids_invoices and i.status not in ('New','To Print','Cancelled') order by i.type")->queryAll();
		if ($id_dis != null){
			foreach ($id_dis as $id){
				array_push($qqq, $id);
			}
			$this->generatePdf('transfers', $qqq, $template, $transferid, null, $ids_invoices); 			
			$file = Invoices::getTransferMore($template,$transferid);	
			if ($file !== null){
				chmod($file, 0777);
				header('Content-disposition: attachment; filename=TRANSFER_'.$transferid.'.pdf');
				header('Content-type: application/pdf');
				readfile($file);
				Yii::app()->end();				
			} else{
				$this->redirect(array(Utils::getMenuOrder(true)));
			}			
		}
	}
	public function actionPrint() {
		if (isset($_GET['token'])){
			setcookie("fileDownloadToken", $_GET['token']);
		}
		$qqq = array();		$q = array();
		$ids_invoices = '('.$_GET['checkinvoice'].')';		
		$id_dis = Yii::app()->db->createCommand("SELECT DISTINCT i.id_project, i.id_customer,i.partner , i.old FROM invoices i WHERE i.status = 'To Print' AND id IN $ids_invoices ")->queryAll();
		if ($id_dis != null){
			foreach ($id_dis as $id){
				array_push($qqq, $id);
			}
			$this->generatePdf('invoices', $qqq, null, null, null, $ids_invoices); 
			$file = Invoices::getFileMore(true);	
			if ($file !== null) 
			{
				$eas = Yii::app()->db->createCommand("SELECT DISTINCT id_ea FROM invoices i WHERE id IN $ids_invoices ")->queryAll();
				if(!empty($eas))
				{
				//	print_r($eas);exit;
					foreach($eas as $ea)
					{
						clearstatcache();
						//print_r(Yii::app()->getBaseUrl().'/output'.$ea['id_ea'].'.png');exit;
						if (file_exists($_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR .'output'.$ea['id_ea'].'.png'))
						{  
						    unlink(Yii::app()->getBaseUrl().'output'.$ea['id_ea'].'.png');
						}	
					}
				}
				chmod($file, 0777);
				header('Content-disposition: attachment; filename=INVOICE_'.date('Y-m-d').'.pdf');
				header('Content-type: application/pdf');
				readfile($file);
				
				Yii::app()->end();
			} else{
				$this->redirect(array(Utils::getMenuOrder(true)));
			}			
		}else{			
			$idp_diss = Yii::app()->db->createCommand("SELECT DISTINCT i.old_sns_inv FROM invoices i WHERE i.status = 'Printed' AND id IN ".$ids_invoices." and old='Yes' ")->queryAll();
			if ($idp_diss != null and !empty($idp_diss)){				
				foreach ($idp_diss as $id){
					$id_final = $id['old_sns_inv'];
					$id_inv = Yii::app()->db->createCommand("SELECT id FROM invoices WHERE old_sns_inv like '{".$id_final."}' ")->queryScalar();
					$model = $this->loadModel((int) $id_inv);
					$file = $model->getFilePrinted(true);
					if($file != null)
					{	chmod($file, 0777);
						array_push($qqq, $file);
					}
				}
				include_once(dirname(Yii::app()->request->scriptFile)."/protected/components/pdfConcat.php");				
				$pdf = new concat_pdf();
				$val = implode(' , ',$qqq);
				$pdf->setFiles($qqq);
				$pdf->concat();
				$pdf->Output("invoices.pdf", "D");
			}else{
				$idp_dis = Yii::app()->db->createCommand("SELECT DISTINCT i.final_invoice_number FROM invoices i WHERE i.status = 'Printed' AND id IN ".$ids_invoices." ")->queryAll();
			if ($idp_dis != null)	{ 	
				foreach ($idp_dis as $id)	{
					$id_final = $id['final_invoice_number'];
					$id_inv = Yii::app()->db->createCommand("SELECT id FROM invoices WHERE final_invoice_number like '{".$id_final."}' ")->queryScalar();
					$model = $this->loadModel((int) $id_inv);
					$file = $model->getFilePrinted(true);
					if($file != null)
					{	chmod($file, 0777);
						array_push($qqq, $file);
					}
				}
				include_once(dirname(Yii::app()->request->scriptFile)."/protected/components/pdfConcat.php");
				$pdf = new concat_pdf();
				$val = implode(' , ',$qqq);
				$pdf->setFiles($qqq);
				$pdf->concat();
				$pdf->Output("invoices.pdf", "D");
			}
			}			
		}		
	}
	public function actionPrintReceivables()	{
		/*if (!isset($_REQUEST['checkinvoice']))	{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one invoice!',
			));
			exit;
		}		
		$ids = implode(',', $_REQUEST['checkinvoice']);
		$inv_ids = array();
		$files = array();$qqq = array();		$q = array();
		$ids_invoices = "(".$ids.")";


		$id_dis = Yii::app()->db->createCommand("SELECT DISTINCT(i.final_invoice_number), i.id, i.partner_inv, i.snsapj_partner_inv, i.old_sns_inv, i.id_project, i.id_customer,i.partner , i.old FROM invoices i WHERE  i.id IN (".$ids.") group by final_invoice_number")->queryAll();
		 
		if ($id_dis != null){
			foreach ($id_dis as $id){
				array_push($qqq, $id);
			}
			$this->generatePdf('receivables', $qqq, null, null, null, $ids_invoices); 
			$file = Invoices::getFileMore();	
			if ($file !== null) 
			{
				header('Content-disposition: attachment; filename=INVOICE_'.date('Y-m-d').'.pdf');
				header('Content-type: application/pdf');
				readfile($file);
				Yii::app()->end();
			} else{
				$this->redirect(array(Utils::getMenuOrder(true)));
			}			
		}
		*/
		if (!isset($_REQUEST['checkinvoice']))	{
			echo CJSON::encode(array(
					'status'=>'fail',
					'message' => ' You have to select at least one invoice!',
			));
			exit;
		}		
		$ids = implode(',', $_REQUEST['checkinvoice']);
		$inv_ids = array();
		$files = array();
		$id_dis = Yii::app()->db->createCommand("SELECT DISTINCT(i.final_invoice_number), MAX(i.id) as id FROM invoices i WHERE  i.id IN (".$ids.") group by final_invoice_number union select DISTINCT(i.partner_inv), MAX(i.id) as id FROM invoices i WHERE  i.id IN (".$ids.") and partner_inv is not null group by partner_inv")->queryAll();
		if ($id_dis != null){
			foreach ($id_dis as $id){
				$inv_ids[] = $id['id'];
			}
			$invoices = Invoices::model()->findAllByPk($inv_ids);
			foreach ($invoices as $inv){
				$file2= $inv->getSNSPrinted(true);
				if ($file2 != null){
					chmod($file2, 0777);
					array_push($files, $file2);
				}
				$file = $inv->getFilePrinted2(true);
				if ($file != null && $file2 ==null){
					chmod($file, 0777);
					array_push($files, $file);
				}
			}
			if (!empty($files)){
				include_once(dirname(Yii::app()->request->scriptFile)."/protected/components/pdfConcat.php");
				$pdf = new concat_pdf();
				$pdf->setFiles($files);
				$pdf->concat();
				$pdf->Output("invoices.pdf", "D");
			}
		}else{
				$id_dis = Yii::app()->db->createCommand("SELECT DISTINCT(i.old_sns_inv), MAX(i.id) as id  FROM invoices i WHERE  i.id IN (".$ids.") group by old_sns_inv ")->queryAll();
				if ($id_dis != null){
					foreach ($id_dis as $id){
						$inv_ids[] = $id['id'];
					}
					$invoices = Invoices::model()->findAllByPk($inv_ids);
					foreach ($invoices as $inv){
						$file2= $inv->getSNSPrinted(true);
						if ($file2 != null){
							array_push($files, $file2);
						}
						$file = $inv->getFilePrinted2(true);
						if ($file != null && $file2 ==null){
							array_push($files, $file);
						}
					}
					if (!empty($files)){
						include_once(dirname(Yii::app()->request->scriptFile)."/protected/components/pdfConcat.php");
						$pdf = new concat_pdf();
						$pdf->setFiles($files);
						$pdf->concat();
						$pdf->Output("invoices.pdf", "D");
					}
				}
		}
	}
	public function actioncheckSend(){
		if (isset($_POST['checkinvoice'])){ 
			$inv = Invoices::getNumberInvoicesPrinted($_POST['checkinvoice']);
		}else{ 
			$inv = Invoices::getNumberInvoicesPrinted();
		}
		echo json_encode(array(
			'inv' => $inv,
			'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
		));		
	}
	public function actionCheckEmail()	{
		if (isset($_POST['checkinvoice'])){ 
			$inv = Invoices::getNumberInvoicesEmail($_POST['checkinvoice']);
		}else{ 
			$inv = Invoices::getNumberInvoicesEmail();
		}
		echo json_encode(array(
			'inv' => $inv,
			'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
		));		
	}
	public function actioncheckTransfer(){
		if (isset($_POST['checkinvoice'])){ 
			$inv = Invoices::getNumberTransferToPrint($_POST['checkinvoice']);
		}else{ 
			$inv = Invoices::getNumberTransferToPrint();
		}
		echo json_encode(array(
			'inv' => $inv,
			'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
		));		
	}
	public function actionCheckPrint(){
		if (isset($_POST['checkinvoice']))	{ 
			$inv = Invoices::getNumberInvoicesToPrint($_POST['checkinvoice']);
		}else{ 
			$inv = Invoices::getNumberInvoicesToPrint();
		}
		echo json_encode(array(
			'inv' => $inv,
			'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
		));		
	}
	public static function sendNotificationsEmails($models, $status, $case = 1){
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('invoices_'.$status);
		if ($notif != NULL) {
			$to_replace = array(
				'{group_description}',
				'{invoices}', 
				'{invoicesPartner}'
			);    		
    		switch($case)
    		{
    			case 2:
    				$invoices = '';
    				$i = 1;
    				foreach($models as $model)
    				{
    					$invoices .= $i++.'- Invoice #'.(!empty($model->final_invoice_number) ? $model->final_invoice_number : $model->invoice_number).' - '.$model->customer->name;
    					$invoices .= ' - '.Utils::formatNumber($model->gross_amount).' '.$model->iCurrency->codelkup;
    					$invoices .= '<br>';
    				}
    				$subject = $notif['name'];
    				break;
    			default:
					$invoices = '<ul>';
					foreach($models as $model)
					{	
						$invoices .= '<li > Invoice #'.(!empty($model->final_invoice_number) ? $model->final_invoice_number : $model->invoice_number).' - '.$model->customer->name.'</li>';
						$part[$model->partner] = Yii::app()->db->createCommand("SELECT COUNT(*) FROM invoices where partner = '$model->partner' and id_customer = '$model->id_customer' AND project_name = '$model->project_name' AND id ='$model->id '")->queryScalar();
						$inv_number = $model['invoice_number'];
					}
					$subject = str_replace('{invoice_number}', $inv_number, $notif['name']);
					$invoices .= '</ul>';
    		}
			$invoicesPartner = '<ul>';
			foreach ($part as $key => $partner) {
				if ($partner == 1)	{
					$invoicesPartner .='<li>'. $partner.' Invoice for partner '.Codelkups::getCodelkup($key).'</li>';
				}	else{ 
					$invoicesPartner .='<li>'. $partner.' Invoices for partner '.Codelkups::getCodelkup($key).'</li>';
				}
			}
			$invoicesPartner .= '</ul>';			
			$replace = array(
				EmailNotificationsGroups::getGroupDescription($notif['id']),
				$invoices,
				$invoicesPartner				
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
    		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email) {
				Yii::app()->mailer->AddAddress($email);
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
	public function actionsendToCustomer(){  
		$part = array();	$status = 'printed';	$ids_invoices = $_POST['val'];
		
		$inv='';	$notif = EmailNotifications::getNotificationByUniqueName('invoices_'.$status);
		if ($notif != NULL) {	
			$arr= array(); $arrpart= array();
			$i=0;
			foreach($ids_invoices as $model)
			{
				$invoice_model = Invoices::model()->findByPk((int)$model);
				$inv_number = $invoice_model->final_invoice_number;
				
				if( $invoice_model->sentemail!= '1' && ((!in_array($inv_number , $arr) && $invoice_model->partner != '79' && $invoice_model->partner != Maintenance::PARTNER_AUST) || (!in_array($invoice_model->partner_inv , $arrpart) && $invoice_model->partner == Maintenance::PARTNER_AUST) ))
				{
					if($status  == 'printed'){
						if($invoice_model->partner == Maintenance::PARTNER_SNSI){
							$sns = 'SNSI';
							$inv_number = $invoice_model->partner_inv;
						}else if($invoice_model->partner== Maintenance::PARTNER_SNSAPJ ){ 
							$sns = 'SNS APJ';
							$inv_number = $invoice_model->snsapj_partner_inv;
						}else if($invoice_model->partner== Maintenance::PARTNER_APJ ){ 
							$sns = 'APJ';
							$inv_number = $invoice_model->snsapj_partner_inv;
						}else if($invoice_model->partner== Maintenance::PARTNER_AUST ){ 
							$sns = 'SNS AUST';
							$inv_number = $invoice_model->partner_inv;
						}
						else{ 
							$sns = 'SNS';
							$inv_number = $invoice_model->final_invoice_number;
						}    			
						$to_replace = array(
							'{sns}',
							'{invoice_number}',
						);
						$replace = array(
							$sns,
							$inv_number,
						);
						$subject = str_replace($to_replace, $replace, $notif['name']);
					}else
					{	
						$subject = str_replace('{invoice_number}', isset($invoice_model->final_invoice_number)?$invoice_model->final_invoice_number:$invoice_model->invoice_number, $notif['name']);
					}
					$to_replace = array(
						'{bill_to_contact}',
						'{invoice_number}',
						'{project_name}', 
						'{ea_id}',
						'{date}'
					);
					if($invoice_model->project_name != null)
					{	$proj_name = $invoice_model->project_name; }
					else  if ( $invoice_model->idEa !=null)
					{	$proj_name = $invoice_model->idEa->description; }
					else
					{	$proj_name = '';  }

						if ($invoice_model->idEa!=null){
							if ( $invoice_model->idEa->billto_contact_person!=null){
								$bill_to_contact_person=$invoice_model->idEa->billto_contact_person;
							}else{
								$bill_to_contact_person=$invoice_model->customer->bill_to_contact_person;
							}
						} else{
							$bill_to_contact_person=$invoice_model->customer->bill_to_contact_person;
						}
					$arr = explode(' ',trim($bill_to_contact_person));
					if ($invoice_model->id_ea != null && $invoice_model->id_ea!='' && $invoice_model->id_ea!=' '){
						$strea='and EA#'.$invoice_model->id_ea.'.';
					}else{
						$strea='';
					}
					$replace = array(
						isset($arr[0])?$arr[0]:"",
						$inv_number,
						$proj_name,
						$strea,
						date("d/m/Y", strtotime("+1 month"))				
					);
					$body = str_replace($to_replace, $replace, $notif['message']);
					//$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					/*foreach($emails as $email) {
						if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
						{	//Yii::app()->mailer->AddCcs($email);
							Yii::app()->mailer->AddAddress($email);
						}
					}*/
					$emailrep= Users::getEmailbyID($invoice_model->id_assigned);
					if(!empty($emailrep))
					{
						 Yii::app()->mailer->AddAddress($emailrep);
					}

					Yii::app()->mailer->From="nadine.abboud@sns-emea.com";
					$pieces = explode(";", trim($invoice_model->customer->bill_to_contact_email));
					foreach($pieces as $email) {
						if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
						{	//Yii::app()->mailer->AddCcs($email);
							 Yii::app()->mailer->AddAddress($email);
						}
					}
				/*	if(filter_var((trim($invoice_model->customer->bill_to_contact_email)), FILTER_VALIDATE_EMAIL)) 
						Yii::app()->mailer->AddAddress($invoice_model->customer->bill_to_contact_email);  */
					//if(filter_var((trim($invoice_model->customer->primary_contact_email)), FILTER_VALIDATE_EMAIL)) 
						//Yii::app()->mailer->AddCcs($invoice_model->customer->primary_contact_email);
					//Yii::app()->mailer->AddCcs('micheline.daaboul@sns-emea.com');
					Yii::app()->mailer->AddAddress('micheline.daaboul@sns-emea.com');
					//Yii::app()->mailer->AddAddress('houda.nasser@sns-emea.com');
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->ClearFiles();
					if($invoice_model->partner == Maintenance::PARTNER_SNSI){
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsi/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf'))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsi/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf');
					}else if($invoice_model->partner == Maintenance::PARTNER_AUST){
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsaust/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf'))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsaust/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf');
					}else if($invoice_model->partner == Maintenance::PARTNER_SNSAPJ){ 
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsapj/INVOICE_'.str_replace('/','_',$invoice_model->snsapj_partner_inv).'.pdf'))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsapj/INVOICE_'.str_replace('/','_',$invoice_model->snsapj_partner_inv).'.pdf');
					}else if($invoice_model->partner == Maintenance::PARTNER_APJ){ 
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/apj/INVOICE_'.str_replace('/','_',$invoice_model->snsapj_partner_inv).'.pdf'))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/apj/INVOICE_'.str_replace('/','_',$invoice_model->apj_partner_inv).'.pdf');
					}else{
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/INVOICE_'.str_replace('/','_',$invoice_model->final_invoice_number).'.pdf'))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/INVOICE_'.str_replace('/','_',$invoice_model->final_invoice_number).'.pdf');
					}
					if($invoice_model['id_ea'] != null)	{
						$item = Eas::model()->findByPk((int)$invoice_model['id_ea']);
						$file_ea = $item->getFile(true,true);
						if($file_ea == null)
							$file_ea = $item->getFile(true);
						if($file_ea != null)
							Yii::app()->mailer->AddFile($file_ea);
					}
					if($ids_invoices != null)	{
						$all = '('. implode(',',$_POST['val']).')';
						if($invoice_model->partner == Maintenance::PARTNER_AUST )
						{
							$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$invoice_model->id_customer}' AND project_name = '{$invoice_model->project_name}' AND partner = '{$invoice_model->partner}' AND partner_inv= '".$invoice_model->partner_inv."' AND id IN $all ")->queryAll();
						}else{
							$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$invoice_model->id_customer}' AND project_name = '{$invoice_model->project_name}' AND partner = '{$invoice_model->partner}' AND final_invoice_number= '".$invoice_model->final_invoice_number."' AND id IN $all ")->queryAll();
						
						}
					}else{
						$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$invoice_model->id_customer}' AND project_name = '{$invoice_model->project_name}' AND partner = '{$invoice_model->partner}' AND id = $invoice_model->id ")->queryAll();
					}
					foreach ($expenses_ids as $expens){
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses/Expenses'.Utils::paddingCode($expens['id_expenses']).'.pdf')){
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses/Expenses'.Utils::paddingCode($expens['id_expenses']).'.pdf');
							}					
							if(isset($expens['id_expenses']) && $expens['id_expenses'] !='' && $expens['id_expenses']!=' '){
							$receipts = Yii::app()->db->createCommand("SELECT expenses_id,file FROM expenses_uploads WHERE expenses_id={$expens['id_expenses']}")->queryAll();
							foreach ($receipts as $receipt)
							{
								if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses'.'/'.$receipt['expenses_id'].'/'.$receipt['file']))
									Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses'.'/'.$receipt['expenses_id'].'/'.$receipt['file']);
						
							}
							}
					}
					if($ids_invoices != null){
						$all = '('. implode(',',$_POST['val']).')';
						if($invoice_model->partner == Maintenance::PARTNER_AUST )
						{
							$travel_expenses_ids = Yii::app()->db->createCommand("SELECT id , file FROM travel WHERE inv_number in (select id from invoices where partner_inv= '".$invoice_model->partner_inv."' AND partner = '{$invoice_model->partner}'  ) and inv_number IN $all ")->queryAll();		
						}else{
							$travel_expenses_ids = Yii::app()->db->createCommand("SELECT id , file FROM travel WHERE inv_number in (select id from invoices where final_invoice_number= '".$invoice_model->final_invoice_number."' AND partner = '{$invoice_model->partner}'  ) and inv_number IN $all ")->queryAll();		
						}
					}else{
						$travel_expenses_ids = Yii::app()->db->createCommand("SELECT id , file FROM travel WHERE inv_number = $invoice_model->id ")->queryAll();
					}
					foreach ($travel_expenses_ids as $texpens){
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/travel'.'/'.$texpens['id'].'/'.$texpens['file']))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/travel'.'/'.$texpens['id'].'/'.$texpens['file']);
					}
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true))	{	
						$arr[$i]= $invoice_model->final_invoice_number;
						if($invoice_model->partner == Maintenance::PARTNER_AUST )
						{
							$arrpart[$i] = $invoice_model->partner_inv;
						}
						if(!empty($invoice_model->final_invoice_number))
						{
							$nr = Yii::app()->db->createCommand("UPDATE invoices SET sentemail = 1 WHERE final_invoice_number ='".$invoice_model->final_invoice_number."' ")->execute();
						}else{
							$nr = Yii::app()->db->createCommand("UPDATE invoices SET sentemail = 1 WHERE partner_inv ='".$invoice_model->partner_inv."' and partner= ".$invoice_model->partner." ")->execute();
						}
						$i++;
						Yii::app()->mailer->From="no-reply@sns-emea.com";
						
					}
				}
			}
			echo json_encode(array(
							'inv' => 'sent',
							'nb' => $i,
						));
    	}
		
	}
	public static function sendNotificationsEmailsPrinted($invoice_model,$status,$ids_invoices = null){ 
		$part = array();
		$notif = EmailNotifications::getNotificationByUniqueName('invoices_'.$status);
		if ($notif != NULL) 	{	
    		$inv_number = $invoice_model->final_invoice_number;
    		if($status  == 'printed'){
    			if($invoice_model->partner == Maintenance::PARTNER_SNSI){
    				$sns = 'SNSI';
    				$inv_number = $invoice_model->partner_inv;
    			}else if($invoice_model->partner == Maintenance::PARTNER_AUST){
    				$sns = 'SNS AUST';
    				$inv_number = $invoice_model->partner_inv;
    			}else if($invoice_model->partner== Maintenance::PARTNER_SNSAPJ ){ 
    				$sns = 'SNS APJ';
    				$inv_number = $invoice_model->snsapj_partner_inv;
    			}else if($invoice_model->partner== Maintenance::PARTNER_APJ ){ 
    				$sns = 'APJ';
    				$inv_number = $invoice_model->snsapj_partner_inv;
    			}else{ 
    				$sns = 'SNS';
    				$inv_number = $invoice_model->final_invoice_number;
    			}    			
    			$to_replace = array(
					'{sns}',
					'{invoice_number}',
    			);
    			$replace = array(
    				$sns,
    				$inv_number,
    			);
    			$subject = str_replace($to_replace, $replace, $notif['name']);
    		}else
    			$subject = str_replace('{invoice_number}', isset($invoice_model->final_invoice_number)?$invoice_model->final_invoice_number:$invoice_model->invoice_number, $notif['name']);
			$to_replace = array(
				'{bill_to_contact}',
				'{invoice_number}',
				'{project_name}', 
				'{ea_id}',
				'{date}'
			);
			if($invoice_model->project_name != null)
				$proj_name = $invoice_model->project_name; 
			else 
				$proj_name = $invoice_model->idEa->description;
			$bill_to_contact_person = $invoice_model->idEa->billto_contact_person!=null?$invoice_model->idEa->billto_contact_person:$invoice_model->customer->bill_to_contact_person;
			$arr = explode(' ',trim($bill_to_contact_person));			
			$replace = array(
				isset($arr[0])?$arr[0]:"",
				$inv_number,
				$proj_name,
				$invoice_model->id_ea,
				date("d/m/Y", strtotime("+1 month"))
				
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
    		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			Yii::app()->mailer->ClearAddresses();
			foreach($emails as $email) {
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
				{	//Yii::app()->mailer->AddCcs($email);
					Yii::app()->mailer->AddAddress($email);
				}
			}
			Yii::app()->mailer->From="nadine.abboud@sns-emea.com";
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->ClearFiles();
			if($invoice_model->partner == Maintenance::PARTNER_SNSI)
			{
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsi/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf'))
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsi/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf');
			}else if($invoice_model->partner == Maintenance::PARTNER_AUST)
			{
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsaust/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf'))
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsaust/INVOICE_'.str_replace('/','_',$invoice_model->partner_inv).'.pdf');
			}else if($invoice_model->partner == Maintenance::PARTNER_SNSAPJ)
			{ 
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsapj/INVOICE_'.str_replace('/','_',$invoice_model->snsapj_partner_inv).'.pdf'))
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/snsapj/INVOICE_'.str_replace('/','_',$invoice_model->snsapj_partner_inv).'.pdf');
			}else if($invoice_model->partner == Maintenance::PARTNER_APJ)
			{ 
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/apj/INVOICE_'.str_replace('/','_',$invoice_model->snsapj_partner_inv).'.pdf'))
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/apj/INVOICE_'.str_replace('/','_',$invoice_model->apj_partner_inv).'.pdf');
			}else{
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/INVOICE_'.str_replace('/','_',$invoice_model->final_invoice_number).'.pdf'))
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/invoices/INVOICE_'.str_replace('/','_',$invoice_model->final_invoice_number).'.pdf');
			}
			if($invoice_model['id_ea'] != null){
				$item = Eas::model()->findByPk((int)$invoice_model['id_ea']);
				$file_ea = $item->getFile(true,true);
				if($file_ea == null)
					$file_ea = $item->getFile(true);
				if($file_ea != null)
					Yii::app()->mailer->AddFile($file_ea);
			}
			if($ids_invoices != null){
				$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$invoice_model->id_customer}' AND project_name = '{$invoice_model->project_name}' AND partner = '{$invoice_model->partner}' AND id IN $ids_invoices ")->queryAll();
				
			}else{
				$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$invoice_model->id_customer}' AND project_name = '{$invoice_model->project_name}' AND partner = '{$invoice_model->partner}' AND id = $invoice_model->id ")->queryAll();
				
			}
			foreach ($expenses_ids as $expens)	{
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses/Expenses'.Utils::paddingCode($expens['id_expenses']).'.pdf')){
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses/Expenses'.Utils::paddingCode($expens['id_expenses']).'.pdf');
					}					
					if(isset($expens['id_expenses']) && $expens['id_expenses'] !='' && $expens['id_expenses']!=' '){
					$receipts = Yii::app()->db->createCommand("SELECT expenses_id,file FROM expenses_uploads WHERE expenses_id={$expens['id_expenses']}")->queryAll();
					foreach ($receipts as $receipt)	{
						if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses'.'/'.$receipt['expenses_id'].'/'.$receipt['file']))
							Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/expenses'.'/'.$receipt['expenses_id'].'/'.$receipt['file']);
					}
				}
			}
			if($ids_invoices != null)
			{
				$travel_expenses_ids = Yii::app()->db->createCommand("SELECT id , file FROM travel WHERE inv_number IN $ids_invoices ")->queryAll();
			}else{
				$travel_expenses_ids = Yii::app()->db->createCommand("SELECT id , file FROM travel WHERE inv_number = $invoice_model->id ")->queryAll();
			}
			foreach ($travel_expenses_ids as $texpens){
				if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/travel'.'/'.$texpens['id'].'/'.$texpens['file']))
					Yii::app()->mailer->AddFile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$invoice_model->id_customer.'/travel'.'/'.$texpens['id'].'/'.$texpens['file']);
			}
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){	
				Yii::app()->mailer->From="no-reply@sns-emea.com";
				return true;
			}
    	}
    	return false;
	}	
	public function actionCheckStatus(){
		$ok = true;
		$status = '( "To Print" , "Cancelled" )';
		if (isset($_POST['checkinvoice'])) 	{
			$ids_invoices = implode(',',$_POST['checkinvoice']);
			$res = Yii::app()->db->createCommand("SELECT count(id) FROM invoices WHERE id IN ($ids_invoices) AND NOT (status = 'New' || status = 'Cancelled') ")->queryScalar();
			if ($res >=1){
			echo json_encode(array_merge(array(
					'status'=>'fail',
				)));
				exit;
			}
		}else{
			echo json_encode(array_merge(array(
					'status'=>'fail',
			)));
			exit;
		}		
		echo json_encode(array_merge(array(
					'status'=>'success',
		)));
		exit;		 
	}	
	public function actionShareAll(){
		$model = new ShareByForm;
		$file = array();
		if (isset($_POST['checkinvoice']))	{
			$ids_invoices = $_POST['checkinvoice'];
			array_push($file, 'file');
			foreach ($ids_invoices as $id){
				$model_inv = Invoices::model()->findByPk($id);
				$item[] = $model_inv;
				if ($model_inv->final_invoice_number == null && !($model_inv->partner == '1218' && $model_inv->partner_inv!=null ))
					$this->generatePdf('invoicesShare', $id);
			}
			if ($item)	{
				if (isset($_POST['ShareByForm']))	{
					foreach ($item as $it)
						if($it->final_invoice_number != null || ($it->partner == '1218' && $it->partner_inv!=null) )
							array_push($file, $it->getFilePrinted2(true));
						else
							array_push($file, $it->getFileShare(true));
					$model->attributes = $_POST['ShareByForm'];
					if ($model->validate()  && $file != NULL){
						$emails_to = explode(',', $model->to);
						$emails_invalid = array();
						$validator = new CEmailValidator;
						foreach ($emails_to as $em){
							$email = '';
							if (trim($em)){
								$arr = explode('<', $em);
								if (count($arr) == 2) {
									$to = array();
									$to[substr($arr[1], 0, -1)] = $arr[0];
									if ($validator->validateValue(substr($arr[1], 0, -1)))	{
										$email = $to;
									}
								} else {
									if ($validator->validateValue($em))	{
										$email = $em;
									}
								}
								if (!empty($email))	{
									Yii::app()->mailer->AddAddress($email);
								}else{
									$emails_invalid[] = $em;
								}
							}
						}	
						Yii::app()->mailer->Subject  = $model->subject;
						for($k = 1 ; $k < count($file) ; $k++){
							if($file[$k] != null)
								Yii::app()->mailer->AddFile($file[$k]);
						}
						Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($model->body)."</div>");
						if(Yii::app()->mailer->Send(true))
							$send = 1;
						else 
							$send = 0;
						echo json_encode(array(
								"status"=>"success",
								'sent'=> $send,
								'not_sent_to' => implode(',',$emails_invalid),
						));
						exit;
					}
				}	
				$form = $this->renderPartial('shareby', array('model'=> $model, 'item'=>$item), true, true);
				echo json_encode(array(
						"status"=>"failure",
						"form" => $form,
						"file_found" => $file == NULL ? 0 : 1,
				));
				exit;
			}
		}
	}
	public function loadModel($id){
		$model = Invoices::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}?>