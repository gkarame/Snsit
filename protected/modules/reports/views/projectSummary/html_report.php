<?php //print_r($expenses);
if($mes != null)
	echo $mes;
else
foreach($projects as $key=>$expens){?>
	<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
		<?php 
			if( $profit == "Yes")		
				uasort($expens, $this->build_sorter('profit'));
		?>
		<?php foreach($expens as $key_project=>$project){ 
			//uasort($project, $this->build_sorter('amount'));
			 $types = $project['id_ea'];
			 foreach ($types as $type)
			 	$id_customer = $type['customer_id'];
			?>
		<div class="table_rep">
			<table class="first">
				<tr>
					<td class="h3" style="padding-top:20px;font-family:arial;padding-left:20px;border-top:1.5px solid #567885;"><b>Customer:</b> <?php echo Customers::getNameById($id_customer);?></td>
					<td class="h3" style="padding-top:20px;font-family:arial;border-top:1.5px solid #567885;"><b>Project Status:</b> <?php echo Projects::getStatusLabel(Projects::getId('status', $key_project)); ?></td>
				</tr>
				<tr>
					<td class="h3" style="padding-bottom:20px;font-family:arial;padding-left:20px;border-bottom:1.5px solid #567885;"><b>Project:</b> <?php echo Projects::getNameById($key_project);?></td>
					<td class="h3" style="padding-bottom:20px;font-family:arial;border-bottom:1.5px solid #567885;"><b>Category:</b> <?php echo Codelkups::getCodelkup(Projects::getId('id_type', $key_project)); ?></td>
				</tr>
			</table>
			<table class="second">
				<tr>
					<th class="h2" style="text-align:left;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Ea</th>
					<th class="h2" style="text-align:left;font-family:arial;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Man Days</th>
					<th class="h2" style="text-align:left;font-family:arial;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> Actual Man Days</th>
					<th class="h2" style="text-align:left;font-family:arial;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Remaining Man Days</th>
					<th class="h2" style="text-align:left;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px"> Budget</th>
					<th class="h2" style="text-align:left;font-family:arial;width:37%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Actual </th>
					<th class="h2" style="text-align:left;font-family:arial;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Remaining </th>
					<th class="h2" style="text-align:left;font-family:arial;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Actual Rate:</th>
					<th class="h2" style="text-align:left;font-family:arial;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Cost</th>
					<th class="h2" style="text-align:left;font-family:arial;width:37%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;padding-left:20px">Profit</th>
				
				</tr>
				<?php 
					$man_days = 0;
					$actual_man_days = 0;
					$remaining_man_days = 0;
					$budget = 0;
					$actual = 0;
					$remaining = 0;
					$actual_rate = 0;
					$cost = 0;
					$profit = 0;
				?>
				<?php foreach ($types as $key_type =>$val) {//print_r($val);
				?>
					<tr>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo $val['id_ea'];?></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo $val['man_days']?></td><?php $man_days+= $val['man_days']?>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo Projects::getProjectActualManDays($val['id_project']);?></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo $val['man_days'] - Projects::getProjectActualManDays($val['id_project']);?></td><?php $remaining_man_days += $val['man_days'] - Projects::getProjectActualManDays($val['id_project']);?>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo Utils::formatNumber($val['amount'])?></td><?php $budget +=$val['amount']?>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php  if(Projects::getProjectActualManDays($val['id_project']) != 0){ echo Utils::formatNumber($val['amount']/Projects::getProjectActualManDays($val['id_project'])); $actual += $val['amount']/Projects::getProjectActualManDays($val['id_project']);}?></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php  if(Projects::getProjectActualManDays($val['id_project']) != 0){ echo Utils::formatNumber($val['amount'] - $val['amount']/Projects::getProjectActualManDays($val['id_project']));$remaining+= $val['amount'] - $val['amount']/Projects::getProjectActualManDays($val['id_project']);}?></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php  if(Projects::getProjectActualManDays($val['id_project']) != 0){ echo Utils::formatNumber($val['amount']/Projects::getProjectActualManDays($val['id_project']));$actual_rate += $val['amount']/ Projects::getProjectActualManDays($val['id_project']);}?></td>
						<td class="h3" style="text-align:left;font-family:arial;border-right:1px solid #567885;padding-left:20px"><?php echo Utils::formatNumber(Projects::getProjectActualManDays($val['id_project'])*SystemParameters::getCost()*8); $cost += Projects::getProjectActualManDays($val['id_project'])*SystemParameters::getCost()*8;?></td>
						<td class="h3" style="text-align:left;font-family:arial;padding-left:20px"><?php if(Projects::getProjectActualManDays($key_project) != 0){ echo Utils::formatNumber($val['amount']- $val['amount']/Projects::getProjectActualManDays($val['id_project'])); $profit+=$val['amount']- $val['amount']/Projects::getProjectActualManDays($val['id_project']);}?></td>
					</tr>
				<?php }?>
				<tr>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						Total 
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($man_days);?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber(Projects::getProjectActualManDays($key_project));?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($remaining_man_days);?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($budget);?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($actual);?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($remaining);?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($actual_rate);?>
					</td>
					<td class="h2" style="padding:10px 5px;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($cost);?>
					</td>
					<td class="h2" style="font-weight:normal;font-family:arial;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;padding-left:20px">
						<?php echo Utils::formatNumber($profit);?>
					</td>
				</tr>
			</table>
		</div>
		
		<?php }?>
	</div>
<?php }?>
