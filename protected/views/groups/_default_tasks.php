<div class="email_notif_div">
	<form enctype="multipart/form-data" id="default-tasks-form" method="post" action="<?php echo Yii::app()->createAbsoluteUrl('groups/saveDefaultTasks/' . $model->id); ?>">
		<?php echo $this->getDefaultTasksTabs();?>
		<div class="row buttons">
			<a onclick="saveDefaults();" href="javascript:void(0);" class="saveBtn" id="save-defaults"><?php echo Yii::t('translation', 'Save');?></a>
			<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
		</div>
	</form>
</div>
<script type="text/javascript">
function saveDefaults(){
	$.ajax({
 		type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('groups/saveDefaultTasks', array('id'=>$model->id));?>", 
	  	dataType: "json", data: $('#default-tasks-form').serialize(),
	  	success: function(data) {
	  		if (data.status == "success") { closeTab(configJs.current.url); }else{
		  		delDefaultTaskMess(data.id_to_delete,data.users); } } });
}
function changeBillable(id_default_task,value){
	$.ajax({
 		type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('groups/changeDefaultTasks');?>", 
	  	dataType: "json", data: {'id_default_task':id_default_task,'value':value},
	  	success: function(data) {
	  		if (data.status == "success") { } }	});
}
function delDefaultTaskMess(id_tasks,users_id){
	buttons_id = {
	        "YES": {
	        	class: 'yes_button',
	        	click: function(){ deleteTasks(id_tasks,users_id); $( this ).dialog( "close" ); }
	        },
	        "NO": {
	        	class: 'no_button',
	        	click: function(){ deleteTasks(id_tasks,users_id,<?php echo $model->id?>); $( this ).dialog( "close" ); }
	        }
	}
	custom_alert("DELETE MESSAGE", "This tasks has some time entries assigned to it which will be lost if deleted, are you sure you want to delete tasks?", buttons_id);
}
function deleteTasks(id_tasks,users_id,id_group){
	$.ajax({
 		type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('groups/deleteDefaultTasks');?>", 
	  	dataType: "json", data: {'id_tasks':id_tasks,'users':users_id,'id_group':id_group},
	  	success: function(data) { if (data.status == "success") { closeTab(configJs.current.url); } }	});
}
</script>