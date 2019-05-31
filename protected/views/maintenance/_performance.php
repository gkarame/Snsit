<?php
if($model->support_service == '503'){?><table class="items"><thead><tr><th id="performance-grid_c0">Year</th><th id="performance-grid_c1">Issues</th><th id="performance-grid_c2">Escalations</th>
			<th id="performance-grid_c3">System  Down</th><th id="performance-grid_c4">Hours</th><th id="performance-grid_c5">Revenues</th>
			<th id="performance-grid_c6">Cost</th><th id="performance-grid_c7">Profit</th><th id="performance-grid_c8">Profit %</th></tr></thead><tbody>	
<?php }else {?>	
<table class="items"><thead><tr><th id="performance-grid_c0">Year</th><th id="performance-grid_c1">Hours</th><th id="performance-grid_c2">Revenues</th>
			<th id="performance-grid_c3">Cost</th><th id="performance-grid_c4">Profit</th><th id="performance-grid_c5">Profit %</th> </tr></thead><tbody>	

<?php }
if($model->support_service == '503'){
 $values = Maintenance::getAllSupportDesk($model);	if(empty($values)){	$k= date('Y');	?>
		<tr><td><?php echo $k; ?></td><td><?php echo "0"?></td><td><?php echo "0";?></td><td><?php echo "0";?></td><td><?php echo "0"?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k));?></td>
			<td><?php echo Utils::formatNumber(Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost());?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k)-(Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost()))?></td>
			<td><?php if ($model->getTotalNetAmountYear($k) !=0){echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost())/$model->getTotalNetAmountYear($k)*100);}else{echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost())/1);}?></td>
		</tr>
		<?php }else{	foreach($values as $k=>$val){?>
		<tr><td><?php echo $k;?></td>	<?php $support = $val['support'];?>	<td><?php if(!empty($val['support'] )) {echo count($support); } else{ echo '0'; }?></td>
			<td><?php echo (isset($val['escalate']))?$val['escalate']:"0";?></td><td><?php echo (isset($val['system_down']))?$val['system_down']:"0";?></td>
			<td><?php if (Maintenance::getHoursByYear($model->id_maintenance, $k)==''){ echo "0";}else{echo Maintenance::getHoursByYear($model->id_maintenance, $k);}?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k));?></td><td><?php echo Utils::formatNumber(Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost());?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k)-(Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost()))?></td>
			<td><?php if ($model->getTotalNetAmountYear($k) !=0){echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost())/$model->getTotalNetAmountYear($k)*100);}else{echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYear($model->id_maintenance, $k)*SystemParameters::getCost())/1);}?></td>
		</tr>
<?php }	} }else{ $values = Maintenance::getAllMaintenanceTime($model);	if(empty($values)){	$k= date('Y');	?>
		<tr><td><?php echo $k; ?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k));?></td>
			<td><?php echo Utils::formatNumber(Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost());?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k)-(Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost()))?></td>
			<td><?php if ($model->getTotalNetAmountYear($k) !=0){echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost())/$model->getTotalNetAmountYear($k)*100);}else{echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost())/1);}?></td>
		</tr>
		<?php }else{	foreach($values as $k=>$val){?>
		<tr><td><?php echo $k;?></td>	 
			<td><?php if(!empty(Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k))){ echo Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k); }else{ echo "0";} ?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k));?></td><td><?php echo Utils::formatNumber(Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost());?></td>
			<td><?php echo Utils::formatNumber($model->getTotalNetAmountYear($k)-(Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost()))?></td>
			<td><?php if ($model->getTotalNetAmountYear($k) !=0){echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost())/$model->getTotalNetAmountYear($k)*100);}else{echo Utils::formatNumber(($model->getTotalNetAmountYear($k)-Maintenance::getHoursByYearNotStandard($model->id_maintenance, $k)*SystemParameters::getCost())/1);}?></td>
		</tr>
<?php }	}  }?>	</tbody></table>
<div class="board inline-block ui-state-default " ><div class="bhead">	<div class="title">number of issues, hours per year </div>	<div class="drag"></div></div>	
	<div class="bcontenu"><div id="chartContainerMaintenance" class="style_chart700 graph"  style="padding-top:25px"></div></div><div class="ftr"></div></div>   
<div class="board inline-block ui-state-default " ><div class="bhead">	<div class="title">Revenues, Cost, Profit per year </div>	<div class="drag"></div></div>	
	<div class="bcontenu"><div id="chartContainerLine" class="style_chart700 graph" style="padding-top:25px"></div></div> <div class="ftr"></div></div>   