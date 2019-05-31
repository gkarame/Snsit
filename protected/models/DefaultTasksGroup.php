<?php
class DefaultTasksGroup extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'default_tasks_group';
	}
	public function rules()	{
		return array(
			array('id_group, id_default_task', 'required'),
			array('id_group, id_default_task', 'numerical', 'integerOnly'=>true),
			array('id, id_group, id_default_task', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'idDefaultTask' => array(self::BELONGS_TO, 'DefaultTasks', 'id_default_task'),
			'idGroup' => array(self::BELONGS_TO, 'Groups', 'id_group'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'id_group' => 'Id Group',
			'id_default_task' => 'Id Default Task',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_group',$this->id_group);	$criteria->compare('id_default_task',$this->id_default_task);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function isActivated($id_group, $id_default_task)	{
		$criteria=new CDbCriteria;
		$criteria->compare('id_default_task',$id_default_task);
		$criteria->compare('id_group', $id_group);
		$group_defaults = self::model()->find($criteria);
		if ($group_defaults != NULL)
			return true;
		return false;
	}
}?>