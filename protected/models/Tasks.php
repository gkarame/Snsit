<?php
class Tasks extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'tasks';
	}
	public function rules(){
		return array(
			array('id_phase, task, billable', 'required'),
			array('id_phase', 'numerical', 'integerOnly'=>true),
			array('billable', 'length', 'max'=>3),
			array('id, id_phase, task, billable', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_phase' => 'Id Phase',
			'task' => 'Task',
			'billable' => 'Billable',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_phase',$this->id_phase);
		$criteria->compare('task',$this->task,true);	$criteria->compare('billable',$this->billable,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getAllByPhaseId($id_phase){
		return Yii::app()->db->createCommand('SELECT id,task,billable from tasks where id_phase='.(int)$id_phase)->queryAll();		
	}
	public static function getAllByPhaseTemplate($id_phase, $template){
		if($template  == 2 && $id_phase == 5) //integration development
		{
			return Yii::app()->db->createCommand('SELECT id,task,billable from tasks where id not in (11, 17,14,15) and id_phase='.(int)$id_phase)->queryAll();		
		}
		else if($template  == 2 && $id_phase == 6) //integration UAT
		{
			return Yii::app()->db->createCommand('SELECT id,task,billable from tasks where id not in (23, 24,14,15) and id_phase='.(int)$id_phase)->queryAll();		
		}
		else if($template  == 2 && $id_phase == 7) //integration golive
		{
			return Yii::app()->db->createCommand('SELECT id,task,billable from tasks where id not in (71,14,15) and id_phase='.(int)$id_phase)->queryAll();		
		}
		else if($template  == 2 && $id_phase == 20) //integration trainings
		{
			return Yii::app()->db->createCommand('SELECT id,task,billable from tasks where id not in (64,66,14,15) and id_phase='.(int)$id_phase)->queryAll();		
		}
		else{
			return Yii::app()->db->createCommand('SELECT id,task,billable from tasks where id not in (14,15) and id_phase='.(int)$id_phase)->queryAll();		
		}		
	}
}?>