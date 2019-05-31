<?php
class CodelistsCategories extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'codelists_categories';
	}
	public function rules(){
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>100),
			array('list_order', 'numerical', 'integerOnly'=>true),
			array('id, name, list_order', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'codelists' => array(self::HAS_MANY, 'Codelists', 'id_category'),
		);
	}
	public function attributeLabels(){
		return array(	'id' => 'ID',	'name' => 'Name',		);
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}?>