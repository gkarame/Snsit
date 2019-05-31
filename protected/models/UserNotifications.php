<?php
class UserNotifications extends CActiveRecord{
	public function tableName(){
		return 'usernotifications';
	}
	public function rules(){
		return array(
			array('id, name, url', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('name, url', 'length', 'max'=>255),
			array('id, name, url', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public static function getAllUserNotifications(){
		return Yii::app()->db->createCommand('SELECT * FROM usernotifications ORDER BY name')->queryAll();		
	}
	public static function getAllUserNotificationsForPermission(){
		$array = array();
		foreach(self::getAllUserNotifications() as $k=>$v){
			$array['id'.$v['id']] = array('label' =>Yii::t('translation', $v['name']));	
		}
		return $array;
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'url' => 'Url',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);	$criteria->compare('url',$this->url,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}?>
