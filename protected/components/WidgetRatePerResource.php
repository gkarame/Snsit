<?php
class WidgetRatePerResource extends CWidget 
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
    	$this->render('/widgets/ratePerResource', array(
            'dis'=>$this,
        ));
    }  
	public function CharChart1($year = null)
    {
		$sr = array();
		for($i = 0;$i<3;$i++) {	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {  $months[] = $Feb; }
		sort($months);	$dataset = array();	
		$users = Yii::app()->db->createCommand("SELECT id_user FROM `user_groups` WHERE id_group=9")->queryAll();   		
   		foreach ($months as $k => $month)
		{
			$data = array();  $data['month'] = date('M-Y', strtotime($month));	$sr = array();			
			foreach($users as $user)
			{
				$query="SELECT count(1)	FROM `support_desk`  s
				where  YEAR(s.rate_date)=YEAR(CURRENT_DATE()) and s.status=5 and s.rate is not null and s.rate not in (0,1,2,3) 
				and (SELECT id_user from support_desk_comments where status=3 and s.id=id_support_desk ORDER BY date DESC limit 1)='".$user['id_user']."'
				and  s.rate_date like '$month%'";
  				$value=Yii::app()->db->createCommand($query)->queryScalar(); 
				$sum = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` s where YEAR(s.rate_date)=YEAR(CURRENT_DATE()) AND rate_date like '$month%' and s.status=5 and s.rate is not null and (SELECT id_user from support_desk_comments where status=3 and s.id=id_support_desk ORDER BY date DESC limit 1)='".$user['id_user']."'")->queryScalar();       	
				if ($sum != 0)	{ $avg= ($value*100/$sum); if($avg!=0){	$avg=number_format($avg,2); $data[Users::getUsername($user['id_user'])] = $avg;	} }
        	}
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
    	foreach ($values as $val)
		{
		    array_push($x_axis,array('valueField'=>Users::getUsername($val['id_user']),'name'=>Users::getUsername($val['id_user'])));
		}
		echo json_encode($x_axis);	
    }
} ?>
