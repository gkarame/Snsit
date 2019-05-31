<?php
class EmailNotifications extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'email_notifications';
	}	
	public function rules()	{
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>255),
			array('message', 'safe'),
			array('id, name, message', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'perGroup' => array(self::HAS_MANY, 'EmailNotificationsGroups', 'id_email_notification'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'message' => 'Message',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id', $this->id);	$criteria->compare('name', $this->name,true);	$criteria->compare('message', $this->message,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getNotificationByUniqueName($name){
		return Yii::app()->db->createCommand()
    						->select('id, name, message')
    						->from('email_notifications')
    						->where('unique_name =:name', array(':name'=>$name))
    						->queryRow();
	}
	public static function getUniqueNameByNotificationId($id){
		return Yii::app()->db->createCommand()
    						->select('unique_name')
    						->from('email_notifications')
    						->where('id =:id', array(':id'=>$id))
    						->queryScalar();
	}
	public static function getIdsByModule($module){
		return Yii::app()->db->createCommand()
    						->select('id')
    						->from('email_notifications')
    						->where('module =:module', array(':module'=>$module))
    						->queryAll();
	}
	public static function saveSentNotification($id_model, $table, $id_notification){
		Yii::app()->db->createCommand()
			->insert('email_notifications_sent', array(
			    'id_model'=>$id_model,
			    'table_model'=>$table,
				'id_notification' => $id_notification,
		));
	}
	public static function isNotificationPAlertSent($id_model, $table, $id_notification){
		return Yii::app()->db->createCommand()
					->select('id')
    				->from('email_notifications_sent')
    				->where('id_model =:id AND table_model = :table AND id_notification = :id_notif  AND date(adddate)= CURRENT_DATE()', 
    					array(':id'=>$id_model, ':table' => $table, ':id_notif' => $id_notification))
    				->queryScalar();
	}
	public static function isNotificationSent($id_model, $table, $id_notification){
		return Yii::app()->db->createCommand()
					->select('id')
    				->from('email_notifications_sent')
    				->where('id_model =:id AND table_model = :table AND id_notification = :id_notif', 
    					array(':id'=>$id_model, ':table' => $table, ':id_notif' => $id_notification))
    				->queryScalar();
	}
	public static function saveSentProjectNotification($id_project, $id_user){
		Yii::app()->db->createCommand()
			->insert('projects_emails', array(
			    'id_project'=>$id_project,
				'id_user' => $id_user,
		));
	}
	public static function saveSentTimesheetNotification($id_user, $model, $not){
		Yii::app()->db->createCommand()
			->insert('timesheet_notifications_sent', array(
			    'user'=>$id_user,
				'table_model' => $model,
				'id_notification' => $not,
		));
	}
	public static function isTimesheetNotificationSent($id_user, $table, $not){
		return Yii::app()->db->createCommand()
					->select('id')
    				->from('timesheet_notifications_sent')
    				->where('user =:id AND table_model = :table AND id_notification = :id_notif AND date(adddate)= CURRENT_DATE()', 
    					array(':id'=>$id_user, ':table' => $table, ':id_notif' => $not))
    				->queryScalar();
	}
}?>