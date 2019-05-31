<div class="create" style="width:80%;"><div class="row" "><div><?php echo CHtml::activelabelEx($model,"Health Indicators", array("style"=>"font-size:13px !important;")); ?></div></div><br clear="all">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'sr-healthindicators-form','enableAjaxValidation'=>false,	'htmlOptions' => array(	'class' => 'ajax_submit',	'enctype' => 'multipart/form-data',	),	)); ?>
<table><tr><td><div  class="row_buttonlist ProjScopeRow "><?php echo $form->label($model, 'project_scope'); ?></div></td><td>
	<?php echo $form->radioButtonList($model, 'project_scope', StatusReportHealthIndicators::getHealthIndicatorsList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
</td><td>	<?php echo $form->error($model,'project_scope'); ?></td></tr></div><tr><td><div  class="row_buttonlist ResourcesRow ">
	<?php echo $form->label($model, 'resources'); ?></div></td><td>	<?php echo $form->radioButtonList($model, 'resources', StatusReportHealthIndicators::getHealthIndicatorsList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
	</td><td>	<?php echo $form->error($model,'resources'); ?></td></tr><tr><td><div  class="row_buttonlist">	<?php echo $form->label($model, 'timeline'); ?></div></td><td>
	<?php echo $form->radioButtonList($model, 'timeline', StatusReportHealthIndicators::getHealthIndicatorsList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
</td><td><?php echo $form->error($model,'timeline'); ?></td></tr><tr><td><div  class="row_buttonlist "><?php echo $form->label($model, 'project_finance'); ?></div>	</td>	<td>
	<?php echo $form->radioButtonList($model, 'project_finance', StatusReportHealthIndicators::getHealthIndicatorsList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
	</td><td><?php echo $form->error($model,'project_finance'); ?></td></tr><tr><td><div  class="row_buttonlist "><?php echo $form->label($model, 'risks_issues'); ?></div></td><td>
	<?php echo $form->radioButtonList($model, 'risks_issues', StatusReportHealthIndicators::getHealthIndicatorsList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
	</td><td><?php echo $form->error($model,'risks_issues'); ?></td></tr><tr><td>	<div  class="row_buttonlist "><?php echo $form->label($model, 'overall_project_health'); ?>
	</div></td><td>
	<?php echo $form->radioButtonList($model, 'overall_project_health', StatusReportHealthIndicators::getHealthIndicatorsList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
	</td><td><?php echo $form->error($model,'overall_project_health'); ?></td></tr>

	<tr class="row" ><td style="    font-size: 13px !important;font-family: Arial, Helvetica, sans-serif;padding-left: 2px;font-weight: bold;   color: #333;padding-top: 15px;" >FORMAT</td></tr>
	<tr><td>
	<?php echo $form->radioButtonList($model, 'format', StatusReportHealthIndicators::getTypeList(),  array('separator' => "  ", 'labelOptions'=>array('style'=>'display:inline'))); ?>
	</td><td><?php echo $form->error($model,'format'); ?></td></tr>

	</table><br clear="all" />
<div class="row buttons">	<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('id'=>'btn_save','onclick' => 'js:submitForm(this);return false;')); ?></div>	</div>
<?php $this->endWidget(); ?></div>