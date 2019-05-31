<?php
class StatusReportHealthIndicators extends CActiveRecord{
	CONST INDICATOR_NOTSET = 0;	CONST INDICATOR_ONTRACK = 1;	CONST INDICATOR_MANAGEABLERISKS = 2;	CONST INDICATOR_MAJORPROBLEM = 3;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'status_report_health_indicators';
	}
	public function rules(){
		return array(
			array('project_scope,resources,timeline,project_finance,risks_issues,overall_project_health, indicators_date, format', 'required'),
		);
	}
	public function relations(){
		return array(
			'eReport' => array(self::BELONGS_TO, 'ProjectsStatusReports', 'status_report')
		);
	}
	public static function getHealthIndicatorsList(){
		return array(self::INDICATOR_ONTRACK => "G",
			self::INDICATOR_MANAGEABLERISKS => "Y",
			self::INDICATOR_MAJORPROBLEM => "R",
			);
	}

	public static function getTypeList(){
		return array(1 => "PDF",
			2 => "EXCEL",
			);
	}

	public static function getHealthIndicatorsList2(){
		return array(self::INDICATOR_ONTRACK => "G",
			self::INDICATOR_MANAGEABLERISKS => "Y",
			self::INDICATOR_MAJORPROBLEM => "R",
			self::INDICATOR_NOTSET => " ",
			);
	}
	public static function getIndicatorLabel($ind){
		$list = self::getHealthIndicatorsList2();		return $list[$ind];
	}
	public static function getHealthIndicatorsProvider($id_report){
		$criteria=new CDbCriteria;	$criteria->condition = "(id in (Select id from status_report_health_indicators where status_report =".$id_report."))";
		return new CActiveDataProvider('StatusReportHealthIndicators', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
}
?>