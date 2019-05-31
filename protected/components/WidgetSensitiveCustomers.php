<?php
class WidgetSensitiveCustomers extends CWidget 
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
    	$this->render('/widgets/sensitiveCustomers', array(
            'dis'=>$this,
        ));
    }
 	public static function getResources()
 	{
 		$rtrnRes = array();
 		$results=Yii::app()->db->createCommand("select DISTINCT(id_customer) as cust from receivables r where r.id_customer not in (239,323) and r.id_customer in (select id from customers where checkb='checked')  and ((r.partner =77 and r.status <> 'Paid') OR (r.partner !=77 and r.status<>'Paid' and (r.partner_status <>'Paid' or r.partner_status is null)))")->queryAll(); 
		foreach ($results as $result) 
 		{ 	
 			$key = $result['cust']; 			
 			if(!isset($rtrnRes[$key]))
 			{
	 			$rtrnRes[$key]['id'] = 	$result['cust']; $rtrnRes[$key]['customer'] = Customers::getNameById($result['cust']);
	 			$rtrnRes[$key]['avgage'] =  Receivables::getavgageNotPaidAll($result['cust']);
	 			$rtrnRes[$key]['maxage'] = Receivables::getmaxAgePendingAll($result['cust']);
				$rtrnRes[$key]['AmountInDollars'] = Receivables::gettotalnotpaidandAll($result['cust']);
			}
		}
		uasort($rtrnRes, Widgets::build_sorter('avgage')); return $rtrnRes;
 	}
}?>
