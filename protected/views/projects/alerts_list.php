<div class="user-view mytabs hidden"><?php $this->widget('CCustomJuiTabs', array( 
	   'options'=>array(   'collapsible'=>false,  	'active' =>  'js:configJs.current.activeTab',    ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',));
	if (isset($_GET['id_project']) &&$_GET['id_project']!='' && $_GET['id_project']!=' ' )	{
		$id_project=$_GET['id_project'];	$alerts = Projects::getCurrentProjectWithAlerts($id_project);
	}else {
		$group=Users::checkIfDirector(Yii::app()->user->id);
		if($group=='14'){$alerts = Projects::getAllProjectWithAlertsForDirectors();	} else{ $alerts = Projects::getUserProjectWithAlerts();		}	}	?>	
	<div class="projectAlerts paddigl0">	<div class="row title">	<div class="item user inline-block normal uppercase width136">Project</div>	<div class="item inline-block normal uppercase">Customer</div>
		<div class="item inline-block normal uppercase width144">Project Manager</div>	<div class="item inline-block normal uppercase width280">Alert / Warnings</div>
		<div class="item inline-block normal uppercase width75">Date</div></div><?php foreach($alerts as $row){	?>
			<div class="row nobackground">	<div class="item user inline-block normal width136">
					<a href="<?php echo Yii::app()->createAbsoluteUrl('projects/view'); echo "/".$row['id_project']; ?>" > <?php echo Projects::getNameById($row['id_project']); ?></a>
				</div>	<div class="item inline-block normal">
					<a href="<?php echo Yii::app()->createAbsoluteUrl('customers/view'); echo "/".Projects::getCustomerByProject($row['id_project']); ?>" > <?php  echo Customers::getNameById(Projects::getCustomerByProject($row['id_project'])); ?></a>
				</div>	<div class="item inline-block normal width144">	<?php echo Projects::getProjectManager($row['id_project']); ?> </div>				
				<div class="item inline-block normal width274 paddingl21">
					<?php 	$alerts = array();	$warnings = array();  $proejctsalerts = ProjectsAlerts::getProjectAlerts($row['id_project'],'alerts');
						$proejctswarnings = ProjectsAlerts::getProjectAlerts($row['id_project'],'warnings');						
		foreach ($proejctsalerts as $value) {	array_push($alerts, $value)  ;	}
		foreach ($proejctswarnings as $value) {		array_push($warnings, $value)  ;	}
						foreach ($alerts as $alert1) { 	?>
							<div class="row nobackground">	<div class="item inline-block normal nobackground width252 ">
										<div class="notificationImg1 
											<?php switch ($alert1['severity']) {
											case '1' : 
												echo " veryImp";break;
											case '2':
												echo " imp";break;
											case '3':
												echo " normal";break;
										}?>"></div>	<?php echo $alert1['description'];?></div></div>
										<?php }	foreach ($warnings as $alert2) { 			?>
							<div class="row nobackground"><div class="item inline-block normal nobackground width252 ">
										<div class="notificationImg1
											warning"></div>									
										<?php echo $alert2['description'];?></div></div> <?php }?></div>				
				<div class="item inline-block normal width75"><?php echo Utils::formatDate($row['date'],'Y-m-d H:i:s','d/m/Y');?></div>	</div>	<?php } ?>	</div></div>