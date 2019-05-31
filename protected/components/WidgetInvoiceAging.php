<?php
class WidgetInvoiceAging extends CWidget 
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
    	$years = Widgets::getLast3Years();
    	$this->render('/widgets/invoiceAging', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart1($year = null)
    {
      $values = array();      $results = array();      $data_chart = array();
      switch ($year) {
      	case '1':
      		$date = date('Y-m-d',strtotime('now - 1 year'));
      		break;
      	case '2':
      		$onedate = date('Y-m-d',strtotime('now - 1 year'));
          $date = date('Y-m-d',strtotime(date("Y-m-d", strtotime($onedate)) . " - 6 months "));
      		break;
      	case '3':
      		$date = date('Y-m-d',strtotime('now - 2 year'));
      		break;
		case '4':
      		$date = date('Y-m-d',strtotime('now - 1 month'));
      		break;
		case '5':
      		$date = date('Y-m-d',strtotime('now - 2 months'));
      		break;
		case '6':
      		$date = date('Y-m-d',strtotime('now - 3 months'));
      		break;
 		case '7':
      		$date = date('Y-m-d',strtotime('now - 4 months'));
      		break;    
 		case '8':
      		$date = date('Y-m-d',strtotime('now - 5 months'));
      		break;  		
      	default:
      		$date = date('Y-m-d',strtotime('now - 6 months'));
      		break;
      }
    $months = array(0,60,90,120,180,365);
for($i = 0;$i<6;$i++){
  $j = $i+1;
if($i < 5){
	 $values = Yii::app()->db->createCommand("
 select sum(net_amount) as total ,DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age,r.currency
        from receivables r
        where r.id_customer not in (239,323) and (((partner = 77 and r.status <>'Paid') or (partner != 77 and (partner_status <>'Paid' or partner_status is null))) and partner !=554   and DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) > $months[$i] and DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) <= $months[$j])
GROUP BY r.currency,age ORDER BY age desc    		")->queryAll();  } else { 
 $values = Yii::app()->db->createCommand("
 select sum(net_amount) as total ,DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age,r.currency
        from receivables r
        where r.id_customer not in (239,323) and (((partner = 77 and r.status <> 'Paid') or (partner != 77 and (partner_status <>'Paid' or  partner_status is null) )) and partner !=554   and DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) >$months[$i] )
GROUP BY r.currency, age ORDER BY age desc")->queryAll(); }
	 foreach ($values as $key => $value) 
      {
        if ($i < 5){
        $key = $months[$i].'-'.$months[$j]; }else{  $key = '>'.$months[$i]; }
      	if ($value['currency'] != CurrencyRate::OFFICIAL_CURRENCY)
				{
					$rate = CurrencyRate::getCurrencyRate($value['currency']);
					if (isset($rate['rate']))
					{
						$realamount = $value['total'] * $rate['rate'];
					}
				}else{	$realamount = $value['total'];	}
        if(!isset($results[$key])){
          $results[$key]['Aging'] = $key;
          $results[$key]['total'] = $realamount;
        }  else{   $results[$key]['total'] += $realamount;  }      
        }
      }
		foreach ($results as $res) {
          array_push($data_chart,array('label' => $res['Aging'],
                    'value' => (double)round( $res['total'],2)));
        }
		echo json_encode($data_chart);
      }
  }


