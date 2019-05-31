<?php
class Internal extends CActiveRecord{
	public $customErrors = array();	public $customer_name;	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'internal';
	}
	public function rules(){
		return array(
			array('name, project_manager,business_manager, eta, estimated_effort', 'required'),
			array('project_manager,business_manager, status', 'numerical', 'integerOnly'=>true),
			array('project_manager, business_manager', 'exist', 'attributeName' => 'id', 'className' => 'Users', 'allowEmpty'=>true),
			array('estimated_effort', 'numerical', 'integerOnly'=>false),
			array('name', 'length', 'max'=>500),
			array('notes, recipients', 'length', 'max'=>1000),
			array('eta', 'length', 'max'=>10),
			array('name, project_manager,business_manager, status,notes', 'safe', 'on'=>'search')
		);
	}
	 	

	public function relations(){
		return array('projectManager' => array(self::BELONGS_TO, 'Users', 'project_manager'),
			'businessManager' => array(self::BELONGS_TO, 'Users', 'business_manager'),);
	}
	public static function getPmOpsAssigned($project){	
		$pm= Internal::getProjectManagerInitials($project);
		$ops = Yii::app()->db->createCommand("SELECT distinct(uta.id_user) FROM internal p , internal_tasks pt , user_internal uta WHERE uta.id_task=pt.id and p.internal=pt.id_internal and p.id=".$project." and uta.id_user!= p.project_manager and uta.id_user!= p.business_manager and uta.id_user in (select id_user from user_groups where id_group in (12,22,27)) ")->queryAll();
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
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public static function gettaskitems($id){
		return Yii::app()->db->createCommand()
    		->select('*')
    		->from('internal_tasks')
    		->where('id_internal =:id', array(':id'=>$id))
    		->order('description ASC')
    		->queryAll();
	}
	 public static function sendEmailNew($model){
		$notif = EmailNotifications::getNotificationByUniqueName('new_internal');
		if ($notif != NULL){
			$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/>Please note that a new internal project has been added:<br/><br/>";
            $message .= "- <b>Name:</b> ".'<a href="'.Yii::app()->createAbsoluteUrl('internal/view', array('id'=>$model->id)).'">'.$model->name.'</a>';
           	$message .= "<br/>- <b>PM:</b> ".Users::getNameById($model->project_manager);
			$message .= "<br/>- <b>BM:</b> ".Users::getNameById($model->business_manager);
           	$message .= "<br/>- <b>ETA:</b> ".$model->eta;
           	$message .= "<br/>- <b>EMDs:</b> ".$model->estimated_effort;
			$message.= "<br/><br/>Best Regards,<br/>SNSit";

	        $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
            
			$emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
            foreach($emails as $email)	{
				//Yii::app()->mailer->AddCcs($email);
				Yii::app()->mailer->AddAddress($email);
			}
			
			if(!empty($model->recipients))
            {
                	$recipients =  explode(',', $model->recipients);
	                foreach($recipients as $recipient){
						$emr = UserPersonalDetails::getEmailById($recipient);
						if (!empty($emr)) {
		                 //   Yii::app()->mailer->AddCCs($emr);
							Yii::app()->mailer->AddAddress($emr);
		                }
					}
			}

			$email_pm=UserPersonalDetails::getEmailById($model->project_manager);
			Yii::app()->mailer->AddAddress($email_pm);

			$email_bm=UserPersonalDetails::getEmailById($model->business_manager);
			Yii::app()->mailer->AddAddress($email_bm);

            Yii::app()->mailer->Subject =  'New Internal Project';
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);     
    	}	
    }
	public function getTasks(){
		$criteria=new CDbCriteria;	$criteria->condition = 'id_internal = '.($this->id ? $this->id : 0);
		//print_r($_GET['InternalTasks']['description'])	;exit;
		if(isset( $_GET['InternalTasks']['priority'])){
			$criteria->compare('priority',$_GET['InternalTasks']['priority']);	

		}
		if(isset( $_GET['InternalTasks']['status'])){
			$status= $_GET['InternalTasks']['status'];	
			$status= implode(",",$status);	
			if(!empty(trim($status)) || $status == '0')
			{	$criteria->addCondition('status in ('.$status.')');	}

		}
		if(isset( $_GET['InternalTasks']['description'])){
			$criteria->compare('description',$_GET['InternalTasks']['description'],true);	

		}
		return new CActiveDataProvider('InternalTasks', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'t.id ASC',  
            ),
			));
	}
	public function attributeLabels(){
		return array(
			'name' => 'Name',
			'project_manager' => 'PM',
			'business_manager' => 'BM',
			'eta' => 'ETD',
			'estimated_effort' => 'Total MDs',
			'status' => 'Status',
			'adddate' => 'Creation Date',
			'close_date' =>'Actual Close Date'
		);
	}
	public static function getProjectManager($id){
		$pm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `internal` p,users u where p.id=".$id." and p.project_manager=u.id;")->queryRow();
		if (!empty($pm)){ $manager=$pm['first']." ".$pm['last']; }else{	$manager=" "; }		
		return $manager;
	}
		
	public static function getBusinessManager($id){
		$bm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `internal` p,users u where p.id=".$id." and p.business_manager=u.id;")->queryRow();
		if (!empty($bm)){ $manager=$bm['first']." ".$bm['last']; }else{	$manager=" "; }		
		return $manager;
	}


	public static function getBusinessManagerInitials($id){
		$bm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `internal` p,users u where p.id=".$id." and p.business_manager=u.id;")->queryRow();
		if (!empty($bm)){ 
			$fname= $bm['first'];
			$lname=  $bm['last'];
			$manager=$fname[0]."".$lname[0]; 
		}else{	
			$manager=""; 

		}		
		return $manager;
	}

	public static function getProjectManagerInitials($id){
		$pm= Yii::app()->db->createCommand("SELECT u.firstname as first,u.lastname as last FROM `internal` p,users u where p.id=".$id." and p.project_manager=u.id;")->queryRow();
		if (!empty($pm)){ 
			$fname= $pm['first'];
			$lname=  $pm['last'];
			$manager=$fname[0]."".$lname[0]; 
		}else{	
			$manager=""; 

		}		
		return $manager;
	}
	public static function getlastupdate($internal){
		$sum = Yii::app()->db->createCommand("SELECT DATE_FORMAT((MAX(close_date)), \"%d/%m/%Y\") FROM internal_tasks WHERE id_internal=".$internal." LIMIT 1")->queryScalar();
		if(empty($sum)){ return Yii::app()->db->createCommand("SELECT DATE_FORMAT(adddate, \"%d/%m/%Y\") FROM internal WHERE id=".$internal." LIMIT 1")->queryScalar(); }
		return $sum;
	}	
	public static function getlastClosed($internal){
		$sum = Yii::app()->db->createCommand("SELECT DATE_FORMAT((MAX(close_date)), \"%d/%m/%Y\") FROM internal_tasks WHERE id_internal=".$internal." LIMIT 1")->queryScalar();
		if(empty($sum)){ return 'N/A'; }
		return $sum;
	}	
	public static function getTasksEMDs($internal){
		$sum = Yii::app()->db->createCommand("SELECT SUM(estimated_effort) FROM internal_tasks WHERE id_internal=".$internal." ")->queryScalar();
		if(empty($sum)){ return 0;  }
		return $sum;
	}
	public static function getUsersNames($ids)
	{
		if(!empty($ids))
		{
			$all= Yii::app()->db->createCommand("SELECT CONCAT(firstname,' ',lastname) as name from users where id IN (".$ids.")")->queryAll();
			$names= implode(', ', array_column($all, 'name'));
			return $names;
		}else{
			return '';
		}
	}
	public static function getNameById($id){
		return Yii::app()->db->createCommand("SELECT name FROM internal where id=".$id)->queryScalar();
	}
	public static function validateallTasksclosed($id){
		return Yii::app()->db->createCommand("SELECT count(1) FROM internal_tasks where id_internal=".$id." and status !=3")->queryScalar();
	}
	public static function getTimeSpentPerProject($internal){
		$sum = Yii::app()->db->createCommand("SELECT SUM(amount)/8 FROM user_time WHERE id_task in (select id from internal_tasks where id_internal=".$internal." ) and `default`='3'")->queryScalar();
		if(empty($sum)){ return 0;  }
		return $sum;
	}

	public static function getUsersAutocompleteDS(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN internal ON users.id=internal.project_manager order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];	$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getAllUsers(){
		$result =  Yii::app()->db->createCommand("SELECT DISTINCT users.id, users.firstname, users.lastname FROM users where users.active=1 order by users.firstname, users.lastname")->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getAssignedUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN user_internal ON users.id=user_internal.id_user order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getStatusLabelDrop(){
		for ($i=0; $i <3; $i++) { $list[$i]=self::getStatusLabel($i);}		
		return $list;
	} 
	public static function getStatusLabel($value){
		$list = self::getStatusList();
		return $list[$value];
	}
	public static function getStatusList(){		
		return array(			
			0 => 'Active',
			1 => 'Closed',
			2 => 'Cancelled'
		); 
	}
	public static function getStatusCode($str)
	{
		switch ($str) {
			case 'Active':
				return 0;
				break;
			case 'Closed':
				return 1;
				break;
			case 'Cancelled':
				return 2;
				break;
			default:
				return '';
				break;
		}
	}

	public static function getAssignUsersPerProject($id){
		return 	Yii::app()->db->createCommand("SELECT Distinct(u.id) as id, concat(u.firstname,' ', u.lastname) as name FROM users u left join user_internal on user_internal.id_user=u.id where user_internal.id_task in (select i.id from internal_tasks i where i.id_internal=".$id.") order by firstname")->queryAll();
	}
	public static function getAllTasks($id){
			$criteria=new CDbCriteria;	
			$criteria->condition = 'id_internal = '.($id ? $id : 0);	
			return new CActiveDataProvider('InternalTasks', array(
				'criteria' => $criteria,
				'pagination'=>array(
	                'pageSize' => Utils::getPageSize(),
	            ),
	             'sort'=>array(
	    			'defaultOrder'=>'id ASC',  
	            ),
			));		
	}

	public function search($selector = null){
		$criteria=new CDbCriteria;

 		if (isset($this->name)  && $this->name !=""){
			$criteria->compare('t.name', $this->name, true);
		}
		
		if (isset($this->status) && $this->status !=""){
			$code= self::getStatusCode($this->status);
			$criteria->compare('t.status', $code, false);
		}
		if (isset($this->project_manager) && $this->project_manager !=""){
			$iduser = Users::getIdByName($this->project_manager);
			$criteria->compare('t.project_manager', $iduser, false);
		}		
		if (isset($this->notes) && $this->notes !=""){
			$user= Users::getIdByName($this->notes);
			if(isset($user))
			{
				$criteria->addCondition(" t.id in (select i.id_internal from internal_tasks i LEFT JOIN user_internal u ON u.id_task=i.id  where u.id_user=".$user." )");
			}
		}
		
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => ($selector != null)?1000:Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder' => 'name ASC'
		    ),
		));
	}

} ?>