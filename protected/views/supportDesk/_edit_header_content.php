<div class="bg mask"></div><fieldset id="header_fieldset">	<div class="textBox inline-block bigger_amt">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activeLabelEx($model, 'assigned_to'); ?></div>
<div class="input_text">	<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model->idUser,'attribute' => 'firstname','source'=>Users::getAllAutocomplete(true),
							'options'=>array('minLength'=>'0','showAnim'=>'fold',),
							'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	),	));		?>		</div>
			<?php echo CCustomHtml::error($model, 'assigned_to', array('id'=>"SupportDesk_assigned_to_em_")); ?></div>
		<?php if(Yii::app()->user->isAdmin){?>	<div class="textBox inline-block bigger_amt">
				<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"reason"); ?></div>	<div class="input_text"><div class="hdselect">
						<?php echo CHtml::activeDropDownList($model, "reason", Codelkups::getCodelkupsDropDown('reason'), array('class'=>'input_text_value','prompt'=>" ")); ?>
					</div>	</div>	<?php echo CCustomHtml::error($model, "reason", array('id'=>"SupportDesk_reason_em_")); ?>	</div>
			<div class="textBox inline-block bigger_amt">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"responsibility"); ?></div>
				<div class="input_text"><div class="hdselect">	<?php echo CHtml::activeDropDownList($model, "responsibility", array('PS'=>'PS' ,'CS'=>'CS'), array('class'=>'input_text_value','prompt'=>" ")); ?>
					</div>	</div>	<?php echo CCustomHtml::error($model, "responsibility", array('id'=>"SupportDesk_responsibility_em_")); ?>	</div>
			<div class="textBox inline-block bigger_amt"  >	<div class="input_text_desc padding_smaller" ><?php echo CHtml::activelabelEx($model,"repeat"); ?></div>
				<div class="o_clasa" onclick="CheckOrUncheckInput(this)" style="display:block;with:25px;height:25px;position:relative">
				<div class="repeat_inp input <?php echo ($model->repeat == "Yes")?"checked":""?>">
						 <?php echo CHtml::CheckBox('repeat',($model->repeat == "Yes")?true:false , array ('value'=>'Yes','style'=>'width:10px;margin-left: 17px;margin-top: 8px;')); ?>
				</div></div><?php echo CCustomHtml::error($model, "repeat", array('id'=>"SupportDesk_repeat_em_")); ?>	</div>	<?php }?>
	<div style="right:75px;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="color:#333;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
