<div class="create"><?php $form=$this->beginWidget('CActiveForm', array('id'=>'quality-header-form','enableAjaxValidation'=>false,)); ?>
	<div class="row marginb20 width165" >	<?php echo $form->labelEx($model, 'type'); ?>
		<div class="selectBg_create width165">	<?php echo $form->dropDownList($model, 'type', Quality::getAlltype(), array('prompt' => Yii::t('translations', 'Choose Type'), 'class'=> 'width165' )); ?>	</div>		
		<?php echo $form->error($model,'type'); ?></div>

	<div class="row marginb20 width179">	<?php echo $form->labelEx($model,'id_project'); ?>		
		<div class="inputBg_create width179"><?php	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_project',
						'source'=>Projects::getActiveProjectsAutocomplete(),'options'=>array('minLength'=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ refreshProjecttasks(ui.item.id); }"),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "",),));	?>
		</div> 	<?php echo $form->error($model,'id_project'); ?></div>
	<div class="row marginb20 width179 "><?php echo $form->labelEx($model, 'id_task'); ?>	<div class="selectBg_create width179">
		<?php echo $form->dropDownList($model, 'id_task', Quality::GetAllProjectsDevTasks(), array('prompt' => Yii::t('translations', 'Choose Task'), 'disabled' => true)); ?>
			</div>		<?php echo $form->error($model,'id_task'); ?></div>
	<div class="row marginb20 width179 ">	<?php echo $form->labelEx($model, 'id_user'); ?>
		<div class="selectBg_create width179"><?php echo $form->dropDownList($model, 'id_user', Users::getAllSelect(), array('prompt' => Yii::t('translations', ''),'class'=>'')); ?></div>		
		<?php echo $form->error($model,'id_user'); ?></div>
<div class="row marginb20 width165 "   >
		<?php echo $form->labelEx($model, 'id_resc', array('style'=>'')); ?>	<div class="selectBg_create width165" >
		<?php echo $form->dropDownList($model, 'id_resc', Users::getAllSelect(), array('prompt' => Yii::t('translations', 'Choose QA'),'class'=>'width165' )); ?>
			</div>		<?php echo $form->error($model,'id_resc'); ?></div>

	<div class="row item inline-block normal width179 " >	<?php echo $form->label($model,'fbr_delivery'); ?><div class="dataRow " style="width: 179px;    height: 23px;" >
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'fbr_delivery','cssFile' => false,
		        'options'=>array('minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'	),'htmlOptions'=>array('class'=>'width150','autocomplete'=>'off','id'=>'end_date'),   ));?>
			<span class="calendar calfrom"></span><?php echo  CCustomHtml::error($model,'fbr_delivery'); ?>	</div></div> 

		<div class="row item inline-block normal width179  ">
		<?php echo $form->label($model,'expected_delivery_date'); ?><div class="dataRow " style="width: 169px;    height: 23px;" >	
		<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'expected_delivery_date','cssFile' => false,'options'=>array(
		    		'minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width150','autocomplete'=>'off','id'=>'start_date'),)); ?>
			<span class="calendar calfrom"></span>	<?php echo CCustomHtml::error($model,'expected_delivery_date');  ?>	</div>	</div>		

<div class="row marginb20 width179" >	<?php echo $form->labelEx($model, 'complexity'); ?>
		<div class="selectBg_create width179">	<?php echo $form->dropDownList($model, 'complexity', Quality::getAllComplx(), array( 'class'=> '' )); ?>	</div>		
		<?php echo $form->error($model,'complexity'); ?></div>		<div class="horizontalLine"></div>
	<div class="row buttons"><?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?></div><br clear="all" /><?php $this->endWidget(); ?></div><br clear="all" />
<script type="text/javascript">
function refreshProjecttasks(id) {	
	var id_ts = "<?php echo $model->id_task;?>";
	if (!id) {	id = $('#Quality_id_project').val(); }	console.log(id);	
	if (id){
		$('#Quality_id_task').removeAttr('disabled');
		$.ajax({ type: "GET",data: {id : id},url: '<?php echo Yii::app()->createAbsoluteUrl('quality/GetProjectsDevTasks');?>',dataType: "json",
 			success: function(data) {
			  	if (data) {
			  		var selectOptions = '<option value=""></option>';	var index = 1;
			  		$.each(data,function(id,name){
			  			var selected = (id == id_ts) ? 'selected="selected"' : ''; 
			  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
			  		});
				    $('#Quality_id_task').html(selectOptions);
			  	}	}	});
	} else {
		$('#Quality_id_task').html(''); 	$('#Quality_id_task').attr('disabled', 'disabled');	} }	
</script>