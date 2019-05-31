<?php
/* @var $this TravelController */
/* @var $model Travel */
/* @var $form CActiveForm */
?>
<div class="wide search" id="search-travel">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); ?>
	
		
		
		
		
		<div class="row author ">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'id_user'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'id_user',		
						'source'=>TravelBooking::getUsersAutocomplete(),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
							'class'	  => "width141",
						),
				));
				?>
			</div>
		</div>
	
		
		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			
			<?php 
			if(GroupPermissions::checkPermissions('travel-list','write'))
			{
			?>
			<?php echo CHtml::link(Yii::t('translation', 'New Travel'), array('create'), array('class'=>'add-travel add-btn')); ?>
			<?php 
			}
			?>
		</div>
		<div class="horizontalLine search-margin"></div>	
		
	<?php $this->endWidget(); ?>
	
</div><!-- search-form -->
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>
<script>
	// refresh projects
	refreshProjectListsTravel();
</script>