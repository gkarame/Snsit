<?php
class WidgetSrCloseResource extends CWidget 
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
    	$this->render('/widgets/srCloseResource', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
		$sr = array();	$month =  date('Y-m');	$close_status = SupportDesk::STATUS_CLOSED;
		$values = Yii::app()->db->createCommand("SELECT id,assigned_to,reopen FROM support_desk WHERE date like '$month%' AND status = '$close_status'")->queryAll();
		foreach ($values as $val)
	    {
	    	if (!isset($sr[$val['assigned_to']]))
	    		$sr[$val['assigned_to']] = 1;
	    	else 
	    		$sr[$val['assigned_to']] ++; 
	    }
	    $data_chart = array();   $sum = 0;  $i = 0;
	    foreach ($sr as $key=>$result)
	    {
			$reopen = Yii::app()->db->createCommand("SELECT sum(reopen) FROM support_desk sd WHERE sd.assigned_to = {$key} AND date like '$month%' ")->queryScalar();
			array_push($data_chart,array('label' => Users::getUsername($key).' ('.$reopen.' R)',
            	  'value' => $result));	    	
	    }	  
    	$chart = array( 'pieRadius'=>'100'); usort($data_chart, Widgets::build_sorter('value'));
    	echo json_encode(array(
            	'chart'=>$chart,
    			'data'=>$data_chart,	));    	
    }
}?>
