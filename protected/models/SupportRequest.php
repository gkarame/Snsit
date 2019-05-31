<?php
class SupportRequest extends CActiveRecord{
	const STATUS_OPEN = 0;			const STATUS_PENDING_INFO = 1;	
	const STATUS_IN_RESEARCH = 2;	const STATUS_IN_DEV = 3;
	const STATUS_RESOLVED = 4;		const STATUS_REOPENED = 5;	
	const STATUS_CLOSED = 6;		const STATUS_CANCELLED = 7;
	const STATUS_NOTCONFIRM = 8;
	public $statuss, $prio;

	public static function model($className=__CLASS__){		return parent::model($className);	}
	public function tableName(){
		return 'rsr';
	}
	public function rules(){
		return array(
			array('rsr_no,sr, id_customer, severity, short_description, schema, assigned_to, logged_by, product, category, submitter_name', 'required'),
			array('id_customer, version, status, dbms, product, category, root, customer_contact_id, logged_by, assigned_to', 'numerical', 'integerOnly'=>true),
			array('rsr_no', 'length', 'max'=>5),
			array('short_description', 'length', 'max'=>128),
			array('submitter_name, sr', 'length', 'max'=>50),
			array('schema', 'length', 'max'=>4),
			array('rsr_no, id_customer, assigned_to, severity, status, eta, statuss, product ,category ', 'safe', 'on'=>'search'),	);	
	}
	public function relations(){
		return array(
			'idCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'assigned_to'),
			'product0' => array(self::BELONGS_TO, 'Codelkups', 'product'),
			'version0' => array(self::BELONGS_TO, 'Codelkups', 'version')
		);	
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'rsr_no' => 'RSR#',
			'sr' => 'Linked SRs#',
			'id_customer' => 'Customer',
			'submitter_name' => 'Submitter',
			'severity' => 'Severity',
			'root' => 'Root Cause',
			'version' => 'WMS Version',
			'status' => 'Status',
			'dbms' => 'DB Type',
			'eta' => 'ETA',
			'short_description' => 'Description',
			'product' => 'Product',			
			'adddate' => 'Creation Date',
			'category' => 'Category',
			'schema' => 'Schema',
			'logged_by' => 'Logged By',	
			'assigned_to' => 'Assigned To',
			'closedate' => 'Closure Date'
			);	
	}
	public static function getRootList(){		
		return array(	
			0 => 'Connectivity Problem',
			1 => 'Database Performance',		
			2 => 'Integration Design',		
			3 => 'WMS Base Bug',	
			4 => 'WMS Customization',	
			5 => 'WMS Design',			
			6 => 'WMS Performance',	
		); 	
	}
	public static function getApprovalList(){		
		return array(	
			1 => 'Approved',		
			2 => 'Awaiting Approval',		
		); 	
	}
	public static function getRootLabel($value){
		if(isset($value) && $value != null)
		{
			$list = self::getRootList();
			return $list[$value];	
		}else{
			return '';
		}
	}
	public static function getCategories($id_rsr,$cat){
		$all=SupportRequest::getCategoryList();
		if(GroupPermissions::checkPermissions('general-rsr','write')) {
			return CHtml::dropDownlist('category', $cat,$all , array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_rsr.','."5".')',
		    	'style'=>'width:140px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('category', $cat, $all, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:140px;border:none;'
		    ));
	    }	
	}
	public static function getCategoryList(){		
		return array(		
			1 => 'Technical Support',	
			2 => 'Training',
		); 	
	}
	public static function getCategoryListShort(){		
		return array(	
			1 => 'TS',		
			2 => 'TR',
		); 	
	}
	public static function getCategoryShortLabel($value){
		if(isset($value) && $value != null)
		{
			$list = self::getCategoryListShort();
			return $list[$value];	
		}else{
			return '';
		}	
	}
	public static function getCategoryLabel($value){
		if(isset($value) && $value != null)
		{
			$list = self::getCategoryList();
			return $list[$value];	
		}else{
			return '';
		}	
	}
	public static function getPrivilegedStatusList($status){		
		if($status == self::STATUS_CLOSED){
			return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',	
				self::STATUS_CLOSED => 'Closed',	
			); 
		}else if ($status == self::STATUS_REOPENED)
		{
			return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',		
				self::STATUS_REOPENED => 'Reopened',	 
			); 
		}else if ($status == self::STATUS_NOTCONFIRM)
		{
			return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',		
				self::STATUS_REOPENED => 'Reopened',	
				self::STATUS_CANCELLED => 'Cancelled',
				self::STATUS_NOTCONFIRM => 'Not Confirmed'
			); 
		}else if ($status == self::STATUS_CANCELLED)
		{
			return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',		
				self::STATUS_REOPENED => 'Reopened',	
				self::STATUS_CANCELLED => 'Cancelled',
			); 
		}else{
			return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',
			); 
		}	
	}
	
	public static function validateCloseSRs($srs){	
		return Yii::app()->db->createCommand(" SELECT count(1) FROM `support_desk` where id in (".$srs.") and status<5 ")->queryScalar();	
	}


	public static function countTime($id, $customer, $srs){	
		$arr= explode(',', $srs); $str="(";
		foreach ($arr as $issue) {
			$str.=" u.srs like '%".$issue."%' or";
		}
		$str .=" 1=0 )";
		$times= Yii::app()->db->createCommand("select u.amount, s.rsr_id from user_time u, rsr_id s where u.`default`=1 and u.id = s.id_user_time and s.rsr_id like '%".$id."%' and u.amount>0  and id_task in (select id from default_tasks where id_parent=1324 and name= (select name from customers where id=".$customer." ) )")->queryAll();	
		$timesSR= Yii::app()->db->createCommand("select SUM(u.amount) from user_time u where u.`default`=1 and u.amount>0  and id_task in (select id from default_tasks where id_parent=1324 and name= (select name from customers where id=".$customer." ) ) and ".$str)->queryScalar();	
		$tot=0;
		foreach ($times as $time) {
			$arr= explode(',', $time['rsr_id']);
			if(in_array($id, $arr))
			{
				$tot += $time['amount']; 
			}
		}
		return ($tot + $timesSR);
	}


	public static function countSRs($srs){	
		return Yii::app()->db->createCommand(" SELECT count(1) FROM `support_desk` where id in (".$srs.")  ")->queryScalar();	
	}
	
	public static function getClosedLastm($customer)
	{
	 	return Yii::app()->db->createCommand("SELECT count(1) FROM rsr s where  s.id_customer=".$customer." and s.status =6 and s.closedate >= (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();

	}
	public static function getOpenLess($customer)
	{
	 	return Yii::app()->db->createCommand("SELECT count(1) FROM rsr s where  s.id_customer=".$customer." and s.status !=6 and s.adddate >= (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();

	}
	public static function getOpenMore($customer)
	{
	 	return Yii::app()->db->createCommand("SELECT count(1) FROM rsr s where  s.id_customer=".$customer." and s.status !=6 and s.adddate < (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();

	}
	public static function getOpenRsrs($customer){	
		$ids= Yii::app()->db->createCommand(" SELECT rsr_no,short_description FROM `rsr` where id_customer=".$customer." and status!=6 ")->queryAll();
		$dd = array();
		//$dd[0] = '';	
		foreach ($ids as $res){
			$dd[$res['rsr_no']] = $res['rsr_no'].' - ' .$res['short_description'];				
		}
		return $dd;
	}
	public static function getStatusList(){		
		return array(
			self::STATUS_OPEN => 'Open',
			self::STATUS_PENDING_INFO => 'Pending Info',
			self::STATUS_IN_RESEARCH => 'In Research',
			self::STATUS_IN_DEV => 'In Development',
			self::STATUS_RESOLVED => 'Resolved',
			self::STATUS_CLOSED => 'Closed',
			self::STATUS_REOPENED => 'Reopened',
				self::STATUS_CANCELLED => 'Cancelled',		
				self::STATUS_NOTCONFIRM => 'Not Confirmed'	
		); 	
	}

	public static function getStatusListSearch(){	

			if(Users::checkCSManagers(Yii::app()->user->id)== 0 )
			{
				return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',
				self::STATUS_CLOSED => 'Closed',
				self::STATUS_REOPENED => 'Reopened',
					self::STATUS_CANCELLED => 'Cancelled',		
			); 

			}	else{
				return array(
				self::STATUS_OPEN => 'Open',
				self::STATUS_PENDING_INFO => 'Pending Info',
				self::STATUS_IN_RESEARCH => 'In Research',
				self::STATUS_IN_DEV => 'In Development',
				self::STATUS_RESOLVED => 'Resolved',
				self::STATUS_CLOSED => 'Closed',
				self::STATUS_REOPENED => 'Reopened',
					self::STATUS_CANCELLED => 'Cancelled',		
					self::STATUS_NOTCONFIRM => 'Not Confirmed'	
			); 
			}
			
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList();
		return $list[$value];	
	}	
	public static function getSeverityLevel($value){			
		switch($value){
			case "High": 
				return "High";
				break;
			case "Medium":
				return "Medium";
				break;
			case "Low":
				return "Low";
				break;		
		}	
	}	
	public static function getTotalStatus($value){
		$result = Yii::app()->db->createCommand("Select count(1) from rsr where status = '$value' ")->queryScalar();
		return $result;	
	}
	public static function getRSR($value){
		return Yii::app()->db->createCommand("SELECT rsr_no FROM `rsr` where sr like '%".$value."%' ")->queryScalar();
	}
	public static function getSRs($value){
		$arrs= explode(",", $value);
		$links='';
		foreach ($arrs as $arr) {
			$links .=   "<a href=".Yii::app()->createAbsoluteUrl("supportDesk/update/".$arr)."> ".$arr." </a> ,";
		}
		$links= substr($links, 0, strlen($links)-1);
		return $links;
	}
	public static function getSRsTypes($value){
		$result = Yii::app()->db->createCommand("SELECT   GROUP_CONCAT(DISTINCT(category))  FROM support_desk where id in (".$value.") ")->queryScalar();
		$arrs= explode(",", $result);
		$links='';
		foreach ($arrs as $arr) {
			$links .=  SupportDesk::getCategoryLabel($arr).', ';  
		}
		$links= substr($links, 0, strlen($links)-2);
		return $links;
	}
	public static function getDirPathComm($customer,$model_id,$comm = null){
		$model_id = (int)$model_id;
		if($comm != null)
		{		$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportrequest/{$model_id}/comments/";
		}else{
			$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportrequest/{$model_id}/";
	 	}
		if (!is_dir( $path ) ) 
	 	{
            mkdir( $path, 0777, true);
            chmod( $path, 0777 );
        }
		return $path; 
	}

	public static function getDirPath($customer,$id_rsr = null)
	{
		if($id_rsr != null)
		{
			$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportrequest/{$id_rsr}/";
		}else{
			$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportrequest/";
		}
		if (!is_dir( $path ) ) 
	 	{
            mkdir( $path, 0777, true);
            chmod( $path, 0777 );
        }
		return $path; 
	}

	public function search($export = null,$circle=null){
		$criteria=new CDbCriteria;
		
		$criteria->with = array('idCustomer');
		$criteria->compare('t.id', $this->rsr_no,true);
		$criteria->compare('severity', $this->severity);
		$criteria->compare('product', $this->product);
		$criteria->compare('version', $this->version);
			
		if (isset($this->assigned_to) && $this->assigned_to != null){
			$last = explode("  ", $this->assigned_to);
			$criteria->join='LEFT JOIN users ON users.id=t.assigned_to';
			$criteria->addCondition('users.firstname LIKE :tn');
			$criteria->params[':tn']='%'.substr($this->assigned_to,0,strrpos($this->assigned_to,"  ")).'%';
			$criteria->addCondition('users.lastname LIKE :tr');
			$criteria->params[':tr']='%'.$last[1].'%';
		}
		$criteria->compare('idCustomer.name',$this->id_customer);

		if ((isset($this->status) && $this->status != "" || isset($this->statuss) && $this->statuss != "") && count($this->status) == count(array_filter($this->status, "strlen")))	{
			$statuss=$this->status;	$inv_status="";
        	foreach ($statuss as $value) {
        	$inv_status.="'".rtrim(ltrim($value," ")," ")."',";
        	}
        	$criteria->addCondition("t.status in (".$inv_status." '-1' ) ");
		}else{
			$criteria->addCondition("t.status in (0,1,2,3,4,5,8) ");
		}
			if((Users::checkCSManagers(Yii::app()->user->id)) == 0){
				$criteria->addCondition("t.status != 8 ");
			}
		//print_r($criteria);exit;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => ($export != null || $circle != null) ? 1000:Utils::getPageSize(),
            ),		
			'sort'=>array(
	    			'defaultOrder' => 't.rsr_no DESC'
			    ),
			));	
	}
	public function renderRSRNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("SupportRequest/update", array("id" => $this->id)).'">'.$this->rsr_no.'</a>';
	}

	public static function getRSRUsers($id_rsr,$id_user){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 and users.id in (select id_user from user_groups where id_group in (9,12,13,19,18) and id_user <>20) order by users.firstname, users.lastname')->queryAll();
		$users = array();
		$users[''] = ' ';
		foreach ($result as $i => $res){
			$users[$res['id']] = $res['firstname'].'  '.$res['lastname'];
		}
		if(GroupPermissions::checkPermissions('general-rsr','write')) {
			return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_rsr.','."2".')',
		    	'style'=>'width:140px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:140px;border:none;'
		    ));
	    }
	}

	public static function sendEmailAssigned($model, $oldUser){
		$notif = EmailNotifications::getNotificationByUniqueName('rsr_assigned_to');
		if ($notif != NULL){
    		$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = '".$model->customer_contact_id."'")->queryScalar();    		
    		$customer = Yii::app()->db->createCommand("SELECT name from customers WHERE id = '".$model->id_customer."'")->queryScalar();    		
			$to_replace_sub = array(			
				'{no}', 
    			'{customer_name}'
			);
			$replace_sub = array(
				 $model->rsr_no,
				$customer
			);
    		$subject = str_replace($to_replace_sub,$replace_sub, $notif['name']); 
    		$to_replace = array(
				'{customer_name}',
				'{no}', 
    			'{user_name}',
			);
			$replace = array(
				substr($name_customer, 0, strpos($name_customer, ' ')),
				'<a href="'.Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$model->id)).'">'.$model->rsr_no.'</a>',
				Users::getUsername($model->assigned_to),
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
			
			$email_assigned = Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user ='".$model->assigned_to."'")->queryScalar();
    		if (filter_var($email_assigned, FILTER_VALIDATE_EMAIL)){
    			//Yii::app()->mailer->AddAddress($email_assigned);
    		} 
		
			$email_old = Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user =".$oldUser."")->queryScalar();
		    if (filter_var($email_old, FILTER_VALIDATE_EMAIL))   {
		    	//Yii::app()->mailer->AddCcs($email_old);
				//Yii::app()->mailer->AddAddress($email_old);
		    } 

			$ca =  Customers::getCAbyCustomer($model->id_customer);
			$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);
			if(isset($ca)){
				$email_ca=UserPersonalDetails::getEmailById($ca);
				//Yii::app()->mailer->AddCcs($email_ca);
				//::app()->mailer->AddAddress($email_ca);
			}
			if(isset($account_manager)){
				$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
				//Yii::app()->mailer->AddCcs($email_account_manager);
				//Yii::app()->mailer->AddAddress($email_account_manager);
			}

			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
				foreach ($emails as $email) 
				{
					if (filter_var($email, FILTER_VALIDATE_EMAIL))  
					{	//Yii::app()->mailer->AddCcs($email);	
					//	Yii::app()->mailer->AddAddress($email);
					}
				}	
    	
			Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
    		Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    	Yii::app()->mailer->Send(true);
    	}	
    }

    public static function getStatusContor($provider){
		$status = array();
		for ($i=0; $i<9; $i++){
			$status[$i] = 0;
		}
		foreach ($provider->getData() as $data)	{
			if ($data->status !=  6 && $data->status !=  7)	{
				$status[$data->status]++;
			}
		}
		 unset($status[6]);
		  unset($status[7]);
		   unset($status[8]);
		return $status;
	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN rsr ON rsr.id_customer=customers.id')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res)
		{
			$customers[$i]['label'] = $res['name'];
			$customers[$i]['id'] = $res['id'];
		}
		return $customers;	
	}

	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){
			$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}		
		return $users;
	}	

	public static function getprivilegedstatuses($rsr,$status){
		
		$all=SupportRequest::getPrivilegedStatusList($status);
		if(GroupPermissions::checkPermissions('general-rsr','write')) {
			return CHtml::dropDownlist('status', $status,$all , array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $rsr.','."4".')',
		    	'style'=>'width:140px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('status', $status, $all, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:140px;border:none;'
		    ));
	    }	
	}

	public static function getAllApproval($rsr,$approved){
		
		$all=SupportRequest::getApprovalList();
		if(GroupPermissions::checkPermissions('general-rsr','write')) {
			return CHtml::dropDownlist('approved', $approved,$all , array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $rsr.','."7".')',
		    	'style'=>'width:140px;border:none;'
		    ));
	    }
	}

	public static function getAllstatuses($rsr,$status){
		
		$all=SupportRequest::getStatusList();
		if(GroupPermissions::checkPermissions('general-rsr','write')) {
			return CHtml::dropDownlist('status', $status,$all , array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $rsr.','."4".')',
		    	'style'=>'width:140px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('status', $status, $all, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:140px;border:none;'
		    ));
	    }	
	}

	public function getFiles($id_comm = null){
		if ($id_comm == null){
			$path = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'supportrequest'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
			$files = Yii::app()->db->createCommand( "SELECT filename FROM `rsr_files` WHERE id_rsr='$this->id'")->queryColumn();
		}else{
			$path = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'supportrequest'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR."comments".DIRECTORY_SEPARATOR;
			$files = Yii::app()->db->createCommand( "SELECT filename FROM `rsr_comm_files` WHERE id_comm='$id_comm'")->queryColumn();
		}
		$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$dirName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;
		$existing = array();
		foreach ($files as $file){
			if (file_exists($dirPath.$file) && is_file($dirPath.$file)){
				$existing[]['path'] = $path.$file;
			}else{
				if ($id_comm == null){
					Yii::app()->db->createCommand( "DELETE FROM `rsr_files` WHERE id_rsr='$this->id' && filename='$file'")->execute();
					Yii::app()->db->createCommand("UPDATE `rsr` SET files=files-1 WHERE id='{$this->id}'")->execute();
				}else{
					Yii::app()->db->createCommand( "DELETE FROM `rsr_comm_files` WHERE id_rsr='{$this->id}' && id_comm='{$id_comm}' && filename='$file'")->execute();
					Yii::app()->db->createCommand("UPDATE `rsr_comments` SET files=files-1 WHERE id='{$id_comm}'")->execute();	
				}			}		}		
		return $existing;	
	}	
	public function getComments()
	{
		return SupportRequest::getAllComments($this->id);	
	}	
	public static function getAllComments($id)
	{
		return Yii::app()->db->createCommand("SELECT * FROM rsr_comments WHERE id_rsr = '".(int)$id."' ORDER BY id DESC")->queryAll();
	}

	public static function sendEmail($id_rsr, $id_comm){
		$notif = EmailNotifications::getNotificationByUniqueName('rsr_update');

		$model = SupportRequest::model()->findByPk($id_rsr);
		$model_comm = SupportRequestComments::model()->findByPk($id_comm);
		$items = "";
		if ($notif != NULL) {
    		$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = '{$model->customer_contact_id}'")->queryScalar();
    		$to_replace = array('{no}', '{customer_name}');
    		$replace = array($model->rsr_no, $model->idCustomer->name);
    		$subject = str_replace($to_replace, $replace, $notif['name']);
    		
    		$histories = Yii::app()->db->createCommand("SELECT * FROM rsr_comments WHERE id_rsr = '{$model->id}' AND id != '{$model_comm->id}' ORDER BY date DESC")->queryAll();
    		if (!empty($histories))	{
    			foreach ($histories as $history)
    			{
    				$items .= "
    						<b>Date</b>: ".date("d/m/Y H:i:s",strtotime($history['date']))."
							<b>Comment From</b>: ".Users::getNameById($history['id_user'])."
							<b>Comment</b>: {$history['comment']}
							<b>Status</b>: ".SupportRequest::getStatusLabel($history['status'])."
							";
    			}    		}    		
    		
	    		$to_replace = array(
					'{customer_name}',
					'{no}', 
					'{date}',
					'{sns}', 
					'{comment}', 
	    			'{status}', 
					'{history}'
				);
				$replace = array(
					substr($name_customer, 0, strpos($name_customer, ' ')),
					'<a href="'.Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$model->id)).'">'.$model->rsr_no.'</a>',
					date("d/m/Y H:i:s",strtotime($model_comm->date)),
					Users::getUsername($model_comm->id_user),
					$model_comm->comment,
					SupportRequest::getStatusLabel($model_comm->status),
					$items
				);		

			$body = str_replace($to_replace, $replace, $notif['message']);	
    		Yii::app()->mailer->ClearAddresses();
    		Yii::app()->mailer->ClearCcs();
			$id_rep=Yii::app()->db->createCommand("SELECT cs_representative from customers WHERE id ='".$model->id_customer."'")->queryScalar();
			$email_csrep = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$id_rep." ")->queryScalar();
			Yii::app()->mailer->AddAddress($email_csrep);

			$id_assigned= Yii::app()->db->createCommand("SELECT assigned_to FROM `rsr` where rsr_no='".$model->rsr_no."' and id_customer='".$model->id_customer."'")->queryScalar();
			if($id_rep != $id_assigned){
				$email_assi = EmailNotificationsGroups::getLMNotificationUsers($id_rep);
					if (filter_var($email_assi, FILTER_VALIDATE_EMAIL)) {
						//	Yii::app()->mailer->AddCcs($email_assi);
							Yii::app()->mailer->AddAddress($email_assi);
							}
					}		

			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
				foreach ($emails as $email) 
				{
					if (filter_var($email, FILTER_VALIDATE_EMAIL))  
					{	//Yii::app()->mailer->AddCcs($email);	
				Yii::app()->mailer->AddAddress($email);
					}
				}	
					
	    			$ca =  Customers::getCAbyCustomer($model->id_customer);
					$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);
			
					if(isset($ca)){
						$email_ca=UserPersonalDetails::getEmailById($ca);
						//Yii::app()->mailer->AddCcs($email_ca);
						Yii::app()->mailer->AddAddress($email_ca);
					}
					if(isset($account_manager)){
						$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
						//Yii::app()->mailer->AddCcs($email_account_manager);
						Yii::app()->mailer->AddAddress(email_account_manager);
					}

					
			$files = $model->getFiles($model_comm->id);
			foreach ($files as $file)
			{
				Yii::app()->mailer->AddFile($file['path']);
			}			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    	Yii::app()->mailer->Send(true);
    	}
    }

    public static function sendEmailNew($model, $comment=''){
		$notif = EmailNotifications::getNotificationByUniqueName('new_rsr');
		if ($notif != NULL){
			$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/>A new RSR has been created, please find below the RSR information:<br/>";
            $message .= "<br/>- <b>Customer:</b> ".Customers::getNameById($model->id_customer);
            $message .= "<br/>- <b>RSR#:</b> ".'<a href="'.Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$model->id)).'">'.$model->rsr_no.'</a>';
           	$message .= "<br/>- <b>Title:</b> ".$model->short_description;
           	if(trim($comment)!=''){
				$message .= "<br/>- <b>Description:</b> ".$comment;
           	}
           	$message .= "<br/>- <b>Linked SRs#:</b> ".$model->sr;
           	$message .= "<br/>- <b>Severity:</b> ".$model->severity."<br/>- <b>Schema:</b> ".$model->schema."<br/>- <b>Version:</b> ".$model->version0->codelkup."<br/>- <b>DB Type:</b> ".Codelkups::getCodelkup($model->dbms)."<br/>- <b>Assigned To:</b> ".Users::getNameById($model->assigned_to);
           	$message .= "<br/>- <b>Logged by:</b> ".Users::getNameById($model->logged_by);
           
			$message.= "<br/><br/>Best Regards,<br/>SNSit";

	        $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
            
			$emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
            foreach($emails as $email)	{
			 	//Yii::app()->mailer->AddAddress($email);
			}

			if (filter_var(UserPersonalDetails::getEmailById($model->assigned_to), FILTER_VALIDATE_EMAIL)){
	    	 	//Yii::app()->mailer->AddAddress(UserPersonalDetails::getEmailById($model->assigned_to));
	    	}
	    	$ca =  Customers::getCAbyCustomer($model->id_customer);
			$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);			
			if(isset($ca)){
				$email_ca=UserPersonalDetails::getEmailById($ca);
				//Yii::app()->mailer->AddAddress($email_ca);
			}
			if(isset($account_manager)){
				$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
			 	//Yii::app()->mailer->AddCcs($email_account_manager);
				//Yii::app()->mailer->AddAddress($email_account_manager);
			}  
			Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
            Yii::app()->mailer->Subject = 'Critical: '.Customers::getNameById($model->id_customer)." - New RSR#".$model->rsr_no;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
			 Yii::app()->mailer->Send(true);     
    	}	
    }

    public static function sendEmailPending($model, $comment=''){
		$notif = EmailNotifications::getNotificationByUniqueName('new_rsr');
		if ($notif != NULL){
			$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/>A new RSR is pending approval, please find below the RSR information:<br/>";
            $message .= "<br/>- <b>Customer:</b> ".Customers::getNameById($model->id_customer);
            $message .= "<br/>- <b>RSR#:</b> ".'<a href="'.Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$model->id)).'">'.$model->rsr_no.'</a>';
           	$message .= "<br/>- <b>Title:</b> ".$model->short_description;
           	if(trim($comment)!=''){
				$message .= "<br/>- <b>Description:</b> ".$comment;
           	}
           	$message .= "<br/>- <b>Linked SRs#:</b> ".$model->sr;
           	$message .= "<br/>- <b>Severity:</b> ".$model->severity."<br/>- <b>Schema:</b> ".$model->schema."<br/>- <b>Version:</b> ".$model->version0->codelkup."<br/>- <b>DB Type:</b> ".Codelkups::getCodelkup($model->dbms)."<br/>- <b>Assigned To:</b> ".Users::getNameById($model->assigned_to);
           	$message .= "<br/>- <b>Logged by:</b> ".Users::getNameById($model->logged_by);
           $message.= "<br/><br/>Kindly check to confirm the RSR.";
			$message.= "<br/><br/>Best Regards,<br/>SNSit";

	        $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
            
			$emails    = Users::getCSManagers();                
            foreach($emails as $email)	{
			 	//Yii::app()->mailer->AddAddress($email['email']);
			}			
			Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
            Yii::app()->mailer->Subject = 'Critical: '.Customers::getNameById($model->id_customer)." - Pending Approval RSR#".$model->rsr_no;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
			 Yii::app()->mailer->Send(true);     
    	}	
    }

    public static function sendEmailClosed($model){
		$notif = EmailNotifications::getNotificationByUniqueName('rsr_closed');
		if ($notif != NULL){
			$to_replace = array(
                    '{body}'
                );
            
            $message = "Dear All,<br/><br/><b>RSR#".'<a href="'.Yii::app()->createAbsoluteUrl('supportRequest/update', array('id'=>$model->id)).'">'.$model->rsr_no.'</b></a> has been closed, please find below the RSR information:<br/><br/>';
            $message .= "- <b>Customer:</b> ".Customers::getNameById($model->id_customer)."<br/>- <b>Linked SRs#:</b> ".$model->sr;
           	$message .= "<br/>- <b>Severity:</b> ".$model->severity."<br/>- <b>Schema:</b> ".$model->schema."<br/>- <b>Version:</b> ".$model->version0->codelkup."<br/>- <b>DB Type:</b> ".Codelkups::getCodelkup($model->dbms);
           	$message .= "<br/>- <b>Assigned To:</b> ".Users::getNameById($model->assigned_to);
           	$message .= "<br/>- <b>Logged by:</b> ".Users::getNameById($model->logged_by);
           	$message .= "<br/>- <b>Logged on:</b> ".date('d/m/Y', strtotime($model->adddate));
           	$message .= "<br/>- <b>RSR Root:</b> ".supportRequest::getRootLabel($model->root);
           	$message .= "<br/>- <b>RSR description:</b> ".$model->short_description;
			$message.= "<br/><br/>Best Regards,<br/>SNSit";

	        $replace = array(
                $message                    
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
            
			$emails    = EmailNotificationsGroups::getNotificationUsers($notif['id']);                
            foreach($emails as $email)	{
			//	Yii::app()->mailer->AddAddress($email);
			}

			if (filter_var(UserPersonalDetails::getEmailById($model->assigned_to), FILTER_VALIDATE_EMAIL)){
	    			//Yii::app()->mailer->AddAddress(UserPersonalDetails::getEmailById($model->assigned_to));
	    	}
	    	$ca =  Customers::getCAbyCustomer($model->id_customer);
			$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);			
			if(isset($ca)){
				$email_ca=UserPersonalDetails::getEmailById($ca);
				//Yii::app()->mailer->AddCcs($email_ca);
				//Yii::app()->mailer->AddAddress($email_ca);
			}
			if(isset($account_manager)){
				$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
				//Yii::app()->mailer->AddCcs($email_account_manager);
			//	Yii::app()->mailer->AddAddress($email_account_manager);
			}  
			Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
            Yii::app()->mailer->Subject =  Customers::getNameById($model->id_customer)." - Closed RSR#".$model->rsr_no;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);     
    	}	
    }
	
} ?>