<?php if(GroupPermissions::checkPermissions('groups-permissions','write')){ ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#save-perm').click(function(){	var id = <?php echo $model->id;?>;		var send = $('#group-permissions-form').serializeArray();
			send.push({name: 'id', value: id});
			$.ajax({
		 		type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('groups/savePermissions');?>", dataType: "json", data: send,
			  	success: function(data) {
			  		if (data.status != "success") { alert('The action requested couldn\'t be completed'); }
			  		else{ closeTab(configJs.current.url); } } }); }); });
</script>
<?php } ?>
<div class="permissionsContent mytabs hidden">
	<form enctype="multipart/form-data" id="group-permissions-form" method="post" action="<?php echo Yii::app()->createAbsoluteUrl('groups/savePermissions/' . $model->id); ?>">
		<?php $this->widget('zii.widgets.jui.CJuiTabs', array('tabs'=> Permissions::getTabs(),'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',
		    'options'=>array('collapsible'=>false,),));		
		if(GroupPermissions::checkPermissions('groups-permissions','write')){ ?>
		<div class="row buttons">
			<a href="javascript:void(0);" id="save-perm" class="saveBtn"><?php echo Yii::t('translation', 'Save');?></a>
			<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
		</div>
		<?php } ?>
	</form>
</div>