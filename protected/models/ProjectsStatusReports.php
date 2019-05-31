<?php
class ProjectsStatusReports extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_status_reports';
	}
	public function rules(){
		return array(
			array('date', 'required'),
			array('title,report_num','length','max'=>45),
			array('invoicesflag','length','max'=>1),
		);
	}
	public function relations(){
		return array(
			'eProject' => array(self::BELONGS_TO, 'Projects', 'project')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'project' => 'Project',
			'date' => 'Date',
			'title' => 'Title',
			'invoicesflag' => 'Include Financials',
			'incluetimesheet' => 'Include Timesheet',
			'includechecklist' => 'Include Checklist'
		);
	}
	public static function getAll(){
		$status_reports =   Yii::app()->db->createCommand()
			->select('*')
			->from('projects_status_reports')
			->queryAll();
		return $status_reports;
	}	
	public static function getprojectperReport($report){
		$proj =   Yii::app()->db->createCommand("select project from projects_status_reports where id =".$report."")->queryScalar();
		return $proj;
	}
	public static function checksameDayReport($project, $date){
		$reports =   Yii::app()->db->createCommand("select id from projects_status_reports where project =".$project." and date='".$date."'")->queryAll();		
		if (count($reports)>0){
			foreach($reports as $report){
				$delete =   Yii::app()->db->createCommand("delete from status_report_health_indicators where status_report =".$report['id']." and indicators_date='".$date."'")->execute();
			}
		}
		return true;
	}
} ?>