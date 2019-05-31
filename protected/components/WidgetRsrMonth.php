<?php
class WidgetRsrMonth extends CWidget 
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
    	$this->render('/widgets/rsrMonth', array(
            'dis'=>$this,
        ));
    }    
 	 public function CharChart()
    { 
    	$values = array();	$data_chart = array();
    	for($i = 2;$i>=0;$i--){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now'))); $jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb; }
		sort($months);	$time = "( Last 3 Months )"; $data_chart = array();	
		foreach($months as $month)
		{
			$query="SELECT count(1) as value from rsr where adddate like '$month%'";  
	  		$value=Yii::app($query)->db->createCommand($query)->queryScalar(); 
	  		//array_push($data_chart,array('category' => date('F', strtotime($month)), 'value' => (int)$value));
	  		array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (int)$value));
		}
	    //print_r($data_chart);exit;
    	echo json_encode($data_chart);
	}
}?>



				