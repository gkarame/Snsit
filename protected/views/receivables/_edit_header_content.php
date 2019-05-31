<div class="bg"></div><fieldset id="header_fieldset" ><div class="textBox inline-block bigger_amt">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"status"); ?></div>	<div class="input_text"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status",  Invoices::getStatusList(array('New','To Print')), array('class'=>'input_text_value','prompt'=>"",)); ?>	</div>	</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"Receivables_status_em_")); ?></div>
	<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "id_assigned"); ?></div>
		<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "id_assigned",  Receivables::getAllUsserToAssign(true), array( 'class'=>'input_text_value','prompt'=>"",)); ?>	</div>	</div>
		<?php echo CCustomHtml::error($model, "id_assigned", array('id'=>"Receivables_id_assigned_em_")); ?></div>
<?php if ($model->partner != null && $model->sold_by != Maintenance::PARTNER_SNS) { ?>
	<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "partner_status"); ?></div>
		<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "partner_status",  Receivables::getPartnerStatus(), array('class'=>'input_text_value',	'prompt'=>"",	)); ?>
			</div></div><?php echo CCustomHtml::error($model, "partner_status", array('id'=>"Receivables_partner_status_em_")); ?></div>
	

<?php if ($model->partner != '1218') { ?> 

	<div class="textBox bigger_amt inline-block"><div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "partner_inv"); ?></div>
		<div class="input_text"><?php if( $model->partner=='78' || $model->partner=='1218' || $model->partner=='1336' ){  echo CHtml::activeTextField($model, "partner_inv" , array('class'=> 'input_text_value','maxlength'=>'25')); }
				  if($model->partner=='79'){ echo CHtml::activeTextField($model, "span_partner_inv" , array('class'=> 'input_text_value','maxlength'=>'25'));}
				   	 if($model->partner=='201' || $model->partner=='554'){  echo CHtml::activeTextField($model, "snsapj_partner_inv" , array('class'=> 'input_text_value','maxlength'=>'25'));  }	 ?>
		</div>	<?php echo CCustomHtml::error($model, "partner_inv", array('id'=>"Receivables_partner_inv_em_")); ?></div> <?php } } ?>
	

	<div class="textBox textarea_inpt inline-block ">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "remarks"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "remarks", array('class'=> 'input_text_value')); ?></div>
		<?php echo CCustomHtml::error($model, "remarks", array('id'=>"Receivables_remarks_em_")); ?></div>	
	<div class="textBox textarea_inpt inline-block "><div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"notes"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "notes", array('class'=> 'input_text_value')); ?>	</div>
		<?php echo CCustomHtml::error($model, "notes", array('id'=>"Receivables_notes_em_")); ?></div>
	 <div style="right:95px;top:123px" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="right:30px;color:#333;top:123px" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>