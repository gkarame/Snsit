<div class="tache new"><div id="support" class="bg" ></div><fieldset class="new_milestonen"><?php $id = $model->isNewRecord ? 'new' : $model->id;?>
<div class="textBox  inline-block first" style="width:650px;"><div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]milestone"); ?></div>
<div class="input_text"><?php echo CHtml::activeTextField($model,"[$id]milestone", array('style'=>'width:640px;')); ?></div>
<?php echo CCustomHtml::error($model, "[$id]milestone",array('id'=>"StatusReportMilestones".$id."_milestone_em_")); ?></div>
		<div style="right:75px;top:80%;" class="save" onclick="updateMilestone(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;top:80%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache-milestonen').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid-milestonesn');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div></fieldset></div>