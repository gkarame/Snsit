<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($timesheetSummary);
	$total_hours=0;
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">Customer</th>
					<th class="h2" style="text-align:left;font-family:arial;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Project</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px"> Resource</th>
					<th class="h2" style="text-align:left;font-family:arial;width:11%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Phase</th>
					<th class="h2" style="text-align:left;font-family:arial;width:11%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Task</th>
					<th class="h2" style="text-align:left;font-family:arial;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Description</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Date</th>
					<th class="h2" style="text-align:left;font-family:arial;width:3%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Hrs.</th>
				<th class="h2" style="text-align:center;font-family:arial;width:3%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Bill.</th>	
								<th class="h2" style="text-align:center;font-family:arial;width:3%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Approved</th>	
				</tr>
				<?php foreach($timesheetSummary as $key => $timesheet) { 
					if($timesheet['project_name']=='CA')
					{
					if ($timesheet['customer_id']!=0) { $customername=Customers::getNameById($timesheet['customer_id']);}
					else{ $customername=$timesheet['description'];}
				}else{ $customername=$timesheet['customer_name']; }	
				$phase=''; $task='';
				if ($timesheet['project_name']=='CA')
				{	$phase='CA';	}else if ($timesheet['customer_id']=='177'){
					$phase=$timesheet['description'];
				}else{
					$phase=ProjectsTasks::getPhaseDescByid($timesheet['description']);
				}
				
				if ($timesheet['project_name']=='CA')
				{	$task='CA';	}else if ($timesheet['customer_id']=='177'){
					$task=$timesheet['description'];
				}else{
					$task=ProjectsTasks::getTaskDescByid($timesheet['description']);
				}
				
				?>
			
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px"><?php echo  $customername; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:10px"><?php echo  $timesheet['project_name'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo $timesheet['username'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:10px"><?php echo $phase;?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:10px"><?php echo $task;?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo $timesheet['comment'];?></td>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo Utils::formatDate($timesheet['date']);?></td>					
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:10px"><?php echo Utils::formatNumber($timesheet['amount']);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $timesheet['billable'] /*=='Yes'){echo 'Y';}else{ echo 'N';};*/?></td>
							<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $timesheet['approved'] /*=='Yes'){echo 'Y';}else{ echo 'N';};*/?></td>
					</tr>
				 <?php $total_hours= $total_hours + $timesheet['amount'];  } ?>
				 <tr>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">  <?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #567885;">
					</td>
				</tr>
				 <tr>
					
					<th  colspan="8" rowspan="2" class="h2" style="padding:10px 5px;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:10px;border-left:1px solid #B20533;text-align:right;font-family:arial;"><?php echo "Total Hours Spent: " ; ?>
					</th>
					<th  colspan="2" rowspan="2" class="h2" style="padding:10px 5px;text-align:left;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:5px">  <?php echo  Utils::formatNumber($total_hours) ; ?>
					</th>
					
				</tr>
				
				
			</table>
		</div>
		
	
	</div>
		<?php }?>

