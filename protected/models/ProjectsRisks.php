<?php
class ProjectsRisks extends CActiveRecord{
	public $dataprovider;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_risks';
	}
	public function rules(){
		return array(
			array('risk,responsibility,status,privacy,planned_actions', 'required'), //readd id_project 
			array('priority', 'numerical', 'integerOnly'=>true),
			array('id_project','safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'project' => array(self::BELONGS_TO, 'Projects', 'id_project')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Project',
			'risks' => 'Risks'
		);
	}
	public function searchRisks($id_project){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project',$id_project);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria
		));
	}
	public static function getRisksProvider($id_project){
		$criteria= new CDbCriteria;	$criteria->condition = "id_project =".$id_project." and status != 'Closed'";
		return new CActiveDataProvider('ProjectsRisks', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
	public static function getAll(){
		$risks =   Yii::app()->db->createCommand()
			->select('*')
			->from('projects_risks')
			->queryAll();
		return $risks;
	}
	public static function getRisk($id){
		$risk = Yii::app()->db->createCommand()
			->select('*')
			->from('projects_risks')
			->where('id = :id',array(':id' =>$id))
			->queryAll();
		return $risk;
	}
	public static function getAlertsCount($projectId, $risks = false){
		if ($risks === false){
			$alerts =   Yii::app()->db->createCommand()
				->select('alerts')
    			->from('projects_alerts')
    			->where('id_project = :id', array(':id'=>$projectId))
    			->queryScalar();    		
		}
		$alerts_array = explode(',', $alerts);
		return count($alerts_array); 
	}	
	public static function build_sorter($key, $key2 = null) {
	    return function ($a, $b) use ($key, $key2) {
	    	$result = strnatcmp($a[$key], $b[$key]);
	    	if ($key2 != null && $result == 0){
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
	public static function getProjectRisks($projectId){
		$risks =   Yii::app()->db->createCommand()
			->select('risk')
			->from('projects_risks')
			->where('id_project = :id', array(':id'=>$projectId))
			->queryScalar();
		$alertsData = array();
		if (!empty($alerts)){
			$values = array();
			if (strpos($alerts, '#') !== false){
				$alerIds = array();		$a = explode(',', $alerts);
				foreach ($a as $alert){
					if(strpos($alert,'#') !== false){
						$params = explode('#', $alert);		$alerIds[] = reset($params);		$values[$params[0]] = $params[1];
					}else{	$alerIds[] = $alert;}
				}
				$ids = implode(',', $alerIds);
			}else{
				$ids = $alerts; 
			}		
			$alertsData = Yii::app()->db->createCommand("SELECT * from alerts WHERE `id` IN (". $ids .") ORDER BY severity ASC ")->queryAll();
			foreach ($alertsData as &$data){
				if (isset($values[$data['id']])){
					$data['description'] = 	str_replace('{percent}', $values[$data['id']], $data['description']);
					if ($values[$data['id']] > '100'){
						$data['severity'] = 1;
						break;
					}else{
						switch ($values[$data['id']]){
							case '50':
							case '75':
								$data['severity'] = 2;
								break;
							case '90':
							case '100':
								$data['severity'] = 1;
								break;
						}				
				}
			}
		}
		uasort($alertsData, ProjectsAlerts::build_sorter('severity'));
		return $alertsData;
		}
	}
	public static function parseAlerts($id_project, $alerts){
		foreach ($alerts as $al) {
			if ($al['id_project'] == $id_project)
				return $al['alerts'];
		}
		return false;
	}
	public static function getRisks($id_project){
		return  Yii::app()->db->createCommand()
			->select('*')
			->from('projects_risks')
			->where('status <> "Closed" and id_project = :id', array(':id'=>$id_project))
			->queryAll();		
		}
	public static function getRisksStr($id_project){
		$results=  Yii::app()->db->createCommand()
			->select('risk')
			->from('projects_risks')
			->where('status <> "Closed" and id_project = :id', array(':id'=>$id_project))
			->queryAll();	
			if(!empty($results))
			{	$str=implode(', ', array_column($results, 'risk'));	}else{	$str='';	}
			return $str;
		}
}?>