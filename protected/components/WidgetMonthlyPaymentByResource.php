<?php
class WidgetMonthlyPaymentByResource extends CWidget 
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
    	$this->render('/widgets/monthlypaymentbyresource', array(
            'dis'=>$this,
        ));
    } 
 	public function CharChart($year = null)
    {
  		for($i = 0;$i<3;$i++) {	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01'; $Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb;	}
		$dataset = array();	sort($months);
		foreach ($months as $k => $month)
		{
			$sr = array();	$data = array(); $data['month'] = date('M-Y', strtotime($month)); $lastday=date('Y-m-t', strtotime($month));
			$starter=date('Y-m-01', strtotime($month)); $bound=date('Y-m-01', strtotime('2016-10')); $sr=Receivables::gettotalNetResPerMonth(date('m', strtotime($month)), $lastday);
			if ($starter>=$bound)
			{
				foreach ($sr as $key=>$result)
				{ 
				    if($key == 0){ $data['Not Assigned'] = $result; }
				    else { $data[Users::getUsername($key)] = (double)round($result,2);	}
				}
			}else{
					foreach ($sr as $key=>$result)
				    { 
				    	if($key == 0){ $data['Not Assigned'] = 0; }
				    	else{ $data[Users::getUsername($key)] = 0;	}
				    }
				}		
				array_push($dataset,$data);
		}
		echo json_encode($dataset);
    }    
    public function getSeries(){
    	$x_axis = array(); 
    	$values = Yii::app()->db->createCommand("select distinct id_assigned from receivables
			where id_customer not in (239,323) and ((partner = 77 and status ='Paid') or (partner != 77 and partner_status ='Paid') and (Not ISNULL(id_assigned)))
			order by id_assigned ASC ")->queryAll();	
    	foreach ($values as $val)
		    {
		    	if($val['id_assigned'] != 0)
		     		array_push($x_axis,array('valueField'=>Users::getUsername($val['id_assigned']),'name'=>Users::getUsername($val['id_assigned'])));
		     	else
		     		array_push($x_axis,array('valueField'=>'Not Assigned','name'=>'Not Assigned'));
		    }
			echo json_encode($x_axis);
    }
} ?>
