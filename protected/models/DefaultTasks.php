<?php
class DefaultTasks extends CActiveRecord{
	const SUPPORT_TASK = 27;
	const RSR_TASK= 1324;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'default_tasks';
	}
	public function rules()	{
		return array(
			array('name, billable', 'required'),
			array('id_parent', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>250),
			array('billable', 'length', 'max'=>3),
			array('id, name, id_parent, billable', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'idParent' => array(self::BELONGS_TO, 'DefaultTasks', 'id_parent'),
			'defaultTasks' => array(self::HAS_MANY, 'DefaultTasks', 'id_parent'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'id_parent' => 'Id Parent',
			'billable' => 'Billable',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('name',$this->name,true);
		$criteria->compare('id_parent',$this->id_parent);	$criteria->compare('billable',$this->billable,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getTasksGroupedByParent(){
		return Yii::app()->db->createCommand()
				->select('t.id, t.name, t.billable, m.name as parent, m.id as id_parent')
				->from('default_tasks t')
				->join('default_tasks m', 'm.id = t.id_parent')
				->where('t.id_parent !=828 and t.id_parent !='. DefaultTasks::SUPPORT_TASK.' and t.id_parent !='. DefaultTasks::RSR_TASK)
				->order('m.name,t.name')
				->queryAll();
	}	
	public static function getDescription($id_task)	{
		return Yii::app()->db->createCommand('SELECT name from default_tasks where id='.(int)$id_task)->queryScalar();		
	}
}?>