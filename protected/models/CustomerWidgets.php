<?php
class CustomerWidgets extends CActiveRecord{
	public function tableName()	{
		return 'customer_widgets';
	}
	public function rules()	{
		return array(
			array('widget_id, user_id, order', 'required'),
			array('widget_id, user_id, order', 'numerical', 'integerOnly'=>true),
			array('id, widget_id, user_id, order', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'user' => array(self::BELONGS_TO, 'Customers_Contacts', 'id'),
			'widgets' => array(self::BELONGS_TO, 'Widgets', 'widget_id'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'widget_id' => 'Widget',
			'user_id' => 'User',
			'order' => 'Order',
		);
	}
	public function getWid($id){
			$criteria=new CDbCriteria;	$criteria->compare('id',$id);		
			return new CActiveDataProvider($this, array(
					'criteria'=>$criteria
			));		
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);	$criteria->compare('widget_id',$this->widget_id);	$criteria->compare('user_id',$this->user_id);
		$criteria->compare('order',$this->order);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function model($className=__CLASS__){	return parent::model($className);}
}?>
