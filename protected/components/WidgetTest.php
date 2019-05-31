<?php
class WidgetTest extends CWidget 
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
    	$this->render('/widgets/Test', array(
            'dis'=>$this,
    		'years'=>$years
        ));
    }
    public function CharChart($year = null)
    {
    	$date = array();	$month =  date('Y-m');
		for($i=11;$i>=0;$i--) {	$date[] = date('Y-m',strtotime($month . ' - '.$i.' month')); }
	    $year=(date('Y',strtotime('now')));  $jan=$year.'-01';   $Feb=$year.'-02';  $mar=$year.'-03';
	    if (in_array($jan, $date) && in_array($mar, $date) && !in_array($Feb, $date)) {   $date[] = $Feb;  }
 	    sort($months);	$data = date('Y',strtotime('now'));	$data_chart = self::getCustomer($data);
    	$chart = array( 
          	  	"palette" => "2",
		        "animation" => "1",
		        "yaxisname" => "Sales Achieved",
		        "showvalues" => "1",
		        "formatnumberscale" => "0",
		        "showpercentintooltip" => "0",
		        "showlabels" => "0",
		        "showlegend" => "1",
		    );
    	echo json_encode($data_chart);
    }	
    public static function getCustomer($date,$top = 5)
    {
    	$status = "(".Eas::STATUS_CANCELLED.",".Eas::STATUS_NEW.")";    	
    	$results = Yii::app()->db->createCommand("SELECT id,currency,id_customer FROM eas WHERE status NOT IN $status and approved like '$date%'")->queryAll();
   		$sum = array();	$data_chart= array();
   		foreach ($results as $result)
   		{
   			$eas = Eas::model()->findByPk((int)$result['id']);
   			if (!isset($sum[$result['id_customer']]))
    			$sum[$result['id_customer']]['val'] = $eas->getNetAmount(true);
    		else{
    			$val = $eas->getNetAmount(true);
    			$sum[$result['id_customer']]['val'] +=$val;
    		}
   		}
   		uasort($sum, Widgets::build_sorter('val'));
   		foreach ($sum as $key_sum=>$s)	{	array_push($sum[$key_sum],array('id'=>$key_sum)); }
   		foreach (array_slice($sum, 0, $top) as $cust)
   		{   			
   			array_push($data_chart,array('category' =>  Customers::getNameById($cust[0]['id']),
       		'value' => $cust['val']));
   		}
    	return $data_chart;
    }
}?>
