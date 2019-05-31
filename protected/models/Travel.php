<?php
class Travel extends CActiveRecord{
	const STATUS_NEW = 0;	const STATUS_INVOICED = 1;	const STATUS_CLOSED = 2;	public $project_name, $customer_name;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'travel';
	}
	public function rules(){
		return array(
			array('travel_cod, id_user, id_customer, id_project, expense_type, amount, currency, billable, date', 'required'),
			array('id_user, id_customer, id_project, expense_type, currency, status', 'numerical', 'integerOnly'=>true),
			array('amount,training', 'numerical'),
			array('travel_cod', 'length', 'max'=>5),
			array('billable', 'length', 'max'=>3),
			array('id, travel_cod, id_user, id_customer, id_project, expense_type, amount, currency, billable, status, date, project_name, customer_name', 'safe', 'on'=>'search'),
			array('project_name, customer_name,file', 'safe'),
		);
	}
	public function relations(){
		return array(
			'currencyType' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'idCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'idProject' => array(self::BELONGS_TO, 'Projects', 'id_project'),
			'expenseType' => array(self::BELONGS_TO, 'Codelkups', 'expense_type'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'travel_cod' => 'Travel Code',
			'id_user' => 'User',
			'id_customer' => 'Customer',
			'id_project' => 'Project/Training',
			'expense_type' => 'Expense Type',
			'amount' => 'Amount',
			'currency' => 'Currency',
			'billable' => 'Billable',
			'status' => 'Status',
			'date' => 'Date',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->with = array('idCustomer', 'idProject', 'expenseType');
		$criteria->together = true;
		$criteria->compare('idCustomer.name',$this->customer_name,true);
		if (substr($this->id_project, -1) == 't') {
			$id_project= substr($this->id_project, 0, -1);
			$criteria->compare('t.training','1',true);	
		}else{
			$id_project=$this->id_project;
		}
		$criteria->compare('idProject.id',$id_project, true);
		$criteria->compare('t.status',$this->status,true);	
		$criteria->compare('t.expense_type',$this->expense_type,true);	
		if(!empty($this->date))
		{
			$datef= str_replace("/","-",$this->date);
			$datef=date('Y-m-d', strtotime($datef));
			$criteria->compare('t.date',$datef,true);
		}
		
		if (isset($this->id_user) && $this->id_user != ''){
			$last = explode("  ", $this->id_user);	$criteria->join='LEFT JOIN users ON users.id=t.id_user';
			$criteria->addCondition('users.firstname LIKE :tn');	$criteria->params[':tn']='%'.substr($this->id_user,0,strrpos($this->id_user,"  ")).'%';
			$criteria->addCondition('users.lastname LIKE :tr');		$criteria->params[':tr']='%'.$last[1].'%';
		}
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize' => 50,
			),
			'sort'=>array(
				'defaultOrder' => 'idCustomer.name ASC',
				'attributes' => array(
					'idCustomer.name'=>array(
						'asc'=>'idCustomer.name',
						'desc'=>'idCustomer.name DESC',
					),
					'idProject.name'=>array(
						'asc'=>'idProject.name',
						'desc'=>'idProject.name DESC',
					),
					'idUser.name'=>array(
						'asc'=>'idUser.name',
						'desc'=>'idUser.name DESC',
					),
					'expenseType.codelkup'=>array(
						'asc'=>'expenseType.codelkup',
						'desc'=>'expenseType.codelkup DESC',
					),
					'*',
				),
			),
		));
	}
	public static function getCustomersAutocomplete(){		
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN eas ON eas.id_customer=customers.id order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['name'];		$customers[$i]['id'] = $res['id'];	}
		return $customers;
	}
	public function getFilename(){
		$path = $this->getFile(true);
		if ($path != NULL){	return pathinfo($path, PATHINFO_BASENAME);	}
		return NULL;
	}
	public static function getDirPath($customer_id, $model_id){
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."travel".DIRECTORY_SEPARATOR.$model_id.DIRECTORY_SEPARATOR;
	 	if (!is_dir( $path)){    mkdir( $path, 0777, true);   chmod( $path, 0777 ); }
		return $path; 
	}	
	public static function getStatusforApproval($id_travel,$status){ 	
	$result= array('0'=>'New','1'=>'Invoiced','2'=>'Closed');
			return CHtml::dropDownlist('status', $status, $result, array(
		        'class'     => 'status',
		    	'onchange'=>'changeInput('."value".','. $id_travel.')',
		    	'style'=>'width:190px;border:none;'
		    ));
	}
	public function renderTravelExpenseNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("travel/view", array("id" => $this->id)).'">'.$this->travel_cod.'</a>';
	}
	public static function getAllStatus(){	return array('0'=>'New','1'=>'Invoiced','2'=>'Cancelled');	}
	public static function getStatusLabel($value){
		$list = self::getStatusList($value);
		return $list[$value];
	}
	public static function getStatusList($currentStatus = null){
		switch ($currentStatus){
			case self::STATUS_NEW:
				$statuses = array(
				self::STATUS_NEW => 'New',
				);
				break;
			case self::STATUS_INVOICED:
				$statuses = array(
				self::STATUS_INVOICED => 'Invoiced',
				);
				break;
			case self::STATUS_CLOSED:
				$statuses = array(
				self::STATUS_CLOSED => 'Closed',
				);
				break;
			default:
				$statuses = array(
				self::STATUS_NEW => 'New',
				self::STATUS_INVOICED => 'Invoiced',
				self::STATUS_CLOSED => 'Closed',
					
				);
				break;
		}
		return $statuses;
	}
	public static function getStatusNameByStatus($status){
		if ($status == Travel::STATUS_NEW){
			echo Yii::t('translations', 'New');
		}else if ($status == Travel::STATUS_INVOICED){
			echo Yii::t('translations', 'Invoiced');
		}else if ($status == Travel::STATUS_CLOSED){
			echo Yii::t('translations', 'Closed');
		}		
		return;
	}
	public static function  getExpenseTypesSelect(){
		return CHtml::listData(Codelkups::model()->findAllBySql('SELECT codelkups.id, codelkups.codelkup from codelkups JOIN codelists ON codelists.id = codelkups.id_codelist AND codelists.id_category = 16 and codelkups.id < 300'),
				'id', 'codelkup');
	}	
	public static function getAllExpenseTypesSelect(){
		return CHtml::listData(Codelkups::model()->findAllBySql('SELECT codelkups.id, codelkups.codelkup from codelkups JOIN codelists ON codelists.id = codelkups.id_codelist AND codelists.id_category = 16'),
				'id', 'codelkup');
	}	
	public static function getBillabledSelect(){	return array('yes' => 'Yes', 'no' => 'No');	}	
	public static function getStatusSelect(){
		return array(
			self::STATUS_NEW => 'New',
			self::STATUS_INVOICED => 'Invoiced',
			self::STATUS_CLOSED=> 'Closed',
		);
	}
	public static function getTravel($id_project,$id_customer,$final_number){
		$new_status = 0;	$groups = array();
		if	(!empty($id_project) && $id_project!=0 && !empty($id_customer) && $id_customer!=0)	{
			$val = Yii::app()->db->createCommand("SELECT * FROM travel WHERE id_customer = $id_customer AND id_project = $id_project AND billable = 'yes' AND status = $new_status ")->queryAll();
			if (!empty($val)){
				foreach ($val as $row){	$groups[$row['id_user']][$row['id']] = $row;	}
				foreach($groups as $id_res => $group){ 		
					$ids[]= self::getTravelExpensesInvoices($group,$id_customer,$id_project,$id_res,$final_number);
				}			
				if (!empty($ids)){ 	return $ids; }else {	return false; }
			}
		}
		return false;
	}
	public static function getTravelExpensesInvoices($group,$id_customer,$id_project,$id_res,$final_number) {
		$val = Yii::app()->db->createCommand("SELECT id FROM invoices WHERE id_customer = $id_customer AND id_project = $id_project AND id_resource=$id_res AND status = 'To Print' and type='Travel Expenses' ")->queryAll();
		foreach ($val  as $key => $value) {
			$invoice_number=Invoices::getInvNumberById($value['id']);	$invoice = Invoices::model()->findByPk($value['id']);						
			self::changeStatusInv($group,$invoice_number,$final_number);
		}			
		return $val;
	}
	public static function createInvoice($travel){
		$id_customer=$travel->id_customer;	$id_project=$travel->id_project;	$id_res=$travel->id_user;
		$invoice = new Invoices();	$invoice->invoice_number = "00000";	$invoice->id_customer = $id_customer; 
		$assigneduser= Yii::app()->db->createCommand("SELECT id_assigned from customers where id= ".$id_customer." ")->queryScalar();
			if ( !empty($assigneduser) && $assigneduser!= 0){
				$invoice->id_assigned=$assigneduser;
				$updateinvoices = Yii::app()->db->createCommand("UPDATE `invoices` SET id_assigned='".$assigneduser."' WHERE id_customer=".$id_customer." and (id_assigned is null or id_assigned=0) ")->execute();
			}
		if($travel->training == '1'){
			$title= Trainings::getName($travel->id_project);
			$id_ea = Eas::getIdCurrencyTraining($id_customer,Trainings::getEA($travel->id_project));
		}else{
			$invoice->project_name = Projects::getNameById($id_project);	$invoice->id_project = $id_project;
			$id_ea = Eas::getIdCurrency($id_customer,$id_project);
		}
		if($invoice->project_name != null){
			$title = Users::getUsername($id_res)." - Airfare/Visa and Travel Insurance Expenses";
		}
		$invoice->invoice_title = $title;	
		$invoice->id_ea = $id_ea['id'];	$invoice->currency = $travel->currency;
		$invoice->status = "To Print";	$invoice->payment =0;
		$invoice->payment_procente= 0;	

		if(!empty($invoice->id_ea))
		{
			$cat= Yii::app()->db->createCommand("SELECT category FROM eas WHERE id=".$invoice->id_ea )->queryScalar();
		}else{
			$cat='';
		}
			$region=Yii::app()->db->createCommand("SELECT region, country from customers where id=".$invoice->id_customer." ")->queryRow(); 
			if($region['region'] =='59'){
				if($cat =='27' || $cat =='28'){
						if($region['country'] =='113' || $region['country']=='115'){
								$invoice->partner= Maintenance::PARTNER_SNS;
								$invoice->sns_share = 100;	
						}else {
								 $invoice->partner= '79' ; 
								 $invoice->sns_share = 80;
								 $invoice->partner_status='Not Paid';
								  }
			 }	else{
				 	$invoice->partner= Maintenance::PARTNER_SNS;
				 	$invoice->sns_share = 100;
				 }
			}elseif($region['region'] =='63') {
				{
					$invoice->partner= '201'; 
				}
				$invoice->sns_share = 80; 
				$invoice->partner_status='Not Paid';
			}else{
				$invoice->partner= Maintenance::PARTNER_SNSI;
				$invoice->sns_share = 80;
				$invoice->partner_status='Not Paid';
			}
$invoice->sns_share = 100;

		$invoice->invoice_date_month = date('m');
		$invoice->invoice_date_year = date('Y');	$amount = self::getAmount($travel->amount,$travel->currency,$invoice->currency);		
		$invoice->amount = $amount;	$invoice->net_amount = $amount;	$invoice->gross_amount = $amount;
		$invoice->sold_by = ""; $invoice->type = "Travel Expenses";	$invoice->id_resource = $id_res;
		if($invoice->save()){
			$invoice->invoice_number = Utils::paddingCode($invoice->id);
			if($invoice->invoice_number == "99999")
			{	$invoice->invoice_number = "00000";}
			
			self::changeStatusInvOne($travel->id,$invoice->invoice_number);
			if($invoice->save())
			{	return $invoice->id;	}
		}
	}
	public static function getAmount($amount,$travelcurrency,$currency){
			$billable = $amount;	$rate = CurrencyRate::getCurrencyRate($travelcurrency);
			if($travelcurrency!= $currency){
				if (isset($rate['rate'])){	$billable = $billable * $rate['rate'];	}
			}
		return $billable;
	}
	public static function changeStatusInv($id,$inv_no,$final_number){ 		
		$ids=array_keys($id);	$travel_ids=" ";
			if(count($ids==1)){
				$travel_ids="'".$ids."'"; 
			}else{
			foreach ($ids as $key=>$value) {
				 $travel_ids.="'".$value[$key]."' ,"; 
			}
			}
			$status_inv = 1;
			Yii::app()->db->createCommand("UPDATE travel SET  inv_number = '$inv_no' ,  final_inv_number = '$final_number' WHERE id in ($travel_ids)")->execute();
	
	}
	public static function changeStatusInvoicedInfo($id,$final){  
		$status = 1;
		Yii::app()->db->createCommand("UPDATE travel SET status = '$status', final_inv_number= '".$final."'  WHERE id = $id ")->execute();
	}
	public static function changeStatusInvOne($id,$inv_no){  
			$status_inv = 1;	Yii::app()->db->createCommand("UPDATE travel SET  inv_number = '$inv_no' WHERE id='$id' ")->execute();	
	}
	public static function addFinalInvNo($final_number,$ids){		
		if(!empty($ids)){
			foreach ($ids as $id){	$_id[] = $id['id']; }
			$ids = implode(',',$_id);
			$nr = Yii::app()->db->createCommand("UPDATE travel SET final_inv_number = '$final_number' WHERE id IN ($ids)")->execute();
		}
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users order by  users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public function getFile($path = false, $uploaded = false){		
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."travel". DIRECTORY_SEPARATOR .$this->id. DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'travel'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {	$filePath .= $this->file;	$fileName .= $this->file;
			}else{	return null;	}
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
} ?>