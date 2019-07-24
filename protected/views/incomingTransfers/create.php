<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'irs-header-form','enableAjaxValidation'=>false,)); ?>
<table><tr><td>
	<div class="row customerRow">
	<div>
		<?php echo $form->labelEx($model,'id_customer'); ?>
		<div class="inputBg_create">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'id_customer',		
						'source'=>Customers::getAllAutocomplete(true),
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
							 'select'=>"js:function(event, ui) {
            var terms = split(this.value);
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push( ui.item.value );
            // add placeholder to get the comma-and-space at the end
            terms.push('');

            this.value = terms.join(', ');
            refreshProjectListsProjects();
            refreshDestination();
            (document.getElementById('.header_title')).onclick();
            
            return false;

        }"
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?> 		 
		</div>
	</div>
	<?php //echo $form->hiddenField($model, 'id_customer'); ?>
	<?php echo $form->error($model,'id_customer'); ?>
	</div>
</td>
<td><div class="row projectRow">
		<?php echo $form->labelEx($model, 'partner'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'partner', Codelkups::getCodelkupsDropDown('partner'), array('prompt' => Yii::t('translations', 'Choose partner'))); ?>
		</div>
		<?php echo $form->error($model,'partner'); ?>
	</div>
</td>

<td><div class="row projectRow">
		<?php echo $form->labelEx($model, 'offsetting'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'offsetting',IncomingTransfers::getOffsettingList(), array('prompt' => Yii::t('translations', ''))); ?>
		</div>
		<?php echo $form->error($model,'offsetting'); ?>
	</div>
</td>
</tr>
<tr>

<td><div class="row contactname" >
	<div>
		<?php echo $form->labelEx($model,'received_amount'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'received_amount',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'received_amount'); ?>
	</div>
</div></td>
<td><div class="row projectRow">
		<?php echo $form->labelEx($model, 'currency'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'currency', Codelkups::getCodelkupsDropDown('currency'), array('prompt' => Yii::t('translations', 'Choose Currency'))); ?>
		</div>
		<?php echo $form->error($model,'currency'); ?>
	</div>
</td>
<td><div class="row contactname" >
	<div>
		<?php echo $form->labelEx($model,'bank'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'bank',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'bank'); ?>
	</div>
</div></td>

</tr>
<tr>
<td><div class="row projectRow">
		<?php echo $form->labelEx($model, 'bank_dolphin'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'bank_dolphin', Codelkups::getCodelkupsDropDown('bank_code'), array('onchange' => 'updateAux(this);','prompt' => Yii::t('translations', 'Choose Bank'))); ?>
		</div>
		<?php echo $form->error($model,'bank_dolphin'); ?>
	</div>
</td>
<td><div class="row projectRow">
		<?php echo $form->labelEx($model, 'aux'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'aux', Codelkups::getCodelkupsDropDown('aux_code'), array('prompt' => Yii::t('translations', 'Choose Auxliary'))); ?>
		</div>
		<?php echo $form->error($model,'aux'); ?>
	</div>
</td>

	 
<td><div class="row projectRow">
		<?php echo $form->labelEx($model, 'month'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'month', IncomingTransfers::getMonths(), array('prompt' => Yii::t('translations', 'Choose Month'))); ?>
		</div>
		<?php echo $form->error($model,'month'); ?>
	</div>
</td>
</tr>
<tr>
<td><div class="row " >
	<div>
		<?php echo $form->labelEx($model,'notes'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'notes',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'notes'); ?>
	</div>
</div></td>
<td><div class="row " >
	<div>
		<?php echo $form->labelEx($model,'remarks'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'remarks',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'remarks'); ?>
	</div>
</div></td>

</tr>
</table>
 
	<div class="horizontalLine" style= "  margin: 0 0;"></div>
	<div class="row buttons">		<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>	</div>
	 
<?php $this->endWidget(); ?>
</div> 
<script>
	$(document).ready(function() {
		//updateAux($('#IncomingTransfers_bank_dolphin'));
	});
	function updateAux(element) {
		$this =  $(element);
		//var id = $('#Booking_id_customer').val();
		id= $this.val();
		if (id)
		{
			$.ajax({
	 			type: "GET",
	 			data: {'id' : id},					
	 			url: '<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/getAuxiliariesperbank');?>', 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) {
				  		var selectOptions = '<option value="">Choose Auxliary</option>';
				  		var index = 1;
				  		$.each(data,function(id,name){
				  			var selected =   ''; 
				  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
				  		});
					    $('#IncomingTransfers_aux').html(selectOptions);
				  	}
		  		}
			});
		} 
	}
</script>