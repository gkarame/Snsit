<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($timesheetSummary);
	
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table  class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">Customer</th>
					<th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Project</th>
					<th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">PM</th>
					<th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Version</th>
					<th class="h2" width="100" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">FBR</th>
					<th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Module</th>
					<th class="h2" style="text-align:left;font-family:arial;width:20%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Keywords</th>
					<th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Author</th>
					<th class="h2" style="text-align:left;font-family:arial;width:60%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Actual MDs</th>			
				</tr>							<?php foreach($fbrsList as $key => $lic) { 
					
				?>
			
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:5px"><?php echo $lic['customer'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo  ($lic['project']);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo  Users::getNameByid($lic['project_manager']);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo Codelkups::getCodelkup($lic['version']);?></td>
						<td width="100" style="font-family: Arial, Helvetica, sans-serif;border-right:1px solid #567885; ">
						
						<div class="inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);" style="    float: left;">
						<div class="first_it panel_container">
						<span  style="width: 150px;float: left;"><?php echo $lic['description'] ?></span><u class="red">+</u>
							 <div class="panelM" style = "left:50px">
								 <div style="  background-image: url('/images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
								<div style="  background-image: url('/images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
								 	<div class="coverM" style="background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;float: left;"><?php echo "<b></b> ".$lic['notes']; ?> </div>
								 </div>
								 <div  style="background-image: url('/images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
							 </div>
						 </div>
			 			</div>
						<?//php echo Codelkups::getCodelkup($cs['product']); ?>
						</td>

 
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo Codelkups::getCodelkup($lic['module']);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo $lic['keywords'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo ProjectsTasks::getAllUsersTaskReport($lic['task']);?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo Utils::formatNumber(ProjectsTasks::getTimeSpentperTaskID($lic['task']),2); ?></td>
					</tr>
				 <?php }?>
				<tr>
					<td class="h2" style="border-left:1px solid #B20533;padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:5px">  <?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:left; border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:5px"><?php echo " " ; ?>
					</td>
					
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:left; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #B20533;">
					</td>
					
				</tr>
			</table>
		</div>
		
	
	</div>
		<?php }?>

