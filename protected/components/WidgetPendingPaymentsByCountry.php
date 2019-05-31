<?php
class WidgetPendingPaymentsByCountry extends CWidget 
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
    	$this->render('/widgets/pendingpaymentsbycountry', array(
            'dis'=>$this,
        ));
    }
    public function CharChart($year = null,$Amount = 0)
    {
    	switch ($year)
    	{
    		case '1':
    			$data = date('Y-m',strtotime('now - 3 months'));
    		break;
    		case '2':
    			$data = date('Y-m',strtotime('now - 6 months'));
    		break;
    		case '3':
    			$data = date('Y-m',strtotime('now - 9 months'));
    		break;
        case '4':
          $data = date('Y-m',strtotime('now - 1 year'));
        break;
    		default:
    			$data = date('Y-m',strtotime('now - 3 months'));
    		break;
    	}
		$data_chart = self::getPaymentsByCountry($data,$Amount); echo json_encode($data_chart);
    }
  public static function getsearch($array, $key, $value) 
  { 
      $results = array();
      if (is_array($array)) 
      { 
          if (isset($array[$key]) && $array[$key] == $value) 
              $results[] = $array; 

          foreach ($array as $subarray) 
              $results = array_merge($results, self::getsearch($subarray, $key, $value)); 
      } 
     return $results; 
  } 
  public static function getPaymentsByCountry($date,$bgamount)
    {
    	$results = Yii::app()->db->createCommand("select cl.codelkup as country_name, r.currency, sum(net_amount) as total
        from receivables r join customers c on r.id_customer = c.id join codelkups cl on c.country = cl.id 
        where id_customer not in (239,323) and ((r.partner = 77 and r.status Not in ('Paid')) or (r.partner !=77 and (r.partner_status Not in ('Paid') or r.partner_status is null))) and r.partner != 554 
        group by country_name, r.currency ")->queryAll();
      $uniquenames= array_unique(array_column($results, 'country_name'));  $sum = array();  $data_chart = array();
      foreach ($uniquenames as $uniquename) 
      {
        $key=  $uniquename; $amount=0; $elements= self::getsearch($results,  'country_name' , $uniquename);
        foreach ($elements as $element) 
        {            
          if ($element['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
          {
            $rate = CurrencyRate::getCurrencyRate($element['currency']);
            if (isset($rate['rate'])) {  $amount +=  $element['total'] * $rate['rate']; }
          }else{ $amount += $element['total']; }
        }
		$sum[$key]['val'] = $amount; $sum[$key]['Name'] = $uniquename;        
      }
     
    uasort($sum, Widgets::build_sorter('val'));
   	foreach ($sum as $country)
   	{
   		array_push($data_chart,array('category' =>  $country['Name'],
       	 'value' => (double)round($country['val'],2))); 
    }
    return $data_chart; 
   	}    	
}?>

