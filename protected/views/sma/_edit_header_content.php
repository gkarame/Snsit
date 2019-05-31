<div class="bg sma_bg"><?php $can_modify=true; ?> </div><fieldset id="header_fieldset">
	<?php if(GroupPermissions::checkPermissions('general-sma','write')){ ?>	<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "status", Sma::getStatusList($model->status), array('class'=>'input_text_value')); ?>
			</div></div><?php echo CCustomHtml::error($model, "status", array('id'=>"Sma_status_em_")); ?></div>
<?php if ($model->status !=3 ) {?> <div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"assigned_to"); ?></div>
		<div class="input_text"><div class="hdselect">	<?php echo CHtml::activeDropDownList($model, "assigned_to", Sma::getAllActiveUsers(), array('class'=>'input_text_value')); ?>
			</div>	</div>	<?php echo CCustomHtml::error($model, "assigned_to", array('id'=>"Sma_assigned_to_em_")); ?></div><?php } ?>
	<div class="textBox three-smaller inline-block itms">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "instructions"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($model, "instructions", array('class'=> 'input_text_value')); ?>
			</div><?php echo CCustomHtml::error($model, "instructions", array('id'=>"Sma_instructions_em_"));  ?></div>	<?php } ?>
<?php if(!GroupPermissions::checkPermissions('general-sma','write') && $model->status==1){ ?>
	<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text">	<div class="hdselect">	<?php echo CHtml::activeDropDownList($model, "status", Sma::getStatusListUser($model->status), array('class'=>'input_text_value')); ?>
			</div>	</div>	<?php echo CCustomHtml::error($model, "status", array('id'=>"Sma_status_em_")); ?></div>	<?php } ?>
	<div class="textBox three-smaller inline-block itms">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "notes"); ?></div>
			<div class="input_text">	<?php echo CHtml::activeTextField($model, "notes", array('class'=> 'input_text_value')); ?>		</div>
			<?php echo CCustomHtml::error($model, "notes", array('id'=>"Sma_notes_em_"));  ?></div>	
	<img src="<?php echo Yii::app()->getBaseUrl().'/images/loader.gif';?>" class="hidden" id="img"  style=<?php if(GroupPermissions::checkPermissions('general-sma','write')) {?>"padding-left:190px;padding-top:40px;"<?php }else{?>"padding-left:765px;padding-top:40px;"<?php }?>/ >
	<div style="right:65px; padding-top:75px;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style=" left:853px; color:#333; padding-top:75px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>