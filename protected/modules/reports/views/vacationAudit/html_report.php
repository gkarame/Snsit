<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($timesheetSummary);
	
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">Resource</th>
					<th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Date</th>
					<th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Comment</th>

				
				</tr>



							<?php foreach($vacationAudit as $key => $lic) { 
					
				?>
			
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px"><?php echo Users::getNameByid($lic['id_user']);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo  $lic['date'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $lic['comment'];?></td>
					</tr>
				 <?php }?>
				<tr>
					<td class="h2" style="border-left:1px solid #B20533;padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">  <?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:20px;border-right:1px solid #B20533;">
					</td>
				</tr>
			</table>
		</div>
		
	
	</div>
		<?php }?>

