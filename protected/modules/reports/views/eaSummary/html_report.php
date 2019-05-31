<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($timesheetSummary);
	foreach($eaSummary as $lic) {
			$id_customer = $lic['customer_id'];
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			<table class="first">
				<tr>
					<td class="h3" style="padding-top:30px;font-family:arial;padding-left:20px;border-top:1.5px solid #567885;"><b>Customer:</b> <?php echo Customers::getNameById($id_customer);?></td>
				</tr>
			</table>
			
			<?php 			
							$total_amount = 0;
				?>
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">EA #</th>
					<th class="h2" style="text-align:left;font-family:arial;width:15%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"># of Licenses</th>
					<th class="h2" style="text-align:left;font-family:arial;width:35%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">EA Approve Date</th>
					
					
				</tr>
				
					<tr>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo  $lic['eanumber'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $lic['licnumber'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo $lic['eadate'];?></td>
					</tr>
				 
				<tr>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px"><?php echo " " ; ?>
					</td>
				</tr>
				
			</table>
				<?php  $total_amount += $lic['licnumber'];?>
				
			<table class="first">
				
					<th class="h3" style="padding-top:20px;padding-bottom:20px;font-family:arial;padding-left:20px;border-top:1.5px solid #567885;">Total number of licenses per customer: <?php echo Utils::formatNumber($total_amount);?> </th>
				
			</table>
		
		</div>
		
	
	</div>
		<?php } }?>

