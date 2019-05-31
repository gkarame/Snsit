<?php
class WidgetSupport extends CWidget 
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
    	$this->render('/widgets/srSupport', array(
            'dis'=>$this,
        ));
    }
	public function CharChart1($year = null)
    {
		$sr = array();	$status_close = SupportDesk::STATUS_CLOSED;	$id_default_support = self::DEFAULT_SUPPORT;
		for($i = 0;$i<3;$i++) {	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$x_axis = array(); $year=(date('Y',strtotime('now'))); $jan=$year.'-01'; $Feb=$year.'-02'; $mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb;}
		$dataset = array(); sort($months);
   		foreach ($months as $k => $month)
		{
			$sr = array();  $data = array();
			$values = Yii::app()->db->createCommand("SELECT assigned_to, rate from support_desk WHERE `status`=5 and rate is not null and rate not in (0,1,2) and MONTH(rate_date) like '$month%' ORDER BY  assigned_to ASC")->queryAll();	
			foreach ($values as $val)
		    {
		    	if (!isset($sr[$val['assigned_to']]))
					$sr[$val['assigned_to']] = $val['rate'];
				else
					$sr[$val['assigned_to']]+= $val['rate'];
		    }  
			$data['month'] = date('M-Y', strtotime($month));
			foreach ($sr as $key=>$result)  { $data[Users::getUsername($key)] = $result;  }		  
		    array_push($dataset,$data);
		}
		echo json_encode($dataset);
    }
    public function getSeries(){
    	$id_default_support = self::DEFAULT_SUPPORT;	$x_axis = array(); 
    	$values = Yii::app()->db->createCommand("SELECT distinct(ut.id_user) FROM user_time ut 
													LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
													LEFT JOIN default_tasks df ON (df.id = ut.id_task)
													LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
													WHERE df.id_parent = $id_default_support and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
    	foreach ($values as $val){
		     	array_push($x_axis,array('valueField'=>Users::getUsername($val['id_user']),'name'=>Users::getUsername($val['id_user'])));
		}
		echo json_encode($x_axis);	
    }
} ?>
