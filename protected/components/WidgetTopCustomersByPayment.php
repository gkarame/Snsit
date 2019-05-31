<?php
class WidgetTopCustomersByPayment extends CWidget 
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
    	//invoicesbypartner
    	$this->render('/widgets/topcustomersbypayment', array(
            'dis'=>$this,
        ));
    }
 	public static function getTopCustomers($top = 5)
 	{
 		$data_chart = array();	$rtrnRes = array();
 		$select ="select id_customer,customers.name as  CustName, sum(net_amount) as am,currency from receivables join customers on customers.id = id_customer
			where id_customer not in (239,323) and ((partner = 77 and receivables.status<>'Paid') or (partner != 77 and (partner_status <>'Paid' or partner_status is null)) and partner != 554 )
			GROUP BY id_customer, currency	order by am desc ";
		$results=Yii::app()->db->createCommand($select)->queryAll();		 		 
		foreach ($results as $result) 
 		{ 	
 			$key = $result['id_customer'];
 			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY){
					$rate = CurrencyRate::getCurrencyRate($result['currency']);
					if (isset($rate['rate'])){	$realamount = $result['am'] * $rate['rate']; }
			}else{	$realamount = $result['am']; } 
 			if(!isset($rtrnRes[$key])){ 
 				$rtrnRes[$key]['Name'] = $result['CustName'];	$rtrnRes[$key]['AmountInDollars'] = (double)round($realamount,2);
			}else{	$rtrnRes[$key]['AmountInDollars'] += (double)round($realamount,2);	}
		}
		usort($rtrnRes, Widgets::build_sorter('AmountInDollars'));
		foreach(array_slice($rtrnRes, 0, $top) as $res){
			array_push($data_chart,array('Customer' => $res['Name'],
            	  'value' => $res['AmountInDollars']));
		}
 		echo json_encode($data_chart);
 	}
}?>
