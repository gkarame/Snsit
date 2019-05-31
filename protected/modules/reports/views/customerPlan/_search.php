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
		<div class="row  width274">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'customer'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'name',		
					'source'=>Customers::getAllAutocompleteActive(),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'prompt' => Yii::t('translations', 'All'),
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'prompt' => Yii::t('translations', 'All'),
						'class'=>'width165',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_id_user", null);',
					),

			));
			?>
			
		</div>
	</div>
	
<div class="row width274 ">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'Support'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'support_service', array('501'=>'Elite Plan', '502'=>'Premium Plan'), array('prompt' => Yii::t('translations', 'Choose Plan'))); ?>
			</div>
		</div>
	</div>

		
	
	<div class="row  width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'file', array('Excel'=>'Excel'), array('prompt' => Yii::t('translations', 'Choose formats'))); ?>
			</div>
		</div>
	</div>
				
	<div class="btn" style="margin-bottom:20px;">
		<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
	</div>
	
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