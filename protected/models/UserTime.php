<?php
class UserTime extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'user_time';
	}
	public function rules(){
		return array(
			array('id_user, id_task, id_timesheet, amount, comment, date', 'required'),
			array('id_user, id_task, id_timesheet', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('id, id_user, id_task, id_timesheet, amount, comment, date', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'idTimesheet' => array(self::BELONGS_TO, 'Timesheets', 'id_timesheet'),
			'idTask' => array(self::BELONGS_TO, 'ProjectsTasks', 'id_task'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_user' => 'Id User',
			'id_task' => 'Id Task',
			'id_timesheet' => 'Id Timesheet',
			'amount' => 'Amount',
			'comment' => 'Comment',
			'date' => 'Date',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_task',$this->id_task);	$criteria->compare('id_timesheet',$this->id_timesheet);
		$criteria->compare('amount',$this->amount);	$criteria->compare('comment',$this->comment,true);	$criteria->compare('date',$this->date,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getUserCurrentTimesheetTasks($id_timesheet, $id_user){
		return Yii::app()->db->createCommand()
				->select('*')
				->from('user_time')
				->where('id_timesheet=:id_timesheet AND id_user=:id_user AND `default`=:default', array(':id_timesheet' => $id_timesheet, ':id_user' => $id_user, ':default' => 0))
				->queryAll();
	}
	public static function getTaskByIdUT($id){
		$id_task = Yii::app()->db->createCommand("SELECT id_task FROM user_time ut  where ut.id=".$id." ")->queryScalar();	return $id_task;
	}
	public static function checkifDefault($id){
		$id_task = Yii::app()->db->createCommand("SELECT ut.default FROM user_time ut  where ut.id=".$id." ")->queryScalar();
		return $id_task;
	}
} ?>