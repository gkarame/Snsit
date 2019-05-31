<?php
class WidgetRsrCustomer extends CWidget 
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
    	$this->render('/widgets/rsrCustomer', array(
            'dis'=>$this,
        ));
    }    
 	 public function CharChart()
    { 
    	$values = array();	$data_chart = array();
	    $query="SELECT DISTINCT(id_customer) as id, count(1) as value from rsr GROUP BY id_customer order by count(1)  DESC LIMIT 10";  
	    $values=Yii::app($query)->db->createCommand($query)->queryAll(); $avg_rate=0;   $customer_issues=0;   $customer_rate=0;	 

		usort($values, Widgets::build_sorter('value')); 	
		$total = (double)array_sum(array_column($values,'value'));

	 	foreach ($values as $key => $value) {
	    	$customer_name=Customers::getNameByID($value['id']);
	    	array_push($data_chart,array('category' => $customer_name, 'value' => (double)$value['value']));
		}
    	echo json_encode($data_chart);
	}
}?>



				