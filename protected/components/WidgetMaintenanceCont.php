<?php
class WidgetMaintenanceCont extends CWidget 
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
    	$this->render('/widgets/maintenanceCont', array(
            'dis'=>$this,
        ));
    }
 	public static function getContracts()
 	{
 		$rtrnRes = array();
 		$select ="select distinct c.id, c.name,m.id_maintenance, m.contract_description, m.support_service from customers c left outer join maintenance m on c.id=m.end_customer left outer join users u on c.ca=u.id left outer join users u2 on c.account_manager=u2.id where  m.status='active'  and c.status='1' and m.starting_date is not null and m.support_service in (501,502) order by name";
		$results=Yii::app()->db->createCommand($select)->queryAll();
		foreach ($results as $result) 
 		{ 	
 			$key = $result['id'];
 			$rtrnRes[$key]['id'] = 	$result['id'];
 			$rtrnRes[$key]['name'] = $result['name'];
 			$rtrnRes[$key]['descr'] = $result['contract_description'];
 			$rtrnRes[$key]['serv'] = Codelkups::getCodelkup($result['support_service']);
			$rtrnRes[$key]['contract_id'] = $result['id_maintenance'];
		}
 		return $rtrnRes;
 	}
}
