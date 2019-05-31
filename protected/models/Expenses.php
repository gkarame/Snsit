<?php
class Expenses extends CActiveRecord{
	const STATUS_PAID = 'Paid';	const STATUS_REJECTED = 'Rejected';	const STATUS_APPROVED = 'Approved';	const STATUS_SUBMITTED = 'Submitted';
	const STATUS_NEW = 'New';	public $customer_name;	public $project_id_t;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'expenses';
	}
	public function rules()	{
		return array(
			array('no, project_id, user_id, creationDate,startDate,endDate', 'required'),
			array('customer_id, project_id, currency, user_id', 'numerical', 'integerOnly'=>true),
			array('total_amount, billable_amount, payable_amount', 'numerical'),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers'),
			array('no', 'length', 'max'=>5),
			array('payable', 'length', 'max'=>3),
			array('status', 'length', 'max'=>11),
			array('billable', 'length', 'max'=>3),
			array('startDate, endDate', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy'),
			array('id, no, customer_id, project_id_t, project_id, status, startDate, endDate, currency, total_amount, billable, billable_amount, payable_amount, user_id, creationDate,customer_name', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
			'customer' => array(self::BELONGS_TO, 'Customers', 'customer_id'),
			'project' => array(self::BELONGS_TO, 'Projects', 'project_id'),
			'tTraining' => array(self::BELONGS_TO, 'Trainings', 'project_id'),
			'currency0' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'expensesDetails' => array(self::HAS_MANY, 'ExpensesDetails', 'expenses_id'),
			'expensesUploads' => array(self::HAS_MANY, 'ExpensesUploads', 'expenses_id'),
		);
	}
	public function beforeSave(){
		if (parent::beforeSave())	{
			$this->startDate = DateTime::createFromFormat('d/m/Y', $this->startDate)->format('Y-m-d');
			$this->endDate = DateTime::createFromFormat('d/m/Y', $this->endDate)->format('Y-m-d');
			return true;
		}
		return false;
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'no' => 'SHEET #',
			'customer_id' => 'Customer',
			'project_id' => 'Project',
			'status' => 'Status',
			'startDate' => 'From',
			'endDate' => 'To',
			'currency' => 'Currency',
			'total_amount' => 'Total Amount',
			'billable' => 'Billable',
			'billable_amount' => 'Billable Amount',
			'payable_amount' => 'Payable Amount',
			'user_id' => 'User',
			'creationDate' => 'Creation date',
		);
	}
	public function search()	{
		if (Yii::app()->controller->action->id == 'approval')	{
			$statuses = Groups::getExpenseApprovalStatuses( Groups::getExpensePermissions());
			$criteria=new CDbCriteria;
			$criteria->with = array('project', 'customer');			
			$criteria->compare('no',$this->no,true);
			$criteria->compare('customer.name', $this->customer_id, true);
			$criteria->compare('t.project_id',$this->project_id);
			$criteria->compare('t.status',$this->status,true);
			if (!empty($this->startDate)) $criteria->addCondition('startDate >= '.$this->startDate);
			if (!empty($this->endDate)) $criteria->addCondition('endDate <= '.$this->endDate);
			$criteria->compare('user_id', $this->user_id);
			if($this->status == ''){
				$criteria->addCondition('t.status != "Paid" && t.status != "Invoiced" && t.status != "Transferred"' );
			}
			if(!empty($this->currency))
			{
				$criteria->addCondition('t.user_id in (select id_user from user_personal_details where branch='.$this->currency.')' );
			}
			if($statuses == null){
				$criteria->compare('project.project_manager' ,Yii::app()->user->id);
				$criteria->compare('t.status',"Submitted");
			}else{
				if(count($statuses) == 1){
					$criteria->compare('project.project_manager' ,Yii::app()->user->id);
					$criteria->compare('t.status',"Submitted");
				}else{
					foreach ($statuses as $status)
						$value[] = '"'.$status.'"';
					$status_permission = implode(',', $value);
					$submitted = '"Submitted"';
					$approved='"Approved"';
					$criteria->addCondition('(t.status IN ('.$status_permission.') AND (project.project_manager = '. Yii::app()->user->id.' OR '.Yii::app()->user->id.' IN (SELECT id_user from user_groups where id_group in (17, 11)))) OR (t.status IN ('.$status_permission.') AND (project.business_manager = '. Yii::app()->user->id.' OR '.Yii::app()->user->id.' IN (SELECT id_user from user_groups where id_group in (17, 11)))) OR (t.status ='.$submitted.' AND (project.project_manager = '. Yii::app()->user->id.' OR '.Yii::app()->user->id.' IN (SELECT id_user from user_groups where id_group in (17, 11)))) OR (t.status ='.$submitted.' AND (project.business_manager = '. Yii::app()->user->id.' OR '.Yii::app()->user->id.' IN (SELECT id_user from user_groups where id_group in (17, 11)))) OR ( ('.Yii::app()->user->id.' IN (SELECT id_user from user_groups where id_group in (17, 11))) AND (t.status='.$approved.' OR t.status="Paid" OR  t.status="Transferred" ) )');	
				}
			}		
			return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,'pagination'=>array(
					'pageSize' => 50,
			),
				'sort'=>array(
	    			'defaultOrder'=>'no DESC',            
			         'attributes'=>array(
		         		'customer.name'=>array(
	         				'asc'=>'customer.name',
	         				'desc'=>'customer.name DESC',
		         		),
		         		'project.name' => array(
		         			'asc'=>'project.name',
		         			'desc'=>'project.name DESC',
		         		),
			            '*',
			        ),
		    	),
			));
		}else{
			$criteria=new CDbCriteria;	$criteria->with = array('project', 'customer');	$criteria->compare('no', $this->no,true);
			$criteria->compare('customer.name', $this->customer_id, true);	$criteria->compare('t.project_id',$this->project_id);
			$criteria->compare('t.status', $this->status, true);	$criteria->compare('t.user_id', Yii::app()->user->id);
			return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,'pagination'=>array(
					'pageSize' => 50,
			),
				'sort'=>array(
	    			'defaultOrder'=>'no DESC',            
			         'attributes'=>array(
		         		'customer.name'=>array(
	         				'asc'=>'customer.name',
	         				'desc'=>'customer.name DESC',
		         		),
		         		'project.name' => array(
		         			'asc'=>'project.name',
		         			'desc'=>'project.name DESC',
		         		),
			            '*',
			        ),
		    	),
			));
		}
	}
	public static function getStatusList()	{		
		return array(
			self::STATUS_PAID => 'Paid',
			self::STATUS_REJECTED => 'Rejected',
			self::STATUS_APPROVED => 'Approved',
			self::STATUS_SUBMITTED => 'Submitted',
			self::STATUS_NEW => 'New',
		); 
	}	
	public function getFiles()	{
		$path = "uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.
		$this->customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$dirName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;
		$files = Yii::app()->db->createCommand( "SELECT file FROM `expenses_uploads` WHERE expenses_id='$this->id'")->queryColumn();
		$existing = array();	$i = 0;
		foreach ($files as $file){
			if (file_exists($dirPath.$file) && is_file($dirPath.$file))	{
				$existing[$i]['path'] = $path.$file;	$existing[$i++]['url'] = $path.$file; 
			}else{
				Yii::app()->db->createCommand( "DELETE FROM `expenses_uploads` WHERE expenses_id='$this->id' && file='$file'")->execute();
				Yii::app()->db->createCommand("UPDATE `expenses` SET number_file=number_file-1 WHERE id='{$this->id}'")->execute();
			}
		}
		return $existing;
	}	
	public static function getDirPathBankTransfer($customer_id, $model_id){
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.
			"customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR.'expenses'.DIRECTORY_SEPARATOR.$model_id.DIRECTORY_SEPARATOR;
		if (!is_dir($path)) {	mkdir( $path, 0777, true);		chmod( $path, 0777 );	}
		return $path;
	}	
	public static function getDirPath($customer_id, $model_id)	{
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.
			"customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.$model_id.DIRECTORY_SEPARATOR;
		if( !is_dir( $path ) ) {      mkdir( $path, 0777, true);   chmod( $path, 0777 );       }
		return $path; 
	}
	public static function getDirPathExp($customer_id, $model_id)	{
		$customer_id = (int)$customer_id;	$model_id = (int)$model_id;		
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.
			"uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.$customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR;		
		if (!is_dir($path)) {      mkdir( $path, 0777, true);      chmod( $path, 0777 );       }
		return $path; 
	}	
	public function getFile($path = false, $uploaded = false)	{
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR
			.$this->customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR
			.$this->customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR;		
		$query = "SELECT file FROM `expenses_uploads` WHERE expenses_id='$this->id'";
		$filename = Yii::app()->db->createCommand($query)->queryAll();		$filepath = array();
		foreach($filename as $file)	{		$filepath[] = $filePath.$file['file'];		}
		return array('filename'=>$filename, 'path'=>$filepath);
	}	
	public function getFileBankTransfer($path = false, $uploaded = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR
			."customers".DIRECTORY_SEPARATOR.$this->customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.
			$this->customer_id.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;		
		if ($uploaded) 	{
			if ($this->file){	$filePath .= $this->file;			$fileName .= $this->file;}
			else{	return null;}
		}else{	$filePath .= 'BANK_TRANSFER_'.$this->no.'.pdf';	$fileName .= 'BANK_TRANSFER_'.$this->no.'.pdf';	}		
		if (file_exists($filePath) && is_file($filePath)){	return $path ? $filePath : $fileName;	}
		return null;
	}	
	public function renderExpensesNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("expenses/view", array("id" => $this->id)).'">'.$this->no.'</a>';
	}
	public function renderExpensesNumberApproval(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("expenses/view", array("id" => $this->id)).'?option=approval">'.$this->no.'</a>';
	}
	public function getEaId(){
		$result = Yii::app()->db->createCommand("SELECT id,currency FROM eas WHERE id_project = '$this->project_id'")->queryRow();
		return $result;
	}
	public static function getNetAmount($model,$currency){
		$billable = 0;	$items = $model->expensesDetails;
		foreach ($items as $item){	if ($item->billable == 'Yes') $billable += $item->amount; }		
	//	if ($currency != CurrencyRate::OFFICIAL_CURRENCY){
	//		$rate = CurrencyRate::getCurrencyRate($currency);
	//		if (isset($rate['rate'])){		$billable = $billable * $rate['rate'];	}
	//	}		
		return array(
					'billable_amount'=> Utils::formatNumber($billable), 
		);
	}	
	public static function getNetAmountNoFormat($model,$currency){
		$billable = 0;		
		$items = $model->expensesDetails;
		foreach ($items as $item){
			if ($item->billable == 'Yes') $billable += $item->amount;
		}		
		//if ($currency != CurrencyRate::OFFICIAL_CURRENCY){
		//	$rate = CurrencyRate::getCurrencyRate($currency);
		//	if (isset($rate['rate'])){	$billable = $billable * $rate['rate'];	}
		//}		
		return array(
			'billable_amount'=> $billable, 
		);
	}
	public static function getLastExpenses($status = 'Submitted'){
		$expenses =  Yii::app()->db->createCommand("SELECT users.id AS userid, users.firstname, users.lastname, count(expenses.id) as total, projects.name AS nameProject, projects.project_manager
		FROM expenses 
		LEFT JOIN users ON (expenses.user_id = users.id) 
		LEFT JOIN projects ON (expenses.project_id = projects.id)
		WHERE expenses.status = 'Submitted' and users.id<> projects.project_manager
		GROUP BY projects.project_manager, projects.name 
union
SELECT users.id AS userid, users.firstname, users.lastname, count(expenses.id) as total, projects.name AS nameProject, projects.business_manager
		FROM expenses 
		LEFT JOIN users ON (expenses.user_id = users.id) 
		LEFT JOIN projects ON (expenses.project_id = projects.id)
		WHERE expenses.status = 'Submitted' and users.id= projects.project_manager
		GROUP BY projects.business_manager, projects.name
order by userid ASC
")->queryAll();		
		return $expenses;
	}
	public static function getUserLastExpenses($status = 'Submitted'){
		$user = Yii::app()->user->id;
		$expenses =  Yii::app()->db->createCommand("select count(1) from expenses where status='Submitted' and user_id='".$user."' ")->queryScalar();
		return $expenses;
	}
	public static function getBranchByUser($id_user){
		$Branch =  Yii::app()->db->createCommand("SELECT branch from user_personal_details where id_user=".$id_user."")->queryScalar();			 
		 return $Branch;
	}	
	public static function getRateByBranch($branch){ $rat="";
		switch ($branch) {
		case '31':
			$rat = Yii::app()->db->createCommand()
					->select('rate')
					->from('currency_rate')					
					->where('currency=1')->queryScalar();
			break;
		case '689':
			$rat = Yii::app()->db->createCommand()
					->select('rate')
					->from('currency_rate')					
					->where('currency=9')->queryScalar();
			break;
		case '56':
			$rat = Yii::app()->db->createCommand()
					->select('rate')
					->from('currency_rate')					
					->where('currency=168')->queryScalar();
			
			break;
		case '120':
		$rat = Yii::app()->db->createCommand()
					->select('rate')
					->from('currency_rate')					
					->where('currency=169')->queryScalar();
			break;
		case '121':
			$rat = Yii::app()->db->createCommand()
					->select('rate')
					->from('currency_rate')					
					->where('currency=197')->queryScalar();
			
			break;
		case '452':
			$rat = Yii::app()->db->createCommand()
					->select('rate')
					->from('currency_rate')					
					->where('currency=167')->queryScalar();
			
			break;
		}		
		return $rat;
	}	
	public static function getCurrencyByBranch($branch){ $rat="";
		switch ($branch) {
		case '31':
			$rat = Yii::app()->db->createCommand()
					->select('codelkup')
					->from(' codelkups ')					
					->where('id=9 and id_codelist=8')->queryScalar();
			break;
		case '56':
			$rat = Yii::app()->db->createCommand()
				->select('codelkup')
					->from(' codelkups ')	
					->where('id=168 and id_codelist=8')->queryScalar();	
			break;
		case '120':
		$rat = Yii::app()->db->createCommand()
					->select('codelkup')
					->from(' codelkups ')
					->where('id=169 and id_codelist=8')->queryScalar();	
			break;
		case '121':
			$rat = Yii::app()->db->createCommand()
				->select('codelkup')
					->from(' codelkups ')		
					->where('id=197 and id_codelist=8')->queryScalar();
			break;
		case '452':
			$rat = Yii::app()->db->createCommand()
				->select('codelkup')
					->from(' codelkups ')		
					->where('id=167 and id_codelist=8')->queryScalar();
			
			break;
		}		
		return $rat;
	}	
	public static function changeStatusInvoiced($id){
		$status = "Invoiced";
		Yii::app()->db->createCommand("UPDATE expenses SET status = '$status' WHERE id = $id ")->execute();
		return true;
	}
	public static function changeStatusTransfered($id){	
		Yii::app()->db->createCommand("UPDATE expenses SET status = 'Transferred' WHERE id = $id ")->execute();
		return true;
	}
}?>