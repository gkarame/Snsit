<div class="generalTasksTickets"><div class="tache new" style="width:99%"><div class="bg"></div><fieldset class="new_milestone create"><?php $id = $model->id;?>
<div class="textBox inline-block itms amt" ><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "status"); ?></div>
<div class="input_text"><div class="hdselect">
<?php echo CHtml::activeDropDownList($model, "status",  array('Open'=>'Open','Completed'=>'Completed','N/A'=>'N/A'), array('class'=>'input_text_value')); ?>
</div></div><?php echo CCustomHtml::error($model, "status",array('id'=>"status")); ?></div>
<?php if ($model->id) { ?>	<input type="hidden" name="id_project" value="<?php echo $id_project;?>" />	<?php } ?>
	<div style="right:141px; top:46px;" class="save" onclick="saveChecklist(this,<?php echo $id?>); "><u><b>SAVE</b></u></div>
	<div style="color:#333;right:77px; top:46px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('checklist-grid');<?php }?>"><u><b>CANCEL</b></u></div>
		</fieldset>	</div></div>