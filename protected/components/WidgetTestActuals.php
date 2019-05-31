<?php
class WidgetTestActuals extends CWidget 
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
    $this->render('/widgets/TestActuals', array(
          'dis'=>$this,
       ));
  }
  public function CharChart()
  { 
	$values = array(); $axis = array();
    if (isset($_POST['type'])) {  Yii::app()->session['type'] = $_POST['type']; }
    $select = " SELECT p.id , p.status from projects p , eas e ";
    $where ="WHERE (p.id=e.id_project or p.id=e.id_parent_project) and (e.TM<>'1' or e.TM is null) AND p.id_type in ('26' , '27') and e.old='No' and e.status>=2";
    $orderby="order by p.id";
    if(isset(Yii::app()->session['status']) && Yii::app()->session['status']!="" && Yii::app()->session['status']!=" " && Yii::app()->session['status']!='100'){
        $where=$where." AND p.status=".Yii::app()->session['status']." ";            
    }else{
        Yii::app()->session['status']="" ;  $where=$where." AND (p.status=0 or p.status=1 or p.status=2) ";
    }  
    if(isset(Yii::app()->session['type']) && Yii::app()->session['type']!='100' && Yii::app()->session['type']!="" && Yii::app()->session['type']!=" "){
        $where=$where." AND p.id_type=".Yii::app()->session['type']." ";          
    }
	$query=$select." ".$where." ".$orderby ;  $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
    foreach ($values as $key => $value)
    {       
        if(Projects::getBudgetedMD($value['id'])<='1'){ $BudgetedMDs='1'; }else{  $BudgetedMDs=Projects::getBudgetedMD($value['id']); };
        $ActualMDs= Projects::getActualMDPerParent($value['id']);
        if($ActualMDs <='1'){ $ActualMDsRound='1'; } else{  $ActualMDsRound= $ActualMDs ;}
        $TotNetAamountUSD=Projects::getNetAmount($value['id']);   $RemainingMDS=$BudgetedMDs - $ActualMDs;  $ActualRate=$TotNetAamountUSD/$ActualMDsRound;
		$axis[$key]['tag1']= "<span style='font-size:15px;'><b>".Projects::getNameById($value['id'])."</b></span> \n Budgeted Amnt:".Utils::formatNumber($TotNetAamountUSD,2)."$ \n Budgeted MDs: ".Utils::formatNumber($BudgetedMDs)." \n Actual MDs: ".Utils::formatNumber($ActualMDs)." \n Remaining MDs: ".Utils::formatNumber($RemainingMDS)." \n  Budgeted Rate: ".Utils::formatNumber($TotNetAamountUSD/$BudgetedMDs,2)." $ \n  Actual Rate: ".Utils::formatNumber($ActualRate,2)." $";
        $axis[$key]['older1']= (double)round($ActualRate,2); $axis[$key]['perc1']= (int)$value['status']; $axis[$key]['total1']= (double)round($TotNetAamountUSD,2);
    }
    echo json_encode($axis);
  }
} ?>
