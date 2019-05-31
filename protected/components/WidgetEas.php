<?php
class WidgetEas extends CWidget 
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
    	$years = Widgets::getLast3Years();
    	$this->render('/widgets/eas', array(
            'dis'=>$this,
    		'years'=>$years,
        ));
    }    
 	public function CharChart($year = null)
    {
    	$date = array();		$month =  date('Y-m');		$data_chart = array();
		for($i=11;$i>=0;$i--) 		{			$date[] = date('Y-m',strtotime($month . ' - '.$i.' month'));		}
		$year=(date('Y',strtotime('now')));		$jan=$year.'-01';		$Feb=$year.'-02';		$mar=$year.'-03';
		if (in_array($jan, $date) && in_array($mar, $date) && !in_array($Feb, $date)) {
		   $date[] = $Feb;
		} sort($date);
		foreach ($date as $data)
		{
			$month = date('M',strtotime($data));			$year_data = date('Y',strtotime($data));	$val = self::getAmountMonth($data);
			 array_push($data_chart,array('label' => $month."-".substr($year_data,2,2),
            	  'value' => $val));
		}	    	
    	$chart = array( 
		          "xAxisName" => "Months",
		          "yAxisName" => "Total Amount",
		          "numberPrefix" => "$"
		    );
    	echo json_encode($data_chart);    	
    }
    public static function getAmountMonth($date)
    {
    	$status = "(".Eas::STATUS_CANCELLED.",".Eas::STATUS_NEW.")";
    	$results = Yii::app()->db->createCommand("SELECT id,currency FROM eas WHERE status NOT IN $status and approved like '$date%' and old='No'")->queryAll();
   		$sum = 0;
    	foreach ($results as $result)
    	{
    		$id = $result['id'];
	   		$vals = Yii::app()->db->createCommand("SELECT amount FROM eas_items WHERE id_ea = $id")->queryAll();
	   		foreach ($vals as $val)
	   		{
	   			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
				{
					$rate = CurrencyRate::getCurrencyRate($result['currency']);
					if (isset($rate['rate']))
					{
						$sum += $val['amount'] * $rate['rate'];
					}
				}else{
					$sum += $val['amount'];
				}	   		
	   		}
   		}    	return $sum;
    }
}
