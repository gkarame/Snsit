<?php
class WidgetOldestInvoices extends CWidget 
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
    	$this->render('/widgets/oldestInvoices', array(
            'dis'=>$this,
        ));
    }    	
 	public static function getCustomers($top = 10)
 	{
 		
 		$results=Yii::app()->db->createCommand("select id_customer,customers.name as CustName, final_invoice_number,DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age, net_amount, printed_date,receivables.id_assigned,users.firstname as FN,users.lastname as LN,partner,receivables.status,receivables.partner_status,currency
			from receivables join customers on customers.id = id_customer  join  users on users.id = receivables.id_assigned
			where id_customer not in (239,323) and ((partner = 77 and receivables.status Not in ('Paid')) or (partner != 77 and partner != 554 and (partner_status Not in ('Paid') or partner_status is null)) and partner != 554 )
			order by age desc
 			")->queryAll();	$i=0;	$customers=array();	$rtrnRes = array();
 		foreach ($results as $result) 
 		{
 			if (!empty($result['printed_date']) && $result['printed_date'] != '0000-00-00' && $i<$top)
			{
 			$rtrnRes['customer_name']=$result['CustName']; 	$rtrnRes['Invoice_num']=$result['final_invoice_number'];
 			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
			{
				$rate = CurrencyRate::getCurrencyRate($result['currency']);
				if (isset($rate['rate'])){	$rtrnRes['amount'] = $result['net_amount'] * $rate['rate'];	}
			}else{	$rtrnRes['amount'] = $result['net_amount'];	} 
			$rtrnRes['age']=$result['age'];	$rtrnRes['resource'] = $result['FN'].' '.$result['LN'];
			array_push($customers,array('customer_name'=>$rtrnRes['customer_name'], 'Invoice_num' => $rtrnRes['Invoice_num'],'amount'=>$rtrnRes['amount'], 'Age'=> $rtrnRes['age'],'Resource'=> $rtrnRes['resource']));			
			$i = $i+1;		}
		}
   		return $customers;
 	}
}?>
