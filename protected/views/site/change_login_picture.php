<?php  $this->pageTitle=Yii::app()->name . ' - '. Yii::t('translations', 'Change Background Picture');
$link = Yii::app()->db->createCommand('SELECT * FROM url_log ORDER BY id desc limit 1')->queryRow();  ?>
<div id="info" class="version2"><div class="div2">	<div class="form content-margin" style="float:left">
		<?php $form=$this->beginWidget('CActiveForm', array('id'=>'change-pic-form','enableClientValidation'=>true,	'clientOptions'=>array(	'validateOnSubmit'=>true,),
				'htmlOptions' => array('enctype' => 'multipart/form-data'),	)); ?>		
			<?php echo CHtml::submitButton(Yii::t('translations', 'Save'), array('name'=>'submit', 'style'=>'display:none')); ?>			
			<?php if ($fileName != null) { ?>	<img class="bgThumb" src="<?php echo $fileName.'?'.time();?>" alt="<?php echo Yii::t('translations', 'Background picture');?>" />	<?php } ?>
			<div class="loginDiv changePic"><?php echo $form->fileField($model,'upload_file', array('style'=>'visibility:hidden;width:1px', 'class'=>'upload-file'));?>
				<div class="attach"></div><?php echo $form->error($model,'upload_file'); ?>	</div>			
		<?php $this->endWidget(); ?>	</div>	<div class="form content-margin" style="float:left;height:280px;">
		<?php $form=$this->beginWidget('CActiveForm', array('id'=>'change-url-form','enableAjaxValidation'=>false,'htmlOptions' => array('enctype' => 'multipart/form-data'),	)); ?>
		<div style="font-size: 14;font-family: Arial;margin-bottom:5px"><?php echo $form->label($model,'url_link');?></div>
			<div class="row fields inputBg " align="center"><?php echo $form->textField($model,'url_link', array('value' => Yii::t('translations', $link['link']))); ?>
				<?php echo $form->error($model,'url_link',array('style'=>'padding-top: 31px')); ?>	</div>
			<div style="font-size: 14;font-family: Arial;margin-bottom:5px"><?php echo $form->label($model,'title');?>	</div>
			<div class="row fields inputBg " align="center"><?php echo $form->textField($model,'title', array('value' => Yii::t('translations', $link['title']))); ?>
				<?php echo $form->error($model,'title',array('style'=>'padding-top: 31px')); ?>	</div>
			<div style="font-size: 14;font-family: Arial;margin-bottom:5px"><?php echo $form->label($model,'short_description');?>
			</div><div class="row fields inputBg " align="center">	<?php echo $form->textField($model,'short_description', array('value' => Yii::t('translations', $link['short_description']))); ?>
				<?php echo $form->error($model,'short_description',array('style'=>'padding-top: 31px')); ?>	</div>			
			<div class="row buttons"><?php echo CHtml::submitButton('Change Link',array('name'=>'url','class'=>'class_link')); ?>
				<?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()', 'class' => 'cancelBtn')); ?>	</div>			
		<?php $this->endWidget(); ?></div></div></div>