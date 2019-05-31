<?php
class WidgetDsoIndex extends CWidget 
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
    	$this->render('/widgets/dsoindex', array(
            'dis'=>$this,
        ));
    }    
	public function CharChart1($year = null)
    {
   		for($i = 0;$i<5;$i++)		{			$years[] = date('Y',strtotime('now - '.$i.' year'));		}
		$data_chart = array();		sort($years);
		foreach ($years as $year)
		{
			$all= Invoices::getnettotPerYear($year);
			if($all ==0 ){ $all=1;}
			$value = Invoices::getnetPendingPerYear($year)/$all;
			$finalval = (double)$value*365;
		    array_push($data_chart,array('label' => $year,
	            	  'value' => $finalval));
		}    	echo json_encode($data_chart);    	
    }
}
