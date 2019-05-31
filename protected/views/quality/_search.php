<div class="wide search" id="search-quality" style="overflow: inherit;">
	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',	)); ?>
	<div class="row  ">	<div class="selectBg_search"><?php echo $form->labelEx($model,'Type'); ?>	<span class="spliter"></span>
<div class="select_container width111"><?php echo $form->dropDownList($model, 'type', Quality::getAlltype(), array('prompt' => '')); ?></div></div>
<?php echo $form->error($model,'status'); ?></div>

	<div class="row width_project_name width300"><div class="inputBg_txt"><?php echo $form->label($model,'Project'); ?><span class="spliter"></span>
<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_project','source'=>Projects::getActiveProjectsAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold','select'	=>"js: function(event, ui){ refreshProjecttasks(ui.item.id); }"),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width171",),));?></div>	</div>
<div class="row width300"><div class="selectBg_search"><?php echo $form->labelEx($model,'Task/FBR'); ?><span class="spliter"></span>
<div class="select_container width191" ><?php echo $form->dropDownList($model, 'id_task', Quality::GetAllProjectsDevTasks(), array('prompt' => Yii::t('translations', 'Choose Task'), 'disabled' => true)); ?>
</div></div></div>	<div class="row margint10  ">	<div class="selectBg_search"><?php echo $form->labelEx($model,'Status'); ?>	<span class="spliter"></span>
<div class="select_container width111"><?php echo $form->dropDownList($model, 'status', Quality::getAllStatus(), array('prompt' => '')); ?></div></div>
<?php echo $form->error($model,'status'); ?></div>
<div class="row margint10 width300"><div class="selectBg_search"><?php echo $form->label($model,'resource',array('style'=>'width:100px')); ?>
<span class="spliter"></span><div class="select_container width171"><?php echo $form->dropDownList($model, 'id_user', Users::getAllSelect(), array('prompt' => '')); ?>
			</div></div><?php echo $form->error($model,'id_user'); ?></div> <div class="row margint10 width300" >
		<div class="selectBg_search"><?php echo $form->label($model,'qa res'); ?><span class="spliter"></span>
			<div class="select_container width191"><?php echo $form->dropDownList($model, 'id_resc', Users::getAllSelect(), array('prompt' => '')); ?>
		</div></div><?php echo $form->error($model,'id_resc'); ?></div>
	<div class="row margint10">	<div class="selectBg_search">	<?php echo $form->labelEx($model,'complexity'); ?>
<span class="spliter"></span>	<div class="select_container width111">	<?php echo $form->dropDownList($model, 'complexity', Quality::getAllComplx(), array('prompt' => Yii::t('translations', ''))); ?>
</div></div><?php echo $form->error($model,'complexity'); ?></div><div class="row margint10 width300">
		<div class="selectBg_search"><?php echo $form->labelEx($model,'Result',array('style'=>'width:100px')); ?>
			<span class="spliter"></span><div class="select_container width171">
				<?php echo $form->dropDownList($model, 'score', Quality::getAllScores(), array('prompt' => Yii::t('translations', ''))); ?>
			</div></div><?php echo $form->error($model,'score'); ?></div>
	<div class="btn"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?> 		
 		<div class="wrapper_action" id="action_tabs_right">	<div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel" style="z-index:1000 !important;padding-top:-15px !important;">
			    <div class="headli"></div><div class="contentli"> <?php if(GroupPermissions::checkPermissions('general-quality','write')){ ?>
						<div class="cover"><div class="li noborder"><?php echo CHtml::link(Yii::t('translation', 'Create QA'), array('create'), array('class'=>'add-customer')); ?> </div></div>
						<div class="cover"><div class="li noborder " onclick="sendToQA();">Send To QA</div></div>
						<div class="cover">	<div class="li noborder " onclick="deleteQA();">Delete QA Task(s)</div></div>
						<div class="cover">	<div class="li noborder " onclick="showPop();">Update Comment</div></div>
						<div class="cover"><div class="li noborder" onclick="getExcel();">Export to Excel</div></div>						
						<?php }?></div><div class="ftrli"></div> </div> </div></div><div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?></div>
<script>
function refreshProjecttasks(id) {	
	var id_ts = "<?php echo $model->id_task;?>";
	if (!id) {	id = $('#Quality_id_project').val();	}
	console.log(id);	
	if (id){ $('#Quality_id_task').removeAttr('disabled');
		$.ajax({ type: "GET",data: {id : id},url: '<?php echo Yii::app()->createAbsoluteUrl('quality/GetProjectsDevTasks');?>',dataType: "json",
 			success: function(data) {
			  	if (data) {	var selectOptions = '<option value=""></option>';	var index = 1;
			  		$.each(data,function(id,name){
			  			var selected = (id == id_ts) ? 'selected="selected"' : ''; selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
			  		}); 
					$('#Quality_id_task').html(selectOptions); } } });
	} else {	$('#Quality_id_task').html('');	$('#Quality_id_task').attr('disabled', 'disabled');	} }
</script>