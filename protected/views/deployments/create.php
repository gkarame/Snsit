<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'deployments-header-form',
	'enableAjaxValidation'=>false,
)); ?>
	
	
	<div class="row  marginr22 marginb20">
			<?php echo $form->labelEx($model,'id_customer'); ?>		
		<div class="inputBg_create">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
				'model' => $model,	'attribute' => 'customer_name',	'source'=>Customers::getAllAutocomplete(),
				'options'=>array('minLength'=>'0','showAnim'=>'fold',
											'select'	=>"js: function(event, ui){ refreshProjectListsProjects(ui.item.id);  refrehsversionlist(ui.item.id); }"			),
				'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'onblur' => 'blurAutocomplete(event, this, "#Deployments_id_customer");'),));	?>
		<?php echo $form->hiddenField($model, 'id_customer'); ?> 
		</div>		
		<?php echo $form->error($model, 'customer_name') ? $form->error($model,'customer_name') : $form->error($model, 'id_customer'); ?>		
	</div>
	<div class="row  marginr22 marginb20">
		<?php echo $form->labelEx($model, 'description'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'description', array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'description'); ?>
	</div>
	
	<div class="row  marginr22 marginb20">
		<?php echo $form->labelEx($model, 'module'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'module', array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'module'); ?>
	</div>

	<div class="row  marginr22 marginb20">
		<?php echo $form->labelEx($model, 'infor_version'); ?>
		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'infor_version', Maintenance::getVersionListPerCustomer($model->id_customer), array('prompt' => Yii::t('translations', 'Choose Software Version'))); ?>
		</div>
		<?php echo $form->error($model,'infor_version'); ?>
	</div>

	<div class="row  marginr22 marginb20">
		<?php echo $form->labelEx($model, 'location'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'location', array('autocomplete'=>'off','value' => !empty($model->location)?$model->location:'C:\SNS\Deployment_Logs\\')); ?>
		</div>		
		<?php echo $form->error($model,'location'); ?>
	</div>
	<div class="row marginb20">
			<?php echo CHtml::activeLabelEx($model, 'dep_date'); ?>
			<div class="dataRow " style="margin-right: 20px;">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "dep_date", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'showAnim' => 'fadeIn'		    		
			    	),			    	
			    	'htmlOptions' => array(
			    		'class' => 'datefield ',
			    		'style' => 'border: none;height:23px;',
			    		'value'=> !isset($model->dep_date)? date("m/d/Y"):$model->dep_date,
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo $form->error($model,'dep_date'); ?>
	</div>
	
	<div class="row  marginr22 marginb20 hidden">
		<?php echo $form->labelEx($model, 'assigned_srs'); ?>
		<div class="inputBg_create">
			<?php echo $form->textField($model, 'assigned_srs'); ?>
		</div>		
		<?php echo $form->error($model,'assigned_srs'); ?>
	</div>
	<div class="row  marginr22 marginb20">
		<?php echo $form->labelEx($model, 'notes'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'notes', array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'notes'); ?>
	</div>
    <div class="row  marginr22 marginb20" style="width: 267px;">
        <?=CHtml::label('source', 'source_radio'); ?>
        <div class="">
            <style>#source_radio{display:flex;height: 33px;align-items: center;}#source_radio label{font-weight: 400;text-transform: unset;margin-right: 20px;padding: 0 5px;}</style>
            <?=CHtml::radioButtonList('source_radio','',['Project','Support'])?>
        </div>
    </div>

    <div class="row  marginr22 marginb20 hidden" id="projects_block">
        <?php echo CHtml::label('projects *', 'source'); ?>
        <div class="selectBg_create">
            <?php echo $form->dropDownList($model, 'source', Deployments::getProjectsDD($model->id_customer), array('prompt' => Yii::t('translations', 'Choose Source'), 'onchange' => 'showSrs(this);')); ?>
        </div>

        <?php echo $form->error($model,'source'); ?>
    </div>

    <div class="row  marginr22 marginb20 hidden" id="assigned_srs_block">
        <?php echo $form->labelEx($model, 'assigned_srs *'); ?>
        <div class="inputBg_create">
            <?php echo $form->textField($model, 'assigned_srs', array('autocomplete'=>'off')); ?>
        </div>
        <?php echo $form->error($model,'assigned_srs'); ?>
    </div>

	<div class="horizontalLine"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>
	</div>
	<br clear="all" />

<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
    $('#source_radio input').change(function () {
        if ($(this).val() === '0'){
            $('#projects_block').removeClass('hidden')
            $('#assigned_srs_block').addClass('hidden')
        }else{
            $('#assigned_srs_block').removeClass('hidden')
            $('#projects_block').addClass('hidden')
        }
    })

var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('deployments/GetProjectsByClient');?>';
var getVersionsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('deployments/GetVersionsPerClient');?>';
	$(function() {
		showSrs('#Deployments_source');
		/*changeService('#Maintenance_support_service');
		$("#Maintenance_starting_date").datepicker({ dateFormat: 'dd/mm/yy' });
	
	 	 $("#Maintenance_starting_date").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Maintenance_starting_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Maintenance_starting_date").offset().left));

		 });*/
		
	}); 
	function showSrs(element) {
		$this =  $(element);
		if($this.val() == '663') {
			$('#Deployments_assigned_srs').parents('.row').removeClass('hidden');
		}else{
			$('#Deployments_assigned_srs').parents('.row').addClass('hidden');
		}
	}
	
	function refrehsversionlist(id) {
		var id_project_ts = "<?php echo $model->infor_version;?>";
		if (!id) {
			id = $('#Deployments_id_customer').val();
		}	
		if (id)
		{
			$('#Deployments_infor_version').removeAttr('disabled');
			$.ajax({
	 			type: "GET",
	 			data: {id : id},					
	 			url: getVersionsByClientUrl, 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) {
				  		var selectOptions = '<option value=""></option>';
				  		var index = 1;
				  		$.each(data,function(id,name){
				  			var selected = (id == id_project_ts) ? 'selected="selected"' : ''; 
				  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
				  		});
					    $('#Deployments_infor_version').html(selectOptions);
				  	}
		  		}
			});
		} else {
			$('#Deployments_infor_version').html('');
			$('#Deployments_infor_version').attr('disabled', 'disabled');
		}
	}
	function refreshProjectListsProjects(id) {
		var id_project_ts = "<?php echo $model->source;?>";
		if (!id) {
			id = $('#Deployments_id_customer').val();
		}	
		if (id)
		{
			$('#Deployments_source').removeAttr('disabled');
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
					    $('#Deployments_source').html(selectOptions);
				  	}
		  		}
			});
		} else {
			$('#Deployments_source').html('');
			$('#Deployments_source').attr('disabled', 'disabled');
		}
	}
	function changeService(element)
	{
		$this =  $(element);
		if($this.val() == 501 && $this.val() != "") {
			$('#Maintenance_sma_recipients').parents('.row').removeClass('hidden');
			$('#Maintenance_sma_instances').parents('.row').removeClass('hidden');
		}else if($this.val() != 501 || $this.val() == null)
			{
				$('#Maintenance_sma_recipients').parents('.row').addClass('hidden');
				$('#Maintenance_sma_instances').parents('.row').addClass('hidden');
			}
	}

	function changeCategory(element) {
		$this =  $(element);
		if($this.val() != <?php echo Maintenance::PARTNER_SNS; ?> && $this.val() != "") {
			$('#Maintenance_sns_share').parents('.row').removeClass('hidden');
		}else if($this.val() == <?php echo Maintenance::PARTNER_SNS; ?> || $this.val() == null)
			{
				$('#Maintenance_sns_share').parents('.row').addClass('hidden');
			}
	}
	function addPercent(element) {
		var val = parseFloat($(element).val());
		if (isNaN(val)) {
			$(element).val('0%');			
		} else {
			$(element).val(val + '%');
		}
	}

	function removePercent(element) {
		var val = parseFloat($(element).val());
		if (isNaN(val) || val == 0) {
			$(element).val("");
		} else {
			$(element).val(val);
		}
	}


	
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
