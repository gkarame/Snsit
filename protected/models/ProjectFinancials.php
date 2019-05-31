<?php
class ProjectFinancials extends CActiveRecord{	
	public $country;	public $caller;	public $file;	public $order;	public $id_customer;	
	const TYPE_TRAINING = 24;	const TYPE_LICENCES = 25;	const TYPE_CONSULTING = 26;	const TYPE_SW = 27;	const TYPE_CHANGE_REQUEST = 28;
	const STATUS_INACTIVE = 0;	const STATUS_ACTIVE = 1;	const STATUS_CLOSED = 2;	const STATUS_ON_HOLD = 3;	const STATUS_CANCELLED = 4;	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects';
	}
	public function rules(){
		return array(
			array('name, customer_id, status', 'required'),
			array('customer_id, is_billable, status, project_manager, business_manager, id_parent, id_type, is_billable, country', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>500),
			array('customer_id', 'exist', 'attributeName' => 'id', 'className' => 'Customers'),
			array('project_manager, business_manager', 'exist', 'attributeName' => 'id', 'className' => 'Users', 'allowEmpty'=>true),
			array('id_parent', 'exist', 'attributeName' => 'id', 'className' => 'Projects', 'allowEmpty'=>true),
			array('id_type', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups'),
			array('deactivate_alerts', 'length', 'max'=>256 ),
			array('id,order , name,customer_id,project_manager', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'customer_id'),
			'projectManager' => array(self::BELONGS_TO, 'Users', 'project_manager'),
			'businessManager' => array(self::BELONGS_TO, 'Users', 'business_manager'),
			'parent' => array(self::BELONGS_TO, 'Projects', 'id_parent'),
			'type' => array(self::BELONGS_TO, 'Codelkups', 'id_type'),
			//'eas' => array(self::HAS_MANY, 'Eas', 'id_project'),
			'phases'=>array(self::HAS_MANY, 'ProjectsPhases', 'id_project'),
			'alert' => array(self::HAS_ONE,'ProjectsAlerts','id_project'),
			'eas'=>array(self::HAS_MANY, 'eas', 'id_project'),
		);
	}
	public static function getStatusList(){		
		return array(
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_INACTIVE => 'Inactive',
		); 
	}	
	public static function getStatusLabel($value){
		$list = self::getStatusList();
		return $list[$value];
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Project',
			'is_billable' => 'Billable',
			'customer_id' => 'Customer',
			'project_manager' => 'Project Manager',
			'business_manager' => 'Business Manager',
			'id_type' => 'Type',
			'id_parent' => 'Parent Project',
		);
	}
	public static function isBillable($id){
		return Yii::app()->db->createCommand()
    		->select('is_billable')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
	}	
	public static function getNameById($id){
		$name = Yii::app()->db->createCommand()
    		->select('name')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	return ($name != false ? $name : '');
	}	
	public static function checkIdExists($id){
		$name = Yii::app()->db->createCommand()
    		->select('name')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	return $name;
	}	
	public function getEas(){	return Projects::getSEas($this->id);}	
	public static function getSEas($project){
		$project = (int)$project;	$criteria=new CDbCriteria;
		$criteria->condition = "(id_project = :project) OR (id_parent_project = :project AND status =:approved)";
		$criteria->params = array(':project' => $project,':approved'=>"2");
		return Eas::model()->findAll($criteria);
	}	
	public function getEasProvider(){
		$criteria=new CDbCriteria;	$criteria->condition = "(id_project = :project) OR (id_parent_project = :project AND status =:approved)";
		$criteria->params = array(':project' => $this->id,':approved'=>"2");	
		return new CActiveDataProvider('Eas', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}	
	public function getPhases(){
		$criteria=new CDbCriteria;	$criteria->condition = 'id_project = '.($this->id ? $this->id : 0);	
		return new CActiveDataProvider('projectsPhases', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
	public function getMilestones(){
		$criteria=new CDbCriteria;	$criteria->with = array('idMilestone');	$criteria->together = true;	$criteria->condition = 'id_project = '.($this->id ? $this->id : 0);	
		return new CActiveDataProvider('ProjectsMilestones', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
            	'attributes' => array(
            		 'idMilestone.description'=>array(
		                'asc'=>'idMilestone.description',
		                'desc'=>'idMilestone.description DESC',
		            ),
		            '*'
            	),
    			'defaultOrder'=>'t.id ASC',  
            ),
		));
	}	
	public function getNumberPhases(){
		return Yii::app()->db->createCommand()
    		->select('id')
    		->from('projects_phases')
    		->where('id =:id', array(':id'=>$this->id))
    		->queryScalar();
	}	
	public static function getTasks($id){
			$criteria=new CDbCriteria;	
			$criteria->condition = 'id_project_phase = '.($id ? $id : 0);	
			return new CActiveDataProvider('ProjectsTasks', array(
				'criteria' => $criteria,
				'pagination'=>array(
	                'pageSize' => Utils::getPageSize(),
	            ),
	             'sort'=>array(
	    			'defaultOrder'=>'id ASC',  
	            ),
			));
	}
	public function search(){		
		$criteria=new CDbCriteria;	$criteria->with = array('customer');	$criteria->together = true;
		$criteria->compare('t.name', $this->name, true);	$criteria->compare('customer.name', $this->customer_id);$criteria->compare('t.status', $this->status);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder' => 't.name ASC',            
		        'attributes' => array(
		            'customer.name'=>array(
						'asc'=>'customer.name',
						'desc'=>'customer.name DESC',
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
	public static function getAllProjectsSelect($customer_id=0){
		if ($customer_id){
			return CHtml::listData(self::model()->findAll(array(
				'condition'=>'customer_id=:cid AND status=1', 
				'params'=>array(':cid'=>$customer_id),
				'order'=>'t.name ASC')), 'id', 'name');
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}	
	public static function getAllAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT id, name FROM projects WHERE status=1')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['name'];	$projects[$i]['id'] = $res['id'];}
		return $projects;
	}
	public static function createProject($name, $customer, $type, $parent_project = null){
		$project = new Projects();	$project->name = $name;	$project->customer_id = $customer;	$project->id_type = $type;
		if ($parent_project != null){	$project->id_parent = $parent_project;	}
		if ($project->save()) {	return array('id' => $project->id);	}
		return $project->getErrors(); 
	}	
	public static function getAllProjectPhases($id){
		return ProjectsPhases::model()->findAll(array(
			'condition' => 'id_project = :project', 
			'params' => array(':project' => $id),
			'order' => 'phase_number')
		);
	}	
	public static function getName($id){
		return Yii::app()->db->createCommand('SELECT phase FROM phases where id='.$id)->queryScalar();
	}	
	public static function getProjectId($id_task){
		return Yii::app()->db->createCommand('SELECT id_project FROM projects_phases left join projects_tasks on projects_phases.id = projects_tasks.id_project_phase where projects_tasks.id='.$id_task)->queryScalar();
	}	
	public static function getPhaseId($id_task){
		return Yii::app()->db->createCommand()
    		->select('id_project_phase')
    		->from('projects_tasks')
    		->where('id =:id', array(':id'=>$id_task))
    		->queryScalar();
	}	
	public function getCurrentPhase() {
		$phase = Yii::app()->db
		->createCommand(" SELECT description FROM projects_phases WHERE id_project={$this->id} ORDER BY phase_number ASC")
		->queryScalar();
		return $phase;
	} 
	public static function getCurrentMilestone($id_project) {
		$milestone = Yii::app()->db
		->createCommand(" SELECT id_milestone FROM projects_milestones WHERE id_project= $id_project AND status = 'In Progress' ORDER BY id ASC LIMIT 1")
		->queryScalar();
		return $milestone;
	}
	public static function getDaysOf($id_phase){
		$id_phase = (int) $id_phase;
		return (int) Yii::app()->db->createCommand("SELECT SUM(projects_tasks.man_days_budgeted) FROM projects_tasks WHERE id_project_phase= {$id_phase}")->queryScalar();
	}
	public static function getActualDaysOf($id_phase){
		$id_phase = (int) $id_phase;
		$sum_in_hours = (float) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE id_task IN (SELECT id FROM projects_tasks WHERE id_project_phase= {$id_phase})")->queryScalar();
		return Utils::formatNumber($sum_in_hours/8, $decimal_no = 2);
	}	
	public static function getSumBudget($exp){
		$sum_budget=0;
		foreach ($exp as $val)
			$sum_budget += $val->expense;
		return $sum_budget;
	}
	public static function getSumSpent($exp){
		$sum_spent=0;
		foreach ($exp as $val)
			$sum_spent += Projects::getExpensesAmount($val->id_project);
		return $sum_spent;
	}
	public static function getProjectNetAmountWithoutExpenses($eas) {
		$sum=0;
		foreach ($eas as $val){	$sum += $val->getNetAmount(); }
		return $sum;
	}
	public static function getProjectTotalManDays($eas){
		$sum=0;
		foreach ($eas as $val)
			$sum += $val->getTotalManDays();
		return $sum;
	}
	public static function getProjectActualManDays($project,$eas = null) {
		if ($eas == null){	$eas = Projects::getSEas($project);	}
		$sum=0;		
		foreach ($eas as $val)
			$sum += $val->getActualMD();
		return $sum;
	}
	public static function getActualManDays($project,$id_ea_item ) {
		$eas = EasItems::model()->findAll($id_ea_item);	$sum=0;
		foreach ($eas as $val)
			$sum += $val->getActualMD($project);
		return $sum;
	}
	public static function getProjectRemainingManDays($eas){
		$sum=0;
		foreach($eas as $val)
			$sum += $val->getTotalManDays() - $val->getActualMD();
		return $sum;
	}
 	public static function getCountry($id){
    	return Yii::app()->db->createCommand()
    		->select('codelkup')
    		->from('codelkups')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
 	}
 	public static function getParentProject($id_project){
    	return Yii::app()->db->createCommand()
    		->select('name')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id_project))
    		->queryScalar();
 	} 	
 	public static function getCurrencyName($id){
		return Yii::app()->db->createCommand()
    		->select('codelkup')
    		->from('codelkups')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar(); 		
 	} 	
 	public static function getExpensesAmount($id_project){
		return (float) Yii::app()->db->createCommand()
 			->select('SUM(total_amount)')
    		->from('expenses')
    		->where('project_id =:id', array(':id'=>$id_project))
    		->queryScalar(); 	
 	} 	
 	public static function getId($column,$id_project){
 		return Yii::app()->db->createCommand()
    		->select($column)
    		->from('projects')
    		->where('id =:id', array(':id'=>$id_project))
    		->queryScalar();
 	}
	public static function getIdByName($name){
 		return Yii::app()->db->createCommand()
    		->select('id')
    		->from('projects')
    		->where('name =:id', array(':id'=>$name))
    		->queryScalar();
 	} 	
 	public static function getExpensesType($id_customer, $id_project) {
 		return Yii::app()->db->createCommand()
					->select('expense')
    				->from('eas')
    				->where('id_customer =:id_customer AND  id_project = :id_project', 
    					array(':id_customer'=>$id_customer, ':id_project' => $id_project))
    				->queryScalar();
 	}
 	public static function setStatus($id, $status){
 		$status = (int) $status;	$id = (int) $id;	Yii::app()->db->createCommand("UPDATE projects SET status='{$status}' WHERE id='{$id}'")->execute();
 	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN projects ON projects.customer_id=customers.id')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id']; }
		return $customers;
	}	
	public static function getProjectsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT projects.id, projects.name FROM projects ')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['name'];	$projects[$i]['id'] = $res['id']; }
		return $projects;
	}	
	public static function getCountriesAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT codelkups.id, codelkups.codelkup FROM codelkups INNER JOIN codelists ON codelkups.id_codelist="1"')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['codelkup'];	$projects[$i]['id'] = $res['id'];	}
		return $projects;
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN projects ON users.id=projects.project_manager')->queryAll();
		$users = array();
		foreach ($result as $i => $res)	{	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id']; }
		return $users;
	}
	public static function getAllActiveUsers(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1')->queryAll();		
		$users = array();
		foreach ($result as $i => $res)	{	$users[$res['id']] = $res['firstname'].' '.$res['lastname'];}
		return $users;
	}
	public static function setBillable($id_project){
		Yii::app()->db->createCommand("UPDATE projects SET is_billable = 1 WHERE id = '$id_project'")->execute();
	}	
	public static function getAllProjectWithAlerts(){
		$criteria=new CDbCriteria;	$criteria->with = array('alert','customer');	$criteria->together  = true;	$criteria->Addcondition('alert.alerts <> ""');
		$dataprovider =  new CActiveDataProvider('Projects', array(
				'criteria' => $criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
						'defaultOrder'=>'customer.name ASC',
				),
				
		));
		return $dataprovider->getData();
	}
	public function getIdModel(){
    	$model = __CLASS__;
		return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model'")->queryScalar();    	
    }
} ?>