<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'invoice-header-form','enableAjaxValidation'=>false,)); ?>
	<div class="row marginr36">	<?php echo $form->labelEx($model,'id_customer'); ?>		
		<div class="inputBg_create">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name','source'=>Customers::getAllAutocomplete(),
				'options'=>array('minLength'=>'0','showAnim'=>'fold','select'=>"js:function(event, ui){ $('#Invoices_id_customer').val(ui.item.id);}",
					'change'=>"js:function(event, ui) { if (!ui.item){ $('#Invoices_id_customer').val(''); } }",),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",
					'onblur' => 'blurAutocomplete(event, this, "#Invoices_id_customer");'),));?>
		<?php echo $form->hiddenField($model, 'id_customer'); ?> </div>	
		<?php echo $form->error($model, 'customer_name') ?	$form->error($model,'customer_name') : $form->error($model, 'id_customer'); ?>		
	</div>
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'Invoice Description'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'invoice_description',array('autocomplete'=>'off')); ?></div>
		<?php echo $form->error($model,'project_name'); ?></div>
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'payment'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'payment',array('autocomplete'=>'off')); ?> 
		</div><?php echo $form->error($model,'payment'); ?></div>
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model,'payment_procente'); ?><div class="inputBg_create"><?php echo $form->textField($model, 'payment_procente',array('autocomplete'=>'off')); ?> 
	</div><?php echo $form->error($model,'payment_procente'); ?></div>		
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model,'old'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'old', array('No'=>'No','Yes'=>'Yes')); ?></div>
		<?php echo $form->error($model,'old'); ?></div>	
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'partner'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'partner', Codelkups::getCodelkupsDropDown('partner'), array('onchange' => 'changeCategory(this);')); ?>
		</div><?php echo $form->error($model,'partner'); ?></div>
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'sold_by'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'sold_by', Codelkups::getCodelkupsDropDown('unit'),array('prompt'=>'')); ?>	</div>
		<?php echo $form->error($model,'sold_by'); ?></div>	
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'currency'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'currency', Codelkups::getCodelkupsDropDown('currency'), array('prompt' => Yii::t('translations', 'Choose currency'))); ?>
		</div><?php echo $form->error($model,'currency'); ?></div>	
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'amount'); ?><div class="inputBg_create">
	<?php echo $form->textField($model, 'amount', array('autocomplete'=>'off')); ?>	</div>		
		<?php echo $form->error($model,'amount'); ?></div>	
	<div class="row marginb20 marginr36 "><?php echo $form->labelEx($model, 'invoice month *'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'invoice_date_month', Invoices::getMonths(),array('prompt'=>"")); ?></div>
		<?php if(isset($extra['invoice_date_month'])){?><div class="errorMessage"><?php echo $extra['invoice_date_month'];?></div> <?php }?></div>	
	<div class="row marginb20 marginr36 "><?php echo $form->labelEx($model, 'invoice year *'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'invoice_date_year', Invoices::getYears(),array('prompt'=>"")); ?></div>
		<?php if (isset($extra['invoice_date_year'])) { ?><div class="errorMessage"><?php echo $extra['invoice_date_year'];?></div><?php } ?>
	</div>
	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'type'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'type', array('Standard'=>'Standard' ,'Travel Expenses'=>'Travel Expenses', 'Expenses'=>'Expense Sheet',  'T&M'=>'T&M'), array('prompt'=>'Choose Type','onchange' => 'changeType(this);')); ?>
		</div><?php echo $form->error($model,'type'); ?></div>	
	<div class="row marginb20 marginr36 hidden"><?php echo $form->labelEx($model, 'partner_status'); ?>
		<div class="selectBg_create"><?php echo $form->dropDownList($model, 'partner_status', array('Not Paid'=>'Not Paid','Paid'=>'Paid')); ?>
		</div><?php echo $form->error($model,'partner_status'); ?></div>	
	<div class="row marginb20 marginr36  "><?php echo $form->labelEx($model, 'sns_share'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'sns_share'); ?></div>		
	<?php echo $form->error($model,'po'); ?></div>
		<div class="row marginb20 marginr36 hidden" id='po'><?php echo $form->labelEx($model, 'PO'); ?><div class="inputBg_create">
		<?php echo $form->textField($model, 'po', array('autocomplete'=>'off')); ?></div><?php echo $form->error($model,'po'); ?></div>	
	<div class="row marginb20 marginr36 hidden" id='ea'><?php echo $form->labelEx($model, 'EA#'); ?><div class="inputBg_create">
		<?php echo $form->textField($model, 'id_ea', array('autocomplete'=>'off')); ?></div><?php echo $form->error($model,'id_ea'); ?></div>	
	<div class="horizontalLine"></div><div class="row buttons">	<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?></div>
	<br clear="all" /><?php $this->endWidget(); ?></div>
<br clear="all" />
<script type="text/javascript">
$(function() { changeCategory('#Invoices_partner');}); 
function changeCategory(element){$this=$(element);if($this.val()!=<?php echo Maintenance::PARTNER_SNS;?>&&$this.val()!=""){ $('#Invoices_partner_status').parents('.row').removeClass('hidden')}else if($this.val()==<?php echo Maintenance::PARTNER_SNS;?>||$this.val()==null){ $('#Invoices_partner_status').parents('.row').addClass('hidden')}}
function changeType(element){$this=$(element);if($this.val()=='Standard'||$this.val()=='T&M'){$('#ea').removeClass('hidden');}else{ $('#ea').addClass('hidden'); }
	if($this.val()=='Maintenance'){ $('#po').removeClass('hidden');}else{ $('#po').addClass('hidden');} }
</script>