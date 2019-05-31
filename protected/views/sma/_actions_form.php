<div   class="tache-smaction new">	<div   class="bg" ></div><?php $form=$this->beginWidget('CActiveForm', array('id'=>'smaction-form','enableAjaxValidation'=>false,)); ?>		
		<div class="item inline-block width100 left11 " ><?php echo $form->labelEx($model,'title'); ?>
			<div class="dataRow width100" ><?php echo $form->textField($model,'title'); ?></div><?php echo $form->error($model,'title'); ?>	</div>
		<div class="item inline-block width320" >	<?php echo $form->labelEx($model,'description'); ?>	<div class="dataRow width320" ><?php echo $form->textField($model,'description'); ?></div><?php echo $form->error($model,'description'); ?>
		</div> <?php  if(GroupPermissions::checkPermissions('general-sma','write')){ ?> <div class="item inline-block one normal width111">	<?php echo $form->labelEx($model,'status'); ?>
			<div class="selectRow width111"><?php echo $form->dropDownList($model, 'status', SmaActions::getStatusList(),array('prompt'=>'','class'=>'width111')); ?> 
			</div><?php echo $form->error($model,'status'); ?>	</div>
		 <?php }else{ ?><div class="item inline-block one normal width111">	<?php echo $form->labelEx($model,'status'); ?>	<div class="selectRow width111">
			<?php echo $form->dropDownList($model, 'status', SmaActions::getStatusListNoClose(),array('prompt'=>'','class'=>'width111')); ?> 
			</div>	<?php echo $form->error($model,'status'); ?>	</div>	 <?php }?>	<div class="item inline-block one normal left11 width92">	<?php echo $form->labelEx($model,'severity'); ?>	<div class="selectRow width92">
			<?php echo $form->dropDownList($model, 'severity', SmaActions::getSeverityTypes(),array('prompt'=>'','class'=>'width92')); ?> 
			</div>	<?php echo $form->error($model,'severity'); ?>	</div>	<div class="item inline-block one width114 left11 " ><?php echo $form->labelEx($model,'responsibility'); ?>
			<div class="selectRow width100 ">	<?php echo $form->dropDownList($model, 'responsibility', SmaActions::getResponsibilityTypes(),array('prompt'=>'','class'=>'width100')); ?> 
			</div>	<?php echo $form->error($model,'responsibility'); ?></div>
		<div class="item inline-block one width100 left11" ><?php echo $form->labelEx($model,'tier'); ?><div class="selectRow width96">
				<?php echo $form->dropDownList($model, 'tier', array('Application'=>'Application','Database'=>'Database','Server'=>'Server'), array('prompt'=>'','class'=>'width100')); ?> 
				</div><?php echo $form->error($model,'tier'); ?></div>
		<div class="item inline-block time " style="cursor:pointer;margin-left:0 !important;padding-left:0 !important; ">
			<?php echo $form->labelEx($model,'eta'); ?>	<div class="dataRow">	
			<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "eta",'cssFile' => false,'options'=>array(	'dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'   	), 	'htmlOptions' => array(	'class' => 'datefield' 	),    ));	?>
				<span class="calendar calfrom"></span>	</div><?php echo $form->error($model,'eta'); ?>	</div>
		<div class="item inline-block width400" style="margin-left:-12px !important;">	<?php echo $form->labelEx($model,'suggested_sol'); ?>
			<div class="dataRow width400" ><?php echo $form->textField($model,'suggested_sol'); ?></div><?php echo $form->error($model,'suggested_sol'); ?>	</div>
		<?php if ($model->isNewRecord) { ?>
			<div style="right:78px;top:123px" class="save" onclick="createSmactionItem(this, <?php echo "1"; ?>, '<?php echo Yii::app()->createAbsoluteUrl('sma/createSmactionItem/?id_sma='.$id_sma.'');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } else { ?><div style="right:78px;top:123px" class="save" onclick="updateSmactionItem(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('sma/ManageSmactionItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } ?><div style="color:#333;top:123px" class="save" onclick="$(this).parents('.tache-smaction.new').siblings('.tache-smaction').show();$(this).parents('.tache-smaction.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid-smactions');<?php }?>"><u><b>CANCEL</b></u></div>
	<?php $this->endWidget(); ?></div>
