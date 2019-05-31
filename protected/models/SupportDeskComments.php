<?php
class SupportDeskComments extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'support_desk_comments';
	}
	public function rules(){
		return array(
			array('id_support_desk, date', 'required'),
			array('id_support_desk, id_user, is_admin', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>4000),
			array('avoid', 'length', 'max'=>1000),
			array('files,', 'length', 'max'=>128),
			array('id, id_support_desk, id_user comment,avoid, date, id_user, files', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_support_desk' => 'Id Support Desk',
			'comment' => 'Comment',
			'avoid' => 'Avoid Issue',
			'date' => 'Date',
			'id_user' => 'User',
			'filename' => 'Filename',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id', $this->id);	$criteria->compare('id_support_desk', $this->id_support_desk);
		$criteria->compare('comment', $this->comment,true);	$criteria->compare('avoid', $this->avoid,true);
		$criteria->compare('date', $this->date,true);	$criteria->compare('id_user', $this->id_user);	$criteria->compare('filename', $this->filename,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getUsername($id_user, $is_admin){
		if ($is_admin == 1)	{
			$username = Users::getUsername($id_user);	if (empty($username)) {$username = "SNS"; }
		}else{  $username = CustomersContacts::getNameById($id_user);	}
		return $username;
	}
} ?>