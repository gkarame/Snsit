<?php
class TandM extends CActiveRecord{
	const STATUS_NEW = "New";	const STATUS_APPROVED = "Approved";	const STATUS_PRINTED = "Printed";
	const STATUS_CANCELLED = "Cancelled";	const STATUS_TO_PRINT = "To Print";	const STATUS_PAID = "Paid";	
	public $project_manager, $eanb, $textdays, $invoice_description;	public $customErrors = array();	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'tandm';
	}
	public function rules(){
		return array(
            array('id_project, id_user , ', 'required'),
            array('amount_time,amount_budget', 'numerical', 'integerOnly'=>true),          
            array('status, tandm_month , tandm_year ,ea_number ', 'length', 'max'=>255),  
            array('notes', 'length', 'max'=>2000), 
            array('project_manager', 'exist', 'attributeName' => 'project_manager', 'className' => 'Projects'),  
            array('id,id_project,id_user,status,date ,ea_number', 'safe', 'on'=>'search'),
        );
	}
	public function relations(){
		return array(		
			'project' => array(self::BELONGS_TO, 'Projects', 'id_project'),
			'iCurrency' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'iUnit' => array(self::BELONGS_TO, 'Codelkups', 'sold_by'),
			'iAuthor' => array(self::BELONGS_TO, 'Codelkups', 'partner'),
			'idExpense' => array(self::BELONGS_TO, 'Expenses', 'id_expenses'),
			'eItems' => array(self::HAS_MANY, 'InvoicesExpenses', 'id_invoice'),
			'uUser' => array(self::BELONGS_TO, 'Users', 'id_resource'),			
		);
	}
	public function attributeLabels(){
		return array(          
        );
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->select  = "t.id,t.id_project,  t.tandm_month , t.tandm_year, t.id_user, t.status";
		$criteria->join = 'LEFT JOIN projects  ON projects.id = t.id_project'; 	$criteria->addCondition('projects.name LIKE :tn');		
		$criteria->params[':tn'] = "%".$this->id_project."%";	
		if(!empty($this->notes) && $this->notes!='')
		{	
			$criteria->addCondition('projects.customer_id = :ten');	
			$cust= Customers::getIdByName($this->notes);
			$criteria->params[':ten'] = $cust;
		}	
		
		if ($this->project_manager){ 	
			$criteria->join.=' LEFT JOIN users ON users.id=projects.project_manager';	$split = explode(' ', $this->project_manager, 2);
			if (count($split) == 2)	{
				$criteria->addCondition('(users.firstname = :project_manager1 AND users.lastname = :project_manager2) OR (users.firstname = :project_manager2 AND users.lastname = :project_manager1)');
				$criteria->params[':project_manager1'] = $split[0];		$criteria->params[':project_manager2'] = $split[1];
			}else{
				$criteria->addCondition('users.firstname = :project_manager OR users.lastname = :project_manager');	$criteria->params[':project_manager'] = $this->project_manager;
			} 
		}
        $criteria->compare('t.tandm_month',$this->tandm_month);      $criteria->compare('t.ea_number',$this->ea_number);
 		$criteria->compare('t.tandm_year',$this->tandm_year);
		if(isset($this->status)){	$criteria->compare('t.status',$this->status);	}else{		$criteria->compare('t.status','New');	 }
		$criteria->group ="t.id_project , t.tandm_month , t.tandm_year"  ;  
        return new CActiveDataProvider('TandM', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),           
		));
	}
	public function renderEANumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("eas/update", array("id" => Projects::getEAid($this->id_project))).'">'.Projects::getEAid($this->id_project).'</a>';
	}
	public static function getDirPath($customer_id, $model_id,$snsi = false,$snsapj = false){
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		if ($snsi == true){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR."snsi".DIRECTORY_SEPARATOR;
		}else if ($snsapj == true){
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR."snsapj".DIRECTORY_SEPARATOR;
		}else{
			$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."invoices".DIRECTORY_SEPARATOR;
		}
		if ( !is_dir( $path )) {   mkdir( $path, 0777, true); chmod( $path, 0777 );     }
		return $path; 
	}	
	public static function getDirPathShare($customer_id, $model_id){
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer_id}/invoices/share/";
	 	if (!is_dir( $path ) ) {   mkdir( $path, 0777, true);  chmod( $path, 0777 ); }
		return $path; 
	}
	public function getFilePrinted($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		//$final_invoice_number = Yii::app()->db->createCommand("SELECT final_invoice_number FROM invoices WHERE id = {$this->id}")->queryScalar();
		if (Codelkups::getCodelkup($this->partner) == 'SPAN' && $this->old == "Yes"){
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';
			$fileName .= 'INVOICE_'.str_replace('/','_',$this->invoice_number).'.pdf';	
		}else{
			$filePath .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	$fileName .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	
	public static function validateUserPerm(){

				$value = Yii::app()->db->createCommand("SELECT count(1) FROM user_groups WHERE id_user =".Yii::app()->user->id." and id_group in (1, 11, 14,20) ")->queryScalar();	
				if($value>0){
					return true;
				}			else{
					return false;
				}
	}

	public static function getFinalInvNumberById($id){
			return	$value = Yii::app()->db->createCommand("SELECT final_invoice_number FROM invoices WHERE id =$id ")->queryScalar();				
	}
	 public static function getInvNumberById($id){		
		return	$value = Yii::app()->db->createCommand("SELECT invoice_number FROM invoices WHERE id =$id ")->queryScalar();				
	}
	public function getFile($path = false, $uploaded = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR;
		if ($uploaded){
			if ($this->file){	$filePath .= $this->file;	$fileName .= $this->file;
			}else{	return null;	}
		}else {
			if (Codelkups::getCodelkup($this->partner) == 'SPAN' && $this->old == "Yes"){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->old_sns_inv).'.pdf';		$fileName .= 'INVOICE_'.str_replace('/','_',$this->old_sns_inv).'.pdf';	
			}else if(Codelkups::getCodelkup($this->partner) == 'SNSI' || Codelkups::getCodelkup($this->partner)=='SNS AUST'){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';		$fileName .= 'INVOICE_'.str_replace('/','_',$this->partner_inv).'.pdf';	
			}else if(Codelkups::getCodelkup($this->partner) == 'SNS APJ'){
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->snsapj_partner_inv).'.pdf';		$fileName .= 'INVOICE_'.str_replace('/','_',$this->snsapj_partner_inv).'.pdf';	
			}else{
				$filePath .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	$fileName .= 'INVOICE_'.str_replace('/','_',$this->final_invoice_number).'.pdf';	
			}
		}
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getFileMore($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile)."/uploads/invoices/INVOICE_".date('Y-m-d').'.pdf';
		$fileName = Yii::app()->getBaseUrl(true)."/uploads/invoices/INVOICE_".date('Y-m-d').'.pdf';		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getDirPathMoreInv(){
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/invoices/";
	 	if( !is_dir( $path ) ) 
	 	{
            mkdir( $path, 0777, true);
            chmod( $path, 0777 );
        }
		return $path; 
	}	
	public function getFileShare($path = false, $uploaded = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->id_customer. DIRECTORY_SEPARATOR ."invoices".  DIRECTORY_SEPARATOR."share". DIRECTORY_SEPARATOR ;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'invoices'.DIRECTORY_SEPARATOR.'share'.DIRECTORY_SEPARATOR;
		if ($uploaded) {
			if ($this->file) {
				$filePath .= $this->file;	$fileName .= $this->file;
			}else{
				return null;
			}
		}else {
			$filePath .= 'INVOICE_'.$this->invoice_number.'.pdf';	$fileName .= 'INVOICE_'.$this->invoice_number.'.pdf';	
		}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public function getFinalFilename(){
		$path = $this->getFile(true);
		if ($path != NULL){
			return pathinfo($path, PATHINFO_BASENAME);	
		}
		return NULL;
	}	
	public function getFilename(){
		if (isset($this->final_invoice_number))
			$path = $this->getFilePrinted(true);
		else
			$path = $this->getFileShare(true);
		if ($path != NULL){
			return pathinfo($path, PATHINFO_BASENAME);	
		}
		return NULL;
	}	
	public function renderInvoiceNumber(){
		if($this->final_invoice_number != null)
			$no = $this->final_invoice_number;
		else 
			$no = $this->id;
		echo '<a class="show_link" href="'.Yii::app()->createUrl("invoices/view", array("id" => $this->id)).'">'.$no.'</a>';
	}
	public function getDescriptionGrid(){	
		$arr = Utils::getShortText($this->invoice_title, 22);
		if ($this->invoice_title != null)
		return '<div class="first_it panel_container">'
				.'<div class="item_clip clip">'.$arr['text'].'</div>'
				.'<u class="red">+</u>'
				.'<div class="panel" style = "left:80px">'
					.'<div class="phead"></div>'
					.'<div class="pcontent"><div class="cover">'.$this->invoice_title.'</div></div>'
					.'<div class="pftr"></div>'
				.'</div>'
			.'</div>';
	}
	public static function getResources($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("SELECT distinct id_user FROM user_time u WHERE u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='".$id_project."') and MONTH(u.date)='".$month."' and YEAR(u.date)='".$year."' ")->queryAll();
			$users= array();
			foreach ($id_resc as $value) {
				$users[]= "<span class='".TandM::checkpendingTimesheet($value['id_user'], $month ,$year)."'>".Users::getNameByID($value['id_user'])."</span>";
			}
		$resources= implode ( ", ",$users);		
		echo $resources;
	}
	public static function checkpendingTimesheet($id_user , $month ,$year){	$timesheets = Yii::app()->db->createCommand("SELECT count(1) FROM timesheets WHERE (MONTH(week_start)= MONTH(CURRENT_DATE()) OR (status='New' and MONTH(week_start)='".$month."')) and id_user='".$id_user."' and MONTH(week_start)='".$month."' and YEAR(week_start)='".$year."' ")->queryScalar();
		if($timesheets>0){	return "red";	}else{	return " ";		}
	}
	public static function checkAllpendingTimesheet($id_project , $month ,$year){
		$id_resc = Yii::app()->db->createCommand("SELECT distinct id_user FROM tandm WHERE id_project='".$id_project."' and tandm_month='".$month."' and tandm_year='".$year."' and id_user in (SELECT distinct id_user FROM user_time u WHERE u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='".$id_project."') and MONTH(u.date)='".$month."' and YEAR(u.date)='".$year."')")->queryAll();
			foreach ($id_resc as $value) {		
				$timesheets = Yii::app()->db->createCommand("SELECT count(1) FROM timesheets WHERE status='New' and id_user='".$value['id_user']."' and MONTH(week_start)='".$month."' and YEAR(week_start)='".$year."' ")->queryScalar();
				if($timesheets>0){	return true;	}else{		return false;	}
			}
	}
	public static function getResourcesIDNotAust($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("SELECT distinct id_user FROM user_time u WHERE id_user!=16 and u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='".$id_project."') and MONTH(u.date)='".$month."' and YEAR(u.date)='".$year."' ")->queryAll();
		$resources="";
		foreach ($id_resc as $value) {		$resources.=" ".$value['id_user']." , ";	}
		return $resources;
	}
	public static function getResourcesID($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("SELECT distinct id_user FROM user_time u WHERE u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='".$id_project."') and MONTH(u.date)='".$month."' and YEAR(u.date)='".$year."' ")->queryAll();
		$resources="";
		foreach ($id_resc as $value) {		$resources.=" ".$value['id_user']." , ";	}
		return $resources;
	}
	public static function getAmounttimeByProject($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("select sum(amount) from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user in (".TandM::getResourcesID($id_project , $month ,$year)." 0) and MONTH(date)='".$month."' and YEAR(date)='".$year."'")->queryScalar();
		if (empty($id_resc) || $id_resc==null){	$id_resc=0;	}		return $id_resc;
	}
	
	public static function getAmounttimeByProjectbillableNotAust($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("	select sum(amount) from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes' and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user in (".TandM::getResourcesIDNotAust($id_project , $month ,$year)." 0) and MONTH(date)='".$month."' and YEAR(date)='".$year."'")->queryScalar();
		if (empty($id_resc) || $id_resc==null)	{	$id_resc=0;	}
		return $id_resc;
	}
	public static function getAmounttimeByProjectbillable($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("	select sum(amount) from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes' and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user in (".TandM::getResourcesID($id_project , $month ,$year)." 0) and MONTH(date)='".$month."' and YEAR(date)='".$year."'")->queryScalar();
		if (empty($id_resc) || $id_resc==null)	{	$id_resc=0;	}
		return $id_resc;
	}
	public static function getAmounttimeByProjectbillableAust($id_project , $month ,$year){	$id_resc = Yii::app()->db->createCommand("	select sum(amount) from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes' and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user ='16' and MONTH(date)='".$month."' and YEAR(date)='".$year."'")->queryScalar();
		if (empty($id_resc) || $id_resc==null)	{	$id_resc=0;	}
		return $id_resc;
	}
	public static function getHoursByProjectbillablePerRes($id_project , $month ,$year, $res){	$id_resc = Yii::app()->db->createCommand("	select sum(amount) from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes' and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user = '".$res."' and MONTH(date)='".$month."' and YEAR(date)='".$year."'")->queryScalar();
		return $id_resc;
	}
	public static function getMDsByProjectbillablePerRes($id_project , $month ,$year, $res){	$id_resc = Yii::app()->db->createCommand("	select sum(amount) from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes' and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user = '".$res."' and MONTH(date)='".$month."' and YEAR(date)='".$year."'")->queryScalar();
		$id_resc= number_format(($id_resc/8), 2);	return $id_resc;
	}
	public static function getAmountByProject($id_project , $month ,$year){	
		$tot=0;
		$amounts = Yii::app()->db->createCommand("select sum(amount)as time ,id_user from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes'and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user in (".TandM::getResourcesID($id_project , $month ,$year)." 0) and MONTH(date)='".$month."' and YEAR(date)=".$year." group by id_user")->queryAll();
		foreach ($amounts as $value) {
			$sum=0;	$rate = Yii::app()->db->createCommand("SELECT ea_rate FROM tandm WHERE id_project='".$id_project."' and tandm_month='".$month."' and tandm_year='".$year."'  and id_user='".$value['id_user']."' " )->queryScalar();
			$sum= ($value['time']/8);	$tot = $tot + ($sum*$rate);
		}
		return $tot;
	}
	public static function getAmountByProjectPerUser($id_project , $month ,$year, $user){	
		$tot=0;
		$amounts = Yii::app()->db->createCommand("select sum(amount)as time ,id_user from user_time where id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.billable='Yes'and pt.id_project_phase=pp.id and pp.id_project='".$id_project."' ) and `default`=0 and amount>0 and id_user =".$user." and MONTH(date)='".$month."' and YEAR(date)=".$year." group by id_user")->queryAll();
		foreach ($amounts as $value) {
			$sum=0;	$rate = Yii::app()->db->createCommand("SELECT ea_rate FROM tandm WHERE id_project='".$id_project."' and tandm_month='".$month."' and tandm_year='".$year."'  and id_user='".$value['id_user']."' " )->queryScalar();
			$sum= ($value['time']/8);	$tot = $tot + ($sum*$rate);
		}
		return $tot;
	}		
	public static function getMonthByDate($tandm_month){			
		return $month=date("M",strtotime($tandm_month));
	}
	public static function getYearByDate($tandm_year){			
		return $month=date("Y",strtotime($tandm_year));
	}
	public static function getYears(){
		$start_year = date('Y',strtotime('now - 10 year'));	$end_year = date('Y',strtotime('now + 20 year'));
		for ($i=$start_year;$i<=$end_year;$i++)	{    	$years["{$i}"]="{$i}";	}		
		return $years;                   
	}
	public static function getYearsGrid(){
		$start_year = date('Y',strtotime('now -1 year'));
		$end_year = date('Y',strtotime('now + 20 year'));
		for ($i=$start_year;$i<=$end_year;$i++)	{    	$years["{$i}"]="{$i}";	}		
		return $years;                   
	}
	public static function getMonths(){
		$monthNames = array('','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');		
		for ($i=1;$i<=12;$i++)	{		$months["{$i}"]=Yii::t('default',$monthNames[$i]);	}
		return $months;
	}	
	public static function getInvoiceDate($month,$year,$id){		
	    $months = self::getMonths();    $years = self::getYearsGrid(); 
	    if(GroupPermissions::checkPermissions('general-eas','write')){
		    return CHtml::dropDownlist('months', $month, $months, array('prompt'=>'',
		        'class'     => 'status ',
		    	'onchange'=>'changeInvoiceDate('."value".',"month",'.$id.');',
		    )).CHtml::dropDownlist('years', $year, $years, array('prompt'=>'',
		        'class'     => 'status marginl10',
		    	'onchange'=>'changeInvoiceDate('."value".',"year",'.$id.')',
		    ));
	    }else{
	    	if($month == null)
	    		$months	= array();
	    	if($year == null)
	    		$years = array(); 
	    	return CHtml::dropDownlist('months', $month, $months,array('disabled'=>true)).CHtml::dropDownlist('years', $year, $years,array('disabled'=>true));
	    }
	}
	public static function getInvoiceDateEdit($month,$year,$id){		
	    $months = self::getMonths();    $years = self::getYearsGrid();
		if ($year == 0 || $month == 0)	{	    $month = date("m");	    $year = date("Y");	}		
	    return CHtml::dropDownlist('months', $month, $months, array(
	        'class'     => 'status ',
	    )).CHtml::dropDownlist('years', $year, $years, array(
	        'class'     => 'status marginl10',
	    ));
	}	
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }    
    public static function getNumberInvoicesToPrint($invoices = null)  { 
		$part = array();		$total = 0;		$mes1=" ";		$message = "";
		$sns = Maintenance::PARTNER_SNS; 	$snsaust = Maintenance::PARTNER_AUST;		$snsi = Maintenance::PARTNER_SNSI;	$snsapj = Maintenance::PARTNER_SNSAPJ;	$sns_span = Maintenance::PARTNER_SPAN;
		if ($invoices == null){		$message = 'You have to select at least one invoice!';
		}else{
			$ids = '('.implode(',',$invoices).')';
			$id_customers = Yii::app()->db->createCommand("SELECT id_customer FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND id IN $ids")->queryColumn();
			$ids_customers = '('.implode(',',$id_customers).')';
			if ($id_customers != null){
				$value = Yii::app()->db->createCommand("SELECT id FROM customers WHERE (ISNULL(bill_to_contact_email) OR ISNULL(bill_to_address) OR ISNULL(bill_to_contact_person)) AND id IN $ids_customers ")->queryScalar();
				if ($value != null){
					$message = "not bill";
					return $message;
				}
			}			
			$ids_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$ids_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $snsi AND id IN $ids ORDER BY partner")->queryScalar();
			$ids_snsaust = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $snsaust AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $snsapj AND id IN $ids ORDER BY partner")->queryScalar();			
			$ids_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_TO_PRINT."' AND partner = $sns_span AND id IN $ids ORDER BY partner")->queryScalar();
			if ($ids_sns == 0 && $ids_snsi == 0 && $ids_snsapj == 0 && $ids_sns_span == 0 && $ids_snsaust == 0){		$message = 'There are no invoices in status To Print';
			}else{
				if ($ids_sns+$ids_snsi != 0 ){
					if ($ids_sns+$ids_snsi > 1)	
						$message .= $ids_sns+$ids_snsi.' SNS Invoices to Print.<br> ';
					else 
						$message .= $ids_sns+$ids_snsi.' SNS Invoice to Print.<br> ';
				}
				if ($ids_snsi != 0 ){
					if ($ids_snsi > 1)
						$message .= $ids_snsi.' SNSI Invoices to Print.<br>';
					else 
						$message .= $ids_snsi.' SNSI Invoice to Print.<br>';
				}
				if ($ids_sns+$ids_snsaust != 0 ){
					if ($ids_sns+$ids_snsaust > 1)	
						$message .= $ids_sns+$ids_snsaust.' SNS Invoices to Print.<br> ';
					else 
						$message .= $ids_sns+$ids_snsaust.' SNS Invoice to Print.<br> ';
				}
				if ($ids_snsaust != 0 ){
					if ($ids_snsaust > 1)
						$message .= $ids_snsaust.' SNS AUST Invoices to Print.<br>';
					else 
						$message .= $ids_snsaust.' SNS AUST Invoice to Print.<br>';
				}
				if ($ids_sns+$ids_snsapj != 0 ){
					if ($ids_sns+$ids_snsapj > 1)	
						$message .= $ids_sns+$ids_snsapj.' SNS Invoices to Print.<br> ';
					else 
						$message .= $ids_sns+$ids_snsapj.' SNS Invoice to Print.<br> ';
				}
				if ($ids_snsapj != 0 ){
					if ($ids_snsapj > 1)
						$message .= $ids_snsapj.' SNS APJ Invoices to Print.<br>';
					else 
						$message .= $ids_snsapj.' SNS APJ Invoice to Print.<br>';
				}
				if ($ids_sns_span != 0 ){
					if ($ids_sns_span > 1)
						$message .= $ids_sns_span.' SPAN Invoices to Print.<br>';
					else 
						$message .= $ids_sns_span.' SPAN Invoice to Print.<br>';
				}
			}
			$idsp_sns = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns AND id IN $ids  ORDER BY partner")->queryScalar();
			$idsp_snsi = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsi AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_snsaust = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsaust AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_snsapj = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $snsapj AND id IN $ids ORDER BY partner")->queryScalar();
			$idsp_sns_span = Yii::app()->db->createCommand("SELECT Count(DISTINCT id_customer,id_project) FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND partner = $sns_span AND id IN $ids ORDER BY partner")->queryScalar();
			if ($idsp_sns > 0 || $idsp_snsi > 0 || $idsp_snsapj > 0 || $idsp_sns_span > 0 || $idsp_snsaust > 0){		$message = 'printed';	}
		}
		return $message;
    }    
    public static function changeEaStatus($id_ea){
    	if ($id_ea != null){
    		$id_ea = (int) $id_ea;   	$sum = 0;
	    	$results = Yii::app()->db->createCommand("SELECT payment_procente FROM invoices WHERE status = '".Invoices::STATUS_PRINTED."' AND id_ea = '{$id_ea}'")->queryAll();
	    	foreach ($results as $result){
	    		$sum += $result['payment_procente'];
	    	}
	    	if ($sum != 100){
	    		Yii::app()->db->createCommand("UPDATE eas SET status ='".Eas::STATUS_PART_INVOICED."' where id = '{$id_ea}' ")->execute();
	    	}else{
	    		Yii::app()->db->createCommand("UPDATE eas SET status ='".Eas::STATUS_FULLY_INVOICED."' where id = '{$id_ea}'")->execute();
	    	}
    	}    		
    }    
	public function renderNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("invoices/view", array("id" => $this->id)).'">'.Utils::paddingCode($this->id).'</a>';
	}
	public static function checkToPrintTravelInvoices($travel){		
		$id_project=$travel->id_project;	$id_customer=$travel->id_customer;	$id_res=$travel->id_user;	
		$result =  Yii::app()->db->createCommand('SELECT id FROM invoices where id_project='.$id_project.' and id_customer='.$id_customer.' and id_resource='.$id_res.' and status="To Print" and type="Travel Expenses" ' )->queryScalar();
		return $result;
	}	
	public static function UpdateInvoiceAmount($id_inv,$amount){
		Yii::app()->db->createCommand("UPDATE invoices SET amount = amount+{$amount} , gross_amount=gross_amount+{$amount} , net_amount=net_amount+{$amount} where id = {$id_inv} and status='To Print' ")->execute();
	}
	public static function UpdateALLInvoiceAmount($id_inv,$amount){
		Yii::app()->db->createCommand("UPDATE invoices SET amount = {$amount} , gross_amount={$amount} , net_amount={$amount} where id = {$id_inv} and status='To Print' ")->execute();
	}
	public static function gethoursbillableperea($ea, $month, $year){ 
		if($ea==116){ $ea=99014;}
		$project= Yii::app()->db->createCommand("SELECT id_project from tandm where ea_number=".$ea." and tandm_month=".$month." and tandm_year=".$year." ")->queryScalar();
		$hours= self::getAmounttimeByProjectbillable($project,$month,$year);	
		return $hours;
	}
	public static function gethoursbillablepereaNoAust($ea, $month, $year){
		$country= Customers::getCountryById(Eas::getCustomerByEA($ea));
		if($ea==116){ $ea=99014;}
		$project= Yii::app()->db->createCommand("SELECT id_project from tandm where ea_number=".$ea." and tandm_month=".$month." and tandm_year=".$year." ")->queryScalar();
		if($country == '398')
		{
			$hours= self::getAmounttimeByProjectbillableNotAust($project,$month,$year);
		}else{
			$hours= self::gethoursbillableperea($project,$month,$year);
		}	
				
		return $hours;
	}
	public static function gethoursbillablepereaAust($ea, $month, $year){
		if($ea==116){ $ea=99014;}
		$project= Yii::app()->db->createCommand("SELECT id_project from tandm where ea_number=".$ea." and tandm_month=".$month." and tandm_year=".$year." ")->queryScalar();
		$hours= self::getAmounttimeByProjectbillableAust($project,$month,$year);
		return $hours;
	}
	public static function changeStatusInv($model,$final_number){
		if ($model->final_invoice_number == null){
			if (Codelkups::getCodelkup($model->partner) == 'SPAN' && $model->old == "Yes"){
				$model->final_invoice_number = null;	$model->old_sns_inv=Utils::createOldInvNumber();
			}else{		$model->final_invoice_number = $final_number;	}			
			$model->status = Invoices::STATUS_PRINTED;		$model->printed_date = date('Y-m-d');			
			if ($model->partner == Maintenance::PARTNER_SNSI){
				$model->partner_inv = Utils::createInvNumberPartner();	$model->partner_status = 'Not Paid';
			}
			if ($model->partner == Maintenance::PARTNER_AUST){
				$model->partner_inv = Utils::createInvNumberPartnerAust();	$model->partner_status = 'Not Paid';
			}
			if ($model->partner == Maintenance::PARTNER_SNSAPJ){
				$model->snsapj_partner_inv = Utils::createInvNumberPartnerSNSAPJ();	$model->partner_status = 'Not Paid';
			}
			$pay_date = date('Y-m-d', strtotime( $model->printed_date."+1 month"));	$model->paid_date = $pay_date;	$model->save();	
			$customer_model = Customers::model()->findByPk((int)$model->id_customer);	$project_name = $model->project_name;	Invoices::changeEaStatus($model->id_ea);
		}
	}	
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN invoices ON invoices.id_customer=customers.id')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res){	$customers[$i]['label'] = $res['name'];	$customers[$i]['id'] = $res['id'];	}
		return $customers;
	}
	public static function getEasAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT eas.id, eas.ea_number FROM eas INNER JOIN invoices ON invoices.id_ea=eas.id')->queryAll();
		$eas = array();
		foreach ($result as $i=>$res){	$eas[$i]['label'] = $res['ea_number'];	$eas[$i]['id'] = $res['id']; }
		return $eas;
	}	
	public static function getInvoicesAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT id, final_invoice_number FROM invoices WHERE final_invoice_number IS NOT NULL')->queryAll();
		$inv = array();
		foreach ($result as $i => $res){	$inv[$i]['label'] = $res['final_invoice_number'];	$inv[$i]['id'] = $res['id']; }
		return $inv;
	}	
	public static function getAllPartners($id_invoice,$id_partner){
		$partners = Codelkups::getCodelkupsDropDown('partner');
		if(GroupPermissions::checkPermissions('general-eas','write')){
			return CHtml::dropDownlist('assigned_to', $id_partner, $partners, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_invoice.','."2".')',
		    	'style'=>'width:60px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_partner, $partners, array(
		       	'disabled'=>true,
		    	'style'=>'width:60px;border:none;'
		    ));
	    }
	}
	public static function getAllSoldBy($id_invoice,$id_sold){
		$sold = Codelkups::getCodelkupsDropDown('unit');
	    if(GroupPermissions::checkPermissions('general-eas','write')){
			return CHtml::dropDownlist('assigned_to', $id_sold, $sold, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_invoice.','."3".')',
		    	'style'=>'width:95px;border:none;',
		    	'prompt'=>""
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_sold, $sold, array(
		    	'disabled'=>true,	
	    		'style'=>'width:95px;border:none;',
		    	
		    ));
	    }
	}
	public static function getOld($id_invoice,$old){
		$old_val = array(
	        'Yes' => 'Yes',
	        'No' => 'No',
	    );
	    if(GroupPermissions::checkPermissions('general-eas','write')){
		    return CHtml::dropDownlist('assigned_to', $old, $old_val, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_invoice.','."4".')',
		    	'style'=>'border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $old, $old_val, array(
		        'disabled'=>true,
		    	'style'=>'border:none;'
		    ));
	    }
	}
	public static function getAllTandMProjects(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT p.id, p.name FROM projects p , eas e where p.id=e.id_project and e.TM=1 and p.status=1 order by p.name')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['name'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users INNER JOIN eas ON users.id=eas.author order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}
	public static function getRateDropdown($id,$id_project,$ea_rate){
		$allrates = TandM::getRateDropdownValues($id_project);		
		return CHtml::dropDownlist('assigned_to', $ea_rate, $allrates, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInputRate('."value".','.$id.',this.closest(\'tr\').childNodes[2].childNodes[0].getAttribute("value"))',
		    	'prompt'=>'',
		    	'style'=>'width:60px;border:none;'
		    ));
	}
	public static function getRateDropdownValues($id_project){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT e.id , ei.man_day_rate_n as rate FROM eas e , eas_items ei where e.id=ei.id_ea and e.TM=1 and e.id_project='.$id_project.' ')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$res['rate']] = $res['rate'];	}
		return $users;
	}
	public static function getStatusList(){		
		$all = array(
			'New' => 'New', 
			'Invoiced' => 'Invoiced'
			);
		return $all; 
	}
	public static function checkandInsert($proj){
		$tandms= Yii::app()->db->createCommand("select distinct id_user , pp.id_project , e.ea_number from  user_task u , projects_tasks p , projects_phases pp ,projects po ,eas e 
			where u.id_task=p.id and p.id_project_phase=pp.id and pp.id_project=po.id and po.status<>'2' and po.id=e.id_project and e.TM=1 and  pp.id_project='".$proj."' ")->queryAll();
		if (!empty($tandms)){
			foreach ($tandms as  $value) {
				$getexits=Yii::app()->db->createCommand("SELECT Count(*) from tandm where id_user='".$value['id_user']."' and id_project='".$value['id_project']."' and tandm_month= MONTH(CURRENT_DATE()) and tandm_year=YEAR(CURRENT_DATE()) and ea_number='".$value['ea_number']."'")->queryScalar();
				if ($getexits<1){
					$insert_tandm = Yii::app()->db->createCommand("Insert into tandm (id_user, id_project , tandm_month , tandm_year , status ,ea_number ) values ('".$value['id_user']."' , '".$value['id_project']."' , MONTH(CURRENT_DATE()), YEAR(CURRENT_DATE()) , 'New' ,'".$value['ea_number']."') ")->execute();
				}
			}
		}
	}
}?>