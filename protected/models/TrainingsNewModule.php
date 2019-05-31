<?php
class TrainingsNewModule extends CActiveRecord{
	const STATUS_NEW = 1;	const STATUS_IN_PROCESS = 2;	const STATUS_APPROVED = 3;	const STATUS_CANCELLED = 0;
	const STATUS_POSTPONED = 4;	const STATUS_FOC = 5;	const STATUS_DONE = 6;
	const TYPE_PUBLIC  =639;	const TYPE_PRIVATE =640;	const TYPE_PARTNER =641;
	const EMAIL_STATUS_NOTHING_SENT = 0;	const EMAIL_STATUS_COMM_MANAGER = 1;	const EMAIL_STATUS_SNS_TRAVEL =2;
	const EMAIL_STATUS_SNS_SENIOR_ADMIN_CEO =3;	const EMAIL_STATUS_SENIOR_ADMIN_REMINDER =4;	const EMAIL_STATUS_PARTICIPANTS_REMINDER = 9;
    const EMAIL_TRAINING_APPROVED =6;    const EMAIL_TRAINING_CANCELLED =7;    const EMAIL_TRAINING_COMPLETED = 8;
    const EMAIL_TRAINING_COMPLETED_INSTRUCTOR = 10;	public $customErrors = array();
	public $course_name,$customer_name,$partner_name,$course_name_cdlkp ;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'trainings_new_module';
	}
	public function rules(){
		return array(
			array('course_name, start_date, end_date, location ,country, city, type, status, instructor, year', 'required'),
			array('course_name ,status,country, type, instructor, year,survey_score', 'numerical', 'integerOnly'=>true),
			array('course_name', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups','allowEmpty'=>false),
			array('course_name_cdlkp', 'exist', 'attributeName' => 'codelkup', 'className' => 'Codelkups','allowEmpty'=>true),
			array('md_rate','numerical'),
			array('man_days', 'numerical', 'min'=>0.01),
			array('instructor', 'exist', 'attributeName' => 'id', 'className' => 'Users','allowEmpty'=>false),
			array('country', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups', 'allowEmpty'=>false),
			array('type', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups', 'allowEmpty'=>false),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers','allowEmpty'=>true),
			array('customer', 'exist', 'attributeName' => 'id', 'className' => 'Customers','allowEmpty'=>true),
			array('partner_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers','allowEmpty'=>true),
			array('partner', 'exist', 'attributeName' => 'id', 'className' => 'Customers','allowEmpty'=>true),
			array('location', 'length', 'max'=>255),
			array('start_date, end_date', 'safe'),
			array('status','validateMDs'),
			array('idTrainings,training_number, course_name, customer, instructor, city, status,country, type, partner, year', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eInstructor' => array(self::BELONGS_TO, 'Users', 'instructor'),
			'eCustomer' => array(self::BELONGS_TO, 'Customers', 'customer'),
			'ePartner' => array(self::BELONGS_TO,'Customers','partner'),
			'eCountry' => array(self::BELONGS_TO, 'Codelkups', 'country'),	
			'eCourse' => array(self::BELONGS_TO, 'Codelkups','course_name'),	
			'eType' => array(self::BELONGS_TO,'Codelkups','type'),
		);
	}	
	public function validateMDs(){
	 if ( $this->type == 640 ){
	        if(empty($this->md_rate)){
	         	$this->addError('md_rate','Rate must be specified.');
	        }	   
	        if(empty($this->man_days)){
	         	$this->addError('man_days','MDs must be specified.');
	        }	       	
	    }			
	}
	public static function getItemsLabels(){
		$labels['course_name'] = 'Course';		$labels['training_number'] = 'Training #';
    	$labels['start_date'] = 'Start Date';    	$labels['end_date'] = 'End Date';
    	$labels['city'] = 'City';    	$labels['country'] = 'Country';
    	$labels['customer'] = 'Customer';    	$labels['partner'] = 'Partner';
    	$labels['type'] = 'Type';    	$labels['location'] = 'Location';    	$labels['min_participants'] = 'Minimum Number of Participants';
    	$labels['confirmed_participants'] = 'Confirmed Number of Participants';    	$labels['cost_per_participant'] = 'Price per Participant';
    	$labels['revenues'] = 'Revenues';    	$labels['instructor'] = 'Instructor';    	$labels['year'] = 'Year';
    	$labels['profit'] = 'Profit';    	$labels['survey_score'] = 'Survey Score';    	$labels['cost'] = 'Cost';
    	return $labels;
	}
	public static function getItemLabel($label){
		$labels = self::getItemsLabels();		return $labels[$label];
	}
	public function attributelabels(){
		return array(
			'idTrainings' => 'ID',
			'training_number' => 'Training #',
			'course_name' => 'Course',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'city' => 'City',
			'country' => 'Country',
			'customer' => 'Customer',
			'partner' => 'Partner',
			'type' => 'Type',
			'location' => 'Location',
			'min_participants' => 'Minimum Participants',
			'confirmed_participants' => 'Confirmed Participants',
			'cost_per_participant' => 'Price per Participant',
			'revenues' => 'Revenues',
			'instructor' => 'Instructor',
			'year'=>'Year',
			'survey_score' => 'Survey Score',
			'md_rate'=> 'MD Rate',
			'man_days' =>'Man Days'
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->select  = "t.*";
		$criteria->with = array('eInstructor','eCountry', 'eCourse','eCustomer','ePartner');		$criteria->together = true;
		if ($this->instructor){ 
			$split = explode(' ', $this->instructor, 2);
			if (count($split) == 2)	{
				$criteria->addCondition('(eInstructor.firstname = :author1 AND eInstructor.lastname = :author2) OR (eInstructor.firstname = :author2 AND eInstructor.lastname = :author1)');
				$criteria->params[':author1'] = $split[0];		$criteria->params[':author2'] = $split[1];
			}else{
				$criteria->addCondition('eInstructor.firstname = :author OR eInstructor.lastname = :author');
				$criteria->params[':author'] = $this->instructor;
			} 
		}
		if(($this->customer))
		{
		 $criteria->compare('t.customer', $this->customer, true);	
		//	$criteria->addCondition(" t.idTrainings in (select id_training from training_eas where id_ea in (select id from eas where id_customer= ".$this->customer." ))");
		}
		//$criteria->compare('customer.name', $this->customer_name, true);		$criteria->compare('customer.name', $this->partner_name, true);
		$criteria->compare('t.training_number', $this->training_number, true);		$criteria->compare('eCourse.codelkup', $this->course_name_cdlkp, true);
		$criteria->compare('t.city', $this->city, true);		$criteria->compare('t.country', $this->country, true);
		$criteria->compare('t.type', $this->type, true);		$criteria->compare('t.course_name',$this->course_name, true);
		$criteria->compare('t.status', $this->status, true);		$criteria->compare('t.year', $this->year, true);
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.idTrainings DESC',            
		         'attributes'=>array(
		            'eInstructor.fullname'=>array(
		                'asc'=>'eInstructor.firstname',
		                'desc'=>'eInstructor.firstname DESC',
		            ),
		            'eCountry.codelkup'=>array(
		                'asc'=>'eCountry.codelkup',
		                'desc'=>'eCountry.codelkup DESC',
		            ),
		            'eCourse.codelkup'=>array(
		                'asc'=>'eCourse.codelkup',
		                'desc'=>'eCourse.codelkup DESC',
		            ),
	         		'customer.name'=>array(
	         				'asc'=>'customer.name',
	         				'desc'=>'customengr.name DESC',
	         		),
		            '*',
		        ),
		    ),
		));
	}
	public static function getTrainingsnumbers(){
		$criteria = new CDbCriteria;       	$criteria->select = "t.idTrainings, t.training_number";
        return CHtml::listData(self::model()->findAll($criteria), 'idTrainings','training_number');
	}
	public static function getYears(){
		$start_year = date('Y',strtotime('now - 10 year'));		$end_year = date('Y',strtotime('now + 10 year'));
		
		for ($i=$start_year;$i<=$end_year;$i++){   	$years["{$i}"]="{$i}";	}		
		return $years;                   
	}	
	public static function getCertifiedUsers($id)
	{
		return Yii::app()->db->createCommand(" select count(1) from user_personal_details  up , user_groups ug where ug.id_user = up.id_user 
												and ug.id_group in (11,14,27) and up.id_user = ".$id." ")->queryScalar();
		
	}
	public static function getSeatsCustomers(){
		$results = Yii::app()->db->createCommand("select m.customer, m.support_service, `limit`, ms.id from maintenance m, maintenance_services ms where  m.status='Active' and m.id_maintenance= ms.id_contract and m.support_service in (501,502) and id_service=9 and field_type=1")->queryAll();
		if (!empty($results)){
			foreach ($results as $key=>$result) {
				$actuals= MaintenanceServices::getActual($result['id'], 9, 1);
				if($actuals>=$result['limit']){		unset($results[$key]);	}
			}
			return $results;
		}else{	return null;	}
	}
	public static function getTypesList(){
		return(array(
			self::TYPE_PARTNER => 'Partner',
			self::TYPE_PRIVATE => 'Private',
			self::TYPE_PUBLIC => 'Public',
			));
	}
	public static function getStatusList($currentStatus = null){
		switch ($currentStatus){
			case self::STATUS_NEW:
				$statuses = array(
					self::STATUS_NEW => 'New',
					self::STATUS_APPROVED => 'Approved',					
					self::STATUS_CANCELLED => 'Cancelled',
					self::STATUS_POSTPONED => 'Postponed',
					self::STATUS_FOC => 'FOC',	
				); 
				break;
			case self::STATUS_IN_PROCESS:
				$statuses = array(
					self::STATUS_IN_PROCESS => 'In Process',
					self::STATUS_APPROVED => 'Approved',					
					self::STATUS_CANCELLED => 'Cancelled',
					self::STATUS_POSTPONED => 'Postponed',
					self::STATUS_FOC => 'FOC',
				); 
				break;
			case self::STATUS_APPROVED:
				$statuses = array(
					self::STATUS_APPROVED => 'Approved',
					self::STATUS_POSTPONED => 'Postponed',
					self::STATUS_CANCELLED => 'Cancelled',	
					self::STATUS_DONE => 'Closed',
				); 
				break;
				case self::STATUS_CANCELLED:
				$statuses = array(
					self::STATUS_CANCELLED => 'Cancelled',
				); 
				break;
			case self::STATUS_POSTPONED:
				$statuses =  array(
					self::STATUS_POSTPONED => 'Postponed',
					self::STATUS_APPROVED => 'Approved',			
					self::STATUS_CANCELLED => 'Cancelled',	
					self::STATUS_FOC => 'FOC',		
					self::STATUS_NEW => 'New',
				); 
				break;
				case self::STATUS_FOC:
				$statuses = array (
					self::STATUS_FOC => 'FOC',
					self::STATUS_POSTPONED => 'Postponed',
					self::STATUS_CANCELLED => 'Cancelled',
					self::STATUS_DONE => 'Closed',
					);
				break;
				case self::STATUS_DONE:
				$statuses = array(
					self::STATUS_DONE => 'Closed',
				); 
				break;
			default:
				$statuses = array(
					self::STATUS_NEW => 'New',
					self::STATUS_IN_PROCESS => 'In Process',
					self::STATUS_APPROVED => 'Approved',
					self::STATUS_CANCELLED => 'Cancelled',
					self::STATUS_POSTPONED => 'Postponed',
					self::STATUS_FOC => 'FOC',
					self::STATUS_DONE => 'Closed',									
				); 
				break;
		}
		return $statuses;
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList($value);	return $list[$value];
	}
    public static function getTypeLabel($value){
		$list = self::getTypesList();	return $list[$value];
	}	
	protected function beforeValidate(){
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) { $this->addError($param[0], $param[1]); }
        return $r;
    }    
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
    public function renderTrainingNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("trainingsnewmodule/view", array("id" => $this->idTrainings)).'">'.$this->training_number.'</a>';
	}
	public static function getTrainingProfit($id){
		$cost = Yii::app()->db->createCommand("select ifnull(sum(tc.amount),0) as cost  from training_costs tc where tc.id_training=".$id." ")->queryScalar();
		$rev = Yii::app()->db->createCommand("select sum(ea_amount) from ( select case WHEN e.currency='9' THEN (sum(ei.amount) * ((100 - e.discount)/100)) ELSE (sum((ei.amount)*(select c.rate FROM currency_rate c where c.currency=e.currency order by date limit 1)) * ((100 - e.discount)/100)) END as ea_amount from eas_items ei join eas e on ei.id_ea = e.id where e.status>1 and e.id in (select id_ea from training_eas where id_training = ".$id.") group by e.id) as qu")->queryScalar();
		$profit=$rev-$cost;		return $profit;
	}
	public static function getTrainingCosts($id){
		$result = Yii::app()->db->createCommand("SELECT ifnull(sum(tc.amount),0) as cost from trainings_new_module t, training_costs tc where t.idTrainings = tc.id_training and idTrainings =".$id)->queryScalar();
	return $result;
	}
	public function getTrainingStartDate($id){
			$result = Yii::app()->db->createCommand("select start_date from trainings_new_module where idTrainings=" .$id)->queryScalar();	
			return $result;
	}
	public  function getParticipantsProvider(){
		$criteria=new CDbCriteria;	$criteria->condition = "id_training =".$this->idTrainings;	
		return new CActiveDataProvider('TrainingParticipants', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
	public function getTrainingEndDate($id){
		$result = Yii::app()->db->createCommand("select end_date from trainings_new_module where idTrainings=" .$id)->queryScalar();
		return $result;
	}
	public static function getTrainingActive($id){
		$trainings_model = TrainingsNewModule::model()->findByPk((int)$id);
		return $trainings_model;
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN trainings_new_module ON trainings_new_module.customer=customers.id')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id']; }
		return $customers;
	}
public static function getCityAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT codelkups.id, codelkups.codelkup FROM codelkups INNER JOIN trainings_new_module ON trainings_new_module.city=codelkups.id')->queryAll();
		$cities = array();
		foreach ($result as $i=>$res){	$cities[$i]['label'] = $res['codelkup'];	$cities[$i]['id'] = $res['id'];	}
		return $cities;
	}
	public static function getAllCourses(){
		$result = Yii::app()->db->createCommand('SELECT DISTINCT codelkups.id,codelkups.codelkup from codelkups INNER JOIN trainings_new_module ON trainings_new_module.course_name=codelkups.id')->queryAll();
		$courses = array();
		foreach ($result as $i=>$res){	$courses[$i]['label'] = $res['codelkup'];	$courses[$i]['id'] = $res['id'];	}
		return $courses;
}
	public static function getPartnersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN trainings_new_module ON trainings_new_module.partner=customers.id order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id']; }
		return $customers;
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN trainings_new_module ON users.id=trainings_new_module.Instructor order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public static function getCustomersPartners(){
		$criteria = new CDbCriteria(array(
            'with'=>array(
                'customers'=>array(
	                'alias'=>'cust',
	                'together'=>true
               ),
            ),
            'select' => 'cust.id, cust.name',
            'order'=>'name ASC',        
			
        ));
        return CHtml::listData(self::model()->findAll($criteria), 'id','name');
	}
	public static function getTrainingPartner($id_partner, $id_training){
		$partners = self::getCustomersPartners();
	    if(GroupPermissions::checkPermissions('general-training','write')){
		    return CHtml::dropDownlist('partners', $id_partner, $partners, array('prompt'=>'',
		        'class'     => 'status ',
		    	'onchange'=>'changeTrainingPartner('."value".','.$id_training.');',
		    ));
	    }else{
	    	if($id_partner == null)
	    		$partners	= array();
	    	return CHtml::dropDownlist('partners', $id_partner, $partners,array('disabled'=>true));
	    }
	}
	public static function getTrainingCourse($id_course, $id_training){
		$courses = Codelkups::getCodelkupsDropDown('Training Course');
	    if(GroupPermissions::checkPermissions('general-training','write')){
		    return CHtml::dropDownlist('courses', $id_course, $courses, array('prompt'=>'',
		        'class'     => 'status ',
		    	'onchange'=>'changeTrainingCourse('."value".','.$id_training.');',
		    ));
	    }else{
	    	return CHtml::dropDownlist('courses', $id_course, $courses,array('disabled'=>true));
	    }
	}
	public static function getTrainingCountry($id_country, $id_training){
		$countries = Codelkups::getCodelkupsDropDown('Country');
	    if(GroupPermissions::checkPermissions('general-training','write')){
		    return CHtml::dropDownlist('countries', $id_country, $countries, array('prompt'=>'',
		        'class'     => 'status ',
		    	'onchange'=>'changeTrainingCountry('."value".','.$id_training.');',
		    ));
	    }else{
	    	return CHtml::dropDownlist('countries', $id_country, $countries,array('disabled'=>true));
	    }
	}
	public static function getTrainingType($id_type,$id_training){
		$types = array(
			self::TYPE_PARTNER => 'Partner',
			self::TYPE_PRIVATE => 'Private',
			self::TYPE_PUBLIC => 'Public',
			);
		if(GroupPermissions::checkPermissions('general-training','write')){
			return CHtml::dropDownlist('types', $id_type, $types, array('prompt'=>'',
		        'class'     => 'status ',
		    	'onchange'=>'changeTrainingType('."value".','.$id_training.');',
		    ));

		}else{
			return CHtml::dropDownlist('types', $id_type, $types, array(
		        'disabled'=>true
		    ));
		}
	}
	public static function getTrainingInstructor($id_instructor,$id_training){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1')->queryAll();
		$users = array();	$users[''] = ' ';
		foreach ($result as $i => $res)	{		$users[$res['id']] = $res['firstname'].'  '.$res['lastname'];	}
		if(GroupPermissions::checkPermissions('general-training','write')){
			return CHtml::dropDownlist('instructor', $id_instructor, $users, array(
		        'class'     => 'instructor',
		    	'onchange'=>'changeTrainingInstructor('."value".','. $id_training.')',
		    ));
	    }else{
	    	return CHtml::dropDownlist('instructor', $id_instructor, $users, array(
		        'class'     => 'instructor',
		    	'disabled'=>true,
		    ));
	    }
	}
public static function getTrainingSelectDDL(){
			
		$trainings =  CHtml::listData(Yii::app()->db->createCommand("SELECT  trainings_new_module.idTrainings,concat(idTrainings,' - ',cl.codelkup,' - ',city,' - ',start_date,' to ',end_date)as name
		from trainings_new_module join  codelkups cl  on trainings_new_module.course_name = cl.id order by trainings_new_module.idTrainings Desc")->queryAll(),
				'idTrainings', 'name');
		return $trainings;
	}
public static function getTrainingSelect($id_training =null){
		$tranings = array();
		$query ='select distinct t.idTrainings, t.training_number, t.country, t.city, t.start_date, t.end_date,t.status, cl.codelkup, u.firstname, u.lastname
												from trainings_new_module t join codelkups cl on t.course_name = cl.id
												join users u on t.instructor = u.id';
		if($id_training != null){	$where =' Where t.idTrainings ='.$id_training;	}
		$order =' Order by t.training_number Desc';
		$results = Yii::app()->db->createCommand($query.$where.$order)->queryAll();
		foreach ($results as $i =>$res){
				$trainings[$i]['id'] = $res['idTrainings'];
				$trainings[$i]['val'] = $res['training_number'].' '.$res['codelkup'].' '.$res['firstname'].' '.$res['lastname'].' '.$res['city'];
				$tranings[$i]['desc'] = $res['codelkup'].' '.$res['city'].' '.$res['start_date'].' '.$res['end_date'];
		}	
		return $trainings;
	}
public static function getTrainingsDropDown($id_ea){
	$trainings = array();	$trainings[''] = ' ';
	$results =  Yii::app()->db->createCommand('select distinct t.idTrainings, t.training_number, t.country, t.city, t.start_date, t.end_date,t.status, cl.codelkup, u.firstname, u.lastname
												from trainings_new_module t join codelkups cl on t.course_name = cl.id
												join users u on t.instructor = u.id	Order by t.training_number Desc')->queryAll();
	foreach($results as $i => $res){
		$trainings[$res['idTrainings']] = $res['training_number'].'- '.$res['codelkup'];
	}
	return CHtml::dropDownlist('training','', $trainings, array(
		        'class'     => 'training',
		    	'onchange'=>'changeEaDesc('."value".')',
		    ));
}	
public static function getTrainingEas($id_tranining = null){
	$eas = array();
	$query ='select e.id,e.ea_number,e.status,e.discount from training_eas JOIN eas e on training_eas.id_ea = e.id';
	if($id_training != null){ $where=' where training_eas.id_training= '.$id_tranining; }	
	$order=' order by e.ea_number';
	$results = Yii::app()->db->createCommand($query.$where.$order)->queryAll();
	foreach ($results as $i => $res){
		$eas[$i]['id'] = $res['id'];		$eas[$i]['number'] = $res['ea_number'];
		$eas[$i]['status'] = $res['status'];		$eas[$i]['discount'] = $res['discount'];
	}
	return $eas;
}	
public static function getTrainingParticipants($id_training){
	$participants = array();
	$results = Yii::app()->db->createCommand('select id, firstname, lastname, title, email from training_participants where id_training ='. $id_training)->queryAll();
	foreach ($results as $i => $res){
		$participants[$i]['id'] = $res['id'];		$participants[$i]['name'] = $res['firstname'].' '.$res['lastname'];
		$participants[$i]['title'] = $res['title'];		$participants[$i]['email'] = $res['email'];
	}
	return $participants;
}
	public function getAll($group = NULL){
		$criteria = new CDbCriteria;
		$criteria->select = array(
				"t.*"	
		);
		$dataProvider = new CActiveDataProvider('TrainingsNewModule', array(
				'criteria' => $criteria,
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => 'start_date ASC',
           		 ),
		));
		return $dataProvider;
	}		
}	
?>