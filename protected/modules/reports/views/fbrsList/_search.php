<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */
?>

<div class="wide search">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'post',
		'id'=>'licensing-form',
	)); ?>
	
	
	

	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'customer'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'customer',		
					'source'=>Customers::getAllAutocomplete(),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold'
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width165',
						
					),
			));
			?>
			</div>
		</div>
	</div>


	
	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'project'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'project',		
					'source'=>Projects::getProjectsAutocomplete(),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold'
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width165',
						
					),
			));
			?>
			</div>
		</div>
	</div>


	
	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'module'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'module', fbrsList::getCodelkupsDropDownOriginals('modules'), array('prompt' => Yii::t('translations', 'Choose Module'))); ?>
			</div>
		</div>
	</div>
	


	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'version'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'version', fbrsList::getCodelkupsDropDownOriginals('soft_version'), array('prompt' => '')); ?>
			</div>
		</div>
	</div>





	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'description'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
			<?php echo CHtml::activeTextField($model,'description', array('prompt' => Yii::t('translations', 'Choose FBR'))); ?>
			
			</div>
		</div>
	</div>
		
	
	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'keywords'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
			<?php echo CHtml::activeTextField($model,'keywords', array('prompt' => Yii::t('translations', 'Choose keywords'))); ?>
			
			</div>
		</div>
	</div>


	

	<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
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
