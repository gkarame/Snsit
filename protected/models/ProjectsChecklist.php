<?php
class ProjectsChecklist extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_checklist';
	}
	public function rules(){
		return array(
			array('id_project, id_checklist, status', 'required'),
			array('id_project, id_checklist', 'numerical', 'integerOnly'=>true),
			array('status', 'length', 'max'=>11),			
			array('id, id_project, id_checklist, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idChecklist' => array(self::BELONGS_TO, 'Checklist', 'id_checklist'),
			'idProject' => array(self::BELONGS_TO, 'Projects', 'id_project'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Id Project',
			'id_checklist' => 'Id Checklist',
			'status' => 'Status',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project',$this->id_project);
		$criteria->compare('id_checklist',$this->id_checklist);	$criteria->compare('status',$this->status,true);		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getStatusByChecklistNUmber($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT status FROM projects_checklist pc	 LEFT JOIN Checklist c ON c.id = pc.id_checklist WHERE c.checklist_number = {$number} AND pc.id_project = {$project}")->queryScalar();
	}
	public static function getChecklistByNumber($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT status FROM projects_checklist pc	LEFT JOIN Checklist c ON c.id = pc.id_checklist	WHERE c.checklist_number = {$number} AND pc.id_project = {$project}")->queryRow();
	}
	public static function getChecklistNameByNumber($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT descr FROM projects_checklist pc	LEFT JOIN Checklist c  ON c.id = pc.id_milestone WHERE c.checklist_number = {$number} AND pc.id_project = {$project}")->queryRow();
	}
	public static function checkPhaseStatus($project){
		$project = (int) $project;
		return Yii::app()->db
		->createCommand("SELECT id FROM projects_checklist	WHERE id_checklist= 70 and id_project= '$project' and `status` not in ('Completed')")->queryScalar();
	}
	public static function checkPhasesStatus($project){
		$project = (int) $project;
		return Yii::app()->db
		->createCommand("SELECT count(*) FROM projects_checklist WHERE id_project= '$project' and `status` not in ('Completed','N/A')")->queryScalar();
	}

	public static function gettotalitemsperphase($phase, $project){
		$phase = (int) $phase;	$project= (int) $project;
		$total= Yii::app()->db->createCommand("SELECT count(*) FROM `projects_checklist` where id_project='$project' and id_phase='$phase'  and  status<>'N/A' ")->queryScalar();
		return $total;
	}
	public static function getperphaseitems($project, $phase){
		$project = (int) $project;
		$items= Yii::app()->db->createCommand("SELECT count(*) as closed FROM `projects_checklist` where id_project='$project' and  status='Completed' and id_phase='$phase'")->queryScalar();
		return $items;
	}
	public static function  getlastupdate($project){
		return Yii::app()->db->createCommand("SELECT date_format(max(editdate), '%d/%m/%Y') FROM `projects_checklist` where id_project='$project' ")->queryScalar();
	}
	public static function getperphasesperproject($project){
		$project = (int) $project;
		$items= Yii::app()->db->createCommand("SELECT distinct(id_phase), count(*) as items  FROM `projects_checklist` where id_project='$project' and  status<>'N/A' group By id_phase")->queryAll();
		return $items;
	}
}?>