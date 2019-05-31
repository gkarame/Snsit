<div class="bg" style="    height: 250px;    background-size: 913px 250px;"></div>
<fieldset id="header_fieldset" >	
	 <div class="textBox inline-block bigger_amt marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"sold_by"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "sold_by",  Codelkups::getCodelkupsDropDown('unit'), array( 
					'class'=>'input_text_value','prompt'=>'')); ?>
			</div>	</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"Eas_currency_em_")); ?>
	</div>
	<div class="textBox inline-block bigger_amt marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status",  Invoices::getStatusList(), array('class'=>'input_text_value',)); ?>
			</div>	</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"Eas_currency_em_")); ?>
	</div>
	<div class="textBox bigger_amt inline-block marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"partner invoice"); ?></div>
		<div class="input_text">
			<?php if( $model->partner=='78' || $model->partner=='1218' || $model->partner=='1336' ){  echo CHtml::activeTextField($model, "partner_inv" , array('class'=> 'input_text_value')); }
				  if($model->partner=='79'){ echo CHtml::activeTextField($model, "span_partner_inv" , array('class'=> 'input_text_value'));}
				   	 if($model->partner=='201' || $model->partner=='554'){  echo CHtml::activeTextField($model, "snsapj_partner_inv" , array('class'=> 'input_text_value'));  } ?>
		</div>
		<?php echo CCustomHtml::error($model, "partner_inv", array('id'=>"")); ?>
	</div>



<div class="textBox inline-block bigger_amt marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"type"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "type", array('Standard'=>'Standard' ,'Travel Expenses'=>'Travel Expenses', 'Expenses'=>'Expense Sheet' , 'T&M'=>'T&M' , 'Maintenance'=>'Maintenance'), array('class'=>'input_text_value','prompt'=>" ",'style'=>'width:104px;border:none;',"onchange"=>"changeType(this);")); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "type", array('id'=>"")); ?>
	</div>
	<div class="textBox bigger_amt inline-block marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"Description"); ?></div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "invoice_title", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "invoice_title", array('id'=>"")); ?>
	</div>
	<div class="textBox inline-block bigger_amt marginr22">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"partner"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"partner", Codelkups::getCodelkupsDropDown('partner'), array('class'=>'input_text_value','onchange' => 'changeCategory(this);')); ?>
			</div></div>
		<?php echo CCustomHtml::error($model, "partner", array('id'=>"")); ?>
	</div>	
	<div class="textBox bigger_amt inline-block marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"sns_share"); ?></div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "sns_share", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "sns_share", array('id'=>"")); ?>
	</div>	
	<div class="textBox inline-block bigger_amt marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"old"); ?></div>
		<div class="input_text">
			<div class="hdselect">
			<?php echo CHtml::activeDropDownList($model, "old", array('No'=>'No','Yes'=>'Yes'), array('class'=>'input_text_value',)); ?>
			</div>	</div>
		<?php echo CCustomHtml::error($model, "old", array('id'=>"")); ?>
	</div>	
	<div class="textBox inline-block bigger_amt hidden marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"partner_status"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "partner_status", array('Not Paid'=>'Not Paid','Paid'=>'Paid'), array('class'=>'input_text_value',)); ?>
			</div>	</div>
		<?php echo CCustomHtml::error($model, "partner_status", array('id'=>"")); ?>
	</div>	
	<div class="textBox inline-block bigger_amt  " style="margin-right: 42px;width:239px !important;">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"Invoice date"); ?></div>
		<div class="input_text"  style="width:256px !important;">	
			<div class="hdselect inv_date_edit_header" style="    width: 95%;">
				<?php echo Invoices::getInvoiceDateEdit($model->invoice_date_month,$model->invoice_date_year,$model->id); ?>
			</div>	</div>
		<?php echo CCustomHtml::error($model, "partner_status", array('id'=>"")); ?>
	</div>
	<div class="textBox bigger_amt inline-block marginr22"  style="<?php if($model->status == 'Printed' || $model->status =='Paid')
	{ echo "pointer-events:none;opacity: 0.5;";}?>"> 
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"Amount"); ?></div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "gross_amount", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "gross_amount", array('id'=>"")); ?>
	</div>
	<div class="textBox inline-block bigger_amt marginr22">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"currency"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "currency", Codelkups::getCodelkupsDropDown('currency'), array('class'=>'input_text_value','prompt'=>" ",'style'=>'width:104px;border:none;',"onchange"=>"changeInput(value,$model->id,1)")); ?>
			</div>	</div>
		<?php echo CCustomHtml::error($model, "currency", array('id'=>"")); ?>
	</div>

	<div class="textBox bigger_amt inline-block marginr22 hidden" id='po'>
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"po"); ?></div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "po", array('class'=> 'input_text_value')); ?>
			</div>
		<?php echo CCustomHtml::error($model, "po", array('id'=>"")); ?>
	</div>
	<div class="textBox bigger_amt inline-block marginr22 hidden"  style="<?php if($model->status == 'Printed' || $model->status =='Paid')
	{ echo "pointer-events:none;opacity: 0.5;";}?>" id='escalation'>
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"escalation"); ?></div>
		<div class="input_text">
			<?php echo CHtml::activeTextField($model, "escalation", array('class'=> 'input_text_value', 'onblur' => 'addPercent(this);','onclick' => "removePercent(this);return;" ,"onchange"=>"updateAmt(value,$model->id)")); ?>
			</div>
		<?php echo CCustomHtml::error($model, "escalation", array('id'=>"")); ?>
	</div>

	 <div style="right:135px;top:198px" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="right:70px;color:#333;top:198px" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
<script type="text/javascript">
$(function() { changeType('#Invoices_type'); changeCategory('#Invoices_partner');	console.log("1");  addPercent('#Invoices_escalation');  }); 

function addPercent(element) {	var val = parseFloat($(element).val());	if (isNaN(val)){ $(element).val('0%'); } else { $(element).val(val + '%'); } }
function removePercent(element) {	var val = parseFloat($(element).val());	if (isNaN(val) || val == 0) {	$(element).val("");	}else{ $(element).val(val); }	}

function changeCategory(element){	$this =  $(element);	console.log('2');
	if($this.val() != <?php echo Maintenance::PARTNER_SNS; ?> && $this.val() != "") {
			$('#Invoices_partner_status').parents('div.textBox').removeClass('hidden');
	}else if($this.val() == <?php echo Maintenance::PARTNER_SNS; ?> || $this.val() == null)	{
			$('#Invoices_partner_status').parents('div.textBox').addClass('hidden');
	}
}
function changeType(element){
	$this=$(element);
	if($this.val()=='Maintenance'){ $('#po').removeClass('hidden');  $('#escalation').removeClass('hidden');}else{ $('#po').addClass('hidden');$('#escalation').addClass('hidden');} }
</script>