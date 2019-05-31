<?php	$this->breadcrumbs=array('Alerts',);	if(GroupPermissions::checkPermissions('general-alerts')){
	$user_group=Projects::getUserGroup();	$visa = array();	$passport = array();	$bDays = array();
	if(GroupPermissions::checkPermissions('alerts-visas_alerts')){		$visa = UserVisas::getVisasExpires()->getData();	}	
	if(GroupPermissions::checkPermissions('alerts-passports_alerts')){		$passport = UserVisas::getPassportExpires()->getData();	}
	if(GroupPermissions::checkPermissions('alerts-birthdays')){		$bDays = UserPersonalDetails::getNextBdays(7);	}
	$timesheets = array();		$timesheets_user=array();
	if(GroupPermissions::checkPermissions('alerts-timesheets_submittet')){	
		$group=Users::checkIfDirector(Yii::app()->user->id);
		if($group=='14'){	$timesheets = TimesheetStatuses::getLastTimesheets('Submitted');	}
			else{
				$timesheets = TimesheetStatuses::getLastTimesheets('Submitted');
				$timesheets_user=TimesheetStatuses::getUserLastTimesheets('Submitted');	}	} 
		$timesheets_new = array();		$timesheets_count=array();
		if(GroupPermissions::checkPermissions('alerts-timesheets_new')){
			if($user_group>0){	$timesheets_new = TimesheetStatuses::getLastTimesheets('New');
			}else{
				$timesheets_new = TimesheetStatuses::getLastTimesheets('New');
				$timesheets_count=TimesheetStatuses::getCountTimesheets('New');		}	}
	$audit_needed=array();	$expenses = array();	$systemDown = array();	$support = array();		$totalprojectAlerts = 0;
	if(GroupPermissions::checkPermissions('alerts-issue_tickets')){
		if($user_group>0 || Yii::app()->user->id==9  || Yii::app()->user->id==31){
			$audit_needed = SupportDesk::getLicenseAuditNeeded();
		}else{		$audit_needed = SupportDesk::getLicenseAuditNeededUser();	}	}		
	if(GroupPermissions::checkPermissions('alerts-expenses_sheets')){
		if($user_group>0){	$expenses = Expenses::getLastExpenses('Submitted');	}else{	$expenses = Expenses::getUserLastExpenses('Submitted');	}	}
	if(GroupPermissions::checkPermissions('alerts-system_down')){		$systemDown = SupportDesk::getSystemDown();	}
	if(GroupPermissions::checkPermissions('alerts-issue_tickets')){
		$group=Users::checkIfTechManager(Yii::app()->user->id);
		if($group=='34' || $group=='25'){
			$support = SupportDesk::getNewIssues(0);	}else{		$support = SupportDesk::getNewIssues2(0);	}	}	
	if(GroupPermissions::checkPermissions('alerts-project_alerts')){
		$group=Users::checkIfDirector(Yii::app()->user->id);
		if($group=='14'){
			$projectAlerts = Projects::getAllProjectWithAlertsForDirectors();	$totalprojectAlerts = 0;	$totalprojectWarnings = 0;
			foreach($projectAlerts as $data){
				$totalprojectAlerts += ProjectsAlerts::getAlertsCount($data['id_project'],$data['alerts'],'alert');
				$totalprojectWarnings += ProjectsAlerts::getAlertsCount($data['id_project'],$data['alerts'],'warning');
			}
			$totalalertswarnings= $totalprojectAlerts+$totalprojectWarnings;
		}else{
			$projectAlerts = Projects::getUserProjectWithAlerts();		$totalprojectAlerts = 0;		$totalprojectWarnings = 0;
			foreach($projectAlerts as $data){
				$totalprojectAlerts += ProjectsAlerts::getAlertsCount($data['id_project'],$data['alerts'],'alert');
				$totalprojectWarnings += ProjectsAlerts::getAlertsCount($data['id_project'],$data['alerts'],'warning');
			}
			$totalalertswarnings= $totalprojectAlerts+$totalprojectWarnings;	}	}	?>	
	<div class="options"><div style="margin-right:0px;" class="line"></div>
		<?php if($totalprojectAlerts > 0){	?>	<div class="notification">
				<div style="left:15px" class="pop"><div class="div1"></div><div class="nb"><?php echo $totalalertswarnings;?></div><div class="div2"></div></div>
				 <div class="popups" id="popup1" ><div class="div1"></div>	<div class="div2"><div class="container  paddingt0 margint0" >
					<?php 	foreach($projectAlerts as $row){
									if((ProjectsAlerts::getAlertsCount($row['id_project'],$row['alerts'],'alert')>0) || (ProjectsAlerts::getAlertsCount($row['id_project'],$row['alerts'],'warning')>0)  ){
										echo '<a href="'.Yii::app()->getBaseUrl(true).'/projects/alerts?id_project='.$row['id_project'].'" style="text-decoration: none;">';
									echo '<div class="item">';
										echo '<div class="title">';
											echo Projects::getNameById($row['id_project']);
										echo '</div>';										
										echo '<div class="subtitle">';
											echo Customers::getNameById(Projects::getCustomerByProject($row['id_project']));
										echo '</div>';										
										echo '<div class="subtitle">';
											echo 'Total alerts: ' , ProjectsAlerts::getAlertsCount($row['id_project'],$row['alerts'],'alert');
										echo '</div>';
										echo '<div class="subtitle">';
											echo 'Total warnings: ' , ProjectsAlerts::getAlertsCount($row['id_project'],$row['alerts'],'warning');
										echo '</div>';										
										echo '<div class="date">';
											echo Utils::formatDate($row['date'],'Y-m-d H:i:s','d/m/Y');
										echo '</div>';
									echo '</div>';
									echo '</a>';
								}	}	$id_user= Yii::app()->User->id;	?>
						</div></div><a class="more" href="<?php echo Yii::app()->getBaseUrl(true);?>/projects/alerts"> </a>	</div>	</div>
			<div class="line"></div><?php }	if(count($audit_needed)>0){ ?>	<div class="audit">
				<div style="left:15px" class="pop"><div class="div1"></div><div class="nb"><?php echo count($audit_needed);?></div><div class="div2"></div></div>
				 <div class="popups" id="popup10" >	<div class="div1"></div>	<div class="div2">		<div class="container  paddingt0 margint0">
							<?php 	foreach($audit_needed as $audit){									
										echo '<div class="item">';
											echo '<div class="title">';
												echo Customers::getNameById($audit['id']);
											echo '</div>';
											echo '<div class="subtitle">';
												echo "Balance: ".$audit['balance']." Eligible: ".$audit['allowed']." Audited: ".$audit['audited'];
											echo '</div>';										
											echo '<div class="subtitle">';
												echo "Last Audit Date: ".$audit['date_audit'];
											echo '</div>';
										echo '</div>';  } ?></div></div><div class="div3"></div></div></div><div class="line"></div>
		<?php } if(count($bDays) > 0){	?>	<div class="birthday">	<div class="pop"><div class="div1"></div><div class="nb"><?php echo count($bDays);?></div><div class="div2"></div></div>
				<div class="popups" id="popup2">	<div class="div1"></div><div class="div2">				
						<div class="container mCustomScrollbar _mCS_4 paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
						<?php foreach($bDays as $row):?>
						<div class="item"><div class="text"><?php echo $row['firstname'].' '.$row['lastname'];?></div>
							<div class="date"><?php echo date("d/m/Y", strtotime($row['birthdate'])).' ('.Utils::prettyDate($row['birthdate']).')';?></div></div>
						<?php endforeach;?>
						</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
					</div><div class="div3"></div></div></div><div class="line"></div>
		<?php }	?><?php if(count($support) > 0)	{	?><div class="events"><div class="pop"><div class="div1"></div><div class="nb"><?php echo count($support);?></div><div class="div2"></div></div>
				<div class="popups" id="popup3"><div class="div1"></div><div class="div2">					
						<div class="container mCustomScrollbar _mCS_4 paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
					<?php foreach ($support as $row){
								$now = time();     	$your_date = strtotime($row['date']);    	$datediff = $now - $your_date;    	$days =  floor($datediff/(60*60*24));
								if($days <1){
								if (!isset($sr['Less than 1 day']))
										$sr['Less than 1 day'] = 1;
									else
										$sr['Less than 1 day'] ++;
								}else if($days <3){
										if (!isset($sr['Less than 3 days']))
											$sr['Less than 3 days'] = 1;
										else
											$sr['Less than 3 days'] ++;
									}else if($days<5){
										if (!isset($sr['Less than 5 days']))
											$sr['Less than 5 days'] = 1;
										else
											$sr['Less than 5 days'] ++;
										}else if($days<10){
											if (!isset($sr['Less than 10 days']))
												$sr['Less than 10 days'] = 1;
											else
												$sr['Less than 10 days'] ++;
										}else{
											if (!isset($sr['More than 10 Days']))
												$sr['More than 10 Days'] = 1;
											else
												$sr['More than 10 Days'] ++;
										} }?>									
						<?php foreach($sr as $k => $row):?>
						<a href="<?php echo Yii::app()->getBaseUrl(true);?>/supportDesk/newIssues?days=<?php echo $k; ?>" style="text-decoration: none;">
						<div class="item">
							<div class="title"><?php echo 	$k;?></div>
							<div class="text" ><?php echo "Total issues:".$row; ?></div></div></a>
						<?php endforeach;?>
						</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
					</div><a class="more" href="<?php echo Yii::app()->getBaseUrl(true);?>/supportDesk/newIssues"> </a></div></div><div class="line"></div>
		<?php }	?><?php if(count($systemDown) > 0){	?><div class="remarks">	<div class="pop"><div class="div1"></div><div class="nb"><?php echo count($systemDown);?></div><div class="div2"></div></div>
				<div class="popups" id="popup4"><div class="div1"></div><div class="div2">					
						<div class="container mCustomScrollbar _mCS_4 paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
						<?php foreach($systemDown as $row):?>
						<div class="item">
							<div class="title"><?php echo "System Issue #".$row['id']?></div>
							<div class="text"><?php echo Customers::getNameById($row['id_customer']);?></div>
							<div class="date"><?php echo date("d/m/Y", strtotime($row['date']));?></div></div>
						<?php endforeach;?>
						</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
					</div><div class="div3"></div></div></div><div class="line"></div>
		<?php  } ?>	<?php  $audit=Yii::app()->db->createCommand("SELECT name, n_licenses_audited, n_licenses_allowed FROM customers WHERE n_licenses_allowed < n_licenses_audited")->queryAll();
		if(count($audit) > 0){	?>
				<!--<div class="pop"><div class="div1"></div><div class="nb"><?php echo count($audit);?></div><div class="div2"></div></div>-->				
		<?php } ?><?php if(count($visa) > 0){	?><div class="visa"><div style="left:28px" class="pop"><div class="div1"></div><div class="nb"><?php echo count($visa);?></div><div class="div2"></div></div>
				<div class="popups" id="popup5"><div class="div1"></div><div class="div2">				
						<div class="container mCustomScrollbar _mCS_4 paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
						<?php foreach($visa as $row){ ?><div class="item">
							<div class="title"><?php echo $row->user->fullname;?></div><div class="subtitle"><?php echo $row->notes;?></div>
							<div class="subtitle"><?php echo 'Expires in '.Utils::formatDate($row->expiry_date) ;?></div>
							<div class="subtitle"><?php echo "( ".$row->diff.' days left )';?></div></div><?php } ?>						
						</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
					</div><a class="more" href="<?php echo Yii::app()->getBaseUrl(true);?>/users/visas"> </a></div></div><div class="line"></div>
		<?php }	if(count($passport) > 0){	?>	<div class="tasks">	<div class="pop"><div class="div1"></div><div class="nb"><?php echo count($passport);?></div><div class="div2"></div></div>
				<div class="popups" id="popup6"><div class="div1"></div><div class="div2">					
						<div class="container mCustomScrollbar _mCS_4 paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
						<?php foreach($passport as $row):?><div class="item">
							<div class="title"><?php echo $row->user->fullname;?></div>
							<div class="subtitle"><?php echo 'Expires in '.Utils::formatDate($row->expiry_date);?></div>
							<div class="subtitle"><?php echo '( '.$row->diff.' days left )';?></div></div>
						<?php endforeach;?>
						</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
					</div><a class="more" href="<?php echo Yii::app()->getBaseUrl(true);?>/users/passport"> </a></div></div><div class="line"></div>
		<?php }		if($user_group>0){
		if(count($expenses) > 0)
		{  $res=Yii::app()->db->createCommand("SELECT count(1) FROM expenses WHERE status='Submitted'")->queryScalar();		?>
			<div class="budget">	<div class="pop"><div class="div1"></div><div class="nb"><?php $manager = array();
								  $project = array();		?>
							<?php $count_expenses=0;foreach($expenses as $row):?>		
								<?php $manager[$row['project_manager']][$row['nameProject']]['total'] = $row['total'];	?>
							<?php endforeach;?>
							<?php foreach($manager as $k=>$row){
										foreach($row as $p=>$project){
											$count_expenses+=$project['total'];
										}
								} echo $count_expenses; ?></div><div class="div2"></div></div>
				<div class="popups" id="popup7">	<div class="div1"></div><div class="div2">
							<div class="container mCustomScrollbar _mCS_4 paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
							<?php $manager = array();  $project = array();	?>
							<?php foreach($expenses as $row):?>		
								<?php $manager[$row['project_manager']][$row['nameProject']]['total'] = $row['total'];	?>
							<?php endforeach;?>
							<?php foreach($manager as $k=>$row):?>	
							<div class="item">
								<div class="title"><?php echo Users::getUsername($k);?></div>
								<?php foreach($row as $p=>$project):?>	
									<?php if( $project['total'] >1 ){ $word= ' Expenses'; }else{ $word= ' Expense';} ?>
									<div class="subtitle"><?php echo $p.' - '.$project['total'].$word;?></div>
									
								<?php endforeach;?>
							</div>
							<?php endforeach;?>
							</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
						</div>		<div class="div3"></div></div>	</div>	<div class="line"></div>	<?php }	}else{
		$user=Yii::app()->user->id ;	if($expenses > 0 && !empty($expenses))	{ ?>	<div class="expenses">
				<div class="pop"><div class="div1"></div><div class="nb"> <?php  echo $expenses;  ?></div><div class="div2"></div></div>				
			</div>	<div class="line"></div>	<?php 	}	}	if($user_group>0){	if(count($timesheets_new) > 0){	?>
		<div class="calendar">
			<div class="pop"><div class="div1"></div><div class="nb"><?php $count_new=0; foreach($timesheets_new as $row){$count_new+=$row['total'];} echo $count_new;?></div><div class="div2"></div></div>
			<div class="popups" id="popup8">	<div class="div1"></div><div class="div2">
					<div class="container mCustomScrollbar _mCS_4  paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
					<?php foreach($timesheets_new as $row):?>			
					<div class="item">
						<div class="title"><?php echo $row['firstname'], ' ', $row['lastname'];?></div>
						<div class="subtitle">Timesheets to Submit: <?php echo $row['total'];?></div>
						<div class="date"><?php echo 'Oldest Time Sheet Date: '.Utils::formatDate($row['week_start'],'Y-m-d H:i:s', 'd/m/Y');?></div>		
						<div class="date"></div>
					</div>
					<?php endforeach;?>
					</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
				</div>	<div class="div3"></div></div></div>	<div class="line"></div><?php }	}else{	if(count($timesheets_count) > 0){	?>
		<div class="calendar_alerts">			
			<div class="pop"><div class="div1"></div><div class="nb"><?php echo count($timesheets_count);?></div><div class="div2"></div></div>
			<div class="popups" id="popup8"><div class="div1"></div><div class="div2">
					<div class="container mCustomScrollbar _mCS_4  paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
					<?php foreach($timesheets_new as $row):?>			
					<div class="item">	<div class="title"><?php echo $row['firstname'], '  ', $row['lastname'];?></div>
						<div class="subtitle">Total: <?php echo $row['total'];?></div>
						<div class="date"><?php echo 'Oldest Time Sheet Date: '.Utils::formatDate($row['week_start'],'Y-m-d H:i:s', 'd/m/Y');?></div>		
					<div class="date"></div>
					</div>
					<?php endforeach;?>
					</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
				</div>	<div class="div3"></div></div>	</div>	<div class="line"></div><?php }	}	$manager = array();
			$manager=Yii::app()->db->createCommand("SELECT  count( distinct id) as count , manager  from(
SELECT 
			DISTINCT	t.id , p.business_manager as manager
				 FROM projects p 
				 LEFT JOIN projects_phases pp ON pp.id_project=p.id 
				 LEFT JOIN projects_tasks pt ON pt.id_project_phase=pp.id
				 LEFT JOIN user_time ut ON ut.id_task=pt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user
						WHERE (t.status = 'Submitted' OR t.status = 'In Approval') AND ut.status = 0  AND ut.amount > 0 and ut.id_user= p.project_manager  AND ut.amount >0 AND ut.`default` =0
union
SELECT 
			DISTINCT t.id, p.project_manager  as manager 
				 FROM projects p 
				 LEFT JOIN projects_phases pp ON pp.id_project=p.id 
				 LEFT JOIN projects_tasks pt ON pt.id_project_phase=pp.id
				 LEFT JOIN user_time ut ON ut.id_task=pt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON u.id=t.id_user
						WHERE (t.status = 'Submitted' OR t.status = 'In Approval') AND ut.status = 0  AND ut.amount > 0 and ut.id_user<> p.project_manager AND ut.amount >0 AND ut.`default` =0
union 
	SELECT t.id as id,upd.line_manager as manager
				 FROM default_tasks dt
				 LEFT JOIN default_tasks m ON m.id = dt.id_parent
				 LEFT JOIN user_time ut ON ut.id_task=dt.id
				 LEFT JOIN timesheets t ON ut.id_timesheet=t.id  
				 LEFT JOIN users u ON t.id_user = u.id
				 LEFT JOIN user_personal_details upd ON u.id = upd.id_user 
						WHERE (t.status = 'Submitted' OR t.status = 'In Approval')AND ut.status = 0 AND ut.amount > 0 and dt.id_parent is not null  and ut.id_user<>upd.line_manager  AND ut.`default` =1
) as ts  GROUP BY manager")->queryAll();		
		$group=Users::checkIfDirector(Yii::app()->user->id);		
		if($group==14 || $group==10 ){
		if(count($manager)>0){  $res  =  array_sum(array_column($manager,'count'));			?>
		<div class="calendar_new">	<div class="pop"><div class="div1"></div><div class="nb"><?php echo $res; ?></div><div class="div2"></div></div>
			<div class="popups" id="popup9"><div class="div1"></div><div class="div2">
					<div class="container mCustomScrollbar _mCS_4  paddingt0 margint0"><div style="position:relative; height:100%; overflow:hidden; max-width:100%;" id="mCSB_4" class="mCustomScrollBox mCS-light"><div style="position:relative; top:0;" class="mCSB_container">
					<?php foreach($manager as $row): ?>	<div class="item">	<div class="title"><?php echo Users::getUsername($row['manager']);?></div>
							<div class="subtitle">Timesheets to Approve: <?php 	echo $row['count']; ?></div>	</div>
					<?php endforeach;?>
					</div><div style="position: absolute; display: block;" class="mCSB_scrollTools"><a oncontextmenu="return false;" class="mCSB_buttonUp"></a><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; height: 212px; top: 0px;" class="mCSB_dragger"><div style="position: relative; line-height: 212px;" class="mCSB_dragger_bar"></div></div><div class="mCSB_draggerRail"></div></div><a oncontextmenu="return false;" class="mCSB_buttonDown"></a></div></div></div>
				</div>	<div class="div3"></div></div>	</div>	<div class="line"></div>
		<?php } }else{ $id_user=Yii::app()->user->id;	$timesheets_to_approve=(int)Yii::app()->db->createCommand("	select count(distinct t1.id) from 	(SELECT DISTINCT t.id
FROM projects p
LEFT JOIN projects_phases pp ON pp.id_project = p.id
LEFT JOIN projects_tasks pt ON pt.id_project_phase = pp.id
LEFT JOIN user_time ut ON ut.id_task = pt.id
LEFT JOIN timesheets t ON ut.id_timesheet = t.id
LEFT JOIN users u ON u.id = t.id_user
WHERE ( p.project_manager = ".$id_user." OR ( p.business_manager = ".$id_user." AND t.id_user = p.project_manager ) )
AND ut.amount >0 AND t.id_user <> ".$id_user." AND ut.status =0 AND ut.`default` =0 AND ( t.status = 'Submitted' OR t.status = 'In Approval' )
UNION
				SELECT DISTINCT t.id
FROM default_tasks dt
LEFT JOIN default_tasks m ON m.id = dt.id_parent
LEFT JOIN user_time ut ON ut.id_task = dt.id
LEFT JOIN timesheets t ON ut.id_timesheet = t.id
LEFT JOIN users u ON t.id_user = u.id
LEFT JOIN user_personal_details upd ON u.id = upd.id_user
WHERE upd.line_manager =".$id_user." AND ut.amount >0 AND ut.`default`=1  AND t.id_user <>".$id_user." AND ut.status =0 AND dt.id_parent IS NOT NULL AND ( t.status = 'Submitted' OR t.status = 'In Approval' )  ) as t1")->queryScalar();	?>	
<?php if($timesheets_to_approve>0){?>	<div class="calendar_approval">
			<div class="pop"><div class="div1"></div><div class="nb"><?php echo $timesheets_to_approve; ?></div><div class="div2"></div></div>
		</div>	<div class="line"></div>	<?php 	}	}	?></div>  <?php } ?>
<script>
	$('.header .options .calendar_alerts').click(function(event){
		window.location.replace("<?php echo Yii::app()->getBaseUrl(true);?>/timesheets/index");
	});
	</script>
<script>
	$('.header .options .calendar_approval').click(function(event){
		window.location.replace("<?php echo Yii::app()->getBaseUrl(true);?>/timesheets/approval");
	});
	</script>
<script>
	$('.header .options .expenses').click(function(event){
		window.location.replace("<?php echo Yii::app()->getBaseUrl(true);?>/expenses/index");
	});

	$('.header .options .calendar').click(function(event){
		$('.popups').hide();	$('#popup8').show();
		$("#popup8 .container").mCustomScrollbar("destroy");
		$("#popup8 .container").mCustomScrollbar({  
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});

		$('.header .options .budget').click(function(event){
		$('.popups').hide();	$('#popup7').show();
		$("#popup7 .container").mCustomScrollbar("destroy");
		$("#popup7 .container").mCustomScrollbar({  
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
		$('.header .options .calendar_new').click(function(event){
		$('.popups').hide();	$('#popup9').show();
		$("#popup9 .container").mCustomScrollbar("destroy");
		$("#popup9 .container").mCustomScrollbar({   
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	</script>