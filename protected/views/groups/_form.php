<div class="form simpleForm">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'groups-form','enableAjaxValidation'=>false, )); ?>
	<div class="row">
		<?php echo $form->labelEx($model, 'description');?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50,'placeholder'=> $model->getAttributeLabel('description'))); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>