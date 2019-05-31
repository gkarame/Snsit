<?php
class ChangePasswordForm extends CFormModel{
	public $username;	public $old_password;	public $password;	public $repeat_password;	private $_identity;
	public function rules()	{
		return array(
			array('username, old_password, password, repeat_password', 'required'),
			array('old_password', 'authenticate'),
			array('repeat_password', 'compare', 'compareAttribute'=>'password', 'message'=>'The new password and confirm password don\'t match!'),
		);
	}
	public function attributeLabels(){
		return array(
			'username' => Yii::t('translations', 'Username'),
			'old_password' => Yii::t('translations', 'Password'),
			'password' => Yii::t('translations', 'New Password'),
			'repeat_password' => Yii::t('translations', 'Confirm New Password'),
		);
	}
	public function authenticate($attribute,$params){
		if(!$this->hasErrors()){
			$this->_identity=new UserIdentity($this->username,$this->old_password);
			if(!$this->_identity->authenticate())
				$this->addError('old_password', 'Incorrect username or password.');
		}
	}
	public function changePassword(){
		$user = Users::model()->findByAttributes(array('username'=>$this->username, 'password'=>sha1($this->old_password)));
		if ($user != null) {
			$user->password = sha1($this->password);
			if ($user->save()) {
				return true;
			}
			return false;
		}else{
			$customer = CustomersContacts::model()->findByAttributes(array('username'=>$this->username,'password'=>$this->old_password, 'access' => 'Yes'));
   			if ($customer != null)
   			{
	   			$customer->password = $this->password;
				if ($customer->save()) 
				{
					return true;
				}
				return false;
   			}
		}			
	}
} ?>