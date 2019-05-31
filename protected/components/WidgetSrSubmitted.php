<?php
class WidgetSrSubmitted extends CWidget 
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
    	$this->render('/widgets/srSubmitted', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
   		for($i = 5;$i>=0;$i--){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now'))); $jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb; }
		sort($months);	$time = "( Last 6 Months )";	$values = array();	$data_chart = array();	$x_axis = array();
		$status_closed = 3;	$status_confirm_closed = 5;
		foreach($months as $month)
		{
			$value= Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE date like '$month%' ")->queryScalar();
			$closed=Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryScalar();
			array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (int)$value, 'value2' => (int)$closed));
		}
    	echo json_encode($data_chart);
    }
}?>
