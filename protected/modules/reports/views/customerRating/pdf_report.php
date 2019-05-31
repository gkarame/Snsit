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
		font:bold 12pt Calibri;
	}
	.h3_bold {
		font:bold 11pt Calibri;
	}
	.h3 {
		font:11pt Calibri;
	}
</style>
<?php 
if ($message != null) {
	echo $message;
} else {
	
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">Customer</th>
					<th class="h2" style="text-align:left;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Project</th>
					<th class="h2" style="text-align:left;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> Resource</th>
					<th class="h2" style="text-align:left;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Task</th>
					<th class="h2" style="text-align:left;width:34%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Description</th>
					<th class="h2" style="text-align:left;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Date</th>
					<th class="h2" style="text-align:left;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Hours</th>
					
				</tr>
				<?php foreach($timesheetSummary as $key =>$timesheet) { 
					
				?>
			
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px"><?php echo $timesheet['customer_name'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo  $timesheet['project_name'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $timesheet['username'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $timesheet['description'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $timesheet['comment'];?></td>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo Utils::formatDate($timesheet['date']);?></td>					
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $timesheet['amount'];?></td>
					</tr>
				 <?php }?>
				<tr>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">  <?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:20px">
					</td>
				</tr>
			</table>
		</div>
		
	
	</div>
		<?php } ?>
