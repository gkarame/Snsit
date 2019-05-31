<?php
class ProjectsMilestones extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_milestones';
	}
	public function rules(){
		return array(
			array('id_project, id_milestone, status, last_updated', 'required'),
			array('id_project, id_milestone', 'numerical', 'integerOnly'=>true),
			array('status,applicable', 'length', 'max'=>11),
			array('id, id_project, id_milestone, status, estimated_date_of_completion,estimated_date_of_start, last_updated', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idMilestone' => array(self::BELONGS_TO, 'Milestones', 'id_milestone'),
			'idProject' => array(self::BELONGS_TO, 'Projects', 'id_project'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Id Project',
			'id_milestone' => 'Id Milestone',
			'status' => 'Status',
			'estimated_date_of_completion' => 'End Date',
			'estimated_date_of_start' => 'Start Date',
			'last_updated' => 'Last Updated',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project',$this->id_project);
		$criteria->compare('id_milestone',$this->id_milestone);	$criteria->compare('status',$this->status,true);
		$criteria->compare('estimated_date_of_completion',$this->estimated_date_of_completion,true);
		$criteria->compare('estimated_date_of_start',$this->estimated_date_of_start,true);		
		$criteria->compare('last_updated',$this->last_updated,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getMilestones(){		
		return Yii::app()->db->createCommand("SELECT DISTINCT(description) FROM projects_milestones pm	LEFT JOIN milestones m ON m.id = pm.id_milestone where id_category=27")->queryAll();
	}
	public static function checkProjectMilestone($idProject){		
		$m= Yii::app()->db	->createCommand("select MAX(id_milestone) from projects_milestones where id_project=".$idProject." and status in ('In Progress','Closed') ")->queryScalar();
		if ($m>=5){	return true; }else{	return false; }
	}
	public static function getStatusByMilestoneNUmber($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT status FROM projects_milestones pm	 LEFT JOIN milestones m ON m.id = pm.id_milestone WHERE m.milestone_number = {$number} AND pm.id_project = {$project}")->queryScalar();
	}
	public static function CheckApplicable($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT applicable FROM projects_milestones pm	 LEFT JOIN milestones m ON m.id = pm.id_milestone WHERE m.milestone_number = {$number} AND pm.id_project = {$project}")->queryScalar();
	}	
	public static function getMilestoneByNumber($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT status, last_updated FROM projects_milestones pm LEFT JOIN milestones m ON m.id = pm.id_milestone WHERE m.milestone_number = {$number} AND pm.id_project = {$project}")->queryRow();
	}
	public static function getMilestoneNameByNumber($project, $number){
		$project = (int) $project;	$number = (int) $number;
		return Yii::app()->db->createCommand("SELECT description FROM projects_milestones pm LEFT JOIN milestones m ON m.id = pm.id_milestone  WHERE m.milestone_number = {$number} AND pm.id_project = {$project}")->queryRow();
	}
	public static function checkPhaseStatus($project){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT id FROM projects_milestones	WHERE id_milestone= 6 and id_project= '$project' and `status` not in ('Closed') and applicable='Yes' ")	->queryScalar();
	}
	public static function checkPhasesStatus($project){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT count(*) FROM projects_milestones	WHERE id_project= '$project' and `status` not in ('Closed') and applicable='Yes' ")->queryScalar();
	}
	public static function checkMilestonesCount($project){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT count(*) FROM projects_milestones	WHERE id_project= '$project' ")->queryScalar();
	}
	public static function checkPhasesStatusdate($project){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT count(*) FROM projects_milestones	WHERE id_project= '$project' and  applicable='Yes'  and ((`status` not in ('Closed') and estimated_date_of_completion < CURRENT_DATE()) OR (`status` ='Pending' and estimated_date_of_start<CURRENT_DATE()))")->queryScalar();
	}	
	public static function checkPhaseChecklistdate($project){
		$project = (int) $project;
		$open= Yii::app()->db->createCommand("SELECT COUNT(*) from projects_checklist where status='Open' AND id_project ='$project'")->queryScalar();
		$all=Yii::app()->db->createCommand("SELECT COUNT(*) from projects_checklist where  id_project ='$project'")->queryScalar();
		if($all ==0){	return 0;	}
		$number = Utils::formatNumber(($open/$all)*100,2);
		return $number;
	}
}?>