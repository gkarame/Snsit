<?php
class EmailNotificationsGroups extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'email_notifications_groups';
	}
	public function rules()	{
		return array(
			array('id_email_notification, id_group', 'required'),
			array('id_email_notification, id_group', 'numerical', 'integerOnly'=>true),
			array('id_email_notification', 'unique', 'criteria'=>array(
					'condition'=>'`id_group`=:secondKey',
					'params'=>array(
							':secondKey'=>$this->id_group
					)
				)),
			array('id, id_email_notification, id_group', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'email_notif' => array(self::BELONGS_TO, 'EmailNotifications', 'id_email_notification'),
			'group' => array(self::BELONGS_TO, 'Groups', 'id_group'),
		);
	}
	public static function isActivated($id_group, $id_email_notification)	{
		$criteria=new CDbCriteria;
		$criteria->compare('id_email_notification',$id_email_notification);
		$criteria->compare('id_group', $id_group);
		$group_notif = self::model()->find($criteria);
		if ($group_notif != NULL)
			return true;
		return false;
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'id_email_notification' => 'Email notification',
			'id_group' => 'Group',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_email_notification',$this->id_email_notification);
		$criteria->compare('id_group',$this->id_group);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getNotificationUsers($id_notif){
		return Yii::app()->db->createCommand()
    						->selectDistinct('user_personal_details.email')
    						->from('email_notifications_groups')
    						->leftJoin('user_groups',  'user_groups.id_group = email_notifications_groups.id_group') 
    						->leftJoin('user_personal_details', 'user_personal_details.id_user = user_groups.id_user')
    						->where('user_groups.id_user in (select id from users where active =1 ) and id_email_notification =:id', array(':id'=>$id_notif))
    						->queryColumn();		
	}
	public static function getPMNotificationUsers($id_pm){
		return Yii::app()->db->createCommand("SELECT upd.email as email FROM user_personal_details upd
		where upd.id_user=".$id_pm." ")
    						->queryScalar();	
	}
	public static function getBMNotificationUsers($id_pm,$id_project){
		return Yii::app()->db->createCommand("SELECT distinct upd.email as email FROM user_personal_details upd, projects p
		where p.project_manager=".$id_pm." and p.business_manager=upd.id_user and p.id=".$id_project."")
    						->queryAll();	
	}
	public static function getLMNotificationUsers($id_pm){
		return Yii::app()->db->createCommand("select upd.email as email from user_personal_details upd where upd.id_user=(select line_manager from user_personal_details where id_user=".$id_pm." )")->queryScalar();
	}
	public static function getDirectorsEmails(){
		return Yii::app()->db->createCommand("select upd.email as email from user_personal_details upd where upd.id_user IN (select id_user from user_groups where id_group=14 and id_user IN (select id from users where active =1))")->queryAll();
	}	
	public static function getCSManagementEmails(){
		return Yii::app()->db->createCommand("select upd.email as email from user_personal_details upd where upd.id_user IN (select id_user from user_groups where id_group in (34,25) and id_user IN (select id from users where active =1))")->queryAll();
	}
	public static function getGroupDescription($id_notif){
		return Yii::app()->db->createCommand("SELECT groups.description From groups left join  email_notifications_groups on email_notifications_groups.id_group = groups.id where email_notifications_groups.id_email_notification = '{$id_notif}' limit 1")->queryScalar();
	}	
}?>