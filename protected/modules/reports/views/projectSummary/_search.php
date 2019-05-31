<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */
?>

<div class="wide search">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'post',
		'id'=>'project-summary-form',
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
						'select'	=>"js: function(event, ui){ refreshProjectListsProjects(ui.item.id); }"
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width100',
						'onblur' => 'blurAutocomplete(event, this, "#Projects_id_customer", refreshProjectListsProjects);',
					),
			));
			?>
		</div>
		<?php echo $form->hiddenField($model, 'id_customer'); ?> 
	</div>
		
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'project'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'id', Projects::getAllProjectsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
			</div>
		</div>
		<?php echo $form->error($model,'id'); ?>
	</div>
	

	<div class="row width195">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'Status',array('class'=>'width55')); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'status',array('1'=>'Active','Inactive'=>'Inactive'), array('prompt' => Yii::t('translations', 'Choose status'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'status'); ?>
	</div>
	
	<div class="row width195">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'id_type',array('class'=>'width55')); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'id_type', Codelkups::getCodelkupsDropDown('ea_category'), array('prompt' => Yii::t('translations', 'Choose type'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'id_type'); ?>
	</div>
	 
			<div style="width:209px" class="row margint10  ">	<div class="inputBg_txt"><?php echo $form->label($model,'PM'); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,	'attribute' => 'project_manager','source'=>Projects::getUsersAutocompleteDS(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold',),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width100"),));?></div></div>
	
 

	<div class="row  margint10">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'order',array('class'=>'')); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'order', array('Customer'=>'By Customer (ascendingly)','Profit'=>'By Profit (descendingly)'), array('prompt' => Yii::t('translations', 'Choose order'))); ?>
			</div>
		</div>
		<?php echo $form->error($model,'order'); ?>
	</div>
	
	<div class="row width195 margint10">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'format',array('class'=>'width55')); ?>
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
	refreshProjectListsProjects();
});
function refreshProjectListsProjects(id)
{
	var id_project_ts = "<?php echo $model->id;?>";
	if (!id) {
		id = $('#Projects_id_customer').val();
	}
	console.log(id);	
	if (id)
	{
		$('#Projects_id').removeAttr('disabled');
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
				    $('#Projects_id').html(selectOptions);
			  	}
	  		}
		});
	}else{
		$('#Projects_id').html('');
		$('#Projects_id').attr('disabled', 'disabled');
	}
}
</script>