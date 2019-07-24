<?php
class Invoices extends CActiveRecord{
	const STATUS_NEW = "New";	const STATUS_APPROVED = "Approved";	const STATUS_PRINTED = "Printed";	const STATUS_CANCELLED = "Cancelled";
	const STATUS_TO_PRINT = "To Print";	const STATUS_PAID = "Paid";	
	public $customer_name, $eanb, $textdays, $invoice_description;	public $customErrors = array();	 
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'invoices';
	}
	public function rules(){
		return array(
            array('id_customer, payment, payment_procente, currency,sns_share,amount,type', 'required'),
            array('id_customer, id_ea,  payment_procente, currency, partner, sold_by, id_assigned', 'numerical', 'integerOnly'=>true),
            array('net_amount, gross_amount, partner_amount, amount', 'numerical'),
            array('invoice_number, eanb,', 'length', 'max'=>5),
            array('remarks, notes, type, po', 'length', 'max'=>255),
            array('project_name,invoice_title', 'length', 'max'=>1000),
            array('status,final_invoice_number, transfer_number', 'length', 'max'=>10),
            array('old_sns_inv,snsapj_partner_inv,span_partner_inv,partner_inv', 'length', 'max'=>25),
            array('old', 'length', 'max'=>3),
            array('payment_procente', 'numerical', 'max'=>100, 'min'=>0),
            array('invoice_date_month', 'numerical', 'max'=>12, 'min'=>1),
            array('invoice_date_year', 'length', 'max'=>4, 'min'=>1),
            array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers'),
            array('po','validatePO'),
            array('partner_status', 'length', 'max'=>8),
            array('id, id_assigned, paid_date,id_project, remarks, notes,invoice_number,ADDDATEINV, final_invoice_number,transfer_number , id_customer, invoice_number, invoice_title, project_name, id_ea, payment, payment_procente, status, currency, partner, sns_share, invoice_date_month, sold_by, old, printed_date, partner_status, partner_inv, net_amount, gross_amount, partner_amount, file,invoice_date_year', 'safe', 'on'=>'search'),
        );
	}
	public function relations(){
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'project' => array(self::BELONGS_TO, 'Projects', 'id_project'),
			'iCurrency' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'iUnit' => array(self::BELONGS_TO, 'Codelkups', 'sold_by'),
			'iAuthor' => array(self::BELONGS_TO, 'Codelkups', 'partner'),
			'maintenanceInvoices' => array(self::HAS_MANY, 'MaintenanceInvoices', 'id_invoice'),
            'idEa' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
			'idExpense' => array(self::BELONGS_TO, 'Expenses', 'id_expenses'),
			'eItems' => array(self::HAS_MANY, 'InvoicesExpenses', 'id_invoice'),
			'uUser' => array(self::BELONGS_TO, 'Users', 'id_resource'),			
		);
	}
	public function validatePO($attribute, $params){		
		$mandatory= Customers::getLpo((int)$this->id_customer);
		if( $mandatory == 'Yes' && empty($this->po) && $this->type == 'Maintenance')
		{
			$this->addError('po','PO cannot be blank.');
		}	       
	}	
	public function attributeLabels(){
		return array(
            'id' => 'ID',
            'id_customer' => 'Customer',
            'invoice_number' => 'Invoice #',
            'invoice_title' => 'Invoice Title',
            'project_name' => 'Project Name',
            'id_ea' => 'Id Ea',
            'payment' => 'Payment #',
            'payment_procente' => 'Payment % ',
            'status' => 'Status',
            'currency' => 'Currency',
            'partner' => 'Partner',
            'sns_share' => 'Sns Share',
            'invoice_date_month' => 'Invoice Date',
            'sold_by' => 'Sold By',
            'old' => 'Old',
            'printed_date' => 'Printed Date',
            'partner_status' => 'Partner Status',
            'partner_inv' => 'Partner Inv',
            'net_amount' => 'Net Amount',
            'gross_amount' => 'Gross Amount',
            'partner_amount' => 'Partner Amount',
            'file' => 'File',
			'net_amount' => 'Net Amount',
			'final_invoice_number' => 'Invoice #',
			'transfer_number' => 'Transfer #',
			'remarks' => 'Remarks',
			'notes' => 'Notes',
			'paid_date' => 'Paid date',
			'id_assigned' => 'User assigned',
			'type' => 'Invoice Type',
			'po' => 'PO',
			'ADDDATEINV'=>'Creation Date'
        );
	}
	public function search(){
		$criteria=new CDbCriteria;
		 
       	$criteria->with = array('customer','idEa','project');
		$criteria->compare('id', $this->id);
		$criteria->compare('final_invoice_number', $this->final_invoice_number, true);
        $criteria->compare('customer.name', $this->id_customer, true);       
      if(isset($this->id_ea) && $this->id_ea!='' && $this->id_ea!=' '){      		
      	 	$eassubmitted=" ";		$eassubmitted=str_replace(","," ",$this->id_ea); $eassubmitted=str_replace(" ",",",$eassubmitted);		$eanos = array();	$easnos=explode(",", $eassubmitted);	$eanos="";       		
       		if (count($easnos)==1){
       		$criteria->compare('idEa.id', ltrim($this->id_ea,"0"), true);
       		}else{
      	 	foreach ($easnos as $ea) { $eanos.=" '".rtrim(ltrim(ltrim($ea,"0")," ")," ")."' ,";}
       			 $criteria->addCondition("idEa.id IN (".$eanos." 0) ");
    		}
       	} 
       	if(isset($this->payment) && $this->payment == 1)
       	{
       		 $criteria->addCondition("t.id = (select MIN(i.id) from invoices i where i.id_ea = t.id_ea and (i.status ='New' or i.status= 'To Print')) ");

       	}
        $criteria->compare('invoice_date_year',$this->invoice_date_year);
        $criteria->compare('invoice_date_month',$this->invoice_date_month);
        $criteria->compare('project_name', $this->project_name, true);
		if (isset($this->type) && $this->type!='' &&  $this->type!=' ' && !empty($this->type)){         	
			$types=$this->type;	$inv_type="";
        	foreach ($types as $value) {
        	$inv_type.="'".rtrim(ltrim($value," ")," ")."',";
        	}       	
        	$criteria->addCondition("t.type in (".$inv_type." '-1' ) ");  
        }        
		if (isset($this->status) &&  $this->status!='' &&  $this->status!=' '){
        	$criteria->compare('t.status',$this->status,true);
        }
        if (isset($this->partner) &&  $this->partner!='' &&  $this->partner!=' '){
        	$criteria->compare('t.partner',$this->partner,true);
        }else{
        	$criteria->addCondition(" t.partner !='554' ");
        }  
        return new CActiveDataProvider('Invoices', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' =>50,
            ),
            'sort'=>array(
    			'defaultOrder'=>'customer.name, t.id DESC',            
		         'attributes'=>array(
		            'customer'=>array(
		                'asc'=>'customer.name',
		            ),
		            '*',
		        ),
		    ),
		));
	}

	public function getinvdate(){
		if($this->status !='New' && $this->status !='Cancelled' && $this->status !='To Print' && isset($this->invoice_date_year)  && isset($this->invoice_date_month) ){
			return date("d/m/Y", strtotime(Yii::app()->db->createCommand('select  LAST_DAY(CONCAT('.$this->invoice_date_year.',"-",'.$this->invoice_date_month.',"-01"))')->queryScalar()));
		}else{
			return '';
		}
	}

	public function getAll($group = NULL, $export = false){	
		$criteria = new CDbCriteria;	
		$criteria->with = array('customer');
		$criteria->select = array(
				"t.*"
		);		
		$dataProvider = new CActiveDataProvider('Invoices', array(
				'criteria' => $criteria,
				'pagination'=>($group != null || $export) ? false : array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => $group ? $group : ($export ? 'customer.name ASC' : 't.final_invoice_number ASC'),
           		 ),
		));
		return $dataProvider;
	}
	public static function getDirPathMoreTransfer(){
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/transfers/";
	 	if( !is_dir( $path ) ) { mkdir( $path, 0777, true); chmod( $path, 0777 ); }
		return $path; 
	}
	public static function getDirPathTransfer($partner){
		if ($partner=='SNSAPJ'){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."transfers".DIRECTORY_SEPARATOR."SNSAPJ".DIRECTORY_SEPARATOR;
		}else {
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."transfers".DIRECTORY_SEPARATOR."SNSI".DIRECTORY_SEPARATOR;
		}
		if ( !is_dir( $path )) { mkdir( $path, 0777, true);  chmod( $path, 0777 ); }
		return $path; 
	}
	public static function getDirPath($customer_id, $model_id,$snsi = false,$snsapj = false,$apj = false, $snsaust= false){
		$customer_id = (int)$customer_id;
		$model_id = (int)$model_id;
		if ($snsi == true){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR."snsi".DIRECTORY_SEPARATOR;
		}else if ($snsaust == true){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR."snsaust".DIRECTORY_SEPARATOR;
		}else if ($snsapj == true){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR."snsapj".DIRECTORY_SEPARATOR;
		}else if ($apj == true){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR."apj".DIRECTORY_SEPARATOR;
		}else{
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR;
		}
		if ( !is_dir( $path )) { mkdir( $path, 0777, true); chmod( $path, 0777 );  }
		return $path; 
	}
	public static function getType($type){
			if ($type == 'TandM'){
				return "T&M";
			}else if($type == 'Expenses'){
				return "Expense Sheet";
			}
			else{
				return $type;
			}
	}

	public static function getTransfersPerInvoice($id)
	{
		$str='';
		$trs=  Yii::app()->db->createCommand("select distinct(id_it) as tr from incoming_transfers_details where invoice_number in (".$id.")")->queryAll();
		foreach ($trs as $tr) {
			$str.=' <a class="show_link" href="'.Yii::app()->createUrl("incomingTransfers/update", array("id" => $tr['tr'])).'"> '.Utils::paddingCode($tr['tr']).'</a>,';
		}
		$str= substr($str, 0, -1);
		return $str;
	}

	public static function getDirPathShare($customer_id, $model_id){
		$customer_id = (int)$customer_id;
		$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer_id}/invoices/share/";
	 	if (!is_dir( $path ) ) { mkdir( $path, 0777, true); chmod( $path, 0777 ); }
		return $path; 
	}
	public function getFilePrinted2($path = false){ 
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		if (Codelkups::getCodelkup($this->partner) == 'SPAN' && $this->old == "Yes"){
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SNSI'){
			$filePath .= "snsi".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsi/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SNSAPJ'){
			$filePath .= "snsapj".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsapj/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SPAN'){
			$filePath .= "span".DIRECTORY_SEPARATOR.'SCS_'.str_replace('/','_',$this->span_partner_inv).'.pdf';
			$fileName .= "span/".'SCS_'.str_replace('/','_',$this->span_partner_inv).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SNS AUST'){
			$filePath .= "snsaust".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsaust/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
		}else{
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public function getSNSPrinted($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		if(Codelkups::getCodelkup($this->partner) == 'SNS AUST' && empty($this->final_invoice_number)){
			$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
			$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		
			$filePath .= "snsaust".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsaust/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
			if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
			}else
			{
				return null;
			}
		}else if(Codelkups::getCodelkup($this->partner) != 'SNS' && $this->old=='Yes'){
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) != 'SNS'){
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public static function getTitleAUSTInv($month, $year, $currency){
		$titles= Yii::app()->db->createCommand("SELECT CONCAT(c.name,'(SNSAUST-',i.partner_inv,')') as title FROM invoices i, customers c where i.id_customer=c.id and i.partner=1218 and i.status in ('Printed','Paid') 
		and i.invoice_date_month= ".$month." and i.currency=".$currency." and  i.invoice_date_year=  ".$year." GROUP BY partner_inv order by c.name")->queryAll();
		$arr = array_map(function($titles){ return $titles['title']; }, $titles);
		$title = implode(' - ', $arr);
		return $title;
	}
	public static function getTitleAPJInv($month, $year, $currency){
		$titles= Yii::app()->db->createCommand("SELECT CONCAT(c.name,'(SNSI-',i.partner_inv,')') as title FROM invoices i, customers c where i.id_customer=c.id and i.partner=78 and i.status in ('Printed','Paid') 
		and i.invoice_date_month= ".$month." and i.currency=".$currency." and  i.invoice_date_year=  ".$year." GROUP BY partner_inv order by c.name")->queryAll();
		$arr = array_map(function($titles){ return $titles['title']; }, $titles);
		$title = implode(' - ', $arr);
		return $title;
	}
	public static function getInvoicesData(){
		return Yii::app()->db->createCommand("SELECT sum(net_amount) as amount, currency, MONTH((CURRENT_DATE - INTERVAL 1 MONTH)) as month, YEAR((CURRENT_DATE - INTERVAL 1 MONTH)) as year FROM `invoices` where partner=78 and status in ('Printed','Paid') 
		and invoice_date_month=  MONTH((CURRENT_DATE - INTERVAL 1 MONTH)) and  invoice_date_year=  YEAR((CURRENT_DATE - INTERVAL 1 MONTH))
		GROUP BY currency")->queryAll();
	}
	public static function getInvoicesDataAUST(){
		return Yii::app()->db->createCommand("SELECT sum(net_amount) as amount, currency, MONTH((CURRENT_DATE - INTERVAL 1 MONTH)) as month, YEAR((CURRENT_DATE - INTERVAL 1 MONTH)) as year FROM `invoices` where partner=1218 and status in ('Printed','Paid') 
		and invoice_date_month=  MONTH((CURRENT_DATE - INTERVAL 1 MONTH)) and  invoice_date_year=  YEAR((CURRENT_DATE - INTERVAL 1 MONTH))
		GROUP BY currency")->queryAll();
	}
		public function getFilePrinted($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		if (Codelkups::getCodelkup($this->partner) == 'SPAN' && $this->old == "Yes"){
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SNSI'){
			$filePath .= "snsi".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsi/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SNSAUST'){
			$filePath .= "snsaust".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsaust/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
		}else if(Codelkups::getCodelkup($this->partner) == 'SNSAPJ'){
			$filePath .= "snsapj".DIRECTORY_SEPARATOR.'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
			$fileName .= "snsapj/".'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
		}else{
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public static function getFinalInvNumberById($id){		
			return	$value = Yii::app()->db->createCommand("SELECT final_invoice_number FROM invoices WHERE id =$id ")->queryScalar();				
	}
	public static function getInvNumberById($id){		
			return	$value = Yii::app()->db->createCommand("SELECT invoice_number FROM invoices WHERE id =$id ")->queryScalar();				
	}
	public function getFile($path = false, $uploaded = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {	$filePath .= $this->file;	$fileName .= $this->file;}else{	return null;}
		}else {
			if (Codelkups::getCodelkup($this->partner) == 'SPAN' && $this->old == "Yes"){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->old_sns_inv).'.pdf';
				$fileName .= 'INVOICE_'.str_replace('/','_',$this->old_sns_inv).'.pdf';	
			}else if(Codelkups::getCodelkup($this->partner) == 'SNSI' || Codelkups::getCodelkup($this->partner) == 'SNS AUST'){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';
				$fileName .= 'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
			}else if(Codelkups::getCodelkup($this->partner) == 'SNS APJ'){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->snsapj_partner_inv).'.pdf';
				$fileName .= 'INVOICE_'.str_replace('/','_',$this->snsapj_partner_inv).'.pdf';	
			}else if(Codelkups::getCodelkup($this->partner) == 'APJ'){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->snsapj_partner_inv).'.pdf';
				$fileName .= 'INVOICE_'.str_replace('/','_',$this->snsapj_partner_inv).'.pdf';	
			}else{
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';
				$fileName .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	
			}
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getFileMore($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile)."/uploads/invoices/INVOICE_".date('Y-m-d').'.pdf';
		$fileName = Yii::app()->getBaseUrl(true)."/uploads/invoices/INVOICE_".date('Y-m-d').'.pdf';		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getTransferMore($template,$transferid){
		$path = true;
		if ($template<5){
			$filePath = dirname(Yii::app()->request->scriptFile)."/uploads/transfers/SNSAPJ/TRANSFER_".$transferid.'.pdf';
			$fileName = Yii::app()->getBaseUrl(true)."/uploads/transfers/SNSAPJ/TRANSFER_".$transferid.'.pdf';
		}else{
			$filePath = dirname(Yii::app()->request->scriptFile)."/uploads/transfers/SNSI/TRANSFER_".$transferid.'.pdf';
			$fileName = Yii::app()->getBaseUrl(true)."/uploads/transfers/SNSI/TRANSFER_".$transferid.'.pdf';
		}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public static function getDirPathMoreInv(){
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/invoices/";
	 	if( !is_dir( $path ) ) { mkdir( $path, 0777, true);  chmod( $path, 0777 ); }
		return $path; 
	}	
	public function getFileShare($path = false, $uploaded = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices".  DIRECTORY_SEPARATOR."share". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR.'share'.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {
				$filePath .= $this->file;
				$fileName .= $this->file;
			}else{
				return null;
			}
		}else {
			$filePath .= 'INVOICE_'.$this->invoice_number.'.pdf';
			$fileName .= 'INVOICE_'.$this->invoice_number.'.pdf';	
		}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public function getFinalFilename(){
		$path = $this->getFile(true);
		if ($path != NULL){
			return pathinfo($path, PATHINFO_BASENAME);	
		}
		return NULL;
	}	
	public function getFilename(){
		if (isset($this->final_invoice_number))
			$path = $this->getFilePrinted(true);
		else
			$path = $this->getFileShare(true);
		if ($path != NULL){
			return pathinfo($path, PATHINFO_BASENAME);	
		}
		return NULL;
	}	
	public function renderInvoiceNumber(){
		if($this->final_invoice_number != null)
			$no = $this->final_invoice_number;
		else 
			$no = $this->id;
		echo '<a class="show_link" href="'.Yii::app()->createUrl("invoices/view", array("id" => $this->id)).'">'.$no.'</a>';
	}	
	public static function getpaymentList()
	{
		return array(
			1 => 'Yes', 
			2 =>'No');
	}
	public static function getStatusList($exclude = array()){		
		$all = array(
			Invoices::STATUS_NEW => Invoices::STATUS_NEW, 
			Invoices::STATUS_TO_PRINT => Invoices::STATUS_TO_PRINT,
			Invoices::STATUS_PRINTED => Invoices::STATUS_PRINTED ,
			Invoices::STATUS_CANCELLED => Invoices::STATUS_CANCELLED, 
			Invoices::STATUS_PAID => Invoices::STATUS_PAID);
			
		foreach ($exclude as $v){
			unset($all[$v]);
		}
		return $all; 
	}
public static function getStatusListReceivables($exclude = array()){		
		$all = array(
			Invoices::STATUS_NEW => Invoices::STATUS_NEW, 
			Invoices::STATUS_TO_PRINT => Invoices::STATUS_TO_PRINT,
			Invoices::STATUS_PRINTED => "Not Paid" ,
			Invoices::STATUS_CANCELLED => Invoices::STATUS_CANCELLED, 
			Invoices::STATUS_PAID => Invoices::STATUS_PAID);
			
		foreach ($exclude as $v){
			unset($all[$v]);
		}
		return $all; 
	}		
	public function getDescriptionGrid(){
		$arr = Utils::getShortText($this->invoice_title, 22);
		if ($this->invoice_title != null)
		return '<div class="first_it panel_container">'
				.'<div class="item_clip clip">'.$arr['text'].'</div>'
				.'<u class="red">+</u>'
				.'<div class="panel" style = "left:80px">'
					.'<div class="phead"></div>'
					.'<div class="pcontent"><div class="cover">'.$this->invoice_title.'</div></div>'
					.'<div class="pftr"></div>'
				.'</div>'
			.'</div>';
	}	
	public static function getYears(){
		$start_year = date('Y',strtotime('now - 10 year'));
		$end_year = date('Y',strtotime('now + 10 year'));
		for ($i=$start_year;$i<=$end_year;$i++){
	     	$years["{$i}"]="{$i}";
		}	
		return $years;                   
	}
	public static function getYearsGrid(){
		$start_year = date('Y',strtotime('now -5 year'));
		$end_year = date('Y',strtotime('now + 10 year'));
		for ($i=$start_year;$i<=$end_year;$i++){
	     	$years["{$i}"]="{$i}";
		}	
		return $years;                   
	}
	public static function gettemplates(){
		$templateNames = array('','Template 1 - SNSAPJ AUD','Template 2 - SNSAPJ USD', 'Template 3 - SNSAPJ US', 'Template 4 - SNSAPJ EU', 'Template 5 - SNSI USD', 'Template 6- SNSI EU');
		for ($i=1;$i<=6;$i++){	$templates["{$i}"]=Yii::t('default',$templateNames[$i]);  }
		return $templates;
	}
	public static function getMonths(){ 
		$monthNames = array('','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');		
		for ($i=1;$i<=12;$i++){	$months["{$i}"]=Yii::t('default',$monthNames[$i]);  }
		return $months;
	}	
	public static function getInvoiceDate($month,$year,$id){	
	    $months = self::getMonths();   $years = self::getYearsGrid();
	    if(GroupPermissions::checkPermissions('financial-invoices','write')){
		    return CHtml::dropDownlist('months', $month, $months, array('prompt'=>'',
		        'class'     => 'status ',
		    	'onchange'=>'changeInvoiceDate('."value".',"month",'.$id.');',
		    )).CHtml::dropDownlist('years', $year, $years, array('prompt'=>'',
		        'class'     => 'status marginl10',
		    	'onchange'=>'changeInvoiceDate('."value".',"year",'.$id.')',
		    ));
	    }else{
	    	if($month == null)
	    		$months	= array();
	    	if($year == null)
	    		$years = array(); 
	    	return CHtml::dropDownlist('months', $month, $months,array('disabled'=>true)).CHtml::dropDownlist('years', $year, $years,array('disabled'=>true));
	    }
	}
	public static function getInvoiceDateEdit($month,$year,$id){		
	    $months = self::getMonths();   $years = self::getYearsGrid();
		if ($year == 0 || $month == 0){    $month = date("m");    $year = date("Y");}		
	    return CHtml::dropDownlist('months', $month, $months, array(
	         'style' =>'    width: 45% !important;',
	    )).CHtml::dropDownlist('years', $year, $years, array(
	        'style' =>'   left: 135px;width: 45% !important;',
	    ));
	}
	public static function getmaxAgePendingAll($cust){
		$value=Yii::app()->db->createCommand("select MAX(DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01')))) as maxage
			from invoices r
			where r.invoice_date_year is not null and r.invoice_date_month is not null and  ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status in ('Not Paid') or r.partner_status is null or partner_status='' or partner_status=' ')))  and r.id_customer='".$cust."' ")->queryScalar();
		return $value;
	}
	public static function getavgageNotPaidAll($cust){
		$values=Yii::app()->db->createCommand("select DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) as age
			from invoices r
			where r.invoice_date_year is not null and r.invoice_date_month is not null and ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status in ('Not Paid') or r.partner_status is null or partner_status='' or partner_status=' ')))  and r.id_customer='".$cust."'")->queryAll();
		if(!empty($values)){
			$ct = array_sum(array_column($values,'age'))/count($values);
		} else{
			$ct=0;
		}
		return $ct;
	}
	public static function gettotalnotpaidandAll($cust){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from invoices r
			where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status in ('Not Paid') or r.partner_status is null or partner_status='' or partner_status=' ') ))  and r.id_customer='".$cust."' ")->queryAll();
		 $ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}	
	public static function getnetPendingPerYear($year){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from invoices r
			where r.id_customer not in (239,323) and ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status in ('Not Paid') or r.partner_status is null or partner_status='' or partner_status=' ') ))  and r.invoice_date_year=".$year." ")->queryAll();
		 $ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function getnettotPerYear($year){
		$values=Yii::app()->db->createCommand("select 	case when r.currency=9 	THEN r.net_amount	else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount	from invoices r	where r.id_customer not in (239,323) and ((r.partner =77 and r.status not in ('Cancelled', 'New', 'To Print')) OR (r.partner !=77 and r.status not in ('Cancelled', 'New', 'To Print') and (r.partner_status !='Cancelled' or r.partner_status is null or partner_status='' or partner_status=' ') ) )  and r.invoice_date_year=".$year." ")->queryAll();
		 $ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
     public static function getNumberInvoicesPrinted($invoices = null){ 
		$part = array();	$total = 0;	$mes1=" ";	$message = "";	$sns = Maintenance::PARTNER_SNS;	$snsi = Maintenance::PARTNER_SNSI; $snsaust=  Maintenance::PARTNER_AUST;
		$snsapj = Maintenance::PARTNER_SNSAPJ;	$apj = Maintenance::PARTNER_APJ;	$sns_span = Maintenance::PARTNER_SPAN;		$cubes =  Maintenance::PARTNER_CUBES; 
		if ($invoices == null){
			$message = 'You have to select at least one invoice!';
		}else{
			$ids = '('.implode(',',$invoices).')';
			$id_customers = Yii::app()->db->createCommand("SELECT id_customer FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND id IN $ids")->queryColumn();
			$ids_customers = '('.implode(',',$id_customers).')';
			if ($id_customers != null){
				$value = Yii::app()->db->createCommand("SELECT id FROM customers WHERE (ISNULL(bill_to_contact_email) OR ISNULL(bill_to_address) OR ISNULL(bill_to_contact_person)) AND id IN $ids_customers ")->queryScalar();
				if ($value != null)	{
					$message = "not bill";
					return $message;
				}
			}			
			$ids_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$ids_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsi AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_aust = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsaust AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsapj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_apj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $apj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns_span AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_cube = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $cubes AND id IN $ids ORDER BY partner")->queryScalar();
			if ($ids_sns == 0 && $ids_snsi == 0 && $ids_snsapj == 0  && $ids_apj == 0 && $ids_sns_span == 0 && $ids_aust == 0 && $ids_cube == 0){
				$message = 'There are no invoices in status Printed';
			}else{
				$message = 'Send';
			}
		}
		return $message;
    }
    public static function getNumberInvoicesEmail($invoices){ 
		$part = array();	$total = 0;		$mes1=" ";		$message = "";		$sns = Maintenance::PARTNER_SNS;		$snsi = Maintenance::PARTNER_SNSI; $snsaust=  Maintenance::PARTNER_AUST;
		$snsapj = Maintenance::PARTNER_SNSAPJ;		$apj = Maintenance::PARTNER_APJ;		$sns_span = Maintenance::PARTNER_SPAN;  $cubes =  Maintenance::PARTNER_CUBES; 
		if ($invoices == null){
			$message = 'You have to select at least one invoice!';
		}else{
			$ids = '('.implode(',',$invoices).')';
			$id_customers = Yii::app()->db->createCommand("SELECT id_customer FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND id IN $ids")->queryColumn();
			$ids_customers = '('.implode(',',$id_customers).')';
			if ($id_customers != null){
				$value = Yii::app()->db->createCommand("SELECT id FROM customers WHERE (ISNULL(bill_to_contact_email) OR ISNULL(bill_to_address) OR ISNULL(bill_to_contact_person)) AND id IN $ids_customers ")->queryScalar();
				if ($value != null){
					$message = "not bill";
					return $message;
				}
			}
			$ids_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$ids_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $snsi AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_snsaust = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $snsaust AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $snsapj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_apj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $apj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $sns_span AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_cubes = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status in ('".Invoices::STATUS_TO_PRINT."', '".Invoices::STATUS_NEW."') AND partner = $cubes AND id IN $ids ORDER BY partner")->queryScalar();
			if ($ids_sns > 0 || $ids_snsi > 0 || $ids_snsapj > 0 || $ids_apj > 0 || $ids_sns_span > 0 || $ids_snsaust >  0 || $ids_cubes > 0){
				$message = 'Toprint';	return $message;
			}
			$idsp_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$idsp_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsi AND id IN $ids   ORDER BY partner")->queryScalar();
			$idsp_snsaust = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsaust AND id IN $ids   ORDER BY partner")->queryScalar();
			$idsp_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsapj AND id IN $ids   ORDER BY partner")->queryScalar();
			$idsp_apj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $apj AND id IN $ids   ORDER BY partner")->queryScalar();
			$idsp_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns_span AND id IN $ids   ORDER BY partner")->queryScalar();
			$idsp_cubes = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $cubes AND id IN $ids   ORDER BY partner")->queryScalar();
			if ($idsp_sns > 0 || $idsp_snsi > 0 || $idsp_snsapj > 0 || $idsp_apj > 0 || $idsp_snsaust > 0 || $idsp_cubes > 0 ){
				$message = 'printed';
			}else if ($idsp_sns_span > 0)
			{
				$message = 'SPAN';
			}
			else{
				$message = 'invalid';
			}
		}
		return $message;
    }
    public static function getNumberInvoicesToPrint($invoices = null){ 
		$part = array();		$total = 0;		$mes1=" ";		$message = "";		$sns = Maintenance::PARTNER_SNS;		$snsi = Maintenance::PARTNER_SNSI;
		$snsaust = Maintenance::PARTNER_AUST;	 $snsapj = Maintenance::PARTNER_SNSAPJ;		$apj = Maintenance::PARTNER_APJ;		$sns_span = Maintenance::PARTNER_SPAN; $cubes = Maintenance::PARTNER_CUBES;
		if ($invoices == null){
			$message = 'You have to select at least one invoice!';
		}else{
			$ids = '('.implode(',',$invoices).')';	

			$aux= Yii::app()->db->createCommand("select name from customers where id in (select id_customer from invoices  WHERE id in ".$ids." and partner= 77) and (dolphin_aux is null or TRIM(dolphin_aux)='')")->queryAll();
						
			if(!empty($aux)){
				 
				$straux=implode(', ', array_column($aux,'name'));
				$message ="Kindly specify the dolphin auxiliary for direct invoice customer(s): ".$straux;
				return $message;
			}	

			$id_customers = Yii::app()->db->createCommand("SELECT id_customer FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND id IN $ids")->queryColumn();
			$ids_customers = '('.implode(',',$id_customers).')';
			if ($id_customers != null){
				$value = Yii::app()->db->createCommand("SELECT id FROM customers WHERE (ISNULL(bill_to_contact_email) OR ISNULL(bill_to_address) OR ISNULL(bill_to_contact_person)) AND id IN $ids_customers ")->queryScalar();
				if ($value != null){
					$message = "not bill";
					return $message;
				}
			}	

			$po = Yii::app()->db->createCommand("SELECT count(1) FROM customers WHERE lpo_required= 'Yes' and id in ( select id_customer from invoices where status = '".Invoices::STATUS_TO_PRINT."' and ( po is null or TRIM(po)='' ) and type='Maintenance'  AND id IN $ids) ")->queryScalar();
			if($po > 0)
			{
				$message = "po";
					return $message;
			}


			$ids_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$ids_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $snsi AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $snsapj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_snsaust =Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $snsaust AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_apj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $apj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $sns_span AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_cubes = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $cubes AND id IN $ids ORDER BY partner")->queryScalar();
			if ($ids_sns == 0 && $ids_snsi == 0 && $ids_snsapj == 0  && $ids_apj == 0 && $ids_sns_span == 0 && $ids_snsaust == 0 && $ids_cubes ==0){
				$message = 'There are no invoices in status To Print';
			}else{
			/*	 if ($ids_sns+$ids_snsaust!= 0 ){
					if ($ids_sns+$ids_snsaust > 1)	
						$message .= $ids_sns+$ids_snsaust.' SNS Invoices to Print.<br> ';
					else 
						$message .= $ids_sns+$ids_snsaust.' SNS Invoice to Print.<br> ';
				} */
				if ($ids_snsaust != 0 ){
					if ($ids_snsaust > 1)
						$message .= $ids_snsaust.' SNS AUST Invoices to Print.<br>';
					else 
						$message .= $ids_snsaust.' SNS AUST Invoice to Print.<br>';
				}
				if ($ids_sns+$ids_snsi != 0 ){
					if ($ids_sns+$ids_snsi > 1)	
						$message .= $ids_sns+$ids_snsi.' SNS Invoices to Print.<br> ';
					else 
						$message .= $ids_sns+$ids_snsi.' SNS Invoice to Print.<br> ';
				}
				if ($ids_snsi != 0 ){
					if ($ids_snsi > 1)
						$message .= $ids_snsi.' SNSI Invoices to Print.<br>';
					else 
						$message .= $ids_snsi.' SNSI Invoice to Print.<br>';
				}
				if ($ids_sns+$ids_snsapj != 0 ){
					if ($ids_sns+$ids_snsapj > 1)	
						$message .= $ids_sns+$ids_snsapj.' SNS Invoices to Print.<br> ';
					else 
						$message .= $ids_sns+$ids_snsapj.' SNS Invoice to Print.<br> ';
				}
				if ($ids_snsapj != 0 ){
					if ($ids_snsapj > 1)
						$message .= $ids_snsapj.' SNS APJ Invoices to Print.<br>';
					else 
						$message .= $ids_snsapj.' SNS APJ Invoice to Print.<br>';
				}
				if ($ids_apj != 0 ){
					if ($ids_apj > 1)
						$message .= $ids_apj.'APJ Invoices to Print.<br>';
					else 
						$message .= $ids_apj.'APJ Invoice to Print.<br>';
				}
				if ($ids_sns_span != 0 ){
					if ($ids_sns_span > 1)
						$message .= $ids_sns_span.' SPAN Invoices to Print.<br>';
					else 
						$message .= $ids_sns_span.' SPAN Invoice to Print.<br>';
				}
				if ($ids_cubes != 0 ){
					if ($ids_cubes > 1)
						$message .= $ids_cubes.' LOG CUBES Invoices to Print.<br>';
					else 
						$message .= $ids_cubes.' LOG CUBES Invoice to Print.<br>';
				}
			}
			$idsp_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$idsp_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsi AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_snsaust = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsaust AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsapj AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_apj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $apj AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns_span AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_cubes = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $cubes AND id IN $ids ORDER BY partner")->queryScalar();
			if ($idsp_sns > 0 || $idsp_snsi > 0 || $idsp_snsapj > 0 || $idsp_apj > 0 || $idsp_sns_span > 0 || $idsp_snsaust > 0 || $idsp_cubes > 0){
				$message = 'printed';
			}
		}
		return $message;
    }
    public static function getNumberTransferToPrint($invoices = null){ 
		$part = array();	$total = 0;		$mes1=" ";	$message = "";		 
		if ($invoices == null){
			$message = 'You have to select at least one invoice!';
		}else{
			$ids = '('.implode(',',$invoices).')';			 
			$partner = Yii::app()->db->createCommand("SELECT distinct(partner) FROM invoices WHERE id IN $ids and status not in ('New','To Print','Cancelled') and partner not in (77,79)")->queryAll();
			if (empty($partner)){				
				$message = 'Selected invoices must not be (New, To Print, Cancelled) and for SNSI or SNS APJ!';
			}else if(sizeof($partner)>1){
				$message = 'You have to select invoices for one partner only!';
			}else {
				$transnb= Utils::createTransferNumberPartner($partner[0]['partner']);
				$message=Codelkups::getCodelkup($partner[0]['partner']).' Transfer#'.$transnb;
			}		
		}
		return $message;
    }
 	public static function postTransfer($nb, $invoices){
		Yii::app()->db->createCommand("UPDATE invoices SET transfer_number ='".$nb."' where id in ".$invoices." ")->execute();
	}
    public static function changeEaStatus($id_ea){
    	if ($id_ea != null){
    		$id_ea = (int) $id_ea;   	$sum = 0;
	    	$results = Yii::app()->db->createCommand("SELECT payment_procente FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND id_ea = '{$id_ea}'")->queryAll();
			foreach ($results as $result){
	    		$sum += $result['payment_procente'];
	    	}
	    	$allinv= Yii::app()->db->createCommand("SELECT count(1) from invoices where id_ea ='{$id_ea}' and status!= 'Cancelled' and status!= 'Printed' ")->queryScalar();
	    	if ($sum != 100 && $sum != 200 && $allinv !=0 && Eas::checktandmFlag($id_ea) == 0){
	    		Yii::app()->db->createCommand("UPDATE eas SET status ='".Eas::STATUS_PART_INVOICED."' where id = '{$id_ea}' ")->execute();
	    	}else{
	    		Yii::app()->db->createCommand("UPDATE eas SET status ='".Eas::STATUS_FULLY_INVOICED."' where id = '{$id_ea}'")->execute();
	    	}
    	}    		
    }    
    
    public function renderCustomer(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("customers/view", array("id" => $this->id_customer)).'">'.$this->customer->name.'</a>';
	}
	public function renderNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("invoices/view", array("id" => $this->id)).'">'.Utils::paddingCode($this->id).'</a>';
	}
	public function renderEANumber(){
		if(!empty($this->id_ea) && $this->id_ea!=0 )
		{
			echo '<a class="show_link" href="'.Yii::app()->createUrl("eas/update", array("id" => $this->id_ea)).'">'.Utils::paddingCode($this->id_ea).'</a>';
		}else{
		return '';}
	}
	public static function checkToPrintTravelInvoices($travel){		
		$id_project=$travel->id_project;	$id_customer=$travel->id_customer;	$id_res=$travel->id_user;
		if ($travel->training == '1') {
			if(substr($id_project, -1) == 't') { $id_project = substr($id_project, 0, -1);}
			$ea=Trainings::getEA($id_project);
            $result = Yii::app()->db->createCommand('SELECT id FROM invoices where id_customer='.$id_customer.' and id_ea='.$ea.' and id_resource='.$id_res.' and status="To Print" and type="Travel Expenses" and invoice_title=(select name from trainings where id='.$id_project.') order by id desc LIMIT 1')->queryScalar();
        }else{
        	$result =  Yii::app()->db->createCommand('SELECT id FROM invoices where id_project='.$id_project.' and id_customer='.$id_customer.' and id_resource='.$id_res.' and status="To Print" and type="Travel Expenses" ' )->queryScalar();
		}
		return $result;
	}
	public static function UpdateInvoiceAmount($id_inv,$amount){
		Yii::app()->db->createCommand("UPDATE invoices SET amount = amount+{$amount} , gross_amount=gross_amount+{$amount} , net_amount=net_amount+{$amount} where id = {$id_inv} and status='To Print' ")->execute();
	}
	public static function UpdateALLInvoiceAmount($id_inv,$amount){
		Yii::app()->db->createCommand("UPDATE invoices SET amount = {$amount} , gross_amount={$amount} , net_amount={$amount} where id = {$id_inv} and status='To Print' ")->execute();
	}
	public static function changeStatusInv($model,$final_number,$partner_inv){  
		if ($model->final_invoice_number == null){
			if ($model->old == "Yes"){
				$model->final_invoice_number = null;
				if ($model->old_sns_inv == null || $model->old_sns_inv == '' ){
					$model->old_sns_inv = $partner_inv;
				}
			} else if($model->partner == Maintenance::PARTNER_AUST)
			{
				$model->final_invoice_number = null;
				if (empty($model->partner_inv)){
					$model->partner_inv = $partner_inv;
					$model->partner_status = 'Not Paid';
				}
			}
			else{
				$model->final_invoice_number = $final_number;
			}			
			$model->status = Invoices::STATUS_PRINTED;
			if (!isset($model->printed_date)){
				$model->printed_date = date('Y-m-d');
			}			
			if ($model->partner == Maintenance::PARTNER_SNSI){
				$model->partner_inv = $partner_inv;
				$model->partner_status = 'Not Paid';
			}
			
			if ($model->partner == Maintenance::PARTNER_SNSAPJ){
				$model->snsapj_partner_inv = $partner_inv;
				$model->partner_status = 'Not Paid';
			}
			if ($model->partner == Maintenance::PARTNER_APJ && (empty($model->snsapj_partner_inv) || $model->snsapj_partner_inv == null)){	
				$model->snsapj_partner_inv = $partner_inv;
				$model->partner_inv = $partner_inv;
				$model->final_invoice_number = null;			
			} 
			if ($model->partner == Maintenance::PARTNER_APJ ){				
				$model->final_invoice_number = null;			
			} 
			$pay_date = date('Y-m-d', strtotime( $model->printed_date."+1 month"));
			$model->save();
			$customer_model = Customers::model()->findByPk((int)$model->id_customer);
			$project_name = $model->project_name;
			Invoices::changeEaStatus($model->id_ea);
		}else {
				if($model->status=='To Print'){
					$model->status = Invoices::STATUS_PRINTED;
					if (!isset($model->printed_date)){
						$model->printed_date = date('Y-m-d');
					}
					$pay_date = date('Y-m-d', strtotime( $model->printed_date."+1 month"));
					$model->save();
				}				
		}
	}	
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN invoices ON invoices.id_customer=customers.id order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['name'];		$customers[$i]['id'] = $res['id'];	}
		return $customers;
	}
	public static function getEasAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT eas.id, eas.ea_number FROM eas INNER JOIN invoices ON invoices.id_ea=eas.id')->queryAll();
		$eas = array();
		foreach ($result as $i=>$res){		$eas[$i]['label'] = $res['ea_number'];		$eas[$i]['id'] = $res['id'];	}
		return $eas;
	}	
	public static function getInvoicesAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT id, final_invoice_number FROM invoices WHERE final_invoice_number IS NOT NULL')->queryAll();
		$inv = array();
		foreach ($result as $i => $res){	$inv[$i]['label'] = $res['final_invoice_number'];	$inv[$i]['id'] = $res['id'];}
		return $inv;
	}	
	public static function getAllPartners($id_invoice,$id_partner){
		$partners = Codelkups::getCodelkupsDropDown('partner');
		if(GroupPermissions::checkPermissions('financial-invoices','write')){
			return CHtml::dropDownlist('assigned_to', $id_partner, $partners, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_invoice.','."2".')',
		    	'style'=>'width:70px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_partner, $partners, array(
		       	'disabled'=>true,
		    	'style'=>'width:70px;border:none;'
		    ));
	    }
	}
	public static function getAllSoldBy($id_invoice,$id_sold){
		$sold = Codelkups::getCodelkupsDropDown('unit');
	    if(GroupPermissions::checkPermissions('financial-invoices','write')){
			return CHtml::dropDownlist('assigned_to', $id_sold, $sold, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_invoice.','."3".')',
		    	'style'=>'width:95px;border:none;',
		    	'prompt'=>""
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_sold, $sold, array(
		    	'disabled'=>true,	
	    		'style'=>'width:95px;border:none;',
		    	
		    ));
	    }
	}
	public static function getOld($id_invoice,$old){
		$old_val = array(
	        'Yes' => 'Yes',
	        'No' => 'No',
	    );
	    if(GroupPermissions::checkPermissions('financial-invoices','write')){
		    return CHtml::dropDownlist('assigned_to', $old, $old_val, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_invoice.','."4".')',
		    	'style'=>'border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $old, $old_val, array(
		        'disabled'=>true,
		    	'style'=>'border:none;'
		    ));
	    }
	}
	public static function checkinvPending($project){
		$result =  Yii::app()->db->createCommand("
			select COUNT(*) from invoices where id_ea in (SELECT id from eas where id_project='".$project."') and status in ('New','To Print')")->queryScalar();
		if ($result >0)
			return ' Yes';
		else
			return ' No';
	}
} ?>