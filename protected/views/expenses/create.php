<?php $this->setPageTitle(Yii::app()->name.' - New Expense'); ?>
<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'expenses-form', 'enableAjaxValidation'=>false, )); ?>
	<div class="row marginr36">
		<?php echo $form->labelEx($model,'customer_id'); ?>		
		<div class="inputBg_create">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,	'attribute' => 'customer_name',	'source'=>Customers::getAllAutocompleteNA(),
				'options'=>array('minLength'=>'0','showAnim'=>'fold','select'=>"js:function(event, ui) {
                    				$('#Expenses_customer_id').val(ui.item.id);	getCustomerProjects('#Expenses_customer_id'); }",
					'change'=>"js:function(event, ui) { if (!ui.item) { $('#Expenses_customer_id').val(''); } }",	),
				'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",
				'onblur' => 'blurAutocomplete(event, this, "#Expenses_customer_id");'),	));	?>
		<?php echo $form->hiddenField($model, 'customer_id'); ?> 
		</div>		
		<?php echo $form->error($model, 'customer_name') ?	$form->error($model,'customer_name') : $form->error($model, 'customer_id');	 ?>		
	</div> 
	<div class="row" id ="proj">
		<?php echo $form->labelEx($model,'Project/Training'); ?>
		<div class="projectRow">
			<?php echo $form->dropDownList($model, 'project_id', Projects::getAllProjectsTrainingsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
		</div>
		<?php echo $form->error($model,'project_id'); ?>
	</div>
	<div class="horizontalLine margint15"></div>
	<div class="row startDateRow">
		<?php echo $form->labelEx($model,'startDate'); ?>
		<div class="dataRow"><?php echo $form->textField($model,'startDate',array('autocomplete'=>'off')); ?><span class="calendar calfrom"></span></div>
		<?php echo $form->error($model,'startDate'); ?>
	</div>
	<div class="row endDateRow">
		<?php echo $form->labelEx($model,'endDate'); ?>
		<div class="dataRow"><?php echo $form->textField($model,'endDate',array('autocomplete'=>'off')); ?><span class="calendar calfrom"></span></div>
		<?php echo $form->error($model,'endDate'); ?>
	</div>
	<div class="row " >
			<?php echo $form->labelEx($model,'prefered currency'); ?>
			<div class="projectRow" style="width:130px !important;">
			<?php echo $form->dropDownList($model, 'currency', ExpensesDetails::getCurrencies(), array('prompt' => Yii::t('translations', 'Choose Currency'))); ?> 
			</div>
			<?php echo $form->error($model,'currency'); ?>
		</div>
<div class="row" style="margin-left:20px;">
			<?php echo $form->labelEx($model,'Default payable'); ?>
			<div class="projectRow" style="width:100px !important;">
			<?php echo $form->dropDownList($model, 'payable', array('Yes'=>'Yes','No'=>'No'), array('class'=>'input_text_value', 'prompt' => Yii::t('translations', 'Choose Default'))); ?> 
			</div>
			<?php echo $form->error($model,'payable'); ?>
		</div>		
		<div class="horizontalLine margint15"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Create', array('class'=>'submit')); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
$(document).ready(function() { });
function getCustomerProjects(element) {
	$this = $(element);	var val = $this.val();
	if(val == 0){	$('#proj').hide();	}
	else if (val) {
		$('#proj').show();
		$.ajax({
	 		type: "GET",  	url: '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsTrainingsByClient');?>',
			data: { id: val,exp:true},  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		var arr = [];
			  		for (var key in data) {   if (data.hasOwnProperty(key)) { arr.push({'id': key, 'label': data[key]}); } }			  		
			  		 var sorted = arr.sort(function (a, b) {
		    				if (a.label > b.label) { return 1; }
		    				if (a.label < b.label) { return -1; }
		    				return 0; });
			  		var selectOptions = '<option value=""></option>'; var index = 1;
			  		$.each(sorted,function(index,val){ selectOptions += '<option value="' + val.id+'">'+val.label+'</option>'; });
			  		$('#Expenses_project_id').prop('disabled', false); $('#Expenses_project_id').html(selectOptions);
			  	} } });
	} else { $('#Expenses_project_id').html('<option value=""></option>');	}
}
</script>
<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>