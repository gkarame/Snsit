<input type="hidden" name="verify" value="1" />
<?php $parentArr = array(); 
	 foreach ($tasks as $task) {
	 if(!in_array($task['id_parent'], $parentArr)) { $parentArr[] = $task['id_parent']; ?>
		<h2 class="uppercase marginb0 margint55"><?php echo $task['parent'];?>:</h2>
	<?php } ?>
	<div class="row chk paddingl21" onclick="CheckOrUncheckInput(this)">
		<div class="input"></div>
		<input type="checkbox" name="checked[]" value="<?php echo $task['id'];?>" 
		<?php echo DefaultTasksGroup::isActivated($id_group, $task['id']) ? 'checked' : '';?> />
		<label style="width:20%"><?php echo $task['name'];?></label>		
	</div>
		<div class="margint10 billable_default_task" ><?php echo CHtml::dropDownList('bill', $task['billable'], array("Yes"=>"Yes",'No' =>"No",'N/A' =>"N/A"),array('value'=>$task['billable'],'onchange'=>'changeBillable('.$task['id'].',value)'));?></div>
<?php } ?>
