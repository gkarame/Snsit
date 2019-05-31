<?php
class WidgetMonthlyPayment extends CWidget 
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
    	$this->render('/widgets/monthlypayment', array(
            'dis'=>$this,
        ));
    }
 	public function CharChart($year = null)
    {
    	$currmonth = (intval(date('m',strtotime('now'))));	$sr = array();	$data_chart = array();					
		for ($i = $currmonth-1;$i>-1;$i--)
			{ $months[] = date('Y-m',strtotime('now - '.$i.' month'));			}
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01'; $Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb;}	
		sort($months);
		foreach ($months as $month)
		{
			$m=date('Y-m-t', strtotime($month)); $starter=date('Y-m-01', strtotime($month)); $bound=date('Y-m-01', strtotime('2016-10')); $sr['Payment'] = 0;
			if ($starter>=$bound)
			{
				$values = Yii::app()->db->createCommand("
				select sum(net_amount) as am,currency from receivables 
				where id_customer not in (239,323) and ((partner = 77 and status='Paid') or (partner != 77 and partner !=554 and partner_status ='Paid')) and paid_date like '$month%'
				group by currency ")->queryAll();				
				foreach($values as $value)
				{							
					if($value['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
					{
						$rate = CurrencyRate::getCurrencyRate($value['currency']);
						if (isset($rate['rate']))	{	$realamount = $value['am'] * $rate['rate'];	}
					}else{	$realamount = $value['am'];}
					$sr['Payment'] += (double)round($realamount,2);
				}
			}else{ $sr['Payment'] = 0; }
			$sr['month'] = date('M-y', strtotime($month));	$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment']; array_push($data_chart,$sr);
		}  echo json_encode($data_chart);
    }    
} ?>
