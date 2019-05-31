<?php
class SupportRequestComments extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'rsr_comments';
	}
	public function rules(){
		return array(
			array('id_rsr, date', 'required'),
			array('id_rsr, id_user', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>4000),
			array('files,', 'length', 'max'=>128),
			array('id, id_rsr, id_user comment, date, id_user, files', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_rsr' => 'RSR#',
			'comment' => 'Comment',
			'date' => 'Date',
			'id_user' => 'User',
			'filename' => 'Filename',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id', $this->id);	$criteria->compare('id_rsr', $this->id_rsr);
		$criteria->compare('comment', $this->comment,true);
		$criteria->compare('date', $this->date,true);	$criteria->compare('id_user', $this->id_user);	$criteria->compare('filename', $this->filename,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getUsername($id_user){
		$username = Users::getUsername($id_user);	if (empty($username)) {$username = "SNS"; }
		return $username;
	}
} ?>