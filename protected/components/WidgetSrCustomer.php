<?php
class WidgetSrCustomer extends CWidget 
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
    	$this->render('/widgets/srCustomer', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
		for ($i = 0;$i<12;$i++) {	$months[] = date('Y-m',strtotime('now - '.$i.' month'));	}
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01'; $Feb=$year.'-02'; $mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb; }
		sort($months);	$time = "( last 12 months)"; $values = array();	$sr = array();
		foreach($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT id,id_customer,reopen FROM support_desk WHERE date like '$month%'")->queryAll();
			foreach($values as $val)
			{	
				if (!isset($sr[$val['id_customer']][$month]))
					$sr[$val['id_customer']][$month] = 1;
				else
					$sr[$val['id_customer']][$month] ++;
					
				if (!isset($sr[$val['id_customer']]['reopen']))
					$sr[$val['id_customer']]['reopen'] = $val['reopen']; 
				else 
					$sr[$val['id_customer']]['reopen'] += $val['reopen']; 	
			}

		}
		$total=0;
		foreach($sr as $key=>$results)
		{
			$i = 0;	$sum  = 0;
			foreach ($results as $key_re=>$val)
			{
				if($key_re != 'reopen'){
						$sum += $val;	$total+=$sum;
				}
			}
		}
		$data_chart = array();
		foreach($sr as $key=>$results)
		{
			$i = 0;	$sum  = 0;	$sumtot  = 0;
			foreach ($results as $key_re=>$val)
			{
				if($key_re != 'reopen'){ $sum += $val; $i++;	}
				$sumtot += $val;
			}
			$percent = number_format(($sum/$total)*100,0);
			array_push($data_chart,array('category' => Customers::getNameById($key).' '.$sumtot.' - (1',
            	  'value' => $sum));
		}		
		usort($data_chart, Widgets::build_sorter('value'));	echo json_encode($data_chart);    	
    }
}?>
