<?php
class WidgetSrTopCustomer extends CWidget 
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
    	$this->render('/widgets/srTopCustomer', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	for ($i = 0;$i<3;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb;	}
		sort($months);	$time = "( Last 3 Months )";	$values = array();	$sr = array();
		foreach($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT id,id_customer,reopen FROM support_desk WHERE date like '$month%'")->queryAll();
			foreach ($values as $val)
			{	
				if (!isset($sr[$val['id_customer']]['val'])){ $sr[$val['id_customer']]['val'] = 1;}
				else{ $sr[$val['id_customer']]['val'] ++;}					
				$sr[$val['id_customer']]['cust'] = $val['id_customer']; 				
				if (!isset($sr[$val['id_customer']]['reopen'])) { $sr[$val['id_customer']]['reopen'] = $val['reopen']; }
				else { $sr[$val['id_customer']]['reopen'] += $val['reopen']; }
			}
		}
		uasort($sr, Widgets::build_sorter('val'));	$data_chart = array();
		foreach(array_slice($sr, 0, 10) as $results)
		{
	    	array_push($data_chart,array('category' => Customers::getNameById($results['cust']).' ('.$results['reopen'].' R)',
           	  'value' => $results['val']));
		}
    	echo json_encode($data_chart);    	
    }
}?>
