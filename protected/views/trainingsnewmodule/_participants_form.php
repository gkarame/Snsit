<div class="tache new">	<div class="bg"></div>	<fieldset class="new_participant">	<?php $id = $model->isNewRecord ? 'new' : $model->id;?>	<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]firstname"); ?></div>
			<div class="input_text">		<?php echo CHtml::activeTextField($model,"[$id]firstname", array('class'=>'input_text_value')); ?>
			</div>		<?php echo CCustomHtml::error($model, "[$id]firstname",array('id'=>"TrainingParticipants_".$id."_firstname_em_")); ?>
		</div>	<div class="textBox one inline-block first"><div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]lastname"); ?></div>
			<div class="input_text">	<?php echo CHtml::activeTextField($model,"[$id]lastname", array('class'=>'input_text_value')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]lastname",array('id'=>"TrainingParticipants_".$id."_lastname_em_")); ?>
		</div>	<div class="textBox one inline-block first">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]title"); ?></div>
			<div class="input_text">	<?php echo CHtml::activeTextField($model,"[$id]title", array('class'=>'input_text_value')); ?>
			</div>		<?php echo CCustomHtml::error($model, "[$id]title", array('id'=>"TrainingParticipants_".$id."_title_em_")); ?>
		</div>	<div class="textBox one inline-block first">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]email"); ?></div>
			<div class="input_text">		<?php echo CHtml::activeTextField($model,"[$id]email", array('class'=>'input_text_value')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]email",array('id'=>"TrainingParticipants_".$id."_email_em_")); ?>
		</div>	<div class="textBox one inline-block first">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]customer"); ?></div>
			<div class="input_text">	<?php echo CHtml::activeDropDownList($model,"[$id]customer",Customers::getAllCustomersSelect(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;')); ?>
			</div>	<?php echo CCustomHtml::error($model, "[$id]customer",array('id'=>"TrainingParticipants_".$id."_customer_em_")); ?>
		</div>	<div style="right:141px;" class="save" onclick="saveParticipant(this,'<?php echo $id?>');"><u><b>SAVE</b></u></div>
		<div style="color:#333;right:77px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('connections-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset></div>