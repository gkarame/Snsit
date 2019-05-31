<?php
class StatusReportHighlights extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'status_report_highlights';
	}
	public function rules(){
		return array(
			array('description', 'required'),
			array('description','length','max'=>255),
			array('id,status_report','safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eReport' => array(self::BELONGS_TO, 'ProjectsStatusReports', 'status_report')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'status_report' => 'Status Report',
			'description' => 'Highlight',
		);
	}
	public static function getHighlightsProvider($id_project){
		$criteria= new CDbCriteria;	$criteria->join = "JOIN projects_status_reports prs on t.status_report = prs.id where prs.project =".$id_project;
		return new CActiveDataProvider('StatusReportHighlights', array(
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