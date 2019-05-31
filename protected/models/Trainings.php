<?php
class Trainings extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'trainings';
	}
	public function rules(){
		return array(
			array('id_customer, name', 'required'),
			array('id_customer, id_eas', 'numerical', 'integerOnly'=>true),
			array('id, id_customer, id_eas, name', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_customer' => 'Id Customer',
			'id_eas' => 'Id Eas',
			'name' => 'Name',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_customer',$this->id_customer);
		$criteria->compare('id_eas',$this->id_eas);	$criteria->compare('name',$this->name,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getEA($id)
	{
		return Yii::app()->db->createCommand('select id_eas from trainings where id='.$id.' LIMIT 1')->queryScalar();
	}
	public static function getName($id)
	{
		return Yii::app()->db->createCommand('select name from trainings where id='.$id.' LIMIT 1')->queryScalar();
	}
	public static function createTraining($description, $customer,$id_eas,$start_date,$end_date){
		$training = new Trainings();
		$des = $description." - From ".date('d/m/Y',strtotime($start_date))." - To ".date('d/m/Y',strtotime($end_date));
		$training->name = $des;		$training->id_customer = $customer;		$training->id_eas = $id_eas;
		if ($training->save()) {	return true;	}
		return $training->getErrors(); 
	}
	public static function createTrainingNew($description, $customer,$id_eas){
		$training = new Trainings();
			$training->name = $description;		$training->id_customer = $customer;		$training->id_eas = $id_eas;
			if ($training->save()) {	return true;	}
			return $training->getErrors(); 
	}
} ?>