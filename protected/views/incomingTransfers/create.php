<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'irs-header-form','enableAjaxValidation'=>false,)); ?>
<table><tr><td>
	<div class="row customerRow">
	<div>
		<?php echo $form->labelEx($model,'id_customer'); ?>
		<div class="inputBg_create">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name','source'=>Customers::getAllAutocomplete(),
				'options'=>array('minLength'=>'0','showAnim'=>'fold',
					'select'=>"js:function(event, ui) { $('#InstallationRequests_id_customer').val(ui.item.id); }",
					'change'=>"js:function(event, ui) { if (!ui.item) { $('#InstallationRequests_id_customer').val(''); }
									}", ),
				'htmlOptions'=>array( 'onfocus' => "javascript:$(this).autocomplete('search','');",
					'onblur' => 'blurAutocomplete(event, this, "#InstallationRequests_id_customer");' ), )); ?>		 
		</div>
	</div>
	<?php echo $form->hiddenField($model, 'id_customer'); ?>
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
<td><div class="row contactname" >
	<div>
		<?php echo $form->labelEx($model,'received_amount'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'received_amount',array('autocomplete'=>'off')); ?>
		</div>
	<?php echo $form->error($model,'received_amount'); ?>
	</div>
</div></td>
</tr>
<tr>

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
	});
</script>