<?php
class WidgetReasons extends CWidget 
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
      $this->render('/widgets/unsatisfied', array(
            'dis'=>$this,
        ));
    }    
   public static function CharChart($CustName, $perc, $time, $user = null)
    {
    	if($user !=1 && $user!= null){
    		$id=Users::getIdByName($user);	$w=" assigned_to=".$id." and "; 
    	}else{ $w=''; }
        $id= Customers::getIdByName($CustName);		
		if (!empty($time)){
			$where=' rate_date>=(CURRENT_DATE()- INTERVAL 6 MONTH) ';
			switch ($time){
				case '1':
					$where=' YEAR(rate_date)=YEAR(CURRENT_DATE()) and MONTH(rate_date)=MONTH(CURRENT_DATE()) ';
					$time='1';
					break;
				case '3':
					$where=' rate_date>=(CURRENT_DATE()- INTERVAL 3 MONTH) ';
					break;
				case '6':
					$where=' rate_date>=(CURRENT_DATE()- INTERVAL 6 MONTH) ';
					break;
				case '10':
					$where=' YEAR(rate_date)=(YEAR(CURRENT_DATE())-1) ';
					break;
				case '12':
					$where='  rate_date>=(CURRENT_DATE()- INTERVAL 12 MONTH) ';
					break;
			}
			$values=Yii::app()->db->createCommand("SELECT COUNT(*) as cnt, reason FROM `support_desk` where ".$w." id_customer= ".$id." and ".$where."  group by reason order by count(*)  DESC")->queryAll();
		}else{
			$values=Yii::app()->db->createCommand('SELECT COUNT(*) as cnt, reason FROM `support_desk` where '.$w.' id_customer= '.$id.'  group by reason order by count(*)  DESC')->queryAll();
		}       
      foreach ($values as $key => $value) 
      {        
          $issues= $value['cnt'];
          if (!empty($value['reason'])) {  $TaskID= Codelkups::getCodelkup($value['reason']);  }
          else {   $TaskID='N/A';  }        
          $axis[$key]['older1']= (double)round($issues,2);  $axis[$key]['total1']= $TaskID;  $axis[$key]['tag1']= $TaskID;
          $axis[$key]['tag1']= $TaskID."#: ".(double)round($issues,2)." ";
          $cname= "<span style='font-size:15px;'><b>".$CustName." - ".$perc." </b></span> \n ";
      }
      $axis=array_reverse($axis);  echo json_encode(array('caxis'=>$axis, 'cname'=>$cname));
    }
}?>
