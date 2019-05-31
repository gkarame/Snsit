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


<table class="first">
				<tr>
				
					<td class="h3" style="padding-top:20px;padding-left:20px;border-top:1.5px solid #567885;"><b>Resource:</b> 
					<?php 
					if(count($user) != 1 ) 
						echo "All";
					 else{ foreach($user as $key_user=>$user1)
					 		echo $user1['firstname']." ".$user1['lastname'];}?>
						
					 </td>
					</td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;"><b>Year:</b> <?php echo $_POST['UserPersonalDetails']['years']; ?></td>
				</tr>
				<tr>
					<td class="h3" style="padding-bottom:20px;padding-left:20px;border-bottom:1.5px solid #567885;"><b>Branch:</b> <?php  echo Codelkups::getCodelkup($key);?></td>
					<td class="h3" style="padding-bottom:20px;border-bottom:1.5px solid #567885;"></td>
				</tr>
			</table>
		<table class="second">
				<tr>
					<?php $yy = substr($_POST['UserPersonalDetails']['years'],2,2);?>
					<th class="h2" style="text-align:left;width:14%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">Resource</th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Feb-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Mar-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Apr-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">May-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Iun-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Jun-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Aug-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Sep-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Oct-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Nov-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Dec-<?php echo $yy?></th>
					<th class="h2" style="text-align:right;width:5%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Jan-<?php echo $yy + 1?></th>
					<th class="h2" style="text-align:left;width:4%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Total</th>
					<th class="h2" style="text-align:right;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;">Eligible for</th>
					<th class="h2" style="text-align:left;width:6%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">Balance</th>
				
				</tr>
		<?php foreach($user as $key_user=>$user1){
		?>
			
					<tr>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;"><?php echo $user1['firstname']." ".$user1['lastname'];?></td>
						<?php $total = 0?>
						<?php for($k = 2;$k<=12;$k++){?>
							<td class="h3" style="text-align:left;border-right:1px solid #567885;"><?php 
							$time= Timesheets::getVacationsDays($user1['id'],$_POST['UserPersonalDetails']['years'], $k);
							echo $time; ?></td>
							<?php $total += $time;?>
						<?php }?>
						<?php $januaryHours =  Timesheets::getVacationsDays($user1['id'], $year+1, 01);?>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;"><?php echo $januaryHours; ?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;"><?php echo $total+ $januaryHours;?></td>
						<td class="h3" style="text-align:left;border-right:1px solid #567885;"><?php echo UserPersonalDetails::getAnnualLeaves($user1['id']) ; ?></td>
						<td class="h3" style="text-align:left;"><?php echo (UserPersonalDetails::getAnnualLeaves($user1['id'])-($total+$januaryHours)); ?></td>
					</tr>
		<?php }?>
</table>