<?php $tm=Yii::app()->db->createCommand("SELECT TM from eas WHERE id=".$model->id_ea."")->queryScalar();
$train = Yii::app()->db->createCommand("select  tra.course_name from training_eas tea join trainings_new_module tra on  tea.id_training = tra.idTrainings where tea.id_ea =".$model->id_ea."")->queryScalar();
$ty = Yii::app()->db->createCommand("select  tra.type from training_eas tea join trainings_new_module tra on  tea.id_training = tra.idTrainings where tea.id_ea =".$model->id_ea."")->queryScalar(); ?> 
<div class="tache new">		
		<fieldset class="items_fieldset">
		<?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox three-smaller inline-block itms">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]description"); ?></div>
			<div class="">
				<?php if ($model->ea->category == 24){	 $str = $model->ea->description;
				}else if (!empty($model->id)) {	$str = $model->description;
				}else{	$str=''; }
				echo CHtml::activeTextArea($model, "[$id]description", array('value'=> $str,'class'=> 'eadesc',($model->ea->category != 24)? '':'readOnly','id'=>$model->id.'descr')); ?>
				<?php if ($model->ea->category == 24) {?>
				<input type="hidden" name="training_course" id="training_course" value="<?php echo $train?>" />
				<?php }?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]description", array('id'=>"EasItems_".$id."_description_em_")); ?>
		</div>
		<?php if (!empty($model->settings_codelist) && $model->ea->category != 623) { ?>
			<div class="textBox inline-block itms amt ">
				<div class="input_text_desc padding_smaller"><label class="required"><?php echo $model->settingsCodelist->label; ?><span class="required">*</span></label></div>
				<div class="input_text">
					<div class="hdselect">
						<?php echo CHtml::activeDropDownList($model, "[$id]settings_codelkup",  Codelkups::getCodelkupsDropDown($model->settings_codelist), array('value'=> ($model->ea->category == 24)?$train:"",'disabled' => ($model->ea->category == 24)? 'disabled':"" ,'prompt' => Yii::t('translations', 'Choose ' . $model->settingsCodelist->label), 'class'=>'input_text_value' , 'onChange' => 'js:updateDuration(this.value,'.$model->id.')' )); ?>
					</div> 
				</div>
				<?php echo CCustomHtml::error($model, "[$id]settings_codelkup", array('id'=>"EasItems_".$id."_settings_codelkup_em_")); ?>
			</div>
			<?php }		
			if(Customers::getCountryById(Eas::getCustomerByEA($model->id_ea)) == '398'){ ?>
			<div class="textBox inline-block itms amt " style="padding-left:13px;">
				<div class="input_text_desc padding_smaller"><label class="required"><?php echo "Offshore"; ?><span class="required">*</span></label></div>
				<div class="input_text width85">
					<div class="hdselect">
						<?php echo CHtml::activeDropDownList($model, "[$id]offshore",  EasItems::getOffshoreDropdown(), array( 'class'=>'width81' )); ?>
					</div> 
				</div>
				<?php echo CCustomHtml::error($model, "[$id]offshore", array('id'=>"EasItems_".$id."_offshore_em_")); ?>
			</div>
			<?php }	

		 if ($model->ea->category == 25 ) { 
		if($model->ea->category != 453 && $model->ea->category != 496 && $model->ea->category != 623) {?>
			<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label class="required"><?php echo $labels['man_days'];?><span class="required">*</span></label></div>
				<div class="input_text">
					<?php echo CHtml::activeTextField($model, "[$id]man_days", array('class'=> 'input_text_value man_days', 'onKeyUp' => 'js:changeManDayRate(this);')); ?>
				</div>
				<?php echo CCustomHtml::error($model, "[$id]man_days", array('id'=>"EasItems_".$id."_man_days_em_")); ?>
			</div>
			<?php } ?>
			<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label class="required"><?php echo $labels['amount'];?><span class="required">*</span></label></div>
				<div class="input_text">
					<?php echo CHtml::activeTextField($model, "[$id]amount", array('class'=> 'input_text_value amount', 'onKeyUp' => 'js:changeManDayRate(this);')); ?>
				</div>
				<?php echo CCustomHtml::error($model, "[$id]amount", array('id'=>"EasItems_".$id."_amount_em_")); ?>
			</div>
			<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label><?php echo Yii::t('translations', 'Currency'); ?></label></div>
				<div class="input_text">
					<span class="no_input"><?php echo $model->ea->eCurrency->codelkup;?></span>
				</div>
			</div>
			<?php  if($model->ea->category != 454 && $model->ea->category != 496 && $model->ea->category != 623) {?>
			<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label class="required"><?php echo "S&U%";?><span class="required">*</span></label></div>
				<div class="input_text">
					<?php echo CHtml::activeTextField($model, "[$id]sandu", array('class'=> 'input_text_value sandu','onKeyUp' => 'js:changeManDayRate(this);','id'=>'sandu_field')); ?>
				</div>
			</div>
		<div class="textBox inline-block itms amt">
			<div class="input_text_desc"><label><label><?php echo "S&U Total";?></label></label></div>
			<div class="input_text">    
				<span class="no_input sandu_total"><?php echo Utils::formatNumber($model->getSUTotal());?></span>
			</div>
		</div> <?php } }else{ if($tm!=1){ ?> 
			<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label class="required"><?php echo $labels['amount'];?><span class="required">*</span></label></div>
				<div class="input_text">
					<?php echo CHtml::activeTextField($model, "[$id]amount", array('class'=> 'input_text_value amount', 'onKeyUp' => 'js:changeManDayRate(this);')); ?>
				</div>
				<?php echo CCustomHtml::error($model, "[$id]amount", array('id'=>"EasItems_".$id."_amount_em_")); ?>
			</div>
			<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label><?php echo Yii::t('translations', 'Currency'); ?></label></div>
				<div class="input_text">
					<span class="no_input"><?php echo $model->ea->eCurrency->codelkup;?></span>
				</div>
			</div> 
			<div class="textBox inline-block itms amt">
			<?php if($model->ea->category != 454 && $model->ea->category != 496 && $model->ea->category != 623) { ?>
				<div class="input_text_desc"><label class="required"><?php if($model->ea->category=='24'){ echo "# of participants"; }else{ echo $labels['man_days'];}?><span class="required">*</span></label></div>
				<div class="input_text">
					<?php echo CHtml::activeTextField($model, "[$id]man_days", array('class'=> 'input_text_value man_days', 'onKeyUp' => 'js:changeManDayRate(this);')); ?>
				</div>
				<?php echo CCustomHtml::error($model, "[$id]man_days", array('id'=>"EasItems_".$id."_man_days_em_")); ?>
			</div>
		<?php } }		
		} 	if($tm!=1){?> 
		<div class="textBox inline-block itms amt">
			<?php if($model->ea->category != 454 && $model->ea->category != 496 &&  $model->ea->category != 24 &&  $model->ea->category != 623) { ?>
			<div class="input_text_desc"><label><label><?php echo $labels['man_day_rate'];?></label></label></div>
			<div class="input_text">
				<span class="no_input man_day_rate"><?php echo Utils::formatNumber($model->getManDayRate());?></span>
			</div> <br /><?php  }else{ 
			 if ($model->ea->category == 24) {?>
			 	<div class="input_text_desc"><label><label><?php echo "Type";?></label></label></div>
			 	<input type="hidden" id="training_type" name="training_type" value="<?php echo $ty ?>"/>
			<div class="input_text">
					<div class="hdselect">
						<?php  echo CHtml::activeDropDownList($model, "man_day_rate_n",  Codelkups::getCodelkupsDropDown('training_type'), array('value'=> ($model->ea->category == 24)?$ty:"",'disabled' => ($model->ea->category == 24)? 'disabled':"" ,'prompt' => Yii::t('translations', 'Choose Training Type'), 'class'=>'input_text_value' )); ?>
					</div>
				</div> 
			<?php }else  if ($model->ea->category != 496 &&  $model->ea->category != 623) { ?>
			<div class="input_text_desc"><label><label><?php echo "Frequency";?></label></label></div>
			<div class="input_text">
					<div class="hdselect">
						<?php  echo CHtml::activeDropDownList($model, "man_day_rate_n",  Codelkups::getCodelkupsDropDown('Frequency'), array('prompt' => Yii::t('translations', 'Choose Frequency'), 'class'=>'input_text_value'  )); ?>
					</div> 
				</div> 
			<?php } 
			} ?>
		</div>
		<?php } else { ?>
		<div class="textBox inline-block itms amt">
				<div class="input_text_desc"><label class="required"><?php echo "Estimated MAN DAYS";?><span class="required">*</span></label></div>
				<div class="input_text">
					<?php echo CHtml::activeTextField($model, "[$id]man_days", array('class'=> 'input_text_value man_days')); ?>
				</div>
				<?php echo CCustomHtml::error($model, "[$id]man_days", array('id'=>"EasItems_".$id."_man_days_em_")); ?>
			</div>

		<div class="textBox inline-block itms amt">
			<div class="input_text_desc"><label><label><?php echo $labels['man_day_rate'];?></label></label></div>
			<div class="input_text"> 
					<?php echo CHtml::activeTextField($model, "man_day_rate_n", array('class'=> 'input_text_value')); ?>					
				</div>
		</div> 
		<?php } ?>	
		
		<input type="hidden" name="ea_region" id="ea_region" value="<?php echo  Customers::getRegion($model->ea->id_customer); ?>" /> 
		<input type="hidden" name="ea_customization" id="ea_customization" value="<?php echo $model->ea->customization;?>" /> 
		<input type="hidden" name="ea_template" id="ea_template" value="<?php echo $model->ea->template;?>" /> 
		<input type="hidden" name="ea_category" id="ea_category" value="<?php echo $model->ea->category;?>" /> 
		<?php if ($model->id) { ?>
		<input type="hidden" name="EasItems[<?php echo $id;?>][id]" value="<?php echo $model->id;?>" /> <?php } ?>
		<div style="right:90px;" class="save" onclick="updateItem(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;right:30px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>
<script>
$(document).ready(function(){
		$('#popupfreeinvite').hide();	$('#EasItems_new_settings_codelkup').val('<?php echo ($model->ea->category == 24)?  $train:"" ?>');
		$('#EasItems_man_day_rate_n').val('<?php echo ($model->ea->category == 24)?  $ty : "" ?>')	});
</script>

