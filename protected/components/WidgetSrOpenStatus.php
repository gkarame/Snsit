<?php
class WidgetSrOpenStatus extends CWidget 
{
	public $widget;	
	public function getId($autoGenerate=false) {
		$model = __CLASS__;
		if(!Yii::app()->user->isAdmin){
		return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model' order by id_dashboard desc limit 1")->queryScalar();
	}else{ return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model' order by id_dashboard asc limit 1")->queryScalar();
	} }		
	public static function getName(){
		$model = __CLASS__;
		return Yii::app()->db->createCommand("SELECT distinct name FROM widgets WHERE model = '$model'")->queryScalar();
	}	
    public function run()
    {
    	$this->render('/widgets/srOpenStatus', array(
            'dis'=>$this,
        ));
    }    
 	public function CharChart()
    {	
    	$id_user=Yii::app()->user->id;
    	if(!Yii::app()->user->isAdmin){
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		}
    	$values = array(); $sr = array();
		if(!Yii::app()->user->isAdmin){	
			$values = Yii::app()->db->createCommand("select status from support_desk WHERE   id_customer=".$id_customer." and (status !=3 and status!=5)")->queryAll();
		}else{ $values = Yii::app()->db->createCommand("select status from support_desk WHERE  (status !=3 and status!=5)")->queryAll();}
		foreach ($values as $val)
		{
			$stat="";	
			if (!isset($sr[$val['status']]['val']))
				$sr[$val['status']]['val'] = 1;
			else
				$sr[$val['status']]['val'] ++;
			switch ($val['status']) {
				case '0':
					$stat="New";
					break;
				case '1':
					$stat="In Progress";
					break;
				case '2':
					$stat="Awaiting Customer";							
					break;
				case '4':
					$stat="Reopened";							
					break;						
			}				
			$sr[$val['status']]['cust'] = $stat; 
		}
		uasort($sr, Widgets::build_sorter('val'));	$data_chart = array();
		foreach(array_slice($sr, 0, 10) as $results)
		{
	    	array_push($data_chart,array('category' => $results['cust']."(".$results['val'].")",
           	  'value' => $results['val']));
		}
    	echo json_encode($data_chart);    	
    }
}?>
