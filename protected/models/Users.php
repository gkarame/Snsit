<?php
class Users extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'users';
	}
	public $password_new;
	public function rules(){
		return array(
			array('firstname, lastname, username, password', 'required'),
			array('password_new', 'required', 'on' => 'create'),
			array('username', 'unique'),
			array('active, type', 'numerical', 'integerOnly'=>true),
			array('firstname, lastname, password, password_new', 'length', 'max'=>50),
			array('username', 'length', 'max'=>25),
			array('id, firstname, lastname, username, password, active, type', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'userHrDetails' => array(self::HAS_ONE, 'UserHrDetails', 'id_user'),
			'userPersonalDetails' => array(self::HAS_ONE, 'UserPersonalDetails', 'id_user'),
			'userVisas' => array(self::HAS_MANY, 'UserVisas', 'id_user'),
			'userGroup' => array(self::HAS_ONE, 'UserGroups', 'id_user'),
			'userWidgets' =>array(self::HAS_MANY,'UserWidgets','user_id'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'firstname' => Yii::t('translations', 'Firstname'),
			'lastname' => Yii::t('translations', 'Lastname'),
			'username' => Yii::t('translations', 'Username'),
			'password' => Yii::t('translations', 'Password'),
			'password_new' => Yii::t('translations', 'Password'),
			'active' => Yii::t('translations', 'Employment Status'),
			'type' => Yii::t('translations', 'Type'),
		);
	}	
	public function beforeSave(){
		if (parent::beforeSave()){
			if ($this->isNewRecord){
                if (!isset($this->type)){
                    $this->type = 0;
                }
			}
			return true;
		}
		return false;
	}	
	public static function getBranchByUser($id_user){
		$Branch =  Yii::app()->db->createCommand("SELECT branch from user_personal_details where id_user=".$id_user."")->queryScalar();
		return $Branch;
	}	
	public function getAttachmentsPath() {
		return Yii::app( )->getBasePath( ).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR
			.'user_attachments'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
	}
	public function getAttachmentsUrl() {
		return Yii::app( )->getBaseUrl(true).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."user_attachments".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
	}	
	public function getHasAttachments() {
		if (!$this->isNewRecord){	return is_dir($this->attachmentsPath);	} 
		return false;
	}	
	protected function beforeValidate() {
        if ($this->isNewRecord) 
            $this->password = $this->password_new;
        return parent::beforeValidate();
    }
    protected function afterValidate(){
        parent::afterValidate();
        if (!empty($this->password_new))
                $this->password = sha1($this->password_new);
    }	
	public function afterSave() {
		parent::afterSave();	    
		Utils::addAttachments('users', Yii::app( )->getBasePath( ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'uploads'
			.DIRECTORY_SEPARATOR.'user_attachments'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
	}    
	public function getFullname(){
	    return $this->firstname.' '.$this->lastname;
	}
	public static function getfirstNameById($id){		 
		return Yii::app()->db->createCommand("Select firstname from users where id=".$id." ")->queryScalar(); 
	}	
	public static function getCredentialsbyId($id){
		if($id!=null){
		$firstname=Yii::app()->db->createCommand("Select CONCAT(LEFT(firstname , 1),LEFT(lastname , 1)) from users where id=".$id." ")->queryScalar();
		return $firstname;
		}else{	return " ";	}
	}
	public static function getNameById($id){
		if($id!=null){
		$firstname=Yii::app()->db->createCommand("Select firstname from users where id=".$id." ")->queryScalar();
		$lastname=Yii::app()->db->createCommand("Select lastname from users where id=".$id." ")->queryScalar();
		$fullname=$firstname." ".$lastname;
		return $fullname;
		 }else{	return " ";	}
	}
	public static function getLineManagers(){
		 return CHtml::listData(self::model()->findAll(array('condition'=>'`active` = 1','order'=>'firstname ASC, lastname ASC')), 'id', 'fullname');
	}
	public static function getStatusList(){		
		return array('0' => 'Inactive', '1' => 'Active'); 
	}
	public function isActiveAsString(){
		return ($this->active == 1) ? Yii::t('translations', 'Active') : Yii::t('translations', 'Inactive');
	}	
	public function isActive(){
		return ($model->active == 0) ? false : true;
	}	
	public function getGroup(){
		return $this->userGroup->group;
	}
	public static function getIban($id_user){
		return Yii::app()->db->createCommand('SELECT iban FROM user_hr_details WHERE id_user='.$id_user.'')->queryScalar();
	}
	public static function getbankacc($id_user){
		return Yii::app()->db->createCommand('SELECT bank_account FROM user_hr_details WHERE id_user='.$id_user.'')->queryScalar();
	}
	public static function checkIfCSOrManagers($id_user){
		return Yii::app()->db->createCommand('SELECT id_group FROM user_groups WHERE id_user='.$id_user.' and  id_group in (34, 14, 25, 9, 20) ')->queryScalar();
	}
	public static function checkIfDirectorOnly($id_user){
		return Yii::app()->db->createCommand('SELECT id_group FROM user_groups WHERE id_user='.$id_user.' and id_group=14 ')->queryScalar();
	}
	public static function checkIfDirector($id_user){
		return Yii::app()->db->createCommand('SELECT id_group FROM user_groups WHERE id_user='.$id_user.' and (id_group=14 or id_group=10) order by id_group desc limit 1')->queryScalar();
	}	
	public static function checkIfTechManager($id_user){
		return Yii::app()->db->createCommand('SELECT id_group FROM user_groups WHERE id_user='.$id_user.' and id_group in (34, 25) limit 1')->queryScalar();
	}
	public static function getUnassignedUsers($id_group){
		return 	Yii::app()->db->createCommand('SELECT u.id, u.username, u.firstname, u.lastname FROM users u 
				WHERE u.id NOT IN (SELECT ug.id_user FROM user_groups ug WHERE ug.id_group = '.(int)$id_group . ') order by u.firstname, u.lastname')->queryAll();
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->with = array( 'userPersonalDetails' );  	$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);	$criteria->compare('active',$this->active);	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			 'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'firstname ASC',            
		        'attributes'=>array(
		            'userPersonalDetails.job_title'=>array(
		                'asc'=>'userPersonalDetails.job_title',
		                'desc'=>'userPersonalDetails.job_title DESC',
		            ),
		            'userPersonalDetails.unit'=>array(
		                'asc'=>'userPersonalDetails.unit',
		                'desc'=>'userPersonalDetails.unit DESC',
		            ),
		            'userPersonalDetails.email'=>array(
		                'asc'=>'userPersonalDetails.email',
		                'desc'=>'userPersonalDetails.email DESC',
		            ),
		            'userPersonalDetails.mobile'=>array(
		                'asc'=>'userPersonalDetails.mobile',
		                'desc'=>'userPersonalDetails.mobile DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}
	public function getPermissions(){
		$query = 'SELECT * FROM permissions LEFT JOIN user_groups on user_groups.id_group=permissions.group_id WHERE user_groups.id_user='.$this->id;
		$results = Yii::app()->db->createCommand($query)->queryAll();	$permissions = array();
		foreach ($results as $result){	$permissions[$result['page']] = $result;	}
		return $permissions;
	}	
	public function getVisas(){
		$criteria=new CDbCriteria;	$criteria->with = array('user');	$criteria->condition = 'id_user = '.($this->id ? $this->id : 0);
		return new CActiveDataProvider('UserVisas', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
		));
	}
	public static function getAllAutocomplete($active = false){
		$query = "SELECT id, firstname, lastname FROM users";
		if ($active){	$query .= " WHERE active = 1 order by firstname, lastname";	}
		$result =  Yii::app()->db->createCommand($query)->queryAll();	$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getAllAutocomplete1($active = false){
		$query = "SELECT id, firstname, lastname FROM users";
		if ($active){	$query .= " WHERE active = 1 order by firstname, lastname";	}
		$result =  Yii::app()->db->createCommand($query)->queryAll();	$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getAllAutocompleteTech(){
		$query = "SELECT id, firstname, lastname FROM users WHERE active = 1 and id in (select id_user from user_groups where id_group in (9,19,18, 32,13,34,25)) order by firstname,lastname";
		$result =  Yii::app()->db->createCommand($query)->queryAll();	$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getAllSelect(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1 and id !=  60 order by firstname , lastname ASC')->queryAll();		
		$users = array();
		foreach ($result as $i => $res){	$users[$res['id']] = $res['firstname'].' '.$res['lastname'];	}
		return $users;
	}
	public static function getUsername($id){
		$user =  Yii::app()->db->createCommand()
    		->select('firstname, lastname')
    		->from('users')
    		->where('id =:id', array(':id'=>$id))
    		->queryRow();
    	if ($user){	return $user['firstname'].' '.$user['lastname'];  	}
    	return '';
	}
	public static function getinitials($id){
		$user =  Yii::app()->db->createCommand()
    		->select('firstname, lastname')
    		->from('users')
    		->where('id =:id', array(':id'=>$id))
    		->queryRow();
    	if ($user){	 $fname= $user['firstname'];
    		 $lname= $user['lastname'];  
    		 return $fname[0].$lname[0];  	}
    	return '';
	}
	public static function getActiveUsesId(){
		return Yii::app()->db->createCommand()
				->select('id')
				->from('users')
				->where('active=:active order by firstname, lastname ASC', array(':active' => 1))
				->queryAll();
	}
	public static function getActiveNotAdminUsesId() {
		return Yii::app()->db->createCommand()
				->select('users.id')
				->from('users')
				->join('user_personal_details', 'user_personal_details.id_user = users.id')
				->where('user_personal_details.sns_admin=0 and active=:active and users.id <> 60 order by users.firstname,users.lastname ASC', array(':active' => 1))
				->queryAll(); 
	}
	public static function getCSManagers(){
		$result = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user in  (SELECT id_user FROM user_groups WHERE  id_group in (34, 14, 25));")->queryAll();
		return $result;
	}
	public static function checkCSManagers($id){
		$result = Yii::app()->db->createCommand("SELECT count(*) FROM user_groups WHERE id_user=".$id." and id_group in (34, 14, 25)")->queryScalar();
		return $result;
	}
	public static function checkLead($id){
		$result = Yii::app()->db->createCommand("SELECT count(*) FROM user_personal_details where (job_title like '%Technical Lead%' or job_title like '%Technical Specialist%') and id_user=".$id."")->queryScalar();
		return $result;
	}
	public static function checkAdminTeam($id){
		$result = Yii::app()->db->createCommand("SELECT count(*) FROM user_groups WHERE id_user=".$id." and id_group=11")->queryScalar();
		return $result;
	}
	public static function getcountActiveNotAdminUsers(){
		$result = Yii::app()->db->createCommand("SELECT count(1) FROM `user_personal_details` upd, users u where u.id=upd.id_user and upd.sns_admin='0' and active='1'")->queryScalar();
		return $result;
	}
	public static function getcountAdminUser($id){
		$result = Yii::app()->db->createCommand("SELECT count(1) FROM `user_personal_details` upd, users u where u.id=upd.id_user and upd.sns_admin='1' and id_user=".$id."")->queryScalar();
		return $result;
	}
	public static function getLoggedUserBranch(){
		return Yii::app()->db->createCommand()
				->select('codelkups.codelkup')
				->from('users')
				->leftJoin('user_personal_details', 'users.id = user_personal_details.id_user')
				->leftJoin('codelkups', 'codelkups.id = user_personal_details.branch')
				->where('users.id=:id', array(':id' => Yii::app()->user->id))
				->queryScalar();
	}
	public static function getUserDefaultTasksById($id_user){
		return Yii::app()->db->createCommand()
				->selectDistinct('default_tasks_group.id_default_task as id_task')
				->from('user_groups')
				->join('default_tasks_group', 'default_tasks_group.id_group = user_groups.id_group')
				->where('user_groups.id_user=:id_user', array('id_user' => $id_user))
				->queryAll();
	}
	public static function getEmailbyID($id){
		return Yii::app()->db->createCommand()
				->select('email')
				->from('user_personal_details')				
				->where('id_user=:id', array(':id' => $id))
				->queryScalar();
	}
	public static function getMobilebyID($id){
		return Yii::app()->db->createCommand()
				->select('mobile')
				->from('user_personal_details')				
				->where('id_user=:id', array(':id' => $id))
				->queryScalar();
	}
	public static function getSkypebyID($id){
		return Yii::app()->db->createCommand()
				->select('skype_id')
				->from('user_personal_details')				
				->where('id_user=:id', array(':id' => $id))
				->queryScalar();
	}
	public static function ValidateGroupAdmin($id){
		return Yii::app()->db->createCommand("select count(1) from user_groups where id_user=".$id." and id_group= 11 ")->queryScalar();
	}
	public static function getIdByName($name){
		$name = explode(' ', $name, 2);
		$result = Yii::app()->db->createCommand("select id from users where firstname like '%".$name[0]."%' and lastname like '%".$name[1]."%'")->queryScalar();
		return $result;
	}
	public static function getIdByNameTrim($name){
		$name = explode(' ', $name, 2);
		$result = Yii::app()->db->createCommand("select id from users where firstname like '%".trim($name[0])."%' and lastname like '%".trim($name[1])."%'")->queryScalar();
		 return $result;
	}
	public static function getUnit($id){
		$result = Yii::app()->db->createCommand("select unit from user_personal_details where id_user=".$id." ")->queryScalar();
		$unit= Codelkups::getCodelkup($result);
		return $unit;
	}	
	public static function getUnitId($id){
		$result = Yii::app()->db->createCommand("select unit from user_personal_details where id_user=".$id." ")->queryScalar();
		return $result;
	}
}?>