<?php
class WidgetProjectsAlerts extends CWidget 
{
	public $widget;	
	public function getId($autoGenerate=false) {
		$model = __CLASS__;
		return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model'")->queryScalar();
	}	
	public static function getName(){
		$model = __CLASS__;
		return Yii::app()->db->createCommand("SELECT name FROM widgets WHERE model = '$model'")->queryScalar();
	}	
    public function run()
    {
    	$this->render('/widgets/projectAlerts', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	$values = array(); $sr = array();
    	$values = Yii::app()->db->createCommand("select DISTINCT id_project from projects_alerts order by id_project")->queryAll();
    	foreach ($values as $val){	$sr[$val['id_project']]= ProjectsAlerts::getAlertsCount($val['id_project']); }
    	$data_chart = array();	arsort($sr); $i=0;	
				foreach ($sr as $key=>$results)
					{ if($i<10){
						array_push($data_chart,array('label' => Projects::getNameByID($key),
            			  'value' => $results));
						$i++;
            			   }
						
					}
    	echo json_encode($data_chart);
    }
} ?>
