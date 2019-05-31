<?php
class ForgetPasswordForm extends CFormModel{
	public $username;	private $_identity;
	public function rules()	{
		return array(
			array('username', 'required'),
		);
	}
	public function attributeLabels(){
		return array(
			'username' => Yii::t('translations', 'Username'),
		);
	}
	public function forgetPassword(){
		$username="";
		$user = Users::model()->findByAttributes(array('username'=>$this->username));
		if ($user != null){	
			$username=$this->username;
			$id_user = Yii::app()->db->createCommand("SELECT id FROM users WHERE  username ='".$username."' ")->queryScalar();				
			$link = Yii::app()->getBaseUrl(true).'/site/resetPassword?a4tgalnngqhe5gvlaehg='.$id_user.'&cioi2uef9=1';
			$notif = EmailNotifications::getNotificationByUniqueName('forget_password');		
			if ($user->save()){
			if ($notif != NULL){
					$subject = $notif['name'];
					$to_replace = array(
							'{username}' ,
							'{link}'
						);
					$replace = array(
							$username ,
							$link
		
						);
					$body = str_replace($to_replace, $replace, $notif['message']);
					$email = Yii::app()->db->createCommand("SELECT email FROM user_personal_details upd, users u WHERE u.id = upd.id_user and username ='".$username."' ")->queryScalar();
					Yii::app()->mailer->ClearAddresses();
					if (filter_var($email, FILTER_VALIDATE_EMAIL))
							Yii::app()->mailer->AddAddress($email);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);					
				echo "<div style='position:absolute;margin-left:65%;z-index: 1; margin-top:23%;font-family:Calibri; font-weight:bold; color:#8B0000; font-size:18px'> An email has been sent for you <br/>to reset your password.</div>"; 
				return false;
				}
			}		
		}else{
			$customer = CustomersContacts::model()->findByAttributes(array('username'=>$this->username, 'access' => 'Yes'));
			if ($customer != null){	
				$username=$this->username;
				$id_user = Yii::app()->db->createCommand("SELECT id FROM customers_contacts WHERE  username ='".$username."' ")->queryScalar();
				$link = Yii::app()->getBaseUrl(true).'/site/resetPassword?a4tgalnngqhe5gvlaehg='.$id_user.'&cioi2uef9=2';
				$notif = EmailNotifications::getNotificationByUniqueName('forget_password');			
				if($customer->save()){
				if ($notif != NULL){
					$subject = $notif['name'];
					$to_replace = array(
							'{username}' ,
							'{link}'
						);
					$replace = array(
							$username ,
							$link

						);
					$body = str_replace($to_replace, $replace, $notif['message']);
					$email = Yii::app()->db->createCommand("select email FROM customers_contacts where username='".$username."' ")->queryScalar();
					Yii::app()->mailer->ClearAddresses();
					if (filter_var($email, FILTER_VALIDATE_EMAIL))
							Yii::app()->mailer->AddAddress($email);
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					Yii::app()->mailer->Send(true);				
					echo "<div style='position:absolute;margin-left:66%;z-index: 1; margin-top:25%;font-family:Calibri; font-weight:bold; color:#8B0000; font-size:18px'> An email has been sent for you <br/>to reset your password.</div>"; 
					return false;
				}
				}
			}
			}			
	} 
	public function resetPassword(){ 
		$username="";
		$user = Users::model()->findByAttributes(array('username'=>$this->username));
		if ($user != null){
				$user->password = sha1('R@mykh123');
		}else{
			$customer = CustomersContacts::model()->findByAttributes(array('username'=>$this->username, 'access' => 'Yes'));
			if ($customer != null){ $customer->password = 'sns@7678';}
		}
	}
}
?>