<?php
class Customers extends CActiveRecord{
	public $contact_name;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'customers';
	}
	public function rules()	{
		return array(
			array('name, city, country, week_end,product_type, complexity, website,address,mobile_number, primary_contact_name,bill_to_contact_email, job_title,primary_contact_email , brands,time_zone, region, main_phone, industry, bill_to_address, bill_to_contact_person, default_currency', 'required'),
			array('country, region, status,id_assigned, default_currency,product_1, product_2, product_3,n_licenses_audited,n_licenses_allowed , customer_reference ,addwho, bank, aux', 'numerical', 'integerOnly'=>true),
			array('name', 'unique'),
			array('name, contact_name, strategic, week_end,industry', 'length', 'max'=>500),
			array('city, website, primary_contact_name, job_title, bill_to_contact_email, primary_contact_email, time_zone, support_weekend,date_audit , erp, product_type , brands,custsupport, checkb', 'length', 'max'=>255),
			array('main_phone, mobile_number,dolphin_aux, accounting_code', 'length', 'max'=>100),
			array('website','GUrlValidator'),
			array('lpo_required,reassign_notification', 'length', 'max'=>3),
			array('name, country, status,id_assigned, primary_contact_name,erp', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'cCountry' => array(self::BELONGS_TO, 'Codelkups', 'country'),
			'cRegion' => array(self::BELONGS_TO, 'Codelkups', 'region'),
			'product1' => array(self::BELONGS_TO, 'Codelkups', 'product_1'),
			'product2' => array(self::BELONGS_TO, 'Codelkups', 'product_2'),
			'product3' => array(self::BELONGS_TO, 'Codelkups', 'product_3'),
			'cCurrency' => array(self::BELONGS_TO, 'Codelkups', 'default_currency'),			
			'Users' => array(self::BELONGS_TO, 'Users', 'cs_representative'),
			'Users' => array(self::BELONGS_TO, 'Users', 'ca'),
			'Users' => array(self::BELONGS_TO, 'Users', 'account_manager'),		
			'cConnections' => array(self::HAS_MANY, 'Connections', 'id_customer'),
			'cContacts' => array(self::HAS_MANY, 'CustomersContacts', 'id_customer'),
			'cDocuments' => array(self::HAS_MANY, 'documents', 'id_model'),
			'cEas' => array(self::HAS_MANY, 'Eas', 'id_customer'),
			'cInvoices' => array(self::HAS_MANY, 'Invoices', 'id_customer'),
			'rbank' => array(self::BELONGS_TO, 'Codelkups', 'bank'),
			'raux' => array(self::BELONGS_TO, 'Codelkups', 'aux'),
		);
	}	
public function checklicenses() {        
     if ($this->product_1 == "64"||$this->product_1 == "162"||$this->product_1 == "394"||$this->product_1 == "395"||$this->product_2 == "64"||$this->product_2 == "162"||$this->product_2== "394"||$this->product_2 == "395"||$this->product_3 == "64"||$this->product_3 == "162"||$this->product_3 == "394"||$this->product_3 == "395") {
        if (empty($this->n_licenses_allowed))
           $this->addError("n_licenses_allowed", 'No of Licenses cannot be blank.');
     }
	 
}
	public static function getIdByName($name){
		$id = Yii::app()->db->createCommand()
    		->select('id')
    		->from('customers')
    		->where('name =:name', array(':name'=>$name))
    		->queryScalar();
    		return $id;
	}	
	public static function getCityPernames($customer_id){
		if ($customer_id)
		{	$customer_id= "'".str_replace(", ","','",$customer_id)."'"; }
		$result =  Yii::app()->db->createCommand("SELECT city FROM customers WHERE name in (".$customer_id.") and city in (select codelkup from codelkups where id_codelist='36') order by name LIMIT 1")->queryScalar();
		return $result;
	}
	public static function getCountryById($id)	{
		return Yii::app()->db->createCommand()
    		->select('country')
    		->from('customers')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
	}
	public static function getNameById($id)	{
		$name = Yii::app()->db->createCommand()
    		->select('name')
    		->from('customers')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    		return $name;
	}
	public static function getComplexityLabel($value)	{
		$list = self::getcomplexity($value);	return $list[$value];
	}
	
	public static function getcomplexity(){	
		return array(
					1 => 'Very High',
					2 => 'High',
					3 => 'Medium',					
					4 => 'Low',	
					5 => 'Very Low'					
				); 
	}
	public static function getId($column,$id_customer){
 		return Yii::app()->db->createCommand()
    		->select($column)
    		->from('customers')
    		->where('id =:id', array(':id'=>$id_customer))
    		->queryScalar();
 	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'city' => 'City',
			'country' => 'Country',
			'region' => 'Region',
			'address' => 'Address',
			'main_phone' => 'Primary Phone',
			'status' => 'Status',
			'id_assigned' => 'Receivables Rep',
			'website' => 'Website',
			'industry' => 'Industry',
			'bill_to_address' => 'Bill To Address',
			'bill_to_contact_person' => 'Bill To Contact Person',
			'default_currency' => 'Default Currency',			
			'primary_contact_name' => 'Primary Contact',
			'job_title' => 'Job Title',
			'mobile_number' => 'Mobile Number',
			'product_1' => 'Product 1',
			'product_2' => 'Product 2',
			'product_3' => 'Product 3',
			'accounting_code' => 'Accounting Code',
			'strategic' => 'Strategic',
			'support_weekend' => 'Support Plan',
			'lpo_required' => 'LPO Required',
			'last_date_audit' => 'Last Audit Date',
			'n_licenses_allowed' => '# Licenses Allowed',
			'n_licenses_audited' => '# Licenses Audited',
			'checkb' => 'Payment Sensitive',
			'custsupport' => 'Customization Support',
			'customer_reference'=> 'Bill to Customer',
			'bank' => 'Bank',
			'aux'=> 'Auxiliary',
			'dolphin_aux' => 'Dolphin Aux',
			'complexity' => 'Complexity'
		);
	}
	public static function getStatusList()	{	return array('0' => 'Inactive', '1' => 'Active'); 	}	
	public static function getCustomersBilling()	{	
		$result =  Yii::app()->db->createCommand('SELECT id, name FROM customers order by name ASC')->queryAll();		
		$customers = array();
		foreach ($result as $i => $res)		{			$customers[$res['id']] = $res['name'];		}
		return $customers; 
	}	
	public function isActiveAsString(){
		return ($this->status == 1) ? Yii::t('translations', 'Active') : Yii::t('translations', 'Inactive');
	}	
	public function isActive(){	return ($model->status == 0) ? false : true;}
	public function getContacts(){	if(!isset(Yii::app()->session['limit'])){			 
			$limit= 3;
		}else{
			$limit= Yii::app()->session['limit'];
		}
		$criteria=new CDbCriteria;	
		$criteria->condition = 'id_customer = '.($this->id ? $this->id : 0);
		if ($limit>0){	$criteria->limit = $limit;	}	 
		return new CActiveDataProvider('CustomersContacts', array(
			'criteria' => $criteria,
			'pagination'=> false,
             'sort'=>array(
    			'defaultOrder'=>'name ASC',  
            ),
		));
	}

	public static function getAllSelectVersionCheck()	{
		$result =  Yii::app()->db->createCommand('SELECT id, name FROM customers where id not in (select customer from maintenance where soft_version in (562,563,564	,565,684,685,686,687,1090,1420) and product=64 ) or id= 55 order by name ASC')->queryAll();		
		$customers = array();
		foreach ($result as $i => $res)		{			$customers[$res['id']] = $res['name'];		}
		return $customers;
	}

	public static function getAllSelect()	{
		$result =  Yii::app()->db->createCommand('SELECT id, name FROM customers order by name ASC')->queryAll();		
		$customers = array();
		foreach ($result as $i => $res)		{			$customers[$res['id']] = $res['name'];		}
		return $customers;
	}
	public static function getSupportPlan($id_customer){
		if($id_customer!=null){
		$result= Yii::app()->db->createCommand("SELECT count(1) From customers c where c.id=".$id_customer." and c.support_weekend='N/A'")->queryScalar();
		return ((int)$result[0]);
		}
		return null;
	}
	public static function getRegion($id_customer){		
		$result= Yii::app()->db->createCommand("SELECT region from customers where id=".$id_customer." ")->queryScalar();
		return $result;		
	}
	public static function getCAbyCustomer($id_customer){		
		$result= Yii::app()->db->createCommand("SELECT ca from customers where id=".$id_customer." ")->queryScalar();
		return $result;		
	}
	public static function getAccountManagerbyCustomer($id_customer){		
		$result= Yii::app()->db->createCommand("SELECT account_manager from customers where id=".$id_customer." ")->queryScalar();
		return $result;		
	}	
	public static function checkSensitive($id_customer){		
		$result= Yii::app()->db->createCommand("SELECT count(*) from customers where id=".$id_customer." and checkb='checked' ")->queryScalar();
		return $result;		
	}	
	public static function getNewCaCust($customer)
	{
		$result= Yii::app()->db->createCommand("SELECT count(*) from customers where id=".$customer." and id in (199,272, 250, 33, 90, 363,86, 147,306, 38,263,55,302,148,273,344,152,144,138,61,53,91,94,104,108,110,119,310) ")->queryScalar();
		return $result;	
	}	
	public function getEas(){
		$criteria=new CDbCriteria;	
		$criteria->with = array('eCategory', 'eAuthor');
		$criteria->condition = 'id_customer = '.($this->id ? $this->id : 0);
		return new CActiveDataProvider('Eas', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		         'attributes'=>array(
		            'eCategory.codelkup'=>array(
		                'asc'=>'eCategory.codelkup',
		                'desc'=>'eCategory.codelkup DESC',
		            ),
		            'eAuthor.fullname'=>array(
		                'asc'=>'eAuthor.firstname',
		                'desc'=>'eAuthor.firstname DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}	
	public function getInvoices()	{
		$criteria=new CDbCriteria;	
		$criteria->with = array('iCurrency', 'iAuthor');
		$criteria->condition = 'id_customer = '.($this->id ? $this->id : 0);
		return new CActiveDataProvider('Invoices', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		         'attributes'=>array(
		            'iCurrency.codelkup'=>array(
		                'asc'=>'iCurrency.codelkup',
		                'desc'=>'iCurrency.codelkup DESC',
		            ),
		            'iAuthor.fullname'=>array(
		                'asc'=>'iAuthor.firstname',
		                'desc'=>'iAuthor.firstname DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}	
	public function getDocuments()	{
		$criteria=new CDbCriteria;	
		$criteria->with = array('author');
		$criteria->condition = 'id_model = '.($this->id ? $this->id : 0) . ' AND `model_table` LIKE "customers"' ;
		return new CActiveDataProvider('Documents', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		        'attributes'=>array(
		            'author.fullname'=>array(
		                'asc'=>'author.firstname',
		                'desc'=>'author.firstname DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}
	public static function getIndustry($industry)	{
		$elements= explode(",",$industry);
		$x= count($elements);
		$str='';
		foreach ($elements as $element) {
				$x--;
					$str.=  Codelkups::getCodelkup($element);
					if ($x>0)
						$str.=', ';
		}
		return $str;
	}	
	public function getConnections()	{
		$criteria=new CDbCriteria;
		$criteria->with = array('cType');	
		$criteria->condition = 'id_customer = '.($this->id ? $this->id : 0);
		return new CActiveDataProvider('Connections', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.name ASC',   
            	'attributes'=>array(
		            'cType.codelkup'=>array(
		                'asc'=>'cType.codelkup',
		                'desc'=>'cType.codelkup DESC',
		            ),
		            '*',
		        ),         
		    ),
		));
	}
	public function search(){	
		$criteria = new CDbCriteria;
		$criteria->with = array('cCountry', 'cContacts');
		$criteria->together = true;
		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.country', $this->country);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('cContacts.name', $this->contact_name, true);
		$criteria->compare('t.erp', $this->erp);
		$criteria->compare('t.id', '<> 0');
		$criteria->group = 't.id';	

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
	public static function getContactsAutocomplete()	{
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT cc.id, cc.name FROM customers_contacts cc INNER JOIN customers c ON cc.id_customer=c.id order by cc.name,c.name')->queryAll();
		$items = array();
		foreach ($result as $i=>$res){
			$items[$i]['label'] = $res['name'];	$items[$i]['id'] = $res['id'];
		}
		return $items;
	}	
	public static function getAllAutocomplete()	{
		$result =  Yii::app()->db->createCommand("SELECT id, name FROM customers WHERE name != 'N/A' order by name")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res)		{
			$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id'];
		}
		return $customers;
	}
	public static function getAllAutocompleteActive()	{
		$result =  Yii::app()->db->createCommand("SELECT id, name FROM customers WHERE name != 'N/A' AND status = 1 order by name asc")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['name'];		$customers[$i]['id'] = $res['id'];		}
		return $customers;
	}
public static function getAllBrands()	{
		$result =  Yii::app()->db->createCommand("SELECT distinct brands from customers where status='1'  and brands is not null ")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['brands'];		$customers[$i]['id'] = $res['brands'];		}
		return $customers;
	}
public static function getAllproductType()	{
		$result =  Yii::app()->db->createCommand("SELECT distinct product_type from customers where status='1' and product_type is not null  ")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['product_type'];		$customers[$i]['id'] = $res['product_type'];	}
		return $customers;
	}
public static function getAllERP()	{
		$result =  Yii::app()->db->createCommand("SELECT distinct (erp) from customers where status='1'  and erp is not null order by erp")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]['label'] = $res['erp'];		$customers[$i]['id'] = $res['erp'];	}
		return $customers;
	}
	public static function getERPs(){
		$result =  Yii::app()->db->createCommand("SELECT distinct(erp) from customers where erp!='' and erp!=' ' and erp is not null order by erp ")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]  = $res['erp']; 	}
		return $customers;
	}
	public static function getCustDD(){
		$result =  Yii::app()->db->createCommand("SELECT distinct(name) from customers where name!='' and name!=' ' and name is not null order by name ")->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){		$customers[$i]  = $res['name']; 	}
		return $customers;
	}
	public static function getAllUserAutocompleteActive(){
		$id_user=Yii::app()->user->id;
		$result = Yii::app()->db->createCommand("SELECT p.customer_id as id,c.name as name FROM projects_tasks pt,user_task ut, projects_phases pp, projects p,users u, customers c
where ut.id_task=pt.id and pt.id_project_phase=pp.id and p.id=pp.id_project and ut.id_user=".$id_user." and p.status=1 and c.id=p.customer_id
GROUP BY c.name")->queryAll();
		$customers = array();
		foreach ($result as $i => $res) {	$customers[$i]['label'] = $res['name'];		$customers[$i]['id']=$res['id'];	}
		return $customers;
	}
	public static function GetCustByProject($id){
			$result =  Yii::app()->db->createCommand("SELECT name FROM customers where id=(select customer_id from projects where id=".$id.")")->queryScalar();
			$result = str_replace(' ', '_', $result);
			return $result;
	}
	public static function getAllTStasks(){
		$id_user=Yii::app()->user->id;
		$result = Yii::app()->db->createCommand("	
		SELECT  CONCAT('m',m.id_maintenance) as id, CONCAT(cu.name, ' ' ,c.codelkup) as description 
								FROM maintenance m
									LEFT JOIN customers cu on m.customer=cu.id
									LEFT JOIN codelkups c on m.support_service=c.id
								WHERE m.support_service in ('501','502') and m.status='Active'						
				UNION	
				SELECT CONCAT('p',p.id) as id , p.name as description 
									FROM projects_tasks pt 
										LEFT JOIN projects_phases pp on pt.id_project_phase=pp.id
										LEFT JOIN projects p on pp.id_project=p.id
										LEFT JOIN user_task as ut on pt.id = ut.id_task
								WHERE ut.id_user='".$id_user."' and p.status='1'
				UNION	
				SELECT CONCAT('i',p.id) as id , p.name as description 
									FROM internal_tasks pt 
										LEFT JOIN internal p on pt.id_internal=p.id
										LEFT JOIN user_internal as ut on pt.id = ut.id_task
								WHERE ut.id_user='".$id_user."' and p.status='0'
				UNION
				SELECT  CONCAT('c',dt.id) as id ,dt.name as description 
										FROM customers c ,default_tasks dt
								WHERE   dt.id_parent='828' and c.status=1 
								and not exists (select 1 from maintenance m WHERE m.support_service in ('501','502')  and m.status='Active' and c.id = m.customer) 
								and dt.name not in (SELECT t.name FROM default_tasks t WHERE t.id_parent in ('27','1324') and id_maintenance is null )
								group by description
				UNION
				SELECT   CONCAT('d',dt.id) as id ,name as description 
								FROM default_tasks dt  
						WHERE id_parent ='27' and id_maintenance is null  group by description
				UNION				
					SELECT   '1324' , 'RSR'
				UNION
					SELECT   '27' , 'Support'
				UNION
					SELECT   '828' , 'CA'
				ORDER BY description ASC ")->queryAll();

		$customers = array();
		foreach ($result as $i => $res) {		$customers[$i]['label'] = $res['description'];		$customers[$i]['id']=$res['id'];	}
		return $customers;
	}
	public static function getLogo($customer)
	{
		$result =  Yii::app()->db->createCommand("SELECT id FROM `documents` where id_model=".$customer." and model_table='customers' and id_category=32 order by uploaded DESC limit 1")->queryScalar();
		return $result;
	}

	public static function getFName($id)
	{
		$result =  Yii::app()->db->createCommand("SELECT file FROM `documents` where id=".$id)->queryScalar();
		return $result;
	}
	public static function getAllAutocompleteNA(){
		$result =  Yii::app()->db->createCommand('SELECT id, name FROM customers order by name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id'];	}
		return $customers;
	}	
	public static function getIdAssigned($id)	{
		$result =  Yii::app()->db->createCommand("SELECT id_assigned FROM customers where id=".$id." ")->queryScalar();
		return $result;
	}
	public static function getAllCustomersSelect()	{
		return CHtml::listData(self::model()->findAll(array('order'=>'name ASC')), 'id', 'name');		
	}	
	public static function timeZones(){
		$timezones = array(
		    'Pacific/Midway'       => "(GMT-11:00) Midway Island",
		    'US/Samoa'             => "(GMT-11:00) Samoa",
		    'US/Hawaii'            => "(GMT-10:00) Hawaii",
		    'US/Alaska'            => "(GMT-09:00) Alaska",
		    'US/Pacific'           => "(GMT-08:00) Pacific Time (US &amp; Canada)",
		    'America/Tijuana'      => "(GMT-08:00) Tijuana",
		    'US/Arizona'           => "(GMT-07:00) Arizona",
		    'US/Mountain'          => "(GMT-07:00) Mountain Time (US &amp; Canada)",
		    'America/Chihuahua'    => "(GMT-07:00) Chihuahua",
		    'America/Mazatlan'     => "(GMT-07:00) Mazatlan",
		    'America/Mexico_City'  => "(GMT-06:00) Mexico City",
		    'America/Monterrey'    => "(GMT-06:00) Monterrey",
		    'Canada/Saskatchewan'  => "(GMT-06:00) Saskatchewan",
		    'US/Central'           => "(GMT-06:00) Central Time (US &amp; Canada)",
		    'US/Eastern'           => "(GMT-05:00) Eastern Time (US &amp; Canada)",
		    'US/East-Indiana'      => "(GMT-05:00) Indiana (East)",
		    'America/Bogota'       => "(GMT-05:00) Bogota",
		    'America/Lima'         => "(GMT-05:00) Lima",
		    'America/Caracas'      => "(GMT-04:30) Caracas",
		    'Canada/Atlantic'      => "(GMT-04:00) Atlantic Time (Canada)",
		    'America/La_Paz'       => "(GMT-04:00) La Paz",
		    'America/Santiago'     => "(GMT-04:00) Santiago",
		    'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
		    'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
		    'Greenland'            => "(GMT-03:00) Greenland",
		    'Atlantic/Stanley'     => "(GMT-02:00) Stanley",
		    'Atlantic/Azores'      => "(GMT-01:00) Azores",
		    'Atlantic/Cape_Verde'  => "(GMT-01:00) Cape Verde Is.",
		    'Africa/Casablanca'    => "(GMT) Casablanca",
		    'Europe/Dublin'        => "(GMT) Dublin",
		    'Europe/Lisbon'        => "(GMT) Lisbon",
		    'Europe/London'        => "(GMT) London",
		    'Africa/Monrovia'      => "(GMT) Monrovia",
		    'Europe/Amsterdam'     => "(GMT+01:00) Amsterdam",
		    'Europe/Belgrade'      => "(GMT+01:00) Belgrade",
		    'Europe/Berlin'        => "(GMT+01:00) Berlin",
		    'Europe/Bratislava'    => "(GMT+01:00) Bratislava",
		    'Europe/Brussels'      => "(GMT+01:00) Brussels",
		    'Europe/Budapest'      => "(GMT+01:00) Budapest",
		    'Europe/Copenhagen'    => "(GMT+01:00) Copenhagen",
		    'Europe/Ljubljana'     => "(GMT+01:00) Ljubljana",
		    'Europe/Madrid'        => "(GMT+01:00) Madrid",
		    'Europe/Paris'         => "(GMT+01:00) Paris",
		    'Europe/Prague'        => "(GMT+01:00) Prague",
		    'Europe/Rome'          => "(GMT+01:00) Rome",
		    'Europe/Sarajevo'      => "(GMT+01:00) Sarajevo",
		    'Europe/Skopje'        => "(GMT+01:00) Skopje",
		    'Europe/Stockholm'     => "(GMT+01:00) Stockholm",
		    'Europe/Vienna'        => "(GMT+01:00) Vienna",
		    'Europe/Warsaw'        => "(GMT+01:00) Warsaw",
		    'Europe/Zagreb'        => "(GMT+01:00) Zagreb",
		    'Europe/Athens'        => "(GMT+02:00) Athens",
			'Asia/Beirut'      	   => "(GMT+02:00) Beirut",
		    'Europe/Bucharest'     => "(GMT+02:00) Bucharest",
		    'Africa/Cairo'         => "(GMT+02:00) Cairo",
		    'Africa/Harare'        => "(GMT+02:00) Harare",
		    'Europe/Helsinki'      => "(GMT+02:00) Helsinki",
		    'Europe/Istanbul'      => "(GMT+02:00) Istanbul",
		    'Asia/Jerusalem'       => "(GMT+02:00) Jerusalem",
		    'Africa/Johannesburg'  => "(GMT+02:00) Johannesburg",
		    'Europe/Kiev'          => "(GMT+02:00) Kyiv",
		    'Europe/Minsk'         => "(GMT+02:00) Minsk",
		    'Europe/Riga'          => "(GMT+02:00) Riga",
		    'Europe/Sofia'         => "(GMT+02:00) Sofia",
		    'Europe/Tallinn'       => "(GMT+02:00) Tallinn",			
		    'Europe/Vilnius'       => "(GMT+02:00) Vilnius",		
		    'Asia/Baghdad'         => "(GMT+03:00) Baghdad",			
		    'Asia/Kuwait'          => "(GMT+03:00) Kuwait",		    			
		    'Asia/Qatar'          => "(GMT+03:00) Qatar",
		    'Africa/Nairobi'       => "(GMT+03:00) Nairobi",
		    'Asia/Riyadh'          => "(GMT+03:00) Riyadh",
		    'Asia/Tehran'          => "(GMT+03:30) Tehran",
		    'Europe/Moscow'        => "(GMT+04:00) Moscow",
		    'Asia/Baku'            => "(GMT+04:00) Baku",
		    'Europe/Volgograd'     => "(GMT+04:00) Volgograd",
		    'Asia/Muscat'          => "(GMT+04:00) Muscat",
		    'Asia/Tbilisi'         => "(GMT+04:00) Tbilisi",
		    'Asia/Yerevan'         => "(GMT+04:00) Yerevan",
			'Asia/Dubai'      	   => "(GMT+04:00) Dubai",
		    'Asia/Kabul'           => "(GMT+04:30) Kabul",
		    'Asia/Karachi'         => "(GMT+05:00) Karachi",
		    'Asia/Tashkent'        => "(GMT+05:00) Tashkent",
		    'Asia/Kolkata'         => "(GMT+05:30) Kolkata",
		    'Asia/Kathmandu'       => "(GMT+05:45) Kathmandu",
		    'Asia/Yekaterinburg'   => "(GMT+06:00) Ekaterinburg",
		    'Asia/Almaty'          => "(GMT+06:00) Almaty",
		    'Asia/Dhaka'           => "(GMT+06:00) Dhaka",
		    'Asia/Novosibirsk'     => "(GMT+07:00) Novosibirsk",
		    'Asia/Bangkok'         => "(GMT+07:00) Bangkok",
		    'Asia/Jakarta'         => "(GMT+07:00) Jakarta",
		    'Asia/Krasnoyarsk'     => "(GMT+08:00) Krasnoyarsk",
		    'Asia/Chongqing'       => "(GMT+08:00) Chongqing",
		    'Asia/Hong_Kong'       => "(GMT+08:00) Hong Kong",
		    'Asia/Kuala_Lumpur'    => "(GMT+08:00) Kuala Lumpur",
		    'Australia/Perth'      => "(GMT+08:00) Perth",
		    'Asia/Singapore'       => "(GMT+08:00) Singapore",
		    'Asia/Taipei'          => "(GMT+08:00) Taipei",
		    'Asia/Ulaanbaatar'     => "(GMT+08:00) Ulaan Bataar",
		    'Asia/Urumqi'          => "(GMT+08:00) Urumqi",
		    'Asia/Irkutsk'         => "(GMT+09:00) Irkutsk",
		    'Asia/Seoul'           => "(GMT+09:00) Seoul",
		    'Asia/Tokyo'           => "(GMT+09:00) Tokyo",
		    'Australia/Adelaide'   => "(GMT+09:30) Adelaide",
		    'Australia/Darwin'     => "(GMT+09:30) Darwin",
		    'Asia/Yakutsk'         => "(GMT+10:00) Yakutsk",
		    'Australia/Brisbane'   => "(GMT+10:00) Brisbane",
		    'Australia/Canberra'   => "(GMT+10:00) Canberra",
		    'Pacific/Guam'         => "(GMT+10:00) Guam",
		    'Australia/Hobart'     => "(GMT+10:00) Hobart",
		    'Australia/Melbourne'  => "(GMT+10:00) Melbourne",
		    'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
		    'Australia/Sydney'     => "(GMT+10:00) Sydney",
		    'Asia/Vladivostok'     => "(GMT+11:00) Vladivostok",
		    'Asia/Magadan'         => "(GMT+12:00) Magadan",
		    'Pacific/Auckland'     => "(GMT+12:00) Auckland",
		    'Pacific/Fiji'         => "(GMT+12:00) Fiji",		    
		);
		return $timezones;
	}
	public static function getLpo($id_customer)	{
		return Yii::app()->db->createCommand("SELECT lpo_required FROM customers WHERE id = '$id_customer'")->queryScalar();
	}	
	public static function getTimeZonebyID($id)	{
		$timezone = Yii::app()->db->createCommand()
    		->select('time_zone')
    		->from('customers')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    		return $timezone;
	}
	public static function getLicenseBalance($id){
		return Yii::app()->db->createCommand("select (c.n_licenses_allowed-c.n_licenses_audited) as balance from customers c where c.id=".$id." ")->queryScalar();
	}
	public static function getLicenseLastDate($id){
				return Yii::app()->db->createCommand("select c.date_audit from customers c where c.id=".$id." ")->queryScalar();
	}	
	public static function getCustomerRef($id){
				return Yii::app()->db->createCommand("select customer_reference from customers  where id=".$id." ")->queryScalar();
	}	
	public static function hasMaint($id_customer){
		$check=Yii::app()->db->createCommand("select count(*) from maintenance m, codelkups c where m.product=c.id and c.id_codelist='3' and m.end_customer='".$id_customer."' ")->queryScalar();
		return $check;	
	}
	public static function hasMaintMainCustomer($id_customer)	{
		$check=Yii::app()->db->createCommand("select count(*) from maintenance m, codelkups c where m.product=c.id and c.id_codelist='3' and m.customer='".$id_customer."' ")->queryScalar();
		return $check;	
	}
	public static function getActiveMaint($id_customer)	{
		$check=Yii::app()->db->createCommand("select count(*) from maintenance m, codelkups c where m.product=c.id and c.id_codelist='3'and m.`status`='Active' and m.end_customer='".$id_customer."' ")->queryScalar();
		return $check;	
	}	
	public static function getProductwithActiveMaint($id_customer)	{
			$products= " "; $count=0;
			$customers_needed=Yii::app()->db->createCommand("select DISTINCT(c.id), c.codelkup from maintenance m, codelkups c where m.product=c.id and c.id_codelist='3'and m.`status`='Active' and m.end_customer='".$id_customer."' ")->queryAll();
			foreach ($customers_needed as $value) {
				if ($count == 0)				{
					$products.= $value['codelkup'];
					$count=1;
				}else{
					$products.= " ,".$value['codelkup'];
				}
			}
			return $products;	
	}	
}?>