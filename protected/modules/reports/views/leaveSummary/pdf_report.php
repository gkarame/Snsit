<style>
	div, span, table {
		font:11pt Calibri;
		color:#000;
	}
	table {
		border-collapse:collapse;
		width:100%;
	}
	.first td {
		padding-bottom:5px;
		padding-left:7px;
		padding-right:0;
		padding-top:0;
	}
	.second {
		min-height:300px;
		border:none;
		margin-top:60px;
	}
	.second td, .second th {
		padding:10px 5px;
	}
	.h2 {
		font:bold 11pt Calibri;
	}
	.h3_bold {
		font:bold 11pt Calibri;
	}
	.h3 {
		font:11pt Calibri;
	}
</style>

<div class="project_thead project"  data-id=<?php echo $key;?> >
		<?php //print_r($expens);//exit;?>
		<?php $types = $user['type'];
		?>
		<div class="table_rep">
	
			<table class="first">
				<tr>
					<td class="h3" style="padding-top:20px;padding-left:20px;border-top:1.5px solid #567885;"><b>Resource:</b> <?php echo Users:: getNameById($key) ; ?></td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;"><b>From Date:</b> <?php echo date('d/m/Y',strtotime($user['start_time'])); ?></td>
				</tr>
				<tr>
					<td class="h3" style="padding-bottom:20px;padding-left:20px;border-bottom:1.5px solid #567885;"><b>Leave Type:</b> <?php echo $user['type_name'];?></td>
					<td class="h3" style="padding-bottom:20px;border-bottom:1.5px solid #567885;"><b>To Date:</b> <?php echo date('d/m/Y',strtotime($user['end_time'])); ?></td>
				</tr>
			</table>
			<br/ >
			<br/ >
			<table >
				<tr>
					<th class="h2" style="text-align:left;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Date</th>
					<th class="h2" style="text-align:left;width:37%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Leave Type</th>
					<th class="h2" style="text-align:left;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;margin-left:30px">  Comment</th>
				</tr>
				<?php 
				foreach ($types as $key_type =>$type) {
					uasort($type, $this->build_sorter('date'));
					$dates = $type['date'];
					$total[Timesheets::ITEM_DAYOFF] = 0;
					$total[Timesheets::ITEM_VACATION] = 0;
					$total[Timesheets::ITEM_SICK_LEAVE] = 0;
					$sum[Timesheets::ITEM_DAYOFF] = 0;
					$sum[Timesheets::ITEM_VACATION] = 0;
					$sum[Timesheets::ITEM_SICK_LEAVE] = 0;
					foreach ($dates as $key_date => $date ){//print_r($date);exit;
					$sum[$key_type] += $date['amount'];
					$key1= DateTime::createFromFormat('Y-m-d',$key_date)->format('d/m/Y');
				?>
					<tr>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $key1; ?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo DefaultTasks::getDescription($key_type);?></td>
						<td class="h3" style="text-align:left;padding-left:20px"><?php echo $date['comment'];?></td>
					</tr>
					
				<?php }?>
				<?php $total[$key_type] = count($dates); 	?> 		
<tr>
				

			<?php 	if ($key_type=='13') {  $vacation=$total[$key_type];  $vacationsum=$sum[$key_type] ;  } 
					if ($key_type=='12') {   $sick=$total[$key_type];  $sicksum=$sum[$key_type] ;} 
					if ($key_type=='11') {  $dayoff=$total[$key_type];  $dayoffsum=$sum[$key_type] ;} 
				 ?>


				
				<?php } ?>


			</table>
			<br/ >
			<br/ >
			<?php if($vacation>0 || $sick>0 || $dayoff>0) { ?> 
			<table>

				<tr>
					<th style="text-align:left;width:8%;border-top:1.5px solid #567885;padding-left:20px" >						
								 TOTAL </th>
					<th style="text-align:left;width:8%;border-top:1.5px solid #567885;padding-left:20px" >						
						   AVERAGE
					
					</th>
					</tr>
				<?php if($vacation>0) { ?> 
				<tr>
					<td style="text-align:left;width:8%;padding-left:20px" >						
									 <?php echo DefaultTasks::getDescription('13').": ".$vacation;?></td>
					<td style="text-align:left;width:8%;padding-left:20px" >						
						    <?php echo DefaultTasks::getDescription('13').": "; echo Utils::formatNumber($vacationsum/$vacation); ?>
					
					</td>
				</tr>
				<?php } ?>
			<?php if($sick>0) { ?> 
				<tr>
					<td  style="text-align:left;width:8%;padding-left:20px" >						
									<?php echo DefaultTasks::getDescription('12').": ".$sick;?>					</td>
					<td style="text-align:left;width:8%;padding-left:20px" >						
									<?php echo DefaultTasks::getDescription('12').": ";  echo Utils::formatNumber($sicksum/$sick); ?>
					</td>

				</tr>
				<?php } ?>
				<?php if($dayoff>0) { ?> 
				<tr>
					<td  style="text-align:left;width:8%;padding-left:20px" >						
									 <?php echo DefaultTasks::getDescription('11').": ".$dayoff;?> 
					</td>
					<td  style="text-align:left;width:8%;padding-left:20px" >						
									 <?php echo DefaultTasks::getDescription('11').": "; echo Utils::formatNumber($dayoffsum/$dayoff); ?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td  style="text-align:left;width:8%;padding-left:20px;border-bottom:1.5px solid #567885;" >						
								
					</td>
					<td  style="text-align:left;width:8%;padding-left:20px;border-bottom:1.5px solid #567885;" >						
									
					</td>
				</tr>
				</table>
				<?php } ?>
		</div>
	</div>