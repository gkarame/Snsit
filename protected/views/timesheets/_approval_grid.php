<!--  TIMESHEET TABLE -->
<br/><br/>

<div id="popuptimesheet" > <div class='timesheettitre'></div> <div class='closetimesheet'> </div> <div class="scrollme"> <div class='timehseetcontainer'></div> </div></div>

<div class="tm_approval grid ">
	<div class="thead"> 
		<div class="column120 item side-borders bold">Sheet #</div>
		<div class="column200 item side-borders bold">Task</div>
		<div class="column80 item side-borders bold">Date</div>
		<div class="column70 item side-borders bold">Hours</div>
		<div class="column70 item side-borders bold">Total</div>
		<div class="column300 item side-borders bold">Comment</div>
		<div class="column30 item side-borders side-borders-last bold"></div>
	</div>

	
<form id="approval-form">
<?php foreach ($projects as $key => $project) {?>
	<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
		<div class="project_thead thead side-borders-first task_thead" style="line-height:45px">
			<span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;
			<b><?php echo $project['project'];?></b>
   				<div style="float:right;margin-right:12px;" class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >

					<?php 
					if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
					{	?>
						<input  id="chech_<?php echo $key?>" type="checkbox"  name="tasks[]" class="tasks_chk" onclick="checkedAll('<?php echo $key;?>');"/>
	 				<?php }?>
	 			</div>
		</div>
		
		<?php $users = $project['users'];
		uasort($users, $this->build_sorter('username'));?>
		<?php foreach ($users as $key_user => $user) { 
				$timesheets = $user['timesheets'];
				uasort($timesheets, $this->build_sorter('name'));
			?>
			<div class="user" data-id="<?php echo $key_user;?>" data-id_prj="<?php echo $key;?>">
				<span class="user_head thead head task_thead" style="line-height:45px;padding-left:20px;width:93.8%"><span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;<b><?php echo $user['username'];?></b></span>
				
				<div class="user_body body">
					<?php
					foreach ($timesheets as $key_time => $timesheet) { ?>
					<div class="timesheet timesheet_<?php echo $key_time;?> project_<?php echo $key?>" data-id="<?php echo $key_time;?>" data-id_prj="<?php echo $key_time;?>" 
					data-id-user="<?php echo $key_user;?>">
						<?php  $totaltime =0;
							$times = $timesheet['times'];
							uasort($times, $this->build_sorter('task', 'date'));
							foreach ($times as $time) {   
						?>
						<div class="thead task_thead"  id="taskheader" data-id="<?php echo $time['id'];?>"  data-id="<?php echo $time['id'];?>" data-id_timesheet="<?php echo $key_time;?>"
						 data-id_prj="<?php echo $key;?>" data-id-user="<?php echo $key_user;?>">
							<div class="column30 item side-borders side-borders-first no-border margint5">
							</div>
							<div onclick="clicktimesheetgrid(<?php echo $key_time ?> ,<?php echo $key_user  ?>)"  class="column50 item side-borders paddingl30 no-border margint5 red pointer " style="text-align:left">
								<?php echo $timesheet['name'];?>
							</div>
							<div class="column200 item side-borders no-border margint5 paddingl5" style="text-align:left">
								<?php echo $time['task'];?>
							</div>
							<div class="column80 item side-borders no-border margint5 paddingl5">
								<?php echo date("d/m/Y", strtotime($time['date']));?>
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo $time['amount'];?> 
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo Timesheets::getpertask($time['id_task'], $time['id_user'], $time['date']);?> 
							</div>
							<?php $j = strlen($time['comment']);?>
							<div class="column300 item side-borders no-border margint5 paddingl5" style="text-align: left;">
								<?php echo $time['comment'];?>
							</div>
							<div class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >
						 		<?php 
								if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
								{
								?>
						 			<input type="checkbox" name="tasks[]" class="tasks_chk" value="<?php echo $time['id']."-".$time['id_timesheet'];?>"/>
						 		<?php }?>
						 	</div>
						</div>
						<?php
						$totaltime = $totaltime + $time['amount']; ?>

						<?php } ?>
					</div>
					<div style="font-size:12px; font-weight:bold; padding-top:10px; padding-bottom:10px; padding-left:65px;background-color: #FEF9F9;"  ><span style="padding-right:267px;"> <?php echo $timesheet['name']; ?>  </span> <span style="padding-right:38px;"> <?php echo "Total:"; ?> </span> <?php echo "  "; echo $totaltime ; ?> </div> 
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		<br clear = "all">
	</div>


<?php }?>

<?php foreach ($internal_projects as $key => $project) {?>
	<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
		<div class="project_thead thead side-borders-first task_thead" style="line-height:45px">
			<span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;
			<b><?php echo $project['project'];?></b>
   				<div style="float:right;margin-right:12px;" class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >

					<?php 
					if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
					{	?>
						<input  id="chech_<?php echo $key?>" type="checkbox"  name="tasks[]" class="tasks_chk" onclick="checkedAll('<?php echo $key;?>');"/>
	 				<?php }?>
	 			</div>
		</div>
		
		<?php $users = $project['users'];
		uasort($users, $this->build_sorter('username'));?>
		<?php foreach ($users as $key_user => $user) { 
				$timesheets = $user['timesheets'];
				uasort($timesheets, $this->build_sorter('name'));
			?>
			<div class="user" data-id="<?php echo $key_user;?>" data-id_prj="<?php echo $key;?>">
				<span class="user_head thead head task_thead" style="line-height:45px;padding-left:20px;width:93.8%"><span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;<b><?php echo $user['username'];?></b></span>
				
				<div class="user_body body">
					<?php
					foreach ($timesheets as $key_time => $timesheet) { ?>
					<div class="timesheet timesheet_<?php echo $key_time;?> project_<?php echo $key?>" data-id="<?php echo $key_time;?>" data-id_prj="<?php echo $key_time;?>" 
					data-id-user="<?php echo $key_user;?>">
						<?php  $totaltime =0;
							$times = $timesheet['times'];
							uasort($times, $this->build_sorter('task', 'date'));
							foreach ($times as $time) {   
						?>
						<div class="thead task_thead"  id="taskheader" data-id="<?php echo $time['id'];?>"  data-id="<?php echo $time['id'];?>" data-id_timesheet="<?php echo $key_time;?>"
						 data-id_prj="<?php echo $key;?>" data-id-user="<?php echo $key_user;?>">
							<div class="column30 item side-borders side-borders-first no-border margint5">
							</div>
							<div onclick="clicktimesheetgrid(<?php echo $key_time ?> ,<?php echo $key_user  ?>)"  class="column50 item side-borders paddingl30 no-border margint5 red pointer " style="text-align:left">
								<?php echo $timesheet['name'];?>
							</div>
							<div class="column200 item side-borders no-border margint5 paddingl5" style="text-align:left">
								<?php echo $time['task'];?>
							</div>
							<div class="column80 item side-borders no-border margint5 paddingl5">
								<?php echo date("d/m/Y", strtotime($time['date']));?>
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo $time['amount'];?> 
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo Timesheets::getpertask($time['id_task'], $time['id_user'], $time['date']);?> 
							</div>
							<?php $j = strlen($time['comment']);?>
							<div class="column300 item side-borders no-border margint5 paddingl5" style="text-align: left;">
								<?php echo $time['comment'];?>
							</div>
							<div class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >
						 		<?php 
								if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
								{
								?>
						 			<input type="checkbox" name="tasks[]" class="tasks_chk" value="<?php echo $time['id']."-".$time['id_timesheet'];?>"/>
						 		<?php }?>
						 	</div>
						</div>
						<?php
						$totaltime = $totaltime + $time['amount']; ?>

						<?php } ?>
					</div>
					<div style="font-size:12px; font-weight:bold; padding-top:10px; padding-bottom:10px; padding-left:65px;background-color: #FEF9F9;"  ><span style="padding-right:267px;"> <?php echo $timesheet['name']; ?>  </span> <span style="padding-right:38px;"> <?php echo "Total:"; ?> </span> <?php echo "  "; echo $totaltime ; ?> </div> 
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		<br clear = "all">
	</div>


<?php }?>

<?php foreach ($maintenance_tasks as $key => $project) {?>
	<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
		<div class="project_thead thead side-borders-first task_thead" style="line-height:45px">
			<span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;
			<b><?php echo $project['project'];?></b>
   				<div style="float:right;margin-right:12px;" class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >

					<?php 
					if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
					{	?>
						<input  id="chech_<?php echo $key?>" type="checkbox"  name="tasks[]" class="tasks_chk" onclick="checkedAll('<?php echo $key;?>');"/>
	 				<?php }?>
	 			</div>
		</div>
		
		<?php $users = $project['users'];
		uasort($users, $this->build_sorter('username'));?>
		<?php foreach ($users as $key_user => $user) { 
				$timesheets = $user['timesheets'];
				uasort($timesheets, $this->build_sorter('name'));
			?>
			<div class="user" data-id="<?php echo $key_user;?>" data-id_prj="<?php echo $key;?>">
				<span class="user_head thead head task_thead" style="line-height:45px;padding-left:20px;width:93.8%"><span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;<b><?php echo $user['username'];?></b></span>
				
				<div class="user_body body">
					<?php
					foreach ($timesheets as $key_time => $timesheet) { ?>
					<div class="timesheet timesheet_<?php echo $key_time;?> project_<?php echo $key?>" data-id="<?php echo $key_time;?>" data-id_prj="<?php echo $key_time;?>" 
					data-id-user="<?php echo $key_user;?>">
						<?php  $totaltime =0;
							$times = $timesheet['times'];
							uasort($times, $this->build_sorter('task', 'date'));
							foreach ($times as $time) {   
						?>
						<div class="thead task_thead"  id="taskheader" data-id="<?php echo $time['id'];?>"  data-id="<?php echo $time['id'];?>" data-id_timesheet="<?php echo $key_time;?>"
						 data-id_prj="<?php echo $key;?>" data-id-user="<?php echo $key_user;?>">
							<div class="column30 item side-borders side-borders-first no-border margint5">
							</div>
							<div onclick="clicktimesheetgrid(<?php echo $key_time ?> ,<?php echo $key_user  ?>)"  class="column50 item side-borders paddingl30 no-border margint5 red pointer " style="text-align:left">
								<?php echo $timesheet['name'];?>
							</div>
							<div class="column200 item side-borders no-border margint5 paddingl5" style="text-align:left">
								<?php echo $time['task'];?>
							</div>
							<div class="column80 item side-borders no-border margint5 paddingl5">
								<?php echo date("d/m/Y", strtotime($time['date']));?>
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo $time['amount'];?> 
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo Timesheets::getpertask($time['id_task'], $time['id_user'], $time['date']);?> 
							</div>
							<?php $j = strlen($time['comment']);?>
							<div class="column300 item side-borders no-border margint5 paddingl5" style="text-align: left;">
								<?php echo $time['comment'];?>
							</div>
							<div class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >
						 		<?php 
								if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
								{
								?>
						 			<input type="checkbox" name="tasks[]" class="tasks_chk" value="<?php echo $time['id']."-".$time['id_timesheet'];?>"/>
						 		<?php }?>
						 	</div>
						</div>
						<?php
						$totaltime = $totaltime + $time['amount']; ?>

						<?php } ?>
					</div>
					<div style="font-size:12px; font-weight:bold; padding-top:10px; padding-bottom:10px; padding-left:65px;background-color: #FEF9F9;"  ><span style="padding-right:267px;"> <?php echo $timesheet['name']; ?>  </span> <span style="padding-right:38px;"> <?php echo "Total:"; ?> </span> <?php echo "  "; echo $totaltime ; ?> </div> 
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		<br clear = "all">
	</div>


<?php }?>


<?php foreach ($default_tasks as $key => $default_task) {?>
	<div class="project_thead project" data-id="<?php echo $key;?>" style="border-bottom: 1px solid #BABABA;"> 
		<div class="project_thead thead side-borders-first task_thead" style="line-height:45px">
			<span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;
			<b><?php echo $default_task['parent_phase'];?></b>
		</div>
		<?php $users = $default_task['users']; 
		uasort($users, $this->build_sorter('username'));?>
		<?php foreach ($users as $key_user => $user) { 
				$timesheets = $user['timesheets'];
				uasort($timesheets, $this->build_sorter('name'));
			?>
			<div class="user" data-id="<?php echo $key_user;?>" data-id_prj="<?php echo $key;?>">
				<span class="user_head thead head task_thead" style="line-height:45px;padding-left:20px;width:93.8%"><span class="plus-minus " data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;<b><?php echo $user['username'];?></b></span>
				<div style="border-top: 1px solid #BABABA;" class="paddingt15" >
					<?php 
					if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
					{
					?>
						<input id="chech_<?php echo $key?>" type="checkbox"  name="tasks[]" class="tasks_chk" onclick="checkedAll('<?php echo $key;?>');"/>
	 				<?php }?>
	 			</div>
				<div class="user_body body">
					<?php foreach ($timesheets as $key_time => $timesheet) { ?>
					<div class="timesheet timesheet_<?php echo $key_time;?> project_<?php echo $key?>" data-id="<?php echo $key_time;?>" data-id_prj="<?php echo $key_time;?>" 
					data-id-user="<?php echo $key_user;?>">
						<?php 
							$totalhours=0;
							$times = $timesheet['times'];
							uasort($times, $this->build_sorter('name', 'date'));
							foreach ($times as $time) {
								$totalhours+= $time['amount'];
						?>
						<div class="thead task_thead"  id="taskheader" data-id="<?php echo $time['id'];?>"  data-id="<?php echo $time['id'];?>" data-id_timesheet="<?php echo $key_time;?>"
						 data-id_prj="<?php echo $key;?>" data-id-user="<?php echo $key_user;?>">
							<div class="column30 item side-borders side-borders-first no-border margint5">
							</div>
							<div onclick="clicktimesheetgrid(<?php echo $key_time; ?>,<?php echo $key_user  ?>)"  class="red pointer column50 item side-borders paddingl30 no-border margint5" style="text-align:left">
								<?php echo $timesheet['name']; ?>
							</div>
							<div class="column200 item side-borders no-border margint5 paddingl5" style="text-align:left">
								<?php echo $time['name'];?>
							</div>
							<div class="column80 item side-borders no-border margint5 paddingl5">
								<?php echo date("d/m/Y", strtotime($time['date']));?>
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo $time['amount']; ?> 
							</div>
							<div class="column70 item side-borders no-border margint5 " style="border-right:1px solid #bababa">
								<?php echo ' ';//Timesheets::getpertask($time['id_task'], $time['id_user'], $time['date']);?> 
							</div>
							<?php 
								if($time['default']=='1' && $time['id_task']=='11'){ 
								$lieu = Yii::app()->db->createCommand("SELECT date FROM lieu_of WHERE id_user_time=".$time['id']." ")->queryScalar();
								$time['comment']=$time['comment']."  in lieu of  ".substr($lieu, 0,10) ;
								 $j = strlen($time['comment']) ; } else { 
									 $j = strlen($time['comment'] ); 
						}





							?>
							<div class="column300 item side-borders no-border margint5 " style="text-align: left;">
							
							<textarea  spellcheck="false" class="column300 item  no-border" style=" resize:none ; overflow:auto; background-color:white;  " rows="1" cols="50"><?php echo $time['comment'];?></textarea>
							
							</div>
							<div class="checkbox_div column30 item side-borders side-borders-last paddingtb10 no-border margint15" >
						 		<?php 
								if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
								{
								?>
						 			<input type="checkbox" name="tasks[]" class="tasks_chk" value="<?php echo $time['id']."-".$time['id_timesheet'];?>"/>
						 		<?php }?>
						 	</div>
						</div>
						<?php } ?>
						
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		<br clear = "all">
	</div>
<?php }?>
</form>
</div>
<!--  END TIMESHEET TABLE -->
<script type="text/javascript">
$(document).ready(function() {
	if ($('.project').length == 0)
	{
		$('.buttons-section').hide();
	}
	else
	{
		$('.buttons-section').show();
	}
});
</script>