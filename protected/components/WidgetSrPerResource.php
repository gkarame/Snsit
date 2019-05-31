<?php
class WidgetSrPerResource extends CWidget 
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
    	$this->render('/widgets/srPerResource', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart1($year = null)
    {
  		$data_chart=array();
		$results = Yii::app()->db->createCommand("SELECT SUM(t.avg) , t.id_user , count(t.id) as count , SUM(t.avg)/count(t.id) as response_time  from (select s.id ,TIMESTAMPDIFF(MINUTE,s.date,sdc.date)/1440 as avg, sdc.id_user from support_desk s  , support_desk_comments sdc , users u 
			where sdc.id_user=u.id and s.id =sdc.id_support_desk and s.date<sdc.date and s.date <> '0000-00-00 00:00:00' and sender='SNS' and s.`status` in ('3','5') and s.id_customer in (select id from customers where status=1) and (u.id in ( SELECT id_user FROM `user_groups` where id_group=9) and u.id not in(31,20,9))
			and sdc.date>=(CURRENT_DATE()- INTERVAL 6 MONTH) and s.date>=(CURRENT_DATE()- INTERVAL 6 MONTH) 
			and not exists ( select 1  from support_desk_comments sd where sd.sender='SNS' and sd.id_user<>sdc.id_user and sd.date<sdc.date and sdc.id_support_desk=sd.id_support_desk )
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
