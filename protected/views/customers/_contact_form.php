<div class="tache new">
	<div class="bg"></div>
	<fieldset class="new_contact">
		<?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]name"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]name", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]name", array('id'=>"CustomersContacts_".$id."_name_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]email"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]email", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]email", array('id'=>"CustomersContacts_".$id."_email_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]job_title"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]job_title", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]job_title", array('id'=>"CustomersContacts_".$id."_job_title_em_")); ?>
		</div>
		<div class="textBox one inline-block second">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]mobile_number"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model, "[$id]mobile_number", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]mobile_number", array('id'=>"CustomersContacts_".$id."_mobile_number_em_")); ?>
		</div>
		<div class="textBox one inline-block second">
			<?php $support_weekend=Customers::getSupportPlan($model->id_customer);	?>
			<div id="sd_checkbox">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]sd_access");?></div>
			<div class="">
						 <?php echo CHtml::CheckBox('CustomersContacts['.$id.'][access]',($model->access == "Yes")?true:false , array (
                                        'style'=>'width:10px;margin-left: 17px;margin-top: 8px;','onChange'=>'CheckOrUncheckInput("'.$id.'")'
                                        )); ?>
			</div>
			</div>
			<?php  echo CCustomHtml::error($model, "[$id]mobile_number", array('id'=>"CustomersContacts_".$id."_mobile_number_em_")); ?>
		</div>
		<div class="textBox one inline-block second to-hide width111 <?php echo ($model->access == "Yes")?"":"hidden"?>">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]username"); ?></div>
			<div class="input_text width111">
				<?php echo CHtml::activeTextField($model, "[$id]username", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]username", array('id'=>"CustomersContacts_".$id."_username_em_")); ?>
		</div>
		<div class="textBox one inline-block second to-hide width111 <?php echo ($model->access == "Yes")?"":"hidden"?>">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "[$id]password"); ?></div>
			<div class="input_text width111">
				<?php echo CHtml::activeTextField($model, "[$id]password", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]password", array('id'=>"CustomersContacts_".$id."_password_em_")); ?>
		</div>
		<?php if ($model->id) { ?>
			<input type="hidden" name="CustomersContacts[<?php echo $id;?>][id]" value="<?php echo $model->id;?>" />
		<?php } ?>
		<div style="right:141px;" class="save" onclick="<?php echo ($update) ? 'saveContact(this, \''.$id.'\');' : 'js:submitForm();return false;'?>"><u><b>SAVE</b></u></div>
		<div style="color:#333;right:77px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('contacts-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>
<script>
function CheckOrUncheckInput(id){	
	if (!$('input:checkbox').is(':checked')){
		$('.to-hide').addClass('hidden');
	}else{
		$('.to-hide').removeClass('hidden');
	}
	if(id == "new"){
		var name = $('#CustomersContacts_new_name').val();	var first = name.substring(0, 1);
		var last = name.substring(name.indexOf(" ") + 1, name.length);
		$('#CustomersContacts_new_username').val(first+last);
		$('#CustomersContacts_new_password').val(first+last);
	}else{
		if($('#CustomersContacts_'+id+'_name').is(':empty')){
			if($('#CustomersContacts_'+id+'_username').val() == '' && $('#CustomersContacts_'+id+'_password').val() == ''){	
				var name = $('#CustomersContacts_'+id+'_name').val();
				var first = name.substring(0, 1);	var last = name.substring(name.indexOf(" ") + 1, name.length);
				$('#CustomersContacts_'+id+'_username').val(first+last);
				$('#CustomersContacts_'+id+'_password').val(first+last);
			}
		}
	}
}
	$(document).ready(function(){
			var selected= $("#sw_dropdown option:selected").val()
		if(selected=="N/A"){	$("#sd_checkbox").hide(); }else{	$("#sd_checkbox").show();	}
	});
	$("#sw_dropdown").change(function(){
		var select= $("#sw_dropdown option:selected").val()
		if(select=="N/A"){	$("#sd_checkbox").hide();	}else{	$("#sd_checkbox").show(); }
	});
</script>
