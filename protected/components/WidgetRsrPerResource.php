<?php
class WidgetRsrPerResource extends CWidget 
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
    	$this->render('/widgets/rsrPerResource', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart1($year = null)
    {
  		$data_chart=array();
		$results = Yii::app()->db->createCommand("SELECT SUM(t.avg) , t.id_user , count(t.id) as count , SUM(t.avg)/count(t.id) as response_time  from 
			(select s.id ,TIMESTAMPDIFF(MINUTE,s.adddate,s.closedate)/1440 as avg, s.assigned_to as id_user from rsr s, users u 
			where s.assigned_to=u.id and s.`status` =6 and (u.id in ( SELECT id_user FROM `user_groups` where id_group=9) 
			and u.id not in(31,20,9)) and s.closedate>=(CURRENT_DATE()- INTERVAL 6 MONTH) and s.adddate>=(CURRENT_DATE()- INTERVAL 6 MONTH) 
			GROUP BY s.id order by 2 desc ) t group by t.id_user")->queryAll();
	    foreach ($results as $result) 
	    {
	    	$cname=Users::getNameById($result['id_user']);	$avg= number_format((float)$result['response_time'], 2, '.', ''); 
	    	if ($cname !='' && $cname !='NULL' && $cname !=' ' && $avg >0){
				array_push($data_chart,array('label' => $cname, 'value' =>(float)$avg));
	    	}
	    }
	    usort($data_chart, Widgets::build_sorter('value'));
    	echo json_encode($data_chart);    	
    }
}?>
