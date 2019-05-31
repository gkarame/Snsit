<?php
class ProjectsPhases extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_phases';
	}
	public function rules(){
		return array(
			array('id_project, description', 'required'),
			array('id_project, phase_number', 'numerical', 'integerOnly'=>true),
			array('man_days_budgeted, offset,offset_sign,total_estimated','safe'),
			array('id, id_project, id_phase', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'phase' => array(self::BELONGS_TO, 'Phases', 'id_phase'),
			'task' => array(self::HAS_MANY, 'ProjectsTasks', 'id_project_phase'),
			'project' => array(self::BELONGS_TO, 'Projects', 'id_project')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Project',
			'id_phase' => 'Phase',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project',$this->id_project);	$criteria->compare('id_phase',$this->id_phase);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public function addCustomError($attribute, $error) {   $this->customErrors[] = array($attribute, $error);   }
    public static function getCurrent($id_project){
		$str= Yii::app()->db->createCommand("select description as description from milestones where id in (SELECT id_milestone FROM projects_milestones where id_project=".$id_project." and status='In Progress')")->queryAll();		
		if (empty($str)){
			$str= Yii::app()->db->createCommand("select description as description from milestones where id in (SELECT id_milestone FROM projects_milestones where id_project=".$id_project." and status='Pending')  LIMIT 1")->queryAll();		
			if (empty($str)){ $str="All Phases are Closed";	}			
		}	
		return $str;
	}
	public static function getPhaseByProjectId($id_project, $id_phase){
		return Yii::app()->db->createCommand('SELECT id from projects_phases where id_project='.(int)$id_project.' and id_phase='.(int)$id_phase)->queryScalar();		
	}
	public static function getPhaseDescByPhaseId($id_phase){
		return Yii::app()->db->createCommand('SELECT description from projects_phases where id='.(int)$id_phase)->queryScalar();		
	}
	public static function getPhaseByIdTask($id_task){
		return Yii::app()->db->createCommand('SELECT projects_phases.description FROM projects_phases left join projects_tasks on projects_phases.id = projects_tasks.id_project_phase where projects_tasks.id='.$id_task)->queryScalar();
	}
	public static function getPhaseIdByIdTask($id_task){
		return Yii::app()->db->createCommand('SELECT projects_phases.id FROM projects_phases left join projects_tasks on projects_phases.id = projects_tasks.id_project_phase where projects_tasks.id='.$id_task)->queryScalar();
	}
	public static function getOffsetsByPhase($id_phase){
		return Yii::app()->db->createCommand("SELECT sum(CONCAT(case when offset_sign='Plus' then '+' else '-' END ,`offset`)) FROM projects_phases_offsets  where id_project_phase=".$id_phase)->queryScalar();
	}
	public static function getdevPhaseMDs($proj){
		return Yii::app()->db->createCommand("SELECT SUM(man_days_budgeted) FROM `projects_phases` pp where pp.id_project= ".$proj." and pp.description like 'Development'")->queryScalar();
	}
	public static function getdevPhasetasksCountCustom($proj){
		return Yii::app()->db->createCommand("SELECT count(*) FROM `projects_tasks` where id_project_phase= (SELECT id FROM `projects_phases` pp where pp.id_project= ".$proj." and pp.description like 'Development')
and (description like '%FBR%' or description like '%INT%') and description not in ('FBRXXX', 'INTXXX','Integrated Testing')")->queryScalar();
	}
	public static function getIncludingOffsetMDByPhase($id_phase){
		$sum = (float) Yii::app()->db->createCommand("SELECT sum(projects_phases.total_estimated) from projects_phases where projects_phases.id=".$id_phase." ")->queryScalar();
		return $sum;
	}
	public static function getPhase($id_phase, $type){
		return Yii::app()->db->createCommand("SELECT PHASE from phases where phase_number=".(int)$id_phase." and id_category=".$type." ")->queryScalar();		
	}	
	public static function getTimeSpentperPhase($id_phase,$id_user) {
		$result =  Yii::app()->db->createCommand("select sum(amount)/8 from user_time where id_user='".$id_user."' and `default`<>'1' and id_task in (select pt.id from projects_tasks pt  where pt.id_project_phase='".$id_phase."' ) ")->queryScalar();
		return $result;
	}
	public static function getAllPhasesTimesheetReport($p_id=0){
		if ($p_id)		{
			$phases = $str= Yii::app()->db->createCommand("select id, description from projects_phases where id_project=".$p_id."  order by description")->queryAll();	
			foreach ($phases as $key => $phase) {	$p_phases[$phase['id']]=$phase['description'];	}
			return $p_phases;  
		}else{
			$phases = $str= Yii::app()->db->createCommand("select id, description from projects_phases group by id_phase order by description")->queryAll();	
			foreach ($phases as $key => $phase) {		$p_phases[$phase['id']]=$phase['description'];	}
			return $p_phases;  
		}
	}
} ?>