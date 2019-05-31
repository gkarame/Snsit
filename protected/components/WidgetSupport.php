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
 	public function CharChart($year = null)
    {
		$sr = array();	$status_close = SupportDesk::STATUS_CLOSED;	$id_default_support = self::DEFAULT_SUPPORT;
		for($i = 0;$i<3;$i++) { $months[] = date('Y-m',strtotime('now - '.$i.' month'));}
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb; }
		$x_axis = array();	$ids_users = array(); sort($months);
		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT ut.amount,ut.id_user,ut.date,df.id_parent FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9")->queryAll();	
			foreach ($values as $val) {	array_push($ids_users,$val['id_user']); }
		}
   		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT ut.amount,ut.id_user,ut.date,df.id_parent FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9")->queryAll();	
			foreach ($values as $val)
		    {
		    	if (!isset($sr[$val['id_user']][$month]))
					$sr[$val['id_user']][$month] = $val['amount'];
				else
					$sr[$val['id_user']][$month]+= $val['amount'];
		    }
		    foreach ($ids_users as $id_user){
		    	if (!isset($sr[$id_user][$month]))
		    		$sr[$id_user][$month] = 0;
		    }
		    array_push($x_axis,array('label'=>$month =  date('M-Y', strtotime($month))));
		}
	    $dataset = array();    $category = array();
	    foreach ($sr as $key=>$result)
	    {
	    	$data = array();
	    	foreach ($result as $re)
	    		array_push($data,array('value'=>$re));
    		array_push($dataset, array('seriesname'=>Users::getUsername($key),'data'=>$data)); 
	    }
    	$chart = array( 
		          "xAxisName" => "Months",
		          "yAxisName" => "SRs",
		    );
    	array_push($category, array(
			'category'=>$x_axis)
			);
    	echo json_encode(array(
            	'chart'=>$chart,
    			'categories'=>$category,
    			'dataset'=>$dataset,
        	));
    }
	public function CharChart1($year = null)
    {
		$sr = array();	$status_close = SupportDesk::STATUS_CLOSED;	$id_default_support = self::DEFAULT_SUPPORT;
		for($i = 0;$i<3;$i++) {	$months[] = date('Y-m',strtotime('now - '.$i.' month'));}
		$x_axis = array();	$ids_users = array();	sort($months);
		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT ut.amount,ut.id_user,ut.date,df.id_parent FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
			foreach ($values as $val)  {	array_push($ids_users,$val['id_user']);  }
		}
		$dataset = array(); sort($months);
   		foreach ($months as $k => $month)
		{
			$sr = array();
			$values = Yii::app()->db->createCommand("SELECT ut.amount,ut.id_user,ut.date,df.id_parent FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
			foreach ($values as $val)
		    {
		    	if (!isset($sr[$val['id_user']]))
		    	{
		    		$total= self::GetTotalUserPermonth($val['id_user'], $month);
		    		if($total==0) { $total =1; }
					$sr[$val['id_user']] = round((($val['amount']/$total)*100),2);
				}else{
					$total= self::GetTotalUserPermonth($val['id_user'], $month);
					if($total==0) { $total =1; }
					$sr[$val['id_user']]+= round((($val['amount']/$total)*100),2);
				}
		    }
		    foreach ($ids_users as $id_user){
		    	if (!isset($sr[$id_user]))
		    		$sr[$id_user] = 0;
		    }
		    $data = array();    $data['month'] = date('M-Y', strtotime($month));
			foreach ($sr as $key=>$result)   { $data[Users::getUsername($key)] = $result;  }		  
		    array_push($dataset,$data);
		}
    	echo json_encode($dataset);
    }
    public function GetTotalUserPermonth($user, $month)
    {
    	return Yii::app()->db->createCommand("SELECT SUM(ut.amount) FROM user_time ut LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														WHERE ut.id_user=".$user."  AND (df.id_parent != 2 or df.id_parent is null ) AND ut.date like '$month%'   ")->queryScalar();	
    }
    public function getSeries(){
    	$id_default_support = self::DEFAULT_SUPPORT;	$x_axis = array(); 
    	$values = Yii::app()->db->createCommand("SELECT distinct(ut.id_user) FROM user_time ut 
													LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
													LEFT JOIN default_tasks df ON (df.id = ut.id_task)
													LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
													WHERE df.id_parent = $id_default_support and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
    	foreach ($values as $val)  {
		     	array_push($x_axis,array('valueField'=>Users::getUsername($val['id_user']),'name'=>Users::getUsername($val['id_user'])));
		}
		echo json_encode($x_axis);	
    }
} ?>
