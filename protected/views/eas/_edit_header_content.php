<?php $can_modify=true; ?>
<fieldset id="header_fieldset">
	<div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status", Eas::getStatusList($model->status), array('class'=>'input_text_value')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"Eas_status_em_")); ?>
	</div>
	<?php if($model->category != '25'){?>
		<div class="textBox inline-block bigger_amt">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "expense"); ?> *</div>
			<div class="input_text">
				<div class="hdselect">
					<?php echo CHtml::activeDropDownList($model, "expense",  $model->getExpenses(), array( 
						'prompt' => Yii::t('translations', 'Choose expense'), 
						'class'=>'input_text_value', 
						'onchange' => 'changeExpense(this);',
						'disabled' => $can_modify ? '' : 'disabled'
					)); ?>
				</div>
			</div>
			<?php echo CCustomHtml::error($model, "expense", array('id'=>"Eas_expense_em_")); ?>
		</div>
	<div class="textBox bigger_amt inline-block hidden">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"lump_sum"); ?></div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "lump_sum", array('class'=> 'input_text_value', 'disabled' => $can_modify ? '' : 'disabled')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "lump_sum", array('id'=>"Eas_lump_sum_em_")); ?>
	</div>
		<div class="textBox inline-block bigger_amt">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"currency"); ?></div>
			<div class="input_text">
				<div class="hdselect">	
					<?php echo CHtml::activeDropDownList($model, "currency",  Codelkups::getCodelkupsDropDown('currency'), array( 
						'class'=>'input_text_value',
						'disabled' => $can_modify ? '' : 'disabled'
					)); ?>
				</div>
			</div>
			<?php echo CCustomHtml::error($model, "currency", array('id'=>"Eas_currency_em_")); ?>
		</div>	<?php }?>
	<div class="textBox bigger_amt2 inline-block">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"primary contact"); ?>			
		</div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "primary_contact_name", array('class'=> 'input_text_value' , 'value'=>($model->primary_contact_name <>' ' && $model->primary_contact_name <>'')?$model->primary_contact_name:$model->getPrimaryContact($model->id_customer))); ?>
		</div>
		<?php echo CCustomHtml::error($model, "primary_contact_name", array('id'=>"Eas_primary_contact_name_em_")); ?>
	</div>
		<div class="textBox bigger_amt2 inline-block">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"billto contact"); ?>			
		</div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "billto_contact_person", array('class'=> 'input_text_value' , 'value'=>($model->billto_contact_person <>' ' && $model->billto_contact_person <>'')?$model->billto_contact_person:$model->getBillToContact($model->id_customer))); ?>
		</div>
		<?php echo CCustomHtml::error($model, "billto_contact_person", array('id'=>"Eas_billto_contact_person_em_")); ?>
	</div>
		<div class="textBox bigger_amt3 inline-block">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"billto_address"); ?>
			
		</div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "billto_address", array('class'=> 'input_text_value', 'value'=>($model->billto_address <>' ' && $model->billto_address <>'')?$model->billto_address:$model->getBillToAddress($model->id_customer))); ?>
		</div>
		<?php echo CCustomHtml::error($model, "billto_address", array('id'=>"Eas_billto_contact_person_em_")); ?>
	</div>
	<?php if($model->category == '25'){?>
	<br clear="all"><?php }?>
	<div class="textBox three-smaller inline-block itms" style=" margin-right: 0px;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "description"); ?></div>
			<div class="input_text" style="   <?php if($model->category == 25) { echo 'width: 250px;'; } else{ echo ' width: 250px;';} ?> ">
				<?php  if($model->category == '27' && $model->status<2){ 
					echo CHtml::activeTextField($model, "description", array('class'=> 'input_text_value', 'style'=>' width: 240px;',  'disabled' => $can_modify && ($model->category != '24') ? '' : 'disabled'));
						}else{
							echo CHtml::activeTextField($model, "description", array('class'=> 'input_text_value','style'=>' width: 240px;','disabled' => $can_modify && ($model->category != '24') ? '' : 'disabled'));
					
						}
					?>
			</div>
			<?php echo CCustomHtml::error($model, "description", array('id'=>"Eas_description_em_"));  ?>
		</div>
	<?php if(($model->category == '27' || $model->category == '28') && $model->customization == 1){?>
	<div class="textBox bigger_amt2 inline-block">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"support_percent"); ?>			
		</div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "support_percent", array('class'=> 'input_text_value' )); ?>
		</div>
		<?php echo CCustomHtml::error($model, "support_percent", array('id'=>"Eas_support_percent_em_")); ?>
	</div>
	<div class="textBox bigger_amt2 inline-block">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"support_amt"); ?>			
		</div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "support_amt", array('class'=> 'input_text_value' )); ?>
		</div>
		<?php echo CCustomHtml::error($model, "support_amt", array('id'=>"Eas_support_amt_em_")); ?>
	</div>
	<?php } ?>
		<div class="textBox bigger_amt inline-block">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"lpo"); ?>
			<?php $mandatory = Customers::getLpo((int)$model->id_customer);
				if($mandatory == "Yes")
					echo "*";
			?>
		</div>
		<div class="input_text"  style="width:62px  !important; ">
			<?php echo CHtml::activeTextField($model, "customer_lpo", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "customer_lpo", array('id'=>"Eas_customer_lpo_em_")); ?>
	</div>	

	<div class="textBox bigger_amt inline-block " >
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"crmOpp"); ?></div>
		<div class="input_text"  style="width:62px  !important; ">
			<?php echo CHtml::activeTextField($model, "crmOpp", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "crmOpp", array('id'=>"crmOpp")); ?>
	</div>
    <div class="textBox inline-block bigger_amt country_perdiem" <?php if($model->expense === 'N/A'):?>style="display: none"<?php endif;?>>
        <div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "country_perdiem"); ?> </div>
        <div class="input_text <?=isset($model->country_perdiem)?'checked' : ''?>" style="width: auto;border: none;" id="perdiemchecbox">
            <?php  echo CHtml::CheckBox('',isset($model->country_perdiem)?'checked' : '',array('disabled' => ($model->expense != 'N/A' && $model->expense != 'Actuals')?'disabled':'') ); ?>
        </div>
    </div>
    <div class="textBox inline-block bigger_amt country_perdiem" <?php if($model->expense === 'N/A'):?>style="display: none"<?php endif;?>>
        <div class="input_text_desc"><?php echo CHtml::label("Country",''); ?></div>
        <div class="input_text">
            <div class="hdselect">
                <?php echo CHtml::textField('Eas[country_perdiem]',isset($country_choose['codelkup'])?$country_choose['codelkup']:'', array('prompt' => 'Choose country ', 'class' => 'codelist_dropdown','style' => 'z-index: 1000;')); ?>
            </div>
        </div>
        <?php echo CCustomHtml::error($model, "country_perdiem", array('id'=>"Eas_country_perdiem_id_")); ?>
    </div>
    <?=CHtml::hiddenField('country_perdiem_checbox',$model->country_perdiem,array('id' => 'country_perdiem_checbox_val'))?>
    <div style="right:85px; padding-top:105px;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style=" left:833px; color:#333; padding-top:105px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
<script type="text/javascript">
	function changeExpense(element) {
		$this = $(element);
		switch ($this.val()) {
			case '': 
				$('#Eas_lump_sum').parents('.textBox').addClass('hidden');
				$('.country_perdiem').hide();
                $('#perdiemchecbox input').removeAttr('checked').removeAttr('disabled');
				break;
			case 'N/A':
                $('.country_perdiem').hide();
                $('#perdiemchecbox input').removeAttr('checked').removeAttr('disabled');
                break;
			case 'Actuals':
				$('#Eas_lump_sum').parents('.textBox').addClass('hidden');
                $('.country_perdiem').show();
                $('#perdiemchecbox input').removeAttr('checked').removeAttr('disabled');
				break;
            case 'Lump Sum':
                $('#Eas_lump_sum').parents('.textBox').removeClass('hidden');
                $('.country_perdiem').show();
                $('#perdiemchecbox input').attr('checked','checked').attr('disabled','disabled');
                break;
			default:
                $('.country_perdiem').show();
                $('#perdiemchecbox input').attr('checked','checked').attr('disabled','disabled');
				$('#Eas_lump_sum').parents('.textBox').removeClass('hidden');
				break;
		}

        if ( $('#perdiemchecbox input').attr('checked') !== undefined){
            $('#country_perdiem_checbox_val').val(1)
        }else{
            $('#country_perdiem_checbox_val').val(0)
        }
	}

	$('#perdiemchecbox input').change(function () {
        if ( $(this).attr('checked') !== undefined){
            $('#country_perdiem_checbox_val').val(1)
        }else{
            $('#country_perdiem_checbox_val').val(0)
        }
    })
</script>