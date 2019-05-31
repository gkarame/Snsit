<?php
class UserGroups extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'user_groups';
	}
	public function rules(){
		return array(
			array('id_user, id_group', 'required'),
			array('id_user, id_group', 'numerical', 'integerOnly'=>true),
			array('id_user', 'unique', 'criteria'=>array(
					'condition'=>'`id_group`=:secondKey',
					'params'=>array(
							':secondKey'=>$this->id_group
					)
				)),
			array('id, id_user, id_group', 'safe', 'on'=>'search'),
		);
	}
	public static function getGroupUser($id_user = 0){
		if($id_user == 0){	$id_user = Yii::app()->user->id;	}
		return Yii::app()->db->createCommand("SELECT id_group.user_groups  FROM user_groups LEFT JOIN groups ON (id_group.user_groups = id.groups) WHERE id_user.user_groups = $id_user")->queryAll();
	}
	public function relations(){
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'group' => array(self::BELONGS_TO, 'Groups', 'id_group'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_user' => 'Id User',
			'id_group' => 'Id Group',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_group',$this->id_group);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}?>