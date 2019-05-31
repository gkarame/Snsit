<div class="wide search" id="search-expenses">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',)); ?>
	<div class="row id-row">
		<div class="inputBg_txt">
			<?php echo $form->label($model,'no'); ?>
			<span class="spliter"></span>
			<?php echo $form->textField($model,'no'); ?>
		</div>
	</div>
	<div class="row">
		<div class="inputBg_txt">
			<?php echo $form->label($model,'Customer'); ?>
			<span class="spliter"></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_id','source'=>Customers::getAllAutocompleteNA(),
					'options'=>array('minLength'	=>'0',	'showAnim'	=>'fold',	'select'	=>"js: function(event, ui){ 
						$('#Expenses_customer_name').val(ui.item.id);	refreshProjectListsExpenses(ui.item.id); }" ),
					'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'onblur' => 'blurAutocomplete(event, this, "#Expenses_customer_name", refreshProjectListsExpenses);',),));	?>
		</div>
		<?php echo $form->hiddenField($model, 'customer_name'); ?> 
	</div>
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->label($model, 'project_id'); ?>
			<span class="spliter"></span>
			<div class="select_container">
				<?php echo $form->dropDownList($model, 'project_id', Projects::getAllProjectsTrainingsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
			</div>
		</div>
		<?php echo $form->error($model,'project_id'); ?>
	</div>
	<div class="row last-row small-row">
		<div class="selectBg_search">
			<?php echo $form->label($model, 'status'); ?>
			<span class="spliter"></span>
			<div class="select_container">
				<div class="arrow"></div>
				<?php echo $form->dropDownList($model, 'status', Expenses::getStatusList(), array('prompt'=>'')); ?>
			</div>
		</div>
	</div>
	<div class="btn">
		<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		<?php echo CHtml::link(Yii::t('translation', 'New Expense'), array('create'), array('class'=>'add-expense add-btn')); ?>
	</div>
	<div class="horizontalLine search-margin"></div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
		$(document).ready(function() { refreshProjectListsExpenses();	});
		function refreshProjectListsExpenses(id){
			var id_project_ts = "<?php echo $model->project_id;?>";
			if (!id) {	id = $('#Expenses_customer_name').val(); }
			if (id){
				$('#Expenses_project_id').removeAttr('disabled');
					$.ajax({type: "GET", data: {id : id}, url: '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsTrainingsByClient');?>', dataType: "json",
			 			success: function(data) {
						  	if (data) {
						  		var arr = [];
						  		for (var key in data) {  if (data.hasOwnProperty(key)) {   arr.push({'id': key, 'label': data[key]}); } }						  		
						  		 var sorted = arr.sort(function (a, b) {
					    				if (a.label > b.label) { return 1; }
					    				if (a.label < b.label) { return -1; }
					    				return 0; });							  	
						  		var selectOptions = '<option value=""></option>';	var index = 1;
						  		$.each(sorted,function(index, val){
						  			var selected = (val.id == id_project_ts) ? 'selected="selected"' : ''; 
						  			selectOptions += '<option value="' + val.id+'"' + selected + '>'+val.label+'</option>';
						  		});
							    $('#Expenses_project_id').html(selectOptions);
						  	} } });
			}else{	$('#Expenses_project_id').html('');	$('#Expenses_project_id').attr('disabled', 'disabled');	}
		}
</script>