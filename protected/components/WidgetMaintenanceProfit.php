<?php
class WidgetMaintenanceProfit extends CWidget 
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
    $this->render('/widgets/maintenanceProfit', array(
          'dis'=>$this,
       ));
  }
  public function CharChart()
  { 
	$values = array(); $axis = array();    
	$year= date('Y'); $i=0;
	$values=Yii::app()->db->createCommand("select distinct(customer) from maintenance where customer not in (177, 101) ")->queryAll(); 
    foreach ($values as $customer)
    {
    	$customer_values=Maintenance:: getTotalNetAmountYearPerCustomer($customer['customer'] ,$year);
    	$Yrevenues= $customer_values[0];
		$cost= $customer_values[1];
	    if($Yrevenues != 'false' || $cost> 0)
		{	
	    	$srs= $customer_values[2];
	    	$hours= $customer_values[3];
			$Xprofit= $Yrevenues - $cost;
			$enddate=$customer_values[4];

			if($Xprofit!=0)
	        {
	        	$axis[$i]['tag1']= "<span style='font-size:15px;'><b>".Customers::getNameById($customer['customer'])."</b></span>\nRevenues: ".Utils::formatNumber($Yrevenues,2)." $ \n Cost: ".Utils::formatNumber($cost,2)." $ \n # of SRs: ".Utils::formatNumber($srs)."\n# of Hours: ".Utils::formatNumber($hours)." \n Profit: ".Utils::formatNumber($Xprofit, 2)." $ \n ".$enddate;
		        $axis[$i]['older1']= (double)round($Xprofit,2); 
		        $axis[$i]['total1']= (double)round($Yrevenues,2); 
		        $i++;
		    }
		}
    }
    echo json_encode($axis);
  }

} ?>
