<?php
class WidgetRevenues extends CWidget 
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
    	$this->render('/widgets/revenues', array(
            'dis'=>$this,
        ));
    }
 	public static function getRevenues($old = 'No')
 	{
 		$revenuesDb = Yii::app()->db->createCommand()
 								->select('net_amount as amount, currency, invoice_date_month')
 								->from('invoices')
 								->where('old = :old_status AND invoice_date_year = :currentYear AND status =:new', array(':old_status' => $old, ':currentYear' => date('Y'),':new'=>"New"))
 								->queryAll();
 		$revenuesArr = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
 		foreach($revenuesDb as $key => $revenue)
 		{
 			$amount = $revenue['amount'];
 			if ($revenue['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
 			{
 				$rate = CurrencyRate::getCurrencyRate($revenue['currency']);
 				if (isset($rate['rate'])){	$amount = $amount * $rate['rate']; }
 			} 			
 			$revenuesArr[$revenue['invoice_date_month']] += $amount;
 		} 		
 		return $revenuesArr;
 	}
	public static function getRevenuesBlank($old = 'No')
 	{
 		$revenuesDb = Yii::app()->db->createCommand()
 								->select('net_amount as amount, currency')
 								->from('invoices')
 								->where('old = :old_status AND invoice_date_year IS NULL AND status =:new', array(':old_status' => $old,':new'=>"New"))
 								->queryAll();	$revenuesArr['empty'] = 0;
 		foreach($revenuesDb as $key => $revenue)
 		{
 			$amount = $revenue['amount'];
 			if ($revenue['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
 			{
 				$rate = CurrencyRate::getCurrencyRate($revenue['currency']);
 				if (isset($rate['rate'])) {	$amount = $amount * $rate['rate']; 	}
 			}
 			$revenuesArr['empty'] += $amount;
 		}
 		return $revenuesArr;
 	}
}?>