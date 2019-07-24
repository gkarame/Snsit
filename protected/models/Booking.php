<?php
class Booking extends CActiveRecord{
	const STATUS_NEW = 0;
	const STATUS_CLOSED = 1;
	
	public $project_name, $customer_name;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'booking';
	}
	public function rules(){
		return array(
			array('traveler, purpose, origin, destination, departure_date, departure_time,return_date,hotel_booking, car_booking , return_time, travel_supplier, billable ', 'required'),
			array('traveler, origin, destination, travel_supplier, status,addwho ', 'numerical', 'integerOnly'=>true),
			array('billable,hotel_booking, car_booking', 'length', 'max'=>3),
			array('departure_time,return_time', 'length', 'max'=>15),
			array('car_supplier,hotel_supplier', 'length', 'max'=>25),
			array('id_customer,id_project,hotelname,carname', 'length', 'max'=>500),
			array('notes, reason, carreason', 'length', 'max'=>1000),
			array('hotel_booking','validatecheckdate'),
			array('car_booking','validatepickdate'),
			array('purpose','validatepurpose'),
			array('return_date','validateDates'),
			array('departure_date, return_date, checkin, checkout, pickup, return', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy'),
			array('id, traveler, destination, id_customer, id_project, billable, status', 'safe', 'on'=>'search'),
		);
	}
	
	public function validateDates(){
	 if(!empty($this->return_date) && !empty($this->departure_date)){
	 	$dep=DateTime::createFromFormat('d/m/Y', $this->departure_date)->format('Y-m-d');
	 	$ret=DateTime::createFromFormat('d/m/Y', $this->return_date)->format('Y-m-d');
	        	if ($ret < $dep )
	        	{
	        		$this->addError('return_date','Return Date must larger than Departure Date.');
	        	}
	        }				
	}
	public function validatepurpose(){
	 if ( $this->purpose == 'Project' || $this->purpose == 'CA' ){
	        if(empty($this->id_customer)){
	        	$this->addError('id_customer','Customer must be specified.');
	        }else if (trim($this->id_customer) == 'SNS INTERNAL,')
	        {
				$this->addError('id_customer','Customer cannot be SNS');
	        }
	        if ( $this->purpose == 'Project')
	       	{ 
	       		if(empty($this->id_project)){
	        		$this->addError('id_project','Project must be specified.');
	        	}
	        }        	
	    }			
	}
	public function validatecheckdate(){
	 if ( $this->hotel_booking == 'Yes' ){
	        if(empty($this->checkin)){
	        	$this->addError('checkin','Check-In Date must be specified.');
	        }
	        if(empty($this->checkout)){
	        	$this->addError('checkout','Check-Out Date must be specified.');
	        }
	        if(!empty($this->checkin) && !empty($this->checkout)){
	        	$dep=DateTime::createFromFormat('d/m/Y', $this->checkin)->format('Y-m-d');
	 			$ret=DateTime::createFromFormat('d/m/Y', $this->checkout)->format('Y-m-d');
	        	if ($ret < $dep )
	        	{
	        		$this->addError('checkout','Check-Out Date must larger than  Check-In Date.');
	        	}
	        }	
	        if(empty($this->hotel_supplier) || ( $this->hotel_supplier == 'Choose another hotel'  && empty($this->hotelname)))  {
	        	$this->addError('hotel_supplier','Hotel Name must be specified.');
	        }
	        if ( $this->hotel_supplier == 'Choose another hotel' && empty($this->reason))
	        {
	        	$this->addError('hotel_supplier','Notes must be specified.');
	        }       	
	    }			
	}
	public function validatepickdate(){
	 if ( $this->car_booking == 'Yes' ){
	        if(empty($this->pickup)){
	        	$this->addError('pickup','Pickup Date must be specified.');
	        }
	        if(empty($this->return)){
	        	$this->addError('return','Return Date must be specified.');
	        }	
	        if(!empty($this->pickup) && !empty($this->return)){
	        	$dep=DateTime::createFromFormat('d/m/Y', $this->pickup)->format('Y-m-d');
	 			$ret=DateTime::createFromFormat('d/m/Y', $this->return)->format('Y-m-d');
	        	if ($ret < $dep )
	        	{
	        		$this->addError('return','Return Date must larger than  Pickup Date.');
	        	}
	        }
	        if(empty($this->car_supplier)){
	        	$this->addError('car_supplier','Car Supplier must be specified.');
	        }   
	        if(empty($this->car_supplier) || ( $this->car_supplier == 'Choose another supplier'  && empty($this->carname)))  {
	        	$this->addError('car_supplier','Car Supplier Name must be specified.');
	        }
	        if ( $this->car_supplier == 'Choose another supplier' && empty($this->carreason))
	        {
	        	$this->addError('car_supplier','Notes must be specified.');
	        }        	
	    }			
	}
	public function relations(){
		return array( );
	}
	public function attributeLabels(){
		return array(
			'id' => 'TR#',
			'traveler' => 'Traveler',
			'purpose' => 'Purpose',
			'id_customer' => 'Customer',
			'id_project' => 'Project',
			'origin' => 'Origin',
			'destination' => 'Destination',
			'departure_date' => 'Departing',
			'departure_time' => 'Departure Time',
			'return_date' => 'Returning',
			'return_time' => 'Return Time',
			'hotel_booking' => 'Hotel Booking',
			'car_booking' => 'Car Booking',
			'billable' => 'Billable',
			'status' => 'Status',
			'travel_supplier'=> 'Travel Supplier',
			'notes' => 'Notes',
			'checkin'=> 'Check-In Date',
			'checkout'=>'Check-Out Date',
			'pickup' => 'Pick-Up Date', 
			'return' => 'Return Date',
			'hotelname' => 'Hotel Name',
			'carname' => 'Car Supplier Name',
			'carreason' => 'Notes',
			'reason'=>'Notes'
		);
	}


	public function search(){
		$criteria=new CDbCriteria;		
		$criteria->compare('t.status',$this->status,false);
		if(isset($this->traveler) && !empty($this->traveler)  )
		{
			$user=Users::getIdByName($this->traveler);
			$criteria->compare('t.traveler',$user,false);
		}
		if(Users::checkAdminTeam(Yii::app()->user->id ) == 0 )
		{
			//$criteria->compare('t.traveler',Yii::app()->user->id,false);
			$criteria->addCondition(" (t.traveler =".Yii::app()->user->id." or ".Yii::app()->user->id." in (select project_manager from projects where projects.id in (t.id_project)))  ");	
		}
		$criteria->compare('t.billable',$this->billable,false);
		//print_r($this->id_project);exit;
		if(isset($this->id_project) && !empty($this->id_project))
		{$criteria->addCondition("t.id_project like '%".$this->id_project."%'");}		
		
		if(isset($this->destination) && !empty($this->destination))			
		{
			$destination=self::getIdByCityName($this->destination);
			$criteria->compare('t.destination',$destination,false);	
		}		
		if(isset($this->id_customer) && !empty($this->id_customer))			
		{
			//$customer=Customers::getIdByName($this->id_customer);
			$criteria->addCondition("t.id_customer like '%".$this->id_customer.",%'");	
		}
		//print_r($criteria);exit;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize' => Utils::getPageSize(),
			),
			'sort'=>array(
				'defaultOrder' => 'id DESC',
			),
		));
	}	
	public function renderTravelBookingNumber()
	{
		echo '<a class="show_link" href="'.Yii::app()->createUrl("booking/view", array("id" => $this->id)).'">'.str_pad($this->id, 5, '0', STR_PAD_LEFT).'</a>';
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList($value);
		return $list[$value];
	}
	public static function getStatusList(){
		return array(
				self::STATUS_NEW => 'New',
				self::STATUS_CLOSED => 'Closed');
	}
	public static function getBillableList(){
		return array('Yes'=>'Yes','No'=>'No');
	}
	public static function getPurposeList(){
		return array('Project'=>'Project','BD/Sales'=>'BD/Sales','CA'=>'CA','Exhibition'=>'Exhibition');
	}
	public static function getTimeList(){
		return array('Morning'=>'Morning','Noon'=>'Noon','Afternoon'=>'Afternoon','Evening'=>'Evening','Flexible'=>'Flexible');
	}
	public static function getRadioBut(){
		return array('Yes' => "Yes",
			"No" => "No",	);
	}
	public static function getNamebyIds($value){
		$ids= explode(',', $value);
		$customers='';
		foreach ($ids as $key => $id) {		if($id!=0){		$customers.= Customers::getNamebyId($id).','; }		}
		$customers=substr($customers, 0, (strlen($customers)-1));
		return $customers;
	}
	public static function getProjectByIds($value){
		$ids= explode(',', $value);
		$projects='';
		foreach ($ids as $key => $id) {		if($id!=0){		$projects.= Projects::getNamebyId($id).', '; }		}
		$projects=substr($projects, 0, (strlen($projects)-2));
		return $projects;
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

	public static function getAllCitiesAutocomplete(){
		$query = "select id, codelkup from `codelkups` where id_codelist =36 order by codelkup asc;";
		$result =  Yii::app()->db->createCommand($query)->queryAll();	$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['codelkup'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}

	public static function getIdByCityName($city){
		$query = "select id from `codelkups` where codelkup ='".$city."' and id_codelist =36 LIMIT 1;";
		return Yii::app()->db->createCommand($query)->queryScalar();
	}

}