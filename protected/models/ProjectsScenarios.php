<?php
class ProjectsScenarios extends CActiveRecord{
	public $dataprovider;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_scenarios';
	}
	public function rules(){
		return array(
			array('scenario,status', 'required'),
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
			'scenarios' => 'Scenarios'
		);
	}
	public function searchScenarios($id_project){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_project',$id_project);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria
		));
	}
	public static function getScenariosProvider($id_project){
		$criteria= new CDbCriteria;	$criteria->condition = "id_project =".$id_project." and status != 1";
		return new CActiveDataProvider('ProjectsScenarios', array(
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
		$scenarios =   Yii::app()->db->createCommand()
			->select('*')
			->from('projects_scenarios')
			->queryAll();
		return $scenarios;
	}
	public static function getTestScenario($id){
		$scenario = Yii::app()->db->createCommand()
			->select('*')
			->from('projects_scenarios')
			->where('id = :id',array(':id' =>$id))
			->queryAll();
		return $scenario;
	}	
	public static function build_sorter($key, $key2 = null) {
	    return function ($a, $b) use ($key, $key2) {
	    	$result = strnatcmp($a[$key], $b[$key]);
	    	if ($key2 != null && $result == 0)	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
	public static function getScenarios($id_project){
		return  Yii::app()->db->createCommand()
			->select('*')
			->from('projects_scenarios')
			->where('status !=1 and id_project = :id', array(':id'=>$id_project))
			->queryAll();		
		}
}?>