<?php
class UserTask extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'user_task';
	}
	public function rules(){
		return array(
			array('id_user, id_task', 'required'),
			array('id_user, id_task', 'numerical', 'integerOnly'=>true),
			array('id, id_user, id_task', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'id_user'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_user' => 'Id User',
			'id_task' => 'Id Task',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_user',$this->id_user);	$criteria->compare('id_task',$this->id_task);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getUsers($id_project){
		$id_project = (int) $id_project;
		return 	Yii::app()->db->createCommand("SELECT DISTINCT u.id_user FROM user_task u 
			LEFT JOIN projects_tasks ON u.id_task=projects_tasks.id 
			LEFT JOIN projects_phases ON projects_tasks.id_project_phase= projects_phases.id 
			WHERE id_project ={$id_project}")->queryColumn();		
	}
} ?>