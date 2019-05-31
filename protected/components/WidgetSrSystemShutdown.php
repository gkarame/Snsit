<?php
class WidgetSrSystemShutdown extends CWidget 
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
    	$this->render('/widgets/srSystemShutdown', array(
            'dis'=>$this,
        ));
    }
	public function CharChart1($year = null)
    {
		$sum=0;
		if(Yii::app()->user->isAdmin){
			for($i = 0;$i<6;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
			$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
			if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb; } rsort($months); $dataset = array();
			$time = "( Last 6 Months )";	$status_closed = SupportDesk::STATUS_CLOSED;	$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			foreach($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and system_down='Yes' and date like '$month%' ")->queryScalar();
				$escalate = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and escalate='Yes'  and date like '$month%' ")->queryScalar();
				if($values != null &&  $escalate !=null) {	array_push($dataset, array('state'=>date('M-Y', strtotime($month)), 'value' =>(int)$values , 'escalate'=>(int)$escalate)); }
			}
		}else{
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
			for($i = 0;$i<3;$i++){	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
			$time = "( Last 6 Months )";	$status_closed = SupportDesk::STATUS_CLOSED; $status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$dataset = array();
			foreach($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and system_down='Yes' and id_customer=".$id_customer." and date like '$month%' ")->queryScalar();
				$escalate = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and escalate='Yes' and id_customer=".$id_customer." and date like '$month%' ")->queryScalar();
				if($values != null &&  $escalate !=null)
		    	{
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)), 'value' =>(int)$values , 'escalate'=>(int)$escalate)); 
		    	}
			}
		}
		$dataset=array_reverse($dataset);	echo json_encode($dataset);    	
    }
}?>
