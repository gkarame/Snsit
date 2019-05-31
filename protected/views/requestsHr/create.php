<?php $this->breadcrumbs=array(	'Requests Hr',);?><div class="create"><?php $form=$this->beginWidget('CActiveForm', array('id'=>'ea-header-form','enableAjaxValidation'=>false,)); ?>
	<div class="row marginb20">	<?php echo $form->labelEx($model,'type'); ?>	<div class="selectBg_create">	<?php echo $form->dropDownList($model,'type', RequestsHr::requestsType(), array('prompt' => Yii::t('translations', 'Choose type'))); ?>
		</div>	<?php echo $form->error($model,'type'); ?>	</div>	<div class="row">	<?php echo $form->labelEx($model,'startDate'); ?>	<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'startDate', 'cssFile' => false,'options'=>array('minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'   	),
		    	'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off'),   ));	?>
			<span class="calendar calfrom"></span>	<?php echo $form->error($model,'startDate'); ?>	</div>	</div>	
	<div class="row">	<?php echo $form->labelEx($model,'note'); ?>	<div class="noteInput row_textarea jj" ><?php echo $form->textArea($model,'note', RequestsHr::requestsType()); ?>
			<?php echo $form->error($model,'note'); ?>	</div></div><div class="horizontalLine"></div>	<div class="row buttons"><?php echo CHtml::submitButton('Save', array('class'=>'submit')); ?>
	</div><br clear="all" /><?php $this->endWidget(); ?></div>
