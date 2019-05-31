<?php
class WidgetUnsatisfied extends CWidget 
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
    	$this->render('/widgets/unsatisfied', array(
            'dis'=>$this,
        ));
    }    
 	 public function CharChart()
    { 
    	$values = array();	$data_chart = array();	$top5=array();
	    $query="SELECT DISTINCT(id_customer) FROM `support_desk` where rate_date>=(CURRENT_DATE()- INTERVAL 6 MONTH) AND `status`=5 and rate is not null and rate<>0";  
	    $values=Yii::app($query)->db->createCommand($query)->queryAll(); $avg_rate=0;   $customer_issues=0;   $customer_rate=0;
	    foreach ($values as $key => $value) 
	    {	        
	        $customer_issues = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where rate_date>=(CURRENT_DATE()- INTERVAL 6 MONTH) AND id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate not in (0,4,5)  ")->queryScalar();
	 		if ($customer_issues != 0)	{	array_push($top5, array('id' => $value['id_customer'],'value'=>$customer_issues));	}
	 	}
		usort($top5, Widgets::build_sorter('value'));	$values= array_slice($top5, 0 ,5); 	$total = (double)array_sum(array_column($values,'value'));
	 	foreach ($values as $key => $value) {
	    	$customer_name=Customers::getNameByID($value['id']);
	    	$customer_totalIssues=Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where  id_customer ='".$value['id']."' and `status`=5 and rate is not null and rate <>0  ")->queryScalar();
	        $customer_issues = $value['value'];	$avg_rate=($customer_issues/$total);   $percent = number_format(($avg_rate)*100,0);
			array_push($data_chart,array('category' => $customer_name.' '.$customer_issues.' - 6', 'value' => (double)$percent));
		}
    	echo json_encode($data_chart);
	}
}?>



				