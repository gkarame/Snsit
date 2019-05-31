<?php
class WidgetSrPriorityCustomer extends CWidget 
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
    	$this->render('/widgets/srPriorityCustomer', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	$id_user=Yii::app()->user->id;
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
    	for ($i = 0;$i<3;$i++)	{	$months[] = date('Y-m',strtotime('now - '.$i.' month'));	}
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb;	}
		sort($months);	$time = "( Last 3 Months )";	$values = array();	$sr = array();
		foreach($months as $month)
		{
			$values = Yii::app()->db->createCommand("select severity from support_desk WHERE date like '$month%' and id_customer=".$id_customer." ")->queryAll();
			foreach ($values as $val)
			{	
				if (!isset($sr[$val['severity']]['val']))
					$sr[$val['severity']]['val'] = 1;
				else
					$sr[$val['severity']]['val'] ++;
				$sr[$val['severity']]['cust'] = $val['severity']; 
				
			}
		}			
		uasort($sr, Widgets::build_sorter('val'));	$data_chart = array();
		foreach(array_slice($sr, 0, 10) as $results)
		{
	    	array_push($data_chart,array('category' => $results['cust']."(".$results['val'].")",
           	  'value' => $results['val']));
		}
    	echo json_encode($data_chart);    	
    }
}?>
