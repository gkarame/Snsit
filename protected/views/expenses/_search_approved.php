<div class="wide search" id="search-expenses" style="    overflow: inherit;">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',)); ?>
	<div class="row width132">
		<div class="smallBg_txt selectBg_search">
			<?php echo $form->label($model, 'no', array('class'=>"width55")); ?>
			<span class="spliter"></span>
			<?php echo $form->textField($model,'no',array('size'=>5,'maxlength'=>5,'class'=>'width48')); ?>
		</div>
	</div>
	<div class="row">
		<div class="inputBg_txt">
			<?php echo $form->label($model,'Customer'); ?>
			<span class="spliter"></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,'attribute' => 'customer_id','source'=>Customers::getAllAutocompleteNA(),
					'options'=>array('minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ 
						$('#Expenses_customer_name').val(ui.item.id);	refreshProjectListsExpenses(ui.item.id); }"),
					'htmlOptions'	=>array(	'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'onblur' => 'blurAutocomplete(event, this, "#Expenses_customer_name", refreshProjectListsExpenses);',),	)); ?>
		</div>
		<?php echo $form->hiddenField($model, 'customer_name'); ?> 
	</div>
	<div class="row">
		<div class="selectBg_search">
			<?php echo $form->label($model,'project_id', array('class' => 'width71')); ?>
			<span class="spliter"></span>
			<?php echo $form->dropDownList($model, 'project_id', Projects::getAllProjectsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true,'class'=>'selectors width158')); ?>
		</div>
	</div>
	<div class="row user width211">
		<div class="selectBg_search">
			<?php echo $form->label($model, 'user_id', array('class' => 'width45')); ?>
			<span class="spliter"></span>
			<?php echo $form->dropDownList($model, 'user_id', Users::getAllSelect(), array('prompt'=>'','class'=>'selectors width145')); ?>
		</div>
	</div>
	<div class="row dateRow width191 margint10">
		<div class="dateSearch selectBg_search">
			<?php echo $form->label($model,'startDate', array('class' => 'width55')); ?>
			<span class="spliter"></span>
			<?php echo $form->textField($model,'startDate',array('class'=>'width107')); ?>
			<span class="calendar calfrom"></span>
		</div>
	</div>
	<div class="row dateRow width191 margint10">
		<div class="dateSearch selectBg_search">
			<?php echo $form->label($model,'endDate', array('class' => 'width55')); ?>
			<span class="spliter"></span>
			<?php echo $form->textField($model,'endDate',array('class'=>'width107')); ?>
			<span class="calendar calfrom"></span>
		</div>
	</div>
	<div class="row margint10">
		<div class="selectBg_search">
			<?php echo $form->label($model, 'status', array('class' => 'width71')); ?>
			<span class="spliter"></span>
			<?php echo $form->dropDownList($model, 'status', Groups::getExpenseApprovalStatuses( Groups::getExpensePermissions()), array('prompt'=>'','class'=>'selectors width158')); ?>
		</div>
	</div>
	<div class="row user margint10 width211">
		<div class="selectBg_search">
			<?php echo $form->label($model, 'office', array('class' => 'width45')); ?>
			<span class="spliter"></span>
			<?php echo $form->dropDownList($model, 'currency', Codelkups::getCodelkupsDropDown('branch'), array('prompt'=>'','class'=>'selectors width145')); ?>
		</div>
	</div>
	<div class="btn">
		<?php echo CHtml::submitButton('Search',array('class'=>'search-btn')); ?>
		<div class="wrapper_action" id="action_tabs_right"><div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel"><div class="headli"></div><div class="contentli"> 
						<div class="cover"><div class="li noborder" onclick="printexpenses();">PRINT</div></div>
						<div class="cover"><div class="li noborder delete" onclick="approve();">APPROVE</div></div> 
						<div class="cover"><div class="li noborder delete" onclick="checkpay();">PAY</div></div> 
						<div class="cover"><div class="li noborder delete" onclick="transfer();"><a id="mylink">GENRATE TRANSFER</a></div></div> 
				</div><div class="ftrli"></div></div></div>
	</div>
	<div class="horizontalLine margin20"></div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
	$(document).ready(function(){ refreshProjectListsExpenses(); });
	function refreshProjectListsExpenses(id){
		var id_project_ts = "<?php echo $model->project_id;?>";
		if (!id) {	id = $('#Expenses_customer_name').val();	}
		if (id) {			
			$('#Expenses_project_id').removeAttr('disabled');
				$.ajax({type: "GET",data: {id : id},			
		 			url: '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsTrainingsByClient');?>', 
		 			dataType: "json",
		 			success: function(data) {
					  	if (data) {
					  		var selectOptions = '<option value=""></option>';	var index = 1;
					  		$.each(data,function(id,name){
					  			var selected = (id == id_project_ts) ? 'selected="selected"' : ''; 							       
					  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>'; });
						    $('#Expenses_project_id').html(selectOptions);
					  	} } });
		}else{
			$('#Expenses_project_id').html('');		$('#Expenses_project_id').attr('disabled', 'disabled');
		}
	}



	
</script>