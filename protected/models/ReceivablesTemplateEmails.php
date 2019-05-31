<?php
class ReceivablesTemplateEmails extends CActiveRecord{
	public function tableName(){
		return 'receivables_template_emails';
	}
	public function rules(){
		return array(
			array('id_codelkup, template', 'required'),
			array('id_codelkup', 'numerical', 'integerOnly'=>true),
			array('id, id_codelkup, template', 'safe', 'on'=>'search'),
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
			'template' => 'Template',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_codelkup',$this->id_codelkup);	$criteria->compare('template',$this->template,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
?>