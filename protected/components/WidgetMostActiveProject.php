<?php
class WidgetMostActiveProject extends CWidget 
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
    	$this->render('/widgets/mostActiveProject', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    		$month= date('Y-m-01',strtotime('now - 6 month'));	$current_date = date('Y-m-d',strtotime('now'));	$values = array();	$sr = array();
		 	$select ="SELECT pp.id_project,round(SUM(uts.amount)/8,3) as man_days FROM user_time uts  LEFT JOIN projects_tasks pt ON uts.id_task=pt.id LEFT JOIN projects_phases pp  ON pt.id_project_phase=pp.id ";
			$where=" where uts.`default`='0'  and uts.date between '$month' and '$current_date' ";
			$group="group by pp.id_project ";	$order="order by man_days desc limit 5";
			$values = Yii::app()->db->createCommand($select." ".$where." ".$group." ".$order."" )->queryAll();
			foreach ($values as $val)
			{	$sr[$val['id_project']]= $val['man_days'] ; }			
			$data_chart = array();	asort($sr);
			foreach ($sr as $key=>$results)
			{   
				array_push($data_chart,array('label' =>substr(Projects::getNameByid($key),0,30),
            	  'value' => $results));
			}
			echo json_encode($data_chart);
    }
} ?>
