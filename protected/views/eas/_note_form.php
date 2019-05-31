<div class="tache new notte">
	<fieldset class="note_fieldset">
		<?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox amt inline-block">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]Note"); ?></div>
			<div class="input_text width240">
				<?php echo CHtml::activeTextField($model, "[$id]id_note", array('class'=> 'input_text_value width234')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]id_note", array('id'=>"EasNotes".$id."_note_")); ?>
		</div>	
			<input type="hidden" name="EasNotes[new][id_ea]" value="<?php echo $id_ea;?>" />	
		<div style="right:75px;" class="save" onclick="createNote(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;" class="save" onclick="$('.notte').hide(); $('.new_note').show();"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>

