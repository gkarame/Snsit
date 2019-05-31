<?php
class WidgetCustomerProfile extends CWidget 
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
      $this->render('/widgets/customerSatisfaction', array(
            'dis'=>$this,
        ));
    }
    
   public static function CharChart($id)
    { 
      $values = array();  $axis = array();    $cname=   
      $query="SELECT count(1), MONTH(rate_date), SUM(rate), rate_date FROM `support_desk` where id_customer ='".$id."' and rate_date >= '2016-01-01 00:00:00' 
       and  `status`=5 and rate is not null and rate <>0 and rate_date>= (CURRENT_DATE() - interval 8 MONTH)
              GROUP BY YEAR(rate_date), MONTH(rate_date) ORDER BY rate_date";         
       $values=Yii::app($query)->db->createCommand($query)->queryAll(); $customer_name=Customers::getNameByID($id); $cname= "<span style='font-size:15px;'><b>".$customer_name."</b></span> \n ";
    
      foreach ($values as $key => $value) 
      {       
          $avg= ($value['SUM(rate)']/$value['count(1)']);
          $month= date("F",strtotime($value['rate_date']));        
          $axis[$key]['older1']= (double)round($avg,2);
          $axis[$key]['total1']= $month;   $axis[$key]['tag1']= $month ;
          $axis[$key]['tag1']= "<span style='font-size:15px;'><b>".$customer_name."</b></span> \n Rate:".(double)round($avg,2)." ";
         
      }
      echo json_encode(array('caxis'=>$axis, 'cname'=>$cname)); }
}?>
