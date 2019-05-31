<?php
class RequestsHr extends CActiveRecord{
	const STATUS_NEW = 0;	const STATUS_COMPLETED = 1;	public $customErrors = array();	public $startData;	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'requests_hr';
	}
	public function rules(){
		return array(
			array('user_id, startDate, type, note', 'required'),
			array('user_id, type, status', 'numerical', 'integerOnly'=>true),
			array('id, user_id, startDate, type, note, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'startDate' => 'Required Date',
			'type' => 'Request type',
			'note' => 'Note',
			'status' => 'Status',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('user_id',$this->user_id);
		$criteria->compare('startDate',$this->startDate);	$criteria->compare('type',$this->type);
		$criteria->compare('note',$this->note,true);	$criteria->compare('status',$this->status);
		return new CActiveDataProvider($this, array(
					'criteria'=>$criteria,
					'pagination'=>array(
							'pageSize' => Utils::getPageSize(),
					),
					
					
			));		
	}
	public static function createRequestHr($type, $startDate, $note){	
		$d = DateTime::createFromFormat('d/m/Y', $startDate);	$ds = $d->format('Y-m-d');	
		$req = new RequestsHr();	$req->type = $type;	$req->startDate = $ds;	$req->note = $note;
		$req->user_id = Yii::app()->user->id;	$req->status = 0;	
		if ($req->save()){
			self::sendNotificationsEmails($req);	return array('id' => $req->id);
		}
		return $req->getErrors();
	}
	public  static function   sendNotificationsEmails($model){
	$case = 1;
		$notif = EmailNotifications::getNotificationByUniqueName('requests_hr_'.strtolower(RequestsHr::getStatusLabel($model->status)));		
		if ($notif != NULL)	{	
				$to_replace = array(
						'{requests_hr_type}', '{requests_hr_fullname}', '{requests_hr_date}',
						'{requests_hr_note}', '{requests_hr_link}'
				);
				$note = '';
				if(!empty($model->note))
					$note = '<br/>Note: '.$model->note;
				$replace = array(
						self::getTypeStatus($model->type),
						Users::getUsername($model->user_id),
						DateTime::createFromFormat('Y-m-d', $model->startDate)->format('d/m/Y'),
						$note,
						'<a href="'.Yii::app()->createAbsoluteUrl('requestsHr/update', array('id'=>$model->id,'status'=>'1')).'">Completed</a>',
							);
				
				$subject = str_replace($to_replace, $replace, $notif['name']);
				$body = str_replace($to_replace, $replace, $notif['message']);	
				Yii::app()->mailer->ClearAddresses();
				if($case == 1){
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					$user = UserPersonalDetails::getEmailById($model->user_id);
					//Yii::app()->mailer->AddCcs($user);
					Yii::app()->mailer->AddAddress($user);
					$line_manager = UserPersonalDetails::getLineManagerEmailById($model->user_id);
					//Yii::app()->mailer->AddCcs($line_manager);
					Yii::app()->mailer->AddAddress($line_manager);
					foreach($emails as $email){
						if (!empty($email))	{
							Yii::app()->mailer->AddAddress($email);
						}
					}					
				}
				elseif($case == 2)	{
					$to = UserPersonalDetails::getEmailById($model->user_id);	Yii::app()->mailer->AddAddress($to);
				}
				Yii::app()->mailer->Subject  = $subject;	Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
				if (Yii::app()->mailer->Send(true)){
					EmailNotifications::saveSentNotification($model->id, 'requests_hr', $notif['id']);
				}
			}
		}
		public static function getStatusLabel($value){
			$list = self::getStatusList();	return $list[$value];
		}
		public static function getStatusList($currentStatus = null){
			switch ($currentStatus)	{
				case self::STATUS_COMPLETED:
					$statuses = array(
					self::STATUS_COMPLETED => 'Completed',
					);
					break;
				default:
					$statuses = array(
						self::STATUS_NEW => 'New',
						self::STATUS_COMPLETED => 'Completed',
					);
					break;
			}
			return $statuses;
		}
		public static function getTypeStatus($value){	
			$list = self::requestsType();	return $list[$value];
		}
		public static function requestsType(){	return Codelkups::getCodelkupsDropDown("hr_request");	}
		protected function beforeValidate(){
			$r = parent::beforeValidate();
			foreach ($this->customErrors as $param) {
				$this->addError($param[0], $param[1]);
			}
			return $r;
		}
		public function addCustomError($attribute, $error) {
			$this->customErrors[] = array($attribute, $error);
		}
} ?>