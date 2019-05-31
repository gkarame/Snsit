<div class="projectAlerts"><?php $alerts = array();	$warnings = array(); $proejctsalerts = ProjectsAlerts::getProjectAlerts($model->id,'alerts'); $proejctswarnings = ProjectsAlerts::getProjectAlerts($model->id,'warnings');
foreach ($proejctsalerts as $value){  array_push($alerts, $value)  ;	  }
foreach ($proejctswarnings as $value) {	array_push($warnings, $value)  ;	} if(count($alerts)<>0){ ?>
	<div class="row title">	<div class="item user inline-block normal">ALERT #</div><div class="item inline-block normal">DESCRIPTION</div></div><?php }
		foreach ($alerts as $alert) { ?><div class="row"><div class="item user inline-block normal"><div class="notificationImg 
					<?php switch ($alert['severity']) {
					case '1' : 
						echo " veryImp";break;
					case '2':
						echo " imp";break;
					case '3':
						echo " normal";break;
				}?>"></div><?php echo $alert['id'];?></div><div class="item bigger_width inline-block normal"><?php echo $alert['description'];?></div></div>
<?php } if(count($warnings)<>0){  ?></div>
<div class="projectAlerts"><div class="row title"><div class="item user inline-block normal">WARNING #</div><div class="item inline-block normal">DESCRIPTION</div></div>
<?php }	foreach ($warnings as $warning) { ?>
		<div class="row"><div class="item user inline-block normal">
				<div class="notificationImg 
					warning"></div>
				<?php echo $warning['id'];?>
		</div>	<div class="item bigger_width inline-block normal"><?php echo $warning['description'];?></div>	</div><?php }  ?></div>