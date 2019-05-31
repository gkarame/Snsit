<div class="tache new">	<fieldset class="new_visa">	<?php $id = $visa->isNewRecord ? 'new' : $visa->id;?>
		<div class="textBox one inline-block">		<div class="input_text_desc"><?php echo CHtml::activelabelEx($visa,"[$id]type"); ?></div>
			<div class="input_text"><div class="hdselect">
					<?php echo CHtml::activeDropDownList($visa,"[$id]type", array('passport'=>Yii::t('translations', 'Passport'), 'visa' => Yii::t('translations', 'Visa'), 'driving license' => Yii::t('translations', 'Driving license'))); ?>
				</div>		</div>	<?php echo CCustomHtml::error($visa, "[$id]type", array('id'=>"UserVisas_".$id."_type_em_")); ?>
		</div>	<div class="textBox  inline-block time" style="cursor:pointer;">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($visa,"[$id]expiry_date"); ?></div>
			<div class="input_text">       <?php     $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $visa,'attribute' => "[$id]expiry_date", 'cssFile' => false,
			        'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'), 	'htmlOptions' => array('class' => 'input_text_value datefield'   	),    ));	?>
				<div class="calendar calto"></div>	</div>	<?php echo CCustomHtml::error($visa, "[$id]expiry_date", array('id'=>"UserVisas_".$id."_expiry_date_em_")); ?>
		</div><div class="textBox one inline-block" ><div class="input_text_desc"><?php echo CHtml::activelabelEx($visa,"[$id]visa_type"); ?></div>
			<div class="input_text"><div class="hdselect">	<?php echo CHtml::activeDropDownList($visa, "[$id]visa_type", array('single'=>Yii::t('translations', 'Single'), 'multiple' => Yii::t('translations', 'Multiple'), 'residency' => Yii::t('translations', 'Residency'))); ?>
				</div>	</div>	<?php echo CCustomHtml::error($visa, "[$id]visa_type", array('id'=>"UserVisas_".$id."_visa_type_em_")); ?>
		</div>	<div class="textBox two inline-block amt">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($visa, "[$id]duration_of_stay"); ?></div>
			<div class="input_text">	<?php echo CHtml::activeTextField($visa, "[$id]duration_of_stay", array('class'=>'input_text_value')); ?>
			</div>	<?php echo CCustomHtml::error($visa, "[$id]duration_of_stay", array('id'=>"UserVisas_".$id."_duration_of_stay_em_")); ?>
		</div>   <div class="textBox one inline-block" style="margin-right:5px;float: left;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($visa,"[$id]country"); ?></div>
			<div class="input_text"><div class="hdselect">		<?php echo CHtml::activeDropDownList($visa, "[$id]country", Codelkups::getCodelkupsDropDown('country') ); ?>
				</div>		</div>	<?php echo CCustomHtml::error($visa, "[$id]country", array('id'=>"UserVisas_".$id."_visa_type_em_")); ?>
		</div>	<div class="textBox inline-block three-vis" style="float: left;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($visa, "[$id]notes"); ?></div>
			<div class="input_text">	<?php echo CHtml::activeTextField($visa,  "[$id]notes", array('class'=>'input_text_value')); ?>
			</div>	<?php echo CCustomHtml::error($visa,  "[$id]notes", array('id'=>"UserVisas_".$id."_notes_em_")); ?>
		</div>	<?php if ($visa->id_user) { ?>	<input type="hidden" name="UserVisas[<?php echo $id;?>][id_user]" value="<?php echo $visa->id_user;?>" />
		<?php } ?>	<?php if ($visa->id) { ?>	<input type="hidden" name="UserVisas[<?php echo $id;?>][id]" value="<?php echo $visa->id;?>" />
		<?php } ?>	<div style="right:94px;" class="save" onclick="<?php echo ($update) ? 'saveVisa(this, \''.$id.'\');' : 'js:submitForm();return false;'?>"><u><b>SAVE</b></u></div>
	<div style="color:#333;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$visa->isNewRecord) {?>$.fn.yiiGridView.update('visas-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset></div>