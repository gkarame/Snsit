<?php
class WidgetCountryRevenues extends CWidget 
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
    	$this->render('/widgets/countryRevenues', array(
            'dis'=>$this,
        ));
    }    
    public function CharChart($year = null)
    {
    	switch ($year)
    	{
    		case 'current':
    			$data = date('Y');
    		break;
    		case 'last':
    			$data = date('Y') - 1;
    		break;
    		case 'last3':
    			$data = array(date('Y') , date('Y') - 1, date('Y') - 2);
    		break;
    		default:
    			$data = date('Y');
    		break;
    	}
		$data_chart = self::getCountryRevenues($data);
    	echo json_encode($data_chart);
    }	
    public static function getCountryRevenues($date = '55',$date2 = '55',$date3 = '55')
    {
    	$results = Yii::app()->db->createCommand()
    				->select('invoices.id, invoices.currency, invoices.net_amount as amount, customers.country AS id_country')
    				->from('invoices')
    				->join('customers', 'invoices.id_customer = customers.id')
    				->join('codelkups', 'customers.country = codelkups.id')
    				->where('invoices.status IN("Printed", "Paid")  AND invoices.id_customer not in (239,323) AND  (invoices.printed_date like "'.$date.'%" OR invoices.printed_date like "'.$date2.'%" OR invoices.printed_date like "'.$date3.'%")')
    				->queryAll();
   		$sum = array();
    	$data_chart = array();
   		foreach ($results as $result)
   		{
   			$amount = $result['amount'];   		
   			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
   			{
   				$rate = CurrencyRate::getCurrencyRate($result['currency']);
   				if (isset($rate['rate']))
   				{
   					$amount = $amount * $rate['rate'];
   				}
   			}
   			if (!isset($sum[$result['id_country']])) {
   				$sum[$result['id_country']]['val'] = $amount;
   			} else {
    			$sum[$result['id_country']]['val'] += $amount;
    		}
   		}
   		uasort($sum, Widgets::build_sorter('val'));
   		foreach ($sum as $key_sum=>$s)
   		{
   			array_push($sum[$key_sum],array('id'=>$key_sum));
   		}
   		foreach (array_slice($sum, 0, count($sum)) as $cust)
   		{
   			array_push($data_chart,array('category' =>  Codelkups::getCodelkup($cust[0]['id']),
       		'value' => (int)$cust['val']));
   		}
    	return $data_chart;	
    }
}
