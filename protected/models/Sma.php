<?php
class Sma extends CActiveRecord{	 
	const STATUS_NEW = 0;	const STATUS_APPROVED = 1;	const STATUS_REOPENED = 2;	const STATUS_CLOSED = 3;	public $statuss, $prio;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'sma';
	}
	public function rules(){
		return array(
			array('id_no, id_customer,sma_month, sma_year, instance, adddate,assigned_to ,status, id_maintenance', 'required'),
			array('id_customer, status, sma_month, id_maintenance,sma_year,assigned_to', 'numerical', 'integerOnly'=>true),
			array('id_no', 'length', 'max'=>5),
			array('instance', 'length', 'max'=>50),
			array('notes,instructions', 'length', 'max'=>1000),
			array('displayClosed', 'length', 'max'=>15),
			array('file', 'safe'),
			array('status','updateStatusValidation'),
			array('assigned_to','assigned_toValidation'),
			array('id_no, id_customer,assigned_to, close_date,status, sma_month, sma_year, instance', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'assigned_to'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_no' => 'SSN',
			'sma_month' => 'Month',
			'sma_year' => 'Year',
			'id_customer' => 'Customer',
			'instance' => 'Instance',
			'status' => 'Status',
			'adddate' => 'Creation Date',
			'close_date' => 'Closure Date',
			'notes'=>'Notes',
			'instructions'=>'Special Instructions',
			'close_date'=> 'Closed Date'
		);
	}
	public function isEditable(){	return !in_array($this->status, array(Sma::STATUS_CLOSED));	}
	public function search($export = null,$circle=null){
		$criteria=new CDbCriteria;	$criteria->select = array('*');	$criteria->with = array('idCustomer');
		$criteria->compare('id_no', $this->id_no,true);	$criteria->compare('t.status', $this->status);		
		if (isset($this->assigned_to) && $this->assigned_to != null)
		{	$last = explode("  ", $this->assigned_to);
			$criteria->join='LEFT JOIN users ON users.id=t.assigned_to';
			$criteria->addCondition('users.firstname LIKE :tn');
			$criteria->params[':tn']='%'.substr($this->assigned_to,0,strrpos($this->assigned_to,"  ")).'%';
			$criteria->addCondition('users.lastname LIKE :tr');
			$criteria->params[':tr']='%'.$last[1].'%';
		}
		if (isset(Yii::app()->user->customer_id)){	$criteria->compare('idCustomer.name',Customers::getNameById(Yii::app()->user->customer_id));
		}else{	$criteria->compare('idCustomer.name',$this->id_customer); }
		if (isset($this->status) && $this->status != "" ){	$criteria->compare('t.status', $this->status);	}
		else {	$status = "(".Sma::STATUS_NEW.','.Sma::STATUS_APPROVED.','.Sma::STATUS_CLOSED.','.Sma::STATUS_REOPENED.')';
			$criteria->addCondition('t.status IN '."$status");
		}
     	$criteria->compare('sma_month',$this->sma_month);        $criteria->compare('sma_year',$this->sma_year);	$criteria->compare('t.instance', $this->instance);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => ($export != null || $circle != null) ? 1000:Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder' => 't.id DESC',
            	'attributes'=>array(             
		    ),
		)));
	}	
	public function assigned_toValidation(){
		if ( $this->assigned_to == 0 ){	    $this->addError('assigned_to','Assigned to is required.');	}
	}
	public function updateStatusValidation(){
		if ( $this->status == 3 ){
		    if(empty($this->file) || $this->file=='' || $this->file==' '){		    	
		    	  $this->addError('status','SMA cannot be closed without attachement.');
		    }else {
		    	$result =  Yii::app()->db->createCommand("SELECT count(1) FROM sma_actions WHERE id_sma=".$this->id." and status not in (3,5) and severity=3")->queryScalar();
		    	if($result>0){
		    		 $this->addError('notes','SMA cannot be closed with open actions of Critical severity.');
		    	}		
		    }
		}
	}
	public static function getMonthName($mon){
		 $monthName = date("F", mktime(0, 0, 0, $mon, 10));	 return $monthName.' ';
	}
	public static function getStatusListUser(){
		return array(
			self::STATUS_APPROVED => 'Approved', 
			self::STATUS_CLOSED => 'Closed'			
		); 
	}
	public static function getStatusListDropDown(){
		return array(
				self::STATUS_NEW => 'New',
				self::STATUS_APPROVED => 'Approved', 
				self::STATUS_CLOSED => 'Closed',
				self::STATUS_REOPENED => 'Reopened',
		);
	}
	public static function getStatusList(){		
		return array(
			self::STATUS_NEW => 'New',
			self::STATUS_APPROVED => 'Approved', 
			self::STATUS_CLOSED => 'Closed',
			self::STATUS_REOPENED => 'Reopened' 			
		); 
	}	
	public static function getStatusList2(){		
		return array(
			self::STATUS_NEW => 'New',
			self::STATUS_APPROVED => 'Approved', 
			self::STATUS_CLOSED => 'Closed',
			self::STATUS_REOPENED => 'Reopened' 
		); 
	}	
	public static function getStatusLabel($value){		$list = self::getStatusList();		return $list[$value];	}	
	public static function formatSol($str){
  		$lines = explode("\n", $str);	$newstr='';
  		foreach ($lines as $key => $line) {
  			if($key >1)	{
  				$newstr= $newstr.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$line;
  			}else{	$newstr= $newstr.''.$line;	}
  		}
		return $newstr;
  	}
  	public static function getrecep($emails)
  	{
  		return str_replace(",",", \n",$emails);
  	}
  	public static function formatdescr($str){
  		$lines = explode("\n", $str);		$newstr='';
  		foreach ($lines as $key => $line) {
  			if($key >1) {
  				$newstr= $newstr.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$line;
  			}else{	$newstr= $newstr.''.$line; }
  		}
		return $newstr;
  	}	
	public static function getStatusListCoded(){
		return array(
			self::STATUS_NEW => 'new',
			self::STATUS_APPROVED => 'approved', 
			self::STATUS_CLOSED => 'closed',
			self::STATUS_REOPENED => 'reopened' 
		); 
	}
	public static function getStatusLabelCoded($value){		$list = self::getStatusListCoded();		return $list[$value];	}
	public static function getStatusIdCoded($value){	$list = self::getStatusListCoded();		return array_search($value, $list);	}	
	public function renderSRNumber(){	echo '<a class="show_link" href="'.Yii::app()->createUrl("sma/view", array("id" => $this->id)).'">'.$this->id_no.'</a>';
	}	
	public static function getAllUsers($id_support_desk,$id_user){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1')->queryAll();
		$users = array();	$users[''] = ' ';
		foreach ($result as $i => $res)	{		$users[$res['id']] = $res['firstname'].'  '.$res['lastname'];	}
		if(GroupPermissions::checkPermissions('supportdesk-list','write')){
			return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_support_desk.','."2".')',
		    	'style'=>'width:190px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:190px;border:none;'
		    ));
	    }
	}	
	public static function getSmaUsers($id,$id_user){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 and users.id in (select id_user from user_groups where id_group in (9,12,13,19,18) and id_user <>20) order by users.firstname, users.lastname')->queryAll();
		$users = array();	$users[''] = ' ';
		foreach ($result as $i => $res){ $users[$res['id']] = $res['firstname'].'  '.$res['lastname'];	}
		if(GroupPermissions::checkPermissions('supportdesk-list','write')){
			return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeAssigned('."value".','. $id.','."2".')',
		    	'style'=>'width:190px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:190px;border:none;'
		    ));
	    }
	}	
	public static function getDirPath($customer_id, $model_id){
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."smas".DIRECTORY_SEPARATOR.$model_id.DIRECTORY_SEPARATOR;
	 	if (!is_dir( $path)){  mkdir( $path, 0777, true);  chmod( $path, 0777 );    }
		return $path; 
	}
	public function getFile($path = false, $uploaded = false){		
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."smas". DIRECTORY_SEPARATOR .$this->id. DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'smas'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {	$filePath .= $this->file;	$fileName .= $this->file;	}
			else{	return null;	}
		}else {	$filePath .= 'SMA_'.$this->id_no.'.pdf';	$fileName .= 'SMA_'.$this->id_no.'.pdf';	}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand("SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN maintenance ON maintenance.customer=customers.id where maintenance.support_service=501 and maintenance.status='Active' order by  customers.name")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id']; }
		return $customers;
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res)	{	$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public static function getSMAInstancesAutocomplete(){
		$result =  Yii::app()->db->createCommand("SELECT DISTINCT(sma_instances) FROM maintenance where support_service=501 and status='Active' order by sma_instances")->queryAll();
		$sma_instances = array();	$counter=0;
		foreach ($result as $res){
			$instances= explode(',', $res['sma_instances']);
			foreach ($instances as $key => $value) {	$sma_instances[$counter]['label'] = trim($value);	$sma_instances[$counter]['id'] = $counter;	$counter++;	}
		}
		return $sma_instances;
	}	
	public static function getMonths(){
		$monthNames = array('','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');		
		for ($i=1;$i<=12;$i++){		$months["{$i}"]=Yii::t('default',$monthNames[$i]);   }
		return $months;
	}	 
	public static function getYears(){
		$start_year = date('Y',strtotime('now - 10 year'));	$end_year = date('Y',strtotime('now + 20 year'));
		for ($i=$start_year;$i<=$end_year;$i++){   	$years["{$i}"]="{$i}";	}		
		return $years;                   
	}   	
	public static function getCustomerEmailsSMA($sma){
		return  Yii::app()->db->createCommand('SELECT sma_recipients FROM maintenance WHERE id_maintenance=(select id_maintenance from sma where id='.$sma.') ')->queryScalar();		
	}
	public static function getSMAInstance($sma){ return  Yii::app()->db->createCommand('SELECT instance FROM sma where id='.$sma.'')->queryScalar(); }
	public static function getStatusCheck($sma){	$model = Sma::model()->findByPk($sma);	return  Yii::app()->db->createCommand("SELECT count(1) FROM sma WHERE id_customer=".$model->id_customer." and instance='".$model->instance."' and status!=3 ")->queryScalar();
	}
	public static function getCustomerSMA($sma){	return Yii::app()->db->createCommand("SELECT customer FROM maintenance WHERE id_maintenance=(select id_maintenance from sma where id=".$sma.")")->queryScalar();
	}
	public static function getassignedSMA($sma){	return  Yii::app()->db->createCommand('SELECT assigned_to FROM sma WHERE id='.$sma.' ')->queryScalar();	}
	public static function checkUserAssignment($sma, $user){
		$model = Sma::model()->findByPk($sma);
		return  Yii::app()->db->createCommand("SELECT count(1) FROM sma WHERE assigned_to=".$user." and id_customer=".$model->id_customer." and instance='".$model->instance."' and status!=3 ")->queryScalar();		
	}
	public static function checkUserAssignmentEdit($sma, $user){
		$model = Sma::model()->findByPk($sma);
		return  Yii::app()->db->createCommand("SELECT count(1) FROM sma WHERE assigned_to=".$user." and id_customer=".$model->id_customer." and instance='".$model->instance."' ")->queryScalar();		
	}	
	public static function getAllActiveUsers(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1 order by firstname,lastname')->queryAll();		
		$users = array();	$users[0] = ' ';
		foreach ($result as $i => $res)	{	$users[$res['id']] = $res['firstname'].' '.$res['lastname'];	}
		return $users;
	}
} ?>