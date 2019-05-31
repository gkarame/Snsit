<?php 
class InstallationRequests extends CActiveRecord {
	CONST STATUS_INPROGRESS = 2;	CONST STATUS_COMPLETED = 3;	CONST STATUS_CANCELED = 4;
	CONST DISASTER_YES = 1;	CONST DISASTER_NO = 2;	CONST prerequisite_YES = 1;	CONST prerequisite_NO = 2;
	CONST INSTALL_LOCATION_REMOTE = 1;	CONST INSTALL_LOCATION_ONSITE = 2;	CONST LOCALLY_LOCALLY = 0;	CONST LOCALLY_CUSTOMER_SERVERS = 1;
	public $customErrors = array();	public $customer_name,$project_name,$product_id=null;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'installation_requests';
	}
	public function rules(){
return array(
		array('ir_number,customer,project, deadline_date, expected_starting_date,disaster_recovery,installation_locally,requested_by,status', 'required'),
		array('ir_number,customer ,installation_location,assigned_to,requested_by,status,installation_locally,prerequisites', 'numerical', 'integerOnly'=>true),
		array('customer', 'exist', 'attributeName' => 'id', 'className' => 'Customers','allowEmpty'=>false),
		array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers','allowEmpty'=>true),
		array('assigned_to', 'exist', 'attributeName' => 'id', 'className' => 'Users','allowEmpty'=>true),
		array('requested_by', 'exist', 'attributeName' => 'id', 'className' => 'Users','allowEmpty'=>false),
		array('deadline_date, expected_starting_date', 'safe'),
		array('notes','length','max'=>255),
		array('customer_contact_name,customer_contact_email','length','max'=>45),
		array('customer,project,customer_name, assigned_to, requested_by, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eCustomer' => array(self::BELONGS_TO, 'Customers', 'customer'),
			'eAssigned_to' => array(self::BELONGS_TO, 'Users', 'assigned_to'),
			'eProducts' => array(self::HAS_MANY, 'InstallationrequestsProducts', 'id_ir'),
			'eRequested_by' => array(self::BELONGS_TO, 'Users', 'requested_by'),
			);
	}
public function attributelabels(){
		return array(
			'ir_number' => 'Request #',
			'customer' => 'Customer',
			'project' => 'Project',
			'notes' => 'Notes',
			'deadline_date' => 'Delivery Date',
			'expected_starting_date' => 'Servers Ready On',
			'disaster_recovery' => 'Disaster Recovery Plan',
			'installation_location' => 'On Site/Remote? ',
			'installation_locally' => 'Environment',
			'assigned_to'=>'Assigned To',
			'requested_by' => 'Created By',
			'status' => 'Status',
			'prerequisites' => 'Prerequisites shared?',
			'customer_contact_name' => 'Customer Contact Name',
			'customer_contact_email' => 'Customer Contact Email',
		);
	}
	public function search(){
		$criteria= new CDbCriteria;
		$criteria->select  = "t.*";
		$criteria->with = array('eProducts','eAssigned_to', 'eRequested_by','eCustomer');
		$criteria->together = true;
		if ($this->assigned_to){ 
			$split = explode(' ', $this->assigned_to, 2);
			if (count($split) == 2){
				$criteria->addCondition('(eAssigned_to.firstname = :author1 AND eAssigned_to.lastname = :author2) OR (eAssigned_to.firstname = :author2 AND eAssigned_to.lastname = :author1)');
				$criteria->params[':author1'] = $split[0];
				$criteria->params[':author2'] = $split[1];
			}else{
				$criteria->addCondition('eAssigned_to.firstname = :author OR eAssigned_to.lastname = :author');
				$criteria->params[':author'] = $this->assigned_to;
			} 
		}
		if ($this->requested_by){ 
			$split = explode(' ', $this->requested_by, 2);
			if (count($split) == 2){
				$criteria->addCondition('(eRequested_by.firstname = :author1 AND eRequested_by.lastname = :author2) OR (eRequested_by.firstname = :author2 AND eRequested_by.lastname = :author1)');
				$criteria->params[':author1'] = $split[0];
				$criteria->params[':author2'] = $split[1];
			}else{
				$criteria->addCondition('eRequested_by.firstname = :author OR eRequested_by.lastname = :author');
				$criteria->params[':author'] = $this->requested_by;
			} 
		}
		if($this->customer){
			$criteria->addCondition('(t.customer = :customer)');		$criteria->params[':customer'] = $this->customer;
		}
		if($this->project){
			//print_r($this->project);exit;
			$criteria->addCondition('(t.project = :project)');		$criteria->params[':project'] = $this->project;
		}
		if($this->status){
			$criteria->addCondition('(t.status = :status)');		$criteria->params[':status'] = $this->status;
		}
		if($this->product_id){
			$criteria->addCondition('(eProducts.id_product = :product)');		$criteria->params[':product'] = $this->product_id;
		}		
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination'=>array(
            'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		         'attributes'=>array(
		         	'eAssigned_to.fullname'=>array(
		                'asc'=>'eAssigned_to.firstname',
		                'desc'=>'eAssigned_to.firstname DESC',
		            ),
		            'eRequested_by.fullname'=>array(
		                'asc'=>'eRequested_by.firstname',
		                'desc'=>'eRequested_by.firstname DESC',
		            ),
		            'project.name'=>array(
	         				'asc'=>'project.name',
	         				'desc'=>'project.name DESC',
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
        foreach ($this->customErrors as $param) {           $this->addError($param[0], $param[1]);       }
        return $r;
    }
	public function addProductID($id = null){	$this->product_id = $id;}
	public static function getDirPath($customer,$id_ir){		
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/installationrequests/$id_ir/";		
		if (!is_dir( $path ) ){
            mkdir( $path, 0777, true);            chmod( $path, 0777 );
        }
		return $path; 
	}	
	public function getFiles($id_ir){		
		$path = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->customer.DIRECTORY_SEPARATOR.'installationrequests'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		$files = Yii::app()->db->createCommand( "SELECT filename FROM `installation_requests_attachments` WHERE id_ir='$id_ir'")->queryColumn();	
		$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$dirName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;
		$existing = array();
		foreach ($files as $file){
			if (file_exists($dirPath.$file) && is_file($dirPath.$file)){
				$existing[]['path'] = $path.$file;
			}else{
				Yii::app()->db->createCommand( "DELETE FROM `installation_requests_attachments` WHERE id_ir='$id_ir' && id_ir='{$id_ir}' && filename='$file'")->execute();
			}
		}
		return $existing;
	}
	public function isEditable(){
		return !in_array($this->status, array(InstallationRequests::STATUS_COMPLETED,InstallationRequests::STATUS_CANCELED));
	}
	public static function getDBList(){
		return array(
			self::DB_TYPE_ORACLE => 'ORACLE',
			self::DB_TYPE_MSSQL => 'MSSQL',
			);
	}
	public static function getStatusList($current_status = null){
		switch ($current_status) {
			case self::STATUS_INPROGRESS:
				$statuses = array(
					self::STATUS_INPROGRESS => 'Pending',
					self::STATUS_COMPLETED => 'Closed',
					self::STATUS_CANCELED => 'Canceled');
				break;
			case self::STATUS_COMPLETED:
				$statuses = array(
					self::STATUS_COMPLETED => 'Closed');
				break;
			case self::STATUS_CANCELED:
				$statuses = array(
					self::STATUS_CANCELED => 'Canceled');
				break;
			default:
				$statuses = array(
					self::STATUS_INPROGRESS => 'Pending',
					self::STATUS_COMPLETED => 'Closed',
					self::STATUS_CANCELED => 'Canceled');
				break;
		}
	return $statuses;
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList($value);
		return $list[$value];
	}
	public function renderRequestNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("installationrequests/update", array("id" => $this->id)).'">'.$this->ir_number.'</a>';
	}
	public  static function closeReq($id){
		Yii::app()->db->createCommand("UPDATE `installation_requests` SET status=3 WHERE id= ".$id."")->execute();
		
		return true;
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN installation_requests ON installation_requests.customer=customers.id order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){
			$customers[$i]['label'] = $res['name'];
			$customers[$i]['id'] = $res['id'];
		}
		return $customers;
	}
	public static function getProjectsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT projects.id, projects.name FROM projects INNER JOIN installation_requests ON installation_requests.project=projects.id order by projects.name')->queryAll();
		$training = Yii::app()->db->createCommand("SELECT id_maintenance as id , contract_description as name, maintenance.customer FROM maintenance INNER JOIN installation_requests ON installation_requests.project=CONCAT(maintenance.id_maintenance,'m')  order by maintenance.contract_description ")->queryAll();
			
		$customers = array();
		foreach ($result as $i=>$res){
			$customers[$i]['label'] = $res['name'];
			$customers[$i]['id'] = $res['id'];
		}
		foreach ($training as $i=>$res){
			$customers[$i]['label'] = Customers::getNameById($res['customer']).' '.$res['name'];
			$customers[$i]['id'] = $res['id'].'m';
		}
		return $customers;
	}
	public static function getAssignedUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN installation_requests ON users.id=installation_requests.assigned_to and users.active=1 order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){
			$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	} 
	public static function getRequestUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN installation_requests ON users.id=installation_requests.requested_by order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){
			$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getInstallLocationList(){
		return array(
			self::INSTALL_LOCATION_ONSITE => 'On Site',
			self::INSTALL_LOCATION_REMOTE => 'Remote');
	}
	public static function getInstallationLabel($id =null){
		if($id != null){
		$list = self::getInstallLocationList();
		return $list[$id];
		}else{
			return "";
		}
	}
	public static function getPrerequisitesList(){
		return array(self::prerequisite_YES => 'Yes',
			self::prerequisite_NO => 'No');
	}
	public static function getPrerequisitesLabel($id){
			$list = self::getPrerequisitesList();
			return $list[$id];
	}
	public static function getAssignToUsers($id_ir,$id_user,$status){
		$results = Yii::app()->db->createCommand("select distinct users.id, users.firstname, users.lastname, user_groups.id_group from users join user_groups on id_user
			where user_groups.id_group in (18,13) and users.active = 1
			group by users.id
			ORDER BY 
		firstname, lastname")->queryAll();
		$users = array();
		$users[''] = ' ';
		foreach ($results as $i => $res){	$users[$res['id']] = $res['firstname'].'  '.$res['lastname'];}
		if(GroupPermissions::checkPermissions('ir-assign-installationrequests','write')){
			return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		        'disabled' => ($status == self::STATUS_COMPLETED)?true:false,
		    	'onchange'=>'changeInput('."value".','. $id_ir.')',
		    	'style'=>'width:150px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:190px;border:none;'
		    ));
	    }
	}
	public static function getAssignedToListEdit(){
		$results = Yii::app()->db->createCommand("select distinct users.id, users.firstname, users.lastname, user_groups.id_group from users join user_groups on id_user
			where user_groups.id_group in (18,13) and users.active = 1
			group by users.id
			ORDER BY 
			    CASE
        WHEN firstname LIKE 'D%' THEN 1
        ELSE 2 end,   users.firstname, users.lastname")->queryAll();
		$users = array();
        foreach ($results as  $res){      	$users[$res['id']] = $res['firstname'].' '.$res['lastname'];     }
        return $users;
	}
	public static function getAssignedToList(){
		$results = Yii::app()->db->createCommand("select distinct users.id, users.firstname, users.lastname, user_groups.id_group from users join user_groups on id_user
			where user_groups.id_group in (18,13) and users.active = 1
			group by users.id
			ORDER BY 
			    CASE
        WHEN firstname LIKE 'D%' THEN 1
        ELSE 2 end")->queryAll();
        $users = array();
		foreach ($results as $i => $res){
			$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	} 
	public static function installation_locally(){
		return array( self::LOCALLY_LOCALLY => 'Locally',
		self::LOCALLY_CUSTOMER_SERVERS => 'Customer Servers');
	}
	public static function getlocallycustomerlabel($id){		$list = self::installation_locally();		return $list[$id];	}
	public static function getDisasterList(){
		return array(self::DISASTER_YES => 'YES',
			self::DISASTER_NO => 'NO');
	}
	public static function getDisasterLabel($id){	$list = self::getDisasterList();	return $list[$id];}
	public function getAll($group = NULL){
		$criteria = new CDbCriteria;
		$criteria->select = array(
				"t.*"	
		);
		$dataProvider = new CActiveDataProvider('InstallationRequests', array(
				'criteria' => $criteria,
				'pagination'=>array(
					'pageSize'=>10000, // or another reasonable high value...
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => 'expected_starting_date ASC',
           		 ),
		));
		return $dataProvider;
	}
}
?>