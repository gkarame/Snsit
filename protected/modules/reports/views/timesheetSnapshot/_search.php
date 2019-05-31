<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */
?>

<div class="wide search">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'post',
		'id'=>'timesheet-snapshot-form',
	)); ?>
	
	
	<div class="row dateRow ">
			<div class="dateSearch inputBg_txt">
				<?php echo $form->label($model,'from'); ?>
				<span class="spliter"></span>
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model, 
			        'attribute'=>'from', 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy'
			    	),
			    	'htmlOptions'=>array('class'=>'width111'),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
		</div>
		<div class="row dateRow ">
			<div class="dateSearch inputBg_txt">
				<?php echo $form->label($model,'to'); ?>
				<span class="spliter"></span>
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model, 
			        'attribute'=>'to', 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy'
			    	),
			    	'htmlOptions'=>array('class'=>'width111'),
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
		</div>
	
	<div class="row  ">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'user'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'user',		
					'source'=>Users::getAllAutocomplete(true),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'prompt' => Yii::t('translations', 'All'),
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'prompt' => Yii::t('translations', 'All'),
						'class'=>'width111',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_id_user", null);',
					),

			));
			?>
			<?php echo $form->hiddenField($model, 'id_user'); ?> 
		</div>
	</div>
	
	
	
	<div class="row margint10">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'file', array('Excel'=>'Excel'), array('prompt' => Yii::t('translations', 'Choose formats'))); ?>
			</div>
		</div>
	</div>
		
	
	
	
	
						
	<div class="btn">
		<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		
		
	</div>
	<div class="horizontalLine search-margin"></div>
		
	
	<?php $this->endWidget(); ?>

</div><!-- search-form -->
<script type="text/javascript">


	function CheckOrUncheckInput(obj)
	{
		var checkBoxDiv = $(obj);
		var input = checkBoxDiv.find('input[type="checkbox"]');
		
		if (checkBoxDiv.hasClass('checked')) {
			checkBoxDiv.removeClass('checked');
			input.prop('checked', false);
		}
		else {
			checkBoxDiv.addClass('checked');
			input.prop('checked', true);
		}
	}
</script>