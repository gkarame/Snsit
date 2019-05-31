<div class="tache new term">	
	<fieldset class="terms_fieldset">
		<?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox amt inline-block">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]payment %"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model, "[$id]payment_term", array('class'=> 'input_text_value', 'onblur' => 'addPercent(this);return;',
						'onclick' => "removePercent(this);return;",)); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]payment_term", array('id'=>"EaPaymentTerms_".$id."_payment_term_em_"));  ?>
		</div>
		<div class="textBox itms amt inline-block">
			<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"[$id]milestone"); ?></div>			
			<div class="input_text">
				<div class="hdselect">
					<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
							'model' => $model,	'attribute' => "[$id]milestone",'source'=>EaPaymentTerms::getAllAutocomplete(),	'id'=>'nsecSU',
							'options'=>array(
								'minLength'=>'0',	'showAnim'=>'fold',
								'select'=>"js:function(event, ui) {	console.log('dd');	$('#EaPaymentTerms_milestone').val(ui.item.id);	}",	),
							'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');console.log('a')",), ));	?>
				</div>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]milestone", array('id'=>"EaPaymentTerms_".$id."_milestone_em_")); ?>		
		</div>
		<?php if ($model->id) { ?>
				<input type="hidden" name="EaPaymentTerms[<?php echo $id;?>][id]" value="<?php echo $model->id;?>" />
		<?php } ?>
		<div style="right:115px;" class="save" onclick="updateTermSandU(this, '<?php echo $id;?>');showItemFormNewSU(this, true);return false;"><u><b>NEW</b></u></div>
		<div style="right:75px;" class="save" onclick="updateTermSandU(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('sanduterms-grid');panelClip('.term_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>
