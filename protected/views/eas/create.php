<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'ea-header-form',	'enableAjaxValidation'=>false,)); ?>
	<div class="row marginb20"   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'id_customer'); ?>		
		<div class="inputBg_create width200">
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
				'model' => $model,	'attribute' => 'customer_name',	'source'=>Customers::getAllAutocomplete(),
				'options'=>array('minLength'=>'0','showAnim'=>'fold',
					'select'=>"js:function(event, ui) {
						validateLocation('#Eas_id_customer');
                    				$('#Eas_id_customer').val(ui.item.id);
                    				if (!$('#Eas_id_parent_project').parents('.row').hasClass('hidden')){ getCustomerProjects('#Eas_id_customer');	} }",
					'change'=>"js:function(event, ui) {
                   					if (!ui.item) {	$('#Eas_id_customer').val('');	}else{
						validateLocation('#Eas_id_customer');}
									if (!$('#Eas_id_parent_project').parents('.row').hasClass('hidden')){	getCustomerProjects('#Eas_id_customer'); }	}
									",	),
				'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'onblur' => 'blurAutocomplete(event, this, "#Eas_id_customer");'),));	?>
		<?php echo $form->hiddenField($model, 'id_customer'); ?> 
		</div>		
		<?php echo $form->error($model, 'customer_name') ? $form->error($model,'customer_name') : $form->error($model, 'id_customer'); ?>		
	</div>
	<div class="row marginb20"   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'category'); ?>
		<div class="selectBg_create width208">
			<?php echo $form->dropDownList($model,'category', Codelkups::getCodelkupsDropDown('ea_category'), array('prompt' => Yii::t('translations', 'Choose category'), 'onchange' => 'changeCategory(this);')); ?>
		</div>		
		<?php echo $form->error($model,'category'); ?>
	</div>	
	<div class="row marginb20 hidden"   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'Subtype *'); ?>
		<div class="selectBg_create width208">
			<?php echo $form->dropDownList($model,'template', Eas::getTemplateList()); ?>
		</div>		
		<?php echo $form->error($model,'template'); ?>
	</div>	
	<div class="row marginb20 "  style="height:70px !important;" >
		<?php echo $form->labelEx($model, 'CRM #'); ?>
		<div class="inputBg_create width200">
		<?php echo $form->textField($model, 'crmOpp', array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'crmOpp'); ?>
	</div>
	<div class="row marginb20  hidden"  style="height:70px !important;"  >
		<?php echo $form->labelEx($model,'Project *'); ?>
		<div class="inputBg_create width200">
			<?php echo $form->textField($model, 'project_name',array('autocomplete'=>'off')); ?>
		</div>
		<?php echo $form->error($model,'id_project'); ?>
	</div>	
	<div class="row marginb20 hidden"   style="height:70px !important;" >
		<?php echo $form->labelEx($model, 'Parent Project *'); ?>
		<div class="selectBg_create width208">
			<?php echo $form->dropDownList($model, 'id_parent_project', array(), array('prompt' => Yii::t('translations', 'Choose project'))); ?>
		</div>
		<?php echo $form->error($model,'id_parent_project'); ?>
	</div>	

	<div class="row hidden" id="customization" style="margin-left:50px; margin-top:6px;margin-bottom: 30px;height:70px;">									
            		<?php echo CHtml::activeLabelEx($model, 'include customization support?', array('style' => 'margin-left:-50px;margin-top: 10px;'));?>	<div class="row <?php echo ($model->customization == '1')?'checked' : ''?>" id="customizationp" onclick="CheckOrUncheckInputcustomization(this);">		
				<?php  echo CHtml::CheckBox('Eas[customization]',($model->customization == '1')?'checked' : '', array("style"=>"   margin-top: -5px;") ); ?> 
				</div>            
			<?php echo  CCustomHtml::error($model,'customization'); ?>
		</div>
	<div class="row startDateRow hidden "   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'start_date'); ?>
		<div class="dateSearch inputBg_txt width200">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model, 'attribute'=>'start_date','cssFile' => false,   'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111'),  ));	?>
		<span class="calendar calfrom"></span>
		</div>
		<?php echo $form->error($model,'start_date'); ?>
	</div>
	<div class="row endDateRow hidden"   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'end_date'); ?>
		<div class="dataRow marginr0 width200"><?php echo $form->textField($model, 'end_date'); ?><span class="calendar calfrom"></span></div>
		<?php echo $form->error($model,'end_date'); ?>
	</div>
	<div class="row trainingsRow hidden "   style="height:70px !important;" >
		<?php
		$trainingEamodel = new TrainingEas(); echo $form->labelEx($trainingEamodel,'id_training'); ?>
		<div class="selectBg_create width208">
		<?php echo $form->dropDownList($trainingEamodel,'id_training',array(),array('prompt' => Yii::t('translations', 'Choose Training'),'onclick'=>'changeEaDesc(this)')); ?></div>
		</div>
		<div>
		<?php echo $form->error($trainingEamodel,'id_training'); ?>
	</div>	
	<div class="row marginb20  hidden" id="support_percent"   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'customization support % *'); ?>
		<div class="inputBg_create width200">
			<?php echo $form->textField($model, 'support_percent',array('autocomplete'=>'off')); ?>
		</div>
		<?php echo $form->error($model,'support_percent'); ?>
	</div>
	<div class="row marginb20  hidden" id="support_amt"   style="height:70px !important;" >
		<?php echo $form->labelEx($model,'Development Amt *'); ?>
		<div class="inputBg_create width200">
			<?php echo $form->textField($model, 'support_amt',array('autocomplete'=>'off')); ?>
		</div>
		<?php echo $form->error($model,'support_amt'); ?>
	</div>		
	<div class="row marginb20  descr row_textarea_ea" style="height:60px !important;" >
		<?php echo $form->labelEx($model, 'description'); ?>
		<div class="inputBg_create width200">
		<?php echo $form->textField($model, 'description',array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'description'); ?>
		<span class="margint10">*The content of this field will be printed on the customer invoice so please make sure it contains a clear description</span>		
	</div>	
	<div class="row hidden" id="TM" style="margin-left:-131px; margin-top:6px;height:70px !important;" >									
            	<div class="row <?php echo ($model->TM == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInput(this);">		
					<?php echo CHtml::activeLabelEx($model, 'T&M');?>	<?php  echo CHtml::CheckBox('Eas[TM]',($model->TM == '1')?'checked' : '' ); ?> 
				</div>            
			<?php echo  CCustomHtml::error($model,'TM'); ?>
		</div>
    <!--
        /*
         * Author: Mike
         * Date: 17.06.19
         * MDs display it on status report
         */
    -->
    <div class="row marginb20 <?php echo ($model->TM == '1')?'' : 'hidden' ?> row_textarea_ea" id="Mds" style="height:60px !important;" >
        <?php echo $form->labelEx($model, 'mds'); ?>
        <div class="inputBg_create width200">
            <?php echo $form->textField($model, 'mds',array('autocomplete'=>'off')); ?>
        </div>
        <?php echo $form->error($model,'mds'); ?>
    </div>

    <div class="horizontalLine" style="    margin-top: 55px;
"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>
	</div>
	<br clear="all" />
<?php $this->endWidget(); ?>
</div>
<br clear="all" />
<script type="text/javascript">
    /*
     * Author: Mike
     * Date: 17.06.19
     * MDs display it on status report
     */
    $(document).ready(function () {
        $('#Eas_TM').change(function () {
            if ($(this).attr("checked") === 'checked'){
                $('#Mds').removeClass('hidden')
            }else {
                $('#Mds').addClass('hidden');
            }
        })
    });
    
	$(function() {	changeCategory('#Eas_category'); validateCustomization(); }); 
	function changeEaDesc(id_training){
		var training = id_training.value;
			$.ajax({
		 		type: "GET",url: "<?php echo Yii::app()->createAbsoluteUrl('eas/getTrainingDesc');?>",
				data: {'id':training}, 	dataType: "json",
			  	success: function(data) {
				  	if (data) {	$('#Eas_description').attr('value', data);	}
		  		} });
	}
	function changeCategory(element) {
		$this =  $(element);
		switch ($this.val()) {
			case '24':
				getTrainings();	$("#TrainingEas_id_training").parents('.row').removeClass('hidden'); document.getElementById('Eas_description').readOnly  = true;
				$('#TM').addClass('hidden');	$('#Eas_template').parents('.row').addClass('hidden');
					$('#Eas_project_name').parents('.row').addClass('hidden');	$('#Eas_id_parent_project').parents('.row').addClass('hidden');
					  			$('#customization').addClass('hidden');	
				break;
			case '454':
				$('#Eas_template').parents('.row').addClass('hidden');
					  			$('#customization').addClass('hidden');	
			case '496':
				document.getElementById('Eas_description').readOnly  = false;	$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				$('#Eas_project_name').parents('.row').addClass('hidden');
				$('#Eas_template').parents('.row').addClass('hidden');	$('#Eas_id_parent_project').parents('.row').addClass('hidden');
				$('#Eas_start_date').parents('.row').addClass('hidden'); $('#Eas_end_date').parents('.row').addClass('hidden');
				$('#TM').addClass('hidden');	$('#Eas_start_date').val('');	$('#Eas_end_date').val('');
					  			$('#customization').addClass('hidden');	
				break;
			case '623':
				document.getElementById('Eas_description').readOnly  = false;	$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				$('#Eas_project_name').parents('.row').addClass('hidden');
				$('#Eas_template').parents('.row').addClass('hidden');	$('#Eas_id_parent_project').parents('.row').addClass('hidden');
				$('#Eas_start_date').parents('.row').addClass('hidden'); $('#Eas_end_date').parents('.row').addClass('hidden');
				$('#TM').addClass('hidden');	$('#Eas_start_date').val('');	$('#Eas_end_date').val('');
					  			$('#customization').addClass('hidden');	
				break;
			case '25':
				document.getElementById('Eas_description').readOnly  = false;	$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				$('#Eas_project_name').parents('.row').addClass('hidden');
				$('#Eas_template').parents('.row').addClass('hidden');	$('#Eas_id_parent_project').parents('.row').addClass('hidden');
				$('#Eas_start_date').parents('.row').addClass('hidden'); $('#Eas_end_date').parents('.row').addClass('hidden');
				$('#TM').addClass('hidden');	$('#Eas_start_date').val('');	$('#Eas_end_date').val('');
					  			$('#customization').addClass('hidden');	
				break;
			case '26':
				document.getElementById('Eas_description').readOnly  = false;	$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				$('#Eas_id_parent_project').parents('.row').addClass('hidden');	$('#Eas_project_name').parents('.row').removeClass('hidden');
				$('#TM').removeClass('hidden');		$('#Eas_start_date').parents('.row').addClass('hidden');	$('#Eas_end_date').parents('.row').addClass('hidden');	
				$('#Eas_start_date').val('');	$('#Eas_end_date').val('');	
				$('#Eas_template').parents('.row').addClass('hidden');
					  			$('#customization').addClass('hidden');	
				break;
			case '27':
				validateLocation("#Eas_id_customer");
				document.getElementById('Eas_description').readOnly  = false;	$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				$('#Eas_id_parent_project').parents('.row').addClass('hidden');	$('#Eas_project_name').parents('.row').removeClass('hidden');
				$('#Eas_template').parents('.row').removeClass('hidden');
				$('#TM').removeClass('hidden');		$('#Eas_start_date').parents('.row').addClass('hidden');	$('#Eas_end_date').parents('.row').addClass('hidden');	
				$('#Eas_start_date').val('');	$('#Eas_end_date').val('');	
					  			
				break;
			case '28':
				validateLocation("#Eas_id_customer");
				document.getElementById('Eas_description').readOnly  = false;	
				$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				$('#Eas_id_parent_project').parents('.row').removeClass('hidden');
				$('#Eas_project_name').parents('.row').removeClass('hidden');
				$('#TM').addClass('hidden');	
				$('#Eas_start_date').parents('.row').addClass('hidden');
				$('#Eas_end_date').parents('.row').addClass('hidden');
				$('#Eas_start_date').val('');
				$('#Eas_end_date').val('');
				$('#Eas_template').parents('.row').addClass('hidden');
				getCustomerProjects("#Eas_id_customer");
				break;
			default:
				$("#TrainingEas_id_training").parents('.row').addClass('hidden');
				document.getElementById('Eas_description').readOnly  = false;
				$('#Eas_template').parents('.row').addClass('hidden');
					  			$('#customization').addClass('hidden');	
				break;
		}
		$('#Eas_id_parent_project').val('');	$('#Eas_project_name').val('');		
	}
	function getTrainings(){
			$.ajax({
		 		type: "GET",  	url: "<?php echo Yii::app()->createAbsoluteUrl('eas/GetTrainings');?>",	dataType: "json",
			  	success: function(data) {
				  	if (data) {
				  		var arr = [];
				  		for (var key in data) {
				  		    if (data.hasOwnProperty(key)) {   arr.push({'id': key, 'label': data[key]});   }
				  		}
				  		var selectOptions = '<option value=""></option>';	var index = 1;
				  		$.each(arr,function(index,val){	selectOptions += '<option value="' + val.id+'">'+val.label+'</option>'; });
					    $('#TrainingEas_id_training').html(selectOptions);
					}
		  		} });
	} 		
	function validateLocation(element) {
		$ea =  $("#Eas_category");
		if($ea.val() == 28 || $ea.val() == 27 )
		{
			$('#customization').removeClass('hidden');
			$this = $(element);	var val = $this.val();
			if (val) {
				$.ajax({
			 		type: "GET", 	url: '<?php echo Yii::app()->createAbsoluteUrl('Eas/GetRegion');?>',
					data: { id: val},	dataType: "json",
				  	success: function(data) {
					  	if (data) { 
					  		if(data.region!=59 || data.custsupport == 'checked' )
					  		{
								$('#Eas_customization').attr('checked', true);
								$('#support_percent').removeClass('hidden');	 $('#support_amt').removeClass('hidden');
								 document.getElementById("Eas_support_percent").value='20';
								if(data.custsupport == 'checked')
								{
									$("#customizationp").addClass("disabledcheck");
								}else{
									$("#customizationp").removeClass("disabledcheck");
								}
					  		}else{
					  			
								$('#Eas_customization').attr('checked', false);
								$("#customizationp").removeClass("disabledcheck");
								  $('#support_percent').addClass('hidden');  $('#support_amt').addClass('hidden');
								   document.getElementById("Eas_support_percent").value='';
					  		}
						}
			  		} });
			} 
		}
	}
	function getCustomerProjects(element) {
		$this = $(element);	var val = $this.val();
		if (val) {
			$.ajax({
		 		type: "GET", 	url: '<?php echo Yii::app()->createAbsoluteUrl('projects/GetParentProjectsByClient');?>',
				data: { id: val},	dataType: "json",
			  	success: function(data) {
				  	if (data) {
					var arr = [];
				  		for (var key in data) {    if (data.hasOwnProperty(key)) {  arr.push({'id': key, 'label': data[key]}); } }
				  		 var sorted = arr.sort(function (a, b) {
			    				if (a.label > b.label) { return 1; }
			    				if (a.label < b.label) { return -1;	}
			    				return 0; });
				  		var selectOptions = '<option value=""></option>'; var index = 1;
				  		$.each(sorted,function(index,val){	selectOptions += '<option value="' + val.id+'">'+val.label+'</option>'; });
					    $('#Eas_id_parent_project').html(selectOptions);
					}
		  		} });
		} else { $('#Eas_id_parent_project').html('<option value=""></option>'); }
	}	
	function CheckOrUncheckInput(obj){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');		
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');		input.prop('checked', false);
		}else{	checkBoxDiv.addClass('checked');	input.prop('checked', true); }
	}

	function CheckOrUncheckInputcustomization(obj){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');		
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');		input.prop('checked', false);  $('#support_percent').addClass('hidden'); document.getElementById("Eas_support_percent").value='';
  $('#support_amt').addClass('hidden');	
		}else{	checkBoxDiv.addClass('checked');	input.prop('checked', true); $('#support_percent').removeClass('hidden');  document.getElementById("Eas_support_percent").value='20';	 $('#support_amt').removeClass('hidden');	 }
	}
	function validateCustomization()
	{
		if($('#Eas_customization:checkbox:checked').length > 0)
		{
 $('#support_percent').removeClass('hidden');
 $('#support_amt').removeClass('hidden');
   document.getElementById("Eas_support_percent").value='20';
		}else{

			$('#support_percent').addClass('hidden');	
			$('#support_amt').addClass('hidden');
			  document.getElementById("Eas_support_percent").value='';
		}
	}
</script>