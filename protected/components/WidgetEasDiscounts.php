 <?php
class WidgetEasDiscounts extends CWidget 
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
    	$this->render('/widgets/easDiscounts', array(
            'dis'=>$this,
        ));
    }
	public function CharChart1($year = null)
    {
   		for($i = 0;$i<5;$i++){	$years[] = date('Y',strtotime('now - '.$i.' year'));	}
		$data_chart = array();		sort($years);		
		foreach ($years as $year)
		{
			$discount= 0;
			$value = Yii::app()->db->createCommand("SELECT case when SUM(discount)/count(1) is null then 0 else SUM(discount)/count(1) end  perc,id from eas where status>=2 and discount>0 and YEAR(created)='$year%'")->queryScalar();
			$afterdisc = Yii::app()->db->createCommand("SELECT SUM(netamountusd) from eas where status>=2 and discount>0 and YEAR(created)='$year%'")->queryScalar();
			$beforedisc = Yii::app()->db->createCommand("
						select CASE
						                WHEN e.category= 25
						                THEN
						                    CASE 
						                        WHEN e.currency='9'
						                        THEN (ei.amount*ei.man_days)
						                        ELSE ((ei.amount*ei.man_days)*(select c.rate FROM currency_rate c where c.currency=e.currency))
						                    END
						                ELSE
						                    CASE 
						                        WHEN e.currency='9'
						                        THEN ei.amount
						                        ELSE (ei.amount*(select c.rate FROM currency_rate c where c.currency=e.currency limit 1))
						                    END
						            END AS totusd
						from eas e, eas_items ei 
						where e.id=ei.id_ea and discount>0  and YEAR(e.created)='$year%' and e.status>=2
						")->queryAll();
			$tot = array_sum(array_column($beforedisc,'totusd'));	$discount=(int)$tot-(int)$afterdisc;
		    array_push($data_chart,array('label' => $year."
				(".$discount.'$)',
		            	  'value' => (int)$value));	
		}
    	echo json_encode($data_chart);
    }
}
