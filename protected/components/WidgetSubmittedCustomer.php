<?php
class WidgetSubmittedCustomer extends CWidget 
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
    	$this->render('/widgets/submittedCustomer', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
   		for($i = 1;$i<5;$i++)	{	$years[] = date('Y',strtotime('now - '.$i.' year'));}
		$data_chart = array();	sort($years);
		foreach ($years as $year)
		{
			$sr = array();
			$values = Yii::app()->db->createCommand("SELECT id,id_customer FROM support_desk WHERE date like '$year%'")->queryAll();
		    foreach ($values as $val)
		    {
		    	if (!isset($sr[$val['id_customer']]))
		    		$sr[$val['id_customer']] = 1;
		    	else 
		    		$sr[$val['id_customer']] ++; 
		    }		    
		    $sum = 0;   $i = 0;
		    foreach ($sr as $key=>$result) {	$sum += $result; $i++; }
		    array_push($data_chart,array('label' => $year."	(".$i.')',	'value' => $sum));
		}
    	$chart = array( 
		          "xAxisName" => "Months",
		          "yAxisName" => "SRs",
		    );
    	echo json_encode(array(
            	'chart'=>$chart,
    			'data'=>$data_chart,
        	));    	
    }
	public function CharChart1($year = null)
    {
   		for($i = 0;$i<5;$i++){	$years[] = date('Y',strtotime('now - '.$i.' year')); }
		$data_chart = array();	sort($years);
		foreach ($years as $year)
		{
			$sr = array();
			$values = Yii::app()->db->createCommand("SELECT id,id_customer FROM support_desk WHERE date like '$year%'")->queryAll();
		    foreach ($values as $val)
		    {
		    	if (!isset($sr[$val['id_customer']]))
		    		$sr[$val['id_customer']] = 1;
		    	else 
		    		$sr[$val['id_customer']] ++; 
		    }		    
		    $sum = 0;    $i = 0;
		    foreach ($sr as $key=>$result){	$sum += $result;	$i++;	}
		    array_push($data_chart,array('label' => $year."	(".$i.')',
	            	  'value' => $sum));
		}
    	echo json_encode($data_chart);    	
    }
}?>
