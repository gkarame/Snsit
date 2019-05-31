<div class="headli"></div>
<div class="contentli">
	<?php $form = $this->beginWidget('CActiveForm', array(
			'id'=>'shareby-form',
		)); ?>
	<fieldset class="shareby_fieldset">
		<div class="create">
			<div class="title">SHARE</div>
			<div class="row">
				<?php echo $form->labelEx($model,'to'); ?>
				<div class="inputBg_create">
					<?php echo $form->textField($model,"to", array('class'=>'auto_email')); ?>
				</div>
				<?php echo CHtml::error($model,"to"); ?>
			</div>
			<div class="row">
				*Attachment: <?php echo $item->getFilename();?>
			</div>	
			<div class="row">
				<?php echo $form->labelEx($model,'subject'); ?>
				<div class="inputBg_create">
					<?php echo $form->textField($model,"subject"); ?>
				</div>
				<?php echo CHtml::error($model,"subject"); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($model,'body'); ?>
				<div class="textareaBg_create">
					<?php echo $form->textArea($model,'body'); ?>
				</div>
				<?php echo CHtml::error($model,"body"); ?>
			</div>
			
			<div class="row buttons">
				<a href="<?php echo Yii::app()->createUrl("site/shareBy", array("id"=>$item->id));?>" class="save customSaveBtn ua" onclick="shareBySubmit(this, '<?php echo $class;?>');return false;"><?php echo Yii::t('translation', 'SEND');?></a>
				<div class="loader"></div>
				<a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('.popup_shareby').fadeOut(100);$('.popup_shareby').html('');"><?php echo Yii::t('translation', 'CANCEL');?></a>
			</div>
		</div>
	</fieldset>
	<?php $this->endWidget(); ?>
</div>
<div class="ftrli"></div>
