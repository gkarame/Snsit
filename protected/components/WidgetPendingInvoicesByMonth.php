<?php
class WidgetPendingInvoicesByMonth extends CWidget 
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
    	$this->render('/widgets/pendinginvoicesbymonth', array(
            'dis'=>$this,
        ));
    }
 	public function CharChart($year = null)
    {
    	$year=(date('Y',strtotime('now')));$sr = array();
			$data_chart = array();
			$jan=$year.'-01';
			$Feb=$year.'-02';
			$mar=$year.'-03';

    	for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
						   $months[] = $Feb;
						}
					}
		sort($months);
		foreach ($months as $month)
			{
				$m=date('Y-m-t', strtotime($month));	$starter=date('Y-m-01', strtotime($month));	$bound=date('Y-m-01', strtotime('2016-10'));
				$sr['Payment'] = 0;
				if ($starter>=$bound)
				{				
					$values=Yii::app()->db->createCommand("select 
					case when r.currency=9 
					THEN r.net_amount 
					else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
					from receivables r
					where LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))<='".$m."' and r.id_customer not in (239,323) and 
					(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))
					 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date>'".$m."' and paid_date<>'0000-00-00'))
					and partner != 554 ")->queryAll();			
			 		$sr['Payment'] = round(array_sum(array_column($values,'net_amount')), 2);
				}else{	$sr['Payment'] = 0; }
				$sr['month'] = date('M-y', strtotime($month));	$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment'];
				array_push($data_chart,$sr);
			 }  
    		echo json_encode($data_chart);
    }    
}?>
