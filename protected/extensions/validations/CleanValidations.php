<?php /*// Project not closed and should be			
				$sql = "select max(ut.date) as maxdate,p.id ,p.name from projects_tasks pt , projects_phases pp , projects p , user_time ut where ut.default=0 and ut.amount>0 and pt.id=ut.id_task and  pt.id_project_phase=pp.id and pp.id_project=p.id and p.project_manager=".Yii::app()->user->id." and p.status=1 group by p.id order by ut.date desc";
				$command =Yii::app()->db->createCommand($sql);
	     
				$lwd = $command->query();
				$countproject=0;
				$resetsession=1;
					foreach ($lwd as $lw)
	        	{				
					$twomonths= Date('Y-m-d', strtotime('2 month ago'));
					if ($lw['maxdate'] < $twomonths){
					$resetsession=0;
					
					}				
			
				}
				if($resetsession==1){
					
					Yii::app()->user->isClean=1;
				
					}else{	
					Yii::app()->user->isClean=0;
				$this->redirect('../site/index');
					} ?*/>