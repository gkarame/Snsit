<?php
class WidgetResourceProductivity extends CWidget 
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
    	$this->render('/widgets/resourceproductivity', array(
            'dis'=>$this,
        ));
    }
 	public static function getResources()
 	{
 		$rtrnRes = array();
 			$select ="select id_assigned as Resource_ID, Count(*) as Invoices_count,users.firstname as FirstName ,users.lastname as LastName,
 			case when receivables.currency=9 
			THEN sum(receivables.net_amount)
			else sum(receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency limit 1)) end as val			
			from receivables JOIN users on receivables.id_assigned = users.id where id_customer not in (239,323) and ((partner =77 and status<>'Paid')) OR (partner !=77 and status <>'Paid' and (partner_status <>'Paid' or partner_status is null)) and partner !=554  GROUP BY Resource_ID ORDER BY val DESC ";
		$results=Yii::app()->db->createCommand($select)->queryAll();
		foreach ($results as $result) 
 		{ 	
 			$key = $result['Resource_ID'];	$realamount = $result['val'];	$rtrnRes[$key]['id'] = 	$result['Resource_ID'];
 			$rtrnRes[$key]['FirstName'] = $result['FirstName'];	$rtrnRes[$key]['LastName'] = $result['LastName'];
 			$rtrnRes[$key]['total_invoices'] = Receivables::getCountInv($result['Resource_ID']);
			$rtrnRes[$key]['AmountInDollars'] = Receivables::getInvPerAssigned($result['Resource_ID']);			
		}
 		return $rtrnRes;
 	}
}?>
