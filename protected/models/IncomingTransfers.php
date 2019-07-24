<?php 
class IncomingTransfers extends CActiveRecord {
	CONST STATUS_NEW = 1;	CONST STATUS_CLOSED = 2;	CONST STATUS_CANCELED = 3;

	public $customErrors = array();	public $customer_name,$project_name,$product_id=null;

	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'incoming_transfers';
	}
	public function rules(){
return array(
		array('it_no, partner, received_amount,bank,currency,status,offsetting,id_user,bank_dolphin,aux,id_customer', 'required'),
		array('partner ,currency,status,offsetting,id_user,bank_dolphin,aux, month', 'numerical', 'integerOnly'=>true),
		array('bank,received_amount', 'numerical', 'integerOnly'=>false),
		//array('id_customer', 'exist', 'attributeName' => 'id', 'className' => 'Customers','allowEmpty'=>false),
		array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers','allowEmpty'=>true),
		array('id_user', 'exist', 'attributeName' => 'id', 'className' => 'Users','allowEmpty'=>true),
		array('notes,id_customer, remarks','length','max'=>2000),
		array('status','validateStatus'),
		array('currency','validateCurrency'),
		array('it_no,partner,id_customer, currency, offsetting, status,id_user,bank_dolphin,aux', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			//'eCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'iCurrency' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'iPartner' => array(self::BELONGS_TO, 'Codelkups', 'partner'),
			'eInvoices' => array(self::HAS_MANY, 'IncomingTransfersDetails', 'id'),
			);
	}
	public static function getMonths(){ 
		$monthNames = array('','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');		
		for ($i=1;$i<=12;$i++){	$months["{$i}"]=Yii::t('default',$monthNames[$i]);  }
		return $months;
	}	
	public static function getMonthName($value){
		if(!empty($value) && $value !=0)
		{
			$list = self::getMonths($value);
			return $list[$value];
		}else{return '';}
	}
	public function validateStatus(){
	 if ( $this->status == 2 ){
	 		if(!IncomingTransfers::matchtotal($this->id))
	 		{
 				$this->addError('status','Received Amt doesnt match invoices total');
	 		}
		 		$inv= Yii::app()->db->createCommand("select id.final_invoice_number as final from incoming_transfers_details id
						where   id_it=".$this->id." and 
						(
								(paid_amount=1 and id.final_invoice_number in (select final_invoice_number from incoming_transfers_details where final_invoice_number =id.final_invoice_number and invoice_number=id.invoice_number and received_amount>0 and id_it !=".$this->id." and id_it in (select id from incoming_transfers where status = 2)))
								or 
								(paid_amount=2 and id.final_invoice_number in (select final_invoice_number from incoming_transfers_details where final_invoice_number =id.final_invoice_number and invoice_number=id.invoice_number and paid_amount=1 and id_it !=".$this->id." and id_it in (select id from incoming_transfers where status = 2)))
						)")->queryAll();
		 		if(!empty($inv))
		 		{
		 			$num= array_unique(array_column($inv , 'final'));
		 			$str= implode(', ', $num);
		 			if(count($num)>1)
		 			{
		 				$this->addError('status','Invoices '.$str.' already paid on another TR#');
		 			}else{
		 				$this->addError('status','Invoice '.$str.' already paid on another TR#');
		 			}
		 			
		 		}

	    }			
	}
	public function validateCurrency(){
		if(!empty($this->id))
		{
			$tr_currency= Yii::app()->db->createCommand("select currency from incoming_transfers where id =".$this->id)->queryScalar();
			if ( $this->currency != $tr_currency ){
				$invs= Yii::app()->db->createCommand("select count(1) from incoming_transfers_details where id_it =".$this->id."")->queryScalar();
		 		if($invs!=0)
		 		{
	 				$this->addError('currency','Currency cannot be modified when invoices are assigned');
		 		}
		    }			
		}
	}
	public static function getAllAuxiliariesperbank($code){
		if ($code){
			$cond='';
			switch ($code){
				//BLOM BANK sal
				case '1035':
					$cond=' AND id in (1066, 1051,1052,1053,1054,1056,1592,1066)';
					break;
				case '1037':
				//BOB
					$cond=' AND id in (1058, 1059, 1060, 1062,1061, 1063, 1064, 1065,1593,1594)'; 
					break;
			}
			$criteria = new CDbCriteria(array(
            'select' => 'id, codelkup',
            'condition' =>'id_codelist= :value '.$cond,
			'params' => array(':value' => '34'), 
            'order'=>'codelkup ASC',
        ));
        return CHtml::listData(Codelkups::model()->findAll($criteria), 'id','codelkup');

		}else{
			return  Codelkups::getCodelkupsDropDown('aux_code');	
		}	
	}
	public static function getName($value){
		$ids= explode(',', $value); $str='';
		foreach ($ids as $key) {
			if(trim($key)!='')
			{
				$str.= trim($key).', ';
			}
		}
		$customers=substr($value, 0, (strlen($str)-2));
		return $customers;
	}
	public static function matchtotal($tr){
		/*$received_amount =  Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.received_amount 
			else r.received_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC,id DESC   limit 1) end as received_amount
			from incoming_transfers r where r.id=".$tr)->queryScalar();
		$total =  Yii::app()->db->createCommand("select 
			SUM(r.received_amount*(select c.rate from currency_rate c where c.currency=r.received_currency  order by date DESC ,id DESC  limit 1))
			from incoming_transfers_details r where r.id_it = ".$tr)->queryScalar();*/
		$received_amount =  Yii::app()->db->createCommand("select r.received_amount as received_amount	from incoming_transfers r where r.id=".$tr)->queryScalar();
		$total =  Yii::app()->db->createCommand("select 	SUM(r.received_amount)		from incoming_transfers_details r where r.id_it = ".$tr)->queryScalar();

		$received_amount = round($received_amount);
		$total = round($total);
		if($received_amount != $total)
		{
			return false;
		}else{
			return true;
		}
	}
	public function attributelabels(){
		return array(
			'it_no' => 'TR#',
			'id_customer' => 'Customer',
			'partner' => 'Partner',
			'notes' => 'Notes',
			'remarks' => 'Remarks',
			'adddate' => 'Date',
			'received_amount' => 'Amount',
			'bank'=> 'Bank Charges($)',
			'id_user' => 'Created By',
			'status' => 'Status',
			'currency' => 'Currency',
			'offsetting' => 'Offsetting',
			'bank_dolphin' =>'Bank',
			'aux'  =>'Auxiliary',
		);
	}
	public function search(){
		$criteria= new CDbCriteria;
		$criteria->select  = "t.*";
		if ($this->id_user){ 
			
			$user_id= Users::getIdByName($this->id_user);
			$criteria->addCondition('(t.id_user = :id_user)');		$criteria->params[':id_user'] = $user_id;
		}
		if($this->it_no)
		{
			$criteria->compare('id', $this->it_no);
		}
		if($this->customer_name){
			$cut_id= Customers::getIdByName($this->customer_name);
			$criteria->addCondition(' ( :customer in (select id_customer from incoming_transfers_details where id_it= t.id) )');		$criteria->params[':customer'] = $cut_id;
		}
		if($this->offsetting){
			$criteria->addCondition('(t.offsetting = :offsetting)');		$criteria->params[':offsetting'] = $this->offsetting;
		}
		if($this->status){
			$criteria->addCondition('(t.status = :status)');		$criteria->params[':status'] = $this->status;
		}
		if($this->partner){
			$criteria->addCondition('(t.partner = :partner)');		$criteria->params[':partner'] = $this->partner;
		}
		if($this->currency){
			$criteria->addCondition('(t.currency = :currency)');		$criteria->params[':currency'] = $this->currency;
		}
	/*	if($this->product_id){
			$criteria->addCondition('(eProducts.id_product = :product)');		$criteria->params[':product'] = $this->product_id;
		}		*/
		//print_r($criteria);exit;
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination'=>array(
            'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		         'attributes'=>array(
		         	'eUser.fullname'=>array(
		                'asc'=>'eUser.firstname',
		                'desc'=>'eUser.firstname DESC',
		            ),
	         		'customer.name'=>array(
	         				'asc'=>'customer.name',
	         				'desc'=>'customer.name DESC',
	         		),
	         		
		            '*',
		        ),
		    ),
		));
	}
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);  
    }
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }

	public static function getInvoicesProvider($tr){
		$criteria=new CDbCriteria;	
		$criteria->condition = "(id in (Select id from incoming_transfers_details where id_it =".$tr."))";	
		return new CActiveDataProvider('IncomingTransfersDetails', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
	public static function getPaid(){
		return "<select class=\"assigned_to\" style=\"width:40px;border:none;\" name=\"assigned_to\" id=\"assigned_to\">
		<option value=\"\"></option>
		<option value=\"1\">Fully</option>
		<option value=\"2\">Partial</option>
		</select>";
	}
	public static function getInvoicesPopup($tr)
	{
		return Yii::app()->db->createCommand("SELECT i.invoice_number, i.final_invoice_number FROM receivables i , incoming_transfers tr where tr.id=".$tr." and i.old = 'No' and (( tr.partner=i.partner and tr.partner!=77) or ( tr.partner=77 and tr.id_customer = i.id_customer)) and i.status!='Paid' and i.invoice_number not in 
		(select invoice_number from incoming_transfers_details where id_it=".$tr." ) ")->queryAll();
	}
	public static function getInvoicesPerTR($tr, $customernames, $partner, $month){
		$where=''; $wheredate='';
		$custid='';

		$ids= explode(',', $customernames); 
		foreach ($ids as $key) {
			if(trim($key)!='')
			{
				$custid.= Customers::getIdByName(trim($key)).', ';
			}
		}
		if(!empty($custid))
		{
			$custid= ''.$custid.'0';
		}
		if(isset($month) && !empty($month) && $month != 0)
		{
			$wheredate =" AND MONTH(paid_date) = ".$month." ";
		}
		$selects= Yii::app()->db->createCommand("SELECT i.invoice_number, i.final_invoice_number, i.net_amount,  i.currency FROM receivables i , incoming_transfers tr where tr.id=".$tr." and i.old = 'No' and i.partner= ". $partner." and 
			(	( tr.partner=i.partner and tr.partner!=77 and i.status!='Paid' and i.partner_status='Paid' ".$wheredate.") or ( tr.partner=77  and i.status!='Paid' and i.id_customer in (".$custid.") )) and i.invoice_number not in (select invoice_number from incoming_transfers_details where id_it=".$tr." ) ")->queryAll();
		return $selects;		
	}

	public static function getInvoices($tr, $invoice, $customernames, $partner, $month){
		$where=''; $wheredate='';
		$custid='';

		$ids= explode(',', $customernames); 
		foreach ($ids as $key) {
			if(trim($key)!='')
			{
				$custid.= Customers::getIdByName(trim($key)).', ';
			}
		}
		if(!empty($custid))
		{
			$custid= ''.$custid.'0';
		}


		if(isset($invoice) && !empty($invoice))
		{
			$where =" AND invoice_number != '".$invoice."' ";
		}
		if(isset($month) && !empty($month) && $month != 0)
		{
			$wheredate =" AND MONTH(paid_date) = ".$month." ";
		}
		$selects= Yii::app()->db->createCommand("SELECT i.invoice_number, i.final_invoice_number FROM receivables i , incoming_transfers tr where tr.id=".$tr." and i.old = 'No' and i.partner= ". $partner." and 
			(	( tr.partner=i.partner and tr.partner!=77 and i.status!='Paid' and i.partner_status='Paid' ".$wheredate.") or ( tr.partner=77  and i.status!='Paid' and i.id_customer in (".$custid.") )) and i.invoice_number not in (select invoice_number from incoming_transfers_details where id_it=".$tr." ".$where.") ")->queryAll();
		$invoices = array();
		foreach ($selects as $i=>$res){
			$invoices[$res['final_invoice_number']] = $res['final_invoice_number'];
		}
		return $invoices;		
	}
	public function isEditable(){
		return !in_array($this->status, array(IncomingTransfers::STATUS_CLOSED,IncomingTransfers::STATUS_CANCELED));
	}

	
	public static function getStatusList(){
		return  array(
					self::STATUS_NEW => 'New',
					self::STATUS_CLOSED => 'Closed',
					self::STATUS_CANCELED => 'Cancelled');
	}

	public static function getStatusLabel($value){
		$list = self::getStatusList($value);
		return $list[$value];
	}
	public function renderRequestNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("IncomingTransfers/update", array("id" => $this->id)).'">'.$this->it_no.'</a>';
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers where id in ( select id_customer from incoming_transfers_details) order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){
			$customers[$i]['label'] = $res['name'];
			$customers[$i]['id'] = $res['id'];
		}
		return $customers;
	}

	public static function getCustomers($tr)
	{

		$result =  Yii::app()->db->createCommand('SELECT name FROM customers where id in ( select id_customer from incoming_transfers_details where id_it= '.$tr.') order by name')->queryAll();
		//print_r($result);exit;
		$str= implode(', ', array_column($result, 'name'));
		return $str;
	}

	public static function getCreatedUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN incoming_transfers ON users.id=incoming_transfers.id_user  order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){
			$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	} 


	public static function getOffsettingList(){
		return array(
			1 => 'No',
			2 => 'Yes'
		);
	}
	public static function getOffsettingLabel($id){
			$list = self::getOffsettingList();
			return $list[$id];
	}


	public function getAll($group = NULL){
		$criteria = new CDbCriteria;
		$criteria->select = array(
				"t.*"	
		);
		$dataProvider = new CActiveDataProvider('IncomingTransfers', array(
				'criteria' => $criteria,
				'pagination'=>array(
					'pageSize'=>10000, // or another reasonable high value...
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => 'id ASC',
           		 ),
		));
		return $dataProvider;
	}
}
?>