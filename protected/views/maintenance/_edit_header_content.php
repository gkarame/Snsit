<div class="bg maintenance_bg"></div><fieldset id="header_fieldset">	
	<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "contract_description"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "contract_description", array('class'=> 'input_text_value')); ?></div>
		<?php echo CCustomHtml::error($model, "contract_description", array('id'=>"")); ?>
	</div>
	<div class="textBox inline-block bigger_amt width136">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"owner"); ?></div>
		<div class="input_text"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"owner", Codelkups::getCodelkupsDropDown('partner'), array('class'=>'input_text_value','onchange' => 'changeCategory(this);')); ?>
			</div></div><?php echo CCustomHtml::error($model, "owner", array('id'=>"")); ?></div>
	<div class="textBox inline-block bigger_amt width136"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"product"); ?></div>
		<div class="input_text width115"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"product", Codelkups::getCodelkupsDropDown('product'), array('class'=>'input_text_value','onchange' => 'changeProduct(this);')); ?>
			</div></div><?php echo CCustomHtml::error($model, "product", array('id'=>"")); ?></div>	
	<div class="textBox three-smaller inline-block itms"><div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "short_description"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "short_description", array('class'=> 'input_text_value')); ?></div>
		<?php echo CCustomHtml::error($model, "short_description", array('id'=>"")); ?>	</div>	
	<div class="textBox inline-block bigger_amt width136"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"customer"); ?></div>
		<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, 'customer', Customers::getAllCustomersSelect(), array('prompt'=>Yii::t('translations', ''))); ?>
			</div></div><?php echo CCustomHtml::error($model, "customer", array('id'=>"")); ?></div>	
	<div class="textBox inline-block bigger_amt width136"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"frequency"); ?></div>
		<div class="input_text width115"><div class="hdselect"><?php echo CHtml::activeDropDownList($model,"frequency", Codelkups::getCodelkupsDropDown('frequency'), array('class'=>'input_text_value')); ?>
			</div></div><?php echo CCustomHtml::error($model, "frequency", array('id'=>"")); ?></div>	
	<div class="textBox inline-block bigger_amt width165"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"support_service"); ?></div>
		<div class="input_text width150"><div class="hdselect"><?php echo CHtml::activeDropDownList($model,"support_service", Codelkups::getCodelkupsDropDown('support_service'), array('class'=>'input_text_value ','onchange' => 'changePlan(this);')); ?>
			</div></div><?php echo CCustomHtml::error($model, "support_service", array('id'=>"")); ?></div>	
	
	<div class="textBox bigger_amt inline-block width120">

<div class="input_text_desc "><?php echo CHtml::activelabelEx($model,"ea"); ?></div>
		<div class="input_text width95"><?php echo CHtml::activeTextField($model, "ea", array('class'=> 'input_text_value width85')); ?>
		</div><?php echo CCustomHtml::error($model, "ea", array('id'=>"Maintenance_ea")); ?></div>	
	<div class="textBox bigger_amt inline-block width136 mr5">

	<div class="input_text_desc width165"><?php echo CHtml::activelabelEx($model,"original_amount"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "original_amount", array('class'=> 'input_text_value')); ?>
		</div><?php echo CCustomHtml::error($model, "original_amount", array('id'=>"")); ?></div>	

	<div class="textBox inline-block bigger_amt width136 mr5"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"currency"); ?></div>
		<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model,"currency", Codelkups::getCodelkupsDropDown('currency'), array('class'=>'input_text_value')); ?>
			</div></div><?php echo CCustomHtml::error($model, "currency", array('id'=>"")); ?>
		</div>

	<div class="textBox inline-block bigger_amt width136">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status",  array('Active'=>'Active','Inactive'=>'Inactive'), array('class'=>'input_text_value',)); ?>
			</div></div><?php echo CCustomHtml::error($model, "status", array('id'=>"Eas_currency_em_")); ?>
	</div>

	<div class="textBox inline-block bigger_amt width136"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"wms_db_type"); ?></div>
		<div class="input_text width115"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"wms_db_type", Codelkups::getCodelkupsDropDown('wms_db_type'), array('class'=>'input_text_value ', 'prompt'=>'Choose DB Type')); ?>
			</div></div><?php echo CCustomHtml::error($model, "wms_db_type", array('id'=>"")); ?></div>	

	<div class="textBox inline-block bigger_amt width165">
	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"contract_duration"); ?></div>
		<div class="input_text width150"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "contract_duration",  array('Open Ended'=>'Open Ended','1 Year'=>'1 Year','2 Years'=>'2 Years','3 Years'=>'3 Years','5 Years'=>'5 Years'), array('class'=>'input_text_value',)); ?>
			</div></div><?php echo CCustomHtml::error($model, "contract_duration", array('id'=>"Eas_currency_em_")); ?>	</div>	
	<div class="textBox bigger_amt inline-block width120">
			<div class="input_text_desc width120"><?php echo CHtml::activelabelEx($model,"cpi"); ?></div>
		<div class="input_text width95"><div class="hdselect width95"><?php echo CHtml::activeDropDownList($model, "cpi",  array('Yes'=>'Yes','No'=>'No'), array('class'=>'input_text_value width80',)); ?>
			</div></div><?php echo CCustomHtml::error($model, "cpi", array('id'=>"Eas_currency_em_")); ?></div>			
	<div class="textBox bigger_amt inline-block width132 mr5">
			<div class="input_text_desc "><?php echo CHtml::activelabelEx($model,"po_renewal"); ?></div>
		<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "po_renewal",  array('Yes'=>'Yes','No'=>'No'), array('class'=>'input_text_value',)); ?>
			</div></div><?php echo CCustomHtml::error($model, "po_renewal", array('id'=>"Eas_currency_em_")); ?></div>	
	<div class="textBox inline-block bigger_amt width136 mr5">

	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"nbwarehourses"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "nbwarehourses", array('class'=> 'input_text_value')); ?>
		</div><?php echo CCustomHtml::error($model, "nbwarehourses", array('id'=>"Eas_lump_sum_em_")); ?>

	</div>
	<div class="textBox bigger_amt inline-block width132">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"escalation_factor"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "escalation_factor", array('class'=> 'input_text_value', 'onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?></div>
		<?php echo CCustomHtml::error($model, "escalation_factor", array('id'=>"")); ?></div>	
 

	
	<div class="item inline-block time normal" style="cursor:pointer; margin-left:10px;">
					<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"starting_date"); ?></div>
					<div class="dateInput" style="    border: 1px solid #cccccc;   height: 24px; -webkit-border-radius: 4px;    -moz-border-radius: 4px;    border-radius: 4px;    width: 110px;    padding: 2px;    background: #fff;   position: relative;">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "starting_date", 
							'cssFile' => false,'options'=>array('dateFormat'=>'yy-m-d','showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield','autocomplete'=>'off', 'style'=>'margin-top:-3px;margin-left:2px;'),)); ?>
						<span class="calendar calfrom"></span>
					</div>
					<?php CCustomHtml::error($model,'starting_date'); ?>
				</div>	



	<div class="textBox inline-block bigger_amt width165"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"end_customer"); ?></div>
		<div class="input_text width150"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, 'end_customer', Customers::getAllCustomersSelect(), array('prompt'=>Yii::t('translations', ''),'class'=>'input_text_value ')); ?>
		</div></div><?php echo CCustomHtml::error($model, "end_customer", array('id'=>"")); ?></div>
	
	
	<div class="textBox inline-block bigger_amt width120">
	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"sw_version"); ?></div>
		<div class="input_text width95"><div class="hdselect width95">
				<?php echo CHtml::activeDropDownList($model,"soft_version", Codelkups::getCodelkupsDropDown('soft_version'), array('class'=>'input_text_value width80', 'prompt'=>'Choose Software Version')); ?>
			</div></div><?php echo CCustomHtml::error($model, "soft_version", array('id'=>"")); ?></div>

	<div class="textBox inline-block bigger_amt width136">  <div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"sns_share"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "sns_share", array('class'=> 'input_text_value','onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?></div>
		<?php echo CCustomHtml::error($model, "sns_share", array('id'=>"Eas_lump_sum_em_")); ?></div>
	
	<?php if($model->owner !=77 && ($model->product == 1402 || $model->product == 1268) ){ ?>
	<div class="textBox inline-block bigger_amt width136 mr5">
<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"net_share"); ?></div>
		<div class="input_text" ><?php echo CHtml::activeTextField($model, "net_share", array('class'=> 'input_text_value','onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?>
		</div><?php echo CCustomHtml::error($model, "net_share", array('id'=>"Eas_lump_sum_em_")); ?>
</div>  <?php } ?>	

	<div class="textBox bigger_amt inline-block width132">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"real_esc"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "real_esc", array('class'=> 'input_text_value', 'onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?></div>
		<?php echo CCustomHtml::error($model, "real_esc", array('id'=>"")); ?></div>	


		<div id= "note" class="textBox bigger_amt inline-block  width132" style="margin-left: 10px;margin-right: 10px;"><div class="input_text_desc " ><?php echo CHtml::activelabelEx($model, "notes"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "notes", array('class'=> 'input_text_value')); ?>
		</div><?php echo CCustomHtml::error($model, "notes", array('id'=>"")); ?></div>
 

	<div class="textBox inline-block bigger_amt width165 <?php if($model->support_service != 502 && $model->support_service != 501) { echo 'hidden'; }?> " id="inst" title="Instances separated by commas, up to 500 characters">
		<div class="input_text_desc"  ><?php echo CHtml::activelabelEx($model, "sma_instances"); ?></div>
		<div class="input_text width150"><?php echo CHtml::activeTextField($model, "sma_instances", array('class'=> 'input_text_value')); ?>	</div>
		<?php echo CCustomHtml::error($model, "sma_instances", array('id'=>"")); ?></div>	

	<div class="textBox three-smaller inline-block itms <?php if($model->support_service != 502 && $model->support_service != 501) { echo 'hidden'; }?> " id="recp" title="Emails separated by commas, up to 500 characters">
		<div class="input_text_desc" ><?php echo CHtml::activelabelEx($model, "sma_recipients"); ?></div><div class="input_text" style="width: 380px;">
			<?php echo CHtml::activeTextField($model, "sma_recipients", array('class'=> 'input_text_value', 'style'=>'width: 360px;')); ?></div>
		<?php echo CCustomHtml::error($model, "sma_recipients", array('id'=>"")); ?></div>
	 
	<div class="textBox bigger_amt inline-block width132">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"licenses"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "licenses", array('class'=> 'input_text_value')); ?></div>
		<?php echo CCustomHtml::error($model, "licenses", array('id'=>"")); ?></div>	



	<div class="textBox bigger_amt inline-block width165"></div><div class="textBox bigger_amt inline-block width165"></div><div>
	<div style="right: 113px;top: 420px !important;" class="save top450" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="right: 50px;top: 420px !important;color:#333;" class="save top450" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</div></fieldset>
<script type="text/javascript">
$(function() { changePlan('#Maintenance_support_service'); changeCategory('#Maintenance_owner');  addPercent('#Maintenance_escalation_factor');  addPercent('#Maintenance_real_esc');  addPercent('#Maintenance_sns_share'); addPercent('#Maintenance_net_share');  }); 
 
function addPercent(element) {	var val = parseFloat($(element).val());	if (isNaN(val)){ $(element).val('0%'); } else { $(element).val(val + '%'); } }
function removePercent(element) {	var val = parseFloat($(element).val());	if (isNaN(val) || val == 0) {	$(element).val("");	}else{ $(element).val(val); }	}
function changeCategory(element) {
		$this =  $(element);
		if($this.val() != <?php echo Maintenance::PARTNER_SNS; ?> && $this.val() != "" && ($('#Maintenance_product').val()== "1402" || $('#Maintenance_product').val() == "1268" )) {	
			$('#Maintenance_net_share').parents('.row').removeClass('hidden');  addPercent('#Maintenance_net_share');
		}else{ $('#Maintenance_net_share').parents('.row').addClass('hidden');	}  }

function changePlan(element) {
		$this =  $(element);
		if($this.val() == "502" ||  $this.val() == "501" )
		{
			$('#inst').removeClass('hidden'); $('#recp').removeClass('hidden');  $('#note').addClass('marginr13');
		}else{
			$('#inst').addClass('hidden');$('#recp').addClass('hidden'); $('#note').removeClass('marginr13');
		}
 }
function changeProduct(element) {
		$this =  $(element);
		if($('#Maintenance_owner').val() != <?php echo Maintenance::PARTNER_SNS; ?> && $('#Maintenance_owner').val() != ""  && ($this.val() == "1402" ||  $this.val() == "1268" )) {	
			$('#Maintenance_net_share').parents('.row').removeClass('hidden'); addPercent('#Maintenance_net_share');
		}else { $('#Maintenance_net_share').parents('.row').addClass('hidden');	}  }
</script>