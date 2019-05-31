<div  id="support" class="tache-risk new">	<div id="support" class="bg" ></div><?php $form=$this->beginWidget('CActiveForm', array('id'=>'risk-form','enableAjaxValidation'=>false,)); ?>
		<div class="item inline-block notes normal" style="left:11px" >	<?php echo $form->labelEx($model,'risk'); ?><div class="dataRow"  style="width:250px;"><?php echo $form->textField($model,'risk'); ?></div><?php echo $form->error($model,'risk'); ?>	</div>
		<div class="item inline-block one normal left11" style="margin-left:-170px;">
			<?php echo $form->labelEx($model,'priority'); ?><div class="selectRow" style="width:250px;"><?php echo $form->dropDownList($model, 'priority', Projects::getPriorityTypes(),array('prompt'=>'')); ?> 
			</div><?php echo $form->error($model,'priority'); ?></div>
<div class="item inline-block one normal left11" style="margin-left:-20px;">
			<?php echo $form->labelEx($model,'status'); ?>	<div class="selectRow" style="width:250px;">
			<?php echo $form->dropDownList($model, 'status', array('Pending'=>'Pending','Closed'=>'Closed'), array('class'=>'input_text_value')); ?> 
			</div>	<?php echo $form->error($model,'status'); ?>	</div>
		<div class="item inline-block notes normal" style="left:11px; top:10px" >
			<?php echo $form->labelEx($model,'planned_actions'); ?>	<div class="dataRow" style="width:250px;"><?php echo $form->textField($model,'planned_actions'); ?></div>
			<?php echo $form->error($model,'planned_actions'); ?></div>
		<div class="item inline-block one normal left11" style="top: 10px;margin-left:-170px;">
			<?php echo $form->labelEx($model,'responsibility'); ?><div class="selectRow" style="width:250px;" >
			<?php echo $form->dropDownList($model, 'responsibility', Projects::getResponsibilityTypes(),array('prompt'=>'')); ?> 
			</div>	<?php echo $form->error($model,'responsibility'); ?></div>
		<div class="item inline-block one normal left11" style="top: 10px;margin-left:-20px;">
			<?php echo $form->labelEx($model,'privacy'); ?>	<div class="selectRow" style="width:150px;">
			<?php echo $form->dropDownList($model, 'privacy', array('External'=>'External','Internal'=>'Internal'), array('class'=>'input_text_value')); ?> 
			</div>	<?php echo $form->error($model,'privacy'); ?></div>
		<?php if ($model->isNewRecord) { ?>
			<div style="right:78px;top:123px" class="save" onclick="createRiskItem(this, <?php echo "1"; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/createRiskItem/?id_project='.$id_project.'');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } else { ?>
			<div style="right:78px;top:123px" class="save" onclick="updateRiskItem(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/ManageRiskItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } ?>
		<div style="color:#333;top:123px" class="save" onclick="$(this).parents('.tache-risk.new').siblings('.tache-risk').show();$(this).parents('.tache-risk.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid-risks');<?php }?>"><u><b>CANCEL</b></u></div>
	<?php $this->endWidget(); ?></div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; var rateUrl = '<?php echo Yii::app()->createAbsoluteUrl('expenses/GetUSDRate');?>';
</script>
