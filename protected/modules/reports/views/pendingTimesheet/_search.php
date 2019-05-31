<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */ /*
?>

<div class="wide search">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'post',
		'id'=>'timesheet-summary-form',
	)); ?>
	<div class="row" style="width:209px">
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'customer_id'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'customer_id',		
					'source'=>Customers::getAllAutocomplete(),
					// additional javascript options for the autocomplete plugin
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'select'	=>"js: function(event, ui){ refreshProjectListsProjects(ui.item.id); }"
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width100',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_id_customer", refreshProjectListsProjects);',
					),
			));
			?>
		</div>
		<?php echo $form->hiddenField($model, 'id_customer'); ?> 
	</div>
		
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'id_project'); ?>
			<span class="spliter"></span>
			<div class="select_container width111" >
				<?php echo $form->dropDownList($model, 'id_project', Projects::getAllProjectsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
			</div>
		</div>
	</div>
	
	
	<div class="row  " style="width:209px">
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
						'class'=>'width100',
						'onblur' => 'blurAutocomplete(event, this, "#TimesheetSummary_id_user", null);',
					),

			));
			?>
			<?php echo $form->hiddenField($model, 'id_user'); ?> 
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
	</div>
		<div class="row dateRow margint10">
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
		<div class="row dateRow margint10">
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
	
	
	
		
	<div class="btn">
		<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		
		
	</div>
	<div class="horizontalLine search-margin"></div>
		
	
	<?php $this->endWidget(); */ ?>

</div><!-- search-form -->
<script type="text/javascript">
$(document).ready(function() {
	refreshProjectListsProjects();
});

function refreshProjectListsProjects(id) {
	var id_project_ts = "<?php echo $model->id_project;?>";
	if (!id) {
		id = $('#TimesheetSummary_id_customer').val();
	}
	console.log(id);	
	if (id)
	{
		$('#TimesheetSummary_id_project').removeAttr('disabled');
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
				    $('#TimesheetSummary_id_project').html(selectOptions);
			  	}
	  		}
		});
	} else {
		$('#TimesheetSummary_id_project').html('');
		$('#TimesheetSummary_id_project').attr('disabled', 'disabled');
	}
}
</script>