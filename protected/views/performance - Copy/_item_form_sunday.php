<?php
/* @var $this ExpensesController */
/* @var $model Expenses */
?>
<div  id="support" class="tache-sunday new">
	<div  id="support" class="bg"></div>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'sunday-form',
		'enableAjaxValidation'=>false,
	)); ?>
	    
			<div class="item inline-block time normal" style="cursor:pointer; margin-left:10px;">
					<?php echo $form->labelEx($model,'date'); ?>
					<div class="dataRow">
						<?php 
						$this->widget('zii.widgets.jui.CJuiDatePicker',array(
							'model'=> $model,
							'attribute' => "from_date", 
							'cssFile' => false,
							'options'=>array(
								'dateFormat'=>'yy/mm/dd',
								'showAnim' => 'fadeIn'
							),
							'htmlOptions' => array(
								'class' => 'datefield'
							),
							
						));
						?>
						<span class="calendar calfrom"></span>
					</div>
					<?php echo $form->error($model,'from_date'); ?>
				</div>
		
			
		<div class="item inline-block one normal " style="margin-left:5px;">
			<?php echo $form->labelEx($model,'primary_contact'); ?>
			<div class="selectRow">
			<?php echo $form->dropDownList($model, 'primary_contact', FullSupport::getAllActiveUsers(),array('prompt'=>'')); ?> 
			</div>
			<?php echo $form->error($model,'primary_contact'); ?>
		</div>
		
		
		<div class="item inline-block one normal "style="margin-left:5px;">
			<?php echo $form->labelEx($model,'secondary_contact'); ?>
			<div class="selectRow">
			<?php echo $form->dropDownList($model, 'secondary_contact', FullSupport::getAllActiveUsers(),array('prompt'=>'')); ?> 
			</div>
			<?php echo $form->error($model,'secondary_contact'); ?>
		</div>
	
	
		<?php if ($model->isNewRecord) { ?>
			<div style="right:78px;" class="save" onclick="createSundayItem(this, <?php echo "1"; ?>, '<?php echo Yii::app()->createAbsoluteUrl('fullsupport/CreateSundayItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } else { ?>
			<div style="right:78px;" class="save" onclick="updateSundayItem(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('fullsupport/manageSundayItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } ?>
		<div style="color:#333;" class="save" onclick="$(this).parents('.tache-sunday.new').siblings('.tache-sunday').show();$(this).parents('.tache-sunday.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid-sunday');<?php }?>"><u><b>CANCEL</b></u></div>

	<?php $this->endWidget(); ?>
</div><!-- form -->
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var rateUrl = '<?php echo Yii::app()->createAbsoluteUrl('expenses/GetUSDRate');?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>