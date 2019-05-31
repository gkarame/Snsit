<?php
class WidgetBillability extends CWidget 
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
    	$this->render('/widgets/billability', array(
            'dis'=>$this,
        ));
    } 	 
 	public function CharChart($year = null)
    {
    		
    		 $axis = array();
	$current_date = date('Y-m-d',strtotime('now'));	
			$sr = array();
			$data_chart = array();			
			$tech=" ( "; 
			$techs=UserPersonalDetails::getTechAll();				
			foreach($techs as $t){
				$tech.=" '".$t['id']."',";
			}
			$tech.=" 0 ) ";			$ops=" ( "; $op=UserPersonalDetails::getOpsAll();			
			foreach($op as $o){
				$ops.=" '".$o['id']."',";
			}
			$ops.=" 0 ) ";			
		for ($i = 0;$i<12;$i++)
			{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
			}
		$year=(date('Y',strtotime('now')));	$jan=$year.'-01';	$Feb=$year.'-02';	$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);	foreach ($months as $month)	{
		$value = Yii::app()->db->createCommand("

select  IFNULL(TRUNCATE(bill/tot,2),0) as perc  from 
(select sum(r.amount)*100 as bill from (
select  sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and uts.id_task=t.id and uts.`default`=0  and t.billable='Yes' and uts.date like '$month%' 
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and uts.id_task=dt.id and uts.`default`=1 and dt.billable='Yes' and uts.date like '$month%'   
) as r) as billable 
, (select sum(r.amount) as tot from (
select sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and uts.id_task=t.id and uts.`default`=0 and t.billable in ('Yes','No') and uts.date like '$month%'   
union all
select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and uts.date like '$month%'   
union all
select  sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and uts.id_task=dt.id and uts.`default`=1 and  dt.billable in ('Yes','No') and uts.date like '$month%'  
) as r ) as total ")->queryScalar();	
					$sr['billability']= (double)$value ;
						$sr['month'] = date('M-y', strtotime($month));
						$sr['lookupdate'] = date('Y-m', strtotime($month));  					
			 array_push($data_chart,$sr);
			 }   echo json_encode($data_chart);    }    
} ?>
