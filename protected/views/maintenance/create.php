<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'maintenance-header-form','enableAjaxValidation'=>false,)); ?>
	<?php echo $form->hiddenField($model,'currency_rate_id'); ?> 	
	<div class="row marginb20 marginr36 " style="height:70px;">	<?php echo $form->labelEx($model,'contract_description'); ?><div class="inputBg_create">
		<?php echo $form->textField($model,'contract_description',array('autocomplete'=>'off')); ?>
		</div>	<?php echo $form->error($model,'contract_description'); ?></div>	
		<div class="row marginb20 marginr36"style="height:70px;"><?php echo $form->labelEx($model, 'ea'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'ea',array('onchange' => 'updatea(this);')); ?>
		</div>	<?php echo $form->error($model,'ea'); ?>	</div>
	<div class="row marginb20  "style="height:70px;"><?php echo $form->labelEx($model,'customer'); ?>	
		<div class="inputBg_create"><?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name',		
					'source'=>Customers::getAllAutocomplete(),'options'=>array('minLength'=>'0','showAnim'=>'fold','select'=>"js:function(event, ui) {
	                $('#Maintenance_customer').val(ui.item.id);}",'change'=>"js:function(event, ui) {
	                   	if (!ui.item) { $('#Maintenance_customer').val(''); } }",),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'onblur' => 'blurAutocomplete(event, this, "#Maintenance_customer");'),	));	?> 
			<?php echo $form->hiddenField($model, 'customer'); ?> </div>		
		<?php echo $form->error($model, 'customer_name') ?	$form->error($model,'customer_name') : $form->error($model, 'customer'); ?>	</div>	
	<div class="row marginb20 marginr36" style="height:70px;"><?php echo $form->labelEx($model, 'owner'); ?>	<div class="selectBg_create">
		<?php echo $form->dropDownList($model, 'owner', Codelkups::getCodelkupsDropDown('partner'), array('prompt' => Yii::t('translations', 'Choose owner'), 'onchange' => 'changeCategory(this);')); ?>
		</div><?php echo $form->error($model,'owner'); ?></div>	
	<div class="row marginb20 marginr36" style="height:70px;">	<?php echo $form->labelEx($model, 'product'); ?>	<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'product', Codelkups::getCodelkupsDropDown('product'), array('prompt' => Yii::t('translations', 'Choose product'), 'onchange' => 'changeProduct(this);')); ?>
		</div>	<?php echo $form->error($model,'product'); ?></div>
	<div class="row marginb20  " style="height:70px;"><?php echo $form->labelEx($model, 'support_service'); ?>	<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'support_service', Codelkups::getCodelkupsDropDown('support_service'), array('prompt' => Yii::t('translations', 'Choose support service'), 'onchange' => 'changeService(this);')); ?>
		</div>	<?php echo $form->error($model,'support_service'); ?></div>
<div class="row marginb20 marginr36" style="height:70px;"><?php echo $form->labelEx($model, 'wms_db_type'); ?>
		<div class="selectBg_create"><?php echo $form->dropDownList($model, 'wms_db_type', Codelkups::getCodelkupsDropDown('wms_db_type'), array('prompt' => Yii::t('translations', 'Choose DB Type'))); ?>
		</div>	<?php echo $form->error($model,'wms_db_type'); ?></div>	
	<div class="row marginb20 marginr36" style="height:70px;">	<?php echo $form->labelEx($model, 'soft_version'); ?>	<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'soft_version', Codelkups::getCodelkupsDropDown('soft_version'), array('prompt' => Yii::t('translations', 'Choose Software Version'))); ?>
		</div>	<?php echo $form->error($model,'soft_version'); ?></div>	
	<div class="row marginb20  " style="height:70px;"><?php echo $form->labelEx($model, 'frequency'); ?>	<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'frequency', Codelkups::getCodelkupsDropDown('frequency'), array('prompt' => Yii::t('translations', 'Choose frequency'))); ?>
		</div><?php echo $form->error($model,'frequency'); ?></div>	
	<div class="row marginb20 marginr36 " style="height:70px;"><?php echo $form->labelEx($model, 'original_amount'); ?><div class="inputBg_create">
		<?php echo $form->textField($model, 'original_amount',array('autocomplete'=>'off')); ?></div><?php echo $form->error($model,'original_amount'); ?>
	</div><div class="row marginb20 marginr36" style="height:70px;">	<?php echo $form->labelEx($model, 'currency'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'currency', Codelkups::getCodelkupsDropDown('currency'), array('prompt' => Yii::t('translations', 'Choose currency'))); ?>
		</div>	<?php echo $form->error($model,'currency'); ?>	</div>	
	<div class="row marginb20  " style="height:70px;"><?php echo $form->labelEx($model, 'escalation_factor'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'escalation_factor',array('onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?>
		</div>	<?php echo $form->error($model,'escalation_factor'); ?>	</div>	
	<div class="row marginb20 marginr36 " style="height:70px;">	<?php echo $form->labelEx($model,'status'); ?>	<div class="selectBg_create">
		<?php echo $form->dropDownList($model, 'status', array('Active'=>'Active','Inactive'=>'Inactive')); ?> </div><?php echo $form->error($model,'status'); ?></div>
	<div class="row marginb20 marginr36" style="height:70px;">	<?php echo $form->labelEx($model,'contract_duration'); ?>	<div class="selectBg_create">
		<?php echo $form->dropDownList($model, 'contract_duration', array('Open Ended'=>'Open Ended','1 Year'=>'1 Year','2 Years'=>'2 Years','3 Years'=>'3 Years','5 Years'=>'5 Years')); ?> 
		</div>	<?php echo $form->error($model,'contract_duration'); ?>	</div>	
	<div class="row marginb20  " >	<?php echo $form->labelEx($model,'cpi'); ?>	<div class="selectBg_create">
		<?php echo $form->dropDownList($model, 'cpi', array('Yes'=>'Yes','No'=>'No')); ?> </div><?php echo $form->error($model,'cpi'); ?>	</div>
	<div class="row marginb20 marginr36 " style="height:70px;">	<?php echo $form->labelEx($model,'po_renewal'); ?>	<div class="selectBg_create">
		<?php echo $form->dropDownList($model, 'po_renewal', array('Yes'=>'Yes','No'=>'No')); ?> </div>	<?php echo $form->error($model,'po_renewal'); ?></div>

	<div class="row marginb20 marginr36"style="height:70px;">
		<?php echo $form->labelEx($model, 'short_description'); ?><div class="inputBg_create"><?php echo $form->textField($model, 'short_description', array('autocomplete'=>'off')); ?></div>		
		<?php echo $form->error($model,'short_description'); ?></div>	
	<div class="row startDateRow  "style="height:70px;"><?php echo $form->labelEx($model,'starting_date'); ?>		
		<div class="dataRow margin_right0"><?php echo $form->textField($model,'starting_date',array('autocomplete'=>'off')); ?><span class="calendar calfrom"></span></div>
		<?php echo $form->error($model,'starting_date'); ?></div>


	<div class="row marginb20 marginr36 "style="height:70px;"><?php echo $form->labelEx($model, 'licenses'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'licenses', array()); ?></div>		
		<?php echo $form->error($model,'licenses'); ?></div>

	<div class="row marginb20 marginr36 "style="height:70px;"><?php echo $form->labelEx($model, 'sns_share'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'sns_share', array('onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?></div>		
		<?php echo $form->error($model,'sns_share'); ?></div>

		<div class="row marginb20  "style="height:70px;"><?php echo $form->labelEx($model, 'net_share'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'net_share', array('onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;")); ?></div>		
		<?php echo $form->error($model,'net_share'); ?></div>

	<div class="row marginb20 marginr36 hidden" style="height:70px;" title="Emails separated by commas, up to 500 characters">
		<?php echo $form->labelEx($model, 'sma_recipients'.' *'); ?><div class="inputBg_create">
		<?php echo $form->textField($model, 'sma_recipients', array('autocomplete'=>'off')); ?>
		</div><?php echo $form->error($model,'sma_recipients'); ?>	</div>
	<div class="row marginb20 marginr36 hidden" style="height:70px;" title="Instances separated by commas, up to 500 characters"><?php echo $form->labelEx($model, 'sma_instances'.' *'); ?>	<div class="inputBg_create">
		<?php echo $form->textField($model, 'sma_instances', array('autocomplete'=>'off')); ?></div><?php echo $form->error($model,'sma_instances'); ?>
	</div>

	<div class="horizontalLine"></div><div class="row buttons">	<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>
	</div><br clear="all" />
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
	$(function() {
		changeCategory('#Maintenance_owner');	changeService('#Maintenance_support_service');	$("#Maintenance_starting_date").datepicker({ dateFormat: 'dd/mm/yy' });
	 addPercent('#Maintenance_escalation_factor');   addPercent('#Maintenance_sns_share');
	 	 $("#Maintenance_starting_date").click(function(){	$('#ui-datepicker-div').css('top',parseFloat($("#Maintenance_starting_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Maintenance_starting_date").offset().left));	 }); }); 
	function addPercent(element) {
		var val = parseFloat($(element).val());
		if (isNaN(val)) {	$(element).val('0%');			
		} else {
			if ($(element).hasClass('discountinput') && val > 100) { val = 100; }
			$(element).val(val + '%');
		}
	}	
	function removePercent(element){
		var val = parseFloat($(element).val());
		if (isNaN(val) || val == 0) {	$(element).val(""); } else { $(element).val(val); }
	}

	function updatea(element){
		var val = parseFloat($(element).val());
		$.ajax({type: "POST",url: '<?php echo Yii::app()->createAbsoluteUrl('maintenance/updatefields');?>',
			data: { 'val' : val},dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
				  		$('#Maintenance_original_amount').val(data.amount);
				  		$('#Maintenance_support_service').val(data.plan);
				  		$('#Maintenance_frequency').val(data.freq);
				  		$('#Maintenance_currency').val(data.currency);
				  		$('#Maintenance_customer_name').val(data.cname);
				  		$('#Maintenance_customer').val(data.customer);
				  	} } } });
	}
	function changeService(element){
		$this =  $(element);
		if($this.val() == 501 && $this.val() != "") {	$('#Maintenance_sma_recipients').parents('.row').removeClass('hidden');	$('#Maintenance_sma_instances').parents('.row').removeClass('hidden');
		}else if($this.val() != 501 || $this.val() == null)
		{	$('#Maintenance_sma_recipients').parents('.row').addClass('hidden');	$('#Maintenance_sma_instances').parents('.row').addClass('hidden');	} }
	function changeCategory(element) {
		$this =  $(element);
		if($this.val() != <?php echo Maintenance::PARTNER_SNS; ?> && $this.val() != "" && ($('#Maintenance_product').val()== "1402" || $('#Maintenance_product').val() == "1268" )) {	
			$('#Maintenance_net_share').parents('.row').removeClass('hidden');  addPercent('#Maintenance_net_share');
		}else{ $('#Maintenance_net_share').parents('.row').addClass('hidden');	}  }

	function changeProduct(element) {
		$this =  $(element);
		if($('#Maintenance_owner').val() != <?php echo Maintenance::PARTNER_SNS; ?> && $('#Maintenance_owner').val() != ""  && ($this.val() == "1402" ||  $this.val() == "1268" )) {	
			$('#Maintenance_net_share').parents('.row').removeClass('hidden'); addPercent('#Maintenance_net_share');
		}else { $('#Maintenance_net_share').parents('.row').addClass('hidden');	}  }

	function addPercent(element) {	var val = parseFloat($(element).val());	if (isNaN(val)) {	$(element).val('0%');	} else { $(element).val(val + '%');	} }
	function removePercent(element) {	var val = parseFloat($(element).val());	if (isNaN(val) || val == 0) {	$(element).val("");	} else { $(element).val(val); } }
	function CheckOrUncheckInput(obj){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');		
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	input.prop('checked', false);	}
		else {	checkBoxDiv.addClass('checked');	input.prop('checked', true);	} }	
</script>
