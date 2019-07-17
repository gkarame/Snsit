<?php 
class InstallationrequestsProducts extends CActiveRecord {
	CONST STATUS_PENDING = 0;	CONST STATUS_CLOSED = 1;	CONST STATUS_CANCELLED = 2;
	CONST AUTHENTICATION_AD = 1;	CONST AUTHENTICATION_DB = 2; CONST AUTHENTICATION_HYBRID  = 3;	CONST REPORTING_BIRT = 1;	CONST REPORTING_INFOR = 2;
	CONST DB_TYPE_MSSQL = 2;	CONST DB_TYPE_ORACLE = 3;
	CONST DB_COLLATION_ARABIC = 1;	CONST DB_COLLATION_LATIN = 2;	CONST DB_COLLATION_FRENCH = 3;
	CONST LANGUAGE_PACK_ARABIC = 0;	CONST LANGUAGE_PACK_Portuguese = 1;	CONST LANGUAGE_PACK_Dutch = 2;	CONST LANGUAGE_PACK_English = 3;	CONST LANGUAGE_PACK_French = 4;
	CONST LANGUAGE_PACK_German = 5;	CONST LANGUAGE_PACK_Italian = 6;	CONST LANGUAGE_PACK_Japanese = 7;	CONST LANGUAGE_PACK_Polish = 8;	CONST LANGUAGE_PACK_Russian = 9;
	CONST LANGUAGE_PACK_Simplified_Chinese = 10;	CONST LANGUAGE_PACK_Thai = 11;	CONST LANGUAGE_PACK_Spanish = 12;	CONST LANGUAGE_PACK_Swedish = 13;	CONST LANGUAGE_PACK_Traditional_Chinese = 14;
	CONST LICENSE_SNS = 1;	CONST LICENSE_INFOR = 2;	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'installation_requests_products';
	}
	public function rules(){
		return array(
			array('id_product,id_ir', 'required'),
			array('id_product,id_ir,language_pack,license_type,version,dbtype,number_of_nodes,db_collation,status, number_of_schemas, authentication,reporting_type', 'numerical', 'integerOnly'=>true),
			array('notes','length','max'=>150),
			array('id_product,id_ir', 'safe', 'on'=>'search'),
			);
	}
	public function relations(){
		return array(
			'Installationrequest' => array(self::BELONGS_TO, 'InstallationRequests', 'id_ir'),
			);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_product',$this->id_product);
		$criteria->compare('id_ir',$this->id_ir);	$criteria->compare('number_of_nodes',$this->id_ir);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function renderProductName(){
		if($this->id_product == 64){	echo '<a class="show_link" href="'.Yii::app()->createUrl("installationrequests/createmodelinfo", array("id" => $this->id)).'">'.Codelkups::getCodelkup($this->id_product).'</a>';
		}else{
			echo '<span style="color:#A9A9A9">'.Codelkups::getCodelkup($this->id_product).'</span>';
		}
	}
	public static function isInfoFilled($id){
		$info = Yii::app()->db->createCommand("select id from installation_requests_info where id_irprd = ".$id)->queryScalar();
		if($info != null)
			return 'Yes';
		else 
			return 'No';
	}
	public static function getItemsLabels(){
		$labels['id_product'] = 'Product';		$labels['product_name'] = 'Product Name';		$labels['number_of_nodes'] = 'Number of Nodes';
		$labels['id_ir'] = 'IR #';	$labels['version'] = 'Version';		$labels['dbtype'] = 'DB Type';	
	}
	public function attributelabels(){
		return array(
			'id_product' => 'Product',
			'db_collation' => 'DB Collation',
			'number_of_schemas' => '# Schemas',
			'authentication' => 'Authentication',
			'reporting_type' => 'Reporting',
			'number_of_nodes' => '# Nodes',
			'id_ir' => 'IR #',
			'status' => 'Status',
			'notes' => 'Notes',
			'version' => 'Version',
			'dbtype' => 'DB Type'
		);
	}
	public static function getStatusList(){
		return array(self::STATUS_PENDING => 'Pending',
				self::STATUS_CLOSED => 'Closed',
				self::STATUS_CANCELLED => 'Canceled');
	}
	public static function getStatusLabel($s){		$list = self::getStatusList();		return $list[$s];}
	public static function getStatusDropDown($id,$model_status,$status){
		$status_list = self::getStatusList();
		if(GroupPermissions::checkPermissions('ir-assign-installationrequests','write')){
	    	return CHtml::dropDownlist('status', $status, $status_list, array(
		        'class'     => 'status',
		        'disabled' => ($model_status == InstallationRequests::STATUS_COMPLETED)?true:false,
		    	'onchange'=>'changeProductStatus('."value".','. $id.')',
		    	'style'=>'width:90px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('status', $status, $status_list, array(
		        'class'     => 'status',
		        'disabled' => true,
		    	'onchange'=>'changeProductStatus('."value".','. $id.')',
		    	'style'=>'width:90px;border:none;'
		    ));
	    }
	}	
	public static function getStatusList2($current_status = null){
		switch ($current_status) {
			case self::STATUS_PENDING:
				$statuses = array(
					self::STATUS_PENDING => 'Pending',
					self::STATUS_CLOSED => 'Closed',
					self::STATUS_CANCELLED => 'Canceled');
				break;
			case self::STATUS_CLOSED:
				$statuses = array(
					self::STATUS_CLOSED => 'Closed');
				break;			
			default:
				$statuses = array(
					self::STATUS_PENDING => 'Pending',
					self::STATUS_CLOSED => 'Closed',
					self::STATUS_CANCELLED => 'Canceled');
				break;
		}
	return $statuses;
	}
	public static function getNameProd($id){
	return Yii::app()->db->createCommand("select codelkup from codelkups where id=(select product from maintenance where id_maintenance=".$id.")")->queryScalar();
	}	
	public static function getidProd($id){
	return Yii::app()->db->createCommand("select product from maintenance where id_maintenance=".$id."")->queryScalar();	
	}
	public static function getProductsList($id_ir){
    	$idcustomer = Yii::app()->db->createCommand("select customer from installation_requests where id =".$id_ir)->queryScalar();
		$products = array();
		$results = Yii::app()->db->createCommand("select distinct maintenance.id_maintenance,maintenance.product, c1.codelkup as product_name from maintenance join codelkups c1 on maintenance.product = c1.id
		join customers on maintenance.customer = customers.id 
		where maintenance.customer = ".$idcustomer." 
		and not EXISTS (select 1 from maintenance m join installation_requests_products irp on irp.id_product =  m.id_maintenance where irp.id_ir =".$id_ir."  AND maintenance.product=m.product)
		group by maintenance.product")->queryAll();
		if(empty($results)|| $idcustomer==177){
			$results = Yii::app()->db->createCommand("select 0 as id_maintenance, id as product, codelkup as product_name from codelkups where id_codelist=3")->queryAll();
		}
		foreach ($results as $i => $res){
			$products[$res['id_maintenance'].'-'.$res['product']] = $res['product_name'];
		}
       	return $products;
	}
	public static function checkMaint($id_ir){
    	$idcustomer = Yii::app()->db->createCommand("select customer from installation_requests where id =".$id_ir)->queryScalar();
		$products = array();
		$results = Yii::app()->db->createCommand("select distinct maintenance.id_maintenance,maintenance.product, c1.codelkup as product_name from maintenance join codelkups c1 on maintenance.product = c1.id
		join customers on maintenance.customer = customers.id 
		where maintenance.customer = ".$idcustomer." 
		and not EXISTS (select 1 from maintenance m join installation_requests_products irp on irp.id_product =  m.id_maintenance where irp.id_ir =".$id_ir."  AND maintenance.product=m.product)
		group by maintenance.product")->queryAll();
		if(empty($results)|| $idcustomer==177){	return true;}else{	return false;} 
	}	
	public static function getVersionList($id_ir){
    	$idcustomer = Yii::app()->db->createCommand("select customer from installation_requests where id =".$id_ir)->queryScalar();
		$products = array();
		$results = Yii::app()->db->createCommand("select distinct maintenance.soft_version from maintenance join codelkups c1 on maintenance.product = c1.id
		join customers on maintenance.customer = customers.id 
		where maintenance.customer = ".$idcustomer." and maintenance.product =64 
		and not EXISTS (select 1 from maintenance m join installation_requests_products irp on irp.id_product =  m.id_maintenance where irp.id_ir =".$id_ir."  AND maintenance.product=m.product)
		group by maintenance.product")->queryAll();
		if(empty($results)|| $idcustomer==177){
			$products=Codelkups::getCodelkupsDropDown('soft_version');
		}else{
			foreach ($results as $i => $res){	$products[$res['soft_version']] = Codelkups::getCodelkup($res['soft_version']);		}
		}
       	return $products;
	}
	public static function getdbtypeList($id_ir){
		$idcustomer = Yii::app()->db->createCommand("select customer from installation_requests where id =".$id_ir)->queryScalar();
    	$products = array();
		$results = Yii::app()->db->createCommand("select distinct maintenance.wms_db_type from maintenance join codelkups c1 on maintenance.product = c1.id
		join customers on maintenance.customer = customers.id 
		where maintenance.customer = ".$idcustomer." and maintenance.product =64 
		and not EXISTS (select 1 from maintenance m join installation_requests_products irp on irp.id_product =  m.id_maintenance where irp.id_ir =".$id_ir."  AND maintenance.product=m.product)
		group by maintenance.product")->queryAll();
		if(empty($results)|| $idcustomer==177){	$products=Codelkups::getCodelkupsDropDown('wms_db_type');
		}else{
				foreach ($results as $i => $res){	$products[$res['wms_db_type']] = Codelkups::getCodelkup($res['wms_db_type']);	}
		}
       	return $products;
	}
	public static function getDBCollationList(){
		return array(
			self::DB_COLLATION_ARABIC => 'Arabic',
			self::DB_COLLATION_LATIN => 'Latin',
			self::DB_COLLATION_FRENCH => 'French'
			);
	}
	public static function getDBCollationLabel($id =null){
		if($id ==null)
			return "";
		$list = self::getDBCollationList();
		return $list[$id];
	}
	public static function getswversion($ir, $id_product){
		$sw= Yii::app()->db->createCommand("select version from installation_requests_products where id_ir= ".$ir." and id_product= ".$id_product."")->queryScalar();
		if($sw ==null)	{
			 $cust= Yii::app()->db->createCommand("select customer from installation_requests where id= ".$ir)->queryScalar();
			 $sw= Yii::app()->db->createCommand("select soft_version from maintenance where status='Active' and  customer= ".$cust." and product= ".$id_product." ")->queryScalar();
			if($sw ==null){	return ""; }else{	return $sw;	}
		}else{			 
			return $sw;
		}		
	}
	public static function getwmsdbtype($ir, $id_product){
		$db= Yii::app()->db->createCommand("select dbtype from installation_requests_products where id_ir= ".$ir." and id_product= ".$id_product."")->queryScalar();
		if($db ==null){
			$cust= Yii::app()->db->createCommand("select customer from installation_requests where id= ".$ir)->queryScalar();
			$db= Yii::app()->db->createCommand("select wms_db_type from maintenance where status='Active' and  customer= ".$cust." and product= ".$id_product." ")->queryScalar();
			if($db ==null){	return ""; }else{ return $db;}		}
		else{
			return $db;
		}	
	}
	public static function getReportingTypeList(){
		return array(self::REPORTING_BIRT => 'BIRT',
			self::REPORTING_INFOR => 'INFOR Reporting');
	}
	public static function getReportingTypeLabel($id = null){
		if($id == null)
			return "";		
		$list = self::getReportingTypeList();
		return $list[$id];
	}
	public static function getInstallLocationList(){
		return array(self::INSTALL_LOCATION_ONSITE => 'On Site',
			self::INSTALL_LOCATION_REMOTE => 'Remote');
	}
	public static function getAuthenticationList(){
		return array(
			self::AUTHENTICATION_DB => 'DB Authentication',
			self::AUTHENTICATION_AD => 'Active Directory Authentication',
            self::AUTHENTICATION_HYBRID => 'Hybrid');
	}
	public static function getLicenseList(){
		return array(
			self::LICENSE_SNS => 'SNS',
			self::LICENSE_INFOR => 'Infor');
	}
	public static function getAuthenticationLabel($id =null){
		if($id == null)
			return "";
		$list = self::getAuthenticationList();
		return $list[$id];
	}
	public static function getLicensePerProduct($id){
		return  Yii::app()->db->createCommand("SELECT  CASE   WHEN license_type = 1 THEN 'SNS'  WHEN license_type = 2 THEN 'Infor'     ELSE '' END  AS 'license_type' FROM installation_requests_products where id = ".$id)->queryScalar();
	}
	public static function getLicenseLabel($id =null){
		if($id == null)
			return "";
		$list = self::getLicenseList();
		return $list[$id];
	}
	public static function getLanguagePackLabel($id = null){
		if($id == null)
			return "";
		$list = self::getLanguagePackList();
		return $list[$id];
	}
	public static function getLanguagePackList(){
		return array(self::LANGUAGE_PACK_ARABIC => 'Arabic',
				self::LANGUAGE_PACK_Portuguese => 'Brazilian Portuguese',
				self::LANGUAGE_PACK_Dutch => 'Dutch',
				self::LANGUAGE_PACK_English => 'English',
				self::LANGUAGE_PACK_French => 'French',
				self::LANGUAGE_PACK_German => 'German',
				self::LANGUAGE_PACK_Italian => 'Italian',
				self::LANGUAGE_PACK_Japanese => 'Japanese',
				self::LANGUAGE_PACK_Polish => 'Polish',
				self::LANGUAGE_PACK_Russian => 'Russian',
				self::LANGUAGE_PACK_Simplified_Chinese => 'Simplified Chinese',
				self::LANGUAGE_PACK_Spanish => 'Spanish',
				self::LANGUAGE_PACK_Swedish => 'Swedish',
				self::LANGUAGE_PACK_Thai => 'Thai',
				self::LANGUAGE_PACK_Traditional_Chinese => 'Traditional Chinese',
				);
	}
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }	
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public static function getProductsAutocomplete(){
		$result =  Yii::app()->db->createCommand('select distinct c.id, c.codelkup as name from codelkups c join installation_requests_products p
					on p.id_product = c.id 
					join installation_requests r ON p.id_ir = r.id order by c.codelkup')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['name'];		$customers[$i]['id'] = $res['id'];	}
		return $customers;
	}
    public static function getProductsProvider($id_ir){
		$criteria=new CDbCriteria;	
		$criteria->condition = "(id in (Select id from installation_requests_products where id_ir =".$id_ir."))";	
		return new CActiveDataProvider('InstallationrequestsProducts', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
}
?>