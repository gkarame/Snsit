<?php
class SystemParameters extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'system_parameters';
	}
	public function rules(){
		return array(
			array('system_parameter, label, value', 'required'),
			array('system_parameter', 'length', 'max'=>50),
			array('label, value', 'length', 'max'=>500),
			array('system_parameter, label, value', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'system_parameter' => 'System Parameter',
			'label' => 'Label',
			'value' => 'Value',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('system_parameter',$this->system_parameter,true);
		$criteria->compare('label',$this->label,true);	$criteria->compare('value',$this->value,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getCost(){
		return Yii::app()->db->createCommand("SELECT value from system_parameters where system_parameter ='man_hour_cost' ")->queryScalar();
	}
}?>