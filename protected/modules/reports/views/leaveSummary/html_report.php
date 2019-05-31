<?php //print_r($expenses);
//print_r($expenses);
if($mes != null)
	echo $mes;
else

foreach($leaves as $key=>$user){
	?> 
	<div class="project_thead project" data-id="<?php echo $key;?>" > 
		<?php //print_r($expens);//exit;?>
		<?php $types = $user['type'];
		?>
		<div class="table_rep">
	
			<table class="first">
				<tr>
					<td class="h3" style="padding-top:20px;padding-left:20px;border-top:1.5px solid #567885;font-family:arial;"><b>Resource:</b> <?php echo Users:: getNameById($key) ; ?></td>
					<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;font-family:arial;"><b>From Date:</b> <?php echo date('d/m/Y',strtotime($user['start_time'])); ?></td>
				</tr>
				<tr>
					<td class="h3" style="padding-bottom:20px;padding-left:20px;border-bottom:1.5px solid #567885;font-family:arial;"><b>Leave Type:</b> <?php echo $user['type_name'];?></td>
					<td class="h3" style="padding-bottom:20px;border-bottom:1.5px solid #567885;font-family:arial;"><b>To Date:</b> <?php echo date('d/m/Y',strtotime($user['end_time'])); ?></td>
				</tr>
			</table>
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;width:8%;font-family:arial;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> Date</th>
					<th class="h2" style="text-align:left;width:37%;font-family:arial;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Leave Type</th>
					<th class="h2" style="text-align:left;width:30%;font-family:arial;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;;padding-left:20px">Comment</th>
				</tr>
				<?php 
				foreach ($types as $key_type =>$type) {
					uasort($type, $this->build_sorter('date'));
					$dates = $type['date'];
					$total[Timesheets::ITEM_DAYOFF] = 0;
					$total[Timesheets::ITEM_VACATION] = 0;
					$total[Timesheets::ITEM_SICK_LEAVE] = 0;
					$sum[Timesheets::ITEM_DAYOFF] = 0;
					$sum[Timesheets::ITEM_VACATION] = 0;
					$sum[Timesheets::ITEM_SICK_LEAVE] = 0;
					foreach ($dates as $key_date => $date ){//print_r($date);exit;
					$sum[$key_type] += $date['amount'];
					$key1= DateTime::createFromFormat('Y-m-d',$key_date)->format('d/m/Y');
				?>
					<tr>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo $key1;?></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo DefaultTasks::getDescription($key_type);?></td>
						<td class="h3" style="text-align:left;font-family:arial;padding-left:20px"><?php echo $date['comment'];?></td>
					</tr> <?php }?>
					
					
					<div style="text-align:left;font-family:arial; font-weight:bold; margin-left:19px;"> Total  <?php echo DefaultTasks::getDescription($key_type).": ".count($dates); ?> &nbsp &nbsp   -  &nbsp&nbsp Average <?php echo DefaultTasks::getDescription($key_type).": ".round(($sum[$key_type]/count($dates)),2); ?> </div>
						
					 <br> 
				
			<?php }?>
				</table>
					
		</div>
	</div>
<br/><br/><br/><br/><br/><br/><br/><br/>
<?php }?>
