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
					<th class="h2" style="text-align:left;font-family:arial;width:40%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px">Customer</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"># of Licenses Allowed</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> # of Licenses Audited</th>
					<th class="h2" style="text-align:left;font-family:arial;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Last Audit Date</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Balance</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Purchased Date</th>
					<th class="h2" style="text-align:left;font-family:arial;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"># of Purchased Licenses</th>

				
				</tr>



							<?php foreach($Licensing as $key => $lic) { 
					
				?>
			
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:20px"><?php echo $lic['name'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo  $lic['allowed'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $lic['audited'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $lic['lastaudit'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php if ($lic['delta']<0) { ?> <b><?php echo $lic['delta']; ?> </b> <?php } else { echo $lic['delta']; } ?>  </td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo substr($lic['approved'], 0,10);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $lic['man_days'];?></td>
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
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:20px">
					</td>
				</tr>
			</table>
		</div>
		
	
	</div>
		<?php }?>

