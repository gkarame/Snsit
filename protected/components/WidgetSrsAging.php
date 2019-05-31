<?php
class WidgetSrsAging extends CWidget 
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
    	$years = Widgets::getLast3Years();
    	$this->render('/widgets/srsAging', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart1($year = null)
    {
      $values = array();  $results = array();  $data_chart = array();
      switch ($year) {
      	case '1':
      		$date = date('Y-m-d',strtotime('now - 1 year'));
      		break;
      	case '2':
      		$onedate = date('Y-m-d',strtotime('now - 1 year'));
          $date = date('Y-m-d',strtotime(date("Y-m-d", strtotime($onedate)) . " - 6 months "));
      		break;
      	case '3':
      		$date = date('Y-m-d',strtotime('now - 2 year'));
      		break;
		case '4':
      		$date = date('Y-m-d',strtotime('now - 1 month'));
      		break;
		case '5':
      		$date = date('Y-m-d',strtotime('now - 2 months'));
      		break;
		case '6':
      		$date = date('Y-m-d',strtotime('now - 3 months'));
      		break;
 		case '7':
      		$date = date('Y-m-d',strtotime('now - 4 months'));
      		break;    
 		case '8':
      		$date = date('Y-m-d',strtotime('now - 5 months'));
      		break;  		
      	default:
      		$date = date('Y-m-d',strtotime('now - 6 months'));
      		break;
      }
    $months = array(0,7,15,30,60,90);
for($i = 0;$i<6;$i++){
  $j = $i+1;
  if($i == 0){
	 $values = Yii::app()->db->createCommand("select count(1) as total from support_desk sd
        where  sd.status not in (3,5)  and DATEDIFF(NOW(),sd.date) >= $months[$i] and DATEDIFF(NOW(),sd.date) <= $months[$j]
		")->queryAll(); 
  } else if($i < 5 && $i != 0){
	 $values = Yii::app()->db->createCommand("select count(1) as total from support_desk sd
        where sd.status not in (3,5)  and DATEDIFF(NOW(),sd.date) > $months[$i] and DATEDIFF(NOW(),sd.date) <= $months[$j]
		")->queryAll(); 
  } else { 
 $values = Yii::app()->db->createCommand("select count(1) as total from support_desk sd
        where sd.status not in (3,5)  and  DATEDIFF(NOW(),sd.date) >$months[$i] ")->queryAll();
}
	foreach ($values as $key => $value) {
      	 if ($i == 0 ){
        $key = '1-'.$months[$j];
    }else  if ($i < 5){   $key = $months[$i].'-'.$months[$j];	}else{  $key = '>'.$months[$i];	}      	
        if(!isset($results[$key])){  $results[$key]['Aging'] = $key;  $results[$key]['total'] = $value['total']; }             
        }
      }
	foreach ($results as $res) {
          array_push($data_chart,array('label' => $res['Aging'],
                    'value' => (double)round( $res['total'],2)));
        }
	echo json_encode($data_chart);
    }
}?>


