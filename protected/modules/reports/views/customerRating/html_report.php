<?php 
if ($message != null) {
	echo $message;
} else {
	//echo count($CustomerRating);
	$total_hours=0;
		?>
	<div class="project_thead project" data-id="" style="border-bottom: 1px solid #BABABA;"> 
		<div class="table_rep">
			
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:12%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">Customer</th>
					<th class="h2" style="text-align:center;font-family:arial;width:9%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">SR#</th>
					<th class="h2" style="text-align:center;font-family:arial;width:9%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Assigned</th>
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:10px">Rate</th>						
					<th class="h2" style="text-align:center;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Comment</th>
					<th class="h2" style="text-align:center;font-family:arial;width:9%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:5px">Date</th>	
					
				</tr> 
				<?php foreach($CustomerRating as $cs) { ?>
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px"><?php echo $cs['name'];?></td>
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo '<a class="show_link" href="'.Yii::app()->createUrl("supportDesk/update", array("id" => $cs['sd_no'])).'">'.$cs['sd_no'].'</a>'; ?></td>						
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo Users::getnamebyID($cs['assigned_to']); ?></td>						
						<td  style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><img src="../../images/<?php echo  $cs['rate']; ?>stars.png" width="80px;"></td>
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:left;border-right:1px solid #567885;padding-left:5px"><?php echo $cs['rate_comment']; ?></td>					
						<td style="font-family: Arial, Helvetica, sans-serif;text-align:center;border-right:1px solid #567885;padding-left:5px"><?php echo $cs['rate_date']; ?></td>					
						
					</tr>
				 <?php  } ?>
				 <tr>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;border-left:1px solid #567885;padding-left:10px">  <?php echo " " ; ?>
					</td>
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:10px"><?php echo " " ; ?>
					</td>					
					<td class="h2" style="padding:10px 5px;text-align:center;font-family:arial; border-bottom:1px solid #B20533;border-right:1px solid #567885;padding-left:5px"><?php echo " " ; ?>
					</td>
					
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #567885;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #567885;">
					</td>
					<td class="h2" style="font-weight:normal;text-align:center;font-family:arial; border-bottom:1px solid #B20533;padding-left:5px;border-right:1px solid #567885;">
					</td>
				</tr>
				
				
				
			</table>
		</div>
		
	
	</div>
		<?php }?>

