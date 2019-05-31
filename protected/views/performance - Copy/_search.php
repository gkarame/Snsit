<?php
/* @var $this ProjectsController */
/* @var $model Projects */
/* @var $form CActiveForm */
?>

<div class="wide search" id="search-projects">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); ?>
	
		
		<div class="row width_project_name width229">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'Project Name'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'primary_contact',
						'source'=>Users::getAllAutocomplete(),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'	=>'0',
							'showAnim'	=>'fold',
						),
						'htmlOptions'	=>array(
							'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
							'class'		=> "width100"
						),
				));
				?>
			</div>
		</div>
			
		<div class="row width_common">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'Customer'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'secondary_contact',		
						'source'=>Users::getAllAutocomplete(),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'	=>'0',
							'showAnim'	=>'fold',
						),
						'htmlOptions'	=>array(
							'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
							'class'		=> "width94"
						),
				));
				?>
			</div>
		</div>
			
			
		
		
		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		</div>
		<div class="horizontalLine search-margin"></div>	
		
	<?php $this->endWidget(); ?>
	
</div><!-- search-form -->