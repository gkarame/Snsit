<?php
class WidgetSubmittedReason extends CWidget 
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
    	$this->render('/widgets/submittedReason', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	for ($i = 0;$i<3;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02'; $mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb; }
		sort($months); $time = "( Last 3 Months )";
    	if(Yii::app()->user->isAdmin){
			$values = array(); $sr = array();	sort($months); $data_chart = array();
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and reason is not NULL")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}			
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
	    }else{
		    $id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
			$values = array();	$sr = array();	sort($months);	$data_chart = array();
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and id_customer=".$id_customer." and reason is Not NULL ")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}		
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
	    }
    	$chart = array( 
		          "xAxisName" => "Reason", );
    	echo json_encode(array(
            	'chart'=>$chart,
    			'data'=>$data_chart,
        	));	
    }
	public function CharChart1($year = null)
    {
    	for ($i = 0;$i<3;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }	$time = "( Last 3 Months )";
    	if(Yii::app()->user->isAdmin){
    		$values = array();	$sr = array();	sort($months); $data_chart = array();
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and reason is not NULL")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}	
			$total=array_sum($sr);		
			foreach ($sr as $key=>$results)
			{
				$perce=Utils::formatNumber($results*100/$total,2);
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key)." (".$perce."%)",
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
		}else{
		    $id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
			$values = array();	$sr = array();	sort($months);	$data_chart = array();
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and id_customer=".$id_customer." and reason is not NULL ")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}		
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
	    }
    	echo json_encode($data_chart);    	
    }
}?>
