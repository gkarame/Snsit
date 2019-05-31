<?php
/* @var $this ExpensesController */
/* @var $model Expenses */
?>
<div  id="support" class="tache-scenario new">
	 
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'scenario-form',
		'enableAjaxValidation'=>false,
	)); ?>
		
		<div class="item inline-block notes normal" style="left:11px" >
			<?php echo $form->labelEx($model,'scenario'); ?>
			<div class="dataRow"  style="width:850px;"><?php echo $form->textField($model,'scenario'); ?></div>
			<?php echo $form->error($model,'scenario'); ?>
		</div>

		 

		<div class="item inline-block one normal left11" style="top:70px;margin-left:-448px;">
			<?php echo $form->labelEx($model,'status'); ?>
			<div class="selectRow" style="width:200px;">
			<?php echo $form->dropDownList($model, 'status', array('0'=>'Pending','1'=>'Completed'), array('class'=>'input_text_value')); ?> 
			</div>
			<?php echo $form->error($model,'status'); ?>
		</div>

		 
 
 

		
<div style="padding-top:116px;margin-right:-155px;color:#333;overflow: hidden;width:25%;float:right;"   ><u style="cursor:pointer;" onclick="$(this).parents('.tache-scenario.new').siblings('.tache-scenario').show();$(this).parents('.tache-scenario.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid-scenarios');<?php }?>"><b>CANCEL</b></u></div>
	
		<?php if ($model->isNewRecord) { ?>

			<div style="padding-top:115px;overflow: hidden;float:right;width:4%;"  ><u style="color:#8d0719;cursor:pointer;" onclick="createScenarioItem(this, <?php echo "1"; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/createScenarioItem/?id_project='.$id_project.'');?>');return false;"><b>SAVE</b></u></div>
		<?php } else { ?>
			<div  style="padding-top:115px;overflow: hidden;float:right;width:4%;"  ><u style="color:#8d0719;cursor:pointer;" onclick="updateScenarioItem(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/ManageScenarioItem');?>');return false;"><b>SAVE</b></u></div>
		<?php } ?>
		
	<?php $this->endWidget(); ?>
</div><!-- form -->
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var rateUrl = '<?php echo Yii::app()->createAbsoluteUrl('expenses/GetUSDRate');?>';
</script>
