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
			<?php echo $form->label($model,'Customer'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'customer_id',		
					'source'=>Eas::getCustomersAutocomplete(),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'select'	=>"js: function(event, ui){ $('#Expenses_customer_name').val(ui.item.id);refreshProjectListsExp(ui.item.id); }"
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width100',
						'onblur' => 'blurAutocomplete(event, this, "#Expenses_customer_name", refreshProjectListsExp);',
							
					),
			));
			?>
		</div>
		<?php echo $form->hiddenField($model, 'customer_name'); ?> 
	</div>
		
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'project_id'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'project_id', Projects::getAllProjectsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
			</div>
		</div>
		<?php echo $form->error($model,'project_id'); ?>
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
			<?php echo $form->labelEx($model,'formats'); ?>
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
<script type="text/javascript">
$(document).ready(function() {
	refreshProjectListsExp();
});
function refreshProjectListsExp(id)
{
	var id_project_ts = "<?php echo $model->project_id;?>";
	if (!id) {
		id = $('#Expenses_customer_name').val();
	}
	if (id)
	{
		
		$('#Expenses_project_id').removeAttr('disabled');
			$.ajax({
	 			type: "GET",
	 			data: {id : id},					
	 			url: getProjectsByClientUrl, 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) {
				  		var selectOptions = '<option value=""></option>';
				  		var index = 1;
				  		$.each(data,function(id,name){
				  			var selected = (id == id_project_ts) ? 'selected="selected"' : ''; 
				  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
				  		});
					    $('#Expenses_project_id').html(selectOptions);
				  	}
		  		}
			});
	}else{
		$('#Expenses_project_id').html('');
		$('#Expenses_project_id').attr('disabled', 'disabled');
	}
}
</script>