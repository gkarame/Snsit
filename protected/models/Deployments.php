<?php
class Deployments extends CActiveRecord{
		public $customErrors = array();	public $customer_name;	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'deployments';
	}
	public function rules(){
		return array(
			array('id_customer, dep_date, description,status, module, infor_version,location', 'required'),
			array('id_customer, user,status', 'numerical', 'integerOnly'=>true),
			array('dep_no', 'length', 'max'=>10),
			array('description, notes, location', 'length', 'max'=>2000),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers'),
			array('module, assigned_srs', 'length', 'max'=>500),
			array('infor_version, dep_version', 'length', 'max'=>15),
			array('source', 'length', 'max'=>50),
			array('source','validatesource'),
			array('dep_no, id_customer, module, infor_version, dep_version, assigned_srs, source', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array();
	}
	public function renderDepNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("deployments/view", array("id" => $this->id)).'">'.$this->dep_no.'</a>';
	}
	public function validatesource(){
	 if ( $this->source == '663' &&  empty($this->assigned_srs) ){
	        $this->addError('assigned_srs','Assigned SR(s) must be specified.');
	    }
	  if ( $this->source == '663' &&  !empty($this->assigned_srs) ){
	  		$srs= explode(",",$this->assigned_srs);
	  		foreach ($srs as $sr) {
	  			if(is_numeric($sr))
	  			{
	  				$vcustomer= SupportDesk::getCustomerIdBySr($sr);
		  			if($vcustomer != $this->id_customer)
		  			{
		  				$this->addError('assigned_srs','SR(s) do not belong to the specified customer.');
		  			}
		  		}else{
		  			$this->addError('assigned_srs','Assigned SR(s) is not valid.');
		  		}
	  		}	  			        
	    }  		
	}
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public function attributeLabels(){
		return array(
			'dep_no' => 'Dep#',
			'description' => 'Description',
			'id_customer' => 'Customer',
			'dep_date' => 'Dep. Date',
			'status' => 'Status',
			'module' => 'Module',
			'infor_version' => 'Infor Version',
			'source' => 'Source',
			'user' => 'Resource',
			'assigned_srs' => 'Assigned SR(s)',
			'dep_version' => 'Dep. Version',
			'adddate' =>'Creation Date',
			'location' => 'Backup Location',
			'notes' => 'Notes'
		);
	}

	public static function SRDepNum($sr,$customer){
		return Yii::app()->db->createCommand("SELECT IFNULL(dep_no,'') as dep_no FROM deployments where assigned_srs like '%".$sr."%' and id_customer=".$customer." order by adddate desc limit 1")->queryScalar();
	}
	public static function validateSRDeployment($sr, $customer){
		$result =  Yii::app()->db->createCommand("SELECT count(1) FROM deployments where assigned_srs like '%".$sr."%' and id_customer=".$customer."")->queryScalar();
		if($result>0)
		{
			return true;
		}else{
			return false;
		}
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList();
		return $list[$value];
	}
	public static function getStatusList(){		
		return array(			
			0 => 'Deployed',
			1 => 'Reverted'
		); 
	}
	public static function getProjectsDD($customer= null){
		if($customer)
		{
			$results= Yii::app()->db->createCommand("SELECT id,name FROM `projects` where ((status=1 and customer_id='".$customer."' ) or id= 663) order by name")->queryAll();
		}else{
			$results= Yii::app()->db->createCommand("SELECT id,name FROM `projects` where (status=1 and customer_id!=177) or id= 663 order by name")->queryAll();
		}
		$items = array();
		foreach ($results as $res){	$items[$res['id']] = $res['name']; }
		return $items;
	}
	public static function getProjectsAutocomplete(){
		$result =  Yii::app()->db->createCommand("SELECT id,name FROM `projects` where (status=1 and customer_id!=177) or id= 663 order by name")->queryAll();
		$items = array();
		foreach ($result as $i=>$res){	$items[$i]['label'] = $res['name'];	$items[$i]['id'] = $res['id'];}
		return $items;
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand("SELECT DISTINCT c.id, c.name FROM customers c WHERE c.name != 'N/A' order by c.name")->queryAll();
		$items = array();
		foreach ($result as $i=>$res){	$items[$i]['label'] = $res['name'];	$items[$i]['id'] = $res['id'];}
		return $items;
	}
	public function search($selector = null){
		$criteria=new CDbCriteria;
		if (isset($this->id_customer) && $this->id_customer !=""){
			$criteria->condition = " t.id_customer in (select c.id from customers c where c.name like '%".$this->id_customer."%') ";
		}
		if (isset($this->source) && $this->source !=""){
			$criteria->condition = " t.source in (select p.id from projects p where p.name like '%".$this->source."%')";
		}
		if (isset($this->dep_no)){
			$criteria->compare('t.dep_no', $this->dep_no, true);
		}
		
		if (isset($this->module) && $this->module !=""){
			$criteria->compare('t.module', $this->module, true);
		}
		if (isset($this->dep_version) && $this->dep_version !=""){
			$criteria->compare('t.dep_version', $this->dep_version, true);
		}		
		if (isset($this->infor_version) && $this->infor_version !=""){
			$criteria->compare('t.infor_version', $this->infor_version, true);
		}
			
		if (isset($this->assigned_srs) && $this->assigned_srs !=""){
			$criteria->compare('t.assigned_srs', $this->assigned_srs, true);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => ($selector != null)?1000:Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder' => 'dep_no ASC'
		    ),
		));
	}
} ?>