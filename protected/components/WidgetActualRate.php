<?php
class WidgetActualRate extends CWidget 
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
    	$this->render('/widgets/ActualRate', array(
            'dis'=>$this,
        ));
    } 
 	public function CharChart()
    { 
    	$values = array();	$axis = array();		
		$values = Yii::app()->db->createCommand("
					SELECT t1.projectid as pid,t1.t1.pname as tag1 ,   round((t1.Netamount/IFNULL(t2.ActualMDs,1)),1) as older1 ,t1.pstatus as perc1 , round(t1.Netamount,1) as total1  FROM 
					(
						SELECT
						sum(ei.man_days)as TotalMDs,
						  sum(ei.amount) - (sum(ei.amount * e.discount)/100) as Netamount ,
						  p.name  as pname ,
						  p.id  as projectid ,
							p.id_parent ,
						e.TM ,
						p.id_type ,
						p.status as pstatus,
						p.customer_id ,
						e.currency
						FROM eas_items ei ,eas e , projects p
						WHERE e.id=ei.id_ea AND (p.id =e.id_project or p.id=e.id_parent_project) AND e.category<>'25' AND e.category<>'24'  AND e.category<>'454'   AND e.category<>'496' and e.status>='2'
						GROUP BY p.id 
					) as t1 ,
					(
						SELECT  sum(uti.amount)/8  as ActualMDs, p2.id as projectid, p2.id_parent FROM user_time uti , projects_tasks pt  , projects_phases pp , projects p ,projects p2
									WHERE uti.id_task=pt.id AND uti.default='0' AND pt.id_project_phase=pp.id AND pp.id_project=p.id AND (p2.id=p.id or p2.id=p.id_parent)
						GROUP BY p2.id
					)  as t2	
					WHERE t1.pstatus='2' and t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid AND t1.projectid not in (204,484, 222)  AND (t1.TM<>'1' or t1.TM is null) AND t1.id_type not in ('24' , '25' , '28') 	
					group by tag1
					order by pid")->queryAll();			
		foreach ($values as $key => $value) {
				$axis[$key]['tag1']= $value['tag1']; $axis[$key]['older1']= (int)$value['older1']; $axis[$key]['perc1']= (int)$value['perc1'];
				$axis[$key]['total1']= (int)$value['total1'];				
		}
		echo json_encode($axis);
    }
public function CharChart1()
    { 
		$values = array();	$axis = array();
		$values = Yii::app()->db->createCommand("
					SELECT t1.projectid as pid,t1.t1.pname as tag1 ,   round((t1.Netamount/IFNULL(t2.ActualMDs,1)),1) as older1 ,t1.pstatus as perc1 , round(t1.Netamount,1) as total1  FROM 
					(
						SELECT
						sum(ei.man_days)as TotalMDs,
						  sum(ei.amount) - (sum(ei.amount * e.discount)/100) as Netamount ,
						  p.name  as pname ,
						  p.id  as projectid ,
							p.id_parent ,
						e.TM ,
						p.id_type ,
						p.status as pstatus,
						p.customer_id ,
						e.currency
						FROM eas_items ei ,eas e , projects p
						WHERE e.id=ei.id_ea AND (p.id =e.id_project or p.id=e.id_parent_project) AND e.category<>'25' AND e.category<>'24'  AND e.category<>'454'   AND e.category<>'496' and e.status>='2'
						GROUP BY p.id 
					) as t1 ,
					(
						SELECT  sum(uti.amount)/8  as ActualMDs, p2.id as projectid, p2.id_parent FROM user_time uti , projects_tasks pt  , projects_phases pp , projects p ,projects p2
									WHERE uti.id_task=pt.id AND uti.default='0' AND pt.id_project_phase=pp.id AND pp.id_project=p.id AND (p2.id=p.id or p2.id=p.id_parent)
						GROUP BY p2.id
					)  as t2               
	
					WHERE t1.pstatus='2' and t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid  AND (t1.TM<>'1' or t1.TM is null) AND t1.id_type not in ('24' , '25' , '28') 	
					group by tag1
					order by pid")->queryAll();			
		foreach ($values as $key => $value) {
				$axis[$key]['tag1']= $value['tag1']; 	$axis[$key]['older1']= (int)$value['older1'];
				$axis[$key]['perc1']= (int)$value['perc1']; $axis[$key]['total1']= (int)$value['total1'];				
		}
		echo json_encode($axis);
	}
}
?>
