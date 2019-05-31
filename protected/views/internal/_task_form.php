<div class="tache new "><div class="bg" style="      height: 160px;  background-size: 916px 160px;top:0px;"></div>	<fieldset class="items_fieldset" style="width: 890px !important;"><?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox one inline-block ">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "description"); ?></div>
			<div class="input_text"><?php echo CHtml::activeTextField($model, "description", array('class'=> 'input_text_value')); ?>	</div>
			<?php echo CCustomHtml::error($model, "description", array('id'=>"description"));  ?></div>
			
			<div class="textBox inline-block marginl18 width185">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "priority"); ?></div>
			<div class="input_text"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "priority",  InternalTasks::getPriorityList(), array('prompt' => "", 'class'=>'input_text_value width179')); ?>
				</div> 	</div>	<?php echo CCustomHtml::error($model, "priority", array('id'=>"priority")); ?>	</div>		
		

			<div class="textBox one inline-block marginl18 width185">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "estimated_effort"); ?></div>
			<div class="input_text width185"><?php echo CHtml::activeTextField($model, "estimated_effort", array('class'=> 'input_text_value')); ?>	</div>
			<?php echo CCustomHtml::error($model, "estimated_effort", array('id'=>"estimated_effort"));  ?></div>

			<div class="textBox two inline-block marginl18 width180">	
			<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "eta"); ?></div>
			<div class="input_text"><div class="">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "eta", 
							'cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield'),)); ?>
						<span class="calendar calfrom"></span>

				</div> 	</div>	<?php echo CCustomHtml::error($model, "eta", array('id'=>"eta")); ?>	</div>	


		<div class="textBox inline-block one">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "status"); ?></div>
			<div class="input_text"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status",  InternalTasks::getStatusList(), array('prompt' => "", 'class'=>'input_text_value')); ?>
				</div> 	</div>	<?php echo CCustomHtml::error($model, "status", array('id'=>"status")); ?>	</div>	

			<div class="textBox one inline-block marginl18 width390">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "notes"); ?></div>
			<div class="input_text width390" ><?php echo CHtml::activeTextField($model, "notes", array('class'=> 'input_text_value')); ?>	</div>
			<?php echo CCustomHtml::error($model, "notes", array('id'=>"notes"));  ?></div>

		<?php if($id == "new"){?>
			<div style="right:75px; top:116px" class="save" onclick="createTask(this,<?php echo $id_internal?>);return false;"><u><b>SAVE</b></u></div>
		<?php }else{?>
			<div style="right:75px; top:116px" class="save" onclick="updateTask(this, '<?php echo $id;?>',<?php echo 1;?>);return false;"><u><b>SAVE</b></u></div>
		<?php }?>
		<div style="color:#333;top:116px" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('phase-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset></div>
<script>
function updateTask(element, id) {
	var url = "<?php echo Yii::app()->createAbsoluteUrl('internal/updateTasks');?>";
	if (id != 'new') {	url += '/'+parseInt(id); }
	$.ajax({type: "POST",	data: $(element).parents('.items_fieldset').serialize() ,  	url: url, 	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {
		  			$(element).parents('.tache.new').remove(); 		$.fn.yiiGridView.update('phase-grid');
		  				$('#man_day_rate').html(data.totalDays);	
		  			}
		  			else {	var error = data.error;  		$.each( error, function( key, value ) {		$("#" + key).text(value); 		});		}
		  			 }	} }); }
function createTask(element,id_internal) {
	$(".errorMessage").text("");	var url = "<?php echo Yii::app()->createAbsoluteUrl('internal/createTasks');?>";
	$.ajax({type: "POST",		data: $(element).parents('.items_fieldset').serialize()+'& id_phase='+1+'& id_internal='+id_internal,  	url: url, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {
				  	$(element).parents('.tache.new').remove();  	$('.new_item').show();	$.fn.yiiGridView.update('phase-grid');
			  					  		$('#man_day_rate').html(data.totalDays);	
			  	} else {	var error = data.error;  		$.each( error, function( key, value ) {		$("#" + key).text(value); 		});		}		  	}  		}	}); }
</script>			