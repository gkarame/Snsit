<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($timesheetSnapshot);
	$total_hours=0;
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">Resource</th>
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Annual Leaves</th>
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Day Off</th>
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Sick Leaves</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Emergency/Administrative Leaves</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Average Working Hours per Day</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Average Working Hours per Weekend</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Total Working Hours</th>	
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Billabilty</th>	
					
				</tr>
				<?php foreach($timesheetSnapshot as $key => $timesheet) { 
					
				?>
					<?php 
					$other=0;
					$si=0;
					$vac=0;
					$do=0;
					$ph=0;

					foreach ($timesheet as $k=>$value) {

					
							switch ($k) {
								case 'Sick Leave':
										$si=$value;
									break;
								case 'Vacation':
										$vac=$value;
									break;
								case 'Day Off':
										$do=$value;
									break;	
								case 'Public Holiday':	
										$ph=0;
										break;								
								default:
									$other=$other+$value;
									break;
							}
						
						}
					
					 ?>
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px"><?php echo Users::getNameByid($key);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:10px"><?php echo $vac; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $do; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:10px"><?php echo $si; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:10px"><?php echo $other; ?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $workingavg[$key]['avg'] ;?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $workingavg[$key]['avgoff'] ;?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $workingavg[$key]['actualhours'] ;?></td>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $billabilty[$key]['bill']."%" ;?></td>					
						</tr>
				 <?php  } ?>
				 <tr>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">  <?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #567885;">
					</td>
				</tr>
				
				
				
			</table>
		</div>
		
	
	</div>
		<?php }?>

