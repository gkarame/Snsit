<?php
class WidgetProjectRunOut extends CWidget 
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
    	$this->render('/widgets/projectRunOut', array(
            'dis'=>$this,
        ));
    }    
 	public static function Value()
 	{
 		$criteria=new CDbCriteria;	$criteria->condition = 'customer_id = 13';	$criteria->limit = '5';
		return new CActiveDataProvider('projects', array(
			'criteria' => $criteria,
			'pagination'=>false,
             'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),	));
 	}
 	public static function getProjects()
 	{
 		$results=Yii::app()->db->createCommand("SELECT id, name, customer_id  FROM projects where status=1")->queryAll(); $projects=array();
 		foreach ($results as $result) 
 		{
 			$id=$result['id']; $customer_id=$result['customer_id'];	$project=$result['name'];
			$customer_name=Customers::getNameByID($result['customer_id']);	$budget= Projects::getBudgetedMD($result['id']);
			$total_incloff= Projects::getIncludingOffsetMD($result['id']);
			if ($budget < $total_incloff)
			{
				$str=' ';	$actuals=Projects::getActualMD($result['id']);	$offset= Projects::getTotalOffsetMD($result['id']);
				$offset_req= Projects::getTotalOffset($result['id']);	$reasons= Projects::getreasons($result['id']);
				$currency= Projects::getCurrency($result['id']);
				if($budget >0){ $potential= ($budget-$total_incloff)/$budget;}
				else{	$potential=0;}
				if( empty( $reasons ) ){ $str.='Not Available';	}
				else{
					foreach($reasons as $reason)
					{
						$str.= "- ".$reason['OFFSET_reason']."<br/>";
					}					
				}
				array_push($projects,array('id'=>$id,'customer_id'=>$customer_id, 'customer' => $customer_name,'project'=>$project, 'budget'=> $budget,'actuals'=> $actuals,'includingoffset'=>$total_incloff,
 					'offset'=> $offset,'currency'=> $currency , 'requests'=> $offset_req,'reasons' =>$str,'potential'=>$potential));
			}
 		}
   		return $projects;
 	}
}?>
