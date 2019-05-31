<?php
class Maintenance extends CActiveRecord{
	const PARTNER_SNS = 77;	const PARTNER_SNSI = 78;	const PARTNER_SPAN = 79;	const SUPPORT_TASK = 27;	const PARTNER_SNSAPJ = 201;
	const PARTNER_APJ = 554;	const PARTNER_AUST = 1218; const PARTNER_CUBES = 1336;
		public $customErrors = array();	public $customer_name;	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'maintenance';
	}
	public function rules(){
		return array(
			array('contract_description,customer, owner, product,licenses, frequency, original_amount, currency, sns_share,short_description, wms_db_type , soft_version, contract_duration,po_renewal', 'required'),
			array('customer, owner, product, support_service,ea , frequency, currency,currency_usd,currency_rate_id, net_share, wms_db_type , soft_version ,nbwarehourses, end_customer', 'numerical', 'integerOnly'=>true),
			array('original_amount,amount', 'numerical'),
			array('status', 'length', 'max'=>10),
			array('sma_recipients, sma_instances','validateplan'),
			array('sma_recipients','validateEmails'),
			array('ea','validateEa'),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers'),
			array('short_description, notes,file,contract_description,starting_date', 'length', 'max'=>250),
			array('sma_recipients, sma_instances', 'length', 'max'=>500),
			array('sns_share', 'numerical', 'max'=>100, 'min'=>0),
			array('net_share', 'numerical', 'max'=>100, 'min'=>0),
			array('escalation_factor,real_esc, licenses', 'numerical', 'max'=>100, 'min'=>0),
			array('po_renewal, cpi', 'length', 'max'=>3),
			array('id_maintenance,currency_rate_id, notes,contract_description, customer, owner, product, support_service, frequency, original_amount, currency,real_esc, escalation_factor, net_share, sns_share, starting_date, status, short_description, file, contract_duration, po_renewal, cpi', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'supportService' => array(self::BELONGS_TO, 'Codelkups', 'support_service'),
			'customer0' => array(self::BELONGS_TO, 'Customers', 'customer'),
			'endcustomer' => array(self::BELONGS_TO, 'Customers', 'end_customer'),
			'currency0' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'owner0' => array(self::BELONGS_TO, 'Codelkups', 'owner'),
			'product0' => array(self::BELONGS_TO, 'Codelkups', 'product'),
			'frequency0' => array(self::BELONGS_TO, 'Codelkups', 'frequency'),
			'maintenanceItemss' => array(self::HAS_MANY, 'MaintenanceItems', 'id_contract'),
			'maintenanceServicess' => array(self::HAS_MANY, 'MaintenanceServices', 'id_contract'),
			'wmsdbtype' => array(self::BELONGS_TO, 'Codelkups', 'wms_db_type'),
			'swversion' => array(self::BELONGS_TO, 'Codelkups', 'soft_version')
		);
	}

	public function validateEa($attribute, $params){		
	        if ( !empty($this->ea)  && !empty($this->customer)   &&  !Eas::validateMaintenanceEa($this->ea,$this->customer ) ){
	            $this->addError('ea','EA# is not valid');
			}else if (empty($this->ea) && (empty(($this->id_maintenance) || $this->id_maintenance>322)) && $this->status == 'Active')
			{
				 $this->addError('ea','EA# cannot be blank.');
			}
	}	
	public function validateplan($attribute, $params){		
	        if ( $this->support_service == 501  && $this->customer != 101 ){
	            $ev = CValidator::createValidator('required', $this, $attribute, $params);
      			$ev->validate($this);
			}
	}	
public function validateEmails(){
	 if ( $this->support_service == 501 && $this->customer != 101  ){
	        if(!empty($this->sma_recipients)){
	         	$emails= explode(',', $this->sma_recipients);    $f=false;
		       	foreach ($emails as $key => $value) {	if(!filter_var(trim($value), FILTER_VALIDATE_EMAIL)) { 	$f=true; }	}
		       	if($f){   $this->addError('sma_recipients','SMA Recipients has invalid value.');}
	        }	        	
	    }			
	}
	public function attributeLabels(){
		return array(
			'id_maintenance' => 'Id Maintenance',
			'contract_description' => 'Contract Description',
			'customer' => 'Customer',
			'owner' => 'Owner',
			'product' => 'Product',
			'support_service' => 'Support Service',
			'support_service2' => 'Service',
			'frequency' => 'Frequency',
			'original_amount' => 'Original Amount',
			'currency' => 'Currency',
			'escalation_factor' => 'Esc %',
			'real_esc' =>'Current Esc %',
			'licenses' =>'Licenses',
			'sns_share' => 'Sns Share',
			'net_share' => 'Net Share',
			'starting_date' => 'Starting Date',
			'status' => 'Status',
			'short_description' => 'Short Description',
			'sma_recipients' => 'SMA Recipients',
			'sma_instances' => 'SMA Instances',
			'notes' => 'Notes',	
			'file' => 'File',
			'contract_duration' => 'Contract Duration',
			'travel_expenses' => 'Travel Expenses',
			'po_renewal' => 'Po Renewal',
			'cpi' => 'CPI',
			'nbwarehourses' => '# of Warehouses',
			'ea' =>'EA#'
		);
	}
	public static function getStatusList(){		
			return array('Active' => 'Active','Inactive' => 'Inactive'); 
	}
	public static function getContractByItem($item){
		return  Yii::app()->db->createCommand("SELECT id_contract FROM maintenance_items WHERE id=".$item)->queryScalar();
	}
	public static function getVersionListPerCustomer($customer){
    	$products = array();
    	if(empty($customer))
    	{
    		$products=Codelkups::getCodelkupsDropDown('soft_version');
    		return $products;
    	}
		$results = Yii::app()->db->createCommand("select distinct maintenance.soft_version from maintenance where customer=".$customer." ")->queryAll();
		if(empty($results)|| $customer==177){
			$products=Codelkups::getCodelkupsDropDown('soft_version');
		}else{
			foreach ($results as $i => $res){	$products[$res['soft_version']] = Codelkups::getCodelkup($res['soft_version']);		}
		}
       	return $products;
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand("SELECT DISTINCT c.id, c.name FROM customers c WHERE c.name != 'N/A' order by c.name")->queryAll();
		$items = array();
		foreach ($result as $i=>$res){	$items[$i]['label'] = $res['name'];	$items[$i]['id'] = $res['id'];}
		return $items;
	}
	public function getItems(){
		$criteria=new CDbCriteria;	$criteria->with = array('maintenance0');	$criteria->condition = 'id_contract = '.$this->id_maintenance;	
		return new CActiveDataProvider('MaintenanceItems', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'t.id ASC',  
            	'attributes'=>array(
		            
		            '*',
		        ),
            ),
		));
	}
	public function getMaintenanceInvoices(){
		$criteria=new CDbCriteria;	$criteria->with = array('idContract');	$criteria->condition = 'id_contract = '.$this->id_maintenance;	
		return new CActiveDataProvider('MaintenanceInvoices', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'t.from_period ASC,t.to_period ASC',  
            	'attributes'=>array(
		            
		            '*',
		        ),
            ),
		));
	}
	public function getMaintenanceServices(){
		$criteria=new CDbCriteria;	$criteria->with = array('idContract');	$criteria->condition = 'id_contract = '.$this->id_maintenance;
		return new CActiveDataProvider('MaintenanceServices', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),			
             
		));
	}
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public function getTotalPeriodicAmountUsd(){
		$total_periodic_amount = 0;
		$periodic_amount = self::getPeriodicAmount();
		$freq = self::getPeriod();
		foreach($this->maintenanceItemss as $item){
			if($item['status'] == '1')
			{
				$total_periodic_amount += $item['amount_usd']/$freq;
			}
		}			
		return $periodic_amount+$total_periodic_amount;
	}
	public function getTotalGrossAmountUsd(){

		$countinv= Yii::app()->db->createCommand("select count(1) from maintenance_invoices where id_contract=".$this->id_maintenance)->queryScalar();	
		
		if($countinv == 0){
			$total_gross_amount = 0;
			$amount_usd = $this->amount;
			foreach($this->maintenanceItemss as $item){
				if($item['status'] == '1')
				{
					$total_gross_amount += $item['amount_usd'];	
				}		
			}
			return $total_gross_amount + $amount_usd ;
		}
		$values= Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.gross_amount 
			else r.gross_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as tot_gross_amount
			from invoices r where r.id in (select max(id_invoice) from maintenance_invoices where id_contract= $this->id_maintenance order by from_period desc)")->queryAll();	
		$ct = array_sum(array_column($values,'tot_gross_amount'));
		return $ct;			
	}	

	public function getTotalGrossAmountUsdFromlastINV(){
		$total_gross_amount = 0;
		$amount_usd =  Yii::app()->db->createCommand("select gross_amount from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where id_contract= ".$this->id_maintenance." order by to_period desc, amount desc limit 1 ) ")->queryScalar();
		if(empty($amount_usd) || $amount_usd=="")
		{
			$amount_usd = $this->amount;
			foreach($this->maintenanceItemss as $item){
				if($item['status'] == '1'){
					$total_gross_amount += $item['amount_usd'];		
				}	
			}
		}else{
			$total_gross_amount = Yii::app()->db->createCommand("select sum(amount_usd) from maintenance_items where id_contract= ".$this->id_maintenance." 
																	and date > (select adddateinv from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where id_contract= ".$this->id_maintenance." order by to_period desc, amount desc limit 1))")->queryScalar();
			if(empty($total_gross_amount)  || $total_gross_amount== null || $total_gross_amount==""){	$total_gross_amount = 0;	}
		}			
		return $total_gross_amount + $amount_usd ;
	}	
	public function getTotalGrossAmountFromlastINVBefore($inv)
	{
		$total_gross_amount = 0;
			$p=$this->getPeriod();
		$amount =  Yii::app()->db->createCommand("select gross_amount, date(adddateinv)  as adddateinv   from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where   id_invoice in (select id from invoices where status in ('Printed', 'Not Paid', 'Paid')) and id_contract= ".$this->id_maintenance." and id_invoice < ".$inv." order by to_period desc, id_invoice desc  limit 1 ) ")->queryRow();
		if(empty($amount))
		{
			$tot_amount = $this->original_amount;
			foreach($this->maintenanceItemss as $item){ 
				if($item['status'] == '1')
				{
					if ($p>1 && $item['amount']>0){ $total_gross_amount = $item['amount']/$p; }
					else{ $total_gross_amount += $item['amount'];	}	
				}	
			}
		}else{
			$tot_amount = $amount['gross_amount'];
			$total_gross_amount = Yii::app()->db->createCommand("select sum(amount) from maintenance_items where id_contract= ".$this->id_maintenance." and status=1
																	and date > (select adddateinv from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where id_invoice in (select id from invoices where status in ('Printed', 'Not Paid', 'Paid')) and id_contract= ".$this->id_maintenance." and id_invoice < ".$inv." order by to_period desc, id_invoice desc  limit 1))")->queryScalar();
			if(empty($total_gross_amount)  || $total_gross_amount== null || $total_gross_amount==""){	$total_gross_amount = 0;	}else{
				if ($p>1){ $total_gross_amount = $total_gross_amount/$p; } }
			
			foreach($this->maintenanceItemss as $item){ 
				if($item['status'] == '2' && $item['deactivate_date'] >= $amount['adddateinv'])
				{ 
					if ($p>1){ $itemamt=$item['amount']/$p; }else{ $itemamt= $item['amount']; }
					$tot_amount = $tot_amount - $itemamt;		
				}	
			}
		}	 	
		return $total_gross_amount + $tot_amount ;
	}
	public function getTotalGrossAmountFromlastINVWithoutFreq(){
		$total_gross_amount = 0;
		$amount =  Yii::app()->db->createCommand("select gross_amount, date(adddateinv)  as adddateinv   from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where   id_invoice in (select id from invoices where status in ('Printed', 'Not Paid', 'Paid')) and id_contract= ".$this->id_maintenance." order by to_period desc limit 1 ) ")->queryRow();
		if(empty($amount))
		{
			$tot_amount = $this->original_amount;
			foreach($this->maintenanceItemss as $item){ 
				if($item['status'] == '1')
				{
					$total_gross_amount += $item['amount'];		
				}	
			}
		}else{
			$tot_amount = $amount['gross_amount'];
			$total_gross_amount = Yii::app()->db->createCommand("select sum(amount) from maintenance_items where id_contract= ".$this->id_maintenance." and status=1
																	and date > (select adddateinv from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where id_invoice in (select id from invoices where status in ('Printed', 'Not Paid', 'Paid')) and id_contract= ".$this->id_maintenance." order by to_period desc, amount desc limit 1))")->queryScalar();
			if(empty($total_gross_amount)  || $total_gross_amount== null || $total_gross_amount==""){	$total_gross_amount = 0;	}
			
			foreach($this->maintenanceItemss as $item){ 
				if($item['status'] == '2' && $item['deactivate_date'] >= $amount['adddateinv'])
				{ 
					$tot_amount = $tot_amount - $item['amount'];		
				}	
			}
		}	 	
		return $total_gross_amount + $tot_amount ;
	}	
	public function getTotalGrossAmountFromlastINV(){
		$total_gross_amount = 0; $p=$this->getPeriod(); 
		$amount =  Yii::app()->db->createCommand("select gross_amount, date(adddateinv)  as adddateinv   from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where  id_invoice in (select id from invoices where status in ('Printed', 'Not Paid', 'Paid')) and id_contract= ".$this->id_maintenance." order by  to_period desc, amount desc limit 1 ) ")->queryRow();
		if(empty($amount))
		{
			$tot_amount = $this->original_amount;
			foreach($this->maintenanceItemss as $item){ 
				if($item['status'] == '1')
				{
					if ($p>1 && $item['amount']>0){ $total_gross_amount = $item['amount']/$p; }
					else{ $total_gross_amount += $item['amount'];	}	
				}	
			}
		}else{
			$tot_amount = $amount['gross_amount'];
			$total_gross_amount = Yii::app()->db->createCommand("select sum(amount) from maintenance_items where id_contract= ".$this->id_maintenance." and status=1
																	and date > (select adddateinv from invoices where id = (SELECT id_invoice FROM `maintenance_invoices` where  id_invoice in (select id from invoices where status in ('Printed', 'Not Paid', 'Paid')) and  id_contract= ".$this->id_maintenance." order by  to_period desc, amount desc limit 1))")->queryScalar();
			if(empty($total_gross_amount)  || $total_gross_amount== null || $total_gross_amount=="" || $total_gross_amount==0 ){	$total_gross_amount = 0;	}else{
				if ($p>1){ $total_gross_amount = $total_gross_amount/$p; } 
			}
			
			foreach($this->maintenanceItemss as $item){ 
				if($item['status'] == '2' && $item['deactivate_date'] >= $amount['adddateinv'])
				{ 
					if ($p>1){ $itemamt=$item['amount']/$p; }else{ $itemamt= $item['amount']; }
					$tot_amount = $tot_amount - $itemamt;		
				}	
			}
		}	 	
		return $total_gross_amount + $tot_amount ;
	}	

	public function getTotalGrossAmount(){
		$total_gross_amount = 0;
		$amount_usd = $this->original_amount;
		foreach($this->maintenanceItemss as $item){
			if($item['status'] == '1'){
				$total_gross_amount += $item['amount'];	
			}		
		}
		return $total_gross_amount + $amount_usd ;
	}	
	public function getTotalNetAmountUsd(){

		$countinv= Yii::app()->db->createCommand("select count(1) from maintenance_invoices where id_contract=".$this->id_maintenance)->queryScalar();	
		if($countinv == 0){
			$total_net_amount = 0;	$net_amount = $this->amount*($this->sns_share)/100;
			foreach($this->maintenanceItemss as $item){	
				if($item['status'] == '1'){
					$total_net_amount += $item['amount_usd'] * $item['sns_share']/100; 
				}
			}
			return $total_net_amount+$net_amount;
		}
		
		$values= Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as tot_net_amount
			from invoices r where r.id in (select max(id_invoice) from maintenance_invoices where id_contract= $this->id_maintenance order by from_period desc)")->queryAll();	
		$ct = array_sum(array_column($values,'tot_net_amount'));
		return $ct;			
	}
	public function getTotalNetAmountYear($year){
		$countinv= Yii::app()->db->createCommand("select count(1) from maintenance_invoices where id_contract=".$this->id_maintenance)->queryScalar();	
		if($countinv == 0){
			$total_net_amount = 0;	$net_amount = $this->amount * $this->sns_share/100;
			foreach($this->maintenanceItemss as $item){	
			if(((int)$year) >= ((int)(substr($item['date'], 0, 4)))) {
					if($item['status'] == '1' || ($item['status'] == '2' && ((int)$year)<((int)(substr($item['deactivate_date'], 0, 4)))))
					{
						$total_net_amount += $item['amount_usd'] * $item['sns_share']/100;	
					}
				} 
			}
			return $total_net_amount+$net_amount;
		}
		return Yii::app()->db->createCommand("SELECT net_amount from invoices where id in (select id_invoice from maintenance_invoices where id_contract= $this->id_maintenance ) and invoice_date_year=  ".$year)->queryScalar();		
		
		
	}	
	public static function getEndDate($customer, $plan){
		if(empty($customer) || empty($plan)){ return ''; } 
		$duration= Yii::app()->db->createCommand("SELECT  CASE WHEN starting_date is not null THEN MIN(MONTH(starting_date)) ELSE 'FOREVER' END as date from maintenance where support_service=".$plan." and customer=".$customer." and status='Active'  order by date ASC limit 1 ")->queryScalar();		
		if(empty($duration)){ return ''; }
		else if ($duration == 'FOREVER'){ return '';}
		else{ return "Expiry Date: ".date('F', strtotime("2000-$duration-01")); }
	}
	public static function getTotalNetAmountYearPerCustomer($customer ,$year){ 
		$contracts= Yii::app()->db->createCommand("select id_maintenance from maintenance where customer=".$customer." and YEAR(starting_date)<=".$year." ")->queryAll();		
		$flag= false;
		$total_amount = 0;
		$cost=0;	
		$text='';
		$hours=0;
		$time=0;
		$feliteprem= true;
		$dates='';
		$srs = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE id_customer = ".$customer."  AND YEAR(date)=".$year." ")->queryScalar();
		$manhour=SystemParameters::getCost();
		foreach($contracts as $contract){
			$model = Maintenance::model()->findByPk($contract);
			if($model->status == 'Active'){ $flag=true;}
			$total_amount += $model->getTotalNetAmountYear($year);
			if($model->support_service != '503')
			{
				$time = Maintenance::getHoursByYearNotStandard($model->id_maintenance, $year);
				$hours+= $time;		
				$cost+= ($time*$manhour);
				if($feliteprem)
				{
					$dates.=Codelkups::getCodelkup($model->support_service).' '.self::getEndDate($customer, $model->support_service).' <br />';
					$feliteprem=false;
				}
			}
		}
		$time =Maintenance::getHoursByYearCustomerTotal($customer, $year);	
		$hours+= $time;		
		$cost+= ($time*$manhour);
		$std=self::getEndDate($customer, '503');
		if(!empty($std))
		{ $dates.='Standard Plan '.$std;}
		if($flag || (!$flag && $hours>0))
		{ return array(0 => $total_amount ,1 => $cost, 2=>$srs, 3=>$hours, 4=>$dates); }
		else{  return array(0 => 'false',1 => $cost);}
	}
	public function getTotalPartnerAmountUsd(){
		$total_partner_amount = 0;
		$partner_amount = self::getPartnerAmount();
		foreach($this->maintenanceItemss as $item){	
			if($item['status'] == '1')
			{
				$total_partner_amount += $item['amount_usd']*(1-$item['sns_share']/100); 
			}
		}
		return $total_partner_amount+$partner_amount;
	}	
	public static function getGrossAmountInvoice($id_contract){
		$desc = 0;
		$result = Yii::app()->db->createCommand("SELECT amount FROM maintenance_items WHERE id_contract='{$id_contract}'")->queryAll();		
		foreach ($result as $res)
			$desc += $res['amount'] ;
		return $desc;
	}	
	public static function getMaint($id_cusr){
		$desc = 0;
		$result = Yii::app()->db->createCommand("SELECT support_service FROM maintenance WHERE customer='{$id_cusr}' AND STATUS='Active' order by support_service ASC limit 1")->queryScalar();
		$desc= Codelkups::getCodelkup($result);	$desc= str_replace('Plan', '', $desc);
		return $desc;
	}
	public static function getVersionPerProduct($customer, $id_product){	
		$db= Yii::app()->db->createCommand("select soft_version from maintenance where customer= ".$customer." and product= ".$id_product." order by starting_date desc limit 1 ")->queryScalar();
		if(empty($db)){ return 610; }
		return $db;	
	}
	public static function getDBMSPerProduct($customer, $id_product){	
		$db= Yii::app()->db->createCommand("select wms_db_type from maintenance where customer= ".$customer." and product= ".$id_product." order by starting_date desc limit 1 ")->queryScalar();
		if(empty($db)){ return 609; }
		return $db;	
	}
	public static function getMaintenanceDescription($id_contract){		
		$result = Yii::app()->db->createCommand("SELECT contract_description FROM maintenance WHERE id_maintenance='{$id_contract}'")->queryScalar();	
		return $result;
	}
	public static function getPartnerAmountInvoice($id_contract){
		$desc = 0;
		$result = Yii::app()->db->createCommand("SELECT amount, sns_share FROM maintenance_items WHERE id_contract='{$id_contract}'")->queryAll();		
		foreach ($result as $res)
			$desc += $res['amount']*(1-$res['sns_share']/100 );
		return $desc;
	}	
	public function getPartnerAmount(){
		return $this->amount*(1-$this->sns_share/100);
	}
	public function getPeriodicAmount(){
		switch ($this->frequency0->codelkup){ 
			case 'Biyearly':
				return ($this->amount*($this->sns_share)/100)/2;
			break;
			case 'Quarterly':
				return ($this->amount*($this->sns_share)/100)/4;
			break;
			case 'Monthly':
				return ($this->amount*($this->sns_share)/100)/12;
			 break;
			case 'Yearly':
				return ($this->amount*($this->sns_share)/100)/1;
			 break;
		}	
	}
	public static function getFrequency($freq){
		switch (Codelkups::getCodelkup($freq)) { 
			case 'Biyearly':
				return 6;
			break;
			case 'Quarterly':
				return 3;
			break;
			case 'Monthly':
				return 1;
			 break;
			case 'Yearly':
				return 12;
			 break;
		}
	}
	public function getPeriod(){
		switch ($this->frequency0->codelkup) { 
			case 'Biyearly':
				return 2;
			break;
			case 'Quarterly':
				return 4;
			break;
			case 'Monthly':
				return 12;
			 break;
			case 'Yearly':
				return 1;
			 break;
		}
	}
	public static function getPeriodFreqDays($freq){
		switch (Codelkups::getCodelkup($freq)) { 
			case 'Biyearly':
				return 182.5;
			break;
			case 'Quarterly':
				return 91.25;
			break;
			case 'Monthly':
				return 30.4;
			 break;
			case 'Yearly':
				return 364;
			 break;
		}
	}
	public static function getPeriodFreq($freq){
		switch (Codelkups::getCodelkup($freq)) { 
			case 'Biyearly':
				return 2;
			break;
			case 'Quarterly':
				return 4;
			break;
			case 'Monthly':
				return 12;
			 break;
			case 'Yearly':
				return 1;
			 break;
		}
	}
	public static function getDirPath($model_id){
		$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."maintenance".DIRECTORY_SEPARATOR."{$model_id}".DIRECTORY_SEPARATOR;
	 	if ( !is_dir( $path ) ) {    mkdir( $path, 0777, true); chmod( $path, 0777 ); }
		return $path; 
	}
	public static function getDBMS($contract){		
		$db = Yii::app()->db->createCommand( "SELECT wms_db_type FROM `maintenance` WHERE id_maintenance=".$contract."")->queryScalar();
		return Codelkups::getCodelkup($db);
	}
	public function getFile($path = false, $uploaded = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->customer.DIRECTORY_SEPARATOR.'maintenance'.DIRECTORY_SEPARATOR.$this->id_maintenance.DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->customer.DIRECTORY_SEPARATOR.'maintenance'.DIRECTORY_SEPARATOR.$this->id_maintenance.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) { $filePath .= $this->file;	$fileName .= $this->file;}else{	return null;}
		}else {
			$filePath .= 'EA_'.$this->ea_number.'.pdf';	$fileName .= 'EA_'.$this->ea_number.'.pdf';	
		}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public function search($selector = null){
		$criteria=new CDbCriteria;
		$criteria->with = array( 'customer0','endcustomer');
		$criteria->together = true;
		
		
		$criteria->compare('customer0.name', $this->customer, true);

		$criteria->compare('endcustomer.name', $this->end_customer, true);
		$criteria->compare('t.owner', $this->owner, true); 
			$criteria->compare('t.support_service', $this->support_service, true);
		 if (isset($this->product) && $this->product !=""){
				$criteria->join='LEFT JOIN codelkups ON codelkups.id=t.product';
				$criteria->compare('codelkups.codelkup ', $this->product);
			}
			if (isset($this->starting_date) && $this->starting_date !=""){
				$criteria->addCondition('MONTH(t.starting_date) = '.$this->starting_date.'');
			}
			if(isset($this->frequency) && $this->frequency!= ""){
			$criteria->addCondition('t.frequency = "'.$this->frequency.'"');
		}
		if(isset($this->status) && $this->status!= ""){
			$criteria->addCondition('t.status = "'.$this->status.'"');
		}else{
			$criteria->addCondition('t.status ='."'Active'");
		}
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => ($selector != null)?1000:Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder' => 'customer0.name ASC',            
		        'attributes' => array(
		            'cCountry.codelkup'=>array(
		                'asc'=>'cCountry.codelkup',
		                'desc'=>'cCountry.codelkup DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}
	public static function getProductsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT codelkups.id, codelkups.codelkup FROM codelkups INNER JOIN codelists ON codelkups.id_codelist="3" order by codelkups.codelkup')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['codelkup'];	$projects[$i]['id'] = $res['id'];}
		return $projects;
	}
	public static function getPartnersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT codelkups.id, codelkups.codelkup FROM codelkups INNER JOIN codelists ON codelkups.id_codelist="17"')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['codelkup'];	$projects[$i]['id'] = $res['id'];}
		return $projects;
	}
	public static function getAllMaintenanceTime($model){
		$id_customer = $model->customer;	$id_product = $model->product;
		$results = Yii::app()->db->createCommand("SELECT * FROM support_desk WHERE id_customer = ".$id_customer." AND product = ".$id_product." AND YEAR(date)>2015 ORDER BY date")->queryAll();
		$perform = array();
		$years=self::getYears($model->starting_date);
		foreach ($years as $result){			
			$perform[$result]['year'] = $result; 
			  		
		} 	
		return $perform;
	}
	public static function getAllSupportDesk($model){
		$id_customer = $model->customer;	$id_product = $model->product;
		$results = Yii::app()->db->createCommand("SELECT * FROM support_desk WHERE id_customer = ".$id_customer." AND product = ".$id_product." AND YEAR(date)>2015 ORDER BY date")->queryAll();
		$perform = array();
		$years=self::getYears($model->starting_date);
		foreach ($results as $result){			
			$perform[date('Y', strtotime($result['date']))]['year'] = date('Y', strtotime($result['date']));
			$perform[date('Y', strtotime($result['date']))]['support'][$result['id']] = $result;
			if($result['system_down']=="Yes"){
				if(!isset($perform[date('Y', strtotime($result['date']))]['system_down']))
					$perform[date('Y', strtotime($result['date']))]['system_down'] = 1;
				else
					$perform[date('Y', strtotime($result['date']))]['system_down'] ++;
			}
			if($result['escalate']=="Yes"){
				if(!isset($perform[date('Y', strtotime($result['date']))]['escalate']))
					$perform[date('Y', strtotime($result['date']))]['escalate'] = 1;
				else
					$perform[date('Y', strtotime($result['date']))]['escalate'] ++;
			}			
		}
		foreach ($years as $year) {
				if(!array_key_exists($year, $perform) && $year>2015)
				{
					$perform[$year]['year']=$year;
					$perform[$year]['support']=null;
					$perform[$year]['system_down']=0;
					$perform[$year]['escalate']=0;
				}
			}	
		return $perform;
	}
	public static function getYears($starting_date)
	{
		$current=date("Y");
		$years = array();
		$x=0;
		if(isset($starting_date))
		{
			$contractyear= date('Y', strtotime($starting_date));
			
			if($current == $contractyear)
			{
				$years[$x]= $current;
				return $years;
			}else
			{
				if($current < $contractyear)
				{
					$years[$x]= $contractyear;
					return $years;
				}else
				{
					while($current != $contractyear)
					{
						$years[$x]= $contractyear;
						$x ++; $contractyear++;
					}
					$years[$x]= $current;
				}
				return $years;
			}
		}
		else{
			$years = array();
			$years[$x]= $current;
			return $years;
		}
	}
	public static function getHoursByYearCustomerTotal($customer,$year){
				return(Yii::app()->db->createCommand("	select SUM(u.amount) from user_time u, ticket_id t, support_desk s
		where u.`default`=1 and u.id=t.id_user_time and t.ticket_id=s.id and s.id_customer=".$customer." and  YEAR(u.date)=".$year." ")->queryScalar());
	}
	public static function getHoursByYear($id_maintenance,$year){
		return(Yii::app()->db->createCommand("	select SUM(u.amount) from user_time u, ticket_id t, support_desk s, maintenance m
		where u.`default`=1 and u.id=t.id_user_time and t.ticket_id=s.id and s.id_customer=m.end_customer and s.product=m.product and  YEAR(u.date)=".$year." and  m.id_maintenance=".$id_maintenance." ")->queryScalar());
	}
	public static function getHoursByYearNotStandard($id_maintenance,$year){
		return(Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE `default`=2 and id_task in (SELECT id FROM maintenance_services where id_contract= ".$id_maintenance.") and YEAR(date)=".$year." ")->queryScalar());
	}
	public static function getHoursbydate($id_maintenance,$startstr,$endstr){
		$query="
		select SUM(u.amount)	from user_time u, ticket_id t, support_desk s, maintenance m where u.`default`=1 and u.id=t.id_user_time and t.ticket_id=s.id 
		and s.id_customer=m.end_customer and s.product=m.product and  u.date >=DATE_FORMAT(STR_TO_DATE('".$startstr."', '%d/%m/%Y'), '%Y-%m-%d') 
		and  u.date <=DATE_FORMAT(STR_TO_DATE('".$endstr."', '%d/%m/%Y'), '%Y-%m-%d') and  m.id_maintenance=".$id_maintenance." ";
		return(Yii::app()->db->createCommand($query)->queryScalar());
	}
	public static function getHours($id_maintenance){
		return(Yii::app()->db->createCommand("select SUM(u.amount) from user_time u, ticket_id t, support_desk s, maintenance m
		where u.`default`=1 and u.id=t.id_user_time and t.ticket_id=s.id and s.id_customer=m.end_customer and s.product=m.product and  m.id_maintenance=".$id_maintenance." ")->queryScalar());
	}
	public static function getservices($id){
		$result =  Yii::app()->db->createCommand("SELECT s.id,s.service , m.limit from maintenance_services m, support_services s where m.id_service=s.id and m.id_contract='".$id."' ")->queryAll();
		return $result;
	}
	public static function getSupportServiceDesc($support_services){		
		$result =  Yii::app()->db->createCommand("SELECT codelkup from codelkups where id='".$support_services."' and id_codelist='16' ")->queryScalar();
		return $result;
	}
	public static function getProductDesc($product){		
		$result =  Yii::app()->db->createCommand("SELECT codelkup from codelkups where id='".$product."' and id_codelist='3' ")->queryScalar();
		return $result;
	}
	public static function getCustByMaint($contract){
		$result =  Yii::app()->db->createCommand("SELECT customer from maintenance where id_maintenance=".$contract." ")->queryScalar();
		return $result;
	}
	public static function getPlanByMaint($contract){
		$result =  Yii::app()->db->createCommand("SELECT support_service from maintenance where id_maintenance=".$contract." ")->queryScalar();
		return $result;
	}
	public static function GetCrossingContracts($id){
		$count=0;		
		$services= Yii::app()->db->createCommand("SELECT id ,id_contract, id_service, `limit` FROM `maintenance_services` where field_type=1 and id_contract=".$id." ")->queryAll();
		foreach ($services as $service) {
			$li= MaintenanceServices::getLimit1($service['id'], $service['id_contract'], $service['id_service'], 1, $service['limit']);			
			if($li<0 ){ $count++;}
		} 		
		if($count==0){	return '#000000';}else{ return '#8a1f11'; }
	}
} ?>