<?php
class EasNotes extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'eas_notes';
	}
	public function rules()	{
		return array(
			array('id_ea, id_note', 'required'),
			array('id_ea, id_note', 'numerical', 'integerOnly'=>true),
			array('id, id_ea, id_note', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'note' => array(self::BELONGS_TO, 'Codelkups', 'id_note'),
			'ea' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'id_ea' => 'Id Ea',
			'id_note' => 'Id Note',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_ea',$this->id_ea);	$criteria->compare('id_note',$this->id_note);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}?>