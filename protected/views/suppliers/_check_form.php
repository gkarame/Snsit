<div class="tache new">	<div class=""></div>	<fieldset class="new_product">	<?php $id = $model->id;	?>

	<div class="textBox  inline-block first" style="width:200px !important;">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]description"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($model,"[$id]description", array('style'=>'width:190px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]description",array('id'=>"SuppliersPrint_".$id."_description_em_")); ?>	</div>
		<div class="textBox  inline-block first" style="width:180px !important;padding-left:10px !important;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]jv_nb"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($model,"[$id]jv_nb", array('style'=>'width:170px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]jv_nb",array('id'=>"SuppliersPrint_".$id."_jv_nb_em_")); ?>	</div>
		<div class="textBox  inline-block first" style="width:150px !important;padding-left:10px !important;">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]bank_code"); ?></div>
			<div class="input_text"><?php echo CHtml::activeDropDownList($model,"[$id]bank_code",Codelkups::getCodelkupsDropDown('bank_code'), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:150px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]bank_code",array('id'=>"SuppliersPrint_".$id."_bank_code_em_")); ?>	</div>
		<div class="textBox  inline-block first" style="width:150px !important;padding-left:10px !important;">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]aux_code"); ?></div>
			<div class="input_text"><?php echo CHtml::activeDropDownList($model,"[$id]aux_code",Codelkups::getCodelkupsDropDown('aux_code'), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:150px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]aux_code",array('id'=>"SuppliersPrint_".$id."_aux_code_em_")); ?></div>	 
		<div class="textBox  inline-block first" style="width:200px !important;"><div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]acc_nb"); ?></div>
			<div class="input_text"><?php echo CHtml::activeDropDownList($model,"[$id]acc_nb",Codelkups::getCodelkupsDropDown('acc_code'), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:190px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]acc_nb",array('id'=>"SuppliersPrint_".$id."_acc_nb_em_")); ?></div>	
			<?php if($model->status==1){ ?>
<div class="textBox  inline-block first" style="width:180px !important;padding-left:10px !important;">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]check"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($model,"[$id]check", array('style'=>'width:170px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]check",array('id'=>"SuppliersPrint_".$id."_check_em_")); ?>	</div>
<div class="textBox  inline-block first" style="width:150px !important;padding-left:10px !important;">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]amount"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($model,"[$id]amount", array('style'=>'width:140px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]amount",array('id'=>"SuppliersPrint_".$id."_amount_em_")); ?>	</div>





<div class="textBox inline-block time normal" style="cursor:pointer; margin-left:10px;">
					<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]date"); ?></div>
					<div class="input_text">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "[$id]date", 
							'cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield','autocomplete'=>'off'),)); ?>
						<span class="calendar calfrom"></span>
					</div>
					<?php echo CCustomHtml::error($model, "[$id]date",array('id'=>"SuppliersPrint_".$id."_date_em_")); ?>
				</div>		

<?php }	?>	
		<div style="right:75px;top:80%;" class="save" onclick="updateCheck(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;top:80%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('budget-record-grid');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div>	</fieldset></div>