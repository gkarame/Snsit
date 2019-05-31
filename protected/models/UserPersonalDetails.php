<?php
class UserPersonalDetails extends CActiveRecord{
	public $file;	public $years;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'user_personal_details';
	}
	public function rules(){
		return array(
			array('id_user, email, line_manager', 'required'),
			array('id_user, line_manager, branch, unit', 'numerical', 'integerOnly'=>true),
			array('gender', 'length', 'max'=>1),
			array('email, nationality, marital_status, job_title, ice_contact, ice_mobile, extension, skype_id, sns_admin, pqa,billable ,performance', 'length', 'max'=>255),
			array('home_address', 'safe'),
			array('email', 'email'),
			array('mobile,annual_leaves','length', 'max'=>20),
			array('birthdate', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy'),
			array('years,id, id_user, gender, birthdate, nationality, marital_status, job_title, branch, unit, line_manager, home_address, mobile, ice_contact, ice_mobile, extension', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'lineManager' => array(self::BELONGS_TO, 'Users', 'line_manager'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'rBranch' => array(self::BELONGS_TO, 'Codelkups', 'branch'),
			'rUnit' => array(self::BELONGS_TO, 'Codelkups', 'unit'),
		);
	}
	public function beforeSave(){
		if (parent::beforeSave()){
			if (!empty($this->birthdate)){
				$this->birthdate = DateTime::createFromFormat('d/m/Y', $this->birthdate)->format('Y-m-d H:i:s');
			}else{
				$this->birthdate = null;
			}
			return true;
		}
		return false;
	}
	public function attributeLabels(){
		return array(
			'id' => Yii::t('translations', 'ID'),
			'id_user' => Yii::t('translations', 'Id User'),
			'gender' => Yii::t('translations', 'Gender'),
			'birthdate' => Yii::t('translations', 'Birthdate'),
			'nationality' => Yii::t('translations', 'Nationality'),
			'marital_status' => Yii::t('translations', 'Marital Status'),
			'job_title' => Yii::t('translations', 'Job Title'),
			'branch' => Yii::t('translations', 'Branch'),
			'unit' => Yii::t('translations', 'Unit'),
			'line_manager' => Yii::t('translations', 'Line Manager'),
			'home_address' => Yii::t('translations', 'Home Address'),
			'mobile' => Yii::t('translations', 'Mobile'),
			'ice_contact' => Yii::t('translations', 'Ice Contact'),
			'ice_mobile' => Yii::t('translations', 'Ice Mobile'),
			'extension' => Yii::t('translations', 'Extension'),
			'annual_leaves' => Yii::t('translations', '# Annual Leaves'),
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_user',$this->id_user);
		$criteria->compare('gender',$this->gender,true);	$criteria->compare('birthdate',$this->birthdate,true);	$criteria->compare('nationality',$this->nationality,true);
		$criteria->compare('marital_status',$this->marital_status,true);	$criteria->compare('job_title',$this->job_title,true);
		$criteria->compare('branch',$this->branch,true);	$criteria->compare('unit',$this->unit,true);
		$criteria->compare('line_manager',$this->line_manager);	$criteria->compare('home_address',$this->home_address,true);
		$criteria->compare('mobile',$this->mobile,true);	$criteria->compare('ice_contact',$this->ice_contact,true);
		$criteria->compare('ice_mobile',$this->ice_mobile,true);	$criteria->compare('extension',$this->extension,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getEmailById($id){
		return Yii::app()->db->createCommand()
							->from('user_personal_details')
    						->select('email')
    						->where('id_user =:id', array(':id'=>(int)$id))
    						->queryScalar();		
	}
	public static function getLineManagerEmailById($id){
		return Yii::app()->db->createCommand("select email from user_personal_details where id_user =(select upd.line_manager from users s , user_personal_details upd where s.id=upd.id_user and s.id='".$id."')")	->queryScalar();		
	}	
	public static function getLineManager($id){
		return Yii::app()->db->createCommand("select upd.line_manager from users s , user_personal_details upd where s.id=upd.id_user and s.id='".$id."'")	->queryScalar();		
	}	
	public static function getUserDetails($id){
		return Yii::app()->db->createCommand()
		->from('user_personal_details')
		->select('*')
		->where('id_user =:id', array(':id'=>(int)$id))
		->queryRow();
	}
	public static function getAnnualLeaves($id){
		return Yii::app()->db->createCommand()
		->from('user_personal_details')
		->select('annual_leaves')
		->where('id_user =:id', array(':id'=>(int)$id))
		->queryScalar();
	}
	public static function getJobTitle($id){
		return Yii::app()->db->createCommand()
		->from('user_personal_details')
		->select('job_title')
		->where('id_user =:id', array(':id'=>(int)$id))
		->queryScalar();
	}	
	public static function getNextBdays($nb = 7){
		$bDays =  Yii::app()->db->createCommand("SELECT user_personal_details.id, users.id as userid, user_personal_details.birthdate, DAYOFYEAR( birthdate ) AS bday, users.firstname, users.lastname
			FROM user_personal_details LEFT JOIN users ON (user_personal_details.id_user = users.id)
			WHERE users.active = 1 AND DAYOFYEAR(curdate()) <= dayofyear(birthdate) AND (DAYOFYEAR(curdate()) +$nb) >= dayofyear(birthdate) ORDER BY bday ASC")->queryAll();
		return $bDays;
	}
	public static function getUsersGroupsEmails($user_id){
		return Yii::app()->db->createCommand("SELECT DISTINCT(user_personal_details.email) FROM user_personal_details LEFT JOIN users ON (user_personal_details.id_user = users.id) WHERE id_user IN (SELECT DISTINCT(user_groups.id_user) FROM user_groups WHERE id_group IN (SELECT ug.id_group FROM user_groups as ug WHERE  ug.id_user = $user_id)) AND users.active = 1")->queryAll();	
	}
	public static function getYears(){
		$years["0"]="";
		for ($i=date('Y', strtotime('-5 years'));$i<=2050;$i++)	{  $years["{$i}"]="{$i}";	}
		return $years;                   
	}
	public static function getAllAutocomplete($active = false){
		$query = "SELECT id, firstname, lastname FROM users";
		if ($active){	$query .= " WHERE active = 1 order by firstname ASC";	}
		$result =  Yii::app()->db->createCommand($query)->queryAll();	$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
	public static function getAllUnit(){
	   $result = Yii::app()->db->createCommand("SELECT id , codelkup FROM codelkups where id_codelist='12' and id in('116','117','513','529','586','1086') order by codelkup,id ")->queryAll();	
	   $unit = array();
	   	foreach ($result as $i => $res){	$unit[$i]['label']= $res['codelkup'];	$unit[$i]['id'] = $res['codelkup'];	}
		return $unit;
	}
	public static function getTech(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('116','117','1086') and u.active='1'")->queryAll();	
	}
	public static function getPS(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('116') and u.active='1'")->queryAll();	
	}
	public static function getCS(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('117') and u.active='1'")->queryAll();	
	}
	public static function getCoreTech(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('1086') and u.active='1'")->queryAll();	
	}	
	public static function getOps(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('513') and u.active='1'")->queryAll();	
	}
	public static function getAllLineManagers(){
		return Yii::app()->db->createCommand("select distinct us.line_manager as linem from user_personal_details us  , users u where us.id_user=u.id and u.active='1'")->queryAll();	
	}	
	public static function getTechAll(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('116','117','1086') ")->queryAll();	
	}
	public static function getPSAll(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('116') ")->queryAll();	
	}
	public static function getCSAll(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('117') ")->queryAll();	
	}
	public static function getCoreTechAll(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('1086') ")->queryAll();	
	}	
	public static function getOpsAll(){
		return Yii::app()->db->createCommand("SELECT u.id FROM user_personal_details upd , users u where u.id=upd.id_user and upd.unit in('513') ")->queryAll();	
	}
	public static function getAllLineManagersAll(){
		return Yii::app()->db->createCommand("select distinct us.line_manager as linem from user_personal_details us  , users u where us.id_user=u.id ")->queryAll();	
	}	
}?>