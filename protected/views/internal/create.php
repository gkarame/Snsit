<div class="create">
<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/select2.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/scripts/select2.js');
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'internal-header-form',
	'enableAjaxValidation'=>false,
)); ?>
	
	<div class="row  marginr22 marginb20" style="height:70px !important;">
		<?php echo $form->labelEx($model, 'name'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'name', array('autocomplete'=>'off','style'=>'height:27px')); ?>
		</div>		
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row marginr22 marginb20" style="height:70px !important;">
			<?php echo CHtml::activeLabelEx($model, 'project manager *'); ?>	
			<div class="inputBg_create">
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'project_manager',	'source'=>Users::getAllAutocomplete(true),'options'=>array('minLength'=>'0','showAnim'=>'fold',),
						'htmlOptions'=>array('style'=>'height:27px','onfocus' => "javascript:$(this).autocomplete('search','');",	),	));	?>	
			</div>
			<?php echo $form->error($model,'project_manager'); ?>
	</div>


	<div class="row marginr22 marginb20" style="height:70px !important;">
			<?php echo CHtml::activeLabelEx($model, 'business manager *'); ?>	
			<div class="inputBg_create">
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'business_manager',	'source'=>Users::getAllAutocomplete(true),'options'=>array('minLength'=>'0','showAnim'=>'fold',),
						'htmlOptions'=>array('style'=>'height:27px','onfocus' => "javascript:$(this).autocomplete('search','');",	),	));	?>	
			</div>
			<?php echo $form->error($model,'business_manager'); ?>
	</div>
	
	<div class="row marginb20  margint10" style=" width: 290px;">
			<?php echo CHtml::activeLabelEx($model, 'eta'); ?>
			<div class="dataRow ">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "eta", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'showAnim' => 'fadeIn'		    		
			    	),			    	
			    	'htmlOptions' => array(
			    		'class' => 'datefield ',
			    		'style' => 'border: none;height:27px;' 
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo $form->error($model,'eta'); ?>
	</div>

	<div class="row  marginr22 marginb20 margint10">
		<?php echo $form->labelEx($model, 'estimated_effort'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'estimated_effort', array('autocomplete'=>'off','style'=>'height:27px')); ?>
		</div>		
		<?php echo $form->error($model,'estimated_effort'); ?>
	</div>

	<div class="row margint10">	
	<?php echo $form->labelEx($model,'recipients'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model,'recipients', Users::getAllSelect(), array('multiple'=>'true','style="width:300px;"')); ?></div>	
	<?php echo $form->error($model,'recipients'); ?></div>

	<div class="horizontalLine"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>
	</div>
	<br clear="all" />

<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(document).ready(function() {		$("#Internal_recipients").select2();	});
</script>
