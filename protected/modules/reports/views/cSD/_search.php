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
			<?php echo $form->labelEx($model,'country'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'country', Codelkups::getCodelkupsDropDown('country'), array('prompt' => Yii::t('translations', 'Choose Country'))); ?>
			</div>
		</div>
	</div>
<div class="row   width300">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'account_manager' , array('style'=>'width:134px;')); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'account_manager',		
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
						'class'=>'width137',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_account_manager", null);',
					),

			));
			?>
			<?php echo $form->hiddenField($model, 'id_account_manager'); ?> 
		</div>
	</div>

		<div class="row margint10 width274">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'brands'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'brands',		
					'source'=>Customers::getAllBrands(),
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

	
	
	

	<div class="row  margint10 width274">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'ca'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'ca',		
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
						'class'=>'width165',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_ca", null);',
					),

			));
			?>
			<?php echo $form->hiddenField($model, 'id_ca'); ?> 
		</div>
	</div>
	
	
		<div class="row  margint10 width300">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'type_of_items', array('class'=>'width134')); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'product_type',		
					'source'=>Customers::getAllproductType(),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'prompt' => Yii::t('translations', 'All'),
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'prompt' => Yii::t('translations', 'All'),
						'class'=>'width137',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_product_type", null);',
					),

			));
			?>
			
		</div>
	</div>

<div class="row width274 margint10">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'product'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'product', Codelkups::getCodelkupsDropDown('product'), array('prompt' => Yii::t('translations', 'Choose Product'))); ?>
			</div>
		</div>
	</div>

		<div class="row  margint10 width274">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'erp'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'erp',		
					'source'=>Customers::getAllERP(),
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
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_erp", null);',
					),

			));
			?>
			
		</div>
	</div>	

<div class="row margint10 width300">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'wms_db_type',array('style'=>'width:134px;')); ?>
			<span class="spliter"></span>
			<div class="select_container width137" >
				<?php echo $form->dropDownList($model, 'wms_db_type', Codelkups::getCodelkupsDropDown('wms_db_type'), array('prompt' => Yii::t('translations', 'Choose WMS DB Type'))); ?>
			</div>
		</div>
	</div>
<div class="row margint10 width274 ">
	<div class="selectBg_search">
			<?php echo $form->labelEx($model,'industry'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" onclick='javascript:showdropdown();' >
				<?php echo $form->dropDownList($model, 'industry', Codelkups::getCodelkupsDropDown('industry'), array('id'=>'searchindustry','multiple' => 'multiple','prompt' => Yii::t('translations', 'Choose Industry'),'style'=>'height:150px; width:173px;background-color:white; position:absolute;visibility:hidden')); ?>
			</div>
		</div>
		
	</div>			

<div class="row margint10 width274">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'wms_vers.'); ?>
			<span class="spliter"></span>
			<div class="select_container width165" >
				<?php echo $form->dropDownList($model, 'soft_version', Codelkups::getCodelkupsDropDown('soft_version'), array('prompt' => Yii::t('translations', 'Choose WMS Versions'))); ?>
			</div>
		</div>
	</div>


	
	<div class="row margint10 width300">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format',array('style'=>'width:134px;')); ?>
			<span class="spliter"></span>
			<div class="select_container width137" >
				<?php echo $form->dropDownList($model, 'file', array('Excel'=>'Excel'), array('prompt' => Yii::t('translations', 'Choose formats'))); ?>
			</div>
		</div>
	</div>
		<div class="margint10"></div>		
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