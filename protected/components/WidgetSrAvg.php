<?php
class WidgetSrAvg extends CWidget 
{
	const DEFAULT_SUPPORT = 27;
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
    	$this->render('/widgets/srAvg', array(
            'dis'=>$this,
        ));
    }
 	public function CharChart($year = null)
    {
		$currmonth = (intval(date('m',strtotime('now')))); $sr = array(); $data_chart = array();					
		for ($i = 0;$i<6;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01'; $Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb; }	sort($months);
		foreach ($months as $month)
		{
			$m=date('m', strtotime($month));	$starter=date('Y-m-01', strtotime($month));	$sr['Payment'] = 0; $hours=0;
			$values = Yii::app()->db->createCommand("select DISTINCT(sd_no) as sd from support_desk where MONTH(date)=".$m." and date>='".$starter."' and `status` in (3,5) and sd_no in (select id_support_desk from support_desk_comments where status=3 )")->queryAll();
			$hours=Yii::app()->db->createCommand("select SUM(amount) from user_time where id_task in ( select id from default_tasks where id_parent='27' ) and MONTH(date)=".$m." and date>='".$starter."'")->queryScalar();
			if (sizeof($values)>0) { $sr['Payment']= Utils::formatNumber(($hours/ sizeof($values)),2); }
			else{ $sr['Payment']= 0; }			
			$sr['month'] = date('M-y', strtotime($month));	$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment'];
			array_push($data_chart,$sr);
		}  
		echo json_encode($data_chart);
    }    
} ?>
