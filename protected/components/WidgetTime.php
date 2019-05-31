<?php
class WidgetTime extends CWidget 
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
    	$this->render('/widgets/srTime', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	$sr = array();
		for ($i = 0;$i<6;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb; }
		sort($months); $time = "( Last 6 Months )";	$status_close  = SupportDesk::STATUS_CLOSED;	$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT id,date FROM support_desk WHERE date like '$month%' and (status = $status_close OR status=$status_confirm_closed) ")->queryAll();
			foreach($values as $val)
		    {
		    	$id = $val['id'];
		    	$end_date = Yii::app()->db->createCommand("SELECT date FROM support_desk_comments WHERE id_support_desk = {$id} ORDER BY date DESC LIMIT 1")->queryScalar();
		    	$seconds = strtotime($end_date) - strtotime($val['date']);	$hours   = floor(($seconds) / 3600);
		    	if ($hours < 8)
		    	{
		    		if (!isset($sr['less 8']))
						$sr['less 8'] = 1;
					else
						$sr['less 8'] ++;
		    	}
		    	else if($hours >= 8 && $hours < 16)
				{
		    		if (!isset($sr['8']))
						$sr['8'] = 1;
					else
						$sr['8'] ++;
		    	} 		    	
		    	else if($hours >= 16 && $hours < 24)
			    {
			    	if(!isset($sr['16']))
						$sr['16'] = 1;
					else
						$sr['16'] ++;
			    }
			    else if($hours >= 24 && $hours<48)
				{
				    if(!isset($sr['24']))
						$sr['24'] = 1;
					else
						$sr['24'] ++;
				    }
				   else if($hours >= 48 && $hours<72)
				    {
				    	if(!isset($sr['48']))
							$sr['48'] = 1;
						else
							$sr['48'] ++;
				    	}
				    else if($hours >= 72 && $hours<96)
				    {
				    	if(!isset($sr['72']))
							$sr['72'] = 1;
						else
							$sr['72'] ++;
				    	}
				    	else if($hours >= 96 && $hours<192)
				    	{
				    		if(!isset($sr['96']))
								$sr['96'] = 1;
							else
								$sr['96'] ++;
				    	}
				    	 	else if($hours>=192)
					    {	
					    	if(!isset($sr['192']))
								$sr['192'] = 1;
							else
								$sr['192'] ++;
					    }
		    }	
		}
		$data_chart = array();	ksort($sr);
		foreach ($sr as $key=>$result)
	    {
	    	 array_push($data_chart,array('category' => $result." ".WidgetTime::getLabel($key),
            	  'value' => $result));
	    }
	    usort($data_chart, Widgets::build_sorter('value'));    echo json_encode($data_chart);    	
    }   
    public static function getLabel($type)
    {
    	switch ($type)
    	{
    		case 'less 8':
    			return 'of SRs Closed < 8 hrs';
    			break;
    		case '8':
    			return 'of SRs closed  8 < x < 16 hrs';
    			break;
    		case '16':
    			return 'of SRs closed 16 < x < 24 hrs';
    			break;
    		case '24':
    			return 'of SRs closed 24 < x < 48 hrs';   		 			
    			break;
    		case '48':
    			return 'of SRs closed 48 < x < 72 hrs';
    			break;
    		case '72':
    			return 'of SRs closed 72 < x < 96 hrs';
    			break;
    		case '96':
    			return 'of SRs closed 96 < x < 192 hrs'; 
    			break;
    		case '192':
    			return 'of SRs closed x > 192 hrs'; 
    			break;
    	}
    } 
}?>
