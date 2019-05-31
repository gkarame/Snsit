<?php $form=$this->beginWidget('CActiveForm', array('id'=>'travel-form',	'enableAjaxValidation'=>false,)); ?>
<fieldset id="travel_fields" class="create"> 	<div class="formColumn">	<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'id_user'); ?>	
			<div class="inputBg_create">
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_user',	'source'=>Users::getAllAutocomplete(true),'options'=>array('minLength'=>'0','showAnim'=>'fold',),
						'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	),	));	?>	</div>	<?php echo CCustomHtml::error($model, 'id_user', array('id'=>"Travel_id_user_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'id_customer'); ?>	<div class="inputBg_create">	<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array(	'model' => $model,	'attribute' => 'id_customer','source'=>Customers::getAllAutocomplete(true),		'options'=>array('minLength'=>'0','showAnim'=>'fold',),
						'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'onblur' => "javascript:refreshProjectListsTravel();"),	));		?>
			</div>	<?php echo CCustomHtml::error($model, 'id_customer', array('id'=>"Travel_id_customer_em_")); ?>	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'id_project'); ?>
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'id_project', Projects::getAllProjectsTrainingsSelect($model->id_customer), array('onchange' => 'refreshBillableBasedOnProjectEA(this);','prompt'=>'')); ?>
			</div>	<?php echo CCustomHtml::error($model,'id_project', array('id'=>"Travel_id_project_em_")); ?>	</div>	<div class="row"> <?php echo CHtml::activeLabelEx($model, 'expense_type'); ?> 	<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'expense_type', Travel::getAllExpenseTypesSelect(), array('prompt'=>Yii::t('translations', 'Select Type'))); ?> </div>
			<?php echo CCustomHtml::error($model,'expense_type', array('id'=>"Travel_expense_type_em_")); ?>	</div>	</div>	<div class="formColumn"><div class="row">	<?php echo CHtml::activeLabelEx($model,'amount'); ?>		<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'amount',array('autocomplete'=>'off')); ?>		</div>		<?php echo CCustomHtml::error($model,'amount', array('id'=>"Travel_amount_em_")); ?>	</div>	<div class="row" >
			<?php echo CHtml::activeLabelEx($model, 'billable'); ?>	<div class="selectBg_create">		<?php echo CHtml::activeDropDownList($model, 'billable', Travel::getBillabledSelect()); ?>		</div>
			<?php echo CCustomHtml::error($model,'billable', array('id'=>"Travel_billable_em_")); ?>	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'status'); ?>
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'status', Travel::getStatusSelect()); ?>		</div>			
			<?php echo CCustomHtml::error($model,'status', array('id'=>"Travel_status_em_")); ?>	</div>
		<div class="row item inline-block normal">	<?php echo CHtml::activeLabelEx($model, 'date'); ?>		<div class="dataRow">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "date",'cssFile' => false,'options'=>array('dateFormat'=>'yy-mm-dd','showAnim' => 'fadeIn'), 	'htmlOptions' => array(   		'class' => 'datefield'   ,'autocomplete'=>'off'	),    ));				?>
				<span class="calendar calfrom"></span>	</div>	<?php echo CCustomHtml::error($model,'date', array('id'=>"Travel_date_em_")); ?></div>	</div>
	<div class="row red"> You can attach the travel documents by editing the travel expense after creating it.</div></fieldset>
<div class="row buttons saveDiv">	<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save')); ?></div>
	<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
</div><?php $this->endWidget(); ?>
<script> 	
	var testProjectActualsExpensesUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/testProjectActualExpenses');?>';
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>
<script>
	function refreshProjectListsTravel(){
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/GetProjectsTrainingsByClientName');?>'; 
	var customer = $('#Travel_id_customer').val();
	if(customer){
		$('#Travel_id_project').removeAttr('disabled');
		$.ajax({
 			type: "GET",
 			data: {id : customer},					
 			url: getProjectsByClientUrl, 
 			dataType: "json",
 			success: function(data) {
			  	if (data) {
			  		
			  		var arr = [];

			  		for (var key in data) {
			  		    if (data.hasOwnProperty(key)) {
			  		        arr.push({'id': key, 'label': data[key]});
			  		    }
			  		}
			  		
			  		 var sorted = arr.sort(function (a, b) {
		    				if (a.label > b.label) {
		      					return 1;
		      				}
		    				if (a.label < b.label) {
		     					 return -1;
		     				}

		    				return 0;
					 });
			  		
			  		var selectOptions = '<option value="">'+''+'</option>';
			  		$.each(sorted,function(index, val){
				        selectOptions += '<option value="'+val.id+'">'+val.label+'</option>';
				    });
				    $('#Travel_id_project').html(selectOptions);
			  	}
	  		}
		});	}else{		$('#Travel_id_project').attr('disabled', 'disabled');	} }
</script>