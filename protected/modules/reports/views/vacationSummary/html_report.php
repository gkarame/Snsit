<?php //echo $user."ddd";
if ($mes != null)
{
	echo $mes;	
}else {
	$all = 0;
	foreach ($branches as $key => $branch) { ?>
		<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
			<div class="table_rep">
				<table class="first">
					<tr>
						<td class="h3" style="padding-top:20px;padding-left:20px;font-family:arial;border-top:1.5px solid #567885;"><b>Resource:</b> 
						<?php 
						if ($all = 0) {
							if(count($branch) != 1 ) {
								echo "All";
								$all = 1;
							} else {
						 		foreach ($branch as $key_user => $user)
						 			echo $user['firstname']." ".$user['lastname'];
						 	}
						} else {
							echo "All";
						} 
						?>
						</td>
						<td class="h3" style="padding-top:20px;font-family:arial;border-top:1.5px solid #567885;"><b>Year:</b> <?php echo $year; ?></td>
					</tr>
					<tr>
						<td class="h3" style="padding-bottom:20px;font-family:arial;padding-left:20px;border-bottom:1.5px solid #567885;"><b>Branch:</b> <?php  echo Codelkups::getCodelkup($key);?></td>
						<td class="h3" style="padding-bottom:20px;font-family:arial;border-bottom:1.5px solid #567885;"></td>
					</tr>
				</table>
			<table class="second">
					<tr>
						<?php $yy = substr($year,2,2);?>
						<th class="h2" style="text-align:left;font-family:arial;width:14%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">Resource</th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Feb- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Mar- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Apr- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">May- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Jun- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Jul- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Aug- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Sep- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Oct- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Nov- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Dec- <?php echo $yy?></th>
						<th class="h2" style="text-align:right;font-family:arial;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Jan- <?php echo $yy + 1?></th>
						<th class="h2" style="text-align:left;font-family:arial;width:4%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Total</th>
						<th class="h2" style="text-align:right;font-family:arial;width:11%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Eligible for</th>
						<th class="h2" style="text-align:left;font-family:arial;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">Balance</th>
					
					</tr>
			<?php foreach($branch as $key_user => $user) { ?>
						<tr>
							<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;"><?php echo $user['firstname']." ".$user['lastname'];?></td>
							<?php $total = 0;?>
							<?php for ($k = 2;$k <= 12; $k++) { ?>
							<?php $hoursPerMonth = Timesheets::getVacationsDays($user['id'], $year, $k);?>
								<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;"><?php echo $hoursPerMonth;?></td>
								<?php $total += $hoursPerMonth;?>
							<?php }?>
							<?php $januaryHours =  Timesheets::getVacationsDays($user['id'], $year+1, 1);?>
							<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;"><?php echo $januaryHours;?></td>
							<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;"><?php echo $total + $januaryHours;?></td>
							
							<!-- ************************** 15 is hardcoded needs to be added as feild in DB ************!-->
							<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;"><?php echo UserPersonalDetails::getAnnualLeaves($user['id']) ; ?></td>
							<td class="h3" style="text-align:left;font-family:arial;"><?php echo (UserPersonalDetails::getAnnualLeaves($user['id'])-($total+$januaryHours));  ?></td>
						</tr>
			<?php } ?>
				</table>
			</div>
			
		</div>
	<?php }?>
<?php }?>