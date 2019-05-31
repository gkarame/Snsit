<?php
class WidgetSrClose extends CWidget 
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
    	$this->render('/widgets/srClose', array(
            'dis'=>$this,
        ));
    }
	public function CharChart1($year = null)
    {
		$sum=0;
		if(Yii::app()->user->isAdmin){
			for($i = 0;$i<6;$i++){
				$months[] = date('Y-m',strtotime('now - '.$i.' month'));
			}
			$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
			if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb;	}
			sort($months);	$time = "( Last 6 Months )";	$status_closed = 3;	$status_confirm_closed = 5;
			$values = array();	$sr = array();	$dataset = array();	sort($months);
			foreach($months as $month)
			{
				$sr['PS'][$month] = 0;	$sr['CS'][$month] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,assigned_to FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryAll();
				foreach($values as $val)
				{	
					$id = $val['id'];
					if (Users::getUnit($val['assigned_to']) == 'Tech - CS'){	$sr['CS'][$month]++;	}
					else if (Users::getUnit($val['assigned_to'])== 'Tech - PS') {	$sr['PS'][$month]++;	}
				}
				if($sr != null)
			    {
			    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)),'ps'=>$sr['PS'][$month],'cs'=>$sr['CS'][$month], 'value' =>($sr['PS'][$month]+$sr['CS'][$month]))); 
			    }
			}
		}else{
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
			for($i = 0;$i<3;$i++){ $months[] = date('Y-m',strtotime('now - '.$i.' month')); }
			$time = "( Last 3 Months )"; $status_closed = 3; $status_confirm_closed = 5;	$values = array();
			$sr = array();	$dataset = array();	sort($months);
			foreach($months as $month)
			{
				$sr['PS'][$month] = 0; $sr['CS'][$month] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,assigned_to FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryAll();
				foreach($values as $val)
				{	
					$id = $val['id'];
					if (Users::getUnit($val['assigned_to']) == 'Tech - CS')	{ $sr['CS'][$month]++;
						}else if (Users::getUnit($val['assigned_to'])== 'Tech - PS') { $sr['PS'][$month]++; }
				}
				if($sr != null)
			    {
			    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)),'ps'=>$sr['PS'][$month],'cs'=>$sr['CS'][$month], 'value' =>($sr['PS'][$month]+$sr['CS'][$month]))); 
			   }
			}
		}
    	echo json_encode($dataset);    	
    }
}?>
