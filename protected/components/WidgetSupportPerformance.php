<?php
class WidgetSupportPerformance extends CWidget 
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
      $this->render('/widgets/supportPerformance', array(
            'dis'=>$this,
        ));
    }
	public function CharChart()
    { 
      $values = array();  $axis = array();
      if (isset($_POST['type'])) {  Yii::app()->session['type'] = $_POST['type'];  }      
      if (isset($_POST['status']))  {  Yii::app()->session['status'] = $_POST['status'];  }
	    $query="SELECT count(1), MONTH(rate_date), rate_date FROM `support_desk` where (( rate_date>= (CURRENT_DATE() - interval 11 MONTH)) or (MONTH( rate_date)= MONTH((CURRENT_DATE() - interval 11 MONTH)) AND YEAR( rate_date)= YEAR((CURRENT_DATE() - interval 11 MONTH)))) and `status`=5 and rate is not null and rate not in (0,1,2,3) 
	            GROUP BY MONTH(rate_date) ORDER BY rate_date";  
	    $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
	    foreach ($values as $key => $value) 
	    {
	        $sum = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where YEAR(rate_date)=YEAR('".$value['rate_date']."') AND MONTH(rate_date) = MONTH('".$value['rate_date']."') and `status`=5 and rate is not null")->queryScalar();
	        if ($sum==0)  {  $avg=0; }else  { $avg= ($value['count(1)']*100/$sum);  }
	        $month= date("M",strtotime($value['rate_date']));
	        $axis[$key]['older1']= (double)round($avg,2);  $axis[$key]['total1']= $month;
	        $axis[$key]['tag1']= $month ;  $axis[$key]['tag1']= "<span style='font-size:15px;'><b>".$month."</b></span> \n Rate:".(double)round($avg,2)."%";
		}
		echo json_encode($axis);
	}
} ?>
