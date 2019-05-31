<?php Yii::app()->clientScript->scriptMap['jquery.js'] = false;    Yii::app()->clientScript->scriptMap['jquery.min.js'] = false; Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;    Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false; ?>
<div class="generalTasksTickets" style="margin-top:<?php echo ($model->isNewRecord ) ? ' 20px' : '';?>"><div class="tache new " style="width:<?php echo ($model->isNewRecord ) ? ' 98%' : '';?>" >
<div class="bg" style="top:0px;"></div>	<fieldset class="items_fieldset"><?php $id = $model->isNewRecord ? 'new' : $model->id;?>
<div class="textBox one inline-block ">	<div class="input_text_desc"><label class="required"><?php echo CHtml::activelabelEx($model, "Name"); ?><span class="required">*</span></label></div>
<div class="input_text"><?php echo CHtml::activeTextField($model, "description", array('class'=> 'input_text_value')); ?></div>
<?php echo CCustomHtml::error($model, "description", array('id'=>"description")); ?>	</div>
<?php if($model->isNewRecord ){	?>	<div class="textBox inline-block itms amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "phase_number"); ?></div>
<div class="input_text"><?php echo CHtml::activeTextField($model, "phase_number", array('class'=> 'input_text_value')); ?>
</div><?php echo CCustomHtml::error($model, "phase_number", array('id'=>"phase_number")); ?></div><?php }else{ ?>
<div class="textBox inline-block itms amt">		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "phase_number"); ?></div>
<div class="input_text">	<?php echo CHtml::activeTextField($model, "phase_number", array('class'=> 'input_text_value','readonly' => true)); ?>
</div><?php echo CCustomHtml::error($model, "phase_number", array('id'=>"phase_number")); ?>	</div>	<?php }	?>
<div class="textBox inline-block itms amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "man_days_budgeted"); ?></div>
<div class="input_text"><?php echo CHtml::activeTextField($model, "man_days_budgeted", array('class'=> 'input_text_value')); ?></div>
<?php echo CCustomHtml::error($model, "man_days_budgeted", array('id'=>"man_days_budgeted")); ?></div>				
<?php if($id == "new") { ?> <div style="right:75px; top:46px" class="save" onclick="createPhase(this,<?php echo $id_project;?>);return false;"><u><b>SAVE</b></u></div>
<?php } else { ?><div style="right:75px; top:46px" class="save" onclick="updatePhase(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
<?php }?> <div style="color:#333;top:46px" class="save" onclick="cancel(this,'<?php echo $id;?>');"><u><b>CANCEL</b></u></div>	</fieldset>	</div></div>
<script>
function cancel(element,id) {
	$('.tache.new ').hide();
	if (id != 'new') {	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/updatePhase');?>";	url += '/'+parseInt(id);
		$.ajax({ type: "POST",data:'cancel=1&id_project='+<?php echo $model->id_project;?> ,url: url,dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved'){ $(element).parents('.thead').html( data.form ); } } } }); } }
function updatePhase(element, id) {
	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/updatePhase');?>";
	if (id != 'new') {	url += '/'+parseInt(id); }
	$.ajax({ type: "POST", data: $(element).parents('.items_fieldset').serialize()+'& id_project='+<?php echo $model->id_project?> ,					
	  	url: url, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {
		  			$(element).parents('.thead').html( data.form );		$(element).parents('.foot').html( data.form2 );
			  	} else if (data.status =='failure'){
			  		var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					        }
						}
		  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
			  	}else{
			  		var error = data.error;
			  		$.each( error, function( key, value ){ $("#" + key).text(value); });
			  	} } }	}); }
function createPhase(element,id_project) {
	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/createPhase');?>";
	$.ajax({ type: "POST", data: $(element).parents('.items_fieldset').serialize()+'& id_project='+id_project,	url: url, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {	$(element).parents('.tache.new').remove(); $('.phases.newP').append(data.form2 );
			  	} else {
			  		var error = data.error;
			  		$.each( error, function( key, value ) { $("#" + key).text(value); });
				} } }	}); }
</script>			