<?php
class Requests extends CActiveRecord{	
	
	const STATUS_NEW = 0;	const STATUS_APPROVED = 1;	const STATUS_REJECTED = 2;	const ITEM_VACATION = 91;
	const ITEM_DAYOFF = 90;	const ITEM_SICK_LEAVE = 92; public $customErrors = array();	public $startData;	public $file;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'requests';
	}
	public function rules(){
		return array(
			array('startDate,endDate,type', 'required'),
			array('user_id, type, status,halfday', 'numerical', 'integerOnly'=>true),
			array('inLieuOf1 ,inLieuOf2' ,'safe'),
			array('description' ,'requiredbytype'),
			array('id, user_id, startDate, endDate, type, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){		
		return array(
				'eUser' => array(self::BELONGS_TO, 'Users', 'user_id'),
				'pUser' => array(self::HAS_ONE, 'UserPersonalDetails', 'id_user'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'startDate' => 'From Date',
			'endDate' => 'To Date',
			'type' => 'Leave Type',
			'status' => 'Status',
			'inLieuOf1' => 'In Lieu Of',
			'inLieuOf2' => 'In Lieu Of',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('t.id',$this->id);	$criteria->compare('user_id',$this->user_id);
		$criteria->compare('startDate','>='.$this->startDate,true);	$criteria->compare('endDate', '<='.$this->endDate,true);
		$criteria->compare('type',$this->type);	$criteria->compare('status',$this->status);
		if(empty(Users::checkIfDirectorOnly(Yii::app()->user->id)))	{
			$criteria->join='LEFT JOIN user_personal_details upd ON upd.id_user=t.user_id';
			$criteria->addCondition('upd.line_manager ='.Yii::app()->user->id.' or upd.id_user='.Yii::app()->user->id);
		}	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
    			'defaultOrder' => 'id DESC',            
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
	public static function createRequest($type, $startDate, $endDate, $inLieuOf1=null, $inLieuOf2=null ,$halfday=null , $description= null, $cc = null){		
		$d = DateTime::createFromFormat('d/m/Y', $startDate);	$ds = $d->format('Y-m-d');		
		$d = DateTime::createFromFormat('d/m/Y', $endDate);		$de = $d->format('Y-m-d');
		if($type=='90'){ if($inLieuOf1 != null) {	$d1 = DateTime::createFromFormat('d/m/Y',$inLieuOf1);	$di1 = $d1->format('Y-m-d');	}
						if($inLieuOf2 != null) {	$d2 = DateTime::createFromFormat('d/m/Y',$inLieuOf2);	$di2 = $d2->format('Y-m-d');	}
		}
		$users = array();		
		if($cc !=null){
			foreach ($cc as $user)
				$users[] = $user;
			$users = array_filter($users);
			$users = implode(',',$users);
		}
		$req = new Requests();	$req->type = $type;	$req->startDate = $ds;	$req->endDate = $de;	$req->user_id = Yii::app()->user->id;	$req->status = 0;
		if($type=='90'){
			if($halfday!=null){		$req->halfday = $halfday;	}
			if($inLieuOf1 != null) {	 $req->inLieuOf1 = $di1; 	}
			if($inLieuOf2 != null) { 	$req->inLieuOf2 = $di2; }				
		}
		if($type=='91' && $halfday!=null){		$req->halfday = $halfday;	}	
		if($description !=null ){	$req->description = $description; 	}	
		if($users != null)
			$req->cc = $users;
		else
			$req->cc = "";
		if ($req->save()){	
			self::sendNotificationsEmails($req);	return array('id' => $req->id);
		}
		return $req->getErrors();
	}
	public static function sendNotificationsEmails($model){
		$case = 2;		
		$notif = EmailNotifications::getNotificationByUniqueName('requests_'.strtolower(Requests::getStatusLabel($model->status)));
		$to = array();	$approver="";
		if ($notif != NULL){			
				$to_replace = array(
						'{request_type}', '{user_fullname}', '{startDate}',
						'{endDate}' , '{approve_link}', '{reject_link}',  '{approver}'
				);
				$d = DateTime::createFromFormat('Y-m-d', $model->startDate);	$ds = $d->format('d/m/Y');				
				$d = DateTime::createFromFormat('Y-m-d', $model->endDate);		$de = $d->format('d/m/Y');
				$curr=Yii::app()->user->id;
				if(!empty($curr)){
					$approver=Users::getUsername($curr);
				}else{
					$usr_request = UserPersonalDetails::getUserDetails($model->user_id);	$approver=Users::getUsername($usr_request['line_manager']);
				}				
				$replace_subject = array(
						self::getTypeStatus($model->type),
						Users::getUsername($model->user_id),
						$ds,
						$de,						
						'<a href="'.Yii::app()->createAbsoluteUrl('requests/update/?id='.$model->id."&status=1").'">Approve</a>',
						'<a href="'.Yii::app()->createAbsoluteUrl('requests/update/?id='.$model->id."&status=2").'">Reject</a>',
						$approver
				);				
				if	($model->type=='91'){					
					if($model->halfday!=null){
						$halfday=$model->halfday;	$de = $de.'<br/><b>(Half Day)</b>' ;
					}					
				}				
				$subject = str_replace($to_replace, $replace_subject, $notif['name']);				
				if	($model->type=='90'){
					$di1='';	$description=$model->description;
							if($model->inLieuOf1 !=null){
								$d1 = DateTime::createFromFormat('Y-m-d', $model->inLieuOf1);	$di1 = $d1->format('d/m/Y');
							}
							if($model->inLieuOf2!=null){
								$d2 = DateTime::createFromFormat('Y-m-d', $model->inLieuOf2);	$di2 = $d2->format('d/m/Y');	
							}
							if($model->halfday!=null){	$halfday=$model->halfday;	}	
					if ($model->inLieuOf2==null){
							if($model->halfday==null){
									$de = $de.'<br/> <b><u>In Lieu Of Date: </u></b> '.$di1;
									if($description!=null){
										$de =$de.'<br/> <b><u>Description: </u></b>'.$description;
									}
							}else{
								$de = $de.'<br/><b><u>Half Day In Lieu Of Date: </u></b>'.$di1;
								if($description!=null){
										$de =$de.'<br/> <b><u>Description: </u></b>'.$description;
									}
							}
					}else{
						$de = $de.'<br/><b><u>Half Day In Lieu Of Date: </u></b>'.$di1.'<br/><b><u>and Half Day In Lieu Of Date: </u></b>'.$di2 ;
						if($description!=null){		$de =$de.'<br/><b><u>Description: </u></b>'.$description;	}
					}				
				}
				$replace = array(
						self::getTypeStatus($model->type),
						Users::getUsername($model->user_id),
						$ds,
						$de,						
						'<a href="'.Yii::app()->createAbsoluteUrl('requests/update/?id='.$model->id."&status=1").'">Approve</a>',
						'<a href="'.Yii::app()->createAbsoluteUrl('requests/update/?id='.$model->id."&status=2").'">Reject</a>',
						$approver
				);
				$body = str_replace($to_replace, $replace, $notif['message']);
				if($case == 1){
					$usr = UserPersonalDetails::getUserDetails($model->user_id);
					if(!empty($usr['line_manager'])){						
						$to[] = UserPersonalDetails::getEmailById($usr['line_manager']);					
					}
					if(!empty($usr['unit']) && $usr['unit']=='117'){ 
					//	Yii::app()->mailer->AddCcs('bernard.khazzaka@sns-emea.com');
						Yii::app()->mailer->AddAddress('bernard.khazzaka@sns-emea.com');						
					}
					if($model->cc != null){
						$explode = explode(',', $model->cc);
						foreach ($explode as $user)
						$to[] = UserPersonalDetails::getEmailById($user);
					}
					/*$getPMsUsers= Projects::getAllPMsProjectsAssignedtoUser($model->user_id);
					if(!empty($getPMsUsers)){
						foreach($getPMsUsers as $getPMsUser){
							$emailPM=UserPersonalDetails::getEmailById($getPMsUser['pm']);
							if (filter_var($emailPM, FILTER_VALIDATE_EMAIL)){	
								Yii::app()->mailer->AddCcs($emailPM);
							}
						}
					}*/					
				}
				elseif($case == 2){
					$to[] = UserPersonalDetails::getEmailById($model->user_id);	$usr = UserPersonalDetails::getUserDetails($model->user_id);
					if(!empty($usr['line_manager'])){						
						$to[] = UserPersonalDetails::getEmailById($usr['line_manager']);					
					}
					if(!empty($usr['unit']) && $usr['unit']=='117'){ 
					//	Yii::app()->mailer->AddCcs('bernard.khazzaka@sns-emea.com');
						Yii::app()->mailer->AddAddress('bernard.khazzaka@sns-emea.com');			
					}
					if($model->cc != null){
							$explode = explode(',', $model->cc);
							foreach ($explode as $user)
							$to[] = UserPersonalDetails::getEmailById($user);
						}
					/*$getPMsUsers= Projects::getAllPMsProjectsAssignedtoUser($model->user_id);
					if(!empty($getPMsUsers)){
						foreach($getPMsUsers as $getPMsUser){
							$emailPM=UserPersonalDetails::getEmailById($getPMsUser['pm']);
							if (filter_var($emailPM, FILTER_VALIDATE_EMAIL))
							{	
								Yii::app()->mailer->AddCcs($emailPM);
							}
						}
					}*/
				}
				if(isset($to)){
					Yii::app()->mailer->ClearAddresses();
					foreach($to as $email){
						Yii::app()->mailer->AddAddress($email);
					}
					Yii::app()->mailer->Subject= $subject;
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					//Yii::app()->mailer->AddCcs($emails);
					Yii::app()->mailer->AddAddress($emails);
					/*$getPMsUsers= Projects::getAllPMsProjectsAssignedtoUser($model->user_id);
					if(!empty($getPMsUsers)){
						foreach($getPMsUsers as $getPMsUser){
							$emailPM=UserPersonalDetails::getEmailById($getPMsUser['pm']);
							if (filter_var($emailPM, FILTER_VALIDATE_EMAIL)){	
								Yii::app()->mailer->AddCcs($emailPM);
							}
						}
					}*/ 
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true)){
						EmailNotifications::saveSentNotification($model->id, 'requests', $notif['id']);
					}
				}
		}
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList();	return $list[$value];
	}
	public static function getStatusList($currentStatus = null){
		switch ($currentStatus){
			case self::STATUS_APPROVED:
				$statuses = array(
				self::STATUS_APPROVED => 'Approved',
				);
				break;
			default:
				$statuses = array(
				self::STATUS_NEW => 'New',
				self::STATUS_APPROVED => 'Approved',
				self::STATUS_REJECTED => 'Rejected',
					
				);
				break;
		}
		return $statuses;
	}
	public static function getAllStatus(){	return array('0'=>'New','1'=>'Approved','2'=>'Rejected');	}
	public static function getTypeStatus($value){	$list = self::requstsType();	return $list[$value]; }
	public static function requstsType(){	return 	Codelkups::getCodelkupsDropDown("requests");	}
	public static function getUserBranch(){
		$id_user= Yii::app()->user->id;
		$branch = Yii::app()->db->createCommand("SELECT c.codelkup FROM `codelkups` c, user_personal_details upd where c.id=upd.branch and upd.id_user=".$id_user." ")->queryScalar();
		return $branch;
	}	
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }
	public function addCustomError($attribute, $error) {    $this->customErrors[] = array($attribute, $error);   }
	public static function max(){	return Yii::app()->db->createCommand('SELECT max(EndDate) as max from requests where status = 1')->queryScalar();	}
	public static function min(){	return Yii::app()->db->createCommand('SELECT min(StartDate) as min from requests where status = 1')->queryScalar();	}
public static function getStatusforApproval($id_request,$status,$user){ 	
$result= array('0'=>'New','1'=>'Approved','2'=>'Rejected');		
		if(GroupPermissions::checkPermissions('supportdesk-list','write') && $user!=Yii::app()->user->id ){
			return CHtml::dropDownlist('status', $status, $result, array(
		        'class'     => 'status',
		    	'onchange'=>'changeInput('."value".','. $id_request.')',
		    	'style'=>'width:190px;border:none;'
		    ));
	    }else{
	    	return CHtml::dropDownlist('status', $status, $result, array(
		        'class'     => 'status',
		    	'disabled'=>true,	
		    	'style'=>'width:190px;border:none;'
		    ));
	    }
	}
	public function requiredbytype(){ 	
 	 if( ($this->type=='533' || $this->type=='534')  && (empty($this->description) || $this->description=='' || $this->description==' ') ){ 	 			
         $this->addError('description','Project field required');
 		 }  
	}
}
?>