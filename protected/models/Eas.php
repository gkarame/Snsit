<?php
class Eas extends CActiveRecord{
	const STATUS_NEW = 1;	const STATUS_APPROVED = 2;	const STATUS_INVOICED = 3;	const STATUS_CANCELLED = 0;	const STATUS_PART_INVOICED = 4;
	const STATUS_FULLY_INVOICED = 5;	public $customErrors = array();

	const TEMPLATE_DEFAULT = 1;	const TEMPLATE_INTEGRATION = 2; const TEMPLATE_OPSI = 3; 	
	const TEMPLATE_ROLLOUT = 4;	const TEMPLATE_CONSULTING = 5; const TEMPLATE_CUSTOMIZATION = 6; const TEMPLATE_INSTALLATION = 7;
	

	public $project_name, $id_parent_project, $parent_project, $customer_name, $lump_sum,$amount;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'eas';
	}
	public function rules()	{
		return array(
			array('id_customer, ea_number, status, author, category, description', 'required'),
			array('id_customer, approver, status, author, category, crmOpp, template', 'numerical', 'integerOnly'=>true),
			array('author', 'exist', 'attributeName' => 'id', 'className' => 'Users'),
			array('currency', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups', 'allowEmpty'=>true),
			array('category', 'exist', 'attributeName' => 'id', 'className' => 'Codelkups', 'allowEmpty'=>true),
			array('id_project, id_parent_project', 'exist', 'attributeName' => 'id', 'className' => 'Projects', 'allowEmpty'=>true),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers'),
			array('id_customer', 'exist', 'attributeName' => 'id', 'className' => 'Customers'),
			array('lump_sum, rate', 'numerical'),
			array('ea_number, project_name, project_n, parent_project, customer_name, expense , billto_contact_person, billto_address, primary_contact_name,netamountusd,netmandayrateusd', 'length', 'max'=>255),
			array('description, file, customer_lpo', 'safe'),
			array('discount, support_percent', 'numerical', 'max'=>100),
			array('support_amt','numerical'),
			array('TM,customization', 'length', 'max'=>3),
			array('start_date, end_date', 'safe'),
			array('customization','validatesupport'),
			array('id, id_customer, ea_number, description, id_project, status,TM, author, created, approved, category', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'eAuthor' => array(self::BELONGS_TO, 'Users', 'author'),
			'eCategory' => array(self::BELONGS_TO, 'Codelkups', 'category'),
			'eTerms' => array(self::HAS_MANY, 'EaPaymentTerms', 'id_ea' , 'order'=>'id ASC'),
			'eItems' => array(self::HAS_MANY, 'EasItems', 'id_ea'),
			'project' => array(self::BELONGS_TO, 'Projects', 'id_project'),
			'parentProject' => array(self::BELONGS_TO, 'Projects', 'id_parent_project'),
			'eCurrency' => array(self::BELONGS_TO, 'Codelkups', 'currency'),			
		);
	}
	public function validatesupport(){
		if ( $this->customization == 1 &&  ((empty($this->support_percent) && $this->support_percent!= 0) || strlen(trim($this->support_percent))==0)) {
	        $this->addError('support_percent','Customization Support % must be specified');
	    }
	    if ( $this->customization == 1 &&  ((empty($this->support_amt) && $this->support_amt!= 0) || strlen(trim($this->support_amt))==0)) {
	        $this->addError('support_amt','Development Amt must be specified');
	    }
	}
	public static function getExpenseValues(){
		return array(
			'Actuals' => 'Actuals', 'N/A' => 'N/A', 'Lump Sum' => 'Lump Sum'
		);	
	}	
	public static function getItemsLabelsByCategory($id_category){
		$labels['man_days'] = 'Man Days'; 	$labels['amount'] = 'Amount';   	$labels['man_day_rate'] = 'MD Rate';   	$labels['sandu'] = 'Support %'; 	
    	switch ((int)$id_category){
    		case 24:
				$labels['training_duration'] = 'Duration';   			
				break;			
			case 25:
				$labels['man_days'] = 'License Qty';
    			$labels['amount'] = 'License Price';
    			$labels['man_day_rate'] = 'Total';
    			$labels['sandu'] = 'S&U%';
				break;	
			case 27:
    			$labels['sandu'] = 'Support %';
				break;
			case 454:
				$labels['man_days'] = '';
    			$labels['amount'] = 'Amount';
    			$labels['man_day_rate'] = '';
				break;	
		}
    	return $labels; 
	}	
	public static function getIdCategoryByName($id_category){
	$result= Yii::app()->db->createCommand("SELECT c.id FROM codelkups c WHERE c.codelkup like'".$id_category."' and c.id_codelist=4")->queryScalar();
		return $result;
	}
	public static function getCategory($id_category){
		$result= Yii::app()->db->createCommand("SELECT c.codelkup FROM codelkups c WHERE c.id=".$id_category."")->queryScalar();
		return $result;
	}
	public static function validateMaintenanceEaItem($ea,$contract)
	{
		$result= Yii::app()->db->createCommand("SELECT count(1) FROM eas where id_customer= (select customer from maintenance where id_maintenance= ".$contract.") and id= ".$ea." and ( category=454 or category=25 ) ")->queryScalar();
		if($result>0)
		{ 
			return true;
		}
		else{
			return false;
		}
	}
	public static function validateMaintenanceEa($ea, $customer)
	{
		$result= Yii::app()->db->createCommand("SELECT count(1) FROM eas where id_customer= ".$customer." and id= ".$ea." and ( category=454 or category=25 ) ")->queryScalar();
		if($result>0)
		{ 
			return true;
		}
		else{
			return false;
		}
	}
	public static function checktandmFlag($id){
		if(!empty($id)){
			$result= Yii::app()->db->createCommand("SELECT case when TM=1 then 1 else 0 end TM FROM eas WHERE id=".$id."")->queryScalar();
			return $result;
		}else{
			return 0;
		}
	}
	public static function getLabelByCategoryField($id_category, $field){
		$labels = self::getItemsLabelsByCategory((int)$id_category);
		return $labels[$field];
	}
	public function getManDaysByCategory(){
    	if ($this->category == 25 || $this->category == 454 || $this->category == 496 || $this->category == 623){
			$field = '';
		}else {
			$field = 'Total Man Days: '.Utils::formatNumber($this->getTotalManDays());
		}
    	return $field;
	}
	public function getTrainers(){    	
			$field = Utils::formatNumber($this->getTotalManDays());		return $field;
	}
	public function getNetManDayRateByCategory(){
		if ($this->category == 25 || $this->category == 454 || $this->category == 496 || $this->category == 623){
			$field = ' ';
		}else {
			$field = 'Net Man Day Rate: '.Utils::formatNumber($this->getNetManDayRate());
		}	
		return $field;		
	}	
	public function getExpenses(){
		$return = array('Actuals' => 'Actuals', 'N/A' => 'N/A', 'Lump Sum' => 'Lump Sum');
		if (!in_array($this->expense, array_keys($return)) && !empty($this->expense)){
			unset($return['Lump Sum']);
			$return[$this->expense] = 'Lump Sum';
		}
		return $return;
	}
	public static function setStatus($id, $status){
 		$status = (int) $status;	$id = (int) $id;
 		Yii::app()->db->createCommand("UPDATE eas SET status='{$status}' WHERE id='{$id}'")->execute();
 	}
	public function getTermsSum($exceptThis = 0) {
    	$sum = 0;	$otherTerms = $this->eTerms; 
		foreach ($otherTerms as $term) {
    		if ((int)$exceptThis != $term->id)
    			$sum += $term->payment_term;
    	}
    	return $sum;
    }
    public function getTermsWithoutSumSandU($ea,$exceptThis = 0) {
    	$sum = 0;
    	$otherTerms = Yii::app()->db->createCommand("SELECT * FROM ea_payment_terms where id_ea =".$ea." and (term_type!='sandu' or term_type is null) ")->queryAll();
		foreach ($otherTerms as $term) {
    		if ((int)$exceptThis != $term['id'])
    			$sum += $term['payment_term'];
    	}
    	return $sum;
    }
    public function getTermsSumSandU($ea,$exceptThis = 0)  {
    	$sum = 0;    	
    	$otherTerms = Yii::app()->db->createCommand("SELECT * FROM ea_payment_terms where id_ea =".$ea." and term_type='sandu' and amount>0 ")->queryAll();
		foreach ($otherTerms as $term) 	{
    		if ((int)$exceptThis != $term['id'])
    			$sum += $term['payment_term'];
    	}
    	return $sum;
    }
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'id_customer' => 'Customer Name',
			'ea_number' => 'Ea #',
			'description' => 'Description',
			'id_project' => 'Project',
			'id_parent_project' => 'Parent Project',
			'status' => 'Status',
			'author' => 'Author',
			'created' => 'Created',
			'approved' => 'Approved',
			'category' => 'Type',
			'discount' => 'Discount',
			'support_percent' => 'Customization Support %',
			'support_amt' =>'Development Amt',
			'customer_lpo' => 'Customer LPO',
			'expense' => 'Expense',
			'crmOpp' => 'CRM #',
			'template' => 'Subtype'
		);
	}	
	public function getFormatExpense()	{
		if (!empty($this->expense) && $this->expense != 'N/A' && $this->expense != 'Actuals') 
			return "Lump Sum (".Utils::formatNumber($this->expense).")";
		return $this->expense;
	}
	public function getFormatExpenseUSD()	{
		if (!empty($this->expense) && $this->expense != 'N/A' && $this->expense != 'Actuals') 
			return "Lump Sum (".Utils::formatNumber(Eas::getAmountinUSD($this->currency, $this->expense)).")";
		return $this->expense;
	}

	public static function getAmountinUSD($currency, $amount){
		if ($currency != CurrencyRate::OFFICIAL_CURRENCY){
			$rate = CurrencyRate::getCurrencyRate($currency);
			if (isset($rate['rate'])){
				$amount = $amount * $rate['rate'];
			}else{
				return 0;
			}
		}
		return $amount;
	}
	public function getPrimaryContact($id_customer)	{
			$contact =Yii::app()->db->createCommand("SELECT primary_contact_name FROM customers where id={$id_customer}")->queryScalar();
		return $contact;
	
	}
	public function getBillToContact($id_customer)	{
			$contact =Yii::app()->db->createCommand("SELECT bill_to_contact_person FROM customers where id={$id_customer}")->queryScalar();
		return $contact;	
	}
	public function getBillToAddress($id_customer)	{
			$contact =Yii::app()->db->createCommand("SELECT bill_to_address FROM customers where id={$id_customer}")->queryScalar();
		return $contact;	
	}
	public function AddLicences($id_ea , $id_customer){		
				$license =Yii::app()->db->createCommand("select n_licenses_allowed  from customers where id='".$id_customer."'")->queryScalar();
				$n_allowed=$license;	
				$totlicense =  Yii::app()->db->createCommand("select sum(man_days) as licenseqty from eas_items where id_ea='".$id_ea."' and settings_codelkup in ('64' , '394' , '395') order by settings_codelkup asc  ")->queryScalar();
				$n_allowed=$n_allowed+$totlicense;				 
				 Yii::app()->db->createCommand("UPDATE customers SET n_licenses_allowed='".$n_allowed."' WHERE id='".$id_customer."'")->execute(); 
	}	
	public function modifyTerms(){
		$terms = $this->eTerms;
		if ($this->expense!='N/A' && $this->expense!='Actuals' && $this->expense!='' && $this->expense!=' '){
			$amount = $this->getNetAmountWithExp();
		}else {					
		$amount = $this->getNetAmount();
		}		
		$sanduamount= $this->getTotalSandU();
		foreach ($terms as $term) {
			if($term->term_type =='sandu'){
				$term->amount = ($term->payment_term / 100) * $sanduamount;
				$term->save();
			}else{
				$term->amount = ($term->payment_term / 100) * $amount;
				$term->save();
			}
		}
		if($this->TM == 1){

			if(empty($terms))
			{
				Yii::app()->db->createCommand('insert into ea_payment_terms (id_ea,payment_term,amount, milestone) VALUES ('.$this->id.',100,0,1067)')->execute(); 
			} else if(($this->category==27 && ($this->template == 2 || ($this->template == 6 && Customers::getRegion($this->id_customer) != 59)) ) || ($this->category==28 && $this->customization ==1  ))
			{
				$added= Yii::app()->db->createCommand("select count(1)  from ea_payment_terms where id_ea=".$this->id." and term_type is null")->queryScalar();
				if($added == 0)
				{
					Yii::app()->db->createCommand('insert into ea_payment_terms (id_ea,payment_term,amount, milestone) VALUES ('.$this->id.',100,0,1067)')->execute(); 
				}
			}
		}
	}
	public function getTotalAmount(){
		$sum = 0;	$items = $this->eItems;
		if ($this->category == 25){
			foreach ($items as $item) {	$sum += ($item->amount*$item['man_days']);	}
		}else{
			foreach ($items as $item) {
				$sum += $item->amount;
			}
		}
		return $sum;
	}
	public function getTotalSandU(){
		$sum = 0;
		$items = $this->eItems;
		if ($this->category == 25){
			foreach ($items as $item){	$sum += ($item->getSUTotal());	}
		}else if($this->customization ==1)
		{
			$sum +=($this->support_amt * $this->support_percent)/100;
			//foreach ($items as $item){	$sum += ($item->getYearlySupportTotal());	}
		}		
		return $sum * (1 - $this->discount/100);
	}	
	public function getNetAmountWithExp(){
		if (!empty($this->expense) && $this->expense != 'N/A' && $this->expense != 'Actuals') {
			return $this->getNetAmount() + $this->expense;	
		}
		return $this->getNetAmount();
	}
	public function getTotalManDays(){ 
		$sum = 0; 
		$sum = (float) Yii::app()->db->createCommand("SELECT SUM(man_days) FROM eas_items 
			WHERE id_ea=".$this->id." ")->queryScalar(); 
			return $sum;
	}
	public static function getTotalManDaysPerEa($ea){
		$model= Eas::model()->findByPk($ea);
		$sum = 0;	$items = $model->eItems;
		foreach ($items as $item) {		$sum += $item->man_days;	}
		return $sum;
	}
	public function getActualMD()	{		
		if(isset($this->id_project) && ($this->id_project!='' || $this->id_project!=' ') ){
		$sum = (float) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time 
			WHERE id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase WHERE pp.id_project = {$this->id_project} ) and `default`='0'")
			->queryScalar();
		return $sum/8;
		}else{
			return 0;
		}
	}
	public function getNetAmount($inUsd = false){
		$netAmount = $this->getTotalAmount() * (1 - $this->discount/100); 
		if ($inUsd == true && $this->currency != CurrencyRate::OFFICIAL_CURRENCY){
			$rate = $this->rate;/*CurrencyRate::getCurrencyRate($this->currency);
			if (isset($rate['rate'])){
				$netAmount = $netAmount * $rate['rate'];
			}*/
			$netAmount = $netAmount * $rate;
		}
		return $netAmount;
	}
	public function getNetAmountWithExpOffshore($Offshore){
		if (!empty($this->expense) && $this->expense != 'N/A' && $this->expense != 'Actuals' && $Offshore=='Yes') {
			return $this->getNetAmountOffshore($Offshore) + $this->expense;	
		}
		return $this->getNetAmountOffshore($Offshore);
	}
	public function getNetAmountOffshore($Offshore, $inUsd = false){
		$netAmount = $this->getTotalAmountOffshore($Offshore) * (1 - $this->discount/100); 
		if ($inUsd == true && $this->currency != CurrencyRate::OFFICIAL_CURRENCY){
			$rate = $this->rate;
			$netAmount = $netAmount * $rate;
		}
		return $netAmount;
	}
	public function getTotalAmountOffshore($Offshore){
		$sum = 0;	$items = $this->eItems;
		if ($this->category == 25){
			foreach ($items as $item) {	
				if($item->offshore == $Offshore)
				{ $sum += ($item->amount*$item['man_days']); }
			}
		}else{
			foreach ($items as $item) {
				if($item->offshore == $Offshore)
				{	$sum += $item->amount;	}
			}
		}
		return $sum;
	}
	public function getManDayRate()	{
		$sum = 0;
		$man_days = 0;
		$items = $this->eItems;
		foreach ($items as $item) {
			$sum += $item->amount;
			$man_days += $item->man_days;
		}
		return (($man_days != 0) ? $sum/$man_days : 0);
	}
	public function getTMManDayRate(){
		$sum = 0;	$count = 0;	$items = $this->eItems;
		foreach ($items as $item) {
			$sum += $item->man_day_rate_n;	$count += 1;
		}
		return (($count != 0) ? $sum/$count : 0);
	}
	public function getNetManDayRate(){
		$sum = 0;	$man_days = 0;	$items = $this->eItems;
		foreach ($items as $item) {
			$sum += $item->amount;	$man_days += $item->man_days;
		}
		$net = $sum * (1 - $this->discount / 100);
		return (($man_days != 0) ? $net/$man_days : 0);
	}	
	public function getNetTMManDayRate(){
		$sum = 0;	$count = 0;	$items = $this->eItems;
		foreach ($items as $item) {
			$sum += $item->man_day_rate_n;	$count += 1;
		}
		$net = $sum * (1 - $this->discount / 100);
		return (($count != 0) ? $net/$count : 0);
	} 
	public function getActualRate(){
		$md = $this->getActualMD();
		if ($md) { 
			if($md<1){
					return (float)$this->getNetAmount();	
				}else{
					return (float)$this->getNetAmount() / $md;
				}

		}
		return 0;
	}
	public function getExpensesSpent(){
		$sum = (float) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time 
			WHERE id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase WHERE pp.id_project = {$this->id_project})")
			->queryScalar();
		return $sum/8;
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->select  = "t.*";	$criteria->with = array('eItems','eAuthor', 'customer', 'project');
		$criteria->together = true;
		if ($this->author){ 
			$split = explode(' ', $this->author, 2);
			if (count($split) == 2)	{
				$criteria->addCondition('(eAuthor.firstname = :author1 AND eAuthor.lastname = :author2) OR (eAuthor.firstname = :author2 AND eAuthor.lastname = :author1)');
				$criteria->params[':author1'] = $split[0];	$criteria->params[':author2'] = $split[1];
			}else{
				$criteria->addCondition('eAuthor.firstname = :author OR eAuthor.lastname = :author');
				$criteria->params[':author'] = $this->author;
			} 
		}
		$criteria->compare('customer.name', $this->customer_name, true);		$criteria->compare('t.ea_number', $this->ea_number, true); $criteria->compare('t.crmOpp', $this->crmOpp, true);
		$criteria->compare('t.tm', $this->TM, true);	$criteria->compare('project.id', $this->id_project, true);
		$criteria->compare('t.status', $this->status, true);	$criteria->compare('t.category', Eas::getIdCategoryByName($this->category), true);
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		         'attributes'=>array(
		            'eAuthor.fullname'=>array(
		                'asc'=>'eAuthor.firstname',
		                'desc'=>'eAuthor.firstname DESC',
		            ),
		            'eCurrency.codelkup'=>array(
		                'asc'=>'eCurrency.codelkup',
		                'desc'=>'eCurrency.codelkup DESC',
		            ),
	         		'customer.name'=>array(
	         				'asc'=>'customer.name',
	         				'desc'=>'customer.name DESC',
	         		),
	         		'project.name' => array(
	         				'asc'=>'project.name',
	         				'desc'=>'project.name DESC',
	         		),
	         		'eCategory.codelkup' => array(
	         				'asc'=>'category',
	         				'desc'=>'category DESC',
	         		),
	         		'amount'=> array(
	         				'asc'=>'eItems.amount',
	         				'desc'=>'eItems.amount DESC',
	         		),
	         		
		            '*',
		        ),
		    ),
		));
	}	
	public static function getDirPath($customer_id, $model_id)	{
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."eas".DIRECTORY_SEPARATOR.$model_id.DIRECTORY_SEPARATOR;
	 	if (!is_dir( $path)) {
            mkdir( $path, 0777, true);
            chmod( $path, 0777 );
        }
		return $path; 
	}
	public static function getDirPathSheet($customer_id, $model_id)	{
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."eas".DIRECTORY_SEPARATOR.$model_id.DIRECTORY_SEPARATOR."calculationSheet".DIRECTORY_SEPARATOR;
	 	if (!is_dir( $path)){     mkdir( $path, 0777, true);    chmod( $path, 0777 );  }
		return $path; 
	}	
	public function getFileSheet($path = false, $uploaded = false){		
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."eas". DIRECTORY_SEPARATOR .$this->id. DIRECTORY_SEPARATOR."calculationSheet".DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'eas'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR."calculationSheet".DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {	$filePath .= $this->file;	$fileName .= $this->file;
			}else{ return null; }
		}else {
			$filePath .= 'EA_'.$this->ea_number.'.pdf';	$fileName .= 'EA_'.$this->ea_number.'.pdf';	
		}		
		if (file_exists($filePath) && is_file($filePath)){	return $path ? $filePath : $fileName;	}
		return null;
	}
	public function getFilePDF(){		
		$fileName = str_replace('\\', '/', dirname(Yii::app()->request->scriptFile)) . '/uploads/customers/'.$this->id_customer.'/eas/'.$this->id;  
		if ($this->file) {	$fileName .= '/'.$this->file;	}
		return $fileName;
	}	
	public function getFile($path = false, $uploaded = false){		
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."eas". DIRECTORY_SEPARATOR .$this->id. DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'eas'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {	$filePath .= $this->file;	$fileName .= $this->file;	}
			else{		return null;	}
		}else {
			$filePath .= 'EA_'.$this->ea_number.'.pdf';	$fileName .= 'EA_'.$this->ea_number.'.pdf';	
		}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public function renderEANumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("eas/update", array("id" => $this->id)).'">'.$this->ea_number.'</a>';
	}	
	public function getFilename()	{
		$path = $this->getFile(true);
		if ($path != NULL)		{
			return pathinfo($path, PATHINFO_BASENAME);	
		}else{
			$path = $this->getFileSheet(true);
			if ($path != NULL){
				return pathinfo($path, PATHINFO_BASENAME);	
			}else{
				return NULL;
			}
		}
		return NULL;
	}	
	public static function getTemplateLabel($value)	{
		$list = self::getTemplateList($value);	return $list[$value];
	}
	public static function getTemplateList($currentStatus = null){	
		return array(
					self::TEMPLATE_DEFAULT => 'SCE Implementation',
					self::TEMPLATE_INTEGRATION => 'Integration',
					self::TEMPLATE_OPSI => 'Zeno Implementation',					
					self::TEMPLATE_ROLLOUT => 'Rollout',	
					self::TEMPLATE_CONSULTING => 'System Consultancy/ Training',
					self::TEMPLATE_CUSTOMIZATION =>'Customizations'	,
					self::TEMPLATE_INSTALLATION		=>'Installation'	,					
				); 
	}
	public static function getStatusList($currentStatus = null){	
		switch ($currentStatus){		
			case self::STATUS_APPROVED:
				$statuses = array(
					self::STATUS_APPROVED => 'Approved',					
					self::STATUS_CANCELLED => 'Cancelled',				
				); 
				break;
			case self::STATUS_PART_INVOICED:
				$statuses = array(
					self::STATUS_PART_INVOICED => 'Part Invoiced',
				); 
				break;
			case self::STATUS_FULLY_INVOICED:
				$statuses = array(
					self::STATUS_FULLY_INVOICED => 'Fully Invoiced',
				); 
				break;
			case self::STATUS_INVOICED:
				$statuses =  array(
					self::STATUS_APPROVED => 'Approved',
					self::STATUS_INVOICED => 'Invoiced',
					self::STATUS_CANCELLED => 'Cancelled',
				); 
				break;
			default:
				$statuses = array(
					self::STATUS_NEW => 'New',
					self::STATUS_APPROVED => 'Approved',
					self::STATUS_PART_INVOICED => 'Part Invoiced',
					self::STATUS_FULLY_INVOICED => 'Fully Invoiced',
					self::STATUS_CANCELLED => 'Cancelled',										
				); 
				break;
		}
		return $statuses;
	}	
	public static function getStatusLabel($value)	{
		$list = self::getStatusList($value);	return $list[$value];
	}	
	public function beforeSave(){
		if (parent::beforeSave()){	if ($this->isNewRecord){	$this->created = date('Y-m-d H:i:s');	}
			return true;
		}
		return false;
	}	
	protected function beforeValidate(){
		$this->discount = (float) $this->discount;
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }    
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public function getItems(){
		$criteria=new CDbCriteria;	
		$criteria->with = array('settingsCodelkup');
		$criteria->condition = 'id_ea = '.$this->id;	
		return new CActiveDataProvider('EasItems', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort'=>array(
    			'defaultOrder'=>'t.id ASC',  
            	'attributes'=>array(
		            'settingsCodelkup.codelkup'=>array(
		                'asc'=>'settingsCodelkup.codelkup',
		                'desc'=>'settingsCodelkup.codelkup DESC',
		            ),
		            '*',
		        ),
            ),
		));
	}    
	public static function getEaItemsCodelist($id_category){
		switch ((int)$id_category)
		{
			case 454:
    			$header = 'Support Service';
    			break;
    		case 496:
    			$header = 'Labels';
    			break;
    		case 623:
    			$header = 'Recruitment';
    			break;
			case 24:
    			$header = 'Training Course';
    			break;
			case 25:
			case 27:
				$header = 'Product';
				break;
			case 26:
				$header = 'Consultancy';
				break;
				break;
			default:
				$header = '';
		}
		return $header;		
	}	 
	public function getItemsCodelist(){
		return self::getEaItemsCodelist($this->category);
	}	
	public static function getCustomerByEA($ea){
 		return Yii::app()->db->createCommand()
    		->select('id_customer')
    		->from('eas')
    		->where('id =:id', array(':id'=>$ea))
    		->queryScalar();
 	}
	public function getTerms(){
		$criteria=new CDbCriteria;	
		$criteria->with = array('eMilestone');
		$criteria->condition = "(term_type !='sandu' or term_type is null) and id_ea = ".$this->id;	
		return new CActiveDataProvider('EaPaymentTerms', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id ASC',            
		        'attributes'=>array(
		            'eMilestone.codelkup'=>array(
		                'asc'=>'eMilestone.codelkup',
		                'desc'=>'eMilestone.codelkup DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}
	public function getTermsSANDUProvider(){
		$criteria=new CDbCriteria;	
		$criteria->with = array('eMilestone');
		$criteria->condition = "term_type ='sandu' and id_ea = ".$this->id;		
		return new CActiveDataProvider('EaPaymentTerms', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id ASC',            
		        'attributes'=>array(
		            'eMilestone.codelkup'=>array(
		                'asc'=>'eMilestone.codelkup',
		                'desc'=>'eMilestone.codelkup DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}	
	public function getNotes($all = false){
		if ($all){
			return Yii::app()->db->createCommand()
	    		->select('c.codelkup')
	    		->from('eas_notes n')
	    		->join('codelkups c', 'c.id=n.id_note')
	    		->where('id_ea =:id', array(':id'=>$this->id))
	    		->queryColumn();
		}
		return Yii::app()->db->createCommand()
	    		->select('id_note')
	    		->from('eas_notes')
	    		->where('id_ea =:id', array(':id'=>$this->id))
	    		->queryColumn();
	}	
	public function deleteAllNotes(){
		Yii::app()->db->createCommand()->delete('eas_notes', 'id_ea=:id', array(':id'=>$this->id));			
	}	
	public function isEditable(){
		return !in_array($this->status, array(Eas::STATUS_APPROVED, Eas::STATUS_INVOICED, Eas::STATUS_PART_INVOICED, Eas::STATUS_FULLY_INVOICED));
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN eas ON eas.id_customer=customers.id order by customers.name')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id'];		}
		return $customers;
	}
	public static function getAllCategories(){
		$result= Yii::app()->db->createCommand("SELECT  c.id, c.codelkup FROM codelkups c WHERE id_codelist=4 order by c.codelkup")->queryAll();
		$categories = array();
		foreach ($result as $i=>$res){	$categories[$i]['label'] = $res['codelkup'];	$categories[$i]['id'] = $res['id'];	}
		return $categories;		
	}	
	public static function getProjectsAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT projects.id, projects.name FROM projects INNER JOIN eas ON projects.id=eas.id_project order by projects.name')->queryAll();
		$projects = array();
		foreach ($result as $i=>$res){	$projects[$i]['label'] = $res['name'];	$projects[$i]['id'] = $res['id'];		}
		return $projects;
	}	
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN eas ON users.id=eas.author order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res)	{	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public function getTermsPayment(){
		$result = Yii::app()->db->createCommand("SELECT id,payment_term,milestone FROM ea_payment_terms WHERE id_ea = '$this->id'")->queryAll();
		return $result;
	}
	public static function getCategoryById($ea){
		$result = Yii::app()->db->createCommand("SELECT category FROM eas WHERE id = '$ea'")->queryScalar();
		return $result;
	}
	public static function getTermsPaymentwithoutSandU($ea){
		$result = Yii::app()->db->createCommand("SELECT id,payment_term,milestone,case when term_type= 'sandu' then 'S&U' else '' end term_type   FROM ea_payment_terms WHERE id_ea = '$ea'  order by term_type ")->queryAll();
		return $result;
	}
	public function getTermsPaymentwithoutSU(){
		$result = Yii::app()->db->createCommand("SELECT id,payment_term,milestone FROM ea_payment_terms WHERE id_ea = '$this->id' and (term_type<>'sandu' or term_type is null) ")->queryAll();
		return $result;
	}
	public static function cardinalNumber($number){
		switch ((int)$number)  	{
			case 1:
				return "First";
			break;
			case 2:
				return "Second";
			break;
			case 3:
				return "Third";
			break;
			case 4:
				return "Fourth";
			break;
			case 5:
				return "Fifth";
			break;
			case 6:
				return "Sixth";
			break;
			case 7:
				return "Seventh";
			break;
			case 8:
				return "Eighth";
			break;
			case 9:
				return "Ninth";
			break;
			case 10:
				return "Tenth";
			break;
			case 11:
				return "Eleventh";
			break;
			case 12:
				return "Twelfth";
			break;
			case 13:
				return "Thirteenth";
			break;
			case 14:
				return "Fourteenth";
			break;
			case 15:
				return "Fifteenth";
			break;
		}
	}
	public static function getIdCurrencyTraining($id_customer,$id) {
		$ea= Yii::app()->db->createCommand("SELECT id,currency from eas where id= $id AND id_customer = $id_customer")->queryRow();
		if(!empty($ea))
		{
			return $ea;
		}
		return $ea;
	}
	public static function getIdCurrency($id_customer,$id_project) {
		$ea= Yii::app()->db->createCommand("SELECT id,currency from eas where id_project = $id_project AND id_customer = $id_customer")->queryRow();
		if(!empty($ea))
		{
			return $ea;
		}else
		{
			$ea= Yii::app()->db->createCommand("SELECT id,currency from eas where id_parent_project = $id_project AND id_customer = $id_customer")->queryRow();
		}
		return $ea;
	}
	public static function getIdProjByEa($id_ea) {
		return Yii::app()->db->createCommand("SELECT id_project from eas where id=$id_ea ")->queryScalar();
	}
	public static function singleProject($id_customer, $project_name){
		$res = Yii::app()->db->createCommand("SELECT id from projects where customer_id = $id_customer AND name = '$project_name'")->queryRow();
		if($res != false){
			return false;
		}
		return true;
	}	
}?>