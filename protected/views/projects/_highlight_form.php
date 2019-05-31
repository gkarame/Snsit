<div class="tache new">	<div id="support" class="bg" ></div><fieldset class="new_highlightn">
	<?php $id = $modelhighlight->isNewRecord ? 'new' : $modelhighlight->id;	?>
		<div class="textBox  inline-block first" style="width:650px;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($modelhighlight,"[$id]description"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($modelhighlight,"[$id]description", array('style'=>'width:640px;')); ?>	</div>
			<?php echo CCustomHtml::error($modelhighlight, "[$id]description",array('id'=>"StatusReportHighlights".$id."_description_em_")); ?>	</div>		
		<div style="right:75px;top:80%;" class="save" onclick="updateHighlight(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;top:80%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache-highlightn').show();$(this).parents('.tache.new').remove();<?php if (!$modelhighlight->isNewRecord) {?>$.fn.yiiGridView.update('items-grid-highlightsn');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div></fieldset></div>