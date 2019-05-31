<?php
class WidgetAgeVariance extends CWidget 
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
    	$this->render('/widgets/ageVariance', array(
            'dis'=>$this,
        ));
    }
 public function CharChartAge($year = null)
    {
    	$currentm= date('Y-m',strtotime('now'));
		for($i = 0;$i<3;$i++)
	    {
			$months[] = date('Y-m',strtotime('now - '.$i.' month'));
		}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		$dataset = array();
		sort($months);
		foreach ($months as $k => $month)
		{
			$sr = array();
				
			    $data = array();
			    $data['month'] = date('M-Y', strtotime($month));
			    $lastday=date('Y-m-t', strtotime($month));
				$starter=date('Y-m-01', strtotime($month));
				$bound=date('Y-m-01', strtotime('2016-10'));
			if ($currentm == $month)
			{
$sr = Yii::app()->db->createCommand(" select DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) as age,
				IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 365, '>365', 
					IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 180,'180 - 365',
						IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 120,'120 - 180',
							IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 90,'90 - 120', 
								IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 60,'60 - 90', '0 - 60')
							)
						)
					)
				) as textdays,
			case when t.currency=9 
					THEN t.net_amount 
					else t.net_amount*(select c.rate from currency_rate c where c.currency=t.currency  order by date DESC  limit 1) end as net_amount
					from receivables t
					where LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))<='".$lastday."' and id_customer not in (239,323) and 
					(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and (partner_status Not in ('Paid','Cancelled') or partner_status is null or partner_status='' or partner_status=' ')))
					 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date>'".$lastday."' and paid_date<>'0000-00-00'))
					and partner != 554  ")->queryAll();
			}else
			{
				$sr = Yii::app()->db->createCommand(" select DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) as age,
				IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 365, '>365', 
					IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 180,'180 - 365',
						IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 120,'120 - 180',
							IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 90,'90 - 120', 
								IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 60,'60 - 90', '0 - 60')
							)
						)
					)
				) as textdays,
			case when t.currency=9 
					THEN t.net_amount 
					else t.net_amount*(select c.rate from currency_rate c where c.currency=t.currency  order by date DESC  limit 1) end as net_amount
					from receivables t
					where LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))<='".$lastday."' and id_customer not in (239,323) and 
					(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and (partner_status Not in ('Paid','Cancelled') or partner_status is null or partner_status='' or partner_status=' ')))
					 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date>'".$lastday."' and paid_date<>'0000-00-00'))
					and partner != 554  ")->queryAll();
				}
				$totyear=0;$tot360=0; $tot180=0; $tot120=0; $tot90=0; $tot60=0;
				
				foreach ($sr as $key => $value) {
					
					if ($value['textdays'] == '>365')
					{
						$totyear= $totyear + $value['net_amount'];
					} else if ($value['textdays'] == '180 - 365')
					{
						$tot360= $tot360 + $value['net_amount'];
					} else if ($value['textdays'] == '120 - 180')
					{
						$tot180= $tot180 + $value['net_amount'];
					} 
					else if ($value['textdays'] == '90 - 120')
					{
						$tot120= $tot120 + $value['net_amount'];
					} else if ($value['textdays'] == '60 - 90')
					{
						$tot90= $tot90 + $value['net_amount'];
					} 
					else  
					{
						$tot60= $tot60 + $value['net_amount'];
					} 
				}
				
				$data['>365'] = (double)round($totyear,2);	
				$data['180 - 365'] = (double)round($tot360,2);
				$data['120 - 180'] = (double)round($tot180,2);
				$data['90 - 120'] = (double)round($tot120,2);
				$data['60 - 90'] = (double)round($tot90,2);
				$data['0 - 60'] = (double)round($tot60,2);
			    //asort($data);
			    array_push($dataset,$data);

		}
		echo json_encode($dataset);
    }
    public function getSeries(){
    	$x_axis = array();     	 
		array_push($x_axis,array('valueField'=>'>365','name'=>'>365'));
		array_push($x_axis,array('valueField'=>'180 - 365','name'=>'180 - 365'));		
		array_push($x_axis,array('valueField'=>'120 - 180','name'=>'120 - 180'));
		array_push($x_axis,array('valueField'=>'90 - 120','name'=>'90 - 120'));
		array_push($x_axis,array('valueField'=>'60 - 90','name'=>'60 - 90'));
		array_push($x_axis,array('valueField'=>'0 - 60','name'=>'0 - 60'));
		echo json_encode($x_axis);
    }
}
?>
