<?php
class Suppliers extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'suppliers';
	}
	public function rules(){
		return array(
			array('name, id_type, main_contact, main_phone, category', 'required'),
			array('id_type ,currencyId, countryId,dolphin_code', 'numerical', 'integerOnly'=>true),
			array('name, bank_name, swift, iban', 'length', 'max'=>50),
			array('category', 'length', 'max'=>15),
			array('emails', 'length', 'max'=>500),
			array('preffered', 'length', 'max'=>3),
			array('main_contact, account_name, city', 'length', 'max'=>100),
			array('main_phone, other_phone', 'length', 'max'=>20),
			array('emails','validateEmails'),
			array('preffered','validatepreffered'),
			array('id, name, id_type, main_contact, main_phone, other_phone, account_name, check,bank_name, swift, iban', 'safe', 'on'=>'search'),
		);
	}
	public function validateEmails(){
	        if(!empty($this->emails)){
	         	$emails= explode(';', $this->emails);    $f=false;
		       	foreach ($emails as $key => $value) {	if(!filter_var(trim($value), FILTER_VALIDATE_EMAIL)) { 	$f=true; }	}
		       	if($f){   $this->addError('emails','Email Address has invalid value.');}
	        }
	}
	public function validatepreffered(){
	        if(!empty($this->preffered) && $this->preffered== 'Yes'){
	        	if($this->category == 'Travel Agency')
	        	{
	        		$result =  Yii::app()->db->createCommand("SELECT count(1) FROM suppliers where category='Travel Agency' and preffered='Yes' and countryId=".$this->countryId." and id!=".$this->id)->queryScalar();
	        		if($result!=0){   $this->addError('preffered','Cannot have more than one Preferred Supplier of this type per country');}
	        	}else{
					$result =  Yii::app()->db->createCommand("SELECT count(1) FROM suppliers where category='".$this->category."' and preffered='Yes' and city='".$this->city."'  and id!=".$this->id)->queryScalar();
	        		if($result!=0){   $this->addError('preffered','Cannot have more than one Preferred Supplier of this type per city');}
	        	}
	        }	        	
	}
	public function relations(){
		return array(
			'idType' => array(self::BELONGS_TO, 'Codelkups', 'id_type'),
			'idCountry' => array(self::BELONGS_TO, 'Codelkups', 'countryId'),
			'idCurrency' => array(self::BELONGS_TO, 'Codelkups', 'currencyId'),
			'check'=> array(self::HAS_ONE, 'SuppliersPrint','id'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Supplier',
			'id_type' => 'Type',
			'main_contact' => 'Main Contact',
			'main_phone' => 'Main Phone',
			'other_phone' => 'Other Phone',
			'account_name' => 'Account Name',
			'bank_name' => 'Bank Name',
			'swift' => 'Swift',
			'iban' => 'IBAN',
			'city' => 'City',
			'check' => 'Check#',
			'dolphin_code' => 'Supplier Code',
			'category'=> 'Category',
			'preffered' => 'Preferred Supplier',
			'emails'=> 'E-mail Address',
			'countryId' => 'Country'
		);
	}	
	public static function getCategories(){		
		return array(			
			'Car Rental' => 'Car Rental',
			'Hotel' => 'Hotel',
			'Office Expenses'=>'Office Expenses',
			'Travel Agency' =>'Travel Agency'  
		); 
	}
	public static function getPreffered(){		
		return array(
			'Yes' =>'Yes'  ,
			'No' => 'No'
		); 
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->with = array('idType');	$criteria->together = true;
		$criteria->compare('name',$this->name,true);	$criteria->compare('id_type',$this->id_type);
		$criteria->compare('main_contact',$this->main_contact,true);
		$criteria->compare('countryId',$this->countryId);
		if(isset($this->check) && $this->check!=''){
			$criteria->join='LEFT JOIN suppliers_print sp ON sp.id_supplier=t.id';
			$criteria->addCondition('sp.check LIKE '.$this->check.'');
		}
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => Utils::getPageSize(),
			),
			'sort'=>array(
				'defaultOrder' => 't.name ASC',
				'attributes' => array(
					'idType.codelkup'=>array(
						'asc'=>'idType.codelkup',
						'desc'=>'idType.codelkup DESC',
					),
					'*',
				),
			),
		));
	}
	public static function getContactsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT id, main_contact FROM suppliers order by main_contact')->queryAll();
		$items = array();
		foreach ($result as $i=>$res){		$items[$i]['label'] = $res['main_contact'];		$items[$i]['id'] = $res['id'];	}
		return $items;
	}
	public static function getNamesAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT id, name FROM suppliers order by name  ')->queryAll();
		$items = array();
		foreach ($result as $i=>$res){	$items[$i]['label'] = $res['name'];	$items[$i]['id'] = $res['id'];	}
		return $items;
	}
	public static function getNamesByChequeAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT s.id, s.name FROM suppliers s,suppliers_print sp where sp.id_supplier=s.id order by s.name')->queryAll();
		$items = array();
		foreach ($result as $i=>$res){	$items[$i]['label'] = $res['name'];	$items[$i]['id'] = $res['id'];	}
		return $items;
	}
	public static function getNameById($id){	$result =  Yii::app()->db->createCommand("SELECT name FROM suppliers Where id = '$id' ")->queryScalar();
		return $result;
	}
	public static function getIdByName($name){
		$result =  Yii::app()->db->createCommand("SELECT id FROM suppliers Where name = '$name' ")->queryScalar();
		return $result;
	}
	public static function getEmailById($id){
		$result =  Yii::app()->db->createCommand("SELECT emails FROM suppliers Where id = ".$id." ")->queryScalar();
		return $result;
	}
	public static function getemails($emails)
  	{
  		return str_replace(";","; \n",$emails);
  	}	
	public static function getDirPathLetter($supplier_id, $model_id){
		$supplier_id = (int)$supplier_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.
			"suppliers".DIRECTORY_SEPARATOR.$supplier_id.DIRECTORY_SEPARATOR.'letter'.DIRECTORY_SEPARATOR;
		if (!is_dir($path)){		mkdir( $path, 0777, true);	chmod( $path, 0777 );	}
		return $path;
	}	
	public static function getTravelSupp($origin=null){
		if($origin != null)
		{
			return  Yii::app()->db->createCommand("SELECT s.id FROM suppliers s,codelkups c WHERE s.category = 'Travel Agency' and s.preffered='Yes' and s.countryId=c.id and c.codelkup='".$origin."' LIMIT 1")->queryScalar();		
			
		}else
		{
			$result =  Yii::app()->db->createCommand("SELECT id, name FROM suppliers WHERE category = 'Travel Agency' order by name ASC")->queryAll();		
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}		
	}
	public static function getHotelSupp($origin=null){
		if($origin != null)
		{
			return  Yii::app()->db->createCommand("SELECT s.id FROM suppliers s,codelkups c WHERE s.category = 'Hotel' and s.preffered='Yes' and s.countryId=c.id and c.codelkup='".$origin."' LIMIT 1")->queryScalar();		
		}else
		{
			$result =  Yii::app()->db->createCommand("SELECT id, name FROM suppliers WHERE category = 'Hotel' order by name ASC")->queryAll();		
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getCarSupp($origin=null){
		if($origin != null)
		{
			return  Yii::app()->db->createCommand("SELECT s.id FROM suppliers s,codelkups c WHERE s.category = 'Car Rental' and s.preffered='Yes' and s.countryId=c.id and c.codelkup='".$origin."' LIMIT 1")->queryScalar();		
		}else
		{
			$result =  Yii::app()->db->createCommand("SELECT id, name FROM suppliers WHERE category = 'Car Rental' order by name ASC")->queryAll();		
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getHotelSuppPerCityDD($origin=null){
		if($origin != null)
		{
			$result=  Yii::app()->db->createCommand("SELECT id, name, preffered FROM suppliers WHERE category = 'Hotel' and city='".$origin."' union all select 'Choose another hotel' as id , 'Choose another hotel' as name, 'No' as preffered order by preffered desc, name ASC")->queryAll();	
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getHotelSuppPerCity($origin=null){
		if($origin != null)
		{
			$result=  Yii::app()->db->createCommand("SELECT s.id FROM suppliers s WHERE s.category = 'Hotel' and s.preffered='Yes' and s.city='".$origin."' LIMIT 1")->queryScalar();		
			if(!empty($result)){ return $result; }else{ return null; }
		}else
		{
			$result =  Yii::app()->db->createCommand("SELECT id, name FROM suppliers WHERE category = 'Hotel' order by name ASC")->queryAll();		
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getTravelSuppPerOriginDD($origin=null){
		if($origin != null)
		{
			if(is_numeric($origin))
			{
				$cname= Codelkups::getCodelkup($origin);
				$result=  Yii::app()->db->createCommand("SELECT s.id, s.name, s.preffered FROM suppliers s,codelkups c WHERE s.category = 'Travel Agency' and ((s.preffered='Yes' and s.countryId=c.id and c.codelkup='".$cname."' ) or s.id = 114) order by preffered desc, name ASC ")->queryAll();	
			}else{
				$result=  Yii::app()->db->createCommand("SELECT s.id, s.name, s.preffered FROM suppliers s,codelkups c WHERE s.category = 'Travel Agency' and ((s.preffered='Yes' and s.countryId=c.id and c.codelkup='".$origin."' ) or s.id = 114) order by preffered desc, name ASC ")->queryAll();	
			}
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getCarSuppPerCityDD($origin=null){
		if($origin != null)
		{
			$result=  Yii::app()->db->createCommand("SELECT id, name, preffered FROM suppliers WHERE category = 'Car Rental' and city='".$origin."' union all select 'Choose another supplier' as id , 'Choose another supplier' as name, 'No' as preffered  order by preffered desc, name ASC ")->queryAll();	
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getCarSuppPerCity($origin=null){
		if($origin != null)
		{
			return  Yii::app()->db->createCommand("SELECT s.id FROM suppliers s WHERE s.category = 'Car Rental' and s.preffered='Yes' and s.city='".$origin."' LIMIT 1")->queryScalar();		
		}else
		{
			$result =  Yii::app()->db->createCommand("SELECT id, name FROM suppliers WHERE category = 'Car Rental' order by name ASC")->queryAll();		
			$users = array();
			foreach ($result as $i => $res){	$users[$res['id']] = $res['name'];	}
			return $users;
		}
	}
	public static function getDirPathCheck($supplier_id, $model_id){
		$supplier_id = (int)$supplier_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.
			"suppliers".DIRECTORY_SEPARATOR.$supplier_id.DIRECTORY_SEPARATOR.'check'.DIRECTORY_SEPARATOR;
		if (!is_dir($path)) {	mkdir( $path, 0777, true);	chmod( $path, 0777 );	}
		return $path;
	}	
	public static function getDirPathCheckMultip($supplier_id){
		$supplier_id = (int)$supplier_id;	
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.
			"suppliers".DIRECTORY_SEPARATOR.$supplier_id.DIRECTORY_SEPARATOR.'check'.DIRECTORY_SEPARATOR;
		if (!is_dir($path)) {	mkdir( $path, 0777, true);	chmod( $path, 0777 );	}
		return $path;
	}
	public function getSuppliersProvider(){
		$criteria=new CDbCriteria;
			$criteria->condition = "(id_supplier = :supplier)";	
			$criteria->params = array(':supplier' => $this->id);	

			if(!isset($_GET['Suppliers']['status']) || (isset($_GET['Suppliers']['status']) && $_GET['Suppliers']['status'] ==1 )){
				$criteria->compare('status',1, false);
			}
	 	// print_r($criteria);exit;
		return new CActiveDataProvider('SuppliersPrint', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'date DESC',  
            ),
		));
	}	
	public function renderNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("suppliers/view", array("id" => $this->id)).'">'.$this->name.'</a>';
	}
}?>