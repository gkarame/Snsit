<div class="generalTasksTickets"><div class="tache new" style="width:99%"><div class="bg"></div><fieldset class="new_milestone create">
<?php $id = $model->id;?><div class="textBox inline-block itms amt" ><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "status"); ?></div>
<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "status",  array('Pending'=>'Pending','In Progress'=>'In Progress','Closed'=>'Closed'), array('class'=>'input_text_value')); ?>
</div></div><?php echo CCustomHtml::error($model, "status",array('id'=>"status"));  ?>	</div>
<div class="textBox inline-block itms amt" ><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "applicable"); ?></div>
<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "applicable",  array('Yes'=>'Yes','No'=>'No'), array('class'=>'input_text_value')); ?></div> 
</div><?php echo CCustomHtml::error($model, "applicable",array('id'=>"applicable")); ?>		</div>
<div class="textBox inline-block itms amt row" style="float:none;" >	<div class="input_text_desc padding_smaller">
<?php echo CHtml::activelabelEx($model, "estimated_date_of_completion",array('class'=>"paddigb0")); ?>	</div>	<div class="input_text">	
<?php  $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'estimated_date_of_completion','cssFile' => false,
'options'=>array('id' => 'estimated_date_of_completion','dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'),
'htmlOptions' => array('class' => 'input_text_value datefield'),));	?>	<div class="calendar calto"></div></div>
<?php echo CCustomHtml::error($model, 'estimated_date_of_completion', array('id'=>"estimated_date_of_completion")); ?>	</div>			
<?php if ($model->id) { ?>	<input type="hidden" name="id_project" value="<?php echo $id_project;?>" />	<?php } ?>
<div style="right:141px; top:46px;" class="save" onclick="saveMilestone(this,<?php echo $id?>); "><u><b>SAVE</b></u></div>
<div style="color:#333;right:77px; top:46px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('milestones-grid');<?php }?>"><u><b>CANCEL</b></u></div>
</fieldset>	</div></div>