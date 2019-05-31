<div class="tache new"><div class="bg" style="top:0px;"></div>
	<fieldset class="items_fieldset"><?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox three-smaller marginr36  inline-block itms" style="width: 410px;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "contract_description"); ?></div>
			<div class="input_text" style="width: 410px;"><?php echo CHtml::activeTextField($model, "contract_description", array('class'=> 'input_text_value',"style"=>'width: 400px;')); ?></div>
			<?php echo CCustomHtml::error($model, "contract_description"); ?></div>
		<div class="textBox bigger_amt marginr36 width107 inline-block ">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"amount"); ?></div>
			<div class="input_text width107"><?php echo CHtml::activeTextField($model, "amount", array('class'=> 'input_text_value')); ?>
			</div><?php echo CCustomHtml::error($model, "amount"); ?>
		</div><div class="textBox inline-block bigger_amt marginr36 "  style="    margin-left: -5px;">
			<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"currency"); ?></div>
			<div class="input_text"><div class="hdselect"> 
					<?php echo CHtml::activeDropDownList($model,"currency", Codelkups::getCodelkupsDropDown('currency'), array('class'=>'input_text_value', 'style'=> ' opacity: 0.6; pointer-events: none;')); ?>
		</div></div><?php echo CCustomHtml::error($model, "currency", array('id'=>"")); ?>	</div>
		<div class="textBox bigger_amt inline-block marginr36 width107 ">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"ea# *"); ?></div>
			<div class="input_text width107"><?php echo CHtml::activeTextField($model, "ea", array('class'=> 'input_text_value','onchange' => 'updateaItem(this);')); ?>
			</div><?php echo CCustomHtml::error($model, "ea"); ?>
		</div>
		<div class="textBox bigger_amt inline-block marginr36  width107">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"licences *"); ?></div>
			<div class="input_text width107"><?php echo CHtml::activeTextField($model, "licences", array('class'=> 'input_text_value')); ?>
			</div><?php echo CCustomHtml::error($model, "licences"); ?>
		</div>
		<div class="textBox inline-block bigger_amt marginr36 " style="width: 118px;">
			<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
			<div class="input_text "><div class="hdselect">
					<?php echo CHtml::activeDropDownList($model,"status", MaintenanceItems::getStatusList(), array('class'=>'input_text_value')); ?>
		</div></div><?php echo CCustomHtml::error($model, "status", array('id'=>"")); ?>	</div>

		<?php if($model->idContract->owner !=  Maintenance::PARTNER_SNS){?>
			<div class="textBox bigger_amt inline-block marginr36 width107">
				<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"sns_share"); ?></div>
				<div class="input_text width107"><?php echo CHtml::activeTextField($model, "sns_share", array('class'=> 'input_text_value')); ?>	</div>
				<?php echo CCustomHtml::error($model, "sns_share", array('id'=>"Eas_lump_sum_em_")); ?>	</div>	<?php }?>

		<?php  if(empty($model->id)){ ?>
	<div class="textBox inline-block time normal width107  " style="cursor:pointer; margin-right: 27px;">
					<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"from_inv_date"); ?></div>
					<div class="dateInput" style="    border: 1px solid #cccccc;   height: 24px; -webkit-border-radius: 4px;    -moz-border-radius: 4px;    border-radius: 4px;    width: 103px;    padding: 2px;    background: #fff;   position: relative;">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "from_inv_date", 
							'cssFile' => false,'options'=>array('dateFormat'=>'d/m/yy','showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield','autocomplete'=>'off', 'style'=>'width: 100px;margin-left:2px;'),)); ?>
						<span class="calendar calfrom"></span>
					</div>
					<?php CCustomHtml::error($model,'from_inv_date'); ?>
				</div>	


	<div class="textBox inline-block time normal width107  marginr36" style="cursor:pointer; ">
					<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"to_inv_date"); ?></div>
					<div class="dateInput" style="    border: 1px solid #cccccc;   height: 24px; -webkit-border-radius: 4px;    -moz-border-radius: 4px;    border-radius: 4px;    width: 103px;    padding: 2px;    background: #fff;   position: relative;">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "to_inv_date", 
							'cssFile' => false,'options'=>array('dateFormat'=>'d/m/yy','showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield','autocomplete'=>'off', 'style'=>'width: 100px;margin-left:2px;'),)); ?>
						<span class="calendar calfrom"></span>
					</div>
					<?php CCustomHtml::error($model,'to_inv_date'); ?>
				</div>	
			<?php }?>	
		<?php if ($model->id) { ?>	<input type="hidden" name="MaintenanceItems[id]" value="<?php echo $model->id;?>" /> <?php } ?>	
		<div style="right:75px;" class="save" onclick="updateItem(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset></div>