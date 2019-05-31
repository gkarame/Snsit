<?php
class WidgetMonthBillability extends CWidget 
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
      $this->render('/widgets/billability', array(
            'dis'=>$this,
        ));
    }    
   public static function CharChart($id, $resc)
    {
      $values = array();      $axis = array();      
      switch ($resc){
				case '1':
				 $tech=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getTechAll() as $t){
					$tech.=" '".$t['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;
				break;
				case '2':
				$ops=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getOpsAll() as $o){
					$ops.=" '".$o['id']."',";
				}
				$ops.=" 0 ) ";
				$resources=$ops;
				break;
				case '3':
				$resources=' ';
				break;
				case '4':
				 $techPS=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getPSAll() as $t){
					$techPS.=" '".$t['id']."',";
				}
				$techPS.=" 0 ) ";
				$resources=$techPS;
				break;
				case '5':
				 $techCS=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getCSAll() as $t){
					$techCS.=" '".$t['id']."',";
				}
				$techCS.=" 0 ) ";
				$resources=$techCS;
				break;
				case '6':
				 $coretech=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getCoreTechAll() as $t){
					$coretech.=" '".$t['id']."',";
				}
				$coretech.=" 0 ) ";
				$resources=$coretech;
				break;
   			}
      $query="(select ID,(r.amount) as bill from (
select  t.description as ID,sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and uts.id_task=t.id and uts.`default`=0  and t.billable='No' ".$resources." and uts.date like '$id%' 
group by ID 
union all
select  concat(i.name,' - task: ',dti.description) as ID,sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti, internal i where u.id=uts.id_user and dti.id_internal=i.id and uts.id_task=dti.id and uts.`default`=3  and dti.billable='No' ".$resources." and uts.date like '$id%' 
group by ID 
union all
select  dt.`name` as ID,sum(uts.amount) as amount from users u , user_time uts , default_tasks dt where dt.id_parent!=828 and u.id=uts.id_user  and uts.id_task=dt.id and uts.`default`=1 and dt.billable='No' ".$resources." and uts.date like '$id%'   
group by ID) as r) order by bill Desc limit 9";    
		$ca=Yii::app($query)->db->createCommand("select  sum(uts.amount) as amount from users u , user_time uts , default_tasks dt where dt.id_parent=828 and u.id=uts.id_user  and uts.id_task=dt.id and uts.`default`=1 and dt.billable='No' ".$resources." and uts.date like '$id%'  ")->queryScalar();    
        $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
        $b=true;
        $key=0;
	    foreach ($values as $value) 
	    { 
	    	if($ca >= $value['bill'] && $b == true)  
	    	{
	    		$b=false;
	    		$TaskBillability= $ca;  $TaskID = 'CA'; $axis[$key]['older1']= (double)round($TaskBillability,2);
		        $axis[$key]['total1']= $TaskID; $axis[$key]['tag1']= $TaskID;
		        $axis[$key]['tag1']= "<span style='font-size:15px;'><b>".$TaskID."</b></span> \n Hours:".(double)round($TaskBillability,2)." ";
		        $cname= "<span style='font-size:15px;'><b>".date('M-y', strtotime($id))." Top 10 Not Billable"."</b></span> \n ";
	    		$key++;
	    	}    
	        $TaskBillability= $value['bill'];  $TaskID = $value['ID']; $axis[$key]['older1']= (double)round($TaskBillability,2);
	        $axis[$key]['total1']= $TaskID; $axis[$key]['tag1']= $TaskID;
	        $axis[$key]['tag1']= "<span style='font-size:15px;'><b>".$TaskID."</b></span> \n Hours:".(double)round($TaskBillability,2)." ";
	        $cname= "<span style='font-size:15px;'><b>".date('M-y', strtotime($id))." Top 10 Not Billable"."</b></span> \n ";
	   		$key++;
	   }
    	$axis=array_reverse($axis); echo json_encode(array('caxis'=>$axis, 'cname'=>$cname)); 
    }
} ?>
