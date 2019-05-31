<?php
class WidgetResourceNonBillable extends CWidget 
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
    	$this->render('/widgets/resourceNonBillable', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart($year = null)
    {
    	Yii::app()->session['monthnobill']=''; Yii::app()->session['topnobill']='';	$month= date('Y-m-01',strtotime('now - 6 month'));
		$current_date = date('Y-m-d',strtotime('now'));	$values = array();	$sr = array();		
			$values = Yii::app()->db->createCommand(" select t1.id_user , TRUNCATE(((nonbillable*100)/total) ,2) as nbperc from 
( select r.id_user , sum(r.amount) as nonbillable from (
select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='No' and uts.date between '$month' and '$current_date' GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and dti.billable='No' and uts.date between '$month' and '$current_date' GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='No' and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 
) as r  GROUP BY r.id_user ) t1 ,
(
select r.id_user , sum(r.amount) as total from (
select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable in ('No','Yes') and uts.status='1' and uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('No','Yes') and uts.status='1' and uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable in ('No','Yes') and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 
) as r  GROUP BY r.id_user )t2 where t1.id_user = t2.id_user order by nbperc desc limit 5")->queryAll();
		foreach ($values as $val){	$sr[$val['id_user']]= (int)$val['nbperc'] ; }
			$data_chart = array();	asort($sr);	
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Users::getNameByid($key),
            	  'value' => $results));
			}
    		echo json_encode($data_chart);
    }
}?>
