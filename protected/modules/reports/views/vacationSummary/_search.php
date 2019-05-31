<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */
?>

<div class="wide search">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'post',
		'id'=>'expense-summary-form',
	)); ?>
	<div class="row" style="width:209px">
		<div class="inputBg_txt">
			<?php echo $form->label($model,'User'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'id_user',		
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
						'class'=>'width100'
					),
			));
			?>
		</div>
	</div>
		
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'branch'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'branch', Codelkups::getCodelkupsDropDown('branch'), array('prompt' => Yii::t('translations', 'All'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'branch'); ?>
	</div>
	
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'year'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'years', UserPersonalDetails::getYears(), array('prompt' => Yii::t('translations', 'Choose year'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'branch'); ?>
	</div>
	
	<div class="row margint10">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'file', array('Pdf'=>'Pdf','Excel'=>'Excel'), array('prompt' => Yii::t('translations', 'Choose formats'))); ?>
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