<?php
class WidgetSrSubmittedResolved extends CWidget 
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
    	$this->render('/widgets/srSubmittedResolved', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	$id_user=Yii::app()->user->id; $sr = array();
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		for($i = 0;$i<3;$i++)  {	$months[] = date('Y-m',strtotime('now - '.$i.' month')); }
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {   $months[] = $Feb;}
		sort($months);	$x_axis = array();	$ids_users = array();
		foreach ($months as $month){	
			$submitted="Submitted";	$resolved="Resolved";	array_push($ids_users,$submitted); array_push($ids_users,$resolved); }
   		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where id_customer=".$id_customer." and date like '$month%'")->queryScalar();
			$num_closed=0;
		    if (!isset($sr[$submitted][$month])){ $sr[$submitted][$month] = $values; }
			else{ $sr[$submitted][$month]+= $values; }
			$values_closed =Yii::app()->db->createCommand("select sc.id_support_desk, max(sc.date) from support_desk sd, support_desk_comments sc where sc.id_support_desk=sd.id and (sc.`status`=3 or sc.`status`=5) and id_customer=".$id_customer." and sc.is_admin=1  
				group by sc.id_support_desk having max(sc.date) Like '$month%'")->queryAll();
		  	foreach ($values_closed as $val_closed) {	$num_closed++;	}
		  	if (!isset($sr[$resolved][$month])){ $sr[$resolved][$month] = $num_closed; }
			else{ $sr[$resolved][$month]+= $num_closed; }
		    foreach ($ids_users as $id_user){
		    	if (!isset($sr[$id_user][$month]))
		    		$sr[$id_user][$month] = 0;
		    }
		    array_push($x_axis,array('label'=>$month =  date('M-Y', strtotime($month))));
		}
	    $dataset = array();  $category = array();
	    foreach ($sr as $key=>$result)
	    {
	    	$data = array();
	    	foreach ($result as $re)
	    		array_push($data,array('value'=>$re));
    		array_push($dataset, array('seriesname'=>$key,'data'=>$data)); 
	    }
    	$chart = array( 
		          "xAxisName" => "Months",
		          "yAxisName" => "SRs",  );
    	array_push($category, array(
			'category'=>$x_axis)	);
    	echo json_encode(array(
            	'chart'=>$chart,
    			'categories'=>$category,
    			'dataset'=>$dataset,
        	));
    }
	public function CharChart1($year = null)
    {
    	$id_user=Yii::app()->user->id;
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		$sr = array();	$submitted="Submitted";	$resolved="Resolved";	$status_close = SupportDesk::STATUS_CLOSED;	$id_default_support = self::DEFAULT_SUPPORT;
		for($i = 0;$i<3;$i++)  {	$months[] = date('Y-m',strtotime('now - '.$i.' month'));}
		$x_axis = array(); $ids_users = array();
		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("select sdc1.date, sdc1.status ,sdc1.id_support_desk from support_desk sd, support_desk_comments as sdc1 where sdc1.id_support_desk=sd.id and  sd.id_customer=".$id_customer." and sdc1.id = (select max(sdc2.id) from support_desk_comments as sdc2 where sdc1.id_support_desk=sdc2.id_support_desk and sdc2.date) and sdc1.date like '$month%'")->queryAll();
			foreach ($values as $val) {	array_push($ids_users,$submitted);	array_push($ids_users,$resolved); }
		}
		$dataset = array();
   		foreach ($months as $k => $month)
		{
			$sr = array();
			$values = Yii::app()->db->createCommand("select sdc1.date, sdc1.status ,sdc1.id_support_desk from support_desk sd, support_desk_comments as sdc1 where sdc1.id_support_desk=sd.id and  sd.id_customer=".$id_customer." and sdc1.id = (select max(sdc2.id) from support_desk_comments as sdc2 where sdc1.id_support_desk=sdc2.id_support_desk and sdc2.date) and sdc1.date like '$month%'")->queryAll();
			foreach($values as $val) {				
		    	if (!isset($sr[$submitted]))
					$sr[$submitted]++;
				else
					$sr[$submitted]++;

				if($val['status']==3 || $val['status']==5){
					if(!isset($sr[$resolved]))
						$sr[$resolved]++;
					else
						$sr[$resolved]++;
				}
			}
		    foreach ($ids_users as $id_user){
		    	if (!isset($sr[$id_user]))
		    		$sr[$id_user] = 0;
		    }
		    $data = array();   $data['month'] = date('M-Y', strtotime($month));
			foreach ($sr as $key=>$result){ $data[$key] = $result; }
		    array_push($dataset,$data);
		}
    	echo json_encode($dataset);
    }
    public function getSeries(){
    	$id_default_support = self::DEFAULT_SUPPORT; $x_axis = array(); 
		$values = Yii::app()->db->createCommand("select sdc1.date, sdc1.status ,sdc1.id_support_desk from support_desk sd, support_desk_comments as sdc1 where sdc1.id_support_desk=sd.id and  sd.id_customer=".$id_customer." and sdc1.id = (select max(sdc2.id) from support_desk_comments as sdc2 where sdc1.id_support_desk=sdc2.id_support_desk and sdc2.date) and sdc1.date like '$month%'")->queryAll();
		foreach ($values as $val) { $submitted="Submitted";	$resolved="Resolved";	array_push($ids_users,$submitted);	array_push($ids_users,$resolved); }
    	foreach ($values as $key=>$val) {	array_push($x_axis,array('valueField'=>$key,'name'=>$key));   }
    	echo json_encode($x_axis);	
    }
} ?>
