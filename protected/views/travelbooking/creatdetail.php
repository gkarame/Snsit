<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'travel-form',
	'enableAjaxValidation'=>false,
)); ?>
<fieldset id="travel_fields" class="create"> 
	<div class="formColumn">
		
		
		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'project_id'); ?>
			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'project_id', Projects::getAllProjectsSelect(),array('prompt'=>'')); ?>
			</div>
			
			<?php echo CCustomHtml::error($model,'project_id', array('id'=>"TravelBooking_project_id_em_")); ?>
		</div>
		
		
		
		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'destination_country'); ?>
			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'destination_country', TravelBooking::getAllCountriesSelect(), array('prompt'=>Yii::t('translations', 'Select Country'))); ?>
			</div>
			
			<?php echo CCustomHtml::error($model,'destination_country', array('id'=>"Travel_destination_country_em_")); ?>
		</div>
	</div>
		<div class="row item inline-block normal">
			<?php echo CHtml::activeLabelEx($model, 'from_date'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "from_date", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'yy-mm-dd',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		'class' => 'datefield'
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'from_date', array('id'=>"TravelBooking_from_date_em_")); ?>
		</div>
		
		
		<div class="row item inline-block normal">
			<?php echo CHtml::activeLabelEx($model, 'to_date'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "to_date", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'yy-mm-dd',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		'class' => 'datefield'
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'to_date', array('id'=>"TravelBooking_to_date_em_")); ?> 
		</div>
	</div>
</fieldset>

<div class="row buttons saveDiv">
	<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save')); ?></div>
	<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
</div>
<?php $this->endWidget(); ?>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var testProjectActualsExpensesUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/testProjectActualExpenses');?>';
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>
<script>
	// refresh projects
	function refreshProjectListsTravel()
{
	var customer = $('#Travel_id_customer').val();
	console.log(customer);
	if(customer)
	{
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
		});
	}
	else
	{
		$('#Travel_id_project').attr('disabled', 'disabled');
	}
}
</script>