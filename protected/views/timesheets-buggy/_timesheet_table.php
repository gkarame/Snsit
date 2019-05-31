<!--  TIMESHEET TABLE -->
<?php if(strtotime($model->week_start)< strtotime('2015-08-23')) {?>
	<div class="thead"> 
		<div class="column250 item side-borders side-borders-first bold">Project Name</div>
		<div class="column80 item side-borders bold weekendtit">MON <?php echo date('j', strtotime($model->week_start))?></div>
		<div class="column80 item side-borders bold">TUE <?php echo date('j', strtotime($model->week_start . ' + 1 day'))?></div>
		<div class="column80 item side-borders bold">WED <?php echo date('j', strtotime($model->week_start . ' + 2 day'))?></div>
		<div class="column80 item side-borders bold">THU <?php echo date('j', strtotime($model->week_start . ' + 3 day'))?></div>
		<div class="column80 item side-borders bold">FRI <?php echo date('j', strtotime($model->week_start . ' + 4 day'))?></div>
		<div class="column80 item side-borders bold">SAT <?php echo date('j', strtotime($model->week_start . ' + 5 day'))?></div>
		<div class="column80 item side-borders bold weekendtit">SUN <?php echo date('j', strtotime($model->week_start . ' + 6 day'))?></div>
		<div class="column80 item side-borders side-borders-last bold">TOTAL</div>
	</div>
 <?php } else {?>
	<div class="thead"> 
		<div class="column250 item side-borders side-borders-first bold">Project Name</div>
		<div class="column80 item side-borders bold <?php if(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452') { ?> weekendtit <?php } ?>">SUN <?php echo date('j', strtotime($model->week_start))?></div>
		<div class="column80 item side-borders bold">MON <?php echo date('j', strtotime($model->week_start . ' + 1 day'))?></div>
		<div class="column80 item side-borders bold">TUE <?php echo date('j', strtotime($model->week_start . ' + 2 day'))?></div>
		<div class="column80 item side-borders bold">WED <?php echo date('j', strtotime($model->week_start . ' + 3 day'))?></div>
		<div class="column80 item side-borders bold">THU <?php echo date('j', strtotime($model->week_start . ' + 4 day'))?></div>
		<div class="column80 item side-borders bold <?php if(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452') { ?> weekendtit <?php } ?>">FRI <?php echo date('j', strtotime($model->week_start . ' + 5 day'))?></div>
		<div class="column80 item side-borders bold weekendtit">SAT <?php echo date('j', strtotime($model->week_start . ' + 6 day'))?></div>
		<div class="column80 item side-borders side-borders-last bold">TOTAL</div>
	</div> <?php } ?>
	<div class="project_thead hide"></div>
	<?php $showPrj = 0; $prjArr = array(); $taskArr = array(); $prj = 0;?>
	

	<?php foreach($data as $task) { 
		
	
		if ($task['id_project'] != $prj && !in_array($task['id_project'], $prjArr)) { 
		
		$prj = $task['id_project']; array_push($prjArr, $task['id_project']);
	 ?>
			<div class="thead project_thead"> 
				<div class="column250 item side-borders side-borders-first projectName">
					<span class="deleteFromTimesheet" data-id="<?php echo $task['id_project']; ?>"></span>&nbsp;&nbsp;&nbsp;
					<span class="plus-minus" data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;
					<?php echo $task['project_name']?>
				</div>
				<?php $taskCertainDate = $model->week_start; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders <?php if(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452') { ?> weekend <?php } ?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<?php $taskCertainDate = $model->week_start . ' + 1 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<?php $taskCertainDate = $model->week_start . ' + 2 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<?php $taskCertainDate = $model->week_start . ' + 3 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<?php $taskCertainDate = $model->week_start . ' + 4 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<?php $taskCertainDate = $model->week_start . ' + 5 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders <?php if(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452') { ?> weekend <?php } ?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<?php $taskCertainDate = $model->week_start . ' + 6 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project'], $taskCertainDate); ?>
				<div class="column80 item side-borders weekend" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>"><?php echo $resources; ?></div>
				<div class="column80 item side-borders side-borders-last"><?php $resources = Timesheets::getCurrentProjectTotalTime($model->id, $task['id_project']); ?><?php echo $resources; ?></div>
			</div>
		<?php }   ?>
		
		<?php if (!in_array($task['id_task'], $taskArr)) { array_push($taskArr, $task['id_task']); ?>
			<div class="thead task_thead" data-id="<?php echo $task['id_task'];?>" data-default="0" data-billable="<?php echo $task['task_billabillity'];?>">
				<div class="column250 item side-borders side-borders-first"><span class="deleteFromTimesheet" data-id="<?php echo $task['id_task']; ?>"></span>&nbsp;&nbsp;&nbsp;<?php echo $task['task_description']?></div>
					<div class="column80 item side-borders <?php if(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452') { ?> weekendtask <?php } ?>">
						<?php $taskCertainDate = $model->week_start; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>
					<div class="column80 item side-borders">
						<?php $taskCertainDate = $model->week_start . ' + 1 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>
					<div class="column80 item side-borders">
						<?php $taskCertainDate = $model->week_start  . ' + 2 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>
					<div class="column80 item side-borders">
						<?php $taskCertainDate = $model->week_start  . ' + 3 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>
					<div class="column80 item side-borders">
						<?php $taskCertainDate = $model->week_start  . ' + 4 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>
					<div class="column80 item side-borders <?php if(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452') { ?> weekendtask <?php } ?>">
						<?php $taskCertainDate = $model->week_start  . ' + 5 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>
					<div class="column80 item side-borders weekendtask">
						<?php $taskCertainDate = $model->week_start  . ' + 6 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task'], $taskCertainDate); ?>
						<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-comment="<?php echo $resources['comment']; ?>" />
					</div>	
				<div class="column80 item side-borders side-borders-last"><span><?php echo Timesheets::getUserTimeOnTaskByDay($model->id, $task['id_task']); ?></span></div>
			</div>
		<?php } ?>
	<?php }?>		
	<?php $parentsArr = array(); ?>
	<?php foreach($default_tasks as $default_task) { ?>
		<?php if(!in_array($default_task['parent'], $parentsArr)) { array_push($parentsArr, $default_task['parent']); ?>
			<div class="thead project_thead default"> 
				<div class="column250 item side-borders side-borders-first">
					<!--<span class="deleteFromTimesheet" data-id="<?php /*echo $default_task['id_project'];*/ ?>"></span>&nbsp;&nbsp;&nbsp;-->
					<span class="plus-minus" data-collapsed="1" href="javascript:void(0)"></span>&nbsp;&nbsp;&nbsp;
					<?php echo $default_task['parent']?>
				</div>
				<?php $taskCertainDate = $model->week_start; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders <?php if(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452') { ?> weekendtask <?php } ?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<?php $taskCertainDate = $model->week_start . ' + 1 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<?php $taskCertainDate = $model->week_start . ' + 2 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<?php $taskCertainDate = $model->week_start . ' + 3 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<?php $taskCertainDate = $model->week_start . ' + 4 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<?php $taskCertainDate = $model->week_start . ' + 5 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders  <?php if(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452') { ?> weekendtask <?php } ?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<?php $taskCertainDate = $model->week_start . ' + 6 day'; $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], $taskCertainDate, 1); ?>
				<div class="column80 item side-borders weekendtask" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate)); ?>">
					<?php echo $resources; ?>
				</div>
				<div class="column80 item side-borders side-borders-last">
					<?php $resources = Timesheets::getCurrentProjectTotalTime($model->id, $default_task['id_project'], '', 1); ?>
					<?php echo $resources; ?>
				</div>
			</div>
		<?php } ?>
		<div class="thead task_thead default" data-id="<?php echo $default_task['id'];?>" data-default="1" data-billable="<?php echo $default_task['billable'];?>">
			<div class="column250 tss item side-borders side-borders-first" style="line-height:14px;"><!--<span class="deleteFromTimesheet" data-id="<?php /*echo $default_task['id']; */?>"></span>&nbsp;&nbsp;&nbsp;--><?php echo $default_task['name']?></div>
				<div class="column80 tss item side-borders <?php if(Users::getBranchByUser($model->id_user)!='56' && Users::getBranchByUser($model->id_user)!='452') { ?> weekendtask <?php } ?>">
					<?php $taskCertainDate = $model->week_start; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-comment="<?php echo $resources['comment']; ?>" />
				</div>
				<div class="column80 tss item side-borders ">
					<?php $taskCertainDate = $model->week_start . ' + 1 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>"  data-comment="<?php echo $resources['comment']; ?>" />
				</div>
				<div class="column80 tss item side-borders ">
					<?php $taskCertainDate = $model->week_start . ' + 2 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>"  data-comment="<?php echo $resources['comment']; ?>" />
				</div>
				<div class="column80 tss item side-borders ">
					<?php $taskCertainDate = $model->week_start . ' + 3 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>"  data-comment="<?php echo $resources['comment']; ?>" />
				</div>
				<div class="column80 tss item side-borders ">
					<?php $taskCertainDate = $model->week_start . ' + 4 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-comment="<?php echo $resources['comment']; ?>" />
				</div>
				<div class="column80 tss item side-borders  <?php if(Users::getBranchByUser($model->id_user)=='56' || Users::getBranchByUser($model->id_user)=='452') { ?> weekendtask <?php } ?>">
					<?php $taskCertainDate = $model->week_start . ' + 5 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>"  data-comment="<?php echo $resources['comment']; ?>" />
				</div>
				<div class="column80 tss item side-borders  weekendtask">
					<?php $taskCertainDate = $model->week_start . ' + 6 day'; $resources = Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], $taskCertainDate, 1); ?>
					<input type="text" readonly="readonly" name="" value="<?php echo $resources['amount'];?>" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate))?>" data-lieu-of="<?php echo Timesheets::getLieuOfDate($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>" data-ticket="<?php echo Timesheets::getTicketByUTID($default_task['id'], date('Y-m-d H:i:s', strtotime($taskCertainDate))); ?>"  data-comment="<?php echo $resources['comment']; ?>" />
				</div>	
			<div class="column80 tss item side-borders side-borders-last"><span><?php echo Timesheets::getUserTimeOnTaskByDay($model->id, $default_task['id'], '', 1); ?></span></div>
		</div>
	<?php }?>
	<div class="project_thead hide"></div>
	<div class="thead total_hours">
		<div class="column250 item side-borders side-borders-first bold">Total</div>
			<?php $taskCertainDate = $model->week_start; ?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start)?></div>
			<?php $taskCertainDate = $model->week_start . ' + 1 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start . ' + 1 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 2 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start . ' + 2 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 3 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start . ' + 3 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 4 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start . ' + 4 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 5 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start . ' + 5 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 6 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalTimes($model->id, $model->week_start . ' + 6 day')?></div>
		<div class="column80 item side-borders side-borders-last "><?php echo Timesheets::getTotalTimes($model->id)?></div>
	</div>
	<div class="thead total_billable">
		<div class="column250 item side-borders side-borders-first bold">Total Billable</div>
			<?php $taskCertainDate = $model->week_start; ?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start)?></div>
			<?php $taskCertainDate = $model->week_start . ' + 1 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 1 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 2 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 2 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 3 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 3 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 4 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 4 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 5 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 5 day')?></div>
			<?php $taskCertainDate = $model->week_start . ' + 6 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 6 day')?></div>
		<div class="column80 item side-borders side-borders-last"><?php echo Timesheets::getTotalBillableTimes($model->id)?></div>
	</div>
	<div class="thead billability">
		<div class="column250 item side-borders side-borders-first bold">Billabillity %</div>
			<?php $taskCertainDate = $model->week_start; ?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start), Timesheets::getTotalTimes($model->id, $model->week_start))?></div>
			<?php $taskCertainDate = $model->week_start . ' + 1 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 1 day'), Timesheets::getTotalTimes($model->id, $model->week_start . ' + 1 day'))?></div>
			<?php $taskCertainDate = $model->week_start . ' + 2 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 2 day'), Timesheets::getTotalTimes($model->id, $model->week_start . ' + 2 day'))?></div>
			<?php $taskCertainDate = $model->week_start . ' + 3 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 3 day'), Timesheets::getTotalTimes($model->id, $model->week_start . ' + 3 day'))?></div>
			<?php $taskCertainDate = $model->week_start . ' + 4 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 4 day'), Timesheets::getTotalTimes($model->id, $model->week_start . ' + 4 day'))?></div>
			<?php $taskCertainDate = $model->week_start . ' + 5 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 5 day'), Timesheets::getTotalTimes($model->id, $model->week_start . ' + 5 day'))?></div>
			<?php $taskCertainDate = $model->week_start . ' + 6 day';?>
			<div class="column80 item side-borders" data-date="<?php echo date('Y-m-d H:i:s', strtotime($taskCertainDate));?>"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id, $model->week_start . ' + 6 day'), Timesheets::getTotalTimes($model->id, $model->week_start . ' + 6 day'))?></div>
		<div class="column80 item side-borders side-borders-last"><?php echo Timesheets::calculateBillability(Timesheets::getTotalBillableTimes($model->id), Timesheets::getTotalTimes($model->id))?></div>
	</div><?php Timesheets::getStatusFunc($model->id)?>
<!--  END TIMESHEET TABLE -->
