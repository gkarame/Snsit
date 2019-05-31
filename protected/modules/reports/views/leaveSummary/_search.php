<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */
$day = Timesheets::ITEM_DAYOFF;
$sick = Timesheets::ITEM_SICK_LEAVE;
$vacation = Timesheets::ITEM_VACATION;
?>

<div class="wide search">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'post',
		'id'=>'expense-summary-form',
	)); ?>
	<div class="row" style="width:209px">
		<div class="inputBg_txt">
			<?php echo $form->label($model,'user'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'user_id',		
					'source'=>Users::getAllAutocomplete1(true),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'prompt' => Yii::t('translations', 'All'),
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'prompt' => Yii::t('translations', 'All'),
						'class'=>'width100'
					),
			));
			?>
		</div>
	</div>
		
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'Type'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'type',  array("$day"=>'Day Off',"$sick"=>'Sick Leave',"$vacation"=>'Vacation'), array('prompt' => Yii::t('translations', 'All'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'Type'); ?>
	</div>
	
	<div class="row dateRow " style="width:209px">
		<div class="dateSearch selectBg_search">
			<?php echo $form->label($model,'startDate'); ?>
			<?php echo $form->textField($model,'startDate',array('class'=>'width103')); ?>
			<span class="calendar calfrom"></span>
		</div>
	</div>

	<div class="row dateRow " style="width:209px">
		<div class="dateSearch selectBg_search">
			<?php echo $form->label($model,'endDate'); ?>
			<?php echo $form->textField($model,'endDate',array('class'=>'width103')); ?>
			<span class="calendar calfrom"></span>
		</div>
	</div>
	
	<div class="row margint10">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'file', array('Pdf'=>'Pdf','Excel'=>'Excel'), array('prompt' => Yii::t('translations', 'Choose format'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'project_id'); ?>
	</div>
		
	<div class="btn">
		<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		
		
	</div>
	<div class="horizontalLine search-margin"></div>
		
	
	<?php $this->endWidget(); ?>

</div><!-- search-form -->