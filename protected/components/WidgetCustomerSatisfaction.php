<?php
class WidgetCustomerSatisfaction extends CWidget 
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
   public function CharChart()
    { 
      $values = array();     $axis = array();      
      $query="SELECT DISTINCT(id_customer) FROM `support_desk` where `status`=5 and rate is not null and rate<>0";         
      $values=Yii::app($query)->db->createCommand($query)->queryAll();       $avg_rate=0;        $customer_issues=0;        $customer_rate=0;
      foreach ($values as $key => $value) 
      {
          $customer_name=Customers::getNameByID($value['id_customer']);
          $customer_data = Yii::app()->db->createCommand("SELECT count(1) as tot, sum(rate) as rate FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryAll();
          $customer_issues = array_sum(array_column($customer_data, 'tot'));
          if ($customer_issues != 0)
          {
            $customer_rate= array_sum(array_column($customer_data, 'rate'));
            $avg_rate=($customer_rate/$customer_issues);
            $axis[$key]['older1']= (double)round($avg_rate,2);
            $axis[$key]['total1']= (double)$customer_issues;
            $axis[$key]['tag1']= $value['id_customer']."- <span style='font-size:15px;'><b>".$customer_name."</b></span> \n Rate:".(double)round($avg_rate,2)." \n Number of issues:".(double)round($customer_issues,2)." " ;
          }
      }
      usort($axis, Widgets::build_sorter('total1'));      $axis=array_reverse($axis);      echo json_encode($axis);    }
} ?>
