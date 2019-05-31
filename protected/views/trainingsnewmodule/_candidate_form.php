<div class="tache2 new" >
	<div class="" ></div>
	<fieldset class="new_candidate">
	<?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]id_customer"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeDropDownlist($model,"[$id]id_customer",Customers::getAllCustomersSelect(),array('prompt' => Yii::t('translations', 'Choose Customer'),'style' =>'border:none;margin-top:3px;margin-left:2px;width:240px;' ,'onchange'=>'changeCandidateDesc(this)'));?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]id_customer",array('id'=>"TrainingFreeCandidates".$id."_id_customer_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]contact_name"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]contact_name", array('class'=>'input_text_value','style' => 'border:none;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]contact_name",array('id'=>"TrainingFreeCandidates".$id."_contact_name_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]contact_email"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]contact_email", array('class'=>'input_text_value','style' => 'border:none;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]contact_email",array('id'=>"TrainingFreeCandidates".$id."_contact_email_em_")); ?>
		</div>
		<div style="right:141px;" class="save" onclick="saveCandidate(this,'<?php echo $id?>');"><u><b>SAVE</b></u></div>
		<div style="color:#333;right:77px;" class="save" onclick="$(this).parents('.tache2.new').siblings('.new_can').show();$(this).parents('.tache2.new').remove();$.fn.yiiGridView.update('candidates-grid');"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>
<script>
function changeCandidateDesc(id_customer){
		var customer = id_customer.value;
			$.ajax({type: "GET",  	url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/getCandidateDesc');?>",	data: {'id':customer},  	dataType: "json",
			  	success: function(data) {
				  	if (data) {
				  		$('#TrainingFreeCandidates_<?php echo $id ?>_contact_name').attr('value', data.name);
				  		$('#TrainingFreeCandidates_<?php echo $id ?>_contact_email').attr('value', data.email);	  	}	  		}			});	}
</script>