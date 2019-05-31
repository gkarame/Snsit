<?php
class WidgetPendingInvoicesByPartner extends CWidget 
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
    	$this->render('/widgets/invoicesbypartner', array(
            'dis'=>$this,
        ));
    }
 	public static function getAmounts()
 	{
 		$data_chart = array();	$rtrnRes = array();
 		$select ="select partner,sum(net_amount) as am,currency from receivables 
		where id_customer not in (239,323) and ((partner = 77 and status<>'Paid') or (partner != 77 and partner !=554 and (partner_status <>'Paid' or partner_status is null)))
		group by currency,partner";	 $results=Yii::app()->db->createCommand($select)->queryAll(); 		 		 
		foreach ($results as $result) 
 		{ 	
 			$key = $result['partner'];
 			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
				{
					$rate = CurrencyRate::getCurrencyRate($result['currency']);
					if (isset($rate['rate']))
					{
						$realamount = $result['am'] * $rate['rate'];
					}
				}else{	$realamount = $result['am']; } 
 			if(!isset($rtrnRes[$key]))
 			{
 			$rtrnRes[$key]['Name'] = Codelkups::getCodelkup($result['partner']);
			$rtrnRes[$key]['AmountInDollars'] = (double)round($realamount,2);
			}
			else{	$rtrnRes[$key]['AmountInDollars'] += (double)round($realamount,2);	}
		}
		foreach($rtrnRes as $res){
			array_push($data_chart,array('Partner' => $res['Name'],
            	  'value' => $res['AmountInDollars']));
		}
 		echo json_encode($data_chart);
 	}
}?>
