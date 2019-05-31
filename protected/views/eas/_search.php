<div class="wide search" id="search-eas">
	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',)); ?>	
		<div class="row ea_number width105" >
			<div class="inputBg_txt" >
				<?php echo $form->label($model,'ea_number'); ?>
				<span class="spliter"></span>
				<?php echo $form->textField($model,'ea_number',array('class' => 'width46')); ?>
			</div>
		</div>		
		<div class="row customer width280">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'Customer'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,	'attribute' => 'customer_name',		
						'source'=>Eas::getCustomersAutocomplete(),
						'options'=>array('minLength'	=>'0',	'showAnim'	=>'fold',
							'select'	=>"js: function(event, ui){ 
							$('#Eas_id_customer').val(ui.item.id);	refreshProjectListsEas(ui.item.id); }"
						),
						'htmlOptions'	=>array(
							'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
							'onblur' => 'blurAutocomplete(event, this, "#Eas_id_customer", refreshProjectListsEas);',
							'class'	  => "width171",
						),
				));	?>
			</div>
			<?php echo $form->hiddenField($model, 'id_customer'); ?>
		</div>
		<div class="row project_name width260">
			<div class="selectBg_search">
				<?php echo $form->label($model,'id_project', array('class'=>"width100")); ?>
				<span class="spliter"></span>
				<div class="select_container width131">
					<?php echo $form->dropDownList($model, 'id_project', Projects::getAllProjectsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
				</div>
			</div>
			<?php echo $form->error($model,'id_project'); ?>
		</div>
		<div class="row status width215">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'status'); ?>
				<span class="spliter"></span>
				<div class="select_container width136"><?php echo $form->dropDownList($model, 'status', Eas::getStatusList(), array('prompt'=>'')); ?></div>
			</div>
		</div>
		<div class="row TM width105 margint10" >
			<div class="inputBg_txt" >
				<?php echo $form->label($model,'T&M' ,array('style' => 'width:30px;')); ?>
				<span class="spliter"></span>
					<div class="select_container width46">
					<?php echo $form->dropDownList($model, 'TM',array('1'=>'Yes','0'=>'No'), array('prompt' => Yii::t('translations', ''))); ?>
				</div>
			</div>
		</div>		
		<div class="row author margint10 width280">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'author'); ?>
				<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'author',		
						'source'=>Eas::getUsersAutocomplete(),
						'options'=>array('minLength'=>'0',	'showAnim'=>'fold',	),
						'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	'class'	  => "width171",),	));	?>
			</div>
		</div>		
	<div class="row author margint10 width260" style="margin-left:10px;">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'category',array('class'=>'width100')); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,	'attribute' => 'category',	'source'=>Eas::getAllCategories(),
						'options'=>array('minLength'=>'0',	'showAnim'=>'fold',),
						'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width131",),)); ?>
			</div>
		</div>

		<div class="row author margint10 width215" style="margin-left:10px;">
			<div class="inputBg_txt" >
				<?php echo $form->label($model,'crmOpp', array('style'=>'width:50px;')); ?>
				<span class="spliter"></span>
				<div class="select_container width136"><?php echo $form->textField($model,'crmOpp',array('class' => '')); ?></div>
			</div>
		</div>		


		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); 
			if(GroupPermissions::checkPermissions('eas-list','write')){	echo CHtml::link(Yii::t('translation', 'New EA'), array('create'), array('class'=>'add-ea add-btn'));	} ?>
		</div>
		<div class="horizontalLine search-margin"></div>	
	<?php $this->endWidget(); ?>
</div>			
<script type="text/javascript">
		$(document).ready(function() {	refreshProjectListsEas();	});
		function refreshProjectListsEas(id){
			var id_project_ts = "<?php echo $model->id_project;?>";
			if (!id) {	id = $('#Eas_customer_name').val();	}
			if (id)	{				
				$('#Eas_id_project').removeAttr('disabled');
					$.ajax({
			 			type: "GET",	data: {id : id},					
			 			url: '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>', 
			 			dataType: "json",
			 			success: function(data) {
						  	if (data) {
							var arr = [];
						  		for (var key in data) {
						  		    if (data.hasOwnProperty(key)) {	arr.push({'id': key, 'label': data[key]}); }
						  		}
								var sorted = arr.sort(function (a, b) {
					    				if (a.label > b.label) {	return 1;	}
					    				if (a.label < b.label) {	return -1;	}
					    				return 0;
								});							  	
						  		var selectOptions = '<option value=""></option>';	var index = 1;
						  		$.each(sorted,function(index, val){
						  			var selected = (val.id == id_project_ts) ? 'selected="selected"' : ''; 
						  			selectOptions += '<option value="' + val.id+'"' + selected + '>'+val.label+'</option>';
						  		});
							    $('#Eas_id_project').html(selectOptions);
						  	}
				  		}
					});
			}else{	$('#Eas_id_project').html('');	$('#Eas_id_project').attr('disabled', 'disabled');	}
		}
</script>