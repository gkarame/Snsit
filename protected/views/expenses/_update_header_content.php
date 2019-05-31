<div class="update">
	<div class="row em" style="background:none">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'expenses-form',
		'enableAjaxValidation'=>false,
		'action'=>Yii::app()->createAbsoluteUrl('expenses/updateHeader', array('id' => $model->id))
	)); ?>
		<div class="item inline-block normal">
			<?php echo $form->labelEx($model,'customer_id'); ?>
			<div class="customerRow">
			<?php echo $form->dropDownList($model, 'customer_id', Customers::getAllCustomersSelect(), array('prompt' => Yii::t('translations', 'Choose customer'), 'onchange' => 'refreshProjectsList()')); ?> 
			</div>
			<?php echo $form->error($model,'customer_id'); ?>
		</div>
		<div class="item inline-block normal">
			<?php echo $form->labelEx($model,'project_id'); ?>
			<div class="projectRow">
				<?php echo $form->dropDownList($model, 'project_id', Projects::getAllProjectsTrainingsSelect($model->customer_id)); ?>
			</div>
			<?php echo $form->error($model,'project_id'); ?>
		</div>
		<div class="item inline-block normal">
			<?php echo $form->labelEx($model,'startDate'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array( 'model'=> $model,'attribute' => "startDate", 'cssFile' => false,
			        'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'),
			    	'htmlOptions' => array('class' => 'datefield'), )); ?>
				<span class="calendar calfrom"></span></div>
			<?php echo $form->error($model,'startDate'); ?>
		</div>
		<div class="item inline-block normal last">
			<?php echo $form->labelEx($model,'endDate'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "endDate",'cssFile' => false,
			        'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'),
			    	'htmlOptions' => array('class' => 'datefield'), ));	?>
				<span class="calendar calfrom"></span></div>
			<?php echo $form->error($model,'endDate'); ?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
</div>