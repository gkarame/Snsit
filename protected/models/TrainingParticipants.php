<?php
class TrainingParticipants extends CActiveRecord{
	public $customer_name;	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'training_participants';
	}
	public function rules(){
		return array(
			array('id_training,participant_number', 'required'),
			array('id_training,participant_number,customer', 'numerical', 'integerOnly'=>true),
			array('customer', 'exist', 'attributeName' => 'id', 'className' => 'Customers','allowEmpty'=>true),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers','allowEmpty'=>true),
			array('id,participant_number,id_training,firstname,lastname,email', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eTraining' => array(self::BELONGS_TO, 'TrainingsNewModule', 'id_training'),
			'eCustomer' => array(self::BELONGS_TO, 'Customers', 'customer'),
			
		);
	}
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {      $this->addError($param[0], $param[1]);      }
        return $r;
    }
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'participant_number' => 'Participant #',
			'id_training' => 'Training ID',
			'firstname' => 'First Name',
			'lastname' => 'Last Name',
			'email' => 'Email',
			'title' => 'Title',
			'customer' => 'Customer',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_training',$this->id_training);
		$criteria->compare('participant_number',$this->participant_number);	$criteria->compare('firstname',$this->firstname);
		$criteria->compare('lastname',$this->lastname);	$criteria->compare('email',$this->email);
		$criteria->compare('customer.name', $this->customer_name, true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
} ?>