<?php
class WidgetRsrvsSr extends CWidget 
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
    	$this->render('/widgets/rsrvsSr', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
   		for($i = 5;$i>=0;$i--){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now'))); $jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb; }
		sort($months);	$time = "( Last 6 Months )"; $data_chart = array();	
		foreach($months as $month)
		{
			$sr= Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE date like '$month%' ")->queryScalar();
			$srtime= Yii::app()->db->createCommand("select sum(amount) from user_time where `default`=1 and date like '$month%'  and id_task in (select id from default_tasks where id_parent=27)")->queryScalar();
			$rsr=Yii::app()->db->createCommand("SELECT count(1) FROM rsr WHERE adddate like '$month%' ")->queryScalar();
			$rsrtime= Yii::app()->db->createCommand("select sum(amount) from user_time where `default`=1 and date like '$month%'  and id_task in (select id from default_tasks where id_parent=1324)")->queryScalar();
			
			if($sr == 0 || $srtime== null)
			{
				$avgsr= 0;
			}else{
				$avgsr= $srtime/$sr;
			}
			if($rsr == 0 || $rsrtime== null)
			{
				$avgrsr= 0;
			}else{
				$avgrsr= $rsrtime/$rsr;
			}
			array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (double)$avgrsr, 'value2' => (double)$avgsr));
		}
    	echo json_encode($data_chart);
    }
}?>
