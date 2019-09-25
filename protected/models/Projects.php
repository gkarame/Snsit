<?php
class Projects extends CActiveRecord{	
	public $country;	public $caller;	public $file;	public $order;	public $id_customer;	
	const TYPE_TRAINING = 24;	const TYPE_LICENCES = 25;	const TYPE_CONSULTING = 26;	const TYPE_SW = 27;	const TYPE_CHANGE_REQUEST = 28;
	const STATUS_INACTIVE = 0;	const STATUS_ACTIVE = 1;	const STATUS_CLOSED = 2;	const STATUS_ON_HOLD = 3;	const STATUS_CANCELLED = 4;
	const PRIORITY_HIGH=1;	const PRIORITY_MEDIUM=2;	const PRIORITY_LOW=3;	const RESPONSIBILITY_SNS = 'SNS';	const RESPONSIBILITY_CLIENT = 'CLIENT';
	const RESPONSIBILITY_3rd = 'THIRD PARTY';	const RESPONSIBILITY_BOTH = 'SNS/CLIENT';	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects';
	}
	public function rules(){
		return array(
			array('name, customer_id,complexmodule, status,tm', 'required'),
			array('customer_id, is_billable, status, project_manager, business_manager, id_parent,version, product, id_type, template, is_billable, country', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>500),			
			array('customer_id', 'exist', 'attributeName' => 'id', 'className' => 'Customers'),
			array('project_manager, business_manager', 'exist', 'attributeName' => 'id', 'className' => 'Users', 'allowEmpty'=>true),
			array('id_parent', 'exist', 'attributeName' => 'id', 'className' => 'Projects', 'allowEmpty'=>true),
			array('id_type', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups'),
			array('deactivate_alerts,under_support,complexnotes', 'length', 'max'=>256 ),
			array('transition_to_support_date, closed_date', 'length', 'max'=>10 ),
			array('surveystatus', 'length', 'max'=>15 ),			
			array('tm,complexmodule,qa', 'length', 'max'=>3),			
			array('project_manager','validatefields'),
			array('id,order , name,customer_id,project_manager,surveystatus,status, tm', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'customer_id'),
			'projectManager' => array(self::BELONGS_TO, 'Users', 'project_manager'),
			'businessManager' => array(self::BELONGS_TO, 'Users', 'business_manager'),
			'parent' => array(self::BELONGS_TO, 'Projects', 'id_parent'),
			'type' => array(self::BELONGS_TO, 'Codelkups', 'id_type'),
			'phases'=>array(self::HAS_MANY, 'ProjectsPhases', 'id_project'),
			'checklist'=>array(self::HAS_MANY, 'ProjectsChecklist', 'id_project'),
			'alert' => array(self::HAS_ONE,'ProjectsAlerts','id_project'),
			'eas'=>array(self::HAS_MANY, 'eas', 'id_project'),
		);
	}
	public function validatefields(){
	 if ( $this->id_type == 27 && ($this->template == 1 || $this->template == 4 || $this->template == 6)) {
	        if(empty($this->product)){
	         	 $this->addError('product','Product must be specified.');
	        }	
	        if(empty($this->version)){
	         	 $this->addError('version','Version must be specified.');
	        }	        	
	    }			
	}

	public static function getStatusReportFile($id_project){
		$id_document = Yii::app()->db->createCommand("select max(id) from documents where model_table ='projects' and id_model = ".$id_project)->queryScalar();
		$name_document = Yii::app()->db->createCommand("select file from documents where id = ".$id_document)->queryScalar();
		$path = self::getDirPath($id_project,$id_document);
		if($name_document){
			$path.=$name_document;
			if(file_exists($path) && is_file($path)){		return($path);	}
		}
	}
	public static function getDirPath($project_id, $document_id){
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.
			"projects".DIRECTORY_SEPARATOR.$project_id.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$document_id.DIRECTORY_SEPARATOR;
		if (!is_dir($path)){	mkdir( $path, 0777, true);	chmod( $path, 0777 );	}
		return $path;
	}
	public static function getcomplexnotes(){		
		return array(
			'Billing' => 'Billing',
			'Complex Integration' =>'Complex Integration',
			'Cross-Docking' => 'Cross-Docking',
			'End to End Serial Capturing' => 'End to End Serial Capturing',			
			'Labor' => 'Labor',
			'Production' => 'Production',
			'Task Interleaving' => 'Task Interleaving',
			'TMS' => 'TMS',
			'Work Order' => 'Work Order'
		); 	
	}
	public static function getcomplexModule(){		
		return array(
			'Yes' => 'Yes',
			'No' => 'No',
		); 
	}
	public static function getStatusList(){		
		return array(
			self::STATUS_INACTIVE => 'Inactive',
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_CLOSED => 'Closed',
		); 
	}
public static function getSurveyStatusList(){		
		return array(
			'Cancelled' => 'Cancelled',
			'No Response' => 'No Response',
		); 
	}
	public static function getPriorityTypes(){
		return array(
			self::PRIORITY_HIGH => 'HIGH',
			self::PRIORITY_MEDIUM => 'MEDIUM',
			self::PRIORITY_LOW => 'LOW');
	}
	public static function getPriorityLabel($id){
		$prios = self::getPriorityTypes();
		return $prios[$id];
	}
	public static function getResponsibilityTypes(){
		return array(
			self::RESPONSIBILITY_SNS => 'SNS',
			self::RESPONSIBILITY_CLIENT => 'CLIENT',
			self::RESPONSIBILITY_BOTH=> 'SNS/CLIENT',
			self::RESPONSIBILITY_3rd => 'THIRD PARTY');
	}	
	public static function getStatusLabel($value){
		$list = self::getStatusList();
		return $list[$value];
	}	
	public static function getStatusLabelDrop(){
		for ($i=0; $i <3; $i++) { $list[$i]=self::getStatusLabel($i);}		
		return $list;
	} 
public static function getTm(){
		return array(
			0 => 'Yes',
			1 => 'No');
	}
public static function getqa(){
		return array(
			'Yes' => 'Yes',
			'No' => 'No');
	}
	
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Project Name',
			'is_billable' => 'Billable',
			'customer_id' => 'Customer',
			'project_manager' => 'Project Manager',
			'business_manager' => 'Business Manager',
			'project_manager_edit' =>'Project Manager *',
			'business_manager_edit' => 'Business Manager *',
			'version' => 'Version',
			'product' => 'Product',
			'version_edit' => 'Version *',
			'product_edit' => 'Product *',
			'id_type' => 'Type',
			'id_parent' => 'Parent Project',
			'tm'=> 'T&M',
			'complexmodule'=>'Complex Modules',
			'complexnotes' =>'Values',
			'surveystatus' => 'Survey Status',
			'qa' => 'QA',
			'template' => 'Subtype'
		);
	}	
	public static function getAllCategoriesProjects(){
		$result= Yii::app()->db->createCommand("SELECT  c.id, c.codelkup FROM codelkups c WHERE id_codelist=4 and id in (26,27,28) order by c.codelkup")->queryAll();
		$categories = array();
		foreach ($result as $i=>$res){	$categories[$i]['label'] = $res['codelkup'];	$categories[$i]['id'] = $res['id'];	}
		return $categories;		
	}
	public static function getStatus($id){
		if (!empty($id)){	$stat= Yii::app()->db->createCommand("SELECT status FROM projects where id=".$id." ")->queryScalar(); return self::getStatusLabel($stat);	}else{ return ''; }
	}
	public static function isBillable($id){
		$expense= Yii::app()->db->createCommand("SELECT expense FROM eas where id_project=".$id." ")->queryScalar();
		if (empty($expense)){	$expense= Yii::app()->db->createCommand("SELECT expense FROM eas where id_parent_project=".$id." ")->queryScalar();	}
		if ($expense=='Actuals'){	return 1; }else	{	return 0; }
	}	
	public static function getNameById($id){
		$name = Yii::app()->db->createCommand()
    		->select('name')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	return ($name != false ? $name : '');
	}	
	public static function getNameById2($id , $m, $internal = 0){

		if($internal>0)
		{
			$name = Internal::getNameById($id);
		}
		else if ($m>0){
			$name = Maintenance::getMaintenanceDescription($id);
		}else{
			$name = Yii::app()->db->createCommand()
	    		->select('name')
	    		->from('projects')
	    		->where('id =:id', array(':id'=>$id))
	    		->queryScalar();
    	}
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
	public static function getProjectManager($id){
		$pm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `projects` p,users u where p.id=".$id." and p.project_manager=u.id;")->queryRow();
		if (!empty($pm)){ $manager=$pm['first']." ".$pm['last']; }else{	$manager=" "; }		
		return $manager;
	}
	public static function getProjectManagerInitials($id){
		$pm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `projects` p,users u where p.id=".$id." and p.project_manager=u.id;")->queryRow();
		if (!empty($pm)){ 
			$fname= $pm['first'];
			$lname=  $pm['last'];
			$manager=$fname[0]."".$lname[0]; 
		}else{	
			$manager=" "; 

		}		
		return $manager;
	}
    public static function getBusinessManager($id){
		$pm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `projects` p,users u where p.id=".$id." and p.business_manager=u.id;")->queryRow();
		if (!empty($pm)){	$manager=$pm['first']." ".$pm['last']; }else{	$manager=" "; }
		return $manager;
	}
	public static function getProjectManagerEmail($id){
		$pm= Yii::app()->db->createCommand("SELECT upd.email as email FROM `projects` p,user_personal_details upd where p.id=".$id." and p.project_manager=upd.id_user")->queryScalar();
		return $pm;
	}
    public static function getBusinessManagerEmail($id){
		$bm= Yii::app()->db->createCommand("SELECT upd.email as email FROM `projects` p,user_personal_details upd where p.id=".$id." and p.business_manager=upd.id_user")->queryScalar();
		return $bm;
	}
	public function getEas(){
		return Projects::getSEas($this->id);
	}
	public static function getEAid($project){
		return	$id_ea= Yii::app()->db->createCommand("SELECT ea_number FROM eas where id_project=".$project." ")->queryScalar();
		
	}
	public static function getEAids($project){
		return	$id_ea= Yii::app()->db->createCommand("SELECT id FROM eas where id_project=".$project." ")->queryScalar();		
	}	
	public static function getSEas($project){
		$project = (int)$project;	$criteria=new CDbCriteria;	$criteria->condition = "(id_project = :project) OR (id_parent_project = :project AND status >=:approved)";
		$criteria->params = array(':project' => $project,':approved'=>"2");	return Eas::model()->findAll($criteria);
	}	
	public function getEasProvider(){
		$criteria=new CDbCriteria;	$criteria->condition = "(id_project = :project) OR (id_parent_project = :project AND status >=:approved)";
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
	public function getEasProvider2(){
		$criteria=new CDbCriteria;	$criteria->condition = "((id_project = :project) OR (id_parent_project = :project AND status >=:approved)) and expense != 'N/A'";
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
	public function getChecklist(){
		$criteria=new CDbCriteria;	$criteria->with = array('idChecklist');	$criteria->together = true;	$criteria->condition = 'id_project = '.($this->id ? $this->id : 0);	
		if(isset($_GET['Projects']['status'])|| isset($_GET['Projects']['name'])|| isset($_GET['Projects']['surveystatus'])){
			$status = $_GET['Projects']['status'];		$phase = $_GET['Projects']['name'];	$responsb = $_GET['Projects']['surveystatus'];		
		if(isset($phase)){		$criteria->compare('idChecklist.id_phase',$phase);	}
		if(isset($status)){		$criteria->compare('status',$status);	}
		if(isset($responsb)){		$criteria->compare('responsibility',$responsb);	}
		return new CActiveDataProvider('ProjectsChecklist', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
            	'attributes' => array(
            		 'idChecklist.descr'=>array(
		                'asc'=>'idChecklist.descr',
		                'desc'=>'idChecklist.descr DESC',
		            ),
		            '*'
            	),
    			'defaultOrder'=>'t.id ASC',  
            ),
		));
		}else{
			$criteria->compare('status','Open');
			return new CActiveDataProvider('ProjectsChecklist', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
            	'attributes' => array(
            		 'idChecklist.descr'=>array(
		                'asc'=>'idChecklist.descr',
		                'desc'=>'idChecklist.descr DESC',
		            ),
		            '*'
            	),
    			'defaultOrder'=>'t.id ASC',  
            ),
		));
		}
	}
	public function getIssues(){
		$criteria=new CDbCriteria;	$criteria->condition = 'id_project = '.($this->id ? $this->id : 0);	
		if(isset($_GET['Projects']['complexnotes']) || isset($_GET['Projects']['complexmodule'])){
			$status = $_GET['Projects']['complexnotes'];	$priority = $_GET['Projects']['complexmodule'];
		if(isset($priority)){	$criteria->compare('priority',$priority); }
		
		if(isset($status)){	$criteria->compare('status',$status); }
		return new CActiveDataProvider('ProjectsIssues', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'t.id_issue ASC',  
            ),
		));
		}else{
			return new CActiveDataProvider('ProjectsIssues', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'t.id_issue ASC',  
            ),
			));
		}
	}
	public static function getIssuesitems($id){
		return Yii::app()->db->createCommand()
    		->select('*')
    		->from('projects_issues')
    		->where('id_project =:id', array(':id'=>$id))
    		->queryAll();
	}
	public static function getChecklistitems($id){
		return Yii::app()->db->createCommand()
    		->select('*')
    		->from('projects_checklist')
    		->where('id_project =:id', array(':id'=>$id))
    		->queryAll();
	}
	public function getNumberPhases(){
		return Yii::app()->db->createCommand()
    		->select('id')
    		->from('projects_phases')
    		->where('id =:id', array(':id'=>$this->id))
    		->queryScalar();
	}
	public static function getProjectStatus($id){
		return Yii::app()->db->createCommand()
    		->select('status')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
	}	
	public static function getProjectType($id){
		return Yii::app()->db->createCommand()
    		->select('id_type')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
	}
	public static function checkSurveyStatus($id ,$type){
		return Yii::app()->db->createCommand("SELECT count(1) FROM surveys_results where id_project=".$id." and surv_type='".$type."'")->queryScalar();	
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
public static function getStatusValue($status){		
	switch ($status) {
				case 'Active':
					return 1; break;
				case 'Inactive':
					return 0; break;
				case 'Closed':
					return 2; break;
				case 'On Hold':
					return 3; break;
			    case 'Cancelled':
					return 4; break;
			}

			}
	public function search(){		
		$criteria=new CDbCriteria;
		$criteria->with = array('projectManager','customer');
		$criteria->compare('t.name', $this->name, true);	
		if (isset($this->project_manager) && $this->project_manager != ''){
			$last = explode("  ", $this->project_manager);
			$criteria->join='LEFT JOIN users ON users.id=t.project_manager';	$criteria->addCondition('users.firstname LIKE :tn');
			$criteria->params[':tn']='%'.substr($this->project_manager,0,strrpos($this->project_manager,"  ")).'%';
			$criteria->addCondition('users.lastname LIKE :tr');	$criteria->params[':tr']='%'.$last[1].'%';
		}		
		if (isset($this->status) && $this->status != ''){	$criteria->compare('t.status', '='.self::getStatusValue($this->status));}
		else if (!isset($this->status) || $this->status == ''){	$criteria->compare('t.status', '=1'); }		
		if(isset($this->customer_id)&& $this->customer_id!= "")	{	$criteria->compare('customer.name', $this->customer_id); }
		if(isset($this->tm)&& $this->tm!= ""){	$criteria->compare('t.tm', $this->tm);	}
		if(isset($this->template)&& $this->template!= ""){	$criteria->compare('t.template', $this->template);	}
		if(isset($this->id_type)&& $this->id_type!= ""){	$criteria->compare('t.id_type', Eas::getIdCategoryByName($this->id_type));	}		
		$criteria->compare('t.id', '<> 0');		$criteria->compare('t.customer_id', '<> 0');
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder' => 't.name ASC',            
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
public static function getAllProjectsAssignedtoUserProd($user_id){	
		/*$techteam = Yii::app()->db->createCommand("SELECT count(*) FROM user_groups WHERE id_user='".$user_id."' and  (id_group=9 or id_group=13 )")->queryScalar();
		if ($techteam > 0){
			$projectlist = Yii::app()->db->createCommand("SELECT distinct p.id ,p.name FROM projects p , projects_phases pp , projects_tasks pt , user_task uta 
						WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$user_id."' and p.status='1' 
						UNION
						SELECT  CONCAT('m',m.id_maintenance) as id, CONCAT(cu.name, ' ' ,c.codelkup) as description	FROM maintenance m
								LEFT JOIN customers cu on m.customer=cu.id	LEFT JOIN codelkups c on m.support_service=c.id
							WHERE m.support_service in ('501','502') and m.status='Active' order by name")->queryAll();
		}else*/
		{
			$projectlist = Yii::app()->db->createCommand("SELECT distinct p.id ,p.name FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$user_id."' and p.status='1' order by p.name")->queryAll();
		}
		if ($user_id){
			$projects = CHtml::listData( $projectlist, 'id', 'name');		
			return $projects;  
		}		
	}	
	public static function getActiveOpsAssignedIds($project){	
		$ops = Yii::app()->db->createCommand("SELECT distinct(uta.id_user) FROM projects p , projects_phases pp , projects_tasks pt , user_task uta, users u WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and p.id=".$project." and p.status='1' and uta.id_user in (select id_user from user_groups where id_group in (12,22,27))  and uta.id_user=u.id and u.active=1")->queryAll();
		return $ops;  
	}
	public static function getOpsAssignedIds($project){	
		$ops = Yii::app()->db->createCommand("SELECT distinct(uta.id_user) FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and p.id=".$project." and p.status='1' and uta.id_user in (select id_user from user_groups where id_group in (12,22,27)) ")->queryAll();
		return $ops;  
	}
	public static function getPmOpsAssigned($project){	
		$pm= Projects::getProjectManagerInitials($project);
		$ops = Yii::app()->db->createCommand("SELECT distinct(uta.id_user) FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and p.id=".$project." and p.status='1' and uta.id_user!= p.project_manager and uta.id_user in (select id_user from user_groups where id_group in (12,22,27)) ")->queryAll();
		$str='';
		$str.=$pm;
		if (!empty($ops)){
			if (!empty($pm)){
				$str.=', ';
			}
			$i=0;	$tot= sizeof($ops)-1;
			foreach ($ops as $key => $op){
				$str.= Users::getinitials($op['id_user']);
				if($key<$tot){	$str.=', ';	}
			}
		}
		return $str;  
	}
	public static function getPmOpsAssignedWhole($project){	
		$pm= Projects::getProjectManager($project);
		$ops = Yii::app()->db->createCommand("SELECT distinct(uta.id_user) FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and p.id=".$project." and p.status='1' and uta.id_user!= p.project_manager and uta.id_user in (select id_user from user_groups where id_group in (12,22,27)) ")->queryAll();
		$str='';
		$str.=$pm;
		if (!empty($ops)){
			if (!empty($pm)){
				$str.=', ';
			}
			$i=0;	$tot= sizeof($ops)-1;
			foreach ($ops as $key => $op){
				$str.= Users::getNameById($op['id_user']);
				if($key<$tot){	$str.=', ';	}
			}
		}
		return $str;  
	}
	public static function getAllProjectsAssignedtoUser($user_id){	
		$projectlist = Yii::app()->db->createCommand("SELECT distinct p.id ,p.name FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$user_id."' and p.status='1' order by p.name")->queryAll();
		if ($user_id){	$projects = CHtml::listData( $projectlist, 'id', 'name');	return $projects;  }
	}
	public static function getAllPMsProjectsAssignedtoUser($user_id){	
		return Yii::app()->db->createCommand("SELECT distinct p.project_manager as pm FROM projects p , projects_phases pp , projects_tasks pt , user_task uta, users u WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and uta.id_user='".$user_id."' and p.status='1'  and u.id=p.project_manager and u.active=1")->queryAll();
	}
	public static function getAllParentProjectsMaintSelect($customer_id=0,$type = null){
		if ($customer_id){
			if(preg_match("/[a-z]/i", $customer_id)){
				$customer_id=Customers::getIdByName($customer_id);
			}
			if (isset($type)){
			    if ($type == 'project'){
			        return Yii::app()->db->createCommand("SELECT id ,name FROM projects WHERE customer_id = $customer_id AND status = 1")->queryAll();
                }else if($type == 'maintenance'){
			        return Yii::app()->db->createCommand("SELECT id_maintenance as id , contract_description as name FROM maintenance WHERE customer = $customer_id and support_service in (501,502) and status='Active'")->queryAll();
                }
            }else{
                $items = array();
                $training = Yii::app()->db->createCommand("SELECT id_maintenance as id , contract_description as name FROM maintenance WHERE customer = $customer_id and support_service in (501,502) and status='Active'")->queryAll();
                $result =  Yii::app()->db->createCommand("SELECT id ,name FROM projects WHERE customer_id = $customer_id AND status = 1")->queryAll();
                foreach ($result as $id => $res){	$items[$res['id']] = $res['name'];	}
                foreach ($training as $id => $res)	{	$items[$res['id'].'m'] = $res['name'];	}
                return $items;
            }
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}	
	public static function getAllParentProjectsSelect($customer_id=0){
		if ($customer_id){
			$projects = CHtml::listData(self::model()->findAll(array(
				'condition'=>'customer_id=:cid and (id_type=26 or id_type=27 or id_type=28)', 				
				'params'=>array(':cid'=>$customer_id),
				'order'=>'t.name ASC')), 'id', 'name');
			
			return $projects;  
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}	
	public static function getAllProjectsAndInternalSelect($customer_id=0){
		if ($customer_id){
			$projects = CHtml::listData(self::model()->findAll(array(
				'condition'=>'customer_id=:cid AND status=1', 
				'params'=>array(':cid'=>$customer_id),
				'order'=>'t.name ASC')), 'id', 'name');
			if($customer_id == 177)	
			{
				$internal= Yii::app()->db->createCommand("SELECT id, name from internal ")->queryAll();
				foreach ($internal as $value) {
					$projects[$value['id'].'i']= $value['name'];
				}
			}	
			return $projects;  
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}
	public static function getAllProjectsSelect($customer_id=0){
		if ($customer_id){
			$projects = CHtml::listData(self::model()->findAll(array(
				'condition'=>'customer_id=:cid AND status=1', 
				'params'=>array(':cid'=>$customer_id),
				'order'=>'t.name ASC')), 'id', 'name');			
			return $projects;  
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}
	public static function getAllProjectsSelect2($customer_id){
		if ($customer_id){
			$customer_id= "'".str_replace(", ","','",$customer_id)."'"; 

		$result =  Yii::app()->db->createCommand("SELECT p.id, p.name FROM projects p,customers c WHERE p.status=1 and p.customer_id=c.id and c.name in (".$customer_id.") order by name")->queryAll();

		$projects = array();
		foreach ($result as $i=>$res){	$projects[$res['id']] = $res['name'];	}
		return $projects;  
		}else{
			return  array('' => '');
		}	
	}
	public static function checktandm($proj){
		$tm = Yii::app()->db->createCommand("SELECT TM FROM eas WHERE id_project =".$proj." ")->queryScalar();
		if ($tm == 1){	return 'Yes'; }else{	return 'No'; }
	}
	public static function getMDS($id){
        $mds = Yii::app()->db->createCommand("SELECT mds FROM eas WHERE id_project =".$id." ")->queryScalar();
        return $mds;
    }
	public static function getAllTandMProjects($customer_id=0){
		if ($customer_id){
			$projects = CHtml::listData(self::model()->findAll(array(
				'condition'=>'customer_id=:cid AND status=1', 
				'params'=>array(':cid'=>$customer_id),
				'order'=>'t.name ASC')), 'id', 'name');			
			return $projects;  
		}else{
			return CHtml::listData(self::model()->findAll(array('with'=>'eas', 'together'=>true , 'condition'=>' eas.TM=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}
	public static function getAllProjectsTimesheetReport($customer_id=0){
		if ($customer_id){
			$projects = CHtml::listData(self::model()->findAll(array(
				'condition'=>'customer_id=:cid ', 
				'params'=>array(':cid'=>$customer_id),
				'order'=>'t.name ASC')), 'id', 'name'); 
			if($customer_id == 177)	
			{
				$internal= Yii::app()->db->createCommand("SELECT id, name from internal ")->queryAll();
				foreach ($internal as $value) {
					$projects[$value['id'].'i']= $value['name'];
				}
			}	
			return $projects;  
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}	
	public static function getAllProjectsTrainingsSelect($customer_id=0,$exp = false){		
		if ($customer_id){		
			if(preg_match("/[a-z]/i", $customer_id)){
				$customer_id=Customers::getIdByName($customer_id);
			}	
			$items = array();	$training = Yii::app()->db->createCommand("SELECT id,name FROM trainings WHERE id_customer = $customer_id")->queryAll();
			$type_licences = 25;
			if($exp == true)
				$result =  Yii::app()->db->createCommand("SELECT id ,name FROM projects WHERE customer_id = $customer_id AND status = 1 AND id_type != $type_licences")->queryAll();
			else
				$result =  Yii::app()->db->createCommand("SELECT id ,name FROM projects WHERE customer_id = $customer_id AND status = 1")->queryAll();			
			foreach ($result as $id => $res){	$items[$res['id']] = $res['name'];	}
			foreach ($training as $id => $res)	{	$items[$res['id'].'t'] = $res['name'];	}
			return $items;  
		}else{
			return CHtml::listData(self::model()->findAll(array('condition'=>'status=1', 'order'=>'name ASC')), 'id', 'name');	
		}	
	}	
	public static function getAllAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT id, name FROM projects WHERE status=1')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['name'];	$projects[$i]['id'] = $res['id']; }
		return $projects;
	}
	public static function getUserCustomerProjects($name){
		$id_user= Yii::app()->user->id;
		$result = Yii::app()->db->createCommand("SELECT p.id as id, p.name as name FROM projects_tasks pt,user_task ut, projects_phases pp, projects p,users u,customers c
where ut.id_task=pt.id and pt.id_project_phase=pp.id and p.id=pp.id_project and ut.id_user=".$id_user." and p.status=1 and p.customer_id=c.id and c.`name` LIKE '".$name."%' 
GROUP BY p.name")->queryAll();
		$projects = CHtml::listData($result,'id','name');
		return $projects;
	}
	public static function validatname($name){
		$result = Yii::app()->db->createCommand("select count(*) from projects where name like '".$name."' ")->queryScalar();
		return $result;
	}
	public static function getTemplatePerProject($id)
	{
		$result = Yii::app()->db->createCommand("select template from projects where id=".$id." ")->queryScalar();
		return $result;
	}
	public static function createProjectActive($id, $name, $status ,$customer, $type, $mandaysalert, $parent_project = null, $tm, $template=1){
		if ($tm ==1 ){	$tms = 'Yes'; }else{	$tms='No';	}		
		if ($parent_project != null){
			Yii::app()->db->createCommand("insert into projects (name, customer_id, id_parent, id_type, status, deactivate_alerts, under_support, tm ) values ('".$name."', ".$customer.",".$parent_project.",".$type.",".$status.",'No','No', '".$tms."' ) ")->execute();
		}else{
			Yii::app()->db->createCommand("insert into projects (name, customer_id, id_type, status, deactivate_alerts, under_support, tm, template )	values ('".$name."', ".$customer.",".$type.",".$status.",'No','No','".$tms."', ".$template."  ) ")->execute();
		}	 		
		$id= Yii::app()->db->createCommand("SELECT id FROM projects where name='".$name."'")->queryScalar();
		if ($id) {	if(($mandaysalert <= 30 && $type!=26) || $type==28)	{	Yii::app()->db->createCommand("UPDATE projects SET deactivate_alerts= 'Yes' WHERE id='".$id."' ")->execute(); } }
		return $id;	
	}
	public static function createProject($name, $customer, $type, $parent_project = null){
		$project = new Projects();	$project->name = $name;	$project->customer_id = $customer;	$project->id_type = $type;	$project->status = 0;		
		if ($parent_project != null){	$project->id_parent = $parent_project;	}
		if ($project->save()) {	return array('id' => $project->id);	}
		return $project->getErrors(); 
	}	
	public static function getPhase($id){
		$descrs= ProjectsPhases::getCurrent($id);
		$str='';
		if (is_array($descrs)){	foreach ($descrs as $descr){	$str.=$descr['description'].' ';} }else{ $str=$descrs;	}		
		return $str;
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
	public static function getAllPhasesByProject($id_project){
		return Yii::app()->db->createCommand()
    		->select('id')
    		->from('projects_phases')
    		->where('id_project=:id', array(':id'=>$id_project))
    		->queryColumn();
	}
	public function getCurrentPhase() {
		$phase = Yii::app()->db
		->createCommand(" SELECT description FROM projects_phases WHERE id_project={$this->id} ORDER BY phase_number ASC")
		->queryScalar();
		return $phase;
	} 
	public static function getCurrentMilestone($id_project) {
		$milestone = Yii::app()->db->createCommand("SELECT m.description FROM projects_milestones pm , milestones m WHERE pm.id_milestone=m.id AND pm.id_project= $id_project AND pm.status = 'In Progress' ORDER BY pm.id ASC LIMIT 1")->queryScalar();
		if(empty($milestone)){
			$milestone = Yii::app()->db->createCommand("SELECT  m.description FROM projects_milestones pm , milestones m WHERE pm.id_milestone=m.id AND pm.id_project= $id_project AND pm.status = 'Pending' ORDER BY pm.id ASC LIMIT 1")->queryScalar();
		}
		if(empty($milestone)){
 		$milestone="All Phases are Closed";

		}
		return $milestone;
	} 
	public static function getDaysOf($id_phase){
		$id_phase = (int) $id_phase;
		return (int) Yii::app()->db	->createCommand("SELECT SUM(projects_tasks.man_days_budgeted) FROM projects_tasks WHERE id_project_phase= {$id_phase}")->queryScalar();
	}
	public static function getActualDaysOf($id_phase){
		$id_phase = (int) $id_phase;
		$sum_in_hours = (float) Yii::app()->db
			->createCommand("SELECT SUM(amount) FROM user_time WHERE `default`=0 and  id_task IN (SELECT id FROM projects_tasks WHERE id_project_phase= {$id_phase})")->queryScalar();
		return round(($sum_in_hours/8), 2);
	}
	public static function getApprovedActualDaysOf($id_phase){
		$id_phase = (int) $id_phase;
		$sum_in_hours = (float) Yii::app()->db
			->createCommand("SELECT SUM(amount) FROM user_time WHERE status='1' and `default`=0 and id_task IN (SELECT id FROM projects_tasks WHERE id_project_phase= {$id_phase})")->queryScalar();
		return $sum_in_hours/8;
	}	
	public static function getRate($id){	
		$rate  =   Yii::app()->db->createCommand()
				->select('rate')
    			->from('currency_rate')
    			->where('currency = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryScalar();
    	
    	return $rate;
    }
	public static function getSumBudgetUSD($exp){
		$sum_budget = 0;
		foreach ($exp as $val) {
			switch ($val->expense) {
				case 'Actuals':
				case 'N/A':
				default:
					$curr= Projects::getRate($val->currency);
					$sum_budget += ((int)$val->expense * (int)$curr);
					break;
			}
		}
		return $sum_budget;
	}
	public static function getSumBudget($exp){
		$sum_budget = 0;
		foreach ($exp as $val) {
			switch ($val->expense) {
				case 'Actuals':
				case 'N/A':
				default:
					if(is_numeric($val->expense)) { $sum_budget += $val->expense;}
					break;
			}
		}
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
		foreach ($eas as $val){
			$sum += $val->getNetAmount();
		}
		return $sum;
	}
	public static function getProjectTotalManDays($eas){
		$sum=0;
		foreach ($eas as $val)
			$sum += $val->getTotalManDays();
		return $sum;
	}
	public static function getProjectAverageRate($eas){
		$sum=0;
		foreach ($eas as $val){	$sum += $val->getNetManDayRate();	}
		return $sum/count($eas);
	}
	public static function getProjectTotalManDaysByProject($project){
		$eas = Projects::getSEas($project);	$sum=0;
		foreach ($eas as $val)
			$sum += $val->getTotalManDays();
		return $sum;
	}
	public static function getActualMDPerParent($project)	{

		$parent= Yii::app()->db->createCommand("SELECT id_parent_project FROM `eas`  where id_project=".$project)->queryScalar();

		if (!empty($parent) && $parent!='' && $parent!= null)
		{
				$eas = Projects::getSEas($parent);
				$sum= Projects::getProjectActualManDays($parent,$eas);
				return $sum;	
		}
		else{
			$eas = Projects::getSEas($project);
				$sum= Projects::getProjectActualManDays($project,$eas);
				return $sum;	
		}
		
	}

	public static function getProjectActualManDays($project, $eas = null) {
		if ($eas == null){
			$eas = Projects::getSEas($project);
		}		
		$sum=0;
		foreach ($eas as $val){
			$sum += $val->getActualMD();
		}
		return $sum;
	}
	public static function getActualManDays($project,$id_ea_item ) {
		$eas = EasItems::model()->findAll($id_ea_item);
		$sum=0;
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
	public static function getProjectActualRate($project,$eas){
		$totnetamount= self::getProjectNetAmountWithoutExpenses($eas);
		$totalmandays= self::getProjectActualManDays($project,$eas);
		if($totalmandays==0){	$totalmandays=1;  }
		return ($totnetamount/$totalmandays);
	}
	public static function getProjectActualRateUSD($project,$eas){
		$streastring= "0";
		$strea=Yii::app()->db->createCommand("SELECT id FROM `eas` where (id_project = ".$project.") OR (id_parent_project = ".$project." AND status >=2)")->queryAll();
		
		foreach($strea as $val)
		{
			$streastring = $streastring.','.$val['id'];
		}
		$rate=Yii::app()->db->createCommand("SELECT rate FROM `currency_rate` where currency in 
								(select currency from eas where id in (".$streastring.") ) 
								order by date desc limit 1")->queryScalar();
		$totnetamount= self::getProjectNetAmountWithoutExpenses($eas);
		$totalmandays= self::getProjectActualManDays($project,$eas);
		if($totalmandays==0){	$totalmandays=1;  }
		return (($totnetamount/$totalmandays)*$rate);
	}
	public static function getProjectRemainingManDaysByProject($project){
		$eas = Projects::getSEas($project);	$sum=0;
		foreach($eas as $val)
			$sum += $val->getTotalManDays() - $val->getActualMD();
		return $sum;
	}
 	public static function getCountry($id)	{
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
 	public static function getCurrencyName($id)	{
		return Yii::app()->db->createCommand()
    		->select('codelkup')
    		->from('codelkups')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar(); 		
 	} 	
 	public static function getExpensesAmount($id_project){
		$expenses= (float) Yii::app()->db->createCommand()
 			->select('SUM(total_amount)')
    		->from('expenses')
    		->where('project_id =:id', array(':id'=>$id_project))
    		->queryScalar(); 
    		$travel_expenses = (float) Yii::app()->db->createCommand()
 			->select('SUM(amount)')
    		->from('travel')
    		->where('id_project =:id', array(':id'=>$id_project))
    		->queryScalar(); 
    		return 	$expenses+$travel_expenses;
 	} 	
 	public static function getExpensesAmountUSD($id_project){
		$type= Projects::getExpensesTypePerProject($id_project);
		if(!empty($type) && ctype_digit($type)){
			$expenses=(float)Yii::app()->db->createCommand("select sum(expenses.total_amount*(select c.rate from currency_rate c where c.currency=expenses.currency order by c.date desc limit 1))
			from expenses where billable !='yes' and project_id=".$id_project."	")->queryScalar();
    		$travel_expenses = (float)Yii::app()->db->createCommand("select sum(travel.amount*(select c.rate from currency_rate c where c.currency=travel.currency order by c.date desc limit 1))
			from travel where billable !='Yes' and id_project=".$id_project."	")->queryScalar();
		}else{
			$expenses=(float)Yii::app()->db->createCommand("select sum(expenses.total_amount*(select c.rate from currency_rate c where c.currency=expenses.currency order by c.date desc limit 1))
			from expenses where project_id=".$id_project."	")->queryScalar();
    		$travel_expenses = (float)Yii::app()->db->createCommand("select sum(travel.amount*(select c.rate from currency_rate c where c.currency=travel.currency order by c.date desc limit 1))
			from travel where id_project=".$id_project."	")->queryScalar();
		}
		return 	$expenses+$travel_expenses;
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
	public static function getCustomerByProject($id_project){
 		return Yii::app()->db->createCommand()
    		->select('customer_id')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id_project))
    		->queryScalar();
 	}	
	public static function getCustomerByProjectName($project){
 		return Yii::app()->db->createCommand()
    		->select('customer_id')
    		->from('projects')
    		->where('name =:name', array(':name'=>$project))
    		->queryScalar();
 	}
	public static function getGoliveStatus($id_project){
 		return Yii::app()->db->createCommand()
    		->select('golive')
    		->from('projects')
    		->where('id =:id', array(':id'=>$id_project))
    		->queryScalar();
 	}
 	public static function getExpensesTypePerProject($id_project) {
 		return Yii::app()->db->createCommand()
					->select('expense')
    				->from('eas')
    				->where('id_project = :id_project', 
    					array(':id_project' => $id_project))
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
 	public static function setStatus($id, $status, $mandaysalert){
 		$status = (int) $status;		$id = (int) $id;
 		Yii::app()->db->createCommand("UPDATE projects SET status='{$status}' WHERE id='{$id}'")->execute();
 		if($mandaysalert <= 30)	{	Yii::app()->db->createCommand("UPDATE projects SET deactivate_alerts= 'Yes' WHERE id='{$id}'")->execute();
 		}else{	Yii::app()->db->createCommand("UPDATE projects SET deactivate_alerts= 'No' WHERE id='{$id}'")->execute();	}
 	}
 	public static function setStatusinactive($id, $status){
 		$status = (int) $status;		$id = (int) $id;
 		Yii::app()->db->createCommand("UPDATE projects SET status='".$status."',deactivate_alerts= 'Yes' WHERE id='".$id."'")->execute(); 		
 	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN projects ON projects.customer_id=customers.id order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id']; }
		return $customers;
	}	
	public static function getProjectsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT projects.id, projects.name FROM projects WHERE customer_id !=0 order by projects.name ')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['name'];	$projects[$i]['id'] = $res['id'];	}
		return $projects;
	}	
	public static function getActiveProjectsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT projects.id, projects.name FROM projects WHERE customer_id !=0  and projects.status<>0 order by projects.name ')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['name'];	$projects[$i]['id'] = $res['id'];	}
		return $projects;
	}
	public static function getCountriesAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT codelkups.id, codelkups.codelkup FROM codelkups INNER JOIN codelists ON codelkups.id_codelist="1"')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['codelkup'];	$projects[$i]['id'] = $res['id']; }
		return $projects;
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN projects ON users.id=projects.project_manager order by users.firstname asc')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public static function getUsersAutocompleteDS(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN projects ON users.id=projects.project_manager order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];	$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getPhasesAutocompleteDS(){
		$result =  Yii::app()->db->createCommand('SELECT milestone_number, description FROM milestones WHERE id_category=27')->queryAll();
		$milests = array();
		foreach ($result as $i => $res){	$milests[$i][$res['milestone_number']] = $res['description'];	}
		return $milests;
	}
	public static function getAllActiveUsers(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1 order by firstname asc')->queryAll();		
		$users = array();		$users[0] = ' ';
		foreach ($result as $i => $res){	$users[$res['id']] = $res['firstname'].' '.$res['lastname']; }
		return $users;
	}
	public static function setBillable($id_project){
		Yii::app()->db->createCommand("UPDATE projects SET is_billable = 1 WHERE id = '$id_project'")->execute();
	}
	public static function getAllProjectWithAlertsForDirectors(){
		$result = Yii::app()->db->createCommand("select pa.id , pa.id_project , pa.alerts , pa.date from projects_alerts pa , projects p where pa.alerts<>' ' and pa.id_project=p.id  and p.project_manager is not null and p.deactivate_alerts='No' GROUP BY pa.id_project order by p.name asc")->queryAll();	
		return $result;
	}
	public static function getAllProjectWithAlerts(){
		$id_user= Yii::app()->User->id;		$criteria=new CDbCriteria;		$criteria->with = array('alert','customer');		$criteria->together  = true;
		$criteria->Addcondition('alert.alerts <> "" and ( t.project_manager='.$id_user.' or t.business_manager='.$id_user.' or '.$id_user.' in ( select DISTINCT id_user 
																																from user_task ut , projects_tasks pt , projects_phases pp 
																																	where ut.id_task=pt.id 
																																	and pt.id_project_phase=pp.id 
																																	and pp.id_project=t.id ) )');
		$dataprovider =  new CActiveDataProvider('Projects', array(
				'criteria' => $criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
						'defaultOrder'=>'t.name ASC',
				),
				
		));
		return $dataprovider->getData();
	}
	public static function getAllProjectWithAlertsDi(){
		$id_user= Yii::app()->User->id;	$criteria=new CDbCriteria;	$criteria->with = array('alert','customer');
		$criteria->together  = true;	$criteria->Addcondition('alert.alerts <> "" ');
		$dataprovider =  new CActiveDataProvider('Projects', array(
				'criteria' => $criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
						'defaultOrder'=>'t.name ASC',
				),				
		));
		return $dataprovider->getData();
	}
	public static function getCurrentProjectWithAlerts($id_project){
		$result = Yii::app()->db->createCommand("select pa.id , pa.id_project , pa.alerts , pa.date from projects_alerts pa , projects p where p.id=".$id_project." and pa.alerts<>' ' and pa.id_project=p.id GROUP BY pa.id_project order by p.name asc")->queryAll();	
		return $result ;
	}	
	public static function getProjectAlerts($id_project , $type){
		$alerts="";
		if($id_project!=null){
			$result= ProjectsAlerts::getProjectAlerts($id_project, $type);			
			foreach ($result as $value) {
				if ($value['id'] == 37 && $type=='alerts'){
					$Allphases=ProjectsIssues::getperstatusperproject($id_project);
					$str='';		$x=0;
					foreach ($Allphases as $Allphase){
						$x++;
						if($Allphase['items']>1)
						{
							$iss= 'Issues';
						}else{
							$iss= 'Issue';
						}
						$str.= $Allphase['items']." ".$iss." ".ProjectsIssues::getStatus($Allphase['status'])." ";
							if ($x != count($Allphases)){	$str.=", "; }
					}					
					$alerts .="Severity ".$value['severity'].": ".$str."<br />";

				}else	if ($value['id'] == 39 && $type=='warnings'){					
					$Allphases=ProjectsChecklist::getperphasesperproject($id_project);
					$lastupdate= ProjectsChecklist::getlastupdate($id_project);
					if(empty($lastupdate))
					{
						$str='Check List Items Closed: <br/>- ';
					}else{
						$str='Check List Items Closed – Last Update Date <b>'.$lastupdate.'</b> <br/>- ';
					}
							$x=0;
					foreach ($Allphases as $Allphase){
						$percent=0;		$x++;
						$closed= ProjectsChecklist::getperphaseitems($id_project, $Allphase['id_phase']);
						if ($closed!=0 ){	$percent=Utils::formatNumber((($closed/$Allphase['items'])*100) ,2);
						}else{ $percent=0;	}
						$str.= $percent."% ".Milestones::getMilestoneDescription($Allphase['id_phase']);
							if ($x != count($Allphases)){	$str.="<br/> - "; }
					}					
					$alerts .=$str."<br />";
				}else	if ($value['id'] == 41 && $type=='warnings'){	
					$tot= ProjectsTasks::getFBRsTot($id_project);
					$redundant= ProjectsTasks::checkFBRExistsStat($id_project, 2);
					$notchecked= ProjectsTasks::checkFBRExistsStat($id_project, 1);
					$new= ProjectsTasks::checkFBRExistsStat($id_project, 3);
					if($tot==1)
					{
						$fb='FBR is';
					}else{
						$fb='FBRs are';
					}
					$str=$tot.' '.$fb.' created under the project:';
					if($tot != 0){
						if($notchecked==1)
						{
							$fb='FBR';
						}else{
							$fb='FBRs';
						}
						$str.= "<br/> - ".$notchecked." ".$fb." Not Checked";	
						if($redundant==1)
						{
							$fb='FBR';
						}else{
							$fb='FBRs';
						}
						$str.= "<br/> - ".$redundant." ".$fb." Redundant ";		
						if($new==1)
						{
							$fb='FBR';
						}else{
							$fb='FBRs';
						}
						$str.= "<br/> - ".$new." ".$fb." New";	
						$alerts .=$str."<br />";	
					}		
				}
				else if ($type=='warnings'){
					$alerts .= $value['description']."<br />";
				}else{
					$alerts .="Severity ".$value['severity'].": ".$value['description']."<br />";
				}
			}
		}
		return $alerts;
	}
	public static function getProjectrisks($id_project){
		$risks="";
		if($id_project!=null){
			$result= ProjectsRisks::getRisks($id_project);
			foreach ($result as $value) {	$risks .="Priority ".$value['priority'].": ".$value['risk']."<br />"; }
		}
		return $risks;
	}
	public static function getUserProjectWithAlerts(){  
		$id_user= Yii::app()->User->id;
		$result = Yii::app()->db->createCommand(" select pa.id , pa.id_project , pa.alerts , pa.date from projects_alerts pa , projects p 
where pa.alerts<>' ' and pa.id_project=p.id and p.deactivate_alerts='No' and (p.project_manager=".$id_user." or p.business_manager=".$id_user." or ".$id_user." in ( select DISTINCT id_user from user_task ut , projects_tasks pt , projects_phases pp 
																																	where ut.id_task=pt.id 
																																	and pt.id_project_phase=pp.id 
																																	and pp.id_project=p.id ))
																																	GROUP BY pa.id_project order by p.name asc")->queryAll();	

		return $result;
		}
	public static function getBillability($id){
    	$result=Yii::app()->db->createCommand('SELECT is_billable FROM  projects where id='.$id)->queryScalar();
    	return $result;
	}
	public static function getUserGroup(){
		$id_user= Yii::app()->User->id;
    	$result=Yii::app()->db->createCommand('SELECT count(1) FROM  user_groups ug, email_notifications_groups eng 
       	where ug.id_user='.$id_user.' and ug.id_group=eng.id_group and eng.id_email_notification=44')->queryScalar();
    	return $result;
	}
	public static function getTotalAmount($id_project){
		$results = Yii::app()->db->createCommand()
						->select('eas.category, eas.currency, eas_items.amount, eas_items.man_days')
						->from('projects')
						->join('eas', 'eas.id_project = projects.id')
						->join('eas_items', 'eas_items.id_ea = eas.id')
						->where('projects.id = :id', array(':id' => $id_project))
						->queryAll();
		$sum = 0;
		foreach ($results as $key => $result){
			$rate = 1;
			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY){
				$rate = CurrencyRate::getCurrencyRate($result['currency']);
			}			
			if ($result['category'] == Projects::TYPE_LICENCES){
				$sum += $result['amount'] * $rate['rate'] * $result['man_days'];		
			}else{
				$sum += $result['amount'] * $rate['rate'];
			}	
		}		
		return $sum;
	}
	public static function getProjectExpensesBalance($id_project){
		$eas = Projects::getSEas($id_project);	$sum_budget=0;
		foreach ($eas as $val){
			if($val->expense == "Actuals"){
				return $val->expense;
				break;
			}else{
				$sum_budget += $val->expense;
			}
		}
		$sum_spent=0;
		foreach ($eas as $val)
			$sum_spent += Projects::getExpensesAmount($val->id_project);
		return $sum_budget - $sum_spent;
	}
	public static function getActualMD($id_project){
		if(!empty($id_project) && $id_project!='' && $id_project!=' ' && isset($id_project) ){
		$sum = (float) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time 
			WHERE id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase WHERE pp.id_project =".$id_project." ) and `default`='0'")->queryScalar();
		return $sum/8;
		}else{
			return 0;
		}
	}
	public static function getActualMDPerTask($task){
		if(!empty($task) && $task!='' && $task!=' ' && isset($task) ){
		$sum = (float) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time 
			WHERE id_task =".$task." and `default`='0'")->queryScalar();
		return $sum/8;
		}else{
			return 0;
		}
	}
	public static function getActualMDbyPhase($id_phase){
		if(!empty($id_phase) && $id_phase!='' && $id_phase!=' ' && isset($id_phase) ){
		$sum = (float) Yii::app()->db->createCommand("SELECT  SUM(amount) FROM user_time 
			WHERE (status=0 or status=1)  and `default`='0' and id_task IN (
			SELECT pt.id FROM projects_tasks pt where pt.id_project_phase=".$id_phase.") ")->queryScalar();
			
		return $sum/8;
		}else{
			return 0;
		}
	}
	public static function gettotActualMD($id_project){
		if(!empty($id_project) && $id_project!='' && $id_project!=' ' && isset($id_project) ){
		$sum = (float) Yii::app()->db->createCommand("SELECT  SUM(amount) FROM user_time 
			WHERE  `default`='0' and id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase
																					LEFT JOIN projects p ON p.id = pp.id_project WHERE (p.id = ".$id_project." ) ) ")->queryScalar();
		return $sum/8;
		}else{
			return 0;
		}
	}
	public static function getBudgetedMD($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(man_days) FROM eas_items ei , eas e  where e.id=ei.id_ea and e.status>='2' and (e.id_project=".$id_project." or e.id_parent_project=".$id_project." )")
			->queryScalar();
		return $sum;
	}
	public static function getCurrency($id_project){
		$curr = Yii::app()->db->createCommand("SELECT currency FROM eas_items ei , eas e  where e.id=ei.id_ea and e.status>='2' and (e.id_project=".$id_project." or e.id_parent_project=".$id_project." )")->queryScalar();
		return $curr;
	}
	public static function getEstimatedMD($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(man_days_budgeted) from projects_phases where id_project=".$id_project." ")->queryScalar();
		return $sum;
	}
	public static function getEstimatedMDbyPhase($id_phase){
		$sum = (float) Yii::app()->db->createCommand("SELECT man_days_budgeted from projects_phases where id=".$id_phase." ")->queryScalar();
		return $sum;
	}
	public static function getIncludingOffsetMD($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(total_estimated) from projects_phases where id_project=".$id_project." ")->queryScalar();
		return $sum;
	}
	public static function getNetAmount($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=".$id_project." or e.id_parent_project=".$id_project." ) and e.status!=0")->queryScalar();
		return $sum;
	}
	public static function getNetAmountApproved($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=".$id_project." or e.id_parent_project=".$id_project." ) and e.status>=2")->queryScalar();
		return $sum;
	}
	public static function getBudgetedRate($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(e.netmandayrateusd) FROM  eas e  where (e.id_project=".$id_project." or e.id_parent_project=".$id_project." )")->queryScalar();
		return $sum;
	}
	public static function getTMstatus($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT TM FROM eas e  where e.id_project=".$id_project)->queryScalar();
		return $sum;
	}
	public static function getProjectTotalEstimated($id_task){
		return Yii::app()->db->createCommand('SELECT sum(total_estimated) FROM projects_phases left join projects_tasks on projects_phases.id = projects_tasks.id_project_phase where projects_tasks.id='.$id_task)->queryScalar();
	}
	public static function getTotalOffsetMD($id_project){
		$sum = (float) Yii::app()->db->createCommand("select sum(CONCAT(case when offset_sign='Plus' then '+' else '-' END ,`offset`))  from `projects_phases_offsets` where id_project_phase in (select id from projects_phases where id_project=".$id_project.")")->queryScalar();
		return $sum;
	}
	public static function getTotalOffset($id_project){
		$sum = (float) Yii::app()->db->createCommand("select count(1)  from `projects_phases_offsets` where id_project_phase in (select id from projects_phases where id_project=".$id_project.")")->queryScalar();
		return $sum;
	}
	public static function getreasons($id_project){
		$reason = Yii::app()->db->createCommand("select OFFSET_reason from `projects_phases_offsets` where id_project_phase in (select id from projects_phases where id_project=".$id_project.")")->queryAll();
		return $reason;
	}	
	public static function getProjectsProjectSurveyNotSent($type){
		$list_users="";
		if($type=="close"){
				$results = Yii::app()->db->createCommand("select customer_id, name, project_manager from projects where id_type in('27','26') and `status`='2' and surveystatus not in ('No Response', 'Cancelled') and  id not in (select id_project from surveys_status where surveys_sent='1' and surv_type='Close')")->queryAll();
				$i=0;
				if(count($results)>0){$list_users .="Send survey to the following customers:  <br/><br/>";}
				foreach ($results as $key => $top){		
				$i++;	$list_users .= $i."- Customer : <b>".Customers::getNameById($top['customer_id'])."</b> - Project: <b>".$top['name']."</b> - PM: <b>".Users::getNameById($top['project_manager'])." </b><br/>";		
				}
		}else{
				$results = Yii::app()->db->createCommand("select customer_id,sum(ei.man_days) as budgeted ,p.id as pid ,p.name from projects p , eas e ,eas_items ei 
														where p.id_type in('27','26') and p.id=e.id_project	and e.id=ei.id_ea and ei.man_days
														and p.`status`='1' 	and p.surveystatus not in ('No Response', 'Cancelled') 
														and p.id not in ( select id_project from surveys_status where surveys_sent='1' and surv_type='Intermediate' )
														group by ei.id_ea	having sum(ei.man_days) > '150' ")->queryAll();			
				$i=0;
				if(count($results)>0){$list_users .="Send survey to the following customers:  <br/>";}
				foreach ($results as $key => $top){		
						if(($top['budgeted']/2) < self::getProjectActualManDays($top['pid'])){
								$i++;
								$list_users .= $i."- Customer : <b>".Customers::getNameById($top['customer_id'])."</b> - Project: <b>".$top['name']."</b>  <br/>";
						}										
				}
		}		
		return $list_users;	
	}
	public static function getProjectsProjectSurveyNotSubmitted($type){
		$list_users="";
				$results = Yii::app()->db->createCommand("select id_project from surveys_status where surveys_sent='1'  and surv_type='".$type."' and (surveys_submitted<>'1' or surveys_submitted is null) and (select count(*) from projects p where p.id= id_project and p.surveystatus in ('No Response', 'Cancelled') ) <1")->queryAll();
				$i=0;
				if(count($results)>0){$list_users .="Following customers did not respond yet: <br/><br/>";}
				foreach ($results as $key => $top){		
					$i++;	$list_users .= $i."- Customer : <b>".Customers::getNameById(Projects::getCustomerByProject($top['id_project']))."</b> - Project: <b>".Projects::getNameById($top['id_project'])."</b> - PM: <b>".Projects::getProjectManager($top['id_project'])." </b><br/>";		
				}		
		return $list_users;	
	}	
	public static function getAllPMBMEmails(){
		$sum=Yii::app()->db->createCommand("select distinct p.project_manager as emess from projects p  where  p.`status`='1' and p.project_manager is not null union select distinct p.business_manager as emess from projects p  where  p.`status`='1' and p.business_manager is not null")->queryAll();
		return $sum;
	}
	public static function getSurveyContactByProject($id,$surv_type){
		$name=Yii::app()->db->createCommand("SELECT sent_to , sent_to_first_name  FROM surveys_status where id_project=".$id." and surveys_sent='1' and surv_type='".$surv_type."'")->queryRow();
		return ucfirst($name['sent_to_first_name'])." ".ucfirst($name['sent_to']);
	}
	public static function getCustomerProjects($id){
	    return Yii::app()->db->createCommand("SELECT * FROM projects WHERE customer_id={$id}")->queryAll();
    }
} ?>