<?php
class ProjectsEmails extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_emails';
	}
	public function rules(){
		return array(
			array('id_project, id_user', 'required'),
			array('id_project, id_user', 'numerical', 'integerOnly'=>true),
			array('id, id_project, id_user', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'idProject' => array(self::BELONGS_TO, 'Phases', 'id_project'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Id Project',
			'id_user' => 'Id User',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project',$this->id_project);
		$criteria->compare('id_user',$this->id_user);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function isNotificationSent($id_project, $id_user){
		return Yii::app()->db->createCommand()
					->select('id')
    				->from('projects_emails')
    				->where('id_project =:id_project AND id_user = :id_user', 
    					array(':id_project'=>$id_project, ':id_user' => $id_user))
    				->queryScalar();
	}
}?>