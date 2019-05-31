<?php
class Surveys extends CFormModel{
	public $username;	public $old_password;	public $password;	public $repeat_password;	private $_identity;
	public function rules(){
		return array(
			array('id, id_question,rate, id_project', 'required'),	
			array('comments', 'length', 'max'=>220),
			array('', 'safe', 'on'=>'search'),
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
		if(!$this->hasErrors())	{
			$this->_identity=new UserIdentity($this->username,$this->old_password);
			if(!$this->_identity->authenticate())
				$this->addError('old_password', 'Incorrect username or password.');
		}
	}
	public function changePassword(){
		$user = Users::model()->findByAttributes(array('username'=>$this->username, 'password'=>sha1($this->old_password)));
		if ($user != null) {
			$user->password = sha1($this->password);
			if ($user->save()) 	{	return true; }
			return false;
		}else{
			$customer = CustomersContacts::model()->findByAttributes(array('username'=>$this->username,'password'=>$this->old_password, 'access' => 'Yes'));
   			if ($customer != null){
	   			$customer->password = $this->password;
				if ($customer->save()) {	return true; }
				return false;
   			}
		}			
	}
public static function encrypt_url($string) {
  $key = "MAL_979805"; //key to encrypt and decrypts.
  $result = '';  $test = "";
   for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);    $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)+ord($keychar));     $test[$char]= ord($char)+ord($keychar);
     $result.=$char;
   }
   return urlencode(base64_encode($result));
}
public static function decrypt_url($string) {
    $key = "MAL_979805"; //key to encrypt and decrypts.
    $result = '';   $string = base64_decode(urldecode($string));
   for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);    $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)-ord($keychar));     $result.=$char;
   }
   return $result;
}
public static function getTotalRatebyProject($surv_type, $id_project,$rate){
		$sum = (int) Yii::app()->db->createCommand("select count(1) from surveys_results where surv_type='".$surv_type."' and id_project=".$id_project." and rate=".$rate." ")->queryScalar();
		return $sum;
	}
} ?>