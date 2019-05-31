<?php
class WidgetProjects extends CWidget 
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
    	$this->render('/widgets/projects', array(
            'dis'=>$this,
        ));
    }    
 	public static function Value()
 	{
 		$criteria=new CDbCriteria;	
		$criteria->condition = 'customer_id = 13';	
		$criteria->limit = '5';
		return new CActiveDataProvider('projects', array(
			'criteria' => $criteria,
			'pagination'=>false,
             'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
 	}
 	public static function getProjects()
 	{
 		$select ="							
			SELECT t1.customer_id , t1.projectid ,t1.id_type , t1.pstatus, t1.currency , t1.TotalMDs , t2.ActualMDs  , (t1.TotalMDs - t2.ActualMDs) AS RemaingMDs  ,((t1.TotalMDs - t2.ActualMDs) /IFNULL(t1.TotalMDs,1))*100 AS Overrun , (t1.Netamount/IFNULL(t2.ActualMDs,1))AS ActualRate , t1.Netamount/t1.TotalMDs AS BudgetedRate FROM 
			(
				SELECT sum((select sum(ei.man_days) from eas_items ei where ei.id_ea=e.id))as TotalMDs, sum(e.netamountusd) as Netamount ,	p.id  as projectid ,
				p.id_parent ,e.TM ,p.id_type ,p.status as pstatus,p.customer_id ,e.currency	FROM  eas e , projects p
						WHERE  (p.id =e.id_project or p.id=e.id_parent_project) AND e.category<>'25' AND e.category<>'24'  AND e.category<>'454'   AND e.category<>'496' and e.status>='2'
						GROUP BY p.id ) as t1 ,
					(
						SELECT  sum(uti.amount)/8  as ActualMDs, p2.id as projectid, p2.id_parent FROM user_time uti , projects_tasks pt  , projects_phases pp , projects p ,projects p2
									WHERE uti.id_task=pt.id AND uti.default='0' AND pt.id_project_phase=pp.id AND pp.id_project=p.id AND (p2.id=p.id or p2.id=p.id_parent)
						GROUP BY p2.id
					)  as t2";
		$where ="WHERE t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid  AND (t1.TM<>'1' or t1.TM is null) AND (t1.id_type=26 or t1.id_type=27) AND t1.pstatus=1 ";
		$groupby= "GROUP BY  t1.projectid";	$orderby="  ORDER BY t1.TotalMDs DESC ";
		$query="SELECT t1.customer_id , t1.projectid ,t1.id_type , t1.pstatus, t1.currency , t1.TotalMDs , t2.ActualMDs , (t1.TotalMDs - t2.ActualMDs) AS RemaingMDs ,((t1.TotalMDs - t2.ActualMDs) /IFNULL(t1.TotalMDs,1))*100 AS Overrun , (t1.Netamount/IFNULL(t2.ActualMDs,1))AS ActualRate , t1.Netamount/t1.TotalMDs AS BudgetedRate FROM ( SELECT sum((select sum(ei.man_days) from eas_items ei where ei.id_ea=e.id))as TotalMDs, sum(e.netamountusd) as Netamount , p.id as projectid , p.id_parent , e.TM , p.id_type , p.status as pstatus, p.customer_id , e.currency FROM eas e , projects p WHERE (p.id =e.id_project or p.id=e.id_parent_project) AND e.category<>'25' AND e.category<>'24' AND e.category<>'454' AND e.category<>'496' and e.status>='2' GROUP BY p.id ) as t1 , ( SELECT sum(uti.amount)/8 as ActualMDs, p2.id as projectid, p2.id_parent FROM user_time uti , projects_tasks pt , projects_phases pp , projects p ,projects p2 WHERE uti.id_task=pt.id AND uti.default='0' AND pt.id_project_phase=pp.id AND pp.id_project=p.id AND (p2.id=p.id or p2.id=p.id_parent) GROUP BY p2.id ) as t2	WHERE t1.id_parent is null AND t2.id_parent is null AND t1.projectid =t2.projectid AND (t1.TM<>'1' or t1.TM is null) AND t1.id_type not in ('24' , '25' , '28') AND t1.pstatus=1 AND (t1.id_type=26 or t1.id_type=27) GROUP BY t1.projectid ORDER BY t1.TotalMDs DESC";
		$results=Yii::app($query)->db->createCommand($query)->queryAll(); 
		foreach ($results as $result) 
 		{ 			
 			$result['ActualMDs']=Projects::getActualMD($result['projectid']);
 			if ($result['ActualMDs']==0){	$result['ActualMDs']=1;	}
			$result['TotalMDs']=Projects::getProjectTotalManDaysByProject($result['projectid']); 
			if ($result['TotalMDs']==0)	{	$result['TotalMDs']=1; }
			$result['RemaingMDs']=$result['TotalMDs']-$result['ActualMDs'];
	 		$netamount=Projects::getNetAmount($result['projectid']);
	 		$result['ActualRate']= $netamount/$result['ActualMDs'];	
	 		$result['Overrun']= (($result['TotalMDs']-$result['ActualMDs'])/$result['TotalMDs'])*100;	 		
			$result['BudgetedRate']=Projects::getBudgetedRate($result['projectid']);
		}
 		return $results;
 	}
}?>
