<?php
class YearlySales extends CActiveRecord{
	public function tableName(){
		return 'yearly_sales';
	}
	public function rules(){
		return array(
			array('id_codelkup, Amount', 'required'),
			array('id_codelkup', 'numerical', 'integerOnly'=>true),
			array('id, id_codelkup, Amount', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_codelkup' => 'Id Codelkup',
			'Amount' => 'Amount',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_codelkup',$this->id_codelkup);
		$criteria->compare('Amount',$this->Amount,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
} ?>
