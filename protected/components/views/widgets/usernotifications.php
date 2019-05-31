<div class="bcontenu"> 	<?php  $TSflag=Yii::app()->db->createCommand("SELECT sns_admin FROM user_personal_details WHERE id_user=".Yii::app()->user->id."  ")->queryScalar();
	$dontshow=0; foreach($this->links as $row)
 	{
 		if(GroupPermissions::checkPermissions('quicklinks-id'.$row['id']))
 		{	$name=$row->name;
			$body =$name;
		if(	1==1){
			if ($row['id']==1){
			$countSR=Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE status<3 and assigned_to=".Yii::app()->user->id."  ")->queryScalar();				
					$to_replace = array('{no}'); $replace = array($countSR); $body = str_replace($to_replace, $replace, $name);
					if ($countSR==0) {$dontshow=1 ;}	} else if  ($row['id']==2  && $TSflag==0) { 						
					$countVac=Yii::app()->db->createCommand("SELECT substr(15-sum(amount)/8,1,2)  FROM user_time WHERE id_user=".Yii::app()->user->id." and user_time.default=1 and id_task=13 and date >'2015-01-30'")->queryScalar();				 
						$to_replace = array('{no}'); $replace = array($countVac); $body = str_replace($to_replace, $replace, $name);
					if ($countVac==0) {$dontshow=1 ;}	}else if  ($row['id']==3) { 
						$countTime=Yii::app()->db->createCommand("SELECT count(1)  FROM timesheets WHERE id_user=".Yii::app()->user->id." and status not in ('SUBMITTED','Approved','In Approval') ")->queryScalar();				
						$to_replace = array('{no}');
					$replace = array($countTime);
					$body = str_replace($to_replace, $replace, $name);						
							if ($countTime==0) {$dontshow=1 ;}	}else if  ($row['id']==4) { 
						 $countExpSub=Yii::app()->db->createCommand("SELECT count(1)  FROM expenses WHERE user_id=".Yii::app()->user->id." and status ='SUBMITTED' ")->queryScalar();	
					 $countExpApp=Yii::app()->db->createCommand("SELECT count(1)  FROM expenses WHERE user_id=".Yii::app()->user->id." and status ='Approved' ")->queryScalar();							
						$to_replace = array('{no}','{nu}');
					$replace = array($countExpSub,$countExpApp);
					$body = str_replace($to_replace, $replace, $name);
						if ($countExpApp==0 && $countExpSub==0 ) {$dontshow=1 ;}	}
						if(($row['id']==2 && $TSflag==1)||$row['id']==5 ||$dontshow==1){echo '';}else {
 			echo '<div class="stat"><a href="'. Yii::app()->getBaseUrl(true).$row->url.'" ">'. $body .'</a></div>'	;}
			} else {	if   ($row['id']==5) { 		
				$sql = "select max(ut.date) as maxdate,p.id ,p.name from projects_tasks pt , projects_phases pp , projects p , user_time ut where ut.default=0 and ut.amount>0 and pt.id=ut.id_task and  pt.id_project_phase=pp.id and pp.id_project=p.id and p.project_manager=".Yii::app()->user->id." and p.status=1 group by p.id order by ut.date desc";
				$command =Yii::app()->db->createCommand($sql);
				$lwd = $command->query();
				$countproject=0;
				$resetsession=1; foreach ($lwd as $lw)    	{
					$to_replace = array('{name}');	$replace = array($lw['name']); $body = str_replace($to_replace, $replace, $name);					
					$url_blank=$row->url; $to_replace_id= array('{id}'); $replace_id = array($lw['id']); $url=str_replace($to_replace_id, $replace_id, $url_blank);
				$twomonths= Date('Y-m-d', strtotime('2 month ago')); if ($lw['maxdate'] < $twomonths){ $resetsession=0;
					echo '<div class="stat"><a href="'. Yii::app()->getBaseUrl(true).$url.'" ">'.$body .'</a></div>'	; 	} }
					if($resetsession==1){ 1=1;
					}else{	 0=0;} } else { } }  }	} ?>
</div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1006,
	    'height'=>810,
		'resizable'=>false,
		'closeOnEscape' => true,    ),));?>
<div class="bcontenu z-index" id="links">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title" ><?php if(1==1 ){ echo  WidgetUserNotifications::getName();}else {echo "Pending Tasks" ;} ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div> 	
<?php $dontshow=0; 	foreach($this->links as $row) 	{
 		if(GroupPermissions::checkPermissions('quicklinks-id'.$row['id']))
 		{	$name=$row->name; $body =$name;
		if(	1==1){
			if ($row['id']==1){
					$countSR=Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE status<3 and assigned_to=".Yii::app()->user->id."  ")->queryScalar();				
					$to_replace = array('{no}'); $replace = array($countSR); $body = str_replace($to_replace, $replace, $name);
					if ($countSR==0) {$dontshow=1 ;}
						} else if  ($row['id']==2  && $TSflag==0) { 
						$countVac=Yii::app()->db->createCommand("SELECT substr(15-sum(amount)/8,1,2)  FROM user_time WHERE id_user=".Yii::app()->user->id." and user_time.default=1 and id_task=13 and date >'2015-01-30'")->queryScalar();				 
						$to_replace = array('{no}'); $replace = array($countVac); $body = str_replace($to_replace, $replace, $name); 
						if ($countVac==0) {$dontshow=1 ;}}else if  ($row['id']==3) { 
						$countTime=Yii::app()->db->createCommand("SELECT count(1)  FROM timesheets WHERE id_user=".Yii::app()->user->id." and status not in ('SUBMITTED','Approved','In Approval') ")->queryScalar();				
						$to_replace = array('{no}'); $replace = array($countTime); $body = str_replace($to_replace, $replace, $name);
						if ($countTime==0) {$dontshow=1 ;}}else if  ($row['id']==4) { 
						 $countExpSub=Yii::app()->db->createCommand("SELECT count(1)  FROM expenses WHERE user_id=".Yii::app()->user->id." and status ='SUBMITTED' ")->queryScalar();	
					 $countExpApp=Yii::app()->db->createCommand("SELECT count(1)  FROM expenses WHERE user_id=".Yii::app()->user->id." and status ='Approved' ")->queryScalar();							
						$to_replace = array('{no}','{nu}'); $replace = array($countExpSub,$countExpApp); $body = str_replace($to_replace, $replace, $name);
						if ($countExpApp==0 && $countExpSub==0 ) {$dontshow=1 ;}}
						if(($row['id']==2 && $TSflag==1)||$row['id']==5 ||$dontshow==1){echo '';}else {
 			echo '<div class="stat"><a href="'. Yii::app()->getBaseUrl(true).$row->url.'" ">'. $body .'</a></div>'	;}} else {	
			if   ($row['id']==5) { 	
				$sql = "select max(ut.date) as maxdate,p.id ,p.name from projects_tasks pt , projects_phases pp , projects p , user_time ut where ut.default=0 and ut.amount>0 and pt.id=ut.id_task and  pt.id_project_phase=pp.id and pp.id_project=p.id and p.project_manager=".Yii::app()->user->id." and p.status=1 group by p.id order by ut.date desc";
				$command =Yii::app()->db->createCommand($sql); $lwd = $command->query(); $countproject=0; $resetsession=1;
				foreach ($lwd as $lw) {		
					$to_replace = array('{name}'); $replace = array($lw['name']); 	$body = str_replace($to_replace, $replace, $name); 	$url_blank=$row->url;
					$to_replace_id= array('{id}'); $replace_id = array($lw['id']); $url=str_replace($to_replace_id, $replace_id, $url_blank);
				$twomonths= Date('Y-m-d', strtotime('2 month ago')); if ($lw['maxdate'] < $twomonths){
					$resetsession=0;	echo '<div class="stat"><a href="'. Yii::app()->getBaseUrl(true).$url.'" ">'.$body .'</a></div>'	; }				
			}	if($resetsession==1){
						echo '<div class="stat"> <a>'. $resetsession.' </a></div>';	}
					} else { } } } } ?>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>