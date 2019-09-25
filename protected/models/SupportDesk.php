<?php
class SupportDesk extends CActiveRecord{
	const STATUS_NEW = 0;	const STATUS_IN_PROGRESS = 1;	const STATUS_PENDING_INFO = 2;	const STATUS_CLOSED = 3;	const STATUS_REOPENED = 4;	const STATUS_CONFIRME_CLOSED = 5;	const ID_PROFILE_PICTURE = 26;	public $statuss, $prio;
	public static function model($className=__CLASS__){		return parent::model($className);	}
	public function tableName(){
		return 'support_desk';
	}
	public function rules(){
		return array(
			array('sd_no, time_offset,id_customer, severity, date, description, short_description,schema,environment, product,issue_incurred_previously,issue,submitter_name', 'required'),
			array('id_customer,time_offset, status, product,reason,category, num_of_followups, num_of_automatic_followups ,fullsupport_validation', 'numerical', 'integerOnly'=>true),
			array('sd_no', 'length', 'max'=>5),
			array('deployment', 'length', 'max'=>3),
			array('description,users_checked,escalation_reason', 'length', 'max'=>1500),
			array('short_description', 'length', 'max'=>128),
			array('submitter_name', 'length', 'max'=>50),
			array('system_down, issue_incurred_previously, issue, responsibility', 'length', 'max'=>3),
			array('schema, environment', 'length', 'max'=>4),
			array('sd_no, id_customer,time_offset,assigned_to, severity, status, due_date, escalate_date,reason, statuss,   product ', 'safe', 'on'=>'search'),	);	}
	public function relations(){
		return array(
			'idCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'assigned_to'),
			'product0' => array(self::BELONGS_TO, 'Codelkups', 'product'),
			'reason0' => array(self::BELONGS_TO, 'Codelkups', 'reason'),
		);	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'sd_no' => 'Sd No',
			'id_customer' => 'Id Customer',
			'severity' => 'Severity',
			'status' => 'Status',
			'date' => 'Date',
			'description' => 'Description',
			'short_description' => 'Short Description',
			'system_down' => 'System Down',
			'product' => 'Product',
			'schema' => 'Schema',
			'environment' => 'Environment',
			'issue_incurred_previously' => 'This button',
			'issue' => 'This button',
			'num_of_followups' => 'Number of Follow-ups',
			'last_followup_date' => 'Last Follow-up Date',
			'time_offset'=>'Time Offset',
			'deployment' =>'Deployment',
			'category' =>'Category',
			'escalate'=> 'Escalated',
			'escalate_date' => 'Escalation Date',	);	
	}
	public static function getCategoryList(){		
		return array(		
			0 => 'Change Request',	
			1 => 'Preventive Request',
			2 => 'Support Request',
			3 => 'Corrective Request',
		); 	
	}
	public static function getCategoryListShort(){		
		return array(	
			0 => 'CR',		
			1 => 'PR',
			2 => 'SR',
			3 => 'COR',
		); 	
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
	public function search($export = null,$circle=null){
		$criteria=new CDbCriteria;
		if (isset($this->due_date) && $this->due_date != null){
			$due_date = DateTime::createFromFormat('d/m/Y', $this->due_date)->format('Y-m-d');
		}else{ 	$due_date = null;	}		
		$criteria->select = array(
				'case t.severity
        			when "Low" then 2
        			when "High" then 0
					when "Medium" then 1
    			end as prio',
				'*'
		);
		$criteria->with = array('idCustomer');
		$criteria->compare('sd_no', $this->sd_no,true);
		$criteria->compare('severity', $this->severity);
		if(isset($this->product)){
			$rep= Users::getIdByNameTrim($this->product);
			if(!empty($rep))
			{
				$criteria->addCondition(' t.id_customer in (select id from customers where cs_representative= '.$rep.')');
			}		
		}
		if(isset($this->responsibility)){
			$ca= Users::getIdByNameTrim($this->responsibility);
			if(!empty($ca))
			{
				$criteria->addCondition(' t.id_customer in (select id from customers where ca= '.$ca.')');
			}		
		}
		if(isset($this->submitter_name)){
			if($this->submitter_name == 'CS')
			{
				$criteria->addCondition(' t.assigned_to in (select id_user from user_groups where id_group= 9 )');
			}else if($this->submitter_name == 'PS'){
				$criteria->addCondition(' t.assigned_to in (select id_user from user_groups where id_group= 13 )');
			} else if($this->submitter_name == 'OPS')
			{
				$criteria->addCondition(' t.assigned_to in (select id_user from user_groups where id_group= 12 )');
			}		
		}
	 //	print_r($criteria);exit;
		//$criteria->compare('product', $this->product);
		//$criteria->compare('due_date', $due_date);	
		if(isset($this->reason)){
			$criteria->compare('reason', $this->reason);			
		}	
		if (isset($this->assigned_to) && $this->assigned_to != null){
			$last = explode("  ", $this->assigned_to);
			$criteria->join='LEFT JOIN users ON users.id=t.assigned_to';
			$criteria->addCondition('users.firstname LIKE :tn');
			$criteria->params[':tn']='%'.substr($this->assigned_to,0,strrpos($this->assigned_to,"  ")).'%';
			$criteria->addCondition('users.lastname LIKE :tr');
			$criteria->params[':tr']='%'.$last[1].'%';
		}
		if (isset(Yii::app()->user->customer_id)){
			$criteria->compare('idCustomer.name',Customers::getNameById(Yii::app()->user->customer_id));
		}else{	$criteria->compare('idCustomer.name',$this->id_customer);	}
		if ((isset($this->status) && $this->status != "" || isset($this->statuss) && $this->statuss != "") && count($this->status) == count(array_filter($this->status, "strlen")))	{
			$statuss=$this->status;	$inv_status="";
        	foreach ($statuss as $value) {
        	$inv_status.="'".rtrim(ltrim($value," ")," ")."',";
        	}
        	$criteria->addCondition("t.status in (".$inv_status." '-1' ) ");
		}else {
			if (!isset($this->sd_no) || $this->sd_no=='' || $this->sd_no==' '){				
				$status = "(".SupportDesk::STATUS_NEW.','.SupportDesk::STATUS_PENDING_INFO.','.SupportDesk::STATUS_IN_PROGRESS.','.SupportDesk::STATUS_CLOSED.','.SupportDesk::STATUS_REOPENED.')';
			}else{
				$status = "(".SupportDesk::STATUS_NEW.','.SupportDesk::STATUS_PENDING_INFO.','.SupportDesk::STATUS_IN_PROGRESS.','.SupportDesk::STATUS_CLOSED.','.SupportDesk::STATUS_REOPENED.','.SupportDesk::STATUS_CONFIRME_CLOSED.')';
			}
			$criteria->addCondition('t.status IN '."$status");	
		}
		$user_id=Yii::app()->user->id;
		$criteria->order = " CASE
        WHEN t.status=0 or t.status=1 or t.status=2 or t.status=4 THEN 1
        WHEN t.status=3 or t.status=5 THEN 2
        END,CAST(SUBSTRING(t.users_checked,LOCATE('58',t.users_checked)+1) AS SIGNED), t.id DESC";
		//print_r($criteria);exit;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => ($export != null || $circle != null) ? 1000:Utils::getPageSize(),
            ),		));	}	
	public static function getStatusListDropDown(){
		return array(
				self::STATUS_NEW => 'New',
				self::STATUS_IN_PROGRESS => 'In Progress',
				self::STATUS_PENDING_INFO => 'Awaiting Customer',
				self::STATUS_CLOSED => 'Solution Proposed',
				self::STATUS_REOPENED => 'Reopened',	);	}
	public static function getStatusList(){		
		return array(
			self::STATUS_NEW => 'New',
			self::STATUS_IN_PROGRESS => 'In Progress',
			self::STATUS_PENDING_INFO => 'Awaiting Customer',
			self::STATUS_CLOSED => 'Solution Proposed',
			self::STATUS_REOPENED => 'Reopened',
			self::STATUS_CONFIRME_CLOSED => 'Closed'			
		); 	}	
	public static function getStatusList2()	{		
		return array(
			self::STATUS_NEW => 'New',
			self::STATUS_IN_PROGRESS => 'In Progress',
			self::STATUS_PENDING_INFO => 'Awaiting Customer'					
		); 	}	
	public static function getStatusLabel($value){
		$list = self::getStatusList();
		return $list[$value];	}	
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
		}	}	
	public static function getTotalStatus($value){
		$result = Yii::app()->db->createCommand("Select count(1) from support_desk where status = '$value' ")->queryScalar();
		return $result;	
	}
	public static function getStatusListCoded(){
		return array(
			self::STATUS_NEW => 'new',
			self::STATUS_IN_PROGRESS => 'in_progress',
			self::STATUS_PENDING_INFO => 'pending_info',
			self::STATUS_CLOSED => 'close',
			self::STATUS_REOPENED => 'reopened',
			self::STATUS_CONFIRME_CLOSED => 'confirm_close'
		); 
	}
	public static function getStatusLabelCoded($value){
		$list = self::getStatusListCoded();
		return $list[$value];
	}
	public static function getStatusIdCoded($value){
		$list = self::getStatusListCoded();
		return array_search($value, $list);
	}	
	public function renderSRNumber(){
		echo '<a class="show_link" href="'.Yii::app()->createUrl("supportDesk/update", array("id" => $this->id)).'">'.$this->sd_no.'</a>';
	}
	public static function getSchemaList(){
		$schema = array();
		for($i = 1; $i < 21 ; $i++){
			$schema['WH'.$i] = 'WH'.$i;
		}
		return $schema;
	}
	public static function getStatusContor($provider){
		$status = array();
		for ($i=0; $i<5; $i++){
			$status[$i] = 0;
		}
		foreach ($provider->getData() as $data)	{
			if ($data->status != 5)	{
				$status[$data->status]++;
			}
		}
		return $status;
	}	
	public static function getdeploymentdd($id_support_desk,$deployment){
	    $rsr = SupportRequest::getRSR($id_support_desk);
	    $editable = false;
	    if (!empty($rsr)){
            $deployment = 'No';
            $editable = true;
        }else{
	        $support_desk = self::model()->findByPk($id_support_desk);
	        if (isset($support_desk->reason)){
                $criteria = new CDbCriteria(array(
                    'with'=>array(
                        'codelist'=>array(
                            'alias'=>'cl',
                            'together'=>true
                        ),
                    ),
                    'select' => 'cl.id, cl.codelkup',
                    'condition' =>'cl.codelist LIKE :value',
                    'params' => array(':value' => '%reason%'),
                ));

                $codelkups = Codelkups::model()->findAll($criteria);

                foreach ($codelkups as $codelkup){
                    if ($codelkup->id == $support_desk->reason && ($support_desk->reason == '409' || $support_desk->reason == '413' || $support_desk->reason == '402' || $support_desk->reason == '405' || $support_desk->reason== '404' || $support_desk->reason == '406')){
                        $deployment = 'Yes';
                        $editable = true;
                        break;
                    }
                }
            }
        }

		$users[''] = '';
		$users['Yes'] = 'Yes';
		$users['No'] = 'No';
		if(GroupPermissions::checkPermissions('supportdesk-list','write')) {
			return CHtml::dropDownlist('deployment', $deployment, $users, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_support_desk.','."7".')',
		    	'style'=>'width:190px;border:none;',
                'disabled'=>$editable === true?true:false,
            ));
	    }else{
	    	return CHtml::dropDownlist('deployment', $deployment, $users, array(
		        'class'     => 'assigned_to',
		    	'disabled'=>true,	
		    	'style'=>'width:190px;border:none;'
		    ));
	    }	}
	public static function getAllUsers($id_support_desk,$id_user){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 order by users.firstname, users.lastname')->queryAll();
		$users = array();
		$users[''] = ' ';
		foreach ($result as $i => $res){
			$users[$res['id']] = $res['firstname'].'  '.$res['lastname'];
		}
		if(GroupPermissions::checkPermissions('supportdesk-list','write')) {
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
	    }	}	
	public static function getSupportDeskUsers($id_support_desk,$id_user){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 and users.id in (select id_user from user_groups where id_group in (9,12,13,19,18, 12,22) and id_user <>20) order by users.firstname, users.lastname')->queryAll();
		$users = array();
		$users[''] = ' ';
		foreach ($result as $i => $res){
			$users[$res['id']] = $res['firstname'].'  '.$res['lastname'];
		}
		if(GroupPermissions::checkPermissions('supportdesk-list','write')) {
			return CHtml::dropDownlist('assigned_to', $id_user, $users, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput('."value".','. $id_support_desk.','."2".')',
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
	public function getFileCreate(){
		$ids = array();
		$files = array();
		$path = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR.'supportdesk'.DIRECTORY_SEPARATOR;
		if (isset(Yii::app()->session['id_files']) && !empty(Yii::app()->session['id_files'])){
			$ids = explode(',', Yii::app()->session['id_files']);
			$count = count($ids) - 1;
		}
		$new_ids = array();
		foreach ($ids as $id){
			if (!empty($id)){
				$new_ids[] = $id;
			}
		}
		$ids_str = implode(',', $new_ids);
		if(!empty($ids_str))
			$files = Yii::app()->db->createCommand( "SELECT filename FROM `support_desk_files` WHERE id IN (".$ids_str.")")->queryColumn();
		$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$dirName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;
		
		$existing = array();
		foreach ($files as $file){
			if (file_exists($dirPath.$file) && is_file($dirPath.$file))	{
				$existing[]['path'] = $path.$file;
			}
			
		}
		return $existing;
	}
	public function getFiles($id_comm = null){
		if ($id_comm == null){
			$path = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'supportdesk'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
			$files = Yii::app()->db->createCommand( "SELECT filename FROM `support_desk_files` WHERE id_support_desk='$this->id'")->queryColumn();
		}else{
			$path = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'supportdesk'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR."comments".DIRECTORY_SEPARATOR;
			$files = Yii::app()->db->createCommand( "SELECT filename FROM `support_desk_comm_files` WHERE id_comm='$id_comm'")->queryColumn();
		}
		$dirPath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$dirName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;
		$existing = array();
		foreach ($files as $file){
			if (file_exists($dirPath.$file) && is_file($dirPath.$file)){
				$existing[]['path'] = $path.$file;
			}else{
				if ($id_comm == null){
					Yii::app()->db->createCommand( "DELETE FROM `support_desk_files` WHERE id_support_desk='$this->id' && filename='$file'")->execute();
					Yii::app()->db->createCommand("UPDATE `support_desk` SET files=files-1 WHERE id='{$this->id}'")->execute();
				}else{
					Yii::app()->db->createCommand( "DELETE FROM `support_desk_comm_files` WHERE id_support_desk='{$this->id}' && id_comm='{$id_comm}' && filename='$file'")->execute();
					Yii::app()->db->createCommand("UPDATE `support_desk_comments` SET files=files-1 WHERE id='{$id_comm}'")->execute();	
				}			}		}		return $existing;	}	
	public static function getCustomerBySr($sr){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN support_desk ON support_desk.id_customer=customers.id and support_desk.id='.$sr.'')->queryScalar();
		return $result;	}
	public static function getCustomerIdBySr($sr){
		$result =  Yii::app()->db->createCommand('SELECT id_customer FROM support_desk where id='.$sr.'')->queryScalar();
		return $result;	}
	public static function getCustomersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT customers.id, customers.name FROM customers INNER JOIN support_desk ON support_desk.id_customer=customers.id')->queryAll();
		$customers = array();
		foreach ($result as $i=>$res)
		{
			$customers[$i]['label'] = $res['name'];
			$customers[$i]['id'] = $res['id'];
		}
		return $customers;	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users WHERE active = 1 order by users.firstname, users.lastname')->queryAll();
		$users = array();
		foreach ($result as $i => $res){
			$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}		return $users;	}	
	public static function getCheckedUsers($sr, $status)	{
		if (Yii::app()->user->isAdmin  && $status !='3' && $status != '5'){
			$result =  Yii::app()->db->createCommand("SELECT count(1)  FROM support_desk WHERE id = ".$sr." and users_checked like '%,".Yii::app()->user->id.",%' ")->queryScalar();
			return $result;		
		}else{	return 1;	}	}	
	public static function validateLastUpdate($id_support_desk, $assigned, $customer){
		$comments = Yii::app()->db->createCommand("SELECT is_admin FROM `support_desk_comments` where id_support_desk= ".$id_support_desk." and status !=5 order by date desc limit 3")->queryAll();
    	$admin='';
    	foreach ($comments as $key => $comment) {
    		$admin= $admin.','.$comment['is_admin'];
    	}
    	if(sizeof($comments)>2 && strpos($admin, '1') == false)	{
			$notif = EmailNotifications::getNotificationByUniqueName('sr_three_followups');
			if ($notif != NULL){
	    		$to_replace = array(
				'{group_description}',
				'{body}',
				);
	    		$emBody='Dear CS Assignee and CA,<br/><br/> Please note that customer <b>'.Customers::getNameById($customer).'</b> has posted 3 or more consecutive updates on <b>SR#<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$id_support_desk)).'">'.$id_support_desk.'</a></b>.<br/ ><br/>Best Regards,<br/>SNSit';
				$subject = $notif['name'].' '.Customers::getNameById($customer).' - SR#'.$id_support_desk.'';
				$replace = array(
						EmailNotificationsGroups::getGroupDescription($notif['id']),
						$emBody,
		
				);
				$body = str_replace($to_replace, $replace, $notif['message']);		
				Yii::app()->mailer->ClearAddresses();
				$assignedEmail=Users::getEmailbyID($assigned);
				if (filter_var($assignedEmail, FILTER_VALIDATE_EMAIL)) {
								Yii::app()->mailer->AddAddress($assignedEmail);
							}
				$emails = EmailNotificationsGroups::getCSManagementEmails();
				foreach($emails as $email){ 
					if (filter_var($email['email'], FILTER_VALIDATE_EMAIL)) {
						
								//Yii::app()->mailer->AddCcs($email['email']);
								Yii::app()->mailer->AddAddress($email['email']);
					}					
				}
				$ca =  Customers::getCAbyCustomer($customer);	
				if(!empty($ca)){		
					$email_ca=UserPersonalDetails::getEmailById($ca);	
					if (filter_var($email_ca, FILTER_VALIDATE_EMAIL)) {					
								Yii::app()->mailer->AddAddress($email_ca);
					}	
				}
				Yii::app()->mailer->Subject  = $subject;
				Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				Yii::app()->mailer->Send(true);
	    	}  	  	}	}	
	public static function sendEmail($id_support_desk, $id_comm){
		if (Yii::app()->user->isAdmin){
			$notif = EmailNotifications::getNotificationByUniqueName('support_desk_users');
		}else{
			$notif = EmailNotifications::getNotificationByUniqueName('support_desk_customers');
		}		
		$model = SupportDesk::model()->findByPk($id_support_desk);
		$model_comm = SupportDeskComments::model()->findByPk($id_comm);
		$items = "";
		if ($notif != NULL) {
    		$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = '{$model->customer_contact_id}'")->queryScalar();
    		$to_replace = array('{no}', '{customer_name}','{subject}');
    		if($model->reason == 407 || $model->reason == 413 )
    		{
    			if($model->reason == 407)
    			{
    				$replace = array($model->sd_no, $model->idCustomer->name.' - WMS Inquiry',$model->short_description); 
    			}else{
    				$replace = array($model->sd_no, $model->idCustomer->name.' - Change Request',$model->short_description);   
    			}
    		}else{
    			$replace = array($model->sd_no, $model->idCustomer->name,$model->short_description);  	
    		} 
    		$subject = str_replace($to_replace, $replace, $notif['name']); 
    		$histories = Yii::app()->db->createCommand("SELECT * FROM support_desk_comments WHERE id_support_desk = '{$model->id}' AND id != '{$model_comm->id}' ORDER BY date DESC")->queryAll();
    		if (!empty($histories))	{
    			foreach ($histories as $history)
    			{
    				$items .= "
    						<b>Date</b>: ".date("d/m/Y H:i:s",strtotime($history['date']))."
							<b>Comment From</b>: ".SupportDeskComments::getUsername($history['id_user'], $history['is_admin'])."
							<b>Comment</b>: {$history['comment']}
							<b>Status</b>: ".SupportDesk::getStatusLabel($history['status'])."
							";
    			}    		}    		
    		if (Yii::app()->user->isAdmin) 		{
	    		$to_replace = array(
					'{customer_name}',
					'{no}', 
					'{date}',
					'{sns}', 
					'{comment}', 
					'{history}', 
	    			'{status}'
				);
				$replace = array(
					substr($name_customer, 0, strpos($name_customer, ' ')),
					'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
					date("d/m/Y H:i:s",strtotime($model_comm->date)),
					Users::getUsername($model_comm->id_user),
					$model_comm->comment,
					$items,
					SupportDesk::getStatusLabel($model_comm->status)
				);		}else{
				$to_replace = array(
					'{user_name}',
					'{customer_name}',
					'{no}', 
					'{date}', 
					'{comment}', 
					'{history}',
					'{status}'
				);
				$replace = array(
					Users::getUsername($model->assigned_to),
					$model->idCustomer->name,
					'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
					date("d/m/Y H:i:s",strtotime($model_comm->date)),
					$model_comm->comment,
					$items,
					SupportDesk::getStatusLabel($model_comm->status)
				);
			}			
			$body = str_replace($to_replace, $replace, $notif['message']);	
    		Yii::app()->mailer->ClearAddresses();
    		Yii::app()->mailer->ClearCcs();
			$id_rep=Yii::app()->db->createCommand("SELECT cs_representative from customers WHERE id ='".$model->id_customer."'")->queryScalar();
			$email_csrep = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$id_rep." ")->queryScalar();
			Yii::app()->mailer->AddAddress($email_csrep);

					$id_assigned= Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where sd_no='".$model->sd_no."' and id_customer='".$model->id_customer."'")->queryScalar();
					if($id_rep != $id_assigned){
						$email_assi = EmailNotificationsGroups::getLMNotificationUsers($id_rep);
							if (filter_var($email_assi, FILTER_VALIDATE_EMAIL)) {
								//Yii::app()->mailer->AddCcs($email_assi);
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
			if (Yii::app()->user->isAdmin)	{
				$email_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id = '".$model->customer_contact_id."'")->queryScalar();
	    		if (filter_var($email_customer, FILTER_VALIDATE_EMAIL)) 
	    		{
					Yii::app()->mailer->AddAddress($email_customer);
	    		}
			}else{
				$email_user = UserPersonalDetails::getEmailById($model->assigned_to);
				if (filter_var($email_user, FILTER_VALIDATE_EMAIL)) 
				{
					Yii::app()->mailer->AddAddress($email_user);
				}
			}
			$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '".$model->id_customer."' AND access = 'Yes'")->queryColumn();
			$assgined=Users::getEmailbyID($model->assigned_to);			
	    			$ca =  Customers::getCAbyCustomer($model->id_customer);
					$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);
			
					if(isset($ca)){
						$email_ca=UserPersonalDetails::getEmailById($ca);
					//	Yii::app()->mailer->AddCcs($email_ca);
						Yii::app()->mailer->AddAddress($email_ca);
					}
					if(isset($account_manager)){
						$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
					//	Yii::app()->mailer->AddCcs($email_account_manager);
						Yii::app()->mailer->AddAddress($email_account_manager);
					}

			array_push($emails_customer, $assgined);
			foreach ($emails_customer as $email_c)
			{
				if (filter_var($email_c, FILTER_VALIDATE_EMAIL))
				{
					//Yii::app()->mailer->AddCcs($email_c);
					Yii::app()->mailer->AddAddress($email_c);
				}
			}			
			$files = $model->getFiles($model_comm->id);
			foreach ($files as $file)
			{
				Yii::app()->mailer->AddFile($file['path']);
			}			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    	try
			{
				Yii::app()->mailer->Send(true);
				$webroot = Yii::getPathOfAlias('webroot');
				$path =  $webroot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.php';
				$f = fopen($path, "a+");
				if (false === $f) {
					    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, $model->sd_no.' SENDING EMAIL...' );
				fclose($f);
			}catch (Exception $e) {
		    	$webroot = Yii::getPathOfAlias('webroot');
				$path =  $webroot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.php';
				$f = fopen($path, "a+");
				if (false === $f) {
					    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, $model->sd_no.' '.$e->getMessage().'..' );
				fclose($f);
			}
    	}
    }
    public static function sendEmailChangeStatus($id_support_desk, $id_comm, $user_name, $status)
    {
		$status_1 = $status;
		$notif = EmailNotifications::getNotificationByUniqueName('support_desk_'.$status);
		$model = SupportDesk::model()->findByPk($id_support_desk);
		$model_comm = SupportDeskComments::model()->findByPk($id_comm);
		$items = "";		
		if ($notif != NULL) {
    		$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = $model->customer_contact_id")->queryScalar();
    		$to_replace = array('{no}', '{customer_name}', '{subject}');
    		if($model->reason == 407 || $model->reason == 413 )
    		{
    			if($model->reason == 407)
    			{
    				$replace = array($model->sd_no, $model->idCustomer->name.' - WMS Inquiry', $model->short_description);
    			}else{
    				$replace = array($model->sd_no, $model->idCustomer->name.' - Change Request', $model->short_description);
    			}
    		}else{
    				$replace = array($model->sd_no, $model->idCustomer->name, $model->short_description);
    		}    	
    		$subject = str_replace($to_replace, $replace, $notif['name']);  			
	    	$histories = Yii::app()->db->createCommand("SELECT * FROM support_desk_comments WHERE id_support_desk = '{$model->id}' AND id != '{$model_comm->id}' ORDER BY date DESC")->queryAll();
    		$deploy= "";
    		if($status == 'close')
    		{
    			 
    			$depn= Deployments::SRDepNum($id_support_desk, $model->id_customer);
    			if($depn=='')
    			{
					$deploy="<b>Deployment information</b>: No deployment(s) were required.";
    			}else{
    				$deploy="<b>Deployment information</b>: Deployment#".$depn." was performed.";	
    			}
    		}
    		if (!empty($histories))	{
    			foreach ($histories as $history){
    				$items .= "
    						<b>Date</b>: ".date("d/m/Y H:i:s",strtotime($history['date']))."
							<b>Comment From</b>: ".SupportDeskComments::getUsername($history['id_user'], $history['is_admin'])."
							<b>Comment</b>: {$history['comment']}
							<b>Status</b>: ".SupportDesk::getStatusLabel($history['status'])."
							";
    			}	}    		
    		$to_replace = array(
				'{customer_name}',
				'{no}', 
				'{date}', 
    			'{user_name}',
				'{history}',
    			'{cs_representative}', 
    			'{comment}' 
			);
			Yii::app()->mailer->ClearAddresses();
			if($status!='close'){
			$replace = array(
				substr($name_customer, 0, strpos($name_customer, ' ')),
				'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
				date("d/m/Y H:i:s",strtotime($model_comm->date)),
				$user_name,
				$items,
				Users::getUsername($model->assigned_to),
				$model_comm->comment != "" ? "<b>Comment:</b> ".$model_comm->comment : ""
			);
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			foreach ($emails as $email) 
				{
					if (filter_var($email, FILTER_VALIDATE_EMAIL))  
					{	//Yii::app()->mailer->AddCcs($email);
				Yii::app()->mailer->AddAddress($email);
						}
				}
			$id_rep=Yii::app()->db->createCommand("SELECT cs_representative from customers WHERE id ='".$model->id_customer."'")->queryScalar();
			$email_csrep = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$id_rep." ")->queryScalar();
			Yii::app()->mailer->AddAddress($email_csrep);
					
			$id_assigned= Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where sd_no='".$model->sd_no."' and id_customer='".$model->id_customer."'")->queryScalar();
			
			if($id_rep != $id_assigned)
			{
				$email_assi =Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$id_assigned." ")->queryScalar();
				if (filter_var($email_assi, FILTER_VALIDATE_EMAIL)) 
				{
					//Yii::app()->mailer->AddCcs($email_assi);
					Yii::app()->mailer->AddAddress($email_assi);
				}
			}	}else{
				if($model_comm->comment != "")
				{
					$str= "<b>Reason</b>: ".Codelkups::getCodelkup($model->reason);
					if(!empty($model_comm->root_cause))
					{
						$str .= "<br/> <b>Root Cause</b>: ".$model_comm->root_cause."";
					}
					$str.= " <br/> <b>Issue Solution</b>: ".$model_comm->comment." <br/> ".$deploy;
				}else{
					$str= "".$deploy;
				}
				$replace = array(
				substr($name_customer, 0, strpos($name_customer, ' ')),
				'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
				date("d/m/Y H:i:s",strtotime($model_comm->date)),
				$user_name,
				$items,
				Users::getUsername($model->assigned_to),
				$str
			);
				$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
				foreach ($emails as $email) 
				{
					if (filter_var($email, FILTER_VALIDATE_EMAIL))  
					{//	Yii::app()->mailer->AddCcs($email);	
				Yii::app()->mailer->AddAddress($email);	
					}
				}
	    		if (filter_var(UserPersonalDetails::getEmailById($model->assigned_to), FILTER_VALIDATE_EMAIL)) 
	    		{
	    		//	Yii::app()->mailer->AddCcs(UserPersonalDetails::getEmailById($model->assigned_to));
					Yii::app()->mailer->AddAddress(UserPersonalDetails::getEmailById($model->assigned_to));
	    		}	}
			$body = str_replace($to_replace, $replace, $notif['message']);
	    			$ca =  Customers::getCAbyCustomer($model->id_customer);
					$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);			
					if(isset($ca)){
						$email_ca=UserPersonalDetails::getEmailById($ca);
					//	Yii::app()->mailer->AddCcs($email_ca);
						Yii::app()->mailer->AddAddress($email_ca);
					}
					if(isset($account_manager)){
						$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
						//Yii::app()->mailer->AddCcs($email_account_manager);
						Yii::app()->mailer->AddAddress($email_account_manager);
					}
			if ($status_1 == 'reopened')
			{
				$email_cs = Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user ='".$model->assigned_to."'")->queryScalar();
	    		if (filter_var($email_cs, FILTER_VALIDATE_EMAIL)) 
				{	Yii::app()->mailer->AddAddress($email_cs);
					}
			}else{
				$email_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id='".$model->customer_contact_id."'")->queryScalar();
	    		if (filter_var($email_customer, FILTER_VALIDATE_EMAIL)) 
				{	Yii::app()->mailer->AddAddress($email_customer);
				}
	    		$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '".$model->id_customer."' AND access = 'Yes'")->queryColumn();
	    		foreach ($emails_customer as $email_c)
	    		{
	    			if (filter_var($email_c, FILTER_VALIDATE_EMAIL))
	    			{
	    				//Yii::app()->mailer->AddCcs($email_c);
						Yii::app()->mailer->AddAddress($email_c);
	    			}
	    		}
			}
			$files = $model->getFiles($model_comm->id);
			foreach ($files as $file){
				Yii::app()->mailer->AddFile($file['path']);
			}
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    	try{
	    		Yii::app()->mailer->Send(true);
		    	$webroot = Yii::getPathOfAlias('webroot');
				$path =  $webroot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.php';
				$f = fopen($path, "a+");
				if (false === $f) {
					    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, $model->sd_no.' SENDING EMAIL...' );
				fclose($f);
			}catch (Exception $e){
				$webroot = Yii::getPathOfAlias('webroot');
				$path =  $webroot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.php';
				$f = fopen($path, "a+");
				if (false === $f) {
					    throw new RuntimeException('Unable to open log file for writing');
				}
				$bytes = fwrite($f, $model->sd_no.' '.$e->getMessage().'..' );
				fclose($f);
			}
		}
	}
	public static function sendEmailAssigned($model, $oldUser){
		$notif = EmailNotifications::getNotificationByUniqueName('support_desk_assigned_to');
		if ($notif != NULL){
    		$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = '".$model->customer_contact_id."'")->queryScalar();    		
    		$customer = Yii::app()->db->createCommand("SELECT name from customers WHERE id = '".$model->id_customer."'")->queryScalar();    		
			if($model->reason == 407 ||  $model->reason == 413 )		
    		{
    			if($model->reason == 407)
    			{
    				 $replace_sub = array(
					 $model->sd_no,
					 $customer.' - WMS Inquiry'
					);
    			}else{
    				 $replace_sub = array(
					 $model->sd_no,
					 $customer.' - Change Request'
					);
    			}
    		}else{
    			$replace_sub = array(
				 $model->sd_no,
				$customer
				);
    		}		
			$to_replace_sub = array(			
				'{no}', 
    			'{customer_name}'
			);
			
    		$subject = str_replace($to_replace_sub,$replace_sub, $notif['name']); 
    		$to_replace = array(
				'{customer_name}',
				'{no}', 
    			'{user_name}',
			);
			$replace = array(
				substr($name_customer, 0, strpos($name_customer, ' ')),
				'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
				Users::getUsername($model->assigned_to),
			);
			$body = str_replace($to_replace, $replace, $notif['message']);
			Yii::app()->mailer->ClearAddresses();
			$reassign_notif = Yii::app()->db->createCommand("SELECT reassign_notification from customers WHERE id = '".$model->id_customer."'")->queryScalar();    	 
    		$email_cs = Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user ='".$model->assigned_to."'")->queryScalar();
    		if (filter_var($email_cs, FILTER_VALIDATE_EMAIL)){
    			Yii::app()->mailer->AddAddress($email_cs);
    		}    			
			$email_workings = Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user in (SELECT id_user FROM `support_desk_comments` where id_support_desk='".$model->sd_no."' and is_admin=1 order by date DESC ) ")->queryColumn();	
			if (!empty($email_workings)){
				foreach ($email_workings as $email_working)	{    			
	    				//Yii::app()->mailer->AddCcs($email_working); 
						Yii::app()->mailer->AddAddress($email_working); 				
	    		}
			}			
			$email_old = Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user =".$oldUser."")->queryScalar();
		    if (filter_var($email_old, FILTER_VALIDATE_EMAIL))   {
		    	//Yii::app()->mailer->AddCcs($email_old);
				Yii::app()->mailer->AddAddress($email_old);
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
				Yii::app()->mailer->AddAddress($email_account_manager);
			}
		$email_bern_moh= Yii::app()->db->createCommand("SELECT email from user_personal_details WHERE id_user in (9, 20) ")->queryColumn();	
			foreach ($email_bern_moh as $email_bern_mo)	{    			
    				//Yii::app()->mailer->AddCcs($email_bern_mo);    	
					//Yii::app()->mailer->AddAddress($email_bern_mo);					
    		} 		
    		Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    	Yii::app()->mailer->Send(true);
    	}	}
	public static function sendEmailNew($model, $type){
		$notif = EmailNotifications::getNotificationByUniqueName('support_desk_'.$type);
		if ($notif != NULL){
    		$to_replace = array('{no}','{customer_name}','{subject}');
    		if($model->reason == 407 || $model->reason == 413 )
    		{
    			if($model->reason == 407)
    			{
    				$replace = array($model->sd_no, $model->idCustomer->name.' - WMS Inquiry', $model->short_description);
    			}else{
    				$replace = array($model->sd_no, $model->idCustomer->name.' - Change Request', $model->short_description);  
    			}
    		}else{
    			$replace = array($model->sd_no, $model->idCustomer->name, $model->short_description); 	
    		}  
    		$subject = str_replace($to_replace, $replace, $notif['name']);  		
    		$to_replace = array(
				'{customer_name}',
    			'{severity}',
				'{no}', 
    			'{cs_representative}',
    			'{cs_representative_email}',
    			'{no_link}',
    			'{schema}',
    			'{customer_contact}',
    			'{description}',
    			'{date}',
    			'{environment}',
			);
			$name_customer = Yii::app()->db->createCommand("SELECT name from customers_contacts WHERE id = '".$model->customer_contact_id."'")->queryScalar();
			$replace = array(
				$type == 'system_down' ? $model->idCustomer->name : substr($name_customer, 0, strpos($name_customer, ' ')),
				$model->severity,
								'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
				Users::getUsername($model->assigned_to),
				UserPersonalDetails::getEmailById($model->assigned_to),
				'<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$model->id)).'">'.$model->sd_no.'</a>',
				$model->schema,
				CustomersContacts::getNameById($model->customer_contact_id),
				$model->description,
				date("d/m/Y H:i:s",strtotime($model->date)),
				$model->environment				
			);
			$body = str_replace($to_replace, $replace, $notif['message']);    		
    		if ($type == "new_sr"){
    			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
	    		Yii::app()->mailer->ClearAddresses();
				foreach ($emails as $email){
					if (filter_var($email, FILTER_VALIDATE_EMAIL))  
					//	Yii::app()->mailer->AddCcs($email);
					{	Yii::app()->mailer->AddAddress($email);	}
				}
				$email_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id = '".$model->customer_contact_id."'")->queryScalar();
	    		if (filter_var($email_customer, FILTER_VALIDATE_EMAIL))	{ 
					Yii::app()->mailer->AddAddress($email_customer);
	    		}
	    		if (filter_var(UserPersonalDetails::getEmailById($model->assigned_to), FILTER_VALIDATE_EMAIL)){
	    			//Yii::app()->mailer->AddCcs(UserPersonalDetails::getEmailById($model->assigned_to));
					Yii::app()->mailer->AddAddress(UserPersonalDetails::getEmailById($model->assigned_to));
	    		}
	    		$emails_customer = Yii::app()->db->createCommand("SELECT email from customers_contacts WHERE id_customer = '".$model->id_customer."' AND access = 'Yes'")->queryColumn();
	    		foreach ($emails_customer as $email_c){
	    			if (filter_var($email_c, FILTER_VALIDATE_EMAIL)){
	    				//Yii::app()->mailer->AddCcs($email_c);
						Yii::app()->mailer->AddAddress($email_c);
	    			}
	    		}
	    			$ca =  Customers::getCAbyCustomer($model->id_customer);
					$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);			
					if(isset($ca)){
						$email_ca=UserPersonalDetails::getEmailById($ca);
					//	Yii::app()->mailer->AddCcs($email_ca);
						Yii::app()->mailer->AddAddress($email_ca);
					}
					if(isset($account_manager)){
						$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
					//	Yii::app()->mailer->AddCcs($email_account_manager);
						Yii::app()->mailer->AddAddress($email_account_manager);
					}    			
    		}else{
    			$id_rep=Yii::app()->db->createCommand("SELECT cs_representative from customers WHERE id ='".$model->id_customer."'")->queryScalar();
					$email_csrep = Yii::app()->db->createCommand("SELECT email FROM `user_personal_details` where id_user=".$id_rep." ")->queryScalar();
						if (filter_var($email_csrep, FILTER_VALIDATE_EMAIL)){
							Yii::app()->mailer->AddAddress($email_csrep);
						}					
					$id_assigned= Yii::app()->db->createCommand("SELECT assigned_to FROM `support_desk` where sd_no='".$model->sd_no."' and id_customer='".$model->id_customer."'")->queryScalar();
					if($id_rep != $id_assigned){
						$email_assi = EmailNotificationsGroups::getLMNotificationUsers($id_rep);
							if (filter_var($email_assi, FILTER_VALIDATE_EMAIL)) {
								//Yii::app()->mailer->AddCcs($email_assi);
								Yii::app()->mailer->AddAddress($email_assi);
							}
					}						
					$email_Simmon="Simon.Kosseifi@sns-emea.com";
						if (filter_var($email_Simmon, FILTER_VALIDATE_EMAIL)) {
						//	Yii::app()->mailer->AddCcs($email_Simmon);
							Yii::app()->mailer->AddAddress($email_Simmon);

						} 
						$email_bernard="Bernard.Khazzaka@sns-emea.com";
						if (filter_var($email_Simmon, FILTER_VALIDATE_EMAIL)) {
						//	Yii::app()->mailer->AddCcs($email_bernard);
							Yii::app()->mailer->AddAddress($email_bernard);

						} 
					$email_moh="Mohammed.Obaidah@sns-emea.com";
						if (filter_var($email_Simmon, FILTER_VALIDATE_EMAIL)) {
							//Yii::app()->mailer->AddCcs($email_moh);
							Yii::app()->mailer->AddAddress($email_moh);

						}
				if (filter_var(UserPersonalDetails::getEmailById($model->assigned_to), FILTER_VALIDATE_EMAIL)) 
					//Yii::app()->mailer->AddCcs(UserPersonalDetails::getEmailById($model->assigned_to));
					{	Yii::app()->mailer->AddAddress(UserPersonalDetails::getEmailById($model->assigned_to));	}

	    		$ca =  Customers::getCAbyCustomer($model->id_customer);
				$account_manager = Customers::getAccountManagerbyCustomer($model->id_customer);			
					if(isset($ca)){
						$email_ca=UserPersonalDetails::getEmailById($ca);
					//	Yii::app()->mailer->AddCcs($email_ca);
						Yii::app()->mailer->AddAddress($email_ca);
					}
					if(isset($account_manager)){
						$email_account_manager=UserPersonalDetails::getEmailById($account_manager);
						//Yii::app()->mailer->AddCcs($email_account_manager);
						Yii::app()->mailer->AddAddress($email_account_manager);
					}   		}			
					$nullfile=null;
					$files = $model->getFiles($nullfile);
					foreach ($files as $file){
						Yii::app()->mailer->AddFile($file['path']);
					}					
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
	    	Yii::app()->mailer->Send(true);
	    	$webroot = Yii::getPathOfAlias('webroot');
			$path =  $webroot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.php';
			$f = fopen($path, "a+");
			if (false === $f) {
				    throw new RuntimeException('Unable to open log file for writing');
			}
			$bytes = fwrite($f, $model->sd_no.'--- NEW SR EMAIL---' );
			fclose($f);
    	}	}
    public static function getCurrentSRsPerform()  {    	$values = array();    	$counts = '';
    	$results = Yii::app()->db->createCommand("SELECT status, COUNT(status) as count FROM support_desk where status <3 group by status")->queryAll();
    	foreach ($results as $res){    		$values[$res['status']] = $res['count'];    	}
    	$statuses = self::getStatusList2(); 
    	foreach ($statuses as $stat => $value){
    		if ($stat != SupportDesk::STATUS_REOPENED){
    			$counts  .= "Total SRs Currently in Status ".SupportDesk::getStatusLabel($stat).": ".(isset($values[$stat]) ? $values[$stat] : 0)." <br>";
    		}    	}    	return $counts;    }	

    public static function getCurrentRSRsPerform()  {    	$values = array();    	$counts = '<br><br>';
    	$results = Yii::app()->db->createCommand("SELECT status, COUNT(status) as count FROM rsr group by status")->queryAll();
    	foreach ($results as $res){    		$values[$res['status']] = $res['count'];    	}
    	$statuses = SupportRequest::getStatusList(); 
    	foreach ($statuses as $stat => $value){
    		
    			$counts  .= "Total RSRs Currently in Status ".SupportRequest::getStatusLabel($stat).": ".(isset($values[$stat]) ? $values[$stat] : 0)." <br>";
    		  	
    	}    	return $counts;    }	
	public static function get_total_rsrs_submitted_last_week(){
    	$total_submitted_last_week = Yii::app()->db->createCommand("SELECT count(1) FROM rsr WHERE adddate >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND adddate < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY")->queryScalar();
    	$result="<br>Total RSRs Submitted During this Week: ".$total_submitted_last_week." - Yearly Average: ".SupportDesk::getYearAverageRSRs()." ";
    	return $result ;
    }	
	public static function get_total_submitted_last_week(){
    	$total_submitted_last_week = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE date >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY")->queryScalar();
    	$result="Total SRs Submitted During this Week: ".$total_submitted_last_week." - Yearly Average: ".SupportDesk::getYearAverage()." ";
    	return $result ;
    }	
	public static function get_total_closed_last_week() {
    	$total_closed_last_week = Yii::app()->db->createCommand("SELECT count(DISTINCT id_support_desk)  FROM support_desk_comments WHERE status='3' and  date >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY and id_support_desk not in (select id_support_desk  FROM support_desk_comments where date  < curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY and STATUS>='3') ")->queryScalar();
	 $result="Total SRs Solution Proposed During this Week: ".$total_closed_last_week." - Yearly Average: ".SupportDesk::getYearAverageClose(3)." ";
    	return $result ;    }	
	public static function get_total_confirmed_closed_last_week()   {
    	$total_confirmed_closed_last_week = Yii::app()->db->createCommand("SELECT count(DISTINCT id_support_desk)  FROM support_desk_comments WHERE status='5' and  date >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY and id_support_desk not in (select id_support_desk  FROM support_desk_comments where date  < curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY and STATUS>='5')")->queryScalar();
   	 $result="Total SRs Closed During this Week: ".$total_confirmed_closed_last_week." - Yearly Average: ".SupportDesk::getYearAverageClose(5)." ";
    	return $result ;    }
	public static function get_total_rsrs_confirmed_closed_last_week()   {
    	$total_confirmed_closed_last_week = Yii::app()->db->createCommand("SELECT count(1)  FROM rsr WHERE status='6' and  closedate >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND closedate < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY ")->queryScalar();
   	 $result="<br>Total RSRs Closed During this Week: ".$total_confirmed_closed_last_week." - Yearly Average: ".SupportDesk::getYearRSRsAverageClose(6)." ";
    	return $result ;    }
    public static function customerFollowupsNumber($sr){
		$sns=Yii::app()->db->createCommand("SELECT count(1) FROM support_desk_comments where is_admin=1 and id_support_desk=".$sr." ")->queryScalar();
		if($sns ==0){
			return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk_comments where is_admin=0 and id_support_desk=".$sr." ")->queryScalar();
		}else{
			return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk_comments where is_admin=0 and id_support_desk=".$sr." and date>(SELECT date from support_desk_comments where id_support_desk=".$sr." and is_admin=1 order by date DESC limit 1 )")->queryScalar();
		}	}
	 public static function getClosedPerReason($customer, $reason)
	 {
	 	if($reason =='Inquiry')
	 	{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk s where  s.id_customer=".$customer." and s.reason= 407 and s.status in (3,5 ) and  0<(select count(1) from support_desk_comments  sd where s.id= sd.id_support_desk and sd.status=3 and MONTH(DATE)= MONTH(CURRENT_DATE())  and YEAR(DATE)= YEAR(CURRENT_DATE()) order by date desc limit 1)")->queryScalar();

	 	}else if ($reason == 'CR'){
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk s where  s.id_customer=".$customer." and s.reason= 413 and s.status in (3,5 ) and  0<(select count(1) from support_desk_comments  sd where s.id= sd.id_support_desk and sd.status=3 and MONTH(DATE)= MONTH(CURRENT_DATE())  and YEAR(DATE)= YEAR(CURRENT_DATE()) order by date desc limit 1)")->queryScalar();
	 	}else{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk s where  s.id_customer=".$customer." and s.reason not in (407,413) and s.status in (3,5 ) and 0<(select count(1) from support_desk_comments  sd where s.id= sd.id_support_desk and sd.status=3 and MONTH(DATE)= MONTH(CURRENT_DATE())  and YEAR(DATE)= YEAR(CURRENT_DATE()) order by date desc limit 1)")->queryScalar();
	 	}
	 }
	 public static function getOpenPerReasonLess($customer, $reason)
	 {
	 	if($reason =='Inquiry')
	 	{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and reason= 407 and status  in (0,1,2,4 ) and date >= (CURRENT_DATE()- INTERVAL 1 MONTH) ")->queryScalar();

	 	}else if ($reason == 'CR'){
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where  id_customer=".$customer." and reason= 413 and status  in (0,1,2,4  ) and date >= (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();
	 	}else{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and  (reason not in (407,413) or reason is null) and status in (0,1,2,4 ) and date >= (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();
	 	}
	 }
	 public static function getOpenPerReasonMore($customer, $reason)
	 {
	 	if($reason =='Inquiry')
	 	{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and reason= 407 and status  in (0,1,2,4 ) and date < (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();

	 	}else if ($reason == 'CR'){
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where  id_customer=".$customer." and reason= 413 and status  in (0,1,2,4  ) and date < (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();
	 	}else{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and  (reason not in (407,413) or reason is null) and status in (0,1,2,4 ) and date < (CURRENT_DATE()- INTERVAL 1 MONTH)")->queryScalar();
	 	}
	 }
	 public static function getPendingPerReason($customer, $reason)
	 {
	 	if($reason =='Inquiry')
	 	{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and reason= 407 and status  in (0,1,2,4 )")->queryScalar();

	 	}else if ($reason == 'CR'){
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where  id_customer=".$customer." and reason= 413 and status  in (0,1,2,4  )")->queryScalar();
	 	}else{
	 		return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and  (reason not in (407,413) or reason is null) and status in (0,1,2,4 )")->queryScalar();
	 	}
	 }
	 public static function getEscalations($customer)
	 {
	 	return Yii::app()->db->createCommand("SELECT count(1) FROM support_desk where id_customer=".$customer." and escalate='Yes'")->queryScalar();

	 	
	 }
	 public static function getAvgSat($customer)
	 {
	 	$rates= Yii::app()->db->createCommand("SELECT sum(rate) as rate, count(1) as tot FROM `support_desk` where rate <>'' and rate is not null  and id_customer=".$customer." ")->queryRow();
	 	if($rates['tot']!= 0)
	 	{
	 		return Utils::formatNumber($rates['rate']/$rates['tot'],2);
	 	}else{
	 		return 'N/A';
	 	}

	 }
	 public static function getallCAAccounts()
	 {
	 	return Yii::app()->db->createCommand("SELECT DISTINCT(id), name, account_manager, ca FROM `customers` where  status=1 and ca is not null and ca!='' and id in (select id_customer from support_desk) order by name")->queryAll();

	 }
	public static function  getAccountsPerUser($id)
	{
		return Yii::app()->db->createCommand("SELECT DISTINCT(id), name FROM `customers` where  status=1 and ( cs_representative=".$id." or ca=".$id.") order by name")->queryAll();
	}
	public static function getSRsPerUser($user){
		
		return Yii::app()->db->createCommand("SELECT DISTINCT(sd_no), id_customer, short_description,date,  
case severity
        			when 'Low' then 1
        			when 'High' then 3
					when 'Medium' then 2 end severity, 0 as followps, status FROM `support_desk` where  assigned_to=".$user." and status not in (3,5) order by severity DESC, date DESC")->queryAll();
	}	
	public static function getSRsPerUserPerAgeD($user, $days){
		
		return Yii::app()->db->createCommand("SELECT DISTINCT(sd_no), id_customer, short_description,date,  
case severity
        			when 'Low' then 1
        			when 'High' then 3
					when 'Medium' then 2 end severity, 0 as followps,status FROM `support_desk` where  assigned_to=".$user." and status not in (3,5)  and  DATEDIFF(NOW(),date) > 90 order by severity DESC, date DESC")->queryAll();
	}	
	public static function getSRsPerUserClose($user){
		
		return Yii::app()->db->createCommand("SELECT DISTINCT(s.sd_no), s.id_customer, short_description,date FROM support_desk s where  s.assigned_to=".$user." and s.status in (3,2) and 0= (SELECT count(1)  FROM support_desk_comments sd where sd.date>= (NOW() - INTERVAL 2 DAY) and sd.id_support_desk=s.id) order by s.id_customer")->queryAll();
	}
	public static function getYearAverageClose($status){		
		$result = Yii::app()->db->createCommand("SELECT round( count(distinct id_support_desk) / (select datediff( curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY , DATE_FORMAT(NOW() + INTERVAL 10 HOUR ,'%Y-01-01 00:00:00') ) ) * 7 ) FROM support_desk_comments
	WHERE status='".$status."' and  date >= DATE_FORMAT(NOW() ,'%Y-01-01 00:00:00')
	AND date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY ")->queryScalar();
		return $result;
	}
	public static function getYearRSRsAverageClose($status){		
		$result = Yii::app()->db->createCommand("SELECT round( count(distinct id) / (select datediff( curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY , DATE_FORMAT(NOW() + INTERVAL 10 HOUR ,'%Y-01-01 00:00:00') ) ) * 7 ) FROM rsr
	WHERE status='".$status."' and  closedate >= DATE_FORMAT(NOW() ,'%Y-01-01 00:00:00')
	AND closedate < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY ")->queryScalar();
		return $result;
	}
	public static function getYearAverageRSRs(){		
		$result = Yii::app()->db->createCommand("SELECT round( count(distinct id) / (select datediff( curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY , DATE_FORMAT(NOW() + INTERVAL 10 HOUR ,'%Y-01-01 00:00:00') ) ) * 7 ) FROM rsr
		WHERE adddate >= DATE_FORMAT(NOW() ,'%Y-01-01 00:00:00')
		AND adddate < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY ")->queryScalar();
		return $result;
	}
	public static function getYearAverage(){		
		$result = Yii::app()->db->createCommand("SELECT round( count(distinct id) / (select datediff( curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY , DATE_FORMAT(NOW() + INTERVAL 10 HOUR ,'%Y-01-01 00:00:00') ) ) * 7 ) FROM support_desk
		WHERE date >= DATE_FORMAT(NOW() ,'%Y-01-01 00:00:00')
		AND date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY ")->queryScalar();
		return $result;
	}	
	public static function getException($start_datetime, $finish_datetime){
		$results_system_down = Yii::app()->db->createCommand("SELECT count(id) FROM support_desk WHERE date 
			BETWEEN '$start_datetime' AND '$finish_datetime' AND system_down = 'Yes' ")->queryScalar();
		$results_reopen = Yii::app()->db->createCommand("SELECT count(id) FROM support_desk WHERE date 
			BETWEEN '$start_datetime' AND '$finish_datetime' AND status = 'reopened' ")->queryScalar();
		return "1 - System Down: $results_system_down 
				2 - Reopened: $results_reopen";
	}	
	public static function getTopCustomers(){
		$results  = Yii::app()->db->createCommand("SELECT sd.id_customer, c.name, count(sd.id) as total from support_desk sd 
			LEFT JOIN customers c ON sd.id_customer = c.id WHERE  sd.date >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND sd.date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY
			group by sd.id_customer order by total desc limit 5")->queryAll();
		$list_customer = "";		
		foreach ($results as $key => $result){
			$list_customer .= ($key + 1) .' - '.Customers::getNameById($result['id_customer']).': '.$result['total'].' <br>';
		}
		return $list_customer;
	}	
	public static function getTopPerformers($start_date, $finish_date){
		$results  = Yii::app()->db->createCommand("SELECT u.id, u.firstname as fname, u.lastname as lname, count(DISTINCT sd.id_support_desk) as total FROM support_desk_comments sd, users u WHERE sd.id_user=u.id and sd.status='3' and  sd.date >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND sd.date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY and sd.id_support_desk not in (select id_support_desk  FROM support_desk_comments sdc where sdc.date  < curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY and sdc.STATUS>='3' and sdc.id_user=sd.id_user) and sd.id_user in (select id_user from user_groups where id_group= 9) group by sd.id_user order by total desc")->queryAll();
		$list_users = '';
		foreach ($results as $key => $result){	
			$support_tasks = (float)Yii::app()->db->createCommand("SELECT SUM(amount)as support_tasks FROM `user_time` where `default`='1' and  id_task in (select id from default_tasks where id_parent in ('27', '1324')) and id_user='".$result['id']."' and amount>0 and date >= (curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY) AND date < (curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY)")->queryScalar();
			$projects_tasks = (float)Yii::app()->db->createCommand("SELECT SUM(amount)as projects_tasks FROM `user_time` where `default`='0'  and id_user='".$result['id']."' and amount>0 and date >= (curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY) AND date < (curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY)")->queryScalar();
			$esa_tasks = (float)Yii::app()->db->createCommand("SELECT SUM(amount)as esa_tasks FROM `user_time` where `default`='2'  and id_user='".$result['id']."' and amount>0 and date >= (curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY) AND date < (curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY)")->queryScalar();
			$other_tasks = (float)Yii::app()->db->createCommand("SELECT SUM(amount)as other_tasks FROM `user_time` where  (`default`='1' and  id_task in (select id from default_tasks  where id_parent not in ('27', '1324') ))  and id_user='".$result['id']."' and amount>0 and date >= (curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY) AND date < (curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY)")->queryScalar();
			$all_tasks = (float)Yii::app()->db->createCommand("SELECT SUM(amount) as all_tasks FROM `user_time` where id_user='".$result['id']."' and amount>0 and date >= (curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY) AND date < (curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY)")->queryScalar();
			if($all_tasks==0){$all_tasks=1;}
			$percentage_support= Utils::formatNumber(($support_tasks*100) / $all_tasks);
				$percentage_project= Utils::formatNumber(($projects_tasks*100) / $all_tasks);
					$percentage_esa= Utils::formatNumber(($esa_tasks*100) / $all_tasks);
					$percentage_other= Utils::formatNumber(($other_tasks*100) / $all_tasks);
			$list_users .= $key +1 .' - '.$result['fname'].' '.$result['lname'].': '.$result['total'].' - CS: '.$percentage_support.'%  - PS: '.$percentage_project.'% - ESA: '.$percentage_esa.'% - Other: '.$percentage_other.'% <br>';
		}
		return $list_users;
	}
	public static function getTopWeeklyPerformers($start_date, $finish_date){
		$results  = Yii::app()->db->createCommand("SELECT sd.id_user as id, count(DISTINCT sd.id_support_desk) as total FROM support_desk_comments sd, users u WHERE sd.id_user=u.id and sd.status='3' and  sd.date >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY AND sd.date < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY 
and sd.id_support_desk not in (select id_support_desk  FROM support_desk_comments sdc where sdc.date  < curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY and sdc.STATUS>='3' and sdc.id_user <> sd.id_user)
 group by sd.id_user order by total desc LIMIT 1")->queryScalar();				
	$nr = Yii::app()->db->createCommand("update user_personal_details set top_weekly_performer=top_weekly_performer+1 where id_user='".$results."'")->execute();
	$list_users = "Top CS Weekly Performer During this Year: <br> " ;
	$results_top = Yii::app()->db->createCommand("select id_user ,top_weekly_performer from user_personal_details upd where top_weekly_performer>0 order by top_weekly_performer desc limit 3")->queryAll();
			foreach ($results_top as $key => $top)
		{
		$list_users .= Users::getUsername($top['id_user']).": ".$top['top_weekly_performer']."<br>";		
		}
		return $list_users;
	}	
	public static function getMaintenanceSupport($start_date, $finish_date){
		$srs  = Yii::app()->db->createCommand("SELECT sd.id, sd.id_customer, c.name, c.support_weekend, c.week_end, sd.`date`, sd.sd_no 
			FROM support_desk sd 
			LEFT JOIN customers c ON c.id=sd.id_customer 
			WHERE `date` BETWEEN '$start_date' AND '$finish_date' ORDER BY c.name, sd.sd_no")->queryAll();
		$list = "";		
		foreach ($srs as $sr){
			$weekend_restriction = self::isInWeekend($sr['date'], $sr['week_end']);
			$hours_restriction = !self::isInOrar($sr['date'], $start_date, $finish_date);
			if ($hours_restriction)	{
				$list .= $sr['name'].' - SR #<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$sr['id'])).'">'.$sr['sd_no'].'</a><br/>';
			}	else {
				if (($sr['support_weekend'] == 'No' && $weekend_restriction))
				{
					$list .= $sr['name'].' - SR #<a href="'.Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$sr['id'])).'">'.$sr['sd_no'].'</a><br/>';
				}	}		}		
		return $list;  }	
	public static function isInWeekend($date, $weekend){
        $day_of_week = strtolower(date("l", strtotime($date)));
        $weekend = explode("/", $weekend);
        if (count($weekend) == 2)
        { 
	        $first_day = strtolower(trim($weekend[0]));
			$last_day = strtolower(trim($weekend[1]));
	        if (($day_of_week == $first_day )|| ($day_of_week == $last_day))
	        {
	            return true;
	        }  
        }
        return false;
	}
	public static function isCustomer(){
		return (Yii::app()->user->isAdmin != true)?true:false;
	}	
	public static function isInOrar($date)
	{
		$start_hour = Yii::app()->params['start_hour'];
		$end_hour = Yii::app()->params['end_hour'];		
		$start_date = new DateTime('today '.$start_hour);
		$end_date = new DateTime('today '.$end_hour);
		$date = new DateTime($date);		
		if ($start_date <= $date && $date < $end_date){
			return true;
		}
		return false;			
	}	
	public static function getDirPathComm($customer,$model_id,$comm = null){
		$model_id = (int)$model_id;
		if($comm != null)
		{		$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportdesk/{$model_id}/comments/";
		}else{
			$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportdesk/{$model_id}/";
	 	}
		if (!is_dir( $path ) ) 
	 	{
            mkdir( $path, 0777, true);
            chmod( $path, 0777 );
        }
		return $path; 
	}	
	public static function getDirPath($customer,$id_sr = null)
	{
		if($id_sr != null)
		{
			$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportdesk/{$id_sr}/";
		}else{
			$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/{$customer}/supportdesk/";
		}
		if (!is_dir( $path ) ) 
	 	{
            mkdir( $path, 0777, true);
            chmod( $path, 0777 );
        }
		return $path; 
	}	
	public static function getDescriptionGrid($description)
	{
		$arr = Utils::getShortText($description, 60);
		if ($arr['shortened'])
		{
			return '<div class="shortened"><span>'.$arr['text'].'</span><u class="red" onclick="showFull(this);return">+</u></div>'
					.'<div class="full hidden"><span>'.$description.'</span><u class="red" onclick="showFull(this);return">+</u></div>';	
		}
		else 
		{
			return $description;
		}
	}
	public function getComments()
	{
		return SupportDesk::getAllComments($this->id);	
	}	
	public static function getAllComments($id)
	{
		return Yii::app()->db->createCommand("SELECT * FROM support_desk_comments WHERE id_support_desk = '".(int)$id."' ORDER BY id DESC")->queryAll();
	}	
	public static function getPicture($id_user){
		return Yii::app()->db->createCommand("Select documents.id,documents.file FROM documents LEFT JOIN users ON users.id = documents.uploaded_by WHERE documents.id_category = '12' AND documents.id_model = $id_user ORDER BY uploaded DESC LIMIT 1")->queryRow();
	}
	public static function getSystemDown()
	{
		$new = SupportDesk::STATUS_NEW;
		$results = Yii::app()->db->createCommand("SELECT id,id_customer,date FROM support_desk WHERE system_down ='Yes' AND status = $new ORDER BY date DESC")->queryAll();
		return $results;
	}
	public static function getLastCommentMessage($id) 
	{
		$row = Yii::app()->db->createCommand("SELECT comment, status FROM support_desk_comments WHERE id_support_desk = '".(int)$id."' ORDER BY id DESC LIMIT 1")->queryRow();
		if ($row['comment'] == '') 
			return self::getStatusLabel($row['status']);
		else
			return $row['comment'];
	}public static function getNewIssues($days)
	{
		$new = SupportDesk::STATUS_NEW;
		$inprogress= SupportDesk::STATUS_IN_PROGRESS;
		$reopened = SupportDesk::STATUS_REOPENED;
		$results = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE status =0 or status=1 or status=4 ORDER BY date DESC")->queryAll();
		if ($days==0){
		return $results;
		} else {		
							if($days=='0.5'){	$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4)
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 1 DAY 
						AND date< NOW()+ INTERVAL 10 HOUR ) ORDER BY date DESC")->queryAll();
							}else if($days=='2'){
							$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4)
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 3 DAY 
						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 1 DAY  ) ORDER BY date DESC")->queryAll();
							}else if($days=='4'){
								$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4)
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 5 DAY 
						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 3 DAY  ) ORDER BY date DESC")->queryAll();
							}else if($days=='9'){
							$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4 )
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 10 DAY 
						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 5 DAY  )   ORDER BY date DESC")->queryAll();
						
							}else if($days=='11'){
							$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE  (status = 0 
						or status=1 or status=4)

						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 10 DAY   ORDER BY date DESC")->queryAll(); 
							}		return $result;		}  }
	public static function getNewIssues2($days){
		$user = Yii::app()->user->id;
		$new = SupportDesk::STATUS_NEW;
		$inprogress= SupportDesk::STATUS_IN_PROGRESS;
		$reopened = SupportDesk::STATUS_REOPENED;
		$results = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status =0 or status=1 or status=4) and assigned_to=".$user." ORDER BY date DESC")->queryAll();
		if ($days==0){
		return $results;
		} else {		
							if($days=='0.5'){	$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4) and assigned_to=".$user."
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 1 DAY 
						AND date< NOW()+ INTERVAL 10 HOUR ) ORDER BY date DESC")->queryAll();
							}else if($days=='2'){
							$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4) and assigned_to=".$user."
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 3 DAY 
						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 1 DAY  ) ORDER BY date DESC")->queryAll();
							}else if($days=='4'){
								$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4) and assigned_to=".$user."
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 5 DAY 
						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 3 DAY  ) ORDER BY date DESC")->queryAll();
							}else if($days=='9'){
							$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE (status = 0 
						or status=1 or status=4 ) and assigned_to=".$user."
						and (date > NOW()+ INTERVAL 10 HOUR - INTERVAL 10 DAY 
						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 5 DAY  )   ORDER BY date DESC")->queryAll();
						
							}else if($days=='11'){
							$result = Yii::app()->db->createCommand("SELECT id,id_customer,assigned_to,date, status,description FROM support_desk WHERE  (status = 0 
						or status=1 or status=4) and assigned_to=".$user."

						AND date < NOW()+ INTERVAL 10 HOUR - INTERVAL 10 DAY   ORDER BY date DESC")->queryAll(); 
							} 	return $result;	} 	}
	public static function getSupportWeekend($id_customer){	
		$results = Yii::app()->db->createCommand("SELECT support_weekend FROM customers WHERE id=".$id_customer."")->queryScalar();
		return $results;
	}	
	public static function timezone_offset_string( $offset ){
        return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
	}
	public static function Reassign($user, $id ){
        $x=Yii::app()->db->createCommand("UPDATE `support_desk` SET assigned_to=".$user." where id=".$id." ")->execute();
        return $x;
    }
	public static function getAfterHoursTickets(){	
		$list_users='<table style="font-family:Calibri" ><tr  style="text-align:center; vertical-align:bottom; font-weight:bold"> <td> </td><td width="5%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px;padding-right:10px" >SR#</td><td width="14%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;" >Customer</td><td width="8%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;" >Support Plan</td><td width="10%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Resource</td> <td width="5%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;" >Severity</td>  <td width="25%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Short Description</td>  <td width="9%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;" >System Down</td>		 <td width="12%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Logged Date</td>		 <td width="12%" style=" height: 20px; vertical-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;padding-left:10px;padding-right:10px;border-right:1px solid #567885;" >Closed Date</td>   <td> </td> </tr>';
		$results = Yii::app()->db->createCommand("select distinct(sd.sd_no) , sd.id_customer , sdc.id_user , sd.severity , sd.short_description, sd.system_down , sd.date as submitteddate , sdc.date as closeddate ,c.support_weekend
			from support_desk sd  , support_desk_comments sdc , customers c
			where 
			sd.id_customer=c.id
			and sd.id=sdc.id_support_desk 
			and substring(sd.date,12,20) <>'00:00:00' 
			and sd.date >= DATE_FORMAT(CURRENT_DATE() - INTERVAL 1 MONTH, '%Y/%m/01')
			and sd.date < DATE_FORMAT(CURRENT_DATE(), '%Y/%m/01')
			and (sd.status='3' or sd.status='5') 
			and (substring(sd.date,12,20)<'08:00:00' or substring(sd.date,12,20)>'18:00:00' or  DAYOFWEEK(sd.date)=7)  and sdc.`status`='3' and sdc.sender='SNS' 
			and (substring(sdc.date,12,20)<'08:00:00' or substring(sdc.date,12,20)>'18:00:00'  or  DAYOFWEEK(sdc.date)=7) and  sd.severity='High'
			and sdc.id_user in (SELECT id_user FROM user_groups where id_group=9)
			group by sd.sd_no
			order by sd.date, sdc.date desc
			")->queryAll();
		$users= array() ;
		foreach ($results as $key => $top)
		{
		$list_users .= '<tr> <td> </td> <td  style="border-left:1px solid #567885;text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['sd_no'].'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Customers::getNameById($top['id_customer']).'</td>  <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Maintenance::getMaint($top['id_customer']).'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Users::getNameById($top['id_user']).'</td> <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['severity'].'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['short_description'].'</td> <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['system_down'].'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Utils::formatDate(substr($top['submitteddate'],0,10)).'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Utils::formatDate(substr($top['closeddate'],0,10)).'</td>  <td> </td></tr>';
		array_push($users, $top['id_user']);
		}
		$list_users .='<tr> <td> </td> <td colspan="9" style="text-align:left;border-top:1.5px solid #B20533;padding-left:10px;padding-right:10px"> </td> <td> </td></tr></table>';
		$count= array_count_values($users);
		$list_users_count='<br/> <table style="font-family:Calibri"><tr style="font-weight:bold"> <td style=" height:10px;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px;padding-right:10px;" >Resource</td> 	<td style=" height:10px;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;"># of tickets</td> <td style=" height:10px;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Payable</td></tr>';		
		foreach ($count as $k=> $co)
		{ 
			$list_users_count .= '<tr><td style="border-left:1px solid #567885;text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.Users::getNameById($k).'</td> 	<td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$co.'</td> 	 <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$co*'50'.'</td> </tr>';
			$tot=$co*50;
			Yii::app()->db->createCommand("insert into suppliers_print (support_user,id_supplier, amount, description, `check`,date,id_user, direct, export_flag, status) values (".$k.",126,".$tot.",'".Users::getNameById($k)." - ".$co." Issues', null,LAST_DAY(CURRENT_DATE()),1,0,0,1)")->execute();
		}
		$allSupport=  Yii::app()->db->createCommand("select from_date,primary_contact, secondary_contact, type from fullsupport where from_date >= (CURRENT_DATE() - INTERVAL 1 MONTH) and from_date< CURRENT_DATE()")->queryAll();
		foreach ($allSupport as   $value) {
			if($value['type'] == '403') //sunday support 150
			{
				if(!empty($value['primary_contact']))
				{
					$done=Yii::app()->db->createCommand("select count(1) from suppliers_print WHERE support_user = ".$value['primary_contact']." and id_supplier= 126 and date =LAST_DAY(CURRENT_DATE()) ")->queryScalar();
					if($done>0)
					{
						Yii::app()->db->createCommand("UPDATE suppliers_print SET amount = amount+ 150, description= CONCAT(description, ', ', 'Sunday Support')   WHERE support_user = ".$value['primary_contact']." and id_supplier= 126 and date =LAST_DAY(CURRENT_DATE()) ")->execute();
					}else{
						Yii::app()->db->createCommand("INSERT into suppliers_print (support_user,id_supplier, amount, description, `check`,date,id_user, direct, export_flag, status) values (".$value['primary_contact'].",126,150,'".Users::getNameById($value['primary_contact'])." - Sunday Support', null,LAST_DAY(CURRENT_DATE()),1,0,0,1)")->execute();
					}
				}
			}else{
				if(!empty($value['primary_contact']))
				{
					$done=Yii::app()->db->createCommand("select count(1) from suppliers_print WHERE support_user = ".$value['primary_contact']." and id_supplier= 126 and date =LAST_DAY(CURRENT_DATE()) ")->queryScalar();
					if($done>0)
					{
						Yii::app()->db->createCommand("UPDATE suppliers_print SET amount = amount+ 25, description= CONCAT(description, ', ', 'Primary Support')   WHERE support_user = ".$value['primary_contact']." and id_supplier= 126 and date =LAST_DAY(CURRENT_DATE()) ")->execute();
					}else{
						Yii::app()->db->createCommand("INSERT into suppliers_print (support_user,id_supplier, amount, description, `check`,date,id_user, direct, export_flag, status) values (".$value['primary_contact'].",126,50,'".Users::getNameById($value['primary_contact'])." - Primary Support', null,LAST_DAY(CURRENT_DATE()),1,0,0,1)")->execute();
					}
				}
				if(!empty($value['secondary_contact']))
				{
					$done=Yii::app()->db->createCommand("select count(1) from suppliers_print WHERE support_user = ".$value['secondary_contact']." and id_supplier= 126 and date =LAST_DAY(CURRENT_DATE()) ")->queryScalar();
					if($done>0)
					{
						Yii::app()->db->createCommand("UPDATE suppliers_print SET amount = amount+ 15, description= CONCAT(description, ', ', 'Secondry Support')   WHERE support_user = ".$value['secondary_contact']." and id_supplier= 126 and date =LAST_DAY(CURRENT_DATE()) ")->execute();
					}else{
						Yii::app()->db->createCommand("INSERT into suppliers_print (support_user,id_supplier, amount, description, `check`,date,id_user, direct, export_flag, status) values (".$value['secondary_contact'].",126,25,'".Users::getNameById($value['secondary_contact'])." - Secondary Support', null,LAST_DAY(CURRENT_DATE()),1,0,0,1)")->execute();
					}
				}

			}
		}
		$list_users_count.='<tr><td colspan="3" style="text-align:left;border-top:1.5px solid #B20533;padding-left:10px;padding-right:10px"> </td></tr></table>';
		 $list_users.=  $list_users_count; 
		 return $list_users;
	
	}
	public static function getPotentialsCustomer(){
		$list_users='<br /><table style="font-family:Calibri" ><tr  style="text-align:center; vertical-align:bottom; font-weight:bold"> <td> </td><td width="8%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px;padding-right:10px;"  >Customer</td><td width="8%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;" >SR# </td><td width="5%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;" >Severity</td> <td width="15%" style=" height: 20px; vertical-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;padding-left:10px;padding-right:10px;border-right:1px solid #567885;" >(Last 12 Mo)</td> <td width="15%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Beirut Date </td>	 <td width="12%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Beirut Time </td>	 <td width="15%" style=" height: 20px; vertical-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;padding-left:10px;padding-right:10px;border-right:1px solid #567885;" >Customer Date </td>  <td width="15%" style="height: 20px; border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px;padding-right:10px;">Customer Time </td>  <td> </td> </tr>';
		$results = Yii::app()->db->createCommand("SELECT c.name,c.id , sd.sd_no, sd.severity , DAYNAME(sd.date) as day ,sd.date ,  sd.date - INTERVAL (select time_offset from support_desk s where s.sd_no=sd.sd_no) HOUR + INTERVAL tz.time_offset HOUR  as time 
			From support_desk  sd,customers c , time_zone tz
			WHERE sd.id_customer=c.id 
			and c.time_zone = tz.time_zone 
			and sd.date  > CURRENT_DATE - INTERVAL 30 day  
			and ( DATE_FORMAT( sd.date - INTERVAL (select time_offset from support_desk s where s.sd_no=sd.sd_no) HOUR + INTERVAL tz.time_offset HOUR ,'%H:%i:%s') <'08:00:00'  
					OR DATE_FORMAT( sd.date - INTERVAL (select time_offset from support_desk s where s.sd_no=sd.sd_no) HOUR + INTERVAL tz.time_offset HOUR ,'%H:%i:%s')  >'18:00:00'
					OR (
						((DAYOFWEEK(sd.date)='6' or DAYOFWEEK(sd.date)='7')and c.week_end='Friday/Saturday') 
						OR
						((DAYOFWEEK(sd.date)='7' or DAYOFWEEK(sd.date)='1')and c.week_end='Saturday/Sunday') 
						OR
						((DAYOFWEEK(sd.date)='5' or DAYOFWEEK(sd.date)='6')and c.week_end='Thursday/Friday') 
						)
				)
			and (select COUNT(*) from maintenance where support_service in (502,501) and status='Active' and  (customer=c.id or end_customer=c.id))=0 ")->queryAll();
				$users= array() ;
		foreach ($results as $key => $top){			
			$countissues=Yii::app()->db->createCommand("SELECT count(*) 
			FROM support_desk  sd,customers c , time_zone tz
			WHERE sd.id_customer=c.id 
			and c.time_zone = tz.time_zone 
			and sd.date  > CURRENT_DATE - INTERVAL 12 month  
			and ( DATE_FORMAT( sd.date - INTERVAL (select time_offset from support_desk s where s.sd_no=sd.sd_no) HOUR + INTERVAL tz.time_offset HOUR ,'%H:%i:%s') <'08:00:00'  
			OR DATE_FORMAT( sd.date - INTERVAL (select time_offset from support_desk s where s.sd_no=sd.sd_no) HOUR + INTERVAL tz.time_offset HOUR ,'%H:%i:%s')  >'18:00:00'
			OR (
				((DAYOFWEEK(sd.date)='6' or DAYOFWEEK(sd.date)='7')and c.week_end='Friday/Saturday') 
				OR
				((DAYOFWEEK(sd.date)='7' or DAYOFWEEK(sd.date)='1')and c.week_end='Saturday/Sunday') 
				OR
				((DAYOFWEEK(sd.date)='5' or DAYOFWEEK(sd.date)='6')and c.week_end='Thursday/Friday') 
				)
			)
			and c.support_weekend in ('N/A' , 'Standard')
			and sd.id_customer='".$top['id']."'
			GROUP BY sd.id_customer")->queryScalar();
		$list_users .= '<tr> <td> </td> <td  style="border-left:1px solid #567885;text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['name'].'</td> <td  style="text-align:left;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['sd_no'].'</td><td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['severity'].'</td> <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$countissues.'</td><td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['day']." ".Utils::formatDate(substr($top['date'],0,10)).'</td> <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.substr($top['date'],10).'</td> <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.$top['day']." ".Utils::formatDate(substr($top['time'],0,10)).'</td> <td  style="text-align:center;border-right:1px solid #567885;padding-left:10px;padding-right:10px" >'.substr($top['time'],10).'</td>  <td> </td>  <td> </td> </tr>';
		}
		$list_users .='<tr> <td> </td> <td colspan="8" style="text-align:left;border-top:1.5px solid #B20533;padding-left:10px;padding-right:10px"> </td> <td> </td></tr></table>';
		return $list_users;	
	}
	public static function getWeekendByCustomerId($id_customer){	
		$results = Yii::app()->db->createCommand("SELECT week_end FROM customers WHERE id=".$id_customer."")->queryScalar();
		return $results;
	}
	public static function checkUnratedbyCustomer($id_customer){	
		$msg='';
		$results = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk s WHERE s.id_customer='".$id_customer."' and s.status=3 and (select MAX(date) from support_desk_comments where  s.id=id_support_desk) < DATE_SUB(curdate(), INTERVAL 8 WEEK)")->queryScalar();
		if($results == 0)
		{
			$results = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk s WHERE s.id_customer='".$id_customer."'  and s.status=3 ")->queryScalar();
			if($results > 7 )
			{
				$msg='You have more than 7 pending SRs in status "Solution Proposed". Please close them to be able to submit new SRs. <br/> Thank you.';
			}	
		}else{
			$msg= 'You have pending SRs in status "Solution Proposed" for more than 8 weeks. Please close them to be able to submit new SRs. <br/> Thank you.';
		}
		return $msg;
	}
	public static function getLicenseAuditNeeded(){			
			$customers_needed=Yii::app()->db->createCommand("select  c.id , (c.n_licenses_allowed-c.n_licenses_audited) as balance , c.date_audit, c.n_licenses_allowed as allowed,c.n_licenses_audited as audited from customers c where (c.n_licenses_allowed-c.n_licenses_audited)<=(-3) and c.status=1 and c.id not in (135,84) order by balance asc")->queryAll();
			return $customers_needed;	
	}
	public static function getLicenseAuditNeededUser(){	 		
	 		$id_rep =Yii::app()->user->id; 			
			$customers_needed=Yii::app()->db->createCommand("select  c.id , (c.n_licenses_allowed-c.n_licenses_audited) as balance , c.date_audit, c.n_licenses_allowed as allowed,c.n_licenses_audited as audited  from customers c where (c.n_licenses_allowed-c.n_licenses_audited)<=(-3) and c.status=1 and c.id not in (135,84) and c.cs_representative=".$id_rep."  order by balance asc")->queryAll();
			return $customers_needed;	
	}
	public static function getProductwithActiveMaint($id_customer){
			$products= array();
			$customers_needed=Yii::app()->db->createCommand("select c.id, c.codelkup from maintenance m, codelkups c where m.product=c.id and c.id_codelist='3'and m.`status`='Active' and m.end_customer='".$id_customer."' ")->queryAll();
			foreach ($customers_needed as $value) {
				$products[$value['id']]= $value['codelkup'];
			}
			return $products;	
	}
	public static function checkCACR($sr, $reason)
	{
		$ca= Yii::app()->db->createCommand("SELECT ca from customers where id in (select id_customer from support_desk where id= ".$sr.") ")->queryScalar();
		if($ca != Yii::app()->user->id && $reason == 413)
		{
			return true;
		}		else{
			return false;
		}
	}
	public static function doneLicensing($sr){
		$id_customer = Yii::app()->db->createCommand("SELECT id_customer FROM support_desk where id=".$sr."")->queryScalar();
		$last_date=Yii::app()->db->createCommand("(select IFNULL( c.date_audit ,' ') as lastaudit from customers c , eas e , eas_items ei where c.id=e.id_customer and c.id=".$id_customer." and e.id=ei.id_ea and e.category='25' and e.`status`='2'and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null ) UNION ALL (select IFNULL( c.date_audit ,' ') as lastaudit from customers c where  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
 								and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id )and c.id=".$id_customer." ) ")->queryScalar();
		$wanted=Yii::app()->db->createCommand("(select c.id from customers c , eas e , eas_items ei where c.id=e.id_customer and c.id=".$id_customer." and e.id=ei.id_ea and e.category='25' and e.`status`='2'and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null ) UNION ALL (select c.id from customers c where  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) <>0 and  c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
 								and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id )and c.id=".$id_customer." ) ")->queryScalar();
		$connection_last_date=Yii::app()->db->createCommand("select count(1)  from connections where id_customer='".$id_customer."' ")->queryScalar();// and last_updated > NOW()-INTERVAL 12 week ")->queryScalar();
		$licenseAudited= Yii::app()->db->createCommand("select n_licenses_audited  from customers where id='".$id_customer."'  ")->queryScalar();
		if($wanted!=false){
			$last_date_year = substr($last_date,6,5);
			$last_date_month=substr($last_date,3,3);
			$last_date_day= substr($last_date,0,3);
			$last_date=$last_date_year."-".$last_date_month."-".$last_date_day;
			$date_diff=Yii::app()->db->createCommand("Select DATEDIFF(NOW()+ INTERVAL 10 HOUR,IFNULL('".$last_date."','2013-1-1'))>122 as difference")->queryScalar();
		if($date_diff!='0' || empty($licenseAudited) ||$licenseAudited<1){
			$valid=0;
		}else{
			$valid=1;	}	}
		else{	$valid=1;	}
		if($connection_last_date==0){		$valid=2;	}
		if ($valid != 1){	return false;	}else	{		return true;		}	}  
} ?>