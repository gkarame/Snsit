<div class="email_notif_div">
	<form enctype="multipart/form-data" id="email-notif-form" method="post" action="<?php echo Yii::app()->createAbsoluteUrl('groups/saveEmailNotifications/' . $model->id); ?>">
		<?php echo $this->getEmailNotificationTabs();?>
		<div class="row buttons">
		<?php $visible = true;	if(GroupPermissions::checkPermissions('groups-notifications','write')){	$visible = false;	?>
			<a onclick="saveNotif();" href="javascript:void(0);" class="saveBtn" id="save-notif"><?php echo Yii::t('translation', 'Save');?></a>
			<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
		<?php } ?>
		</div>
	</form>
</div>
<script type="text/javascript">
function saveNotif(){
	$.ajax({
 		type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('groups/saveEmailNotifications', array('id'=>$model->id));?>", 
	  	dataType: "json", data: $('#email-notif-form').serialize(),
	  	success: function(data) { if (data.status == "success") { closeTab(configJs.current.url); } } }); }
</script>
